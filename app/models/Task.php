<?php

class Task {
    private $db;
    
    // Prioridades de tareas
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_CRITICAL = 'critical';
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    /**
     * Crear nueva tarea
     */
    public function create($projectId, $taskName, $description = '', $assignedUserId = null, $priority = self::PRIORITY_MEDIUM, $dueDate = null, $createdByUserId = null, $assignedPercentage = 0) {
        try {
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
                INSERT INTO Tasks (project_id, task_name, description, assigned_percentage, assigned_to_user_id, created_by_user_id, priority, due_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
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
            $this->db->rollback();
            error_log("Error al crear tarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener tarea por ID
     */
    public function findById($taskId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    t.*,
                    p.project_name,
                    p.task_distribution_mode,
                    p.kpi_points as project_kpi_points,
                    u_assigned.username as assigned_to_username,
                    u_assigned.full_name as assigned_to_fullname,
                    u_created.username as created_by_username,
                    u_created.full_name as created_by_fullname
                FROM Tasks t
                JOIN Projects p ON t.project_id = p.project_id
                LEFT JOIN Users u_assigned ON t.assigned_to_user_id = u_assigned.user_id
                LEFT JOIN Users u_created ON t.created_by_user_id = u_created.user_id
                WHERE t.task_id = ?
            ");
            $stmt->execute([$taskId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener tarea: " . $e->getMessage());
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
                    u_assigned.username as assigned_to_username,
                    u_assigned.full_name as assigned_to_fullname,
                    u_created.username as created_by_username,
                    u_created.full_name as created_by_fullname
                FROM Tasks t
                LEFT JOIN Users u_assigned ON t.assigned_to_user_id = u_assigned.user_id
                LEFT JOIN Users u_created ON t.created_by_user_id = u_created.user_id
                WHERE t.project_id = ?
                ORDER BY t.priority DESC, t.due_date ASC, t.created_at ASC
            ");
            $stmt->execute([$projectId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener tareas del proyecto: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener tareas asignadas a un usuario
     */
    public function getByUser($userId, $includeCompleted = false) {
        try {
            $completedFilter = $includeCompleted ? '' : 'AND t.is_completed = 0';
            
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
        } catch (PDOException $e) {
            error_log("Error al obtener tareas del usuario: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Marcar tarea como completada
     */
    public function markAsCompleted($taskId, $userId = null) {
        try {
            $this->db->beginTransaction();
            
            // Obtener información de la tarea
            $task = $this->findById($taskId);
            if (!$task) {
                throw new Exception("Tarea no encontrada");
            }
            
            // Marcar como completada
            $stmt = $this->db->prepare("
                UPDATE Tasks 
                SET is_completed = 1, completed_at = NOW() 
                WHERE task_id = ?
            ");
            $stmt->execute([$taskId]);
            
            // Registrar en historial de KPI si el proyecto tiene KPI asignado
            if ($task['project_kpi_points'] > 0) {
                $this->logTaskCompletion($task, $userId);
            }
            
            // Actualizar progreso del proyecto
            $this->updateProjectProgress($task['project_id']);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al completar tarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Marcar tarea como pendiente
     */
    public function markAsPending($taskId) {
        try {
            $this->db->beginTransaction();
            
            $task = $this->findById($taskId);
            if (!$task) {
                throw new Exception("Tarea no encontrada");
            }
            
            $stmt = $this->db->prepare("
                UPDATE Tasks 
                SET is_completed = 0, completed_at = NULL 
                WHERE task_id = ?
            ");
            $stmt->execute([$taskId]);
            
            // Actualizar progreso del proyecto
            $this->updateProjectProgress($task['project_id']);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al marcar tarea como pendiente: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar tarea
     */
    public function update($taskId, $taskName, $description, $assignedUserId = null, $priority = null, $dueDate = null, $assignedPercentage = null) {
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
            
            if (!empty($fields)) {
                $fields[] = "updated_at = NOW()";
                $values[] = $taskId;
                
                $sql = "UPDATE Tasks SET " . implode(", ", $fields) . " WHERE task_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($values);
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al actualizar tarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar tarea
     */
    public function delete($taskId) {
        try {
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
            $this->updateProjectProgress($task['project_id']);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al eliminar tarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Redistribuir puntos automáticamente entre tareas
     */
    private function redistributeAutomaticPoints($projectId) {
        try {
            // Obtener información del proyecto
            $stmt = $this->db->prepare("SELECT kpi_points FROM Projects WHERE project_id = ?");
            $stmt->execute([$projectId]);
            $project = $stmt->fetch();
            
            if (!$project || $project['kpi_points'] == 0) {
                return true;
            }
            
            // Contar tareas del proyecto
            $stmt = $this->db->prepare("SELECT COUNT(*) as total_tasks FROM Tasks WHERE project_id = ?");
            $stmt->execute([$projectId]);
            $count = $stmt->fetch();
            $totalTasks = $count['total_tasks'];
            
            if ($totalTasks == 0) {
                return true;
            }
            
            // Calcular puntos por tarea
            $pointsPerTask = $project['kpi_points'] / $totalTasks;
            
            // Actualizar todas las tareas
            $stmt = $this->db->prepare("
                UPDATE Tasks 
                SET automatic_points = ? 
                WHERE project_id = ?
            ");
            $stmt->execute([$pointsPerTask, $projectId]);
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error al redistribuir puntos automáticos: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar progreso del proyecto basado en tareas completadas
     */
    private function updateProjectProgress($projectId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_tasks,
                    SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) as completed_tasks
                FROM Tasks 
                WHERE project_id = ?
            ");
            $stmt->execute([$projectId]);
            $stats = $stmt->fetch();
            
            $totalTasks = $stats['total_tasks'];
            $completedTasks = $stats['completed_tasks'];
            $progressPercentage = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
            
            $stmt = $this->db->prepare("
                UPDATE Projects 
                SET total_tasks = ?, completed_tasks = ?, progress_percentage = ?
                WHERE project_id = ?
            ");
            $stmt->execute([$totalTasks, $completedTasks, $progressPercentage, $projectId]);
            
        } catch (Exception $e) {
            error_log("Error al actualizar progreso del proyecto: " . $e->getMessage());
        }
    }
    
    /**
     * Registrar finalización de tarea en historial KPI
     */
    private function logTaskCompletion($task, $userId) {
        try {
            // Obtener ID del KPI del proyecto
            $stmt = $this->db->prepare("SELECT kpi_quarter_id FROM Projects WHERE project_id = ?");
            $stmt->execute([$task['project_id']]);
            $project = $stmt->fetch();
            
            if ($project && $project['kpi_quarter_id']) {
                $points = $task['task_distribution_mode'] === 'automatic' 
                    ? $task['automatic_points'] 
                    : ($task['assigned_percentage'] * $task['project_kpi_points'] / 100);
                
                $stmt = $this->db->prepare("
                    INSERT INTO KPI_History (kpi_id, project_id, task_id, action_type, new_value, user_id, notes) 
                    VALUES (?, ?, ?, 'task_completed', ?, ?, ?)
                ");
                $stmt->execute([
                    $project['kpi_quarter_id'], 
                    $task['project_id'], 
                    $task['task_id'], 
                    $points, 
                    $userId, 
                    "Tarea completada: {$task['task_name']}"
                ]);
            }
        } catch (Exception $e) {
            error_log("Error al registrar finalización en historial KPI: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener estadísticas de tareas
     */
    public function getStats($projectId = null, $userId = null) {
        try {
            $whereConditions = [];
            $params = [];
            
            if ($projectId) {
                $whereConditions[] = "t.project_id = ?";
                $params[] = $projectId;
            }
            
            if ($userId) {
                $whereConditions[] = "t.assigned_to_user_id = ?";
                $params[] = $userId;
            }
            
            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
            
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_tasks,
                    SUM(CASE WHEN t.is_completed = 1 THEN 1 ELSE 0 END) as completed_tasks,
                    SUM(CASE WHEN t.is_completed = 0 THEN 1 ELSE 0 END) as pending_tasks,
                    SUM(CASE WHEN t.priority = 'critical' AND t.is_completed = 0 THEN 1 ELSE 0 END) as critical_pending,
                    SUM(CASE WHEN t.due_date < CURDATE() AND t.is_completed = 0 THEN 1 ELSE 0 END) as overdue_tasks,
                    COALESCE(AVG(CASE WHEN t.is_completed = 1 THEN DATEDIFF(t.completed_at, t.created_at) END), 0) as avg_completion_days
                FROM Tasks t
                $whereClause
            ");
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas de tareas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener tareas próximas a vencer
     */
    public function getUpcomingTasks($days = 7, $userId = null) {
        try {
            $userFilter = $userId ? 'AND t.assigned_to_user_id = ?' : '';
            $params = $userId ? [$days, $userId] : [$days];
            
            $stmt = $this->db->prepare("
                SELECT 
                    t.*,
                    p.project_name,
                    c.clan_name,
                    u.full_name as assigned_to_fullname
                FROM Tasks t
                JOIN Projects p ON t.project_id = p.project_id
                LEFT JOIN Clans c ON p.clan_id = c.clan_id
                LEFT JOIN Users u ON t.assigned_to_user_id = u.user_id
                WHERE t.is_completed = 0 
                  AND t.due_date IS NOT NULL 
                  AND t.due_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
                  $userFilter
                ORDER BY t.due_date ASC, t.priority DESC
            ");
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener tareas próximas: " . $e->getMessage());
            return [];
        }
    }
}
?>