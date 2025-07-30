<?php

class Clan {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    /**
     * Crear nuevo clan
     */
    public function create($clanName, $clanDepartamento = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Clans (clan_name, clan_departamento, created_at) 
                VALUES (?, ?, NOW())
            ");
            $result = $stmt->execute([$clanName, $clanDepartamento]);
            
            if ($result) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al crear clan: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener todos los clanes
     */
    public function getAll() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.*,
                    COUNT(DISTINCT cm.user_id) as member_count,
                    COUNT(DISTINCT p.project_id) as project_count
                FROM Clans c
                LEFT JOIN Clan_Members cm ON c.clan_id = cm.clan_id
                LEFT JOIN Projects p ON c.clan_id = p.clan_id
                GROUP BY c.clan_id
                ORDER BY c.clan_name
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener clanes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener clan por ID
     */
    public function findById($clanId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.*,
                    COUNT(DISTINCT cm.user_id) as member_count,
                    COUNT(DISTINCT p.project_id) as project_count
                FROM Clans c
                LEFT JOIN Clan_Members cm ON c.clan_id = cm.clan_id
                LEFT JOIN Projects p ON c.clan_id = p.clan_id
                WHERE c.clan_id = ?
                GROUP BY c.clan_id
            ");
            $stmt->execute([$clanId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al buscar clan: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar si un nombre de clan ya existe
     */
    public function exists($clanName, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM Clans WHERE clan_name = ?";
            $params = [$clanName];
            
            if ($excludeId !== null) {
                $sql .= " AND clan_id != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar existencia de clan: " . $e->getMessage());
            return true; // En caso de error, asumir que existe para evitar duplicados
        }
    }
    
    /**
     * Actualizar clan
     */
    public function update($clanId, $clanName, $clanDepartamento = null) {
        try {
            $stmt = $this->db->prepare("
                UPDATE Clans 
                SET clan_name = ?, clan_departamento = ?
                WHERE clan_id = ?
            ");
            return $stmt->execute([$clanName, $clanDepartamento, $clanId]);
        } catch (PDOException $e) {
            error_log("Error al actualizar clan: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar clan
     */
    public function delete($clanId) {
        try {
            // Verificar si el clan tiene proyectos asociados
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM Projects WHERE clan_id = ?");
            $stmt->execute([$clanId]);
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                return ['error' => 'No se puede eliminar el clan porque tiene proyectos asociados'];
            }
            
            $stmt = $this->db->prepare("DELETE FROM Clans WHERE clan_id = ?");
            return $stmt->execute([$clanId]);
        } catch (PDOException $e) {
            error_log("Error al eliminar clan: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Agregar miembro al clan
     */
    public function addMember($clanId, $userId) {
        try {
            // Verificar que los parámetros son válidos
            if (!is_numeric($clanId) || !is_numeric($userId) || $clanId <= 0 || $userId <= 0) {
                error_log("ERROR Clan::addMember - Parámetros inválidos: clanId=$clanId, userId=$userId");
                return false;
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO Clan_Members (clan_id, user_id) 
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE clan_id = clan_id
            ");
            return $stmt->execute([$clanId, $userId]);
        } catch (PDOException $e) {
            error_log("ERROR Clan::addMember - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar miembro del clan
     */
    public function removeMember($clanId, $userId) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM Clan_Members 
                WHERE clan_id = ? AND user_id = ?
            ");
            return $stmt->execute([$clanId, $userId]);
        } catch (PDOException $e) {
            error_log("Error al eliminar miembro: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener miembros del clan
     */
    public function getMembers($clanId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.user_id,
                    u.username,
                    u.full_name,
                    u.email,
                    u.is_active,
                    r.role_name
                FROM Clan_Members cm
                JOIN Users u ON cm.user_id = u.user_id
                LEFT JOIN User_Roles ur ON u.user_id = ur.user_id
                LEFT JOIN Roles r ON ur.role_id = r.role_id
                WHERE cm.clan_id = ?
                ORDER BY u.full_name
            ");
            $stmt->execute([$clanId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener miembros: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener clanes del usuario
     */
    public function getUserClans($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.*
                FROM Clans c
                JOIN Clan_Members cm ON c.clan_id = cm.clan_id
                WHERE cm.user_id = ?
                ORDER BY c.clan_name
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener clanes del usuario: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener estadísticas de clanes
     */
    public function getStats() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_clans,
                    COALESCE(AVG(member_counts.member_count), 0) as avg_members_per_clan,
                    COALESCE(MAX(member_counts.member_count), 0) as max_members,
                    COALESCE(MIN(member_counts.member_count), 0) as min_members
                FROM (
                    SELECT 
                        c.clan_id,
                        COUNT(cm.user_id) as member_count
                    FROM Clans c
                    LEFT JOIN Clan_Members cm ON c.clan_id = cm.clan_id
                    GROUP BY c.clan_id
                ) as member_counts
            ");
            $stmt->execute();
            $result = $stmt->fetch();
            
            // Asegurar que todos los valores sean numéricos
            return [
                'total_clans' => (int)($result['total_clans'] ?? 0),
                'avg_members_per_clan' => (float)($result['avg_members_per_clan'] ?? 0),
                'max_members' => (int)($result['max_members'] ?? 0),
                'min_members' => (int)($result['min_members'] ?? 0)
            ];
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas de clanes: " . $e->getMessage());
            return [
                'total_clans' => 0,
                'avg_members_per_clan' => 0.0,
                'max_members' => 0,
                'min_members' => 0
            ];
        }
    }
}