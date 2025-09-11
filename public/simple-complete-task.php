<?php
// Endpoint simplificado para completar tareas
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en producción

// Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Log para debugging
error_log("=== SIMPLE COMPLETE TASK ===");
error_log("POST: " . json_encode($_POST));
error_log("Session: " . json_encode($_SESSION));

try {
    // Verificar método
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Verificar task_id
    $taskId = (int)($_POST['task_id'] ?? 0);
    if ($taskId <= 0) {
        throw new Exception('Task ID inválido');
    }

    // Verificar sesión (opcional para testing)
    if (!isset($_SESSION['user_id'])) {
        error_log("Advertencia: No hay sesión de usuario");
        // No fallar por esto en testing
    }

    // Conexión a BD
    require_once '../config/database.php';
    $db = Database::getConnection();
    
    error_log("Conexión establecida para task_id: $taskId");

    // Actualizar tarea
    $stmt = $db->prepare("
        UPDATE Tasks 
        SET status = 'completed', 
            is_completed = 1,
            completion_percentage = 100,
            completed_at = NOW(),
            updated_at = NOW() 
        WHERE task_id = ?
    ");
    
    $result = $stmt->execute([$taskId]);
    $rowCount = $stmt->rowCount();
    
    error_log("Query ejecutada. Rows affected: $rowCount");

    if ($result && $rowCount > 0) {
        $response = [
            'success' => true, 
            'message' => 'Tarea completada exitosamente',
            'task_id' => $taskId
        ];
        error_log("✅ Éxito: " . json_encode($response));
        echo json_encode($response);
    } else {
        throw new Exception("No se encontró la tarea con ID $taskId o ya estaba completada");
    }

} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("❌ Error: " . $error);
    echo json_encode([
        'success' => false, 
        'message' => $error,
        'debug' => 'Ver error_log para más detalles'
    ]);
}
?>
