<?php
// Verificar estructura de tabla Tasks
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=rinotrack;charset=utf8mb4",
        'root',
        '/VFwtcC6Xj18',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "<h2>Verificación de Tabla Tasks</h2>";

    // Verificar estructura
    $stmt = $pdo->query("DESCRIBE Tasks");
    $columns = $stmt->fetchAll();
    
    echo "<h3>Columnas en la tabla Tasks:</h3>";
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

    // Contar tareas
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM Tasks");
    $count = $stmt->fetch();
    echo "<p>Total de tareas en la tabla: " . $count['total'] . "</p>";

    // Mostrar algunas tareas de ejemplo
    $stmt = $pdo->query("SELECT task_id, task_name, status, is_completed FROM Tasks LIMIT 5");
    $tasks = $stmt->fetchAll();
    
    echo "<h3>Primeras 5 tareas:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>task_id</th><th>task_name</th><th>status</th><th>is_completed</th></tr>";
    foreach ($tasks as $task) {
        echo "<tr>";
        echo "<td>" . $task['task_id'] . "</td>";
        echo "<td>" . htmlspecialchars($task['task_name']) . "</td>";
        echo "<td>" . $task['status'] . "</td>";
        echo "<td>" . $task['is_completed'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
