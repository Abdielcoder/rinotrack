<?php

class Role {
    private $db;
    
    // Jerarquía de roles (de mayor a menor autoridad)
    const SUPER_ADMIN = 'super_admin';
    const ADMIN = 'admin';
    const LIDER_CLAN = 'lider_clan';
    const USUARIO_NORMAL = 'usuario_normal';
    
    const ROLE_HIERARCHY = [
        self::SUPER_ADMIN => 1,
        self::ADMIN => 2,
        self::LIDER_CLAN => 3,
        self::USUARIO_NORMAL => 4
    ];
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    /**
     * Obtener todos los roles
     */
    public function getAll() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    r.*,
                    COUNT(ur.user_id) as user_count
                FROM Roles r
                LEFT JOIN User_Roles ur ON r.role_id = ur.role_id
                GROUP BY r.role_id
                ORDER BY r.role_id
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener roles: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener rol por ID
     */
    public function findById($roleId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Roles WHERE role_id = ?");
            $stmt->execute([$roleId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al buscar rol: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener rol por nombre
     */
    public function findByName($roleName) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Roles WHERE role_name = ?");
            $stmt->execute([$roleName]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al buscar rol por nombre: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener rol del usuario
     */
    public function getUserRole($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT r.*
                FROM Roles r
                JOIN User_Roles ur ON r.role_id = ur.role_id
                WHERE ur.user_id = ?
                LIMIT 1
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener rol del usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Asignar rol a usuario
     */
    public function assignToUser($userId, $roleId) {
        try {
            // Verificar que los parámetros son válidos
            if (!is_numeric($userId) || !is_numeric($roleId) || $userId <= 0 || $roleId <= 0) {
                error_log("ERROR assignToUser - Parámetros inválidos: userId=$userId, roleId=$roleId");
                return false;
            }
            
            // Obtener información de roles una sola vez
            $currentRole = $this->getUserRole($userId);
            $newRole = $this->findById($roleId);
            
            // PROTECCIÓN CRÍTICA: No permitir degradar el rol del super admin existente
            if ($currentRole && $currentRole['role_name'] === 'super_admin' && 
                (!$newRole || $newRole['role_name'] !== 'super_admin')) {
                error_log("SECURITY WARNING: Intento de degradar rol del super admin (User ID: $userId). Operación bloqueada.");
                return false;
            }
            
            // Log de operaciones de cambio de rol para auditoría
            error_log("ROLE CHANGE: Usuario $userId - " . 
                     ($currentRole ? $currentRole['role_name'] : 'SIN_ROL') . 
                     " -> " . 
                     ($newRole ? $newRole['role_name'] : 'DESCONOCIDO'));
            
            // Primero eliminar roles existentes del usuario específico
            $stmt = $this->db->prepare("DELETE FROM User_Roles WHERE user_id = ?");
            $result = $stmt->execute([$userId]);
            
            // Luego asignar el nuevo rol
            $stmt = $this->db->prepare("INSERT INTO User_Roles (user_id, role_id) VALUES (?, ?)");
            $result = $stmt->execute([$userId, $roleId]);
            
            return $result;
        } catch (PDOException $e) {
            error_log("ERROR assignToUser - PDO Exception: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar si un usuario tiene un rol específico
     */
    public function userHasRole($userId, $roleName) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM User_Roles ur
                JOIN Roles r ON ur.role_id = r.role_id
                WHERE ur.user_id = ? AND r.role_name = ?
            ");
            $stmt->execute([$userId, $roleName]);
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar rol del usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar si un usuario tiene al menos un nivel de autoridad
     */
    public function userHasMinimumRole($userId, $minimumRole) {
        try {
            $userRole = $this->getUserRole($userId);
            if (!$userRole) {
                return false;
            }
            
            $userLevel = self::ROLE_HIERARCHY[$userRole['role_name']] ?? 999;
            $minimumLevel = self::ROLE_HIERARCHY[$minimumRole] ?? 999;
            
            return $userLevel <= $minimumLevel;
        } catch (Exception $e) {
            error_log("Error al verificar nivel de rol: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener usuarios por rol
     */
    public function getUsersByRole($roleName) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.user_id,
                    u.username,
                    u.full_name,
                    u.email,
                    u.is_active,
                    u.last_login,
                    u.created_at
                FROM Users u
                JOIN User_Roles ur ON u.user_id = ur.user_id
                JOIN Roles r ON ur.role_id = r.role_id
                WHERE r.role_name = ?
                ORDER BY u.full_name
            ");
            $stmt->execute([$roleName]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener usuarios por rol: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener nombre de rol legible para humanos
     */
    public function getDisplayName($roleName) {
        $displayNames = [
            self::SUPER_ADMIN => 'Super Administrador',
            self::ADMIN => 'Administrador',
            self::LIDER_CLAN => 'Líder de Clan',
            self::USUARIO_NORMAL => 'Usuario Normal'
        ];
        
        return $displayNames[$roleName] ?? $roleName;
    }
    
    /**
     * Obtener descripción del rol
     */
    public function getDescription($roleName) {
        $descriptions = [
            self::SUPER_ADMIN => 'Acceso completo al sistema, puede gestionar todo',
            self::ADMIN => 'Puede gestionar usuarios, clanes y proyectos',
            self::LIDER_CLAN => 'Puede gestionar su clan y proyectos asignados',
            self::USUARIO_NORMAL => 'Acceso básico, puede participar en proyectos'
        ];
        
        return $descriptions[$roleName] ?? 'Sin descripción disponible';
    }
    
    /**
     * Obtener permisos del rol
     */
    public function getPermissions($roleName) {
        $permissions = [
            self::SUPER_ADMIN => [
                'manage_users', 'manage_roles', 'manage_clans', 'manage_projects', 
                'manage_tasks', 'view_admin_panel', 'delete_any', 'edit_any'
            ],
            self::ADMIN => [
                'manage_users', 'manage_clans', 'manage_projects', 
                'manage_tasks', 'view_admin_panel', 'edit_any'
            ],
            self::LIDER_CLAN => [
                'manage_clan_projects', 'manage_clan_members', 'manage_clan_tasks', 'view_clan_admin'
            ],
            self::USUARIO_NORMAL => [
                'view_projects', 'manage_own_tasks', 'view_profile'
            ]
        ];
        
        return $permissions[$roleName] ?? [];
    }
    
    /**
     * Verificar si un usuario tiene un permiso específico
     */
    public function userHasPermission($userId, $permission) {
        try {
            $userRole = $this->getUserRole($userId);
            if (!$userRole) {
                return false;
            }
            
            $permissions = $this->getPermissions($userRole['role_name']);
            return in_array($permission, $permissions);
        } catch (Exception $e) {
            error_log("Error al verificar permiso: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener estadísticas de roles
     */
    public function getStats() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    r.role_name,
                    r.role_id,
                    COUNT(ur.user_id) as user_count
                FROM Roles r
                LEFT JOIN User_Roles ur ON r.role_id = ur.role_id
                GROUP BY r.role_id, r.role_name
                ORDER BY r.role_id
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas de roles: " . $e->getMessage());
            return [];
        }
    }
}