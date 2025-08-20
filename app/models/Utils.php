<?php

class Utils {
    
    /**
     * Limpiar datos de entrada
     */
    public static function sanitizeInput($data) {
        // Tolerar nulls y arreglos; evitar deprecations en PHP 8.1+
        if ($data === null) {
            return '';
        }
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        $data = (string)$data;
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    
    /**
     * Validar email
     */
    public static function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Redirigir a una URL
     */
    public static function redirect($url) {
        // Si la URL no contiene protocolo, construir URL con parámetro route
        if (!preg_match('/^https?:\/\//', $url)) {
            // Si es una ruta interna, usar parámetro route
            if (strpos($url, '?') === false) {
                $url = APP_URL . '?route=' . ltrim($url, '/');
            } else {
                $url = APP_URL . ltrim($url, '/');
            }
        }
        
        header("Location: " . $url);
        exit();
    }
    
    /**
     * Enviar respuesta JSON
     */
    public static function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    
    /**
     * Generar token CSRF
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verificar token CSRF
     */
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Escapar contenido HTML
     */
    public static function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Generar URL absoluta
     */
    public static function url($path = '') {
        return APP_URL . ltrim($path, '/');
    }
    
    /**
     * Generar URL de asset
     */
    public static function asset($path) {
        return APP_URL . ltrim($path, '/');
    }
    
    /**
     * Obtener IP del cliente
     */
    public static function getClientIP() {
        // Verificar IP de diferentes headers (para proxies)
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) && !empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                
                // Si hay múltiples IPs (separadas por coma), tomar la primera
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                
                // Validar que es una IP válida
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Generar menú de navegación
     */
    public static function renderNavMenu($currentPage = 'dashboard') {
        $menuItems = [
            'dashboard' => 'Dashboard',
            'admin' => 'Admin Panel',
            'kpi' => 'KPI System',
            'badges' => 'Badges',
            'perfil' => 'Perfil'
        ];
        
        $html = '<nav class="main-nav"><ul class="nav-menu">';
        
        foreach ($menuItems as $route => $label) {
            $activeClass = $currentPage === $route ? 'active' : '';
            $html .= '<li class="nav-item ' . $activeClass . '">';
            $html .= '<a href="?route=' . $route . '" class="nav-link">' . $label . '</a>';
            $html .= '</li>';
        }
        
        $html .= '</ul></nav>';
        
        return $html;
    }
    
    /**
     * Formatear fecha de forma segura evitando errores deprecated con null
     */
    public static function formatDate($date, $format = 'd/m/Y', $emptyText = 'Sin fecha') {
        if (empty($date)) {
            return $emptyText;
        }
        
        // Verificar si la fecha es válida
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return $emptyText;
        }
        
        return date($format, $timestamp);
    }
    
    /**
     * Formatear fecha y hora de forma segura
     */
    public static function formatDateTime($datetime, $format = 'd/m/Y H:i', $emptyText = 'Sin fecha') {
        return self::formatDate($datetime, $format, $emptyText);
    }
}