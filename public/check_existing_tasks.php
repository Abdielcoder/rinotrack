<?php
require_once __DIR__ . '/../app/bootstrap.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verificar Tareas Personales</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .medium { background-color: #fff3cd; }
        .high { background-color: #f8d7da; }
        .critical { background-color: #d1ecf1; }
        .low { background-color: #d4edda; }
    </style>
</head>
<body>
    <h1>Verificación de Tareas Personales</h1>
    
    <?php
    try {
        $db = Database::getConnection();
        
        echo "<h2>1. Todas las tareas personales (is_personal = 1):</h2>";
        $stmt = $db->query("
            SELECT 
                t.task_id,
                t.task_name,
                t.priority,
                t.status,
                t.created_at,
                t.assigned_to_user_id,
                u.full_name
            FROM Tasks t 
            LEFT JOIN Users u ON t.assigned_to_user_id = u.user_id
            WHERE t.is_personal = 1 
            ORDER BY t.created_at DESC 
            LIMIT 20
        ");
        $personalTasks = $stmt->fetchAll();
        
        if (empty($personalTasks)) {
            echo "<p>No hay tareas personales en la base de datos.</p>";
        } else {
            echo "<table>";
            echo "<tr><th>ID</th><th>Nombre</th><th>Prioridad</th><th>Estado</th><th>Usuario</th><th>Creado</th></tr>";
            foreach ($personalTasks as $task) {
                $priorityClass = $task['priority'];
                echo "<tr class='$priorityClass'>";
                echo "<td>{$task['task_id']}</td>";
                echo "<td>{$task['task_name']}</td>";
                echo "<td><strong>{$task['priority']}</strong></td>";
                echo "<td>{$task['status']}</td>";
                echo "<td>{$task['full_name']} (ID: {$task['assigned_to_user_id']})</td>";
                echo "<td>{$task['created_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Contar por prioridad
            $counts = [];
            foreach ($personalTasks as $task) {
                $priority = $task['priority'];
                $counts[$priority] = ($counts[$priority] ?? 0) + 1;
            }
            
            echo "<h3>Resumen por prioridad:</h3>";
            echo "<ul>";
            foreach ($counts as $priority => $count) {
                echo "<li><strong>$priority:</strong> $count tareas</li>";
            }
            echo "</ul>";
        }
        
        echo "<h2>2. Test de INSERT directo:</h2>";
        
        // Intentar insertar una tarea con prioridad 'high' directamente
        $testTaskName = "Test Direct INSERT " . date('Y-m-d H:i:s');
        $stmt = $db->prepare("
            INSERT INTO Tasks (
                task_name, 
                description, 
                priority, 
                due_date, 
                status, 
                assigned_to_user_id, 
                created_by_user_id, 
                project_id, 
                is_personal
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $testTaskName,
            'Test directo de prioridad',
            'high',  // <- PRIORIDAD ALTA
            date('Y-m-d', strtotime('+1 day')),
            'pending',
            1, // assigned_to_user_id
            1, // created_by_user_id  
            1, // project_id (debe existir)
            1  // is_personal
        ]);
        
        if ($result) {
            $taskId = $db->lastInsertId();
            echo "<p>✅ Tarea de test insertada con ID: $taskId</p>";
            
            // Verificar lo que se guardó
            $stmt2 = $db->prepare("SELECT task_id, task_name, priority FROM Tasks WHERE task_id = ?");
            $stmt2->execute([$taskId]);
            $testTask = $stmt2->fetch();
            
            echo "<p>Tarea guardada: ID={$testTask['task_id']}, Prioridad=<strong>{$testTask['priority']}</strong></p>";
            
            if ($testTask['priority'] === 'high') {
                echo "<p style='color: green;'>✅ La prioridad se guardó correctamente como 'high'</p>";
            } else {
                echo "<p style='color: red;'>❌ ERROR: Se esperaba 'high' pero se guardó '{$testTask['priority']}'</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Error al insertar tarea de test</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
    ?>
</body>
</html>
