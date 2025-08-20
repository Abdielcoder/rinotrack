<?php
// Script simple para probar la inserción de subtareas
header('Content-Type: application/json');

try {
    // Incluir configuración de base de datos
    require_once '../config/database.php';
    
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Verificar que hay al menos una tarea para usar como referencia
    $stmt = $pdo->query("SELECT task_id FROM Tasks LIMIT 1");
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$task) {
        throw new Exception('No hay tareas en la base de datos para usar como referencia');
    }
    
    $taskId = $task['task_id'];
    
    // Intentar insertar una subtarea de prueba
    $stmt = $pdo->prepare("
        INSERT INTO Subtasks (task_id, title, description, completion_percentage, due_date, priority, assigned_to_user_id, created_by_user_id, subtask_order)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $params = [
        $taskId,                    // task_id
        'Subtarea de prueba',       // title
        'Descripción de prueba',    // description
        25,                         // completion_percentage
        null,                       // due_date
        'medium',                   // priority
        null,                       // assigned_to_user_id
        1,                          // created_by_user_id
        1                           // subtask_order
    ];
    
    $result = $stmt->execute($params);
    
    if ($result) {
        $subtaskId = $pdo->lastInsertId();
        
        // Verificar que se insertó correctamente
        $stmt = $pdo->prepare("SELECT * FROM Subtasks WHERE subtask_id = ?");
        $stmt->execute([$subtaskId]);
        $insertedSubtask = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Limpiar la subtarea de prueba
        $pdo->exec("DELETE FROM Subtasks WHERE subtask_id = " . $subtaskId);
        
        $response = [
            'success' => true,
            'message' => 'Inserción de subtarea exitosa',
            'test_data' => [
                'task_id_used' => $taskId,
                'subtask_id_created' => $subtaskId,
                'subtask_inserted' => $insertedSubtask
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ];
    } else {
        $error = $stmt->errorInfo();
        throw new Exception('Error en inserción: ' . $error[2]);
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Error en prueba: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ];
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
