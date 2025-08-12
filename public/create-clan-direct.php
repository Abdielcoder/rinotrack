<?php
/**
 * Endpoint directo para crear clanes - Evita problemas de routing
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir configuraciones
require_once '../config/app.php';
require_once '../config/database.php';

// Incluir modelos
require_once '../app/models/Auth.php';
require_once '../app/models/User.php';
require_once '../app/models/Clan.php';
require_once '../app/models/Role.php';
require_once '../app/models/Utils.php';

// Iniciar sesión
session_start();

// Verificar método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

try {
    // Crear instancias
    $auth = new Auth();
    $clanModel = new Clan();
    $roleModel = new Role();
    
    // Verificar si el usuario tiene acceso de admin
    $currentUser = $auth->getCurrentUser();
    if (!$currentUser) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        exit;
    }
    
    // Verificar rol de admin
    $userRole = $roleModel->getUserRole($currentUser['user_id']);
    if (!$userRole || $userRole['role_name'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Sin permisos de administrador']);
        exit;
    }
    
    // Obtener datos del formulario
    $clanName = Utils::sanitizeInput($_POST['clanName'] ?? '');
    $clanDepartamento = Utils::sanitizeInput($_POST['clanDepartamento'] ?? '');
    
    // Validar datos
    if (empty($clanName)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'El nombre del clan es requerido']);
        exit;
    }
    
    // Verificar si ya existe
    if ($clanModel->exists($clanName)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ya existe un clan con ese nombre']);
        exit;
    }
    
    // Crear el clan
    $clanId = $clanModel->create($clanName, $clanDepartamento);
    
    if ($clanId) {
        echo json_encode([
            'success' => true, 
            'message' => 'Clan creado exitosamente',
            'clan_id' => $clanId
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al crear clan en la base de datos']);
    }
    
} catch (Exception $e) {
    error_log("Error en create-clan-direct: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
} catch (Error $e) {
    error_log("Fatal error en create-clan-direct: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error fatal del servidor']);
}
?>
