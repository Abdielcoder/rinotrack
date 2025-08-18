<?php

class Project {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    /**
     * Crear nuevo proyecto
     */
    public function create($projectName, $description, $clanId, $createdByUserId, $kpiQuarterId = null, $kpiPoints = 0, $taskDistributionMode = 'automatic', $timeLimit = null) {
        try {
            $timeLimitValue = (is_string($timeLimit) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $timeLimit)) ? $timeLimit : null;
            $stmt = $this->db->prepare("
                INSERT INTO Projects (project_name, description, clan_id, created_by_user_id, kpi_quarter_id, kpi_points, task_distribution_mode, time_limit, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'open', NOW())
            ");
            $result = $stmt->execute([$projectName, $description, $clanId, $createdByUserId, $kpiQuarterId, $kpiPoints, $taskDistributionMode, $timeLimitValue]);
            
            if ($result) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al crear proyecto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crear proyecto con KPI asignado
     */
    public function createWithKPI($projectName, $description, $clanId, $createdByUserId, $kpiQuarterId, $kpiPoints, $taskDistributionMode = 'automatic', $timeLimit = null) {
        try {
            $this->db->beginTransaction();
            
            // Verificar que el KPI tenga puntos disponibles
            $stmt = $this->db->prepare("
                SELECT total_points, assigned_points 
                FROM Clan_KPIs 
                WHERE kpi_id = ?
            ");
            $stmt->execute([$kpiQuarterId]);
            $kpi = $stmt->fetch();
            
            if (!$kpi) {
                throw new Exception("KPI no encontrado");
            }
            
            $availablePoints = $kpi['total_points'] - $kpi['assigned_points'];
            if ($kpiPoints > $availablePoints) {
                throw new Exception("No hay suficientes puntos disponibles. Disponibles: {$availablePoints}, Solicitados: {$kpiPoints}");
            }
            
            // Crear proyecto
            $projectId = $this->create($projectName, $description, $clanId, $createdByUserId, $kpiQuarterId, $kpiPoints, $taskDistributionMode, $timeLimit);
            
            if (!$projectId) {
                throw new Exception("Error al crear proyecto");
            }
            
            // Actualizar puntos asignados en KPI
            $stmt = $this->db->prepare("
                UPDATE Clan_KPIs 
                SET assigned_points = assigned_points + ? 
                WHERE kpi_id = ?
            ");
            $stmt->execute([$kpiPoints, $kpiQuarterId]);
            
            $this->db->commit();
            return $projectId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al crear proyecto con KPI: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Obtener todos los proyectos con información del clan
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
                GROUP BY p.project_id, p.project_name, p.description, p.clan_id, p.created_by_user_id, 
                         p.status, p.created_at, p.updated_at, p.kpi_quarter_id, p.kpi_points, 
                         p.task_distribution_mode, c.clan_name, u.full_name, u.username, 
                         ck.year, ck.quarter, ck.total_points, ck.assigned_points
                ORDER BY p.created_at DESC
            ");
            $stmt->execute();
            $projects = $stmt->fetchAll();
            
            // Calcular el progreso para cada proyecto
            foreach ($projects as &$project) {
                $totalTasks = (int)$project['total_tasks'];
                $completedTasks = (int)$project['completed_tasks'];
                $kpiPoints = (float)$project['kpi_points'];
                $earnedPoints = (float)$project['earned_points'];
                
                // Calcular el porcentaje de progreso con fallback
                if ($kpiPoints > 0) {
                    if ($earnedPoints > 0) {
                        $project['progress_percentage'] = round(($earnedPoints / $kpiPoints) * 100, 1);
                    } else {
                        $project['progress_percentage'] = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
                    }
                } else {
                    // Si no hay KPI asignado, calcular basado en tareas completadas
                    $project['progress_percentage'] = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
                }
                
                // Asegurar que los valores estén en el rango correcto
                $project['progress_percentage'] = max(0, min(100, $project['progress_percentage']));
            }
            
            return $projects;
        } catch (PDOException $e) {
            error_log("Error al obtener proyectos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener proyectos con KPI de un trimestre específico
     */
    public function getByKPIQuarter($kpiQuarterId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.*,
                    c.clan_name,
                    u.full_name as created_by_name,
                    COUNT(t.task_id) as total_tasks_count,
                    SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN 1 ELSE 0 END) as completed_tasks_count,
                    CASE 
                        WHEN p.task_distribution_mode = 'automatic' THEN 
                            COALESCE(SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN t.automatic_points ELSE 0 END), 0)
                        ELSE 
                            COALESCE(SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN (t.assigned_percentage * p.kpi_points / 100) ELSE 0 END), 0)
                    END as earned_points
                FROM Projects p
                LEFT JOIN Clans c ON p.clan_id = c.clan_id
                LEFT JOIN Users u ON p.created_by_user_id = u.user_id
                LEFT JOIN Tasks t ON p.project_id = t.project_id
                WHERE p.kpi_quarter_id = ?
                GROUP BY p.project_id
                ORDER BY p.created_at DESC
            ");
            $stmt->execute([$kpiQuarterId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener proyectos por KPI: " . $e->getMessage());
            return [];
        }
    }
    

    
    /**
     * Obtener proyecto por ID
     */
    public function findById($projectId) {
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
                WHERE p.project_id = ?
            ");
            $stmt->execute([$projectId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al buscar proyecto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener proyectos por clan
     */
    public function getByClan($clanId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.*,
                    c.clan_name,
                    u.full_name as created_by_name,
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
                WHERE p.clan_id = ?
                GROUP BY p.project_id, p.project_name, p.description, p.clan_id, p.created_by_user_id, 
                         p.status, p.created_at, p.updated_at, p.kpi_quarter_id, p.kpi_points, 
                         p.task_distribution_mode, c.clan_name, u.full_name
                ORDER BY p.created_at DESC
            ");
            $stmt->execute([$clanId]);
            $projects = $stmt->fetchAll();

            foreach ($projects as &$project) {
                $totalTasks = (int)($project['total_tasks'] ?? 0);
                $completedTasks = (int)($project['completed_tasks'] ?? 0);
                $kpiPoints = (float)($project['kpi_points'] ?? 0);
                $earnedPoints = (float)($project['earned_points'] ?? 0);

                if ($kpiPoints > 0) {
                    if ($earnedPoints > 0) {
                        $project['progress_percentage'] = round(($earnedPoints / $kpiPoints) * 100, 1);
                    } else {
                        $project['progress_percentage'] = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
                    }
                } else {
                    $project['progress_percentage'] = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
                }

                $project['progress_percentage'] = max(0, min(100, $project['progress_percentage']));
            }

            return $projects;
        } catch (PDOException $e) {
            error_log("Error al obtener proyectos del clan: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Actualizar proyecto
     */
    public function update($projectId, $projectName, $description, $clanId, $status = null, $timeLimit = null) {
        try {
            $sql = "UPDATE Projects SET project_name = ?, description = ?, clan_id = ?, updated_at = NOW()";
            $params = [$projectName, $description, $clanId];

            if ($timeLimit !== null) {
                $timeLimitValue = (is_string($timeLimit) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $timeLimit)) ? $timeLimit : null;
                $sql .= ", time_limit = ?";
                $params[] = $timeLimitValue;
            }
            
            if ($status !== null) {
                $sql .= ", status = ?";
                $params[] = $status;
            }
            
            $sql .= " WHERE project_id = ?";
            $params[] = $projectId;
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error al actualizar proyecto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar proyecto
     */
    public function delete($projectId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM Projects WHERE project_id = ?");
            return $stmt->execute([$projectId]);
        } catch (PDOException $e) {
            error_log("Error al eliminar proyecto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Agregar participante al proyecto
     */
    public function addParticipant($projectId, $userId) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Project_Participants (project_id, user_id) 
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE project_id = project_id
            ");
            return $stmt->execute([$projectId, $userId]);
        } catch (PDOException $e) {
            error_log("Error al agregar participante: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener participantes del proyecto
     */
    public function getParticipants($projectId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.user_id,
                    u.username,
                    u.full_name,
                    u.email
                FROM Project_Participants pp
                JOIN Users u ON pp.user_id = u.user_id
                WHERE pp.project_id = ?
                ORDER BY u.full_name
            ");
            $stmt->execute([$projectId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener participantes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener estadísticas de proyectos
     */
    public function getStats() {
        try {
            // Obtener estadísticas básicas de proyectos
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_projects,
                    SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_projects,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_projects,
                    SUM(CASE WHEN status = 'paused' THEN 1 ELSE 0 END) as paused_projects
                FROM Projects
            ");
            $stmt->execute();
            $result = $stmt->fetch();
            
            // Calcular progreso promedio en tiempo real
            $progressStmt = $this->db->prepare("
                SELECT 
                    p.project_id,
                    p.kpi_points,
                    p.task_distribution_mode,
                    COUNT(t.task_id) as total_tasks,
                    SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN 1 ELSE 0 END) as completed_tasks,
                    CASE 
                        WHEN p.task_distribution_mode = 'automatic' THEN 
                            COALESCE(SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN t.automatic_points ELSE 0 END), 0)
                        ELSE 
                            COALESCE(SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN (t.assigned_percentage * p.kpi_points / 100) ELSE 0 END), 0)
                    END as earned_points
                FROM Projects p
                LEFT JOIN Tasks t ON p.project_id = t.project_id AND t.is_subtask = 0
                GROUP BY p.project_id, p.kpi_points, p.task_distribution_mode
            ");
            $progressStmt->execute();
            $projectsProgress = $progressStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcular promedio de progreso
            $totalProgress = 0;
            $projectsWithProgress = 0;
            
            foreach ($projectsProgress as $project) {
                $kpiPoints = (float)$project['kpi_points'];
                $earnedPoints = (float)$project['earned_points'];
                $totalTasks = (int)$project['total_tasks'];
                $completedTasks = (int)$project['completed_tasks'];
                
                // Calcular progreso para este proyecto
                if ($kpiPoints > 0) {
                    $progress = ($earnedPoints / $kpiPoints) * 100;
                } else {
                    $progress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
                }
                
                $totalProgress += $progress;
                $projectsWithProgress++;
            }
            
            $avgProgress = $projectsWithProgress > 0 ? $totalProgress / $projectsWithProgress : 0;
            
            // Asegurar que todos los valores sean numéricos
            return [
                'total_projects' => (int)($result['total_projects'] ?? 0),
                'open_projects' => (int)($result['open_projects'] ?? 0),
                'completed_projects' => (int)($result['completed_projects'] ?? 0),
                'paused_projects' => (int)($result['paused_projects'] ?? 0),
                'avg_progress' => round($avgProgress, 1)
            ];
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [
                'total_projects' => 0,
                'open_projects' => 0,
                'completed_projects' => 0,
                'paused_projects' => 0,
                'avg_progress' => 0.0
            ];
        }
    }
    
    /**
     * Cambiar modo de distribución de tareas
     */
    public function changeTaskDistributionMode($projectId, $newMode) {
        try {
            $this->db->beginTransaction();
            
            // Actualizar modo
            $stmt = $this->db->prepare("
                UPDATE Projects 
                SET task_distribution_mode = ? 
                WHERE project_id = ?
            ");
            $stmt->execute([$newMode, $projectId]);
            
            // Si cambia a automático, redistribuir puntos
            if ($newMode === 'automatic') {
                $this->redistributeAutomaticTaskPoints($projectId);
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al cambiar modo de distribución: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Redistribuir puntos automáticamente entre tareas de un proyecto
     */
    private function redistributeAutomaticTaskPoints($projectId) {
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
     * Actualizar el progreso de un proyecto basado en sus tareas
     */
    public function updateProgress($projectId) {
        try {
            // Calcular el progreso desde la tabla de tareas
            $stmt = $this->db->prepare("
                SELECT 
                    p.kpi_points,
                    p.task_distribution_mode,
                    COUNT(t.task_id) as total_tasks,
                    SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN 1 ELSE 0 END) as completed_tasks,
                    CASE 
                        WHEN p.task_distribution_mode = 'automatic' THEN 
                            COALESCE(SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN t.automatic_points ELSE 0 END), 0)
                        ELSE 
                            COALESCE(SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN (t.assigned_percentage * p.kpi_points / 100) ELSE 0 END), 0)
                    END as earned_points
                FROM Projects p
                LEFT JOIN Tasks t ON p.project_id = t.project_id
                WHERE p.project_id = ?
                GROUP BY p.project_id
            ");
            $stmt->execute([$projectId]);
            $progressData = $stmt->fetch();
            
            if ($progressData) {
                $totalTasks = $progressData['total_tasks'] ?? 0;
                $completedTasks = $progressData['completed_tasks'] ?? 0;
                $kpiPoints = $progressData['kpi_points'] ?? 0;
                $earnedPoints = $progressData['earned_points'] ?? 0;
                
                // Calcular el porcentaje de progreso
                $progressPercentage = ($kpiPoints > 0) ? ($earnedPoints / $kpiPoints) * 100 : 0;
                
                // Actualizar la tabla de proyectos
                $updateStmt = $this->db->prepare("
                    UPDATE Projects 
                    SET 
                        total_tasks = ?, 
                        completed_tasks = ?, 
                        progress_percentage = ?
                    WHERE project_id = ?
                ");
                $updateStmt->execute([$totalTasks, $completedTasks, $progressPercentage, $projectId]);
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Error al actualizar progreso del proyecto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Asignar o actualizar KPI de un proyecto
     */
    public function assignKPI($projectId, $kpiQuarterId, $kpiPoints) {
        try {
            $this->db->beginTransaction();
            
            // Obtener proyecto actual
            $project = $this->findById($projectId);
            if (!$project) {
                throw new Exception("Proyecto no encontrado");
            }
            
            $oldKpiPoints = $project['kpi_points'] ?? 0;
            $oldKpiQuarterId = $project['kpi_quarter_id'];
            
            // Obtener información del trimestre KPI directamente
            $stmt = $this->db->prepare("
                SELECT total_points, 
                       COALESCE(SUM(p.kpi_points), 0) as assigned_points
                FROM KPI_Quarters kq
                LEFT JOIN Projects p ON kq.kpi_quarter_id = p.kpi_quarter_id
                WHERE kq.kpi_quarter_id = ?
                GROUP BY kq.kpi_quarter_id, kq.total_points
            ");
            $stmt->execute([$kpiQuarterId]);
            $kpi = $stmt->fetch();
            
            if (!$kpi) {
                throw new Exception("Trimestre KPI no encontrado");
            }
            
            // Si el proyecto ya tenía puntos asignados al mismo trimestre, restarlos del cálculo
            $currentProjectPoints = ($oldKpiQuarterId == $kpiQuarterId) ? $oldKpiPoints : 0;
            $actuallyAssignedPoints = $kpi['assigned_points'] - $currentProjectPoints;
            $availablePoints = $kpi['total_points'] - $actuallyAssignedPoints;
            
            if ($kpiPoints > $availablePoints) {
                throw new Exception("No hay suficientes puntos disponibles. Disponibles: {$availablePoints}, Solicitados: {$kpiPoints}");
            }
            
            // Actualizar proyecto
            $stmt = $this->db->prepare("
                UPDATE Projects 
                SET kpi_quarter_id = ?, kpi_points = ?
                WHERE project_id = ?
            ");
            $stmt->execute([$kpiQuarterId, $kpiPoints, $projectId]);
            
            // Si está en modo automático, redistribuir puntos entre tareas
            if ($project['task_distribution_mode'] === 'automatic') {
                $this->redistributeAutomaticTaskPoints($projectId);
            }
            
            $this->db->commit();
            return ['success' => true, 'message' => 'KPI asignado exitosamente'];
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al asignar KPI: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Calcular progreso KPI de un proyecto
     */
    public function calculateKPIProgress($projectId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.kpi_points,
                    p.task_distribution_mode,
                    COUNT(t.task_id) as total_tasks,
                    SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN 1 ELSE 0 END) as completed_tasks,
                    CASE 
                        WHEN p.task_distribution_mode = 'automatic' THEN 
                            COALESCE(SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN t.automatic_points ELSE 0 END), 0)
                        ELSE 
                            COALESCE(SUM(CASE WHEN (t.status = 'completed' OR t.is_completed = 1) THEN (t.assigned_percentage * p.kpi_points / 100) ELSE 0 END), 0)
                    END as earned_points
                FROM Projects p
                LEFT JOIN Tasks t ON p.project_id = t.project_id
                WHERE p.project_id = ?
                GROUP BY p.project_id
            ");
            $stmt->execute([$projectId]);
            $result = $stmt->fetch();
            
            if ($result) {
                $totalPoints = $result['kpi_points'] ?? 0;
                $earnedPoints = $result['earned_points'] ?? 0;
                $progressPercentage = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;
                
                return [
                    'total_points' => $totalPoints,
                    'earned_points' => $earnedPoints,
                    'progress_percentage' => $progressPercentage,
                    'total_tasks' => $result['total_tasks'] ?? 0,
                    'completed_tasks' => $result['completed_tasks'] ?? 0,
                    'distribution_mode' => $result['task_distribution_mode']
                ];
            }
            
            return [
                'total_points' => 0,
                'earned_points' => 0,
                'progress_percentage' => 0,
                'total_tasks' => 0,
                'completed_tasks' => 0,
                'distribution_mode' => 'automatic'
            ];
            
        } catch (PDOException $e) {
            error_log("Error al calcular progreso KPI del proyecto: " . $e->getMessage());
            return [];
        }
    }
    
    public function getByUser($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.*,
                    c.clan_name
                FROM Projects p
                LEFT JOIN Clans c ON p.clan_id = c.clan_id
                WHERE p.created_by_user_id = ?
                ORDER BY p.created_at DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener proyectos del usuario: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener proyectos sin KPI asignado
     */
    public function getProjectsWithoutKPI() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.*,
                    c.clan_name,
                    u.full_name as created_by_name
                FROM Projects p
                LEFT JOIN Clans c ON p.clan_id = c.clan_id
                LEFT JOIN Users u ON p.created_by_user_id = u.user_id
                WHERE p.kpi_quarter_id IS NULL OR p.kpi_points = 0
                ORDER BY p.created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener proyectos sin KPI: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener proyectos sin KPI asignado por clan
     */
    public function getProjectsWithoutKPIByClan($clanId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.*,
                    c.clan_name,
                    u.full_name as created_by_name
                FROM Projects p
                LEFT JOIN Clans c ON p.clan_id = c.clan_id
                LEFT JOIN Users u ON p.created_by_user_id = u.user_id
                WHERE (p.kpi_quarter_id IS NULL OR p.kpi_points = 0) AND p.clan_id = ?
                ORDER BY p.created_at DESC
            ");
            $stmt->execute([$clanId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener proyectos sin KPI por clan: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener estadísticas de proyectos por clan
     */
    public function getStatsByClan($clanId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_projects,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_projects,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_projects,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_projects,
                    SUM(CASE WHEN kpi_quarter_id IS NOT NULL AND kpi_points > 0 THEN 1 ELSE 0 END) as kpi_projects,
                    COALESCE(SUM(kpi_points), 0) as total_kpi_points
                FROM Projects 
                WHERE clan_id = ?
            ");
            $stmt->execute([$clanId]);
            $result = $stmt->fetch();
            
            return [
                'total_projects' => (int)($result['total_projects'] ?? 0),
                'active_projects' => (int)($result['active_projects'] ?? 0),
                'completed_projects' => (int)($result['completed_projects'] ?? 0),
                'pending_projects' => (int)($result['pending_projects'] ?? 0),
                'kpi_projects' => (int)($result['kpi_projects'] ?? 0),
                'total_kpi_points' => (int)($result['total_kpi_points'] ?? 0)
            ];
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas de proyectos por clan: " . $e->getMessage());
            return [
                'total_projects' => 0,
                'active_projects' => 0,
                'completed_projects' => 0,
                'pending_projects' => 0,
                'kpi_projects' => 0,
                'total_kpi_points' => 0
            ];
        }
    }

    /**
     * Crear proyecto personal para un usuario
     */
    public function createPersonalProject($projectData) {
        try {
            $userId = (int)($projectData['user_id'] ?? 0);
            
            if ($userId <= 0) {
                error_log("createPersonalProject: user_id inválido: $userId");
                return false;
            }
            
            // Obtener el clan del usuario
            $stmt = $this->db->prepare("
                SELECT cm.clan_id, c.clan_name 
                FROM Clan_Members cm 
                JOIN Clans c ON c.clan_id = cm.clan_id 
                WHERE cm.user_id = ?
                LIMIT 1
            ");
            $stmt->execute([$userId]);
            $userClan = $stmt->fetch();
            
            if (!$userClan) {
                error_log("createPersonalProject: Usuario $userId no pertenece a ningún clan");
                return false;
            }
            
            $clanId = $userClan['clan_id'];
            $clanName = $userClan['clan_name'];
            
            error_log("createPersonalProject: Creando proyecto personal para usuario $userId en clan $clanName (ID: $clanId)");
            
            // Crear el proyecto personal
            $stmt = $this->db->prepare("
                INSERT INTO Projects (
                    project_name, 
                    description, 
                    clan_id, 
                    created_by_user_id, 
                    status,
                    is_personal
                ) VALUES (?, ?, ?, ?, 'active', 1)
            ");
            
            $result = $stmt->execute([
                $projectData['project_name'],
                $projectData['description'],
                $clanId,
                $userId
            ]);
            
            if ($result) {
                $projectId = $this->db->lastInsertId();
                error_log("createPersonalProject: Proyecto personal creado exitosamente con ID: " . $projectId);
                return $projectId;
            } else {
                error_log("createPersonalProject: Error al crear proyecto: " . print_r($stmt->errorInfo(), true));
                return false;
            }
            
        } catch (Exception $e) {
            error_log("ERROR en createPersonalProject: " . $e->getMessage());
            return false;
        }
    }
}