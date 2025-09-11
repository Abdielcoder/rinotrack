<?php
// Endpoint directo para completar tareas - sin MVC
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurar headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Log de inicio
error_log("=== COMPLETE TASK DIRECT ===");
error_log("POST data: " . json_encode($_POST));

try {
    // Verificar método
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Obtener task_id
    $taskId = (int)($_POST['task_id'] ?? 0);
    
    if ($taskId <= 0) {
        throw new Exception('Task ID inválido: ' . $taskId);
    }

    // Configurar base de datos directamente
    require_once '../config/database.php';
    
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USERNAME,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    error_log("Conexión DB establecida");
    error_log("Actualizando task_id: $taskId");

    // Actualizar tarea
    $stmt = $pdo->prepare("
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
    
    error_log("Query ejecutada. Result: " . ($result ? 'true' : 'false') . ", Rows affected: $rowCount");

    if ($result && $rowCount > 0) {
        $response = [
            'success' => true, 
            'message' => 'Tarea completada exitosamente',
            'task_id' => $taskId,
            'rows_affected' => $rowCount
        ];
        error_log("✅ Éxito: " . json_encode($response));
        echo json_encode($response);
    } else {
        throw new Exception("No se encontró la tarea con ID $taskId o ya estaba completada");
    }

} catch (PDOException $e) {
    $error = "Error de base de datos: " . $e->getMessage();
    error_log("❌ PDO Error: " . $error);
    echo json_encode(['success' => false, 'message' => $error]);
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
    error_log("❌ Exception: " . $error);
    echo json_encode(['success' => false, 'message' => $error]);
}
?>
