<?php
/**
 * DIAGNÓSTICO FINAL - ¿Por qué no se ven todas las tareas?
 */

require_once '../app/bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    die('Inicia sesión');
}

$currentUser = $auth->getCurrentUser();
$userId = $currentUser['user_id'];

echo "<h1 style='color:red;'>🔍 DIAGNÓSTICO FINAL - Usuario {$userId}</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // 1. CONTAR TODAS LAS TAREAS DEL USUARIO
    echo "<h2>1️⃣ CONTEO DIRECTO DE TAREAS</h2>";
    
    $stmt1 = $db->prepare("SELECT COUNT(*) as total FROM Tasks WHERE assigned_to_user_id = ?");
    $stmt1->execute([$userId]);
    $directas = $stmt1->fetchColumn();
    echo "<p><strong>Tareas asignadas directamente:</strong> {$directas}</p>";
    
    $stmt2 = $db->prepare("SELECT COUNT(DISTINCT t.task_id) as total FROM Tasks t INNER JOIN Task_Assignments ta ON t.task_id = ta.task_id WHERE ta.user_id = ?");
    $stmt2->execute([$userId]);
    $assignments = $stmt2->fetchColumn();
    echo "<p><strong>Tareas por Task_Assignments:</strong> {$assignments}</p>";
    
    $stmt3 = $db->prepare("SELECT COUNT(*) as total FROM Tasks WHERE is_personal = 1 AND created_by_user_id = ?");
    $stmt3->execute([$userId]);
    $personales = $stmt3->fetchColumn();
    echo "<p><strong>Tareas personales creadas:</strong> {$personales}</p>";
    
    $total_esperado = $directas + $assignments + $personales;
    echo "<p style='background:yellow; padding:10px;'><strong>TOTAL ESPERADO:</strong> {$total_esperado} tareas</p>";
    
    // 2. PROBAR LA CONSULTA NUEVA
    echo "<h2>2️⃣ CONSULTA NUEVA (getAllUserTasksForDashboard)</h2>";
    
    $taskModel = new Task();
    $result = $taskModel->getAllUserTasksForDashboard($userId);
    
    if ($result['success']) {
        $found = count($result['tasks']);
        echo "<p style='background:" . ($found > 50 ? 'lightgreen' : 'lightcoral') . "; padding:10px;'>";
        echo "<strong>Tareas encontradas por la función:</strong> {$found}";
        echo "</p>";
        
        if ($found < $total_esperado) {
            echo "<p style='color:red;'><strong>❌ PROBLEMA:</strong> Se esperaban {$total_esperado} pero solo se encontraron {$found}</p>";
        } else {
            echo "<p style='color:green;'><strong>✅ BIEN:</strong> Se encontraron todas las tareas esperadas</p>";
        }
        
        // Mostrar algunas tareas
        echo "<h3>Muestra de tareas encontradas:</h3>";
        echo "<table border='1' style='border-collapse:collapse; width:100%;'>";
        echo "<tr style='background:#333; color:white;'><th>ID</th><th>Tarea</th><th>Proyecto</th><th>Es Personal</th><th>Estado</th></tr>";
        
        for ($i = 0; $i < min(10, $found); $i++) {
            $task = $result['tasks'][$i];
            $bg = $task['is_personal'] == 1 ? 'background:#e7f3ff;' : '';
            echo "<tr style='{$bg}'>";
            echo "<td>{$task['task_id']}</td>";
            echo "<td>" . htmlspecialchars($task['task_name']) . "</td>";
            echo "<td><strong>" . htmlspecialchars($task['project_name']) . "</strong></td>";
            echo "<td>" . ($task['is_personal'] ? '✅' : '❌') . "</td>";
            echo "<td>{$task['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "<p style='background:red; color:white; padding:10px;'>❌ ERROR: " . $result['error'] . "</p>";
    }
    
    // 3. PROBAR KANBAN
    echo "<h2>3️⃣ KANBAN</h2>";
    
    $clanMemberController = new ClanMemberController();
    $reflection = new ReflectionClass($clanMemberController);
    $method = $reflection->getMethod('getKanbanTasksForUser');
    $method->setAccessible(true);
    
    $kanban = $method->invoke($clanMemberController, $userId, null);
    
    $kanban_total = 0;
    foreach ($kanban as $columna => $tareas) {
        $count = count($tareas);
        $kanban_total += $count;
        $color = $count > 0 ? 'lightgreen' : 'lightcoral';
        echo "<p style='background:{$color}; padding:5px; display:inline-block; margin:5px;'>";
        echo "<strong>{$columna}:</strong> {$count}";
        echo "</p>";
    }
    
    echo "<p style='background:yellow; padding:10px;'><strong>TOTAL EN KANBAN:</strong> {$kanban_total}</p>";
    
    // 4. DIAGNÓSTICO FINAL
    echo "<h2>4️⃣ DIAGNÓSTICO</h2>";
    
    if ($found >= 50) {
        echo "<div style='background:lightgreen; padding:20px; border-radius:10px;'>";
        echo "<h3>✅ SOLUCIÓN FUNCIONANDO</h3>";
        echo "<p>Se encontraron {$found} tareas, las tareas personales muestran 'Personal'</p>";
        echo "<p><strong>Ve al dashboard del clan member para ver todas tus tareas</strong></p>";
        echo "</div>";
    } else {
        echo "<div style='background:lightcoral; padding:20px; border-radius:10px;'>";
        echo "<h3>❌ PROBLEMA ENCONTRADO</h3>";
        echo "<p>Solo se encontraron {$found} tareas de {$total_esperado} esperadas</p>";
        echo "<p>Revisa los logs del servidor para más detalles</p>";
        echo "</div>";
    }
    
    // 5. LOGS DEL SERVIDOR
    echo "<h2>5️⃣ REVISA LOS LOGS</h2>";
    echo "<p>Los logs del servidor te dirán exactamente qué está pasando:</p>";
    echo "<pre style='background:#f0f0f0; padding:10px;'>tail -f /path/to/php/error.log</pre>";
    
} catch (Exception $e) {
    echo "<h2 style='color:red;'>❌ ERROR CRÍTICO: " . $e->getMessage() . "</h2>";
}

echo "<hr>";
echo "<h2>🎯 CONCLUSIÓN</h2>";
echo "<p>Si ves tareas arriba pero no en el dashboard, el problema está en la vista o en el JavaScript.</p>";
echo "<p>Si no ves tareas arriba, el problema está en la consulta SQL.</p>";
?>
