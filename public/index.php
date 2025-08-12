<?php
/**
 * Router principal de RinoTrack - Versión simplificada
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar que los directorios principales existen
$requiredDirs = [
    __DIR__ . '/../config',
    __DIR__ . '/../app/models',
    __DIR__ . '/../app/controllers',
    __DIR__ . '/../app/views'
];

foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        die("Error: Directorio requerido no encontrado: $dir");
    }
}

// Incluir configuraciones con manejo de errores
try {
    require_once __DIR__ . '/../config/app.php';
    require_once __DIR__ . '/../config/database.php';
} catch (Exception $e) {
    die("Error cargando configuración: " . $e->getMessage());
}

// Incluir modelos manualmente (más confiable que autoloader)
$models = [
    __DIR__ . '/../app/models/Utils.php',
    __DIR__ . '/../app/models/User.php',
    __DIR__ . '/../app/models/Auth.php',
    __DIR__ . '/../app/models/Project.php',
    __DIR__ . '/../app/models/Clan.php',
    __DIR__ . '/../app/models/Role.php',
    __DIR__ . '/../app/models/KPI.php',
    __DIR__ . '/../app/models/Task.php',
    __DIR__ . '/../app/models/Gamification.php'
];

foreach ($models as $model) {
    if (file_exists($model)) {
        require_once $model;
    } else {
        die("Error: Modelo requerido no encontrado: $model");
    }
}

// Incluir controladores
$controllers = [
    __DIR__ . '/../app/controllers/AuthController.php',
    __DIR__ . '/../app/controllers/DashboardController.php',
    __DIR__ . '/../app/controllers/AdminController.php',
    __DIR__ . '/../app/controllers/KPIController.php',
    __DIR__ . '/../app/controllers/ClanLeaderController.php',
    __DIR__ . '/../app/controllers/GamificationController.php'
];

foreach ($controllers as $controller) {
    if (file_exists($controller)) {
        require_once $controller;
    } else {
        die("Error: Controlador requerido no encontrado: $controller");
    }
}

// Obtener ruta
$route = $_GET['route'] ?? '';

// Normalizar APP_URL para despliegue en /desarrollo/
if (defined('APP_URL')) {
    // Si la URL actual contiene /desarrollo/rinotrack/public/ y APP_URL no, corregir
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($requestUri, '/desarrollo/rinotrack/public/') !== false && strpos(APP_URL, '/desarrollo/rinotrack/public/') === false) {
        // No se redefine la constante, pero sirve como recordatorio en logs
        error_log('SUGERENCIA: Ajusta APP_URL a https://rinotrack.rinorisk.com/desarrollo/rinotrack/public/');
    }
}

// Router simple
try {
    switch ($route) {
        case '':
        case 'login':
            $controller = new AuthController();
            $controller->showLogin();
            break;
            
        case 'process-login':
            $controller = new AuthController();
            $controller->processLogin();
            break;
            
        case 'logout':
            $controller = new AuthController();
            $controller->logout();
            break;
            
        case 'dashboard':
            $controller = new DashboardController();
            $controller->index();
            break;
            
        case 'admin':
            $controller = new AdminController();
            $controller->index();
            break;
            
        case 'admin/users':
            $controller = new AdminController();
            $controller->users();
            break;
            
        case 'admin/create-user':
            $controller = new AdminController();
            $controller->createUser();
            break;
            
        case 'admin/update-user':
            $controller = new AdminController();
            $controller->updateUser();
            break;
            
        case 'admin/toggle-user-status':
            $controller = new AdminController();
            $controller->toggleUserStatus();
            break;
            
        case 'admin/projects':
            $controller = new AdminController();
            $controller->projects();
            break;
            
        case 'admin/create-project':
            $controller = new AdminController();
            $controller->createProject();
            break;
            
        case 'admin/clans':
            $controller = new AdminController();
            $controller->clans();
            break;
            
        case 'admin/create-clan':
            $controller = new AdminController();
            $controller->createClan();
            break;
            
        case 'admin/update-clan':
            $controller = new AdminController();
            $controller->updateClan();
            break;
            
        case 'admin/delete-clan':
            $controller = new AdminController();
            $controller->deleteClan();
            break;
            
        case 'admin/clan-members':
            $controller = new AdminController();
            $controller->getClanMembers();
            break;
            
        case 'admin/clan-details':
            $controller = new AdminController();
            $controller->getClanDetails();
            break;
            
        case 'admin/add-clan-member':
            $controller = new AdminController();
            $controller->addClanMember();
            break;
            
        case 'admin/remove-clan-member':
            $controller = new AdminController();
            $controller->removeClanMember();
            break;
        
    // Rutas KPI
    case 'kpi':
    case 'kpi/dashboard':
        $controller = new KPIController();
        $controller->dashboard();
        break;
        
    case 'kpi/quarters':
        $controller = new KPIController();
        $controller->quarters();
        break;
        
    case 'kpi/create-quarter':
        $controller = new KPIController();
        $controller->createQuarter();
        break;
        
    case 'kpi/activate-quarter':
        $controller = new KPIController();
        $controller->activateQuarter();
        break;
        
    case 'kpi/update-quarter':
        $controller = new KPIController();
        $controller->updateQuarter();
        break;
        
    case 'kpi/delete-quarter':
        $controller = new KPIController();
        $controller->deleteQuarter();
        break;
        
    case 'kpi/quarter-details':
        $controller = new KPIController();
        $controller->getQuarterDetails();
        break;
        
    case 'kpi/projects':
        $controller = new KPIController();
        $controller->projects();
        break;
        
    case 'kpi/assign':
        $controller = new KPIController();
        $controller->assignKPI();
        break;
        
    case 'kpi/get-available-points':
        $controller = new KPIController();
        $controller->getAvailablePoints();
        break;
        
    case 'kpi/get-tasks':
        $controller = new KPIController();
        $controller->getProjectTasks();
        break;

    case 'kpi/add-task':
        $controller = new KPIController();
        $controller->addTask();
        break;

    case 'kpi/toggle-task-status':
        $controller = new KPIController();
        $controller->toggleTaskStatus();
        break;
        
    case 'kpi/debug':
        $controller = new KPIController();
        $controller->debug();
        break;
        
    case 'kpi/change-distribution':
        $controller = new KPIController();
        $controller->changeDistributionMode();
        break;
        
    case 'kpi/get-project-progress':
        $controller = new KPIController();
        $controller->getProjectProgress();
        break;
        
    case 'kpi/get-projects-data':
        $controller = new KPIController();
        $controller->getProjectsData();
        break;
        
    // Rutas del Líder de Clan
    case 'clan_leader':
    case 'clan_leader/dashboard':
        $controller = new ClanLeaderController();
        $controller->index();
        break;
        
    case 'clan_leader/members':
        $controller = new ClanLeaderController();
        $controller->members();
        break;
        
    case 'clan_leader/add-member':
        $controller = new ClanLeaderController();
        $controller->addMember();
        break;
        
    case 'clan_leader/remove-member':
        $controller = new ClanLeaderController();
        $controller->removeMember();
        break;
        
    case 'clan_leader/get-available-users':
        $controller = new ClanLeaderController();
        $controller->getAvailableUsers();
        break;
        
    case 'clan_leader/get-clan-members':
        $controller = new ClanLeaderController();
        $controller->getClanMembers();
        break;
        
    case 'clan_leader/projects':
        $controller = new ClanLeaderController();
        $controller->projects();
        break;
        
    case 'clan_leader/create-project':
        $controller = new ClanLeaderController();
        $controller->createProject();
        break;
        
    case 'clan_leader/update-project':
        $controller = new ClanLeaderController();
        $controller->updateProject();
        break;
        
    case 'clan_leader/delete-project':
        $controller = new ClanLeaderController();
        $controller->deleteProject();
        break;
        
    case 'clan_leader/kpi-dashboard':
        $controller = new ClanLeaderController();
        $controller->kpiDashboard();
        break;
        
    case 'clan_leader/assign-kpi':
        $controller = new ClanLeaderController();
        $controller->assignKPI();
        break;
        
    case 'clan_leader/get-available-points':
        $controller = new ClanLeaderController();
        $controller->getAvailablePoints();
        break;
        
    case 'clan_leader/tasks':
        $controller = new ClanLeaderController();
        $controller->tasks();
        break;
        
    case 'clan_leader/add-task':
        $controller = new ClanLeaderController();
        $controller->addTask();
        break;
        
    case 'clan_leader/create-task':
        $controller = new ClanLeaderController();
        $controller->createTask();
        break;
        
    case 'clan_leader/update-task':
        $controller = new ClanLeaderController();
        $controller->updateTask();
        break;
        
    case 'clan_leader/toggle-task-status':
        $controller = new ClanLeaderController();
        $controller->toggleTaskStatus();
        break;
        
    case 'clan_leader/get-task-details':
        $controller = new ClanLeaderController();
        $controller->getTaskDetails();
        break;
        
    case 'clan_leader/add-task-comment':
        $controller = new ClanLeaderController();
        $controller->addTaskComment();
        break;
        
    case 'clan_leader/update-subtask-status':
        $controller = new ClanLeaderController();
        $controller->updateSubtaskStatus();
        break;
        
    case 'clan_leader/get-available-labels':
        $controller = new ClanLeaderController();
        $controller->getAvailableLabels();
        break;
        
    case 'clan_leader/test':
        $controller = new ClanLeaderController();
        $controller->test();
        break;
        
    case 'clan_leader/delete-task':
        $controller = new ClanLeaderController();
        $controller->deleteTask();
        break;
        
    case 'clan_leader/delete-subtask':
        $controller = new ClanLeaderController();
        $controller->deleteSubtask();
        break;
        
    case 'clan_leader/add-comment':
        $controller = new ClanLeaderController();
        $controller->addComment();
        break;
        
    case 'clan_leader/add-collaborators':
        $controller = new ClanLeaderController();
        $controller->addCollaborators();
        break;
        
    case 'clan_leader/remove-collaborator':
        $controller = new ClanLeaderController();
        $controller->removeCollaborator();
        break;
        
    case 'clan_leader/update-user-percentage':
        $controller = new ClanLeaderController();
        $controller->updateUserPercentage();
        break;
        
    case 'clan_leader/get-user-stats':
        $controller = new ClanLeaderController();
        $controller->getUserDetailedStats();
        break;
        
            case 'clan_leader/collaborator-availability':
            $controller = new ClanLeaderController();
            $controller->collaboratorAvailability();
            break;
        
        // Rutas de Gamificación
        case 'gamification':
            $controller = new GamificationController();
            $controller->index();
            break;
            
        case 'gamification/badges':
            $controller = new GamificationController();
            $controller->badges();
            break;
            
        case 'gamification/createBadge':
            $controller = new GamificationController();
            $controller->createBadge();
            break;
            
        case 'gamification/updateBadge':
            $controller = new GamificationController();
            $controller->updateBadge();
            break;
            
        case 'gamification/deleteBadge':
            $controller = new GamificationController();
            $controller->deleteBadge();
            break;
            
        case 'gamification/awardBadge':
            $controller = new GamificationController();
            $controller->awardBadge();
            break;
            
        case 'gamification/users':
            $controller = new GamificationController();
            $controller->users();
            break;
            
        case 'gamification/user-profile':
            $controller = new GamificationController();
            $controller->userProfile($_GET['user_id'] ?? 0);
            break;
            
        case 'gamification/clan-achievements':
            $controller = new GamificationController();
            $controller->clanAchievements();
            break;
            
        case 'gamification/createClanAchievement':
            $controller = new GamificationController();
            $controller->createClanAchievement();
            break;
            
        case 'gamification/events':
            $controller = new GamificationController();
            $controller->events();
            break;
            
        case 'gamification/createEvent':
            $controller = new GamificationController();
            $controller->createEvent();
            break;
            
        case 'gamification/leaderboard':
            $controller = new GamificationController();
            $controller->leaderboard();
            break;
            
        case 'badges':
            // Por ahora redirigir al dashboard
            Utils::redirect('dashboard');
            break;
            
        case 'perfil':
            // Por ahora redirigir al dashboard
            Utils::redirect('dashboard');
            break;
            
        default:
            // Verificar si es un asset
            if (strpos($route, 'assets/') === 0) {
                $filePath = __DIR__ . '/' . $route;
                if (file_exists($filePath)) {
                    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                    
                    if ($extension === 'css') {
                        header('Content-Type: text/css');
                    } elseif ($extension === 'js') {
                        header('Content-Type: application/javascript');
                    }
                    
                    readfile($filePath);
                    exit;
                }
            }
            
            http_response_code(404);
            echo "<h1>404 - Página no encontrada</h1>";
            echo "<p>Ruta solicitada: " . htmlspecialchars($route) . "</p>";
            echo "<p><a href='?route=login'>Ir al login</a></p>";
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "<h1>Error interno</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><a href='?route=login'>Intentar de nuevo</a></p>";
}
?>