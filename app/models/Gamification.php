<?php

class Gamification {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    /**
     * Obtener conexión a la base de datos
     */
    public function getDb() {
        return $this->db;
    }
    
    /**
     * Obtener todos los badges disponibles
     */
    public function getAllBadges($activeOnly = true) {
        try {
            $sql = "SELECT * FROM Gamification_Badges";
            if ($activeOnly) {
                $sql .= " WHERE is_active = 1";
            }
            $sql .= " ORDER BY badge_category, badge_name";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener badges: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener badges por categoría
     */
    public function getBadgesByCategory($category) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM Gamification_Badges 
                WHERE badge_category = ? AND is_active = 1 
                ORDER BY badge_name
            ");
            $stmt->execute([$category]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener badges por categoría: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener badges ganados por un usuario
     */
    public function getUserBadges($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT ub.*, b.badge_name, b.badge_description, b.badge_icon, b.badge_color, b.badge_category
                FROM Gamification_User_Badges ub
                JOIN Gamification_Badges b ON ub.badge_id = b.badge_id
                WHERE ub.user_id = ?
                ORDER BY ub.earned_at DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener badges del usuario: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Verificar si un usuario ya tiene un badge
     */
    public function userHasBadge($userId, $badgeId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM Gamification_User_Badges 
                WHERE user_id = ? AND badge_id = ?
            ");
            $stmt->execute([$userId, $badgeId]);
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar badge del usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Asignar badge a un usuario
     */
    public function awardBadge($userId, $badgeId, $contextData = null) {
        try {
            // Verificar si ya tiene el badge
            if ($this->userHasBadge($userId, $badgeId)) {
                return false;
            }
            
            // Obtener información del badge
            $badge = $this->getBadgeById($badgeId);
            if (!$badge) {
                return false;
            }
            
            $this->db->beginTransaction();
            
            // Asignar el badge
            $stmt = $this->db->prepare("
                INSERT INTO Gamification_User_Badges (user_id, badge_id, earned_points, context_data)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$userId, $badgeId, $badge['points_reward'], json_encode($contextData)]);
            
            // Asignar puntos
            if ($badge['points_reward'] > 0) {
                $this->addPoints($userId, $badge['points_reward'], 'earned', 'badge_award', 
                    "Badge ganado: " . $badge['badge_name'], $badgeId, 'badge');
            }
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error al asignar badge: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener badge por ID
     */
    public function getBadgeById($badgeId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Gamification_Badges WHERE badge_id = ?");
            $stmt->execute([$badgeId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener badge por ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Agregar puntos a un usuario
     */
    public function addPoints($userId, $points, $type = 'earned', $source = 'system', $description = '', $relatedId = null, $relatedType = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Gamification_User_Points (user_id, points, points_type, source, description, related_id, related_type)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$userId, $points, $type, $source, $description, $relatedId, $relatedType]);
        } catch (PDOException $e) {
            error_log("Error al agregar puntos: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener puntos totales de un usuario
     */
    public function getUserTotalPoints($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    SUM(CASE WHEN points_type = 'earned' OR points_type = 'bonus' THEN points ELSE 0 END) as earned,
                    SUM(CASE WHEN points_type = 'spent' OR points_type = 'penalty' THEN points ELSE 0 END) as spent
                FROM Gamification_User_Points 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            
            return ($result['earned'] ?? 0) - ($result['spent'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error al obtener puntos totales: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtener historial de puntos de un usuario
     */
    public function getUserPointsHistory($userId, $limit = 50) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM Gamification_User_Points 
                WHERE user_id = ? 
                ORDER BY earned_at DESC 
                LIMIT ?
            ");
            $stmt->execute([$userId, $limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener historial de puntos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener logros de clan
     */
    public function getClanAchievements($clanId) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM Gamification_Clan_Achievements 
                WHERE clan_id = ? AND is_active = 1 
                ORDER BY achievement_category, achievement_name
            ");
            $stmt->execute([$clanId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener logros de clan: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener progreso de logros de clan
     */
    public function getClanAchievementProgress($clanId) {
        try {
            $stmt = $this->db->prepare("
                SELECT ca.*, cp.current_progress, cp.target_progress, cp.is_completed, cp.completed_at
                FROM Gamification_Clan_Achievements ca
                LEFT JOIN Gamification_Clan_Progress cp ON ca.clan_achievement_id = cp.achievement_id AND cp.clan_id = ca.clan_id
                WHERE ca.clan_id = ? AND ca.is_active = 1
                ORDER BY ca.achievement_category, ca.achievement_name
            ");
            $stmt->execute([$clanId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener progreso de logros de clan: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Actualizar progreso de logro de clan
     */
    public function updateClanAchievementProgress($clanId, $achievementId, $progress) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Gamification_Clan_Progress (clan_id, achievement_id, current_progress, target_progress, is_completed)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                current_progress = VALUES(current_progress),
                is_completed = VALUES(is_completed),
                completed_at = CASE WHEN VALUES(is_completed) = 1 AND is_completed = 0 THEN NOW() ELSE completed_at END
            ");
            
            $achievement = $this->getClanAchievementById($achievementId);
            if (!$achievement) {
                return false;
            }
            
            $criteria = json_decode($achievement['required_criteria'], true);
            $targetProgress = $criteria['target_value'] ?? 1;
            $isCompleted = $progress >= $targetProgress;
            
            return $stmt->execute([$clanId, $achievementId, $progress, $targetProgress, $isCompleted ? 1 : 0]);
        } catch (PDOException $e) {
            error_log("Error al actualizar progreso de logro de clan: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener logro de clan por ID
     */
    public function getClanAchievementById($achievementId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Gamification_Clan_Achievements WHERE clan_achievement_id = ?");
            $stmt->execute([$achievementId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener logro de clan por ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener eventos activos
     */
    public function getActiveEvents() {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM Gamification_Events 
                WHERE is_active = 1 AND start_date <= CURDATE() AND end_date >= CURDATE()
                ORDER BY start_date
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener eventos activos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener leaderboard
     */
    public function getLeaderboard($limit = 20) {
        try {
            $stmt = $this->db->prepare("
                SELECT l.*, u.username, u.full_name, c.clan_name
                FROM Gamification_Leaderboard l
                JOIN Users u ON l.user_id = u.user_id
                LEFT JOIN Clans c ON l.clan_id = c.clan_id
                ORDER BY l.total_points DESC, l.badges_earned DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener leaderboard: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Actualizar leaderboard para un usuario
     */
    public function updateUserLeaderboard($userId) {
        try {
            $totalPoints = $this->getUserTotalPoints($userId);
            $badgesCount = $this->getUserBadgesCount($userId);
            $tasksCompleted = $this->getUserTasksCompleted($userId);
            $projectsCompleted = $this->getUserProjectsCompleted($userId);
            
            $userClan = $this->getUserClan($userId);
            $clanId = $userClan ? $userClan['clan_id'] : null;
            
            $stmt = $this->db->prepare("
                INSERT INTO Gamification_Leaderboard (user_id, clan_id, total_points, badges_earned, tasks_completed, projects_completed)
                VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                total_points = VALUES(total_points),
                badges_earned = VALUES(badges_earned),
                tasks_completed = VALUES(tasks_completed),
                projects_completed = VALUES(projects_completed),
                last_updated = NOW()
            ");
            
            return $stmt->execute([$userId, $clanId, $totalPoints, $badgesCount, $tasksCompleted, $projectsCompleted]);
        } catch (PDOException $e) {
            error_log("Error al actualizar leaderboard: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener cantidad de badges de un usuario
     */
    private function getUserBadgesCount($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM Gamification_User_Badges 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Obtener tareas completadas de un usuario
     */
    private function getUserTasksCompleted($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM Tasks 
                WHERE assigned_to_user_id = ? AND status = 'completed'
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Obtener proyectos completados de un usuario
     */
    private function getUserProjectsCompleted($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM Projects 
                WHERE created_by_user_id = ? AND status = 'completed'
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Obtener clan de un usuario
     */
    private function getUserClan($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.* FROM Clans c
                JOIN Clan_Members cm ON c.clan_id = cm.clan_id
                WHERE cm.user_id = ?
                LIMIT 1
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Obtener estadísticas de gamificación
     */
    public function getGamificationStats() {
        try {
            $stats = [];
            
            // Total de usuarios con badges
            $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT user_id) as count 
                FROM Gamification_User_Badges
            ");
            $stmt->execute();
            $stats['users_with_badges'] = $stmt->fetch()['count'] ?? 0;
            
            // Total de badges otorgados
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM Gamification_User_Badges");
            $stmt->execute();
            $stats['total_badges_awarded'] = $stmt->fetch()['count'] ?? 0;
            
            // Total de puntos otorgados
            $stmt = $this->db->prepare("
                SELECT SUM(points) as total 
                FROM Gamification_User_Points 
                WHERE points_type IN ('earned', 'bonus')
            ");
            $stmt->execute();
            $stats['total_points_awarded'] = $stmt->fetch()['total'] ?? 0;
            
            // Eventos activos
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM Gamification_Events 
                WHERE is_active = 1 AND start_date <= CURDATE() AND end_date >= CURDATE()
            ");
            $stmt->execute();
            $stats['active_events'] = $stmt->fetch()['count'] ?? 0;
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas de gamificación: " . $e->getMessage());
            return [];
        }
    }
} 