<?php

class Auth {
    private $db;
    private $userModel;
    
    public function __construct() {
        $this->db = Database::getConnection();
        $this->userModel = new User();
    }
    
    /**
     * Intentar login con username/email y contraseña
     */
    public function login($username, $password, $rememberMe = false) {
        // Buscar usuario
        $user = $this->userModel->findByUsernameOrEmail($username);
        
        // TEMPORAL: Comparación directa sin hash (SOLO PARA DESARROLLO)
        // TODO: Volver a implementar password_verify() antes de producción
        if (!$user || $password !== $user['password_hash']) {
            return false;
        }
        
        // COMENTADO TEMPORALMENTE - CÓDIGO ORIGINAL CON HASH:
        // if (!$user || !password_verify($password, $user['password_hash'])) {
        //     return false;
        // }
        
        // Actualizar último login
        $this->userModel->updateLastLogin($user['user_id']);
        
        // Crear sesión
        $this->createSession($user);
        
        // Configurar "recordarme" si está seleccionado
        if ($rememberMe) {
            $this->setRememberToken($user['user_id']);
        }
        
        return $user;
    }
    
    /**
     * Crear sesión de usuario
     */
    private function createSession($user) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['login_time'] = time();
    }
    
    /**
     * Configurar token de "recordarme"
     */
    private function setRememberToken($userId) {
        try {
            $remember_token = bin2hex(random_bytes(32));
            
            // Guardar token en la base de datos
            $stmt = $this->db->prepare("
                INSERT INTO remember_tokens (user_id, token, expires_at) 
                VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))
                ON DUPLICATE KEY UPDATE 
                token = VALUES(token), 
                expires_at = VALUES(expires_at)
            ");
            $stmt->execute([$userId, hash('sha256', $remember_token)]);
            
            // Establecer cookie (30 días)
            setcookie('remember_token', $remember_token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
        } catch (PDOException $e) {
            error_log("Error al configurar token de recordar: " . $e->getMessage());
        }
    }
    
    /**
     * Verificar si el usuario está autenticado
     */
    public function isLoggedIn() {
        // Verificar sesión activa
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            return true;
        }
        
        // Verificar token de "recordarme"
        if (isset($_COOKIE['remember_token'])) {
            $user = $this->checkRememberToken($_COOKIE['remember_token']);
            if ($user) {
                $this->createSession($user);
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Verificar token de "recordarme" y devolver usuario si es válido
     */
    private function checkRememberToken($token) {
        try {
            $hashedToken = hash('sha256', $token);
            
            $stmt = $this->db->prepare("
                SELECT u.* 
                FROM Users u 
                INNER JOIN remember_tokens rt ON u.user_id = rt.user_id 
                WHERE rt.token = ? 
                AND rt.expires_at > NOW() 
                AND u.status = 'active'
            ");
            $stmt->execute([$hashedToken]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Renovar token por 30 días más
                $this->renewRememberToken($user['user_id'], $hashedToken);
                return $user;
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error al verificar token de recordar: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Renovar token de "recordarme"
     */
    private function renewRememberToken($userId, $hashedToken) {
        try {
            $stmt = $this->db->prepare("
                UPDATE remember_tokens 
                SET expires_at = DATE_ADD(NOW(), INTERVAL 30 DAY) 
                WHERE user_id = ? AND token = ?
            ");
            $stmt->execute([$userId, $hashedToken]);
            
            // Renovar cookie también
            setcookie('remember_token', $_COOKIE['remember_token'], time() + (30 * 24 * 60 * 60), '/', '', false, true);
        } catch (PDOException $e) {
            error_log("Error al renovar token de recordar: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener usuario actual
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return $this->userModel->findById($_SESSION['user_id']);
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        $user = $this->getCurrentUser();
        
        // Eliminar token de "recordarme" si existe
        if (isset($_COOKIE['remember_token']) && $user) {
            $this->removeRememberToken($user['user_id']);
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
        
        // Destruir todas las variables de sesión
        $_SESSION = array();
        
        // Eliminar cookie de sesión si existe
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destruir la sesión
        session_destroy();
    }
    
    /**
     * Eliminar token de recordar de la base de datos
     */
    private function removeRememberToken($userId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
            $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Error al eliminar token de recordar: " . $e->getMessage());
        }
    }
    
    /**
     * Registrar intento de login
     */
    public function logLoginAttempt($username, $success, $ipAddress) {
        try {
            $stmt = $this->db->prepare("INSERT INTO login_attempts (username, success, ip_address, attempt_time) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$username, $success ? 1 : 0, $ipAddress]);
        } catch (PDOException $e) {
            error_log("Error al registrar intento de login: " . $e->getMessage());
        }
    }
    
    /**
     * Verificar límite de intentos de login
     */
    public function checkLoginAttempts($username, $ipAddress) {
        try {
            // Verificar intentos por IP en los últimos 15 minutos
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as attempts 
                FROM login_attempts 
                WHERE ip_address = ? 
                AND success = 0 
                AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
            ");
            $stmt->execute([$ipAddress]);
            $ipAttempts = $stmt->fetch()['attempts'];
            
            if ($ipAttempts >= 5) {
                return false; // Bloqueado por IP
            }
            
            // Verificar intentos por usuario en los últimos 15 minutos
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as attempts 
                FROM login_attempts 
                WHERE username = ? 
                AND success = 0 
                AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
            ");
            $stmt->execute([$username]);
            $userAttempts = $stmt->fetch()['attempts'];
            
            return $userAttempts < 3; // Máximo 3 intentos por usuario
            
        } catch (PDOException $e) {
            error_log("Error al verificar intentos de login: " . $e->getMessage());
            return true; // En caso de error, permitir el intento
        }
    }

    /**
     * Limpiar tokens de "recordarme" expirados
     */
    public function cleanExpiredTokens() {
        try {
            $stmt = $this->db->prepare("DELETE FROM remember_tokens WHERE expires_at < NOW()");
            $stmt->execute();
            
            $deletedCount = $stmt->rowCount();
            if ($deletedCount > 0) {
                error_log("Limpiados $deletedCount tokens de recordar expirados");
            }
        } catch (PDOException $e) {
            error_log("Error al limpiar tokens expirados: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener información del token de "recordarme" del usuario actual
     */
    public function getRememberTokenInfo() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT token, expires_at 
                FROM remember_tokens 
                WHERE user_id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener información del token: " . $e->getMessage());
            return null;
        }
    }
}