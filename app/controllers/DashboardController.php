<?php

class DashboardController {
    private $auth;
    
    public function __construct() {
        $this->auth = new Auth();
    }
    
    /**
     * Mostrar dashboard principal
     */
    public function index() {
        // Verificar autenticación
        $this->requireAuth();
        
        // Obtener información del usuario actual
        $user = $this->auth->getCurrentUser();
        
        if (!$user) {
            // Si no se puede obtener la información del usuario, cerrar sesión
            $this->auth->logout();
            Utils::redirect('login');
        }
        
        // Procesar logout si se solicita
        if (isset($_GET['logout'])) {
            $this->auth->logout();
            Utils::redirect('login');
        }
        
        // Preparar datos para la vista
        $data = [
            'user' => $user,
            'stats' => $this->getStats(),
            'recentActivity' => $this->getRecentActivity(),
            'sessionInfo' => $this->getSessionInfo(),
            'currentPage' => 'dashboard'
        ];
        
        // Cargar vista del dashboard
        $this->loadView('dashboard', $data);
    }
    
    /**
     * Obtener estadísticas del usuario
     */
    private function getStats() {
        // Por ahora datos estáticos, en una aplicación real vendrían de la base de datos
        return [
            'projects' => 24,
            'tasks' => 158,
            'completed' => '89%',
            'in_progress' => 12
        ];
    }
    
    /**
     * Obtener actividad reciente
     */
    private function getRecentActivity() {
        // Por ahora datos estáticos, en una aplicación real vendrían de la base de datos
        return [
            [
                'icon' => 'fas fa-plus',
                'title' => 'Nuevo proyecto creado',
                'time' => 'Hace 2 horas'
            ],
            [
                'icon' => 'fas fa-check',
                'title' => 'Tarea completada',
                'time' => 'Hace 4 horas'
            ],
            [
                'icon' => 'fas fa-edit',
                'title' => 'Perfil actualizado',
                'time' => 'Ayer'
            ]
        ];
    }
    
    /**
     * Obtener información de sesión
     */
    private function getSessionInfo() {
        return [
            'ip_address' => Utils::getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'No disponible',
            'login_time' => isset($_SESSION['login_time']) ? date('d/m/Y H:i:s', $_SESSION['login_time']) : 'No disponible'
        ];
    }
    
    /**
     * Verificar autenticación
     */
    private function requireAuth() {
        if (!$this->auth->isLoggedIn()) {
            Utils::redirect('login');
        }
    }
    
    /**
     * Cargar vista
     */
    private function loadView($view, $data = []) {
        // Extraer variables para la vista
        extract($data);
        
        // Incluir archivo de vista
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            die('Vista no encontrada: ' . $view);
        }
    }
}