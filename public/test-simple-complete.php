<?php
// Test ultra-simple para completar tareas
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Solo headers básicos
header('Content-Type: application/json');

// Log inicial
error_log("=== TEST SIMPLE COMPLETE ===");

try {
    // Verificar si llegaron los datos
    if (!isset($_POST['task_id'])) {
        throw new Exception('No task_id recibido');
    }

    $taskId = (int)$_POST['task_id'];
    error_log("Task ID recibido: $taskId");

    if ($taskId <= 0) {
        throw new Exception('Task ID inválido');
    }

    // Conexión directa sin includes
    $host = 'localhost';
    $dbname = 'rinotrack';
    $username = 'root';
    $password = '/VFwtcC6Xj18';

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    error_log("Conexión establecida");

    // Query simple
    $stmt = $pdo->prepare("UPDATE Tasks SET status = 'completed', is_completed = 1 WHERE task_id = ?");
    $result = $stmt->execute([$taskId]);
    
    error_log("Query ejecutada: " . ($result ? 'true' : 'false'));
    error_log("Filas afectadas: " . $stmt->rowCount());

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'OK', 'task_id' => $taskId]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No rows affected']);
    }

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
