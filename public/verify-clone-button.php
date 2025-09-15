<?php
session_start();
header('Content-Type: application/json');

// Verificar si el usuario está autenticado
$isAuthenticated = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

// Verificar si el archivo de vista existe y tiene los cambios
$viewFile = __DIR__ . '/../app/views/clan_leader/tasks.php';
$fileExists = file_exists($viewFile);
$hasCloneButton = false;

if ($fileExists) {
    $content = file_get_contents($viewFile);
    // Buscar el código del botón de clonar con la sintaxis correcta
    $hasCloneButton = strpos($content, 'openCloneTaskModal(${task.task_id})') !== false;
}

// Información de depuración
$debugInfo = [
    'timestamp' => date('Y-m-d H:i:s'),
    'session_status' => session_status() === PHP_SESSION_ACTIVE ? 'active' : 'inactive',
    'user_authenticated' => $isAuthenticated,
    'user_id' => $_SESSION['user_id'] ?? null,
    'view_file_exists' => $fileExists,
    'clone_button_code_present' => $hasCloneButton,
    'php_version' => PHP_VERSION,
    'session_id' => session_id(),
    'session_data' => [
        'user_id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'role_id' => $_SESSION['role_id'] ?? null
    ]
];

// Respuesta
$response = [
    'success' => true,
    'message' => 'Verificación completada',
    'checks' => [
        'authentication' => $isAuthenticated ? '✅ Usuario autenticado' : '❌ Usuario no autenticado',
        'view_file' => $fileExists ? '✅ Archivo de vista existe' : '❌ Archivo de vista no encontrado',
        'clone_button' => $hasCloneButton ? '✅ Código del botón de clonar presente' : '❌ Código del botón de clonar no encontrado'
    ],
    'debug' => $debugInfo,
    'instructions' => !$isAuthenticated ? 
        'Por favor, inicia sesión primero en /public/?route=login' : 
        'Todo parece estar correcto. Intenta limpiar el caché del navegador.'
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>
