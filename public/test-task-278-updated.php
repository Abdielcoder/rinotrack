<?php
require_once __DIR__ . '/../app/config/bootstrap.php';

echo "<h1>Task 278 Updated Test</h1>";

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Verificar específicamente la tarea 278
    echo "<h2>Task 278 Details (from SQL):</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    echo "<tr><td><strong>task_id</strong></td><td>278</td></tr>";
    echo "<tr><td><strong>task_name</strong></td><td>mi TAREA</td></tr>";
    echo "<tr><td><strong>description</strong></td><td>desc</td></tr>";
    echo "<tr><td><strong>project_id</strong></td><td>43</td></tr>";
    echo "<tr><td><strong>assigned_to_user_id</strong></td><td>4</td></tr>";
    echo "<tr><td><strong>created_by_user_id</strong></td><td>4</td></tr>";
    echo "<tr><td><strong>priority</strong></td><td>medium</td></tr>";
    echo "<tr><td><strong>due_date</strong></td><td>2025-08-29</td></tr>";
    echo "<tr><td><strong>status</strong></td><td>pending</td></tr>";
    echo "<tr><td><strong>is_personal</strong></td><td>1 (YES)</td></tr>";
    echo "</table>";
    
    echo "<h3>Analysis:</h3>";
    echo "<p><strong>✅ Task 278 EXISTS in SQL</strong></p>";
    echo "<p><strong>✅ Is Personal Task:</strong> YES (is_personal = 1)</p>";
    echo "<p><strong>✅ Has Project ID:</strong> YES (project_id = 43)</p>";
    echo "<p><strong>✅ Assigned To User:</strong> 4</p>";
    echo "<p><strong>✅ Created By User:</strong> 4</p>";
    
    echo "<h3>Expected Behavior:</h3>";
    echo "<p>Since this is a personal task (is_personal = 1), the controller should:</p>";
    echo "<ul>";
    echo "<li>✅ Detect it as a personal task</li>";
    echo "<li>✅ Check if current user is assigned_to_user_id (4) OR created_by_user_id (4)</li>";
    echo "<li>✅ Allow update if user ID matches</li>";
    echo "<li>✅ NOT check clan permissions (since it's personal)</li>";
    echo "</ul>";
    
    // Simular la lógica del controlador
    echo "<h3>Controller Logic Simulation:</h3>";
    
    // Simular usuario actual (asumiendo que es el usuario 4)
    $currentUserId = 4; // Cambiar esto según el usuario que esté probando
    echo "<p><strong>Simulating current user ID:</strong> $currentUserId</p>";
    
    $assignedUserId = 4;
    $createdByUserId = 4;
    $isPersonal = 1;
    
    $isOwner = (int)$assignedUserId === (int)$currentUserId;
    $isCreator = (int)$createdByUserId === (int)$currentUserId;
    
    echo "<p><strong>Permission Check:</strong></p>";
    echo "<ul>";
    echo "<li>Is Owner (assigned_to_user_id == current_user): " . ($isOwner ? "✅ YES" : "❌ NO") . "</li>";
    echo "<li>Is Creator (created_by_user_id == current_user): " . ($isCreator ? "✅ YES" : "❌ NO") . "</li>";
    echo "<li>Can Update (isOwner OR isCreator): " . (($isOwner || $isCreator) ? "✅ YES" : "❌ NO") . "</li>";
    echo "</ul>";
    
    if ($isOwner || $isCreator) {
        echo "<p style='color: green;'>✅ <strong>ACCESS GRANTED</strong> - User can update this personal task</p>";
    } else {
        echo "<p style='color: red;'>❌ <strong>ACCESS DENIED</strong> - User cannot update this personal task</p>";
    }
    
    // Verificar si la tarea existe realmente en la base de datos
    echo "<h2>Database Verification:</h2>";
    $stmt = $pdo->prepare("SELECT * FROM Tasks WHERE task_id = ?");
    $stmt->execute([278]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($task) {
        echo "<p style='color: green;'>✅ Task 278 EXISTS in database!</p>";
        echo "<p><strong>Database vs SQL match:</strong> " . 
             ($task['task_name'] === 'mi TAREA' && 
              $task['is_personal'] == 1 && 
              $task['assigned_to_user_id'] == 4) ? "✅ YES" : "❌ NO" . "</p>";
    } else {
        echo "<p style='color: red;'>❌ Task 278 NOT FOUND in database!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
