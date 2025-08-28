<?php
/**
 * Script para verificar y corregir la consistencia entre 'status' e 'is_completed' en la tabla Tasks
 */

// Incluir configuraciones
require_once __DIR__ . '/../config/database.php';

try {
    // Conectar a la base de datos
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $db = new PDO($dsn, DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "<h2>🔍 Verificando consistencia de estados en tareas...</h2>\n";
    
    // 1. Verificar tareas con status='completed' pero is_completed=0
    $stmt = $db->prepare("
        SELECT task_id, task_name, status, is_completed, completed_at, completion_percentage 
        FROM Tasks 
        WHERE status = 'completed' AND is_completed = 0
    ");
    $stmt->execute();
    $inconsistent1 = $stmt->fetchAll();
    
    echo "<h3>Tareas con status='completed' pero is_completed=0:</h3>\n";
    if (empty($inconsistent1)) {
        echo "<p>✅ No se encontraron inconsistencias de este tipo.</p>\n";
    } else {
        echo "<table border='1'>\n";
        echo "<tr><th>Task ID</th><th>Nombre</th><th>Status</th><th>Is Completed</th><th>Completed At</th><th>Percentage</th></tr>\n";
        foreach ($inconsistent1 as $task) {
            echo "<tr>";
            echo "<td>{$task['task_id']}</td>";
            echo "<td>" . htmlspecialchars($task['task_name']) . "</td>";
            echo "<td>{$task['status']}</td>";
            echo "<td>{$task['is_completed']}</td>";
            echo "<td>{$task['completed_at']}</td>";
            echo "<td>{$task['completion_percentage']}%</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
        
        // Corregir estas inconsistencias
        $updateStmt = $db->prepare("
            UPDATE Tasks 
            SET is_completed = 1, completed_at = COALESCE(completed_at, NOW()), completion_percentage = 100.00
            WHERE status = 'completed' AND is_completed = 0
        ");
        $result = $updateStmt->execute();
        $affectedRows = $updateStmt->rowCount();
        echo "<p>🔧 <strong>Corregidas {$affectedRows} tareas.</strong></p>\n";
    }
    
    // 2. Verificar tareas con status!='completed' pero is_completed=1
    $stmt = $db->prepare("
        SELECT task_id, task_name, status, is_completed, completed_at, completion_percentage 
        FROM Tasks 
        WHERE status != 'completed' AND is_completed = 1
    ");
    $stmt->execute();
    $inconsistent2 = $stmt->fetchAll();
    
    echo "<h3>Tareas con status!='completed' pero is_completed=1:</h3>\n";
    if (empty($inconsistent2)) {
        echo "<p>✅ No se encontraron inconsistencias de este tipo.</p>\n";
    } else {
        echo "<table border='1'>\n";
        echo "<tr><th>Task ID</th><th>Nombre</th><th>Status</th><th>Is Completed</th><th>Completed At</th><th>Percentage</th></tr>\n";
        foreach ($inconsistent2 as $task) {
            echo "<tr>";
            echo "<td>{$task['task_id']}</td>";
            echo "<td>" . htmlspecialchars($task['task_name']) . "</td>";
            echo "<td>{$task['status']}</td>";
            echo "<td>{$task['is_completed']}</td>";
            echo "<td>{$task['completed_at']}</td>";
            echo "<td>{$task['completion_percentage']}%</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
        
        // Corregir estas inconsistencias
        $updateStmt = $db->prepare("
            UPDATE Tasks 
            SET is_completed = 0, completed_at = NULL
            WHERE status != 'completed' AND is_completed = 1
        ");
        $result = $updateStmt->execute();
        $affectedRows = $updateStmt->rowCount();
        echo "<p>🔧 <strong>Corregidas {$affectedRows} tareas.</strong></p>\n";
    }
    
    // 3. Verificar tareas completadas sin 100% de progreso
    $stmt = $db->prepare("
        SELECT task_id, task_name, status, completion_percentage, is_completed
        FROM Tasks 
        WHERE status = 'completed' AND completion_percentage != 100.00
    ");
    $stmt->execute();
    $inconsistent3 = $stmt->fetchAll();
    
    echo "<h3>Tareas completadas sin 100% de progreso:</h3>\n";
    if (empty($inconsistent3)) {
        echo "<p>✅ No se encontraron tareas completadas sin 100% de progreso.</p>\n";
    } else {
        echo "<table border='1'>\n";
        echo "<tr><th>Task ID</th><th>Nombre</th><th>Status</th><th>Percentage Actual</th><th>Is Completed</th></tr>\n";
        foreach ($inconsistent3 as $task) {
            echo "<tr>";
            echo "<td>{$task['task_id']}</td>";
            echo "<td>" . htmlspecialchars($task['task_name']) . "</td>";
            echo "<td>{$task['status']}</td>";
            echo "<td>{$task['completion_percentage']}%</td>";
            echo "<td>{$task['is_completed']}</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
        
        // Corregir estas inconsistencias
        $updateStmt = $db->prepare("
            UPDATE Tasks 
            SET completion_percentage = 100.00
            WHERE status = 'completed' AND completion_percentage != 100.00
        ");
        $result = $updateStmt->execute();
        $affectedRows = $updateStmt->rowCount();
        echo "<p>🔧 <strong>Corregidas {$affectedRows} tareas completadas estableciendo progreso a 100%.</strong></p>\n";
    }
    
    // 4. Verificar estadísticas generales
    $stmt = $db->prepare("
        SELECT 
            status, 
            COUNT(*) as total_tasks,
            SUM(is_completed) as completed_flag_count,
            AVG(completion_percentage) as avg_percentage
        FROM Tasks 
        GROUP BY status
        ORDER BY status
    ");
    $stmt->execute();
    $stats = $stmt->fetchAll();
    
    echo "<h3>📊 Estadísticas generales por estado:</h3>\n";
    echo "<table border='1'>\n";
    echo "<tr><th>Status</th><th>Total Tareas</th><th>Con is_completed=1</th><th>Porcentaje Promedio</th></tr>\n";
    foreach ($stats as $stat) {
        echo "<tr>";
        echo "<td>{$stat['status']}</td>";
        echo "<td>{$stat['total_tasks']}</td>";
        echo "<td>{$stat['completed_flag_count']}</td>";
        echo "<td>" . round($stat['avg_percentage'], 2) . "%</td>";
        echo "</tr>\n";
    }
    echo "</table>\n";
    
    echo "<h2>✅ Verificación completada</h2>\n";
    echo "<p><em>Nota: Este script actualiza automáticamente las inconsistencias encontradas.</em></p>\n";
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>\n";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}
?>
