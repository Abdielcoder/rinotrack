<?php
// Controlador de prueba MUY SIMPLE
header('Content-Type: application/json');

// Log de inicio
error_log('=== TEST CONTROLLER - INICIO ===');
error_log('Método HTTP: ' . $_SERVER['REQUEST_METHOD']);
error_log('URL solicitada: ' . $_SERVER['REQUEST_URI']);

// Capturar todos los datos recibidos
$receivedData = [
    'GET' => $_GET,
    'POST' => $_POST,
    'FILES' => $_FILES,
    'SERVER' => [
        'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
        'CONTENT_TYPE' => $_SERVER['CONTENT_TYPE'] ?? 'NO SET',
        'CONTENT_LENGTH' => $_SERVER['CONTENT_LENGTH'] ?? 'NO SET'
    ]
];

// Log detallado de los datos
error_log('=== DATOS RECIBIDOS ===');
error_log('GET: ' . print_r($_GET, true));
error_log('POST: ' . print_r($_POST, true));
error_log('FILES: ' . print_r($_FILES, true));

// Verificar específicamente las subtareas
if (isset($_POST['subtasks'])) {
    error_log('=== SUBTAREAS ENCONTRADAS ===');
    error_log('Valor raw de subtareas: ' . $_POST['subtasks']);
    
    $decodedSubtasks = json_decode($_POST['subtasks'], true);
    if ($decodedSubtasks !== null) {
        error_log('✅ Subtareas decodificadas correctamente: ' . print_r($decodedSubtasks, true));
        error_log('✅ Cantidad de subtareas: ' . count($decodedSubtasks));
        
        foreach ($decodedSubtasks as $index => $subtask) {
            error_log("✅ Subtarea ${index}: " . print_r($subtask, true));
        }
    } else {
        error_log('❌ Error decodificando subtareas. JSON error: ' . json_last_error_msg());
        error_log('❌ JSON raw: ' . $_POST['subtasks']);
    }
} else {
    error_log('❌ NO se encontraron subtareas en $_POST');
    error_log('❌ Claves disponibles en $_POST: ' . implode(', ', array_keys($_POST)));
}

// Verificar otros campos importantes
$importantFields = ['task_title', 'task_due_date', 'task_project', 'assigned_members'];
foreach ($importantFields as $field) {
    if (isset($_POST[$field])) {
        error_log("✅ Campo '${field}' encontrado: " . print_r($_POST[$field], true));
    } else {
        error_log("❌ Campo '${field}' NO encontrado");
    }
}

// Simular respuesta exitosa
$response = [
    'success' => true,
    'message' => 'Datos recibidos correctamente',
    'received_data' => $receivedData,
    'subtasks_count' => isset($_POST['subtasks']) ? count(json_decode($_POST['subtasks'], true) ?: []) : 0,
    'timestamp' => date('Y-m-d H:i:s'),
    'server_info' => [
        'php_version' => PHP_VERSION,
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time')
    ]
];

error_log('=== ENVIANDO RESPUESTA ===');
error_log('Respuesta: ' . json_encode($response, JSON_PRETTY_PRINT));

echo json_encode($response, JSON_PRETTY_PRINT);

error_log('=== TEST CONTROLLER - FINALIZADO ===');
?>
