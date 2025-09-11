<?php
/**
 * PRUEBA FINAL ULTRA-SIMPLE
 * Solo SELECT * FROM Tasks WHERE assigned_to_user_id = X
 */

require_once '../app/bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    die('Inicia sesión');
}

$currentUser = $auth->getCurrentUser();
$userId = $currentUser['user_id'];

echo "<h1>🎯 PRUEBA FINAL SIMPLE - Usuario {$userId}</h1>";

try {
    // 1. USAR LA FUNCIÓN NUEVA ULTRA-SIMPLE
    $taskModel = new Task();
    $result = $taskModel->getAllUserTasksForDashboard($userId);
    
    if ($result['success']) {
        $tasks = $result['tasks'];
        echo "<h2>✅ ÉXITO: " . count($tasks) . " tareas encontradas</h2>";
        
        // Mostrar las primeras 10
        echo "<table border='1' style='width:100%; border-collapse:collapse;'>";
        echo "<tr style='background:#dc3545; color:white;'>";
        echo "<th>ID</th><th>Tarea</th><th>Proyecto Original</th><th>Proyecto Mostrado</th><th>Es Personal</th><th>Estado</th>";
        echo "</tr>";
        
        for ($i = 0; $i < min(10, count($tasks)); $i++) {
            $task = $tasks[$i];
            $style = $task['is_personal'] == 1 ? 'background:#e7f3ff; font-weight:bold;' : '';
            echo "<tr style='{$style}'>";
            echo "<td>{$task['task_id']}</td>";
            echo "<td>" . htmlspecialchars($task['task_name']) . "</td>";
            echo "<td>" . htmlspecialchars($task['original_project_name'] ?? 'N/A') . "</td>";
            echo "<td><strong>" . htmlspecialchars($task['project_name']) . "</strong></td>";
            echo "<td>" . ($task['is_personal'] ? '✅ SÍ' : '❌ NO') . "</td>";
            echo "<td>{$task['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Contar personales
        $personales = array_filter($tasks, function($t) { return $t['is_personal'] == 1; });
        if (count($personales) > 0) {
            echo "<h3>🎯 " . count($personales) . " tareas personales encontradas que mostrarán 'Personal'</h3>";
        }
        
    } else {
        echo "<h2>❌ Error: " . $result['error'] . "</h2>";
    }
    
    // 2. PROBAR KANBAN
    echo "<hr><h2>📊 PRUEBA KANBAN</h2>";
    $clanMemberController = new ClanMemberController();
    $reflection = new ReflectionClass($clanMemberController);
    $method = $reflection->getMethod('getKanbanTasksForUser');
    $method->setAccessible(true);
    
    $kanban = $method->invoke($clanMemberController, $userId, null);
    $totalKanban = array_sum(array_map('count', $kanban));
    
    echo "<p><strong>Total en Kanban:</strong> {$totalKanban} tareas</p>";
    foreach ($kanban as $columna => $tareas) {
        echo "<p><strong>{$columna}:</strong> " . count($tareas) . " tareas</p>";
    }
    
    if ($totalKanban > 0) {
        echo "<h3>✅ ¡PERFECTO! El Kanban funciona</h3>";
    }
    
} catch (Exception $e) {
    echo "<h2 style='color:red;'>❌ ERROR: " . $e->getMessage() . "</h2>";
}

echo "<hr><h2>🚀 RESUMEN</h2>";
echo "<p>Si ves tareas arriba, la solución funciona.</p>";
echo "<p>Las tareas con is_personal = 1 mostrarán 'Personal' como proyecto.</p>";
echo "<p><strong>Ve al dashboard del clan member para ver todas tus tareas.</strong></p>";
?>
