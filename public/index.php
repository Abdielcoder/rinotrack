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
    __DIR__ . '/../app/models/Task.php'
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
    __DIR__ . '/../app/controllers/KPIController.php'
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