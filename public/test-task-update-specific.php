<?php
require_once __DIR__ . '/../app/config/bootstrap.php';

echo "<h1>Task Update Specific Test</h1>";

try {
    // Crear instancia del modelo Task
    $taskModel = new Task();
    echo "<p style='color: green;'>✅ Task model created successfully</p>";
    
    // Buscar la tarea 278 (la que está fallando)
    $task = $taskModel->findById(278);
    if ($task) {
        echo "<h2>Task 278 Found:</h2>";
        echo "<pre>" . print_r($task, true) . "</pre>";
        
        // Intentar actualizar la tarea con los mismos datos que se están enviando
        echo "<h2>Testing Task Update:</h2>";
        $result = $taskModel->update(
            278,                    // taskId
            'mi TAREA',            // taskName
            'desc',                // description
            null,                  // assignedUserId
            'urgent',              // priority
            '2025-08-29',          // dueDate
            null,                  // assignedPercentage
            'pending'              // status
        );
        
        if ($result) {
            echo "<p style='color: green;'>✅ Task update successful!</p>";
        } else {
            echo "<p style='color: red;'>❌ Task update failed!</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Task 278 not found!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Verificar la estructura de la tabla Tasks
echo "<h2>Database Table Structure:</h2>";
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    $stmt = $pdo->query("DESCRIBE Tasks");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "<td>" . $column['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}
?>
