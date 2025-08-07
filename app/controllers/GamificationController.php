<?php

class GamificationController {
    private $auth;
    private $gamificationModel;
    private $userModel;
    private $clanModel;
    private $roleModel;
    
    public function __construct() {
        $this->auth = new Auth();
        $this->gamificationModel = new Gamification();
        $this->userModel = new User();
        $this->clanModel = new Clan();
        $this->roleModel = new Role();
    }
    
    /**
     * Dashboard principal de gamificación
     */
    public function index() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::redirect('dashboard');
        }
        
        $data = [
            'stats' => $this->gamificationModel->getGamificationStats(),
            'leaderboard' => $this->gamificationModel->getLeaderboard(10),
            'activeEvents' => $this->gamificationModel->getActiveEvents(),
            'currentPage' => 'gamification',
            'user' => $this->auth->getCurrentUser(),
            'additionalCSS' => [
                APP_URL . 'assets/css/gamification.css'
            ],
            'additionalJS' => [
                APP_URL . 'assets/js/gamification.js'
            ]
        ];
        
        $this->loadView('admin/gamification/dashboard', $data);
    }
    
    /**
     * Gestión de badges
     */
    public function badges() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::redirect('dashboard');
        }
        
        $data = [
            'badges' => $this->gamificationModel->getAllBadges(false),
            'categories' => $this->getBadgeCategories(),
            'currentPage' => 'gamification',
            'user' => $this->auth->getCurrentUser(),
            'additionalCSS' => [
                APP_URL . 'assets/css/gamification.css'
            ],
            'additionalJS' => [
                APP_URL . 'assets/js/gamification.js'
            ]
        ];
        
        $this->loadView('admin/gamification/badges', $data);
    }
    
    /**
     * Crear nuevo badge
     */
    public function createBadge() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('gamification/badges');
        }
        
        $badgeName = Utils::sanitizeInput($_POST['badge_name'] ?? '');
        $badgeDescription = Utils::sanitizeInput($_POST['badge_description'] ?? '');
        $badgeIcon = Utils::sanitizeInput($_POST['badge_icon'] ?? '🏆');
        $badgeColor = Utils::sanitizeInput($_POST['badge_color'] ?? '#3B82F6');
        $badgeCategory = Utils::sanitizeInput($_POST['badge_category'] ?? 'task_completion');
        $pointsReward = (int)($_POST['points_reward'] ?? 0);
        $requiredCriteria = $_POST['required_criteria'] ?? '{}';
        
        $errors = [];
        
        if (empty($badgeName)) {
            $errors['badge_name'] = 'El nombre del badge es requerido';
        }
        
        if (empty($badgeDescription)) {
            $errors['badge_description'] = 'La descripción del badge es requerida';
        }
        
        if (!empty($errors)) {
            Utils::jsonResponse(['success' => false, 'errors' => $errors]);
        }
        
        try {
            $stmt = $this->gamificationModel->getDb()->prepare("
                INSERT INTO Gamification_Badges (badge_name, badge_description, badge_icon, badge_color, badge_category, points_reward, required_criteria)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([$badgeName, $badgeDescription, $badgeIcon, $badgeColor, $badgeCategory, $pointsReward, $requiredCriteria]);
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Badge creado exitosamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al crear el badge']);
            }
        } catch (PDOException $e) {
            error_log("Error al crear badge: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }
    
    /**
     * Actualizar badge
     */
    public function updateBadge() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('gamification/badges');
        }
        
        $badgeId = (int)($_POST['badge_id'] ?? 0);
        $badgeName = Utils::sanitizeInput($_POST['badge_name'] ?? '');
        $badgeDescription = Utils::sanitizeInput($_POST['badge_description'] ?? '');
        $badgeIcon = Utils::sanitizeInput($_POST['badge_icon'] ?? '🏆');
        $badgeColor = Utils::sanitizeInput($_POST['badge_color'] ?? '#3B82F6');
        $badgeCategory = Utils::sanitizeInput($_POST['badge_category'] ?? 'task_completion');
        $pointsReward = (int)($_POST['points_reward'] ?? 0);
        $requiredCriteria = $_POST['required_criteria'] ?? '{}';
        $isActive = (int)($_POST['is_active'] ?? 1);
        
        $errors = [];
        
        if ($badgeId <= 0) {
            $errors['badge_id'] = 'ID de badge inválido';
        }
        
        if (empty($badgeName)) {
            $errors['badge_name'] = 'El nombre del badge es requerido';
        }
        
        if (empty($badgeDescription)) {
            $errors['badge_description'] = 'La descripción del badge es requerida';
        }
        
        if (!empty($errors)) {
            Utils::jsonResponse(['success' => false, 'errors' => $errors]);
        }
        
        try {
            $stmt = $this->gamificationModel->getDb()->prepare("
                UPDATE Gamification_Badges 
                SET badge_name = ?, badge_description = ?, badge_icon = ?, badge_color = ?, 
                    badge_category = ?, points_reward = ?, required_criteria = ?, is_active = ?
                WHERE badge_id = ?
            ");
            
            $result = $stmt->execute([$badgeName, $badgeDescription, $badgeIcon, $badgeColor, $badgeCategory, $pointsReward, $requiredCriteria, $isActive, $badgeId]);
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Badge actualizado exitosamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al actualizar el badge']);
            }
        } catch (PDOException $e) {
            error_log("Error al actualizar badge: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }
    
    /**
     * Eliminar badge
     */
    public function deleteBadge() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('gamification/badges');
        }
        
        $badgeId = (int)($_POST['badge_id'] ?? 0);
        
        if ($badgeId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'ID de badge inválido']);
        }
        
        try {
            // Verificar si hay usuarios con este badge
            $stmt = $this->gamificationModel->getDb()->prepare("
                SELECT COUNT(*) as count FROM Gamification_User_Badges WHERE badge_id = ?
            ");
            $stmt->execute([$badgeId]);
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                Utils::jsonResponse(['success' => false, 'message' => 'No se puede eliminar el badge porque ya ha sido otorgado a usuarios']);
            }
            
            $stmt = $this->gamificationModel->getDb()->prepare("DELETE FROM Gamification_Badges WHERE badge_id = ?");
            $result = $stmt->execute([$badgeId]);
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Badge eliminado exitosamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al eliminar el badge']);
            }
        } catch (PDOException $e) {
            error_log("Error al eliminar badge: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }
    
    /**
     * Asignar badge manualmente
     */
    public function awardBadge() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('gamification/badges');
        }
        
        $userId = (int)($_POST['user_id'] ?? 0);
        $badgeId = (int)($_POST['badge_id'] ?? 0);
        
        if ($userId <= 0 || $badgeId <= 0) {
            Utils::jsonResponse(['success' => false, 'message' => 'Usuario y badge son requeridos']);
        }
        
        $result = $this->gamificationModel->awardBadge($userId, $badgeId);
        
        if ($result) {
            // Actualizar leaderboard
            $this->gamificationModel->updateUserLeaderboard($userId);
            Utils::jsonResponse(['success' => true, 'message' => 'Badge otorgado exitosamente']);
        } else {
            Utils::jsonResponse(['success' => false, 'message' => 'Error al otorgar el badge o el usuario ya lo tiene']);
        }
    }
    
    /**
     * Gestión de usuarios y sus badges
     */
    public function users() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::redirect('dashboard');
        }
        
        $search = $_GET['search'] ?? '';
        $users = empty($search) ? 
            $this->userModel->getAllWithRoles() : 
            $this->userModel->search($search);
        
        // Obtener información de gamificación para cada usuario
        foreach ($users as &$user) {
            $user['total_points'] = $this->gamificationModel->getUserTotalPoints($user['user_id']);
            $user['badges'] = $this->gamificationModel->getUserBadges($user['user_id']);
            $user['badges_count'] = count($user['badges']);
        }
        
        $data = [
            'users' => $users,
            'badges' => $this->gamificationModel->getAllBadges(),
            'search' => $search,
            'currentPage' => 'gamification',
            'user' => $this->auth->getCurrentUser()
        ];
        
        $this->loadView('admin/gamification/users', $data);
    }
    
    /**
     * Ver perfil de gamificación de un usuario
     */
    public function userProfile($userId) {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::redirect('dashboard');
        }
        
        $user = $this->userModel->findById($userId);
        if (!$user) {
            Utils::redirect('gamification/users');
        }
        
        $data = [
            'user' => $user,
            'userProfile' => $user,
            'badges' => $this->gamificationModel->getUserBadges($userId),
            'totalPoints' => $this->gamificationModel->getUserTotalPoints($userId),
            'pointsHistory' => $this->gamificationModel->getUserPointsHistory($userId, 20),
            'availableBadges' => $this->gamificationModel->getAllBadges(),
            'currentPage' => 'gamification',
            'currentUser' => $this->auth->getCurrentUser()
        ];
        
        $this->loadView('admin/gamification/user_profile', $data);
    }
    
    /**
     * Gestión de logros de clanes
     */
    public function clanAchievements() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::redirect('dashboard');
        }
        
        $clans = $this->clanModel->getAll();
        
        // Obtener logros para cada clan
        foreach ($clans as &$clan) {
            $clan['achievements'] = $this->gamificationModel->getClanAchievements($clan['clan_id']);
            $clan['progress'] = $this->gamificationModel->getClanAchievementProgress($clan['clan_id']);
        }
        
        $data = [
            'clans' => $clans,
            'currentPage' => 'gamification',
            'user' => $this->auth->getCurrentUser()
        ];
        
        $this->loadView('admin/gamification/clan_achievements', $data);
    }
    
    /**
     * Crear logro de clan
     */
    public function createClanAchievement() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('gamification/clan-achievements');
        }
        
        $clanId = (int)($_POST['clan_id'] ?? 0);
        $achievementName = Utils::sanitizeInput($_POST['achievement_name'] ?? '');
        $achievementDescription = Utils::sanitizeInput($_POST['achievement_description'] ?? '');
        $achievementIcon = Utils::sanitizeInput($_POST['achievement_icon'] ?? '🏆');
        $achievementColor = Utils::sanitizeInput($_POST['achievement_color'] ?? '#3B82F6');
        $achievementCategory = Utils::sanitizeInput($_POST['achievement_category'] ?? 'project_completion');
        $pointsReward = (int)($_POST['points_reward'] ?? 0);
        $requiredCriteria = $_POST['required_criteria'] ?? '{}';
        
        $errors = [];
        
        if ($clanId <= 0) {
            $errors['clan_id'] = 'Clan es requerido';
        }
        
        if (empty($achievementName)) {
            $errors['achievement_name'] = 'El nombre del logro es requerido';
        }
        
        if (empty($achievementDescription)) {
            $errors['achievement_description'] = 'La descripción del logro es requerida';
        }
        
        if (!empty($errors)) {
            Utils::jsonResponse(['success' => false, 'errors' => $errors]);
        }
        
        try {
            $stmt = $this->gamificationModel->getDb()->prepare("
                INSERT INTO Gamification_Clan_Achievements (clan_id, achievement_name, achievement_description, achievement_icon, achievement_color, achievement_category, points_reward, required_criteria)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([$clanId, $achievementName, $achievementDescription, $achievementIcon, $achievementColor, $achievementCategory, $pointsReward, $requiredCriteria]);
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Logro de clan creado exitosamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al crear el logro']);
            }
        } catch (PDOException $e) {
            error_log("Error al crear logro de clan: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }
    
    /**
     * Gestión de eventos
     */
    public function events() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::redirect('dashboard');
        }
        
        try {
            $stmt = $this->gamificationModel->getDb()->prepare("
                SELECT * FROM Gamification_Events 
                ORDER BY start_date DESC
            ");
            $stmt->execute();
            $events = $stmt->fetchAll();
        } catch (PDOException $e) {
            $events = [];
        }
        
        $data = [
            'events' => $events,
            'currentPage' => 'gamification',
            'user' => $this->auth->getCurrentUser()
        ];
        
        $this->loadView('admin/gamification/events', $data);
    }
    
    /**
     * Crear evento
     */
    public function createEvent() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::jsonResponse(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Utils::redirect('gamification/events');
        }
        
        $eventName = Utils::sanitizeInput($_POST['event_name'] ?? '');
        $eventDescription = Utils::sanitizeInput($_POST['event_description'] ?? '');
        $eventType = Utils::sanitizeInput($_POST['event_type'] ?? 'special');
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        $bonusMultiplier = (float)($_POST['bonus_multiplier'] ?? 1.0);
        
        $errors = [];
        
        if (empty($eventName)) {
            $errors['event_name'] = 'El nombre del evento es requerido';
        }
        
        if (empty($eventDescription)) {
            $errors['event_description'] = 'La descripción del evento es requerida';
        }
        
        if (empty($startDate) || empty($endDate)) {
            $errors['dates'] = 'Las fechas de inicio y fin son requeridas';
        }
        
        if (!empty($errors)) {
            Utils::jsonResponse(['success' => false, 'errors' => $errors]);
        }
        
        try {
            $stmt = $this->gamificationModel->getDb()->prepare("
                INSERT INTO Gamification_Events (event_name, event_description, event_type, start_date, end_date, bonus_multiplier)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([$eventName, $eventDescription, $eventType, $startDate, $endDate, $bonusMultiplier]);
            
            if ($result) {
                Utils::jsonResponse(['success' => true, 'message' => 'Evento creado exitosamente']);
            } else {
                Utils::jsonResponse(['success' => false, 'message' => 'Error al crear el evento']);
            }
        } catch (PDOException $e) {
            error_log("Error al crear evento: " . $e->getMessage());
            Utils::jsonResponse(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }
    
    /**
     * Leaderboard
     */
    public function leaderboard() {
        $this->requireAuth();
        if (!$this->hasAdminAccess()) {
            Utils::redirect('dashboard');
        }
        
        $data = [
            'leaderboard' => $this->gamificationModel->getLeaderboard(50),
            'currentPage' => 'gamification',
            'user' => $this->auth->getCurrentUser()
        ];
        
        $this->loadView('admin/gamification/leaderboard', $data);
    }
    
    /**
     * Obtener categorías de badges
     */
    private function getBadgeCategories() {
        return [
            'task_completion' => 'Completación de Tareas',
            'project_milestone' => 'Hitos de Proyecto',
            'clan_achievement' => 'Logros de Clan',
            'time_based' => 'Basado en Tiempo',
            'special_event' => 'Eventos Especiales',
            'leadership' => 'Liderazgo',
            'collaboration' => 'Colaboración',
            'innovation' => 'Innovación',
            'quality' => 'Calidad',
            'speed' => 'Velocidad'
        ];
    }
    
    /**
     * Verificar acceso de administrador
     */
    private function hasAdminAccess() {
        $currentUser = $this->auth->getCurrentUser();
        if (!$currentUser) {
            return false;
        }
        
        return $this->roleModel->userHasMinimumRole($currentUser['user_id'], Role::ADMIN);
    }
    
    /**
     * Requerir autenticación
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