<?php
// Script para verificar la base de datos
header('Content-Type: application/json');

try {
    // Incluir configuración de base de datos
    require_once '../config/database.php';
    
    $response = [
        'success' => true,
        'message' => 'Verificación de base de datos',
        'timestamp' => date('Y-m-d H:i:s'),
        'database_info' => []
    ];
    
    // Verificar conexión
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    $response['database_info']['connection'] = '✅ Conexión exitosa';
    
    // Verificar que la tabla Subtasks existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'Subtasks'");
    $subtasksTableExists = $stmt->rowCount() > 0;
    
    $response['database_info']['subtasks_table_exists'] = $subtasksTableExists;
    
    if ($subtasksTableExists) {
        $response['database_info']['subtasks_table'] = '✅ Tabla Subtasks encontrada';
        
        // Verificar estructura de la tabla
        $stmt = $pdo->query("DESCRIBE Subtasks");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response['database_info']['subtasks_structure'] = $columns;
        
        // Verificar que subtask_id sea AUTO_INCREMENT y PRIMARY KEY
        $subtaskIdColumn = null;
        foreach ($columns as $column) {
            if ($column['Field'] === 'subtask_id') {
                $subtaskIdColumn = $column;
                break;
            }
        }
        
        if ($subtaskIdColumn) {
            $response['database_info']['subtask_id_ok'] = 
                strpos($subtaskIdColumn['Extra'], 'auto_increment') !== false && 
                $subtaskIdColumn['Key'] === 'PRI';
        }
        
        // Verificar índices
        $stmt = $pdo->query("SHOW INDEX FROM Subtasks");
        $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response['database_info']['indexes'] = $indexes;
        
        // Verificar que hay al menos una subtarea para testing
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Subtasks");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        $response['database_info']['subtasks_count'] = $count['count'];
        
    } else {
        $response['database_info']['subtasks_table'] = '❌ Tabla Subtasks NO encontrada';
    }
    
    // Verificar tabla Tasks
    $stmt = $pdo->query("SHOW TABLES LIKE 'Tasks'");
    $tasksTableExists = $stmt->rowCount() > 0;
    
    $response['database_info']['tasks_table_exists'] = $tasksTableExists;
    
    if ($tasksTableExists) {
        $response['database_info']['tasks_table'] = '✅ Tabla Tasks encontrada';
        
        // Verificar estructura de Tasks
        $stmt = $pdo->query("DESCRIBE Tasks");
        $taskColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response['database_info']['tasks_structure'] = $taskColumns;
        
        // Verificar que hay al menos una tarea para testing
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Tasks");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        $response['database_info']['tasks_count'] = $count['count'];
    }
    
    // Verificar que el usuario del clan existe
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Users WHERE role = 'clan_leader' LIMIT 1");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    $response['database_info']['clan_leaders_count'] = $count['count'];
    
    // Verificar que hay proyectos
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Projects");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    $response['database_info']['projects_count'] = $count['count'];
    
    // Verificar permisos de inserción
    try {
        $stmt = $pdo->prepare("INSERT INTO Subtasks (task_id, title, description, completion_percentage, due_date, priority, assigned_to_user_id, created_by_user_id, subtask_order) VALUES (999999, 'TEST', 'TEST', 0, NULL, 'medium', NULL, 1, 1)");
        $testInsert = $stmt->execute();
        
        if ($testInsert) {
            $response['database_info']['insert_permission'] = '✅ Permisos de inserción OK';
            // Limpiar el registro de prueba
            $pdo->exec("DELETE FROM Subtasks WHERE task_id = 999999 AND title = 'TEST'");
        } else {
            $response['database_info']['insert_permission'] = '❌ Error en inserción de prueba';
        }
    } catch (Exception $e) {
        $response['database_info']['insert_permission'] = '❌ Excepción en inserción: ' . $e->getMessage();
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Error en verificación: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ];
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
