<?php

class DashboardController {
    private $auth;
    
    public function __construct() {
        $this->auth = new Auth();
        $this->roleModel = new Role();
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
            'isAdmin' => $this->roleModel->userHasMinimumRole($user['user_id'], Role::ADMIN),
            'currentPage' => 'dashboard'
        ];
        
        // Cargar vista del dashboard
        $this->loadView('dashboard', $data);
    }
    
    /**
     * Obtener estadísticas del usuario
     */
    private function getStats() {
        $projectModel = new Project();
        $taskModel = new Task();
        $user = $this->auth->getCurrentUser();

        $projectStats = $projectModel->getStats();
        $taskStats = $taskModel->getStats(null, $user['user_id']);

        return [
            'projects' => $projectStats['total_projects'],
            'tasks' => $taskStats['total_tasks'],
            'completed' => $projectStats['total_projects'] > 0 ? round($projectStats['completed_projects'] / $projectStats['total_projects'] * 100) . '%' : '0%',
            'in_progress' => $taskStats['pending_tasks']
        ];
    }
    
    /**
     * Obtener actividad reciente
     */
    private function getRecentActivity() {
        $projectModel = new Project();
        $taskModel = new Task();
        $user = $this->auth->getCurrentUser();

        $recentProjects = $projectModel->getByUser($user['user_id']);
        $recentTasks = $taskModel->getByUser($user['user_id'], true);

        $activity = [];

        foreach ($recentProjects as $project) {
            $activity[] = [
                'icon' => 'fas fa-plus',
                'title' => 'Nuevo proyecto creado: ' . $project['project_name'],
                'time' => date('d/m/Y', strtotime($project['created_at']))
            ];
        }

        foreach ($recentTasks as $task) {
            if ($task['is_completed']) {
                $activity[] = [
                    'icon' => 'fas fa-check',
                    'title' => 'Tarea completada: ' . $task['task_name'],
                    'time' => date('d/m/Y', strtotime($task['completed_at']))
                ];
            }
        }

        // Sort activity by time
        usort($activity, function ($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activity, 0, 3);
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