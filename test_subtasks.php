<?php
// Script de prueba para verificar subtareas
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/models/Utils.php';
require_once __DIR__ . '/app/models/Task.php';

echo "<h1>Test de Subtareas</h1>";

try {
    $taskModel = new Task();
    
    // Verificar estructura de tabla Subtasks
    $db = Database::getConnection();
    
    echo "<h2>1. Verificando estructura de tabla Subtasks:</h2>";
    $stmt = $db->prepare("DESCRIBE Subtasks");
    $stmt->execute();
    $columns = $stmt->fetchAll();
    
    echo "<table border='1'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "<td>{$col['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar si subtask_id tiene AUTO_INCREMENT
    $hasAutoIncrement = false;
    foreach ($columns as $col) {
        if ($col['Field'] === 'subtask_id' && $col['Extra'] === 'auto_increment') {
            $hasAutoIncrement = true;
            break;
        }
    }
    
    echo "<h2>2. Estado de AUTO_INCREMENT:</h2>";
    if ($hasAutoIncrement) {
        echo "<p style='color: green;'>✅ subtask_id tiene AUTO_INCREMENT - ¡Correcto!</p>";
    } else {
        echo "<p style='color: red;'>❌ subtask_id NO tiene AUTO_INCREMENT - Necesita corrección</p>";
        echo "<p><strong>Ejecuta este comando en MySQL:</strong></p>";
        echo "<code>ALTER TABLE Subtasks ADD PRIMARY KEY (subtask_id), MODIFY subtask_id int(11) NOT NULL AUTO_INCREMENT;</code>";
    }
    
    // Contar subtareas existentes
    echo "<h2>3. Subtareas existentes:</h2>";
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM Subtasks");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    echo "<p>Total de subtareas en BD: <strong>{$count}</strong></p>";
    
    // Mostrar algunas subtareas si existen
    if ($count > 0) {
        echo "<h3>Últimas 5 subtareas:</h3>";
        $stmt = $db->prepare("SELECT s.*, t.task_name FROM Subtasks s LEFT JOIN Tasks t ON s.task_id = t.task_id ORDER BY s.created_at DESC LIMIT 5");
        $stmt->execute();
        $subtasks = $stmt->fetchAll();
        
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Título</th><th>Tarea Padre</th><th>Estado</th><th>Creado</th></tr>";
        foreach ($subtasks as $sub) {
            echo "<tr>";
            echo "<td>{$sub['subtask_id']}</td>";
            echo "<td>{$sub['title']}</td>";
            echo "<td>{$sub['task_name']}</td>";
            echo "<td>{$sub['status']}</td>";
            echo "<td>{$sub['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Verificar tareas con subtareas
    echo "<h2>4. Tareas con subtareas:</h2>";
    $stmt = $db->prepare("
        SELECT t.task_id, t.task_name, COUNT(s.subtask_id) as subtask_count 
        FROM Tasks t 
        LEFT JOIN Subtasks s ON t.task_id = s.task_id 
        GROUP BY t.task_id, t.task_name 
        HAVING subtask_count > 0
        ORDER BY subtask_count DESC
        LIMIT 10
    ");
    $stmt->execute();
    $tasksWithSubtasks = $stmt->fetchAll();
    
    if (empty($tasksWithSubtasks)) {
        echo "<p>No hay tareas con subtareas actualmente.</p>";
    } else {
        echo "<table border='1'>";
        echo "<tr><th>ID Tarea</th><th>Nombre Tarea</th><th>Número de Subtareas</th></tr>";
        foreach ($tasksWithSubtasks as $task) {
            echo "<tr>";
            echo "<td>{$task['task_id']}</td>";
            echo "<td>{$task['task_name']}</td>";
            echo "<td>{$task['subtask_count']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='?route=admin/projects'>Volver a Proyectos</a></p>";
?>
