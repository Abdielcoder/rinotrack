<?php
require_once __DIR__ . '/../app/bootstrap.php';

echo "<h1>Debug de Prioridad - Tareas Personales</h1>";

// Test 1: Verificar valores enum válidos
echo "<h2>1. Valores enum válidos para Tasks.priority:</h2>";
try {
    $db = Database::getConnection();
    $stmt = $db->query("SHOW COLUMNS FROM Tasks LIKE 'priority'");
    $result = $stmt->fetch();
    echo "<pre>Definición: " . print_r($result, true) . "</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Test 2: Insertar tarea de prueba con prioridad 'high'
echo "<h2>2. Test INSERT con prioridad 'high':</h2>";
try {
    $taskName = "Test Prioridad " . date('H:i:s');
    $priority = 'high';
    
    $stmt = $db->prepare("INSERT INTO Tasks (task_name, description, priority, due_date, assigned_to_user_id, created_by_user_id, project_id, is_personal) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([
        $taskName,
        'Test de prioridad',
        $priority,
        date('Y-m-d', strtotime('+1 day')),
        1, // user_id
        1, // created_by
        1, // project_id (usar uno existente)
        1  // is_personal
    ]);
    
    if ($result) {
        $taskId = $db->lastInsertId();
        echo "✅ Tarea creada con ID: $taskId<br>";
        
        // Verificar lo que se guardó
        $stmt2 = $db->prepare("SELECT task_id, task_name, priority FROM Tasks WHERE task_id = ?");
        $stmt2->execute([$taskId]);
        $savedTask = $stmt2->fetch();
        
        echo "<pre>Tarea guardada: " . print_r($savedTask, true) . "</pre>";
        
        if ($savedTask['priority'] === 'high') {
            echo "✅ Prioridad guardada correctamente: {$savedTask['priority']}";
        } else {
            echo "❌ Prioridad incorrecta. Esperado: 'high', Guardado: '{$savedTask['priority']}'";
        }
    } else {
        echo "❌ Error al insertar tarea";
    }
} catch (Exception $e) {
    echo "Error en INSERT: " . $e->getMessage();
}

// Test 3: Verificar tareas personales existentes
echo "<h2>3. Tareas personales existentes:</h2>";
try {
    $stmt = $db->query("SELECT task_id, task_name, priority, created_at FROM Tasks WHERE is_personal = 1 ORDER BY created_at DESC LIMIT 10");
    $personalTasks = $stmt->fetchAll();
    
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Prioridad</th><th>Creado</th></tr>";
    foreach ($personalTasks as $task) {
        $priorityColor = $task['priority'] === 'medium' ? 'style="background-color: yellow"' : '';
        echo "<tr $priorityColor>";
        echo "<td>{$task['task_id']}</td>";
        echo "<td>{$task['task_name']}</td>";
        echo "<td><strong>{$task['priority']}</strong></td>";
        echo "<td>{$task['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "Error en SELECT: " . $e->getMessage();
}

// Test 4: Probar createPersonalTaskSimple directamente
echo "<h2>4. Test createPersonalTaskSimple con prioridad 'critical':</h2>";
try {
    $taskModel = new Task();
    
    $taskData = [
        'task_name' => 'Test Critical Priority ' . date('H:i:s'),
        'description' => 'Test de prioridad crítica',
        'priority' => 'critical',
        'due_date' => date('Y-m-d', strtotime('+2 days')),
        'status' => 'pending',
        'assigned_to_user_id' => 1
    ];
    
    echo "<pre>Datos a enviar: " . print_r($taskData, true) . "</pre>";
    
    $taskId = $taskModel->createPersonalTaskSimple($taskData);
    
    if ($taskId) {
        echo "✅ Tarea creada con ID: $taskId<br>";
        
        // Verificar lo que se guardó
        $stmt = $db->prepare("SELECT task_id, task_name, priority FROM Tasks WHERE task_id = ?");
        $stmt->execute([$taskId]);
        $savedTask = $stmt->fetch();
        
        echo "<pre>Tarea guardada: " . print_r($savedTask, true) . "</pre>";
        
        if ($savedTask['priority'] === 'critical') {
            echo "✅ Prioridad guardada correctamente: {$savedTask['priority']}";
        } else {
            echo "❌ Prioridad incorrecta. Esperado: 'critical', Guardado: '{$savedTask['priority']}'";
        }
    } else {
        echo "❌ Error al crear tarea con createPersonalTaskSimple";
    }
} catch (Exception $e) {
    echo "Error en createPersonalTaskSimple: " . $e->getMessage();
}
?>
