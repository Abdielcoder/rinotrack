<?php
require_once __DIR__ . '/../app/config/bootstrap.php';

echo "<h1>Task Existence Test</h1>";

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Verificar si la tarea 278 existe
    echo "<h2>Checking Task 278:</h2>";
    $stmt = $pdo->prepare("SELECT * FROM Tasks WHERE task_id = ?");
    $stmt->execute([278]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($task) {
        echo "<p style='color: green;'>✅ Task 278 EXISTS!</p>";
        echo "<pre>" . print_r($task, true) . "</pre>";
    } else {
        echo "<p style='color: red;'>❌ Task 278 DOES NOT EXIST!</p>";
    }
    
    // Mostrar las últimas 10 tareas
    echo "<h2>Last 10 Tasks:</h2>";
    $stmt = $pdo->query("SELECT task_id, task_name, project_id, assigned_to_user_id, status, is_personal FROM Tasks ORDER BY task_id DESC LIMIT 10");
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1'>";
    echo "<tr><th>Task ID</th><th>Task Name</th><th>Project ID</th><th>Assigned To</th><th>Status</th><th>Is Personal</th></tr>";
    foreach ($tasks as $task) {
        echo "<tr>";
        echo "<td>" . $task['task_id'] . "</td>";
        echo "<td>" . htmlspecialchars($task['task_name']) . "</td>";
        echo "<td>" . $task['project_id'] . "</td>";
        echo "<td>" . $task['assigned_to_user_id'] . "</td>";
        echo "<td>" . $task['status'] . "</td>";
        echo "<td>" . $task['is_personal'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar tareas personales
    echo "<h2>Personal Tasks:</h2>";
    $stmt = $pdo->query("SELECT task_id, task_name, assigned_to_user_id, status FROM Tasks WHERE is_personal = 1 ORDER BY task_id DESC");
    $personalTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($personalTasks)) {
        echo "<p>No personal tasks found.</p>";
    } else {
        echo "<table border='1'>";
        echo "<tr><th>Task ID</th><th>Task Name</th><th>Assigned To</th><th>Status</th></tr>";
        foreach ($personalTasks as $task) {
            echo "<tr>";
            echo "<td>" . $task['task_id'] . "</td>";
            echo "<td>" . htmlspecialchars($task['task_name']) . "</td>";
            echo "<td>" . $task['assigned_to_user_id'] . "</td>";
            echo "<td>" . $task['status'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Verificar el trigger
    echo "<h2>Checking Task_History Table:</h2>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Task_History");
    $result = $stmt->fetch();
    echo "<p>Task_History records: " . $result['count'] . "</p>";
    
    // Verificar si hay registros para task_id 278 en Task_History
    $stmt = $pdo->prepare("SELECT * FROM Task_History WHERE task_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([278]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($history)) {
        echo "<p>No Task_History records found for task_id 278.</p>";
    } else {
        echo "<p>Task_History records for task_id 278:</p>";
        echo "<pre>" . print_r($history, true) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
