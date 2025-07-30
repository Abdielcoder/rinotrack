<?php

class KPI {
    private $db;
    
    // Estados de KPI
    const STATUS_PLANNING = 'planning';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CLOSED = 'closed';
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    /**
     * Crear KPI trimestral para un clan
     */
    public function create($clanId, $year, $quarter, $totalPoints = 1000) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Clan_KPIs (clan_id, year, quarter, total_points, status) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $result = $stmt->execute([$clanId, $year, $quarter, $totalPoints, self::STATUS_PLANNING]);
            
            if ($result) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al crear KPI: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener KPI por ID
     */
    public function findById($kpiId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    ck.*,
                    c.clan_name,
                    (ck.total_points - ck.assigned_points) as available_points
                FROM Clan_KPIs ck
                JOIN Clans c ON ck.clan_id = c.clan_id
                WHERE ck.kpi_id = ?
            ");
            $stmt->execute([$kpiId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener KPI: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener KPI de un clan para un trimestre específico
     */
    public function getClanKPI($clanId, $year, $quarter) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    ck.*,
                    c.clan_name,
                    (ck.total_points - ck.assigned_points) as available_points
                FROM Clan_KPIs ck
                JOIN Clans c ON ck.clan_id = c.clan_id
                WHERE ck.clan_id = ? AND ck.year = ? AND ck.quarter = ?
            ");
            $stmt->execute([$clanId, $year, $quarter]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener KPI del clan: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener todos los KPIs de un año
     */
    public function getByYear($year) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    ck.*,
                    c.clan_name,
                    (ck.total_points - ck.assigned_points) as available_points,
                    COUNT(p.project_id) as total_projects,
                    COALESCE(SUM(CASE WHEN p.status = 'completed' THEN 1 ELSE 0 END), 0) as completed_projects
                FROM Clan_KPIs ck
                JOIN Clans c ON ck.clan_id = c.clan_id
                LEFT JOIN Projects p ON ck.kpi_id = p.kpi_quarter_id
                WHERE ck.year = ?
                GROUP BY ck.kpi_id, ck.clan_id, ck.year, ck.quarter
                ORDER BY ck.quarter ASC, c.clan_name ASC
            ");
            $stmt->execute([$year]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener KPIs del año: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener KPI actual (trimestre actual)
     */
    public function getCurrentQuarterKPIs() {
        $currentYear = date('Y');
        $currentQuarter = ceil(date('n') / 3);
        return $this->getByYearAndQuarter($currentYear, $currentQuarter);
    }
    
    /**
     * Obtener KPIs por año y trimestre
     */
    public function getByYearAndQuarter($year, $quarter) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    ck.*,
                    c.clan_name,
                    (ck.total_points - ck.assigned_points) as available_points,
                    COUNT(p.project_id) as total_projects,
                    COALESCE(SUM(p.kpi_points), 0) as assigned_points_calculated,
                    COALESCE(SUM(CASE WHEN p.status = 'completed' THEN p.kpi_points ELSE 0 END), 0) as completed_points
                FROM Clan_KPIs ck
                JOIN Clans c ON ck.clan_id = c.clan_id
                LEFT JOIN Projects p ON ck.kpi_id = p.kpi_quarter_id
                WHERE ck.year = ? AND ck.quarter = ?
                GROUP BY ck.kpi_id, ck.clan_id, ck.year, ck.quarter
                ORDER BY c.clan_name ASC
            ");
            $stmt->execute([$year, $quarter]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener KPIs del trimestre: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Asignar puntos a un proyecto
     */
    public function assignPointsToProject($kpiId, $projectId, $points) {
        try {
            $this->db->beginTransaction();
            
            // Verificar disponibilidad de puntos
            $kpi = $this->findById($kpiId);
            if (!$kpi) {
                throw new Exception("KPI no encontrado");
            }
            
            $availablePoints = $kpi['total_points'] - $kpi['assigned_points'];
            if ($points > $availablePoints) {
                throw new Exception("No hay suficientes puntos disponibles. Disponibles: {$availablePoints}, Solicitados: {$points}");
            }
            
            // Actualizar proyecto
            $stmt = $this->db->prepare("
                UPDATE Projects 
                SET kpi_quarter_id = ?, kpi_points = ? 
                WHERE project_id = ?
            ");
            $stmt->execute([$kpiId, $points, $projectId]);
            
            // Actualizar puntos asignados en KPI
            $stmt = $this->db->prepare("
                UPDATE Clan_KPIs 
                SET assigned_points = assigned_points + ? 
                WHERE kpi_id = ?
            ");
            $stmt->execute([$points, $kpiId]);
            
            // Registrar en historial
            $this->logKPIHistory($kpiId, 'project_assigned', $projectId, null, 0, $points);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al asignar puntos: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar puntos de un proyecto
     */
    public function updateProjectPoints($kpiId, $projectId, $newPoints) {
        try {
            $this->db->beginTransaction();
            
            // Obtener puntos actuales del proyecto
            $stmt = $this->db->prepare("SELECT kpi_points FROM Projects WHERE project_id = ?");
            $stmt->execute([$projectId]);
            $project = $stmt->fetch();
            $oldPoints = $project['kpi_points'] ?? 0;
            
            $pointsDifference = $newPoints - $oldPoints;
            
            // Verificar disponibilidad si los puntos aumentan
            if ($pointsDifference > 0) {
                $kpi = $this->findById($kpiId);
                $availablePoints = $kpi['total_points'] - $kpi['assigned_points'];
                if ($pointsDifference > $availablePoints) {
                    throw new Exception("No hay suficientes puntos disponibles");
                }
            }
            
            // Actualizar proyecto
            $stmt = $this->db->prepare("UPDATE Projects SET kpi_points = ? WHERE project_id = ?");
            $stmt->execute([$newPoints, $projectId]);
            
            // Actualizar puntos asignados en KPI
            $stmt = $this->db->prepare("
                UPDATE Clan_KPIs 
                SET assigned_points = assigned_points + ? 
                WHERE kpi_id = ?
            ");
            $stmt->execute([$pointsDifference, $kpiId]);
            
            // Registrar en historial
            $this->logKPIHistory($kpiId, 'project_updated', $projectId, null, $oldPoints, $newPoints);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al actualizar puntos: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crear KPIs para el trimestre actual si no existen
     */
    public function createCurrentQuarterKPIs() {
        try {
            $currentYear = date('Y');
            $currentQuarter = ceil(date('n') / 3);
            
            // Obtener todos los clanes
            $stmt = $this->db->prepare("SELECT clan_id FROM Clans WHERE 1");
            $stmt->execute();
            $clans = $stmt->fetchAll();
            
            $created = 0;
            foreach ($clans as $clan) {
                // Verificar si ya existe KPI para este clan y trimestre
                $existing = $this->getClanKPI($clan['clan_id'], $currentYear, $currentQuarter);
                if (!$existing) {
                    $kpiId = $this->create($clan['clan_id'], $currentYear, $currentQuarter);
                    if ($kpiId) {
                        $created++;
                    }
                }
            }
            
            return $created;
        } catch (Exception $e) {
            error_log("Error al crear KPIs del trimestre: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Registrar cambio en historial
     */
    private function logKPIHistory($kpiId, $actionType, $projectId = null, $taskId = null, $oldValue = null, $newValue = null, $userId = null, $notes = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO KPI_History (kpi_id, project_id, task_id, action_type, old_value, new_value, user_id, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$kpiId, $projectId, $taskId, $actionType, $oldValue, $newValue, $userId, $notes]);
        } catch (PDOException $e) {
            error_log("Error al registrar historial KPI: " . $e->getMessage());
        }
    }
    
    /**
     * Calcular progreso de KPI basado en tareas completadas
     */
    public function calculateKPIProgress($kpiId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    SUM(p.kpi_points) as total_assigned_points,
                    SUM(CASE 
                        WHEN p.task_distribution_mode = 'automatic' THEN 
                            (SELECT COUNT(*) * (p.kpi_points / GREATEST(COUNT(*), 1)) 
                             FROM Tasks t 
                             WHERE t.project_id = p.project_id AND t.is_completed = 1)
                        ELSE 
                            (SELECT SUM(t.assigned_percentage * p.kpi_points / 100) 
                             FROM Tasks t 
                             WHERE t.project_id = p.project_id AND t.is_completed = 1)
                    END) as completed_points
                FROM Projects p
                WHERE p.kpi_quarter_id = ?
                GROUP BY p.kpi_quarter_id
            ");
            $stmt->execute([$kpiId]);
            $result = $stmt->fetch();
            
            if ($result) {
                $totalPoints = $result['total_assigned_points'] ?? 0;
                $completedPoints = $result['completed_points'] ?? 0;
                
                return [
                    'total_points' => $totalPoints,
                    'completed_points' => $completedPoints,
                    'progress_percentage' => $totalPoints > 0 ? ($completedPoints / $totalPoints) * 100 : 0
                ];
            }
            
            return ['total_points' => 0, 'completed_points' => 0, 'progress_percentage' => 0];
            
        } catch (PDOException $e) {
            error_log("Error al calcular progreso KPI: " . $e->getMessage());
            return ['total_points' => 0, 'completed_points' => 0, 'progress_percentage' => 0];
        }
    }
    
    /**
     * Obtener estadísticas generales de KPIs
     */
    public function getGlobalStats() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(DISTINCT ck.kpi_id) as total_kpis,
                    COUNT(DISTINCT ck.clan_id) as clans_with_kpis,
                    SUM(ck.total_points) as total_points_available,
                    SUM(ck.assigned_points) as total_points_assigned,
                    COUNT(DISTINCT p.project_id) as total_projects_with_kpi,
                    COALESCE(AVG(ck.assigned_points / ck.total_points * 100), 0) as avg_assignment_percentage
                FROM Clan_KPIs ck
                LEFT JOIN Projects p ON ck.kpi_id = p.kpi_quarter_id
                WHERE ck.year = ? AND ck.quarter = ?
            ");
            
            $currentYear = date('Y');
            $currentQuarter = ceil(date('n') / 3);
            $stmt->execute([$currentYear, $currentQuarter]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas KPI: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener ranking de clanes por KPI
     */
    public function getClanRanking($year, $quarter) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.clan_name,
                    ck.total_points,
                    ck.assigned_points,
                    COALESCE(progress.completed_points, 0) as completed_points,
                    COALESCE(progress.completed_points / ck.total_points * 100, 0) as completion_percentage,
                    COUNT(p.project_id) as total_projects
                FROM Clan_KPIs ck
                JOIN Clans c ON ck.clan_id = c.clan_id
                LEFT JOIN Projects p ON ck.kpi_id = p.kpi_quarter_id
                LEFT JOIN (
                    SELECT 
                        p.kpi_quarter_id,
                        SUM(CASE 
                            WHEN p.task_distribution_mode = 'automatic' THEN 
                                (SELECT COUNT(*) * (p.kpi_points / GREATEST((SELECT COUNT(*) FROM Tasks WHERE project_id = p.project_id), 1)) 
                                 FROM Tasks t WHERE t.project_id = p.project_id AND t.is_completed = 1)
                            ELSE 
                                (SELECT SUM(t.assigned_percentage * p.kpi_points / 100) 
                                 FROM Tasks t WHERE t.project_id = p.project_id AND t.is_completed = 1)
                        END) as completed_points
                    FROM Projects p
                    WHERE p.kpi_quarter_id IS NOT NULL
                    GROUP BY p.kpi_quarter_id
                ) progress ON ck.kpi_id = progress.kpi_quarter_id
                WHERE ck.year = ? AND ck.quarter = ?
                GROUP BY ck.kpi_id, c.clan_id
                ORDER BY completion_percentage DESC, completed_points DESC
            ");
            $stmt->execute([$year, $quarter]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener ranking de clanes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener ranking de clanes por ID de trimestre
     */
    public function getClanRankingByQuarterId($quarterId) {
        if (!$quarterId) {
            return [];
        }
        
        try {
            // Obtener datos del trimestre
            $stmt = $this->db->prepare("SELECT year, quarter FROM KPI_Quarters WHERE kpi_quarter_id = ?");
            $stmt->execute([$quarterId]);
            $quarter = $stmt->fetch();
            
            if (!$quarter) {
                return [];
            }
            
            return $this->getClanRanking($quarter['year'], $quarter['quarter']);
        } catch (PDOException $e) {
            error_log("Error al obtener ranking por ID de trimestre: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener el trimestre KPI actualmente activo
     */
    public function getCurrentQuarter() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    kq.*,
                    COALESCE(SUM(p.kpi_points), 0) as assigned_points
                FROM KPI_Quarters kq
                LEFT JOIN Projects p ON kq.kpi_quarter_id = p.kpi_quarter_id
                WHERE kq.is_active = 1
                GROUP BY kq.kpi_quarter_id
                LIMIT 1
            ");
            $stmt->execute();
            $result = $stmt->fetch();
            
            // Si no hay trimestre activo, intentar crear uno automáticamente
            if (!$result) {
                $this->initializeKPISystem();
                // Reintentar la consulta
                $stmt->execute();
                $result = $stmt->fetch();
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error al obtener trimestre actual: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Inicializar automáticamente el sistema KPI
     */
    public function initializeKPISystem() {
        try {
            // Verificar si la tabla KPI_Quarters existe
            $stmt = $this->db->prepare("SHOW TABLES LIKE 'KPI_Quarters'");
            $stmt->execute();
            
            if (!$stmt->fetch()) {
                // Crear tabla KPI_Quarters si no existe
                $this->createKPITables();
            }
            
            // Verificar si ya existe un trimestre activo
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM KPI_Quarters WHERE is_active = 1");
            $stmt->execute();
            $activeCount = $stmt->fetch()['count'];
            
            if ($activeCount == 0) {
                // Crear trimestre actual
                $currentYear = date('Y');
                $currentQuarter = 'Q' . ceil(date('m') / 3);
                
                $stmt = $this->db->prepare("
                    INSERT INTO KPI_Quarters (quarter, year, total_points, is_active, status, created_at) 
                    VALUES (?, ?, 1000, 1, 'active', NOW())
                    ON DUPLICATE KEY UPDATE is_active = 1, status = 'active'
                ");
                $stmt->execute([$currentQuarter, $currentYear]);
                
                error_log("Sistema KPI inicializado automáticamente: $currentQuarter $currentYear");
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Error al inicializar sistema KPI: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crear tablas KPI si no existen
     */
    private function createKPITables() {
        try {
            // Crear tabla KPI_Quarters
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS `KPI_Quarters` (
                  `kpi_quarter_id` int NOT NULL AUTO_INCREMENT,
                  `quarter` varchar(10) NOT NULL,
                  `year` int NOT NULL,
                  `total_points` int NOT NULL DEFAULT '1000',
                  `is_active` tinyint(1) NOT NULL DEFAULT '0',
                  `status` enum('planning','active','completed','closed') DEFAULT 'planning',
                  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`kpi_quarter_id`),
                  UNIQUE KEY `unique_quarter_year` (`quarter`,`year`),
                  KEY `idx_is_active` (`is_active`),
                  KEY `idx_year_quarter` (`year`,`quarter`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            
            // Agregar columnas a Projects si no existen
            try {
                $this->db->exec("ALTER TABLE Projects ADD COLUMN kpi_quarter_id int NULL");
                $this->db->exec("ALTER TABLE Projects ADD KEY idx_kpi_quarter (kpi_quarter_id)");
            } catch (PDOException $e) {
                // Columna ya existe, ignorar error
            }
            
            try {
                $this->db->exec("ALTER TABLE Projects ADD COLUMN kpi_points int NULL DEFAULT 0");
            } catch (PDOException $e) {
                // Columna ya existe, ignorar error
            }
            
            try {
                $this->db->exec("ALTER TABLE Projects ADD COLUMN task_distribution_mode enum('automatic','percentage') DEFAULT 'automatic'");
            } catch (PDOException $e) {
                // Columna ya existe, ignorar error
            }
            
            error_log("Tablas KPI creadas exitosamente");
            return true;
        } catch (PDOException $e) {
            error_log("Error al crear tablas KPI: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener todos los trimestres KPI
     */
    public function getAllQuarters() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    kq.*,
                    COALESCE(SUM(p.kpi_points), 0) as assigned_points,
                    COUNT(p.project_id) as projects_count
                FROM KPI_Quarters kq
                LEFT JOIN Projects p ON kq.kpi_quarter_id = p.kpi_quarter_id
                GROUP BY kq.kpi_quarter_id
                ORDER BY kq.year DESC, kq.quarter DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener trimestres: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Crear nuevo trimestre KPI
     */
    public function createQuarter($quarter, $year, $totalPoints = 1000, $activateImmediately = false) {
        try {
            $this->db->beginTransaction();
            
            // Verificar si ya existe el trimestre
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM KPI_Quarters WHERE quarter = ? AND year = ?");
            $stmt->execute([$quarter, $year]);
            if ($stmt->fetch()['count'] > 0) {
                throw new Exception("Ya existe un trimestre {$quarter} para el año {$year}");
            }
            
            // Si se debe activar inmediatamente, desactivar otros trimestres
            if ($activateImmediately) {
                $stmt = $this->db->prepare("UPDATE KPI_Quarters SET is_active = 0");
                $stmt->execute();
            }
            
            // Crear el trimestre
            $stmt = $this->db->prepare("
                INSERT INTO KPI_Quarters (quarter, year, total_points, is_active, status, created_at) 
                VALUES (?, ?, ?, ?, 'planning', NOW())
            ");
            $result = $stmt->execute([$quarter, $year, $totalPoints, $activateImmediately ? 1 : 0]);
            
            if ($result) {
                $quarterId = $this->db->lastInsertId();
                $this->db->commit();
                return $quarterId;
            } else {
                throw new Exception("Error al insertar trimestre");
            }
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al crear trimestre: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener trimestre por ID
     */
    public function findQuarterById($quarterId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    kq.*,
                    COALESCE(SUM(p.kpi_points), 0) as assigned_points,
                    COUNT(p.project_id) as projects_count
                FROM KPI_Quarters kq
                LEFT JOIN Projects p ON kq.kpi_quarter_id = p.kpi_quarter_id
                WHERE kq.kpi_quarter_id = ?
                GROUP BY kq.kpi_quarter_id
            ");
            $stmt->execute([$quarterId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener trimestre por ID: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Actualizar trimestre KPI
     */
    public function updateQuarter($quarterId, $quarter, $year, $totalPoints) {
        try {
            $this->db->beginTransaction();
            
            // Verificar que el trimestre existe
            $existingQuarter = $this->findQuarterById($quarterId);
            if (!$existingQuarter) {
                throw new Exception("Trimestre no encontrado");
            }
            
            // Verificar si ya existe otro trimestre con el mismo período (excepto el actual)
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM KPI_Quarters 
                WHERE quarter = ? AND year = ? AND kpi_quarter_id != ?
            ");
            $stmt->execute([$quarter, $year, $quarterId]);
            if ($stmt->fetch()['count'] > 0) {
                throw new Exception("Ya existe otro trimestre {$quarter} para el año {$year}");
            }
            
            // Actualizar el trimestre
            $stmt = $this->db->prepare("
                UPDATE KPI_Quarters 
                SET quarter = ?, year = ?, total_points = ?, updated_at = NOW()
                WHERE kpi_quarter_id = ?
            ");
            $result = $stmt->execute([$quarter, $year, $totalPoints, $quarterId]);
            
            if ($result) {
                $this->db->commit();
                return true;
            } else {
                throw new Exception("Error al actualizar trimestre");
            }
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al actualizar trimestre: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Activar trimestre KPI (desactiva los demás)
     */
    public function activateQuarter($quarterId) {
        try {
            $this->db->beginTransaction();
            
            // Verificar que el trimestre existe
            $quarter = $this->findQuarterById($quarterId);
            if (!$quarter) {
                throw new Exception("Trimestre no encontrado");
            }
            
            // Desactivar todos los trimestres
            $stmt = $this->db->prepare("UPDATE KPI_Quarters SET is_active = 0, status = 'completed'");
            $stmt->execute();
            
            // Activar el trimestre seleccionado
            $stmt = $this->db->prepare("
                UPDATE KPI_Quarters 
                SET is_active = 1, status = 'active', updated_at = NOW()
                WHERE kpi_quarter_id = ?
            ");
            $result = $stmt->execute([$quarterId]);
            
            if ($result) {
                $this->db->commit();
                return true;
            } else {
                throw new Exception("Error al activar trimestre");
            }
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al activar trimestre: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar trimestre KPI
     */
    public function deleteQuarter($quarterId) {
        try {
            $this->db->beginTransaction();
            
            // Verificar que el trimestre existe
            $quarter = $this->findQuarterById($quarterId);
            if (!$quarter) {
                throw new Exception("Trimestre no encontrado");
            }
            
            // No permitir eliminar trimestre activo
            if ($quarter['is_active']) {
                throw new Exception("No se puede eliminar el trimestre activo");
            }
            
            // Verificar si tiene proyectos asignados
            if ($quarter['projects_count'] > 0) {
                throw new Exception("No se puede eliminar un trimestre con proyectos asignados");
            }
            
            // Eliminar el trimestre
            $stmt = $this->db->prepare("DELETE FROM KPI_Quarters WHERE kpi_quarter_id = ?");
            $result = $stmt->execute([$quarterId]);
            
            if ($result) {
                $this->db->commit();
                return true;
            } else {
                throw new Exception("Error al eliminar trimestre");
            }
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al eliminar trimestre: " . $e->getMessage());
            throw $e; // Re-lanzar para que el controlador pueda capturar el mensaje específico
        }
    }
    
    /**
     * Obtener puntos asignados a proyectos en un trimestre
     */
    public function getAssignedPoints($kpiQuarterId) {
        try {
            if (!$kpiQuarterId) {
                return 0;
            }
            
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(kpi_points), 0) as assigned_points
                FROM Projects 
                WHERE kpi_quarter_id = ?
            ");
            $stmt->execute([$kpiQuarterId]);
            $result = $stmt->fetch();
            
            return (int)($result['assigned_points'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error al obtener puntos asignados: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtener la conexión de base de datos (para uso interno del controlador)
     */
    public function getDB() {
        return $this->db;
    }
}
?>