<?php

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    /**
     * Buscar usuario por username o email
     */
    public function findByUsernameOrEmail($username) {
        try {
            $stmt = $this->db->prepare("
                SELECT user_id, username, email, password_hash, full_name, is_active, last_login, created_at 
                FROM Users 
                WHERE (username = ? OR email = ?) AND is_active = 1
            ");
            $stmt->execute([$username, $username]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al buscar usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Buscar usuario por ID
     */
    public function findById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT user_id, username, email, full_name, is_active, last_login, created_at 
                FROM Users 
                WHERE user_id = ? AND is_active = 1
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al buscar usuario por ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar último login del usuario
     */
    public function updateLastLogin($userId) {
        try {
            $stmt = $this->db->prepare("UPDATE Users SET last_login = NOW() WHERE user_id = ?");
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Error al actualizar último login: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crear nuevo usuario
     */
    public function create($username, $email, $password, $fullName) {
        try {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("
                INSERT INTO Users (username, email, password_hash, full_name, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $result = $stmt->execute([$username, $email, $passwordHash, $fullName]);
            
            if ($result) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al crear usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar si un username o email ya existe
     */
    public function exists($username, $email) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM Users 
                WHERE username = ? OR email = ?
            ");
            $stmt->execute([$username, $email]);
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar existencia de usuario: " . $e->getMessage());
            return true; // En caso de error, asumir que existe para evitar duplicados
        }
    }
    
    /**
     * Activar/desactivar usuario
     */
    public function setActive($userId, $active = true) {
        try {
            $stmt = $this->db->prepare("UPDATE Users SET is_active = ? WHERE user_id = ?");
            return $stmt->execute([$active ? 1 : 0, $userId]);
        } catch (PDOException $e) {
            error_log("Error al cambiar estado de usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener todos los usuarios con información de rol
     */
    public function getAllWithRoles() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.*,
                    r.role_name,
                    r.role_id
                FROM Users u
                LEFT JOIN User_Roles ur ON u.user_id = ur.user_id
                LEFT JOIN Roles r ON ur.role_id = r.role_id
                ORDER BY u.created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener usuarios con roles: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Actualizar información del usuario
     */
    public function update($userId, $username, $email, $fullName, $isActive = 1) {
        try {
            $stmt = $this->db->prepare("
                UPDATE Users 
                SET username = ?, email = ?, full_name = ?, is_active = ?
                WHERE user_id = ?
            ");
            return $stmt->execute([$username, $email, $fullName, $isActive, $userId]);
        } catch (PDOException $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar solo el estado del usuario
     */
    public function updateStatus($userId, $isActive) {
        try {
            $stmt = $this->db->prepare("
                UPDATE Users 
                SET is_active = ?
                WHERE user_id = ?
            ");
            return $stmt->execute([$isActive, $userId]);
        } catch (PDOException $e) {
            error_log("Error al actualizar estado del usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cambiar contraseña del usuario
     */
    public function updatePassword($userId, $newPassword) {
        try {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE Users SET password_hash = ? WHERE user_id = ?");
            return $stmt->execute([$passwordHash, $userId]);
        } catch (PDOException $e) {
            error_log("Error al actualizar contraseña: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar usuario
     */
    public function delete($userId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM Users WHERE user_id = ?");
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener estadísticas de usuarios
     */
    public function getStats() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_users,
                    SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive_users,
                    SUM(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as recent_users
                FROM Users
            ");
            $stmt->execute();
            $result = $stmt->fetch();
            
            // Asegurar que todos los valores sean numéricos
            return [
                'total_users' => (int)($result['total_users'] ?? 0),
                'active_users' => (int)($result['active_users'] ?? 0),
                'inactive_users' => (int)($result['inactive_users'] ?? 0),
                'recent_users' => (int)($result['recent_users'] ?? 0)
            ];
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas de usuarios: " . $e->getMessage());
            return [
                'total_users' => 0,
                'active_users' => 0,
                'inactive_users' => 0,
                'recent_users' => 0
            ];
        }
    }
    
    /**
     * Buscar usuarios por término
     */
    public function search($searchTerm) {
        try {
            $searchTerm = '%' . $searchTerm . '%';
            $stmt = $this->db->prepare("
                SELECT 
                    u.*,
                    r.role_name
                FROM Users u
                LEFT JOIN User_Roles ur ON u.user_id = ur.user_id
                LEFT JOIN Roles r ON ur.role_id = r.role_id
                WHERE u.username LIKE ? 
                   OR u.email LIKE ? 
                   OR u.full_name LIKE ?
                ORDER BY u.full_name
            ");
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al buscar usuarios: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener el clan del usuario
     */
    public function getUserClan($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.clan_id,
                    c.clan_name,
                    c.clan_departamento
                FROM Clans c
                JOIN Clan_Members cm ON c.clan_id = cm.clan_id
                WHERE cm.user_id = ?
                LIMIT 1
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener clan del usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener usuarios por clan
     */
    public function getUsersByClan($clanId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.user_id,
                    u.username,
                    u.full_name,
                    u.email,
                    u.is_active,
                    u.last_login,
                    u.created_at,
                    r.role_name
                FROM Users u
                JOIN Clan_Members cm ON u.user_id = cm.user_id
                LEFT JOIN User_Roles ur ON u.user_id = ur.user_id
                LEFT JOIN Roles r ON ur.role_id = r.role_id
                WHERE cm.clan_id = ?
                ORDER BY u.full_name
            ");
            $stmt->execute([$clanId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener usuarios por clan: " . $e->getMessage());
            return [];
        }
    }
}