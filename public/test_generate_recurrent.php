<?php
require_once __DIR__ . '/../app/bootstrap.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Generación de Tareas Recurrentes</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        .task-item { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; margin: 8px 0; }
        .recurrent-task { border-left: 4px solid #f59e0b; }
        .instance-task { border-left: 4px solid #10b981; margin-left: 20px; }
    </style>
</head>
<body>
    <h1>Test de Generación de Tareas Recurrentes</h1>
    
    <?php
    try {
        $taskModel = new Task();
        $db = Database::getConnection();
        
        echo "<h2>1. Tareas recurrentes existentes:</h2>";
        $stmt = $db->query("
            SELECT task_id, task_name, recurrence_type, recurrence_start_date, 
                   recurrence_end_date, last_generated_date, assigned_to_user_id
            FROM Tasks 
            WHERE is_recurrent = 1 
            ORDER BY task_id
        ");
        $recurrentTasks = $stmt->fetchAll();
        
        if (empty($recurrentTasks)) {
            echo "<p class='info'>No hay tareas recurrentes configuradas</p>";
        } else {
            foreach ($recurrentTasks as $task) {
                echo "<div class='task-item recurrent-task'>";
                echo "<strong>ID {$task['task_id']}:</strong> {$task['task_name']}<br>";
                echo "<strong>Tipo:</strong> {$task['recurrence_type']}<br>";
                echo "<strong>Inicio:</strong> {$task['recurrence_start_date']}<br>";
                echo "<strong>Fin:</strong> " . ($task['recurrence_end_date'] ?? 'Indefinida') . "<br>";
                echo "<strong>Último generado:</strong> " . ($task['last_generated_date'] ?? 'Nunca') . "<br>";
                echo "<strong>Asignado a:</strong> Usuario ID {$task['assigned_to_user_id']}";
                echo "</div>";
            }
        }
        
        echo "<h2>2. Ejecutando generación de instancias:</h2>";
        $generatedCount = $taskModel->generateRecurrentInstances();
        
        if ($generatedCount !== false) {
            echo "<p class='success'>✅ Proceso completado</p>";
            echo "<p class='info'>📊 Instancias generadas: $generatedCount</p>";
            
            if ($generatedCount > 0) {
                echo "<h2>3. Instancias generadas recientemente:</h2>";
                $stmt = $db->query("
                    SELECT t.task_id, t.task_name, t.due_date, t.assigned_to_user_id,
                           tr.task_name as parent_task_name, tr.recurrence_type
                    FROM Tasks t
                    JOIN Tasks tr ON t.parent_recurrent_task_id = tr.task_id
                    WHERE t.created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
                    ORDER BY t.created_at DESC
                    LIMIT 20
                ");
                $instances = $stmt->fetchAll();
                
                foreach ($instances as $instance) {
                    echo "<div class='task-item instance-task'>";
                    echo "<strong>Instancia ID {$instance['task_id']}:</strong> {$instance['task_name']}<br>";
                    echo "<strong>Fecha vencimiento:</strong> {$instance['due_date']}<br>";
                    echo "<strong>Tarea padre:</strong> {$instance['parent_task_name']} ({$instance['recurrence_type']})<br>";
                    echo "<strong>Asignado a:</strong> Usuario ID {$instance['assigned_to_user_id']}";
                    echo "</div>";
                }
            }
        } else {
            echo "<p class='error'>❌ Error en el proceso</p>";
        }
        
        echo "<h2>4. Estadísticas:</h2>";
        $statsStmt = $db->query("
            SELECT 
                COUNT(CASE WHEN is_recurrent = 1 THEN 1 END) as total_recurrent,
                COUNT(CASE WHEN parent_recurrent_task_id IS NOT NULL THEN 1 END) as total_instances,
                COUNT(CASE WHEN is_recurrent = 1 AND recurrence_type = 'daily' THEN 1 END) as daily_tasks,
                COUNT(CASE WHEN is_recurrent = 1 AND recurrence_type = 'weekly' THEN 1 END) as weekly_tasks,
                COUNT(CASE WHEN is_recurrent = 1 AND recurrence_type = 'monthly' THEN 1 END) as monthly_tasks
            FROM Tasks
        ");
        $stats = $statsStmt->fetch();
        
        echo "<ul>";
        echo "<li><strong>Tareas recurrentes configuradas:</strong> {$stats['total_recurrent']}</li>";
        echo "<li><strong>Instancias generadas total:</strong> {$stats['total_instances']}</li>";
        echo "<li><strong>Tareas diarias:</strong> {$stats['daily_tasks']}</li>";
        echo "<li><strong>Tareas semanales:</strong> {$stats['weekly_tasks']}</li>";
        echo "<li><strong>Tareas mensuales:</strong> {$stats['monthly_tasks']}</li>";
        echo "</ul>";
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    }
    ?>
    
    <hr>
    <p>
        <a href="?route=clan_member/tasks">← Volver a Tareas</a> | 
        <a href="add_recurrence_fields.php">Agregar Campos BD</a> |
        <a href="cron_generate_recurrent_tasks.php">Ejecutar Cron Manual</a>
    </p>
</body>
</html>
