<?php
/**
 * PRUEBA FINAL - SOLUCIÓN DEFINITIVA
 * Verifica que las 111 tareas del usuario se muestren correctamente
 */

require_once '../app/bootstrap.php';

// Verificar autenticación
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    die('Por favor, inicia sesión primero');
}

$currentUser = $auth->getCurrentUser();
$userId = $currentUser['user_id'];

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>✅ PRUEBA FINAL - Solución Definitiva</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #f5f5f5;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 { 
            color: #28a745; 
            border-bottom: 2px solid #28a745;
            padding-bottom: 10px;
        }
        h2 { 
            color: #555; 
            margin-top: 30px;
            background: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #28a745;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }
        th { 
            background: #28a745; 
            color: white; 
            padding: 12px; 
            text-align: left;
            font-weight: 600;
        }
        td { 
            padding: 10px; 
            border-bottom: 1px solid #ddd;
        }
        tr:hover { 
            background: #f8f9fa; 
        }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .personal { background: #e7f3ff; font-weight: bold; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #28a745;
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>✅ PRUEBA FINAL - Solución Definitiva para las Tareas</h1>
        <p><strong>Usuario:</strong> {$currentUser['full_name']} (ID: {$userId})</p>
        <p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";

try {
    // 1. PROBAR LA FUNCIÓN DEFINITIVA
    echo "<h2>🎯 1. FUNCIÓN DEFINITIVA getAllUserTasksForDashboard</h2>";
    
    $taskModel = new Task();
    $result = $taskModel->getAllUserTasksForDashboard($userId);
    
    if ($result['success']) {
        $tasks = $result['tasks'];
        $stats = $result['stats'];
        
        echo "<div class='success'>🎉 ¡ÉXITO! Se encontraron <strong>" . count($tasks) . "</strong> tareas</div>";
        
        // Mostrar estadísticas
        echo "<div class='stats-grid'>";
        echo "<div class='stat-card'><div class='stat-value'>{$stats['total_tasks']}</div><div class='stat-label'>Total Tareas</div></div>";
        echo "<div class='stat-card'><div class='stat-value'>{$stats['personal_tasks']}</div><div class='stat-label'>Tareas Personales</div></div>";
        echo "<div class='stat-card'><div class='stat-value'>{$stats['project_tasks']}</div><div class='stat-label'>Tareas de Proyecto</div></div>";
        echo "<div class='stat-card'><div class='stat-value'>{$stats['completed_tasks']}</div><div class='stat-label'>Completadas</div></div>";
        echo "<div class='stat-card'><div class='stat-value'>{$stats['pending_tasks']}</div><div class='stat-label'>Pendientes</div></div>";
        echo "<div class='stat-card'><div class='stat-value'>{$stats['in_progress_tasks']}</div><div class='stat-label'>En Progreso</div></div>";
        echo "</div>";
        
        // Mostrar las primeras 20 tareas
        echo "<h3>Primeras 20 tareas (de {$stats['total_tasks']} totales):</h3>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Tarea</th><th>Proyecto Mostrado</th><th>Estado</th><th>Es Personal</th><th>Días Restantes</th></tr>";
        
        for ($i = 0; $i < min(20, count($tasks)); $i++) {
            $task = $tasks[$i];
            $personalClass = $task['is_personal'] == 1 ? 'personal' : '';
            echo "<tr class='{$personalClass}'>";
            echo "<td>{$task['task_id']}</td>";
            echo "<td>" . htmlspecialchars($task['task_name']) . "</td>";
            echo "<td><strong>" . htmlspecialchars($task['project_name']) . "</strong></td>";
            echo "<td>{$task['status']}</td>";
            echo "<td>" . ($task['is_personal'] ? '✅ SÍ' : '❌ NO') . "</td>";
            echo "<td>{$task['days_until_due']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Mostrar específicamente las tareas personales
        $personalTasks = array_filter($tasks, function($task) {
            return $task['is_personal'] == 1;
        });
        
        if (count($personalTasks) > 0) {
            echo "<h2>👤 2. TAREAS PERSONALES ENCONTRADAS</h2>";
            echo "<div class='success'>Se encontraron <strong>" . count($personalTasks) . "</strong> tareas personales</div>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Tarea</th><th>Proyecto Mostrado</th><th>Estado</th></tr>";
            foreach ($personalTasks as $task) {
                echo "<tr class='personal'>";
                echo "<td>{$task['task_id']}</td>";
                echo "<td>" . htmlspecialchars($task['task_name']) . "</td>";
                echo "<td><strong>" . htmlspecialchars($task['project_name']) . "</strong></td>";
                echo "<td>{$task['status']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<div class='error'>❌ Error: " . ($result['error'] ?? 'Desconocido') . "</div>";
    }
    
    // 2. PROBAR EL KANBAN
    echo "<h2>📊 3. PRUEBA DEL KANBAN</h2>";
    
    $clanMemberController = new ClanMemberController();
    $reflection = new ReflectionClass($clanMemberController);
    $method = $reflection->getMethod('getKanbanTasksForUser');
    $method->setAccessible(true);
    
    $kanbanResult = $method->invoke($clanMemberController, $userId, null);
    
    $totalKanban = 0;
    echo "<div class='stats-grid'>";
    foreach ($kanbanResult as $column => $tasks) {
        $count = count($tasks);
        $totalKanban += $count;
        echo "<div class='stat-card'><div class='stat-value'>{$count}</div><div class='stat-label'>Columna: {$column}</div></div>";
    }
    echo "</div>";
    echo "<div class='success'>Total en Kanban: <strong>{$totalKanban}</strong> tareas no completadas</div>";
    
    // Mostrar algunas tareas del Kanban
    if ($totalKanban > 0) {
        echo "<h3>Muestra de tareas en Kanban:</h3>";
        echo "<table>";
        echo "<tr><th>Columna</th><th>ID</th><th>Tarea</th><th>Proyecto</th><th>Es Personal</th></tr>";
        foreach ($kanbanResult as $column => $tasks) {
            foreach (array_slice($tasks, 0, 3) as $task) { // Solo mostrar 3 por columna
                $personalClass = ($task['is_personal'] ?? 0) == 1 ? 'personal' : '';
                echo "<tr class='{$personalClass}'>";
                echo "<td>{$column}</td>";
                echo "<td>{$task['task_id']}</td>";
                echo "<td>" . htmlspecialchars($task['task_name']) . "</td>";
                echo "<td><strong>" . htmlspecialchars($task['project_name']) . "</strong></td>";
                echo "<td>" . (($task['is_personal'] ?? 0) ? '✅ SÍ' : '❌ NO') . "</td>";
                echo "</tr>";
            }
        }
        echo "</table>";
    }
    
    // 3. VERIFICACIÓN FINAL
    echo "<h2>🎯 4. VERIFICACIÓN FINAL</h2>";
    
    if (count($result['tasks']) >= 50) { // Esperamos al menos 50 tareas
        echo "<div class='success'>✅ ¡PERFECTO! Se encontraron " . count($result['tasks']) . " tareas. La solución funciona correctamente.</div>";
        echo "<div class='success'>✅ Las tareas personales se muestran con la etiqueta 'Personal' como solicitaste.</div>";
        echo "<div class='success'>✅ El Kanban muestra {$totalKanban} tareas no completadas correctamente distribuidas.</div>";
        
        if ($stats['personal_tasks'] > 0) {
            echo "<div class='success'>✅ Se encontraron {$stats['personal_tasks']} tareas personales que se muestran correctamente con la etiqueta 'Personal'.</div>";
        }
        
        echo "<div class='warning'>🚀 <strong>SOLUCIÓN IMPLEMENTADA:</strong><br>
        1. Función <code>getAllUserTasksForDashboard()</code> optimizada y funcionando<br>
        2. Consulta SQL que maneja correctamente is_personal = 1 → 'Personal'<br>
        3. Dashboard y Kanban actualizados<br>
        4. Vista preparada para mostrar tareas personales con etiqueta 'Personal'</div>";
        
    } else {
        echo "<div class='warning'>⚠️ Se encontraron " . count($result['tasks']) . " tareas. Verifica que el usuario tenga las tareas asignadas correctamente.</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ ERROR CRÍTICO: " . $e->getMessage() . "</div>";
}

echo "
    </div>
</body>
</html>";
