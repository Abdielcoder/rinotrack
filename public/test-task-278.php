<?php
require_once __DIR__ . '/../app/config/bootstrap.php';

echo "<h1>Task 278 Specific Test</h1>";

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Verificar específicamente la tarea 278
    echo "<h2>Task 278 Details:</h2>";
    $stmt = $pdo->prepare("SELECT * FROM Tasks WHERE task_id = ?");
    $stmt->execute([278]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($task) {
        echo "<p style='color: green;'>✅ Task 278 EXISTS!</p>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        foreach ($task as $field => $value) {
            echo "<tr>";
            echo "<td><strong>$field</strong></td>";
            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Verificar si es tarea personal
        $isPersonal = (int)($task['is_personal'] ?? 0) === 1;
        echo "<h3>Task Analysis:</h3>";
        echo "<p><strong>Is Personal Task:</strong> " . ($isPersonal ? "YES" : "NO") . "</p>";
        echo "<p><strong>Project ID:</strong> " . ($task['project_id'] ?? 'NULL') . "</p>";
        echo "<p><strong>Assigned To User ID:</strong> " . ($task['assigned_to_user_id'] ?? 'NULL') . "</p>";
        echo "<p><strong>Created By User ID:</strong> " . ($task['created_by_user_id'] ?? 'NULL') . "</p>";
        
        // Verificar el usuario actual (simular)
        echo "<h3>Current User Simulation:</h3>";
        echo "<p>Assuming current user is the assigned user or creator...</p>";
        
        $assignedUserId = $task['assigned_to_user_id'] ?? null;
        $createdByUserId = $task['created_by_user_id'] ?? null;
        
        echo "<p><strong>Assigned User ID:</strong> $assignedUserId</p>";
        echo "<p><strong>Created By User ID:</strong> $createdByUserId</p>";
        
        // Verificar si el usuario actual podría actualizar esta tarea
        if ($isPersonal) {
            echo "<p style='color: blue;'>ℹ️ This is a personal task. User should be able to update if they are the assigned user or creator.</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ This is not a personal task. User needs clan access or assignment.</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Task 278 DOES NOT EXIST!</p>";
        
        // Mostrar las tareas más cercanas
        echo "<h3>Closest Tasks:</h3>";
        $stmt = $pdo->query("SELECT task_id, task_name, is_personal FROM Tasks WHERE task_id BETWEEN 270 AND 285 ORDER BY task_id");
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($tasks)) {
            echo "<p>No tasks found in range 270-285.</p>";
        } else {
            echo "<table border='1'>";
            echo "<tr><th>Task ID</th><th>Task Name</th><th>Is Personal</th></tr>";
            foreach ($tasks as $t) {
                echo "<tr>";
                echo "<td>" . $t['task_id'] . "</td>";
                echo "<td>" . htmlspecialchars($t['task_name']) . "</td>";
                echo "<td>" . ($t['is_personal'] ? "YES" : "NO") . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
