<?php

class Action extends Project {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Crear nueva acción (reutiliza la funcionalidad de Project)
     */
    public function create($actionName, $description, $clanId, $createdByUserId, $kpiQuarterId = null, $kpiPoints = 0, $taskDistributionMode = 'automatic', $timeLimit = null) {
        try {
            $timeLimitValue = (is_string($timeLimit) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $timeLimit)) ? $timeLimit : null;
            $stmt = $this->db->prepare("
                INSERT INTO Projects (project_name, description, clan_id, created_by_user_id, kpi_quarter_id, kpi_points, task_distribution_mode, time_limit, status, is_action, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'open', 1, NOW())
            ");
            $result = $stmt->execute([$actionName, $description, $clanId, $createdByUserId, $kpiQuarterId, $kpiPoints, $taskDistributionMode, $timeLimitValue]);
            
            if ($result) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al crear acción: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener todas las acciones con información del clan
     */
    public function getAllWithClanInfo() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.*,
                    c.clan_name,
                    u.full_name as created_by_name,
                    u.username as created_by_username,
                    ck.year as kpi_year,
                    ck.quarter as kpi_quarter,
                    ck.total_points as kpi_total_points,
                    (ck.total_points - ck.assigned_points) as kpi_available_points,
                    COUNT(t.task_id) as total_tasks,
                    SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN 1 ELSE 0 END) as completed_tasks,
                    CASE 
                        WHEN p.task_distribution_mode = 'automatic' THEN 
                            COALESCE(SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN t.automatic_points ELSE 0 END), 0)
                        ELSE 
                            COALESCE(SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN (t.assigned_percentage * p.kpi_points / 100) ELSE 0 END), 0)
                    END as earned_points
                FROM Projects p
                LEFT JOIN Clans c ON p.clan_id = c.clan_id
                LEFT JOIN Users u ON p.created_by_user_id = u.user_id
                LEFT JOIN Clan_KPIs ck ON p.kpi_quarter_id = ck.kpi_id
                LEFT JOIN Tasks t ON p.project_id = t.project_id AND t.is_subtask = 0
                WHERE p.is_action = 1
                GROUP BY p.project_id, p.project_name, p.description, p.clan_id, p.created_by_user_id, 
                         p.status, p.created_at, p.updated_at, p.kpi_quarter_id, p.kpi_points, 
                         p.task_distribution_mode, c.clan_name, u.full_name, u.username, 
                         ck.year, ck.quarter, ck.total_points, ck.assigned_points
                ORDER BY p.created_at DESC
            ");
            $stmt->execute();
            $actions = $stmt->fetchAll();
            
            // Calcular el progreso para cada acción
            foreach ($actions as &$action) {
                $totalTasks = (int)$action['total_tasks'];
                $completedTasks = (int)$action['completed_tasks'];
                $kpiPoints = (float)$action['kpi_points'];
                $earnedPoints = (float)$action['earned_points'];
                
                // Calcular el porcentaje de progreso con fallback
                if ($kpiPoints > 0) {
                    if ($earnedPoints > 0) {
                        $action['progress_percentage'] = round(($earnedPoints / $kpiPoints) * 100, 1);
                    } else {
                        $action['progress_percentage'] = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
                    }
                } else {
                    // Si no hay KPI asignado, calcular basado en tareas completadas
                    $action['progress_percentage'] = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
                }
                
                // Asegurar que los valores estén en el rango correcto
                $action['progress_percentage'] = max(0, min(100, $action['progress_percentage']));
            }
            
            return $actions;
        } catch (PDOException $e) {
            error_log("Error al obtener acciones: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener acciones por clan
     */
    public function getByClan($clanId) {
        try {
            error_log("=== getByClan ACCIONES INICIADO ===");
            error_log("Clan ID solicitado: $clanId");
            
            $stmt = $this->db->prepare("
                SELECT 
                    p.*,
                    c.clan_name,
                    u.full_name as created_by_name,
                    COALESCE(p.allow_delegation, 0) as allow_delegation,
                    COUNT(t.task_id) as total_tasks,
                    SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN 1 ELSE 0 END) as completed_tasks,
                    CASE 
                        WHEN p.task_distribution_mode = 'automatic' THEN 
                            COALESCE(SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN t.automatic_points ELSE 0 END), 0)
                        ELSE 
                            COALESCE(SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN (t.assigned_percentage * p.kpi_points / 100) ELSE 0 END), 0)
                    END as earned_points
                FROM Projects p
                LEFT JOIN Clans c ON p.clan_id = c.clan_id
                LEFT JOIN Users u ON p.created_by_user_id = u.user_id
                LEFT JOIN Tasks t ON p.project_id = t.project_id AND t.is_subtask = 0
                WHERE p.clan_id = ? AND p.is_action = 1 AND (p.is_personal IS NULL OR p.is_personal != 1)
                GROUP BY p.project_id, p.project_name, p.description, p.clan_id, p.created_by_user_id, 
                         p.status, p.created_at, p.updated_at, p.kpi_quarter_id, p.kpi_points, 
                         p.task_distribution_mode, p.allow_delegation, p.project_type, p.is_personal, 
                         p.time_limit, p.total_tasks, p.completed_tasks, p.progress_percentage,
                         c.clan_name, u.full_name
                ORDER BY p.created_at DESC
            ");
            $stmt->execute([$clanId]);
            $actions = $stmt->fetchAll();
            
            error_log("Acciones obtenidas: " . count($actions));
            foreach ($actions as $action) {
                error_log("  Acción: ID={$action['project_id']}, Nombre='{$action['project_name']}', is_action={$action['is_action']}, created_by={$action['created_by_user_id']}");
            }
            
            error_log("=== getByClan ACCIONES FINALIZADO ===");

            foreach ($actions as &$action) {
                $totalTasks = (int)($action['total_tasks'] ?? 0);
                $completedTasks = (int)($action['completed_tasks'] ?? 0);
                $kpiPoints = (float)($action['kpi_points'] ?? 0);
                $earnedPoints = (float)($action['earned_points'] ?? 0);

                if ($kpiPoints > 0) {
                    if ($earnedPoints > 0) {
                        $action['progress_percentage'] = round(($earnedPoints / $kpiPoints) * 100, 1);
                    } else {
                        $action['progress_percentage'] = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
                    }
                } else {
                    $action['progress_percentage'] = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
                }

                $action['progress_percentage'] = max(0, min(100, $action['progress_percentage']));
            }

            return $actions;
        } catch (PDOException $e) {
            error_log("Error al obtener acciones del clan: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Buscar acciones por nombre
     */
    public function searchByClan($clanId, $searchTerm = '') {
        try {
            $sql = "
                SELECT 
                    p.*,
                    c.clan_name,
                    u.full_name as created_by_name,
                    COALESCE(p.allow_delegation, 0) as allow_delegation,
                    COUNT(t.task_id) as total_tasks,
                    SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN 1 ELSE 0 END) as completed_tasks,
                    CASE 
                        WHEN p.task_distribution_mode = 'automatic' THEN 
                            COALESCE(SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN t.automatic_points ELSE 0 END), 0)
                        ELSE 
                            COALESCE(SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN (t.assigned_percentage * p.kpi_points / 100) ELSE 0 END), 0)
                    END as earned_points
                FROM Projects p
                LEFT JOIN Clans c ON p.clan_id = c.clan_id
                LEFT JOIN Users u ON p.created_by_user_id = u.user_id
                LEFT JOIN Tasks t ON p.project_id = t.project_id AND t.is_subtask = 0
                WHERE p.clan_id = ? AND p.is_action = 1 AND (p.is_personal IS NULL OR p.is_personal != 1)
            ";
            
            $params = [$clanId];
            
            if (!empty($searchTerm)) {
                $sql .= " AND (p.project_name LIKE ? OR p.description LIKE ?)";
                $searchPattern = '%' . $searchTerm . '%';
                $params[] = $searchPattern;
                $params[] = $searchPattern;
            }
            
            $sql .= "
                GROUP BY p.project_id, p.project_name, p.description, p.clan_id, p.created_by_user_id, 
                         p.status, p.created_at, p.updated_at, p.kpi_quarter_id, p.kpi_points, 
                         p.task_distribution_mode, p.allow_delegation, p.project_type, p.is_personal, 
                         p.time_limit, p.total_tasks, p.completed_tasks, p.progress_percentage,
                         c.clan_name, u.full_name
                ORDER BY p.created_at DESC
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $actions = $stmt->fetchAll();

            foreach ($actions as &$action) {
                $totalTasks = (int)($action['total_tasks'] ?? 0);
                $completedTasks = (int)($action['completed_tasks'] ?? 0);
                $kpiPoints = (float)($action['kpi_points'] ?? 0);
                $earnedPoints = (float)($action['earned_points'] ?? 0);

                if ($kpiPoints > 0) {
                    if ($earnedPoints > 0) {
                        $action['progress_percentage'] = round(($earnedPoints / $kpiPoints) * 100, 1);
                    } else {
                        $action['progress_percentage'] = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
                    }
                } else {
                    $action['progress_percentage'] = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
                }

                $action['progress_percentage'] = max(0, min(100, $action['progress_percentage']));
            }

            return $actions;
        } catch (PDOException $e) {
            error_log("Error al buscar acciones del clan: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Actualizar acción
     */
    public function update($actionId, $actionName, $description, $clanId, $status = null, $timeLimit = null) {
        try {
            $sql = "UPDATE Projects SET project_name = ?, description = ?, clan_id = ?, updated_at = NOW()";
            $params = [$actionName, $description, $clanId];

            if ($timeLimit !== null) {
                $timeLimitValue = (is_string($timeLimit) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $timeLimit)) ? $timeLimit : null;
                $sql .= ", time_limit = ?";
                $params[] = $timeLimitValue;
            }
            
            if ($status !== null) {
                $sql .= ", status = ?";
                $params[] = $status;
            }
            
            $sql .= " WHERE project_id = ? AND is_action = 1";
            $params[] = $actionId;
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error al actualizar acción: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar acción
     */
    public function delete($actionId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM Projects WHERE project_id = ? AND is_action = 1");
            return $stmt->execute([$actionId]);
        } catch (PDOException $e) {
            error_log("Error al eliminar acción: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener acción por ID
     */
    public function findById($actionId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.*,
                    c.clan_name,
                    u.full_name as created_by_name,
                    u.username as created_by_username
                FROM Projects p
                LEFT JOIN Clans c ON p.clan_id = c.clan_id
                LEFT JOIN Users u ON p.created_by_user_id = u.user_id
                WHERE p.project_id = ? AND p.is_action = 1
            ");
            $stmt->execute([$actionId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al buscar acción: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener estadísticas de acciones por clan
     */
    public function getStatsByClan($clanId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_actions,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_actions,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_actions,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_actions,
                    SUM(CASE WHEN kpi_quarter_id IS NOT NULL AND kpi_points > 0 THEN 1 ELSE 0 END) as kpi_actions,
                    COALESCE(SUM(kpi_points), 0) as total_kpi_points
                FROM Projects 
                WHERE clan_id = ? AND is_action = 1 AND (is_personal IS NULL OR is_personal != 1)
            ");
            $stmt->execute([$clanId]);
            $result = $stmt->fetch();
            
            return [
                'total_actions' => (int)($result['total_actions'] ?? 0),
                'active_actions' => (int)($result['active_actions'] ?? 0),
                'completed_actions' => (int)($result['completed_actions'] ?? 0),
                'pending_actions' => (int)($result['pending_actions'] ?? 0),
                'kpi_actions' => (int)($result['kpi_actions'] ?? 0),
                'total_kpi_points' => (int)($result['total_kpi_points'] ?? 0)
            ];
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas de acciones por clan: " . $e->getMessage());
            return [
                'total_actions' => 0,
                'active_actions' => 0,
                'completed_actions' => 0,
                'pending_actions' => 0,
                'kpi_actions' => 0,
                'total_kpi_points' => 0
            ];
        }
    }
    
    /**
     * Clonar acción (similar a clonar proyecto)
     */
    public function cloneAction($originalActionId, $newActionData) {
        try {
            $this->db->beginTransaction();
            
            // Obtener la acción original
            $originalAction = $this->findById($originalActionId);
            if (!$originalAction) {
                throw new Exception("Acción original no encontrada");
            }
            
            // Crear nueva acción
            $newActionId = $this->create(
                $newActionData['action_name'],
                $newActionData['description'],
                $originalAction['clan_id'],
                $originalAction['created_by_user_id'],
                null,
                0,
                $originalAction['task_distribution_mode'],
                $newActionData['end_date'] ?? null
            );
            
            if (!$newActionId) {
                throw new Exception("Error al crear nueva acción");
            }
            
            // Si se solicita clonar tareas, hacerlo
            if (isset($newActionData['clone_tasks']) && $newActionData['clone_tasks'] == '1') {
                $this->cloneActionTasks($originalActionId, $newActionId, $newActionData);
            }
            
            $this->db->commit();
            return $newActionId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al clonar acción: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Clonar tareas de una acción
     */
    private function cloneActionTasks($originalActionId, $newActionId, $cloneData) {
        try {
            // Obtener tareas de la acción original
            $stmt = $this->db->prepare("
                SELECT * FROM Tasks 
                WHERE project_id = ? AND (is_subtask = 0 OR is_subtask IS NULL)
                ORDER BY task_id
            ");
            $stmt->execute([$originalActionId]);
            $originalTasks = $stmt->fetchAll();
            
            foreach ($originalTasks as $task) {
                // Calcular nueva fecha si se solicita ajuste
                $newDueDate = $task['due_date'];
                if (isset($cloneData['adjust_dates']) && $cloneData['adjust_dates'] == '1') {
                    // Lógica de ajuste de fechas (similar a la de proyectos)
                    $newDueDate = $this->adjustTaskDate($task['due_date'], $cloneData);
                }
                
                // Crear nueva tarea
                $stmt = $this->db->prepare("
                    INSERT INTO Tasks (
                        task_name, description, project_id, assigned_to_user_id,
                        status, priority, due_date, created_by_user_id, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $task['task_name'],
                    $task['description'],
                    $newActionId,
                    $task['assigned_to_user_id'],
                    'pending',
                    $task['priority'],
                    $newDueDate,
                    $task['created_by_user_id']
                ]);
            }
            
        } catch (Exception $e) {
            error_log("Error al clonar tareas de acción: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Ajustar fecha de tarea para clonación
     */
    private function adjustTaskDate($originalDate, $cloneData) {
        // Implementación similar a la de proyectos
        // Por simplicidad, mantener la fecha original por ahora
        return $originalDate;
    }
    
    /**
     * Toggle delegación de acción
     */
    public function toggleDelegation($actionId, $allowDelegation) {
        try {
            $stmt = $this->db->prepare("
                UPDATE Projects 
                SET allow_delegation = ?, updated_at = NOW()
                WHERE project_id = ? AND is_action = 1
            ");
            return $stmt->execute([$allowDelegation ? 1 : 0, $actionId]);
        } catch (PDOException $e) {
            error_log("Error al actualizar delegación de acción: " . $e->getMessage());
            return false;
        }
    }
}
