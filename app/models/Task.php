<?php

// El modelo Project se cargará automáticamente por el autoloader

class Task {
    private $db;
    
    // Constantes de prioridad
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';
    
    // Constantes de estado
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    
    public function __construct() {
        $this->db = Database::getConnection();
        
        // Limpiar cualquier transacción activa al inicializar
        if ($this->db->inTransaction()) {
            $this->db->rollback();
        }
    }
    
    /**
     * Crear nueva tarea
     */
    public function create($projectId, $taskName, $description = '', $assignedUserId = null, $priority = self::PRIORITY_MEDIUM, $dueDate = null, $createdByUserId = null, $assignedPercentage = 0) {
        try {
            // Verificar si hay una transacción activa y cerrarla
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            
            $this->db->beginTransaction();
            
            // Verificar modo de distribución del proyecto
            $stmt = $this->db->prepare("SELECT task_distribution_mode, kpi_points FROM Projects WHERE project_id = ?");
            $stmt->execute([$projectId]);
            $project = $stmt->fetch();
            
            if (!$project) {
                throw new Exception("Proyecto no encontrado");
            }
            
            // Crear tarea
            $stmt = $this->db->prepare("
                INSERT INTO Tasks (project_id, task_name, description, assigned_percentage, assigned_to_user_id, created_by_user_id, priority, due_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            $result = $stmt->execute([$projectId, $taskName, $description, $assignedPercentage, $assignedUserId, $createdByUserId, $priority, $dueDate]);
            
            if ($result) {
                $taskId = $this->db->lastInsertId();
                
                // Si es modo automático, recalcular puntos de todas las tareas
                if ($project['task_distribution_mode'] === 'automatic') {
                    $this->redistributeAutomaticPoints($projectId);
                }
                
                $this->db->commit();
                return $taskId;
            }
            
            $this->db->rollback();
            return false;
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            error_log("Error al crear tarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crear tarea avanzada con múltiples usuarios y subtareas
     */
    public function createAdvanced($projectId, $taskName, $description = '', $dueDate = null, $clanId = null, $priority = self::PRIORITY_MEDIUM, $createdByUserId = null, $assignedUsers = [], $subtasks = [], $labels = []) {
        try {
            // Verificar si hay una transacción activa y cerrarla
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            
            $this->db->beginTransaction();
            
            // Crear la tarea principal
            $stmt = $this->db->prepare("
                INSERT INTO Tasks (project_id, task_name, description, due_date, priority, created_by_user_id, status, completion_percentage) 
                VALUES (?, ?, ?, ?, ?, ?, 'pending', 0.00)
            ");
            $result = $stmt->execute([$projectId, $taskName, $description, $dueDate, $priority, $createdByUserId]);
            
            if (!$result) {
                throw new Exception('Error al crear la tarea principal');
            }
            
            $taskId = $this->db->lastInsertId();
            
            // Asignar múltiples usuarios si se especifican
            if (!empty($assignedUsers)) {
                $this->assignMultipleUsers($taskId, $assignedUsers, $createdByUserId);
            }
            
            // Crear subtareas si se especifican
            if (!empty($subtasks)) {
                foreach ($subtasks as $subtask) {
                    $this->createSubtaskAdvanced(
                        $taskId, 
                        $subtask['title'], 
                        $createdByUserId,
                        $subtask['description'] ?? '', 
                        $subtask['percentage'] ?? 0,
                        $subtask['due_date'] ?? null,
                        $subtask['priority'] ?? self::PRIORITY_MEDIUM,
                        $subtask['assigned_user_id'] ?? null
                    );
                }
            }
            
            // Asignar etiquetas si se especifican
            if (!empty($labels)) {
                foreach ($labels as $labelId) {
                    $this->assignLabel($taskId, $labelId, $createdByUserId);
                }
            }
            
            // Registrar en el historial
            $this->logTaskAction($taskId, $createdByUserId, 'created', null, null, null, 'Tarea creada');
            
            $this->db->commit();
            return $taskId;
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            error_log("Error al crear tarea avanzada: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Asignar múltiples usuarios a una tarea
     */
    public function assignMultipleUsers($taskId, $userIds, $assignedByUserId) {
        try {
            // Limpiar asignaciones existentes
            $stmt = $this->db->prepare("DELETE FROM Task_Assignments WHERE task_id = ?");
            $stmt->execute([$taskId]);
            
            // Calcular porcentaje por usuario
            $userCount = count($userIds);
            $percentagePerUser = $userCount > 0 ? 100.00 / $userCount : 0;
            
            // Insertar nuevas asignaciones
            $stmt = $this->db->prepare("
                INSERT INTO Task_Assignments (task_id, user_id, assigned_percentage, assigned_by_user_id, status) 
                VALUES (?, ?, ?, ?, 'assigned')
            ");
            
            foreach ($userIds as $userId) {
                $stmt->execute([$taskId, $userId, $percentagePerUser, $assignedByUserId]);
            }
            
            // Actualizar la tarea principal con el primer usuario asignado
            $stmt = $this->db->prepare("UPDATE Tasks SET assigned_to_user_id = ? WHERE task_id = ?");
            $stmt->execute([$userIds[0], $taskId]);
            
            // Registrar en el historial
            $this->logTaskAction($taskId, $assignedByUserId, 'assigned', 'assigned_users', null, implode(',', $userIds), 'Múltiples usuarios asignados');
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error al asignar múltiples usuarios: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crear subtarea avanzada
     */
    public function createSubtaskAdvanced($taskId, $title, $createdByUserId, $description = '', $percentage = 0, $dueDate = null, $priority = self::PRIORITY_MEDIUM, $assignedUserId = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Subtasks (task_id, title, description, completion_percentage, due_date, priority, assigned_to_user_id, created_by_user_id, subtask_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, (SELECT COALESCE(MAX(subtask_order), 0) + 1 FROM Subtasks s WHERE s.task_id = ?))
            ");
            
            $result = $stmt->execute([$taskId, $title, $description, $percentage, $dueDate, $priority, $assignedUserId, $createdByUserId, $taskId]);
            
            if ($result) {
                $subtaskId = $this->db->lastInsertId();
                
                // Registrar en el historial
                $this->logTaskAction($taskId, $createdByUserId, 'created', 'subtask', null, $title, 'Subtarea creada: ' . $title);
                
                return $subtaskId;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Error al crear subtarea avanzada: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Asignar etiqueta a tarea
     */
    public function assignLabel($taskId, $labelId, $assignedByUserId) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Task_Label_Assignments (task_id, label_id, assigned_by_user_id) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE assigned_at = CURRENT_TIMESTAMP
            ");
            return $stmt->execute([$taskId, $labelId, $assignedByUserId]);
        } catch (Exception $e) {
            error_log("Error al asignar etiqueta: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener tareas con información completa
     */
    public function getTasksComplete($projectId = null, $clanId = null, $status = null, $assignedUserId = null) {
        try {
            $sql = "SELECT * FROM v_tasks_complete WHERE 1=1";
            $params = [];
            
            if ($projectId) {
                $sql .= " AND project_id = ?";
                $params[] = $projectId;
            }
            
            if ($clanId) {
                $sql .= " AND clan_id = ?";
                $params[] = $clanId;
            }
            
            if ($status) {
                $sql .= " AND status = ?";
                $params[] = $status;
            }
            
            if ($assignedUserId) {
                $sql .= " AND assigned_to_user_id = ?";
                $params[] = $assignedUserId;
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Error al obtener tareas completas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener subtareas de una tarea
     */
    public function getSubtasks($taskId) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM v_subtasks_complete 
                WHERE task_id = ? 
                ORDER BY subtask_order ASC
            ");
            $stmt->execute([$taskId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error al obtener subtareas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener comentarios de una tarea
     */
    public function getComments($taskId) {
        try {
            $stmt = $this->db->prepare("
                SELECT tc.*, u.full_name, u.username
                FROM Task_Comments tc
                JOIN Users u ON tc.user_id = u.user_id
                WHERE tc.task_id = ?
                ORDER BY tc.created_at DESC
            ");
            $stmt->execute([$taskId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error al obtener comentarios: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Agregar comentario a una tarea
     */
    public function addComment($taskId, $userId, $commentText, $commentType = 'comment', $relatedUserId = null, $oldValue = null, $newValue = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Task_Comments (task_id, user_id, comment_text, comment_type, related_user_id, old_value, new_value) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([$taskId, $userId, $commentText, $commentType, $relatedUserId, $oldValue, $newValue]);
            
            if ($result) {
                // Registrar en el historial
                $this->logTaskAction($taskId, $userId, 'commented', 'comment', null, $commentText, 'Comentario agregado');
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error al agregar comentario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener historial de una tarea
     */
    public function getHistory($taskId) {
        try {
            $stmt = $this->db->prepare("
                SELECT th.*, u.full_name, u.username, ru.full_name as related_user_name
                FROM Task_History th
                JOIN Users u ON th.user_id = u.user_id
                LEFT JOIN Users ru ON th.related_user_id = ru.user_id
                WHERE th.task_id = ?
                ORDER BY th.created_at DESC
            ");
            $stmt->execute([$taskId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error al obtener historial: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener etiquetas de una tarea
     */
    public function getTaskLabels($taskId) {
        try {
            $stmt = $this->db->prepare("
                SELECT tl.*
                FROM Task_Labels tl
                JOIN Task_Label_Assignments tla ON tl.label_id = tla.label_id
                WHERE tla.task_id = ?
                ORDER BY tl.label_name
            ");
            $stmt->execute([$taskId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error al obtener etiquetas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener usuarios asignados a una tarea
     */
    public function getAssignedUsers($taskId) {
        try {
            $stmt = $this->db->prepare("
                SELECT ta.*, u.full_name, u.username, u.email
                FROM Task_Assignments ta
                JOIN Users u ON ta.user_id = u.user_id
                WHERE ta.task_id = ?
                ORDER BY ta.assigned_at ASC
            ");
            $stmt->execute([$taskId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error al obtener usuarios asignados: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Actualizar estado de subtarea
     */
    public function updateSubtaskStatus($subtaskId, $status, $completionPercentage = null, $userId = null) {
        try {
            $sql = "UPDATE Subtasks SET status = ?, updated_at = CURRENT_TIMESTAMP";
            $params = [$status];
            
            if ($completionPercentage !== null) {
                $sql .= ", completion_percentage = ?";
                $params[] = $completionPercentage;
            }
            
            $sql .= " WHERE subtask_id = ?";
            $params[] = $subtaskId;
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result && $userId) {
                // Obtener task_id para el historial
                $stmt = $this->db->prepare("SELECT task_id FROM Subtasks WHERE subtask_id = ?");
                $stmt->execute([$subtaskId]);
                $subtask = $stmt->fetch();
                
                if ($subtask) {
                    $this->logTaskAction($subtask['task_id'], $userId, 'updated', 'subtask_status', null, $status, 'Estado de subtarea actualizado');
                }
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error al actualizar estado de subtarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener tareas vencidas
     */
    public function getOverdueTasks($clanId = null) {
        try {
            $sql = "SELECT * FROM v_overdue_tasks";
            $params = [];
            
            if ($clanId) {
                $sql .= " WHERE clan_id = ?";
                $params[] = $clanId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error al obtener tareas vencidas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Registrar acción en el historial
     */
    private function logTaskAction($taskId, $userId, $actionType, $fieldName = null, $oldValue = null, $newValue = null, $notes = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Task_History (task_id, user_id, action_type, field_name, old_value, new_value, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$taskId, $userId, $actionType, $fieldName, $oldValue, $newValue, $notes]);
        } catch (Exception $e) {
            error_log("Error al registrar acción en historial: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener tareas activas de un usuario
     */
    public function getActiveTasksByUser($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    t.*,
                    p.project_name,
                    p.status as project_status
                FROM Tasks t
                INNER JOIN Projects p ON t.project_id = p.project_id
                WHERE (t.assigned_to_user_id = ? OR t.task_id IN (
                    SELECT task_id FROM Task_Assignments WHERE user_id = ?
                ))
                AND p.clan_id = (SELECT clan_id FROM Users WHERE user_id = ?)
                AND t.is_subtask = 0
                AND t.status IN ('pending', 'in_progress')
                AND (t.due_date IS NULL OR t.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 15 DAY))
                ORDER BY t.due_date ASC
            ");
            
            $stmt->execute([$userId, $userId, $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error al obtener tareas activas del usuario: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Eliminar tarea
     */
    public function delete($taskId) {
        try {
            // Verificar si hay una transacción activa y cerrarla
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            
            $this->db->beginTransaction();
            
            $task = $this->findById($taskId);
            if (!$task) {
                throw new Exception("Tarea no encontrada");
            }
            
            $stmt = $this->db->prepare("DELETE FROM Tasks WHERE task_id = ?");
            $stmt->execute([$taskId]);
            
            // Si es modo automático, recalcular puntos
            if ($task['task_distribution_mode'] === 'automatic') {
                $this->redistributeAutomaticPoints($task['project_id']);
            }
            
            // Actualizar progreso del proyecto
            $projectModel = new Project();
            $projectModel->updateProgress($task['project_id']);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            error_log("Error al eliminar tarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Buscar tarea por ID
     */
    public function findById($taskId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    t.*,
                    p.task_distribution_mode,
                    p.kpi_points,
                    u_assigned.username as assigned_to_username,
                    u_assigned.full_name as assigned_to_fullname,
                    u_created.username as created_by_username,
                    u_created.full_name as created_by_fullname,
                    u_created.full_name as created_by_name
                FROM Tasks t
                JOIN Projects p ON t.project_id = p.project_id
                LEFT JOIN Users u_assigned ON t.assigned_to_user_id = u_assigned.user_id
                LEFT JOIN Users u_created ON t.created_by_user_id = u_created.user_id
                WHERE t.task_id = ?
            ");
            $stmt->execute([$taskId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Error al buscar tarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener tareas de un proyecto
     */
    public function getByProject($projectId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    t.*,
                    u.full_name as assigned_to_fullname,
                    u.username as assigned_to_username,
                    GROUP_CONCAT(DISTINCT ta_users.full_name ORDER BY ta_users.full_name SEPARATOR ', ') as all_assigned_users,
                    GROUP_CONCAT(DISTINCT ta_users.user_id ORDER BY ta_users.full_name SEPARATOR ',') as all_assigned_user_ids
                FROM Tasks t
                LEFT JOIN Users u ON t.assigned_to_user_id = u.user_id
                LEFT JOIN Task_Assignments ta ON t.task_id = ta.task_id
                LEFT JOIN Users ta_users ON ta.user_id = ta_users.user_id
                WHERE t.project_id = ?
                AND t.is_subtask = 0
                GROUP BY t.task_id
                ORDER BY t.due_date ASC, t.created_at DESC
            ");
            
            $stmt->execute([$projectId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error al obtener tareas del proyecto: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener tareas asignadas a un usuario
     */
    public function getByUser($userId, $includeCompleted = false) {
        try {
            $completedFilter = $includeCompleted ? '' : 'AND t.status != "completed"';
            
            $stmt = $this->db->prepare("
                SELECT 
                    t.*,
                    p.project_name,
                    p.task_distribution_mode,
                    p.kpi_points as project_kpi_points,
                    c.clan_name
                FROM Tasks t
                JOIN Projects p ON t.project_id = p.project_id
                LEFT JOIN Clans c ON p.clan_id = c.clan_id
                WHERE t.assigned_to_user_id = ? {$completedFilter}
                ORDER BY t.priority DESC, t.due_date ASC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error al obtener tareas del usuario: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Cambiar el estado de completado de una tarea
     */
    public function toggleStatus($taskId, $isCompleted, $userId = null) {
        try {
            $this->db->beginTransaction();
            
            $task = $this->findById($taskId);
            if (!$task) {
                throw new Exception("Tarea no encontrada");
            }
            
            $newStatus = $isCompleted ? 'completed' : 'pending';
            $completedAt = $isCompleted ? 'NOW()' : 'NULL';
            
            $stmt = $this->db->prepare("
                UPDATE Tasks 
                SET status = ?, completed_at = {$completedAt}
                WHERE task_id = ?
            ");
            $stmt->execute([$newStatus, $taskId]);
            
            // Actualizar progreso del proyecto
            $projectModel = new Project();
            $projectModel->updateProgress($task['project_id']);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al cambiar estado de tarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar tarea
     */
    public function update($taskId, $taskName, $description, $assignedUserId = null, $priority = null, $dueDate = null, $assignedPercentage = null, $status = null) {
        try {
            $this->db->beginTransaction();
            
            $task = $this->findById($taskId);
            if (!$task) {
                throw new Exception("Tarea no encontrada");
            }
            
            // Construir query dinámicamente
            $fields = [];
            $values = [];
            
            if ($taskName !== null) {
                $fields[] = "task_name = ?";
                $values[] = $taskName;
            }
            
            if ($description !== null) {
                $fields[] = "description = ?";
                $values[] = $description;
            }
            
            // Manejar assigned_to_user_id - puede ser null para desasignar
            if ($assignedUserId !== null) {
                $fields[] = "assigned_to_user_id = ?";
                $values[] = $assignedUserId;
            }
            
            if ($priority !== null) {
                $fields[] = "priority = ?";
                $values[] = $priority;
            }
            
            if ($dueDate !== null) {
                $fields[] = "due_date = ?";
                $values[] = $dueDate;
            }
            
            if ($assignedPercentage !== null && $task['task_distribution_mode'] === 'percentage') {
                $fields[] = "assigned_percentage = ?";
                $values[] = $assignedPercentage;
            }
            
            // Manejar estado
            if ($status !== null) {
                $fields[] = "status = ?";
                $values[] = $status;
                
                // Si el estado es completed, establecer completed_at
                if ($status === 'completed') {
                    $fields[] = "completed_at = NOW()";
                    $fields[] = "is_completed = 1";
                } else {
                    $fields[] = "completed_at = NULL";
                    $fields[] = "is_completed = 0";
                }
            }
            
            if (!empty($fields)) {
                $fields[] = "updated_at = NOW()";
                $values[] = $taskId;
                
                $sql = "UPDATE Tasks SET " . implode(", ", $fields) . " WHERE task_id = ?";
                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute($values);
                
                if (!$result) {
                    throw new Exception("Error al ejecutar la consulta SQL");
                }
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            error_log("Error al actualizar tarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Redistribuir puntos automáticamente
     */
    private function redistributeAutomaticPoints($projectId) {
        try {
            // Obtener total de puntos del proyecto
            $stmt = $this->db->prepare("SELECT kpi_points FROM Projects WHERE project_id = ?");
            $stmt->execute([$projectId]);
            $project = $stmt->fetch();
            
            if (!$project || $project['kpi_points'] <= 0) {
                return;
            }
            
            // Contar tareas activas
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as task_count 
                FROM Tasks 
                WHERE project_id = ? AND status != 'completed'
            ");
            $stmt->execute([$projectId]);
            $result = $stmt->fetch();
            
            if ($result['task_count'] > 0) {
                $pointsPerTask = $project['kpi_points'] / $result['task_count'];
                
                // Actualizar puntos de todas las tareas
                $stmt = $this->db->prepare("
                    UPDATE Tasks 
                    SET automatic_points = ? 
                    WHERE project_id = ? AND status != 'completed'
                ");
                $stmt->execute([$pointsPerTask, $projectId]);
            }
        } catch (Exception $e) {
            error_log("Error al redistribuir puntos: " . $e->getMessage());
        }
    }
    
    /**
     * Eliminar una subtarea
     */
    public function deleteSubtask($subtaskId) {
        try {
            $this->db->beginTransaction();
            
            // Obtener información de la subtarea antes de eliminarla
            $stmt = $this->db->prepare("SELECT task_id FROM Subtasks WHERE subtask_id = ?");
            $stmt->execute([$subtaskId]);
            $subtask = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$subtask) {
                $this->db->rollback();
                return false;
            }
            
            // Eliminar la subtarea
            $stmt = $this->db->prepare("DELETE FROM Subtasks WHERE subtask_id = ?");
            $stmt->execute([$subtaskId]);
            
            // Log la acción
            $this->logTaskAction(
                $subtask['task_id'], 
                $_SESSION['user_id'] ?? 1, 
                'deleted_subtask', 
                'subtask_id', 
                $subtaskId, 
                null, 
                'Subtarea eliminada'
            );
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al eliminar subtarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remover asignación de usuario a una tarea
     */
    public function removeUserAssignment($taskId, $userId) {
        try {
            $this->db->beginTransaction();
            
            // Eliminar la asignación
            $stmt = $this->db->prepare("DELETE FROM Task_Assignments WHERE task_id = ? AND user_id = ?");
            $stmt->execute([$taskId, $userId]);
            
            // Log la acción
            $this->logTaskAction(
                $taskId, 
                $_SESSION['user_id'] ?? 1, 
                'removed_user', 
                'user_id', 
                $userId, 
                null, 
                'Usuario removido de la tarea'
            );
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al remover asignación de usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar porcentaje de asignación de un usuario
     */
    public function updateUserAssignmentPercentage($taskId, $userId, $percentage) {
        try {
            $this->db->beginTransaction();
            
            // Actualizar el porcentaje
            $stmt = $this->db->prepare("UPDATE Task_Assignments SET assigned_percentage = ? WHERE task_id = ? AND user_id = ?");
            $stmt->execute([$percentage, $taskId, $userId]);
            
            // Log la acción
            $this->logTaskAction(
                $taskId, 
                $_SESSION['user_id'] ?? 1, 
                'updated_percentage', 
                'assigned_percentage', 
                null, 
                $percentage, 
                "Porcentaje actualizado para usuario $userId"
            );
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al actualizar porcentaje de asignación: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener todas las tareas del clan con paginación, búsqueda y filtros
     */
    public function getAllTasksByClan($clanId, $page = 1, $perPage = 5, $search = '', $statusFilter = '') {
        try {
            $offset = ($page - 1) * $perPage;
            
            // Construir la consulta base
            $baseQuery = "
                FROM Tasks t
                JOIN Projects p ON t.project_id = p.project_id
                LEFT JOIN Users u ON t.assigned_to_user_id = u.user_id
                LEFT JOIN Users creator ON t.created_by_user_id = creator.user_id
                LEFT JOIN Task_Assignments ta ON t.task_id = ta.task_id
                LEFT JOIN Users ta_users ON ta.user_id = ta_users.user_id
                WHERE p.clan_id = ? 
                AND t.is_subtask = 0
            ";
            
            // Agregar filtro de búsqueda si se proporciona
            $searchParams = [$clanId];
            if (!empty($search)) {
                $baseQuery .= " AND (
                    t.task_name LIKE ? OR 
                    t.description LIKE ? OR 
                    p.project_name LIKE ? OR
                    u.full_name LIKE ? OR
                    creator.full_name LIKE ?
                )";
                $searchTerm = "%{$search}%";
                $searchParams = array_merge($searchParams, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }
            
            // Agregar filtro por estado si se proporciona
            if (!empty($statusFilter)) {
                $baseQuery .= " AND t.status = ?";
                $searchParams[] = $statusFilter;
            }
            
            $baseQuery .= " GROUP BY t.task_id";
            
            // Consulta para contar total de registros
            $countQuery = "SELECT COUNT(DISTINCT t.task_id) as total " . $baseQuery;
            $countStmt = $this->db->prepare($countQuery);
            $countStmt->execute($searchParams);
            $totalRecords = $countStmt->fetchColumn();
            
            // Consulta principal con paginación
            $mainQuery = "
                SELECT 
                    t.task_id,
                    t.task_name,
                    t.description,
                    t.due_date,
                    t.priority,
                    t.status,
                    t.completion_percentage,
                    t.automatic_points,
                    p.project_name,
                    p.project_id,
                    u.full_name as assigned_user_name,
                    u.username as assigned_username,
                    creator.full_name as created_by_name,
                    creator.username as created_by_username,
                    DATEDIFF(t.due_date, CURDATE()) as days_until_due,
                    GROUP_CONCAT(DISTINCT ta_users.full_name ORDER BY ta_users.full_name SEPARATOR ', ') as all_assigned_users,
                    GROUP_CONCAT(DISTINCT ta_users.user_id ORDER BY ta_users.full_name SEPARATOR ',') as all_assigned_user_ids
                " . $baseQuery . "
                ORDER BY 
                    CASE t.priority 
                        WHEN 'urgent' THEN 1 
                        WHEN 'high' THEN 2 
                        WHEN 'medium' THEN 3 
                        WHEN 'low' THEN 4 
                    END,
                    t.due_date ASC,
                    t.created_at DESC
                LIMIT ? OFFSET ?
            ";
            
            $mainParams = array_merge($searchParams, [$perPage, $offset]);
            $stmt = $this->db->prepare($mainQuery);
            $stmt->execute($mainParams);
            $tasks = $stmt->fetchAll();
            
            return [
                'tasks' => $tasks,
                'total' => $totalRecords,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($totalRecords / $perPage)
            ];
            
        } catch (PDOException $e) {
            error_log("Error al obtener todas las tareas del clan: " . $e->getMessage());
            return [
                'tasks' => [],
                'total' => 0,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => 0
            ];
        }
    }

    /**
     * Obtener tareas pendientes importantes del clan con paginación y búsqueda
     */
    public function getPendingTasksByClan($clanId, $page = 1, $perPage = 5, $search = '') {
        try {
            $offset = ($page - 1) * $perPage;
            
            // Construir la consulta base
            $baseQuery = "
                FROM Tasks t
                JOIN Projects p ON t.project_id = p.project_id
                LEFT JOIN Users u ON t.assigned_to_user_id = u.user_id
                LEFT JOIN Users creator ON t.created_by_user_id = creator.user_id
                LEFT JOIN Task_Assignments ta ON t.task_id = ta.task_id
                LEFT JOIN Users ta_users ON ta.user_id = ta_users.user_id
                WHERE p.clan_id = ? 
                AND t.status IN ('pending', 'in_progress')
            ";
            
            // Agregar filtro de búsqueda si se proporciona
            $searchParams = [$clanId];
            if (!empty($search)) {
                $baseQuery .= " AND (
                    t.task_name LIKE ? OR 
                    t.description LIKE ? OR 
                    p.project_name LIKE ? OR
                    u.full_name LIKE ? OR
                    creator.full_name LIKE ?
                )";
                $searchTerm = "%{$search}%";
                $searchParams = array_merge($searchParams, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }
            
            $baseQuery .= " GROUP BY t.task_id";
            
            // Consulta para contar total de registros
            $countQuery = "SELECT COUNT(DISTINCT t.task_id) as total " . $baseQuery;
            $countStmt = $this->db->prepare($countQuery);
            $countStmt->execute($searchParams);
            $totalRecords = $countStmt->fetchColumn();
            
            // Consulta principal con paginación
            $mainQuery = "
                SELECT 
                    t.task_id,
                    t.task_name,
                    t.description,
                    t.due_date,
                    t.priority,
                    t.status,
                    t.completion_percentage,
                    t.automatic_points,
                    p.project_name,
                    p.project_id,
                    u.full_name as assigned_user_name,
                    u.username as assigned_username,
                    creator.full_name as created_by_name,
                    creator.username as created_by_username,
                    DATEDIFF(t.due_date, CURDATE()) as days_until_due,
                    GROUP_CONCAT(DISTINCT ta_users.full_name ORDER BY ta_users.full_name SEPARATOR ', ') as all_assigned_users,
                    GROUP_CONCAT(DISTINCT ta_users.user_id ORDER BY ta_users.full_name SEPARATOR ',') as all_assigned_user_ids
                " . $baseQuery . "
                ORDER BY 
                    CASE t.priority 
                        WHEN 'urgent' THEN 1 
                        WHEN 'high' THEN 2 
                        WHEN 'medium' THEN 3 
                        WHEN 'low' THEN 4 
                    END,
                    t.due_date ASC,
                    t.created_at DESC
                LIMIT ? OFFSET ?
            ";
            
            $mainParams = array_merge($searchParams, [$perPage, $offset]);
            $stmt = $this->db->prepare($mainQuery);
            $stmt->execute($mainParams);
            $tasks = $stmt->fetchAll();
            
            return [
                'tasks' => $tasks,
                'total' => $totalRecords,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($totalRecords / $perPage)
            ];
            
        } catch (PDOException $e) {
            error_log("Error al obtener tareas pendientes del clan: " . $e->getMessage());
            return [
                'tasks' => [],
                'total' => 0,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => 0
            ];
        }
    }

    /**
     * Obtener tareas del trimestre actual sin completar para un clan
     */
    public function getCurrentQuarterTasksByClan($clanId) {
        try {
            // Obtener el trimestre actual
            $currentDate = new DateTime();
            $year = $currentDate->format('Y');
            $month = (int)$currentDate->format('n');
            
            // Determinar el trimestre basado en el mes
            if ($month >= 1 && $month <= 3) {
                $quarter = 'Q1';
            } elseif ($month >= 4 && $month <= 6) {
                $quarter = 'Q2';
            } elseif ($month >= 7 && $month <= 9) {
                $quarter = 'Q3';
            } else {
                $quarter = 'Q4';
            }
            
            $query = "
                SELECT 
                    t.task_id,
                    t.task_name,
                    t.description,
                    t.due_date,
                    t.priority,
                    t.status,
                    t.completion_percentage,
                    p.project_name,
                    p.project_id,
                    GROUP_CONCAT(DISTINCT ta_users.full_name ORDER BY ta_users.full_name SEPARATOR ', ') as all_assigned_users,
                    GROUP_CONCAT(DISTINCT ta_users.user_id ORDER BY ta_users.full_name SEPARATOR ',') as all_assigned_user_ids,
                    DATEDIFF(t.due_date, CURDATE()) as days_until_due
                FROM Tasks t
                JOIN Projects p ON t.project_id = p.project_id
                LEFT JOIN Task_Assignments ta ON t.task_id = ta.task_id
                LEFT JOIN Users ta_users ON ta.user_id = ta_users.user_id
                WHERE p.clan_id = ?
                AND t.is_subtask = 0
                AND t.status != 'completed'
                AND p.kpi_quarter_id IN (
                    SELECT kpi_id 
                    FROM Clan_KPIs 
                    WHERE clan_id = ? 
                    AND year = ? 
                    AND quarter = ?
                )
                GROUP BY t.task_id
                ORDER BY 
                    CASE t.priority
                        WHEN 'urgent' THEN 1
                        WHEN 'high' THEN 2
                        WHEN 'medium' THEN 3
                        WHEN 'low' THEN 4
                    END,
                    t.due_date ASC
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$clanId, $clanId, $year, $quarter]);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener tareas del trimestre actual: " . $e->getMessage());
            return [];
        }
    }
}
?>
