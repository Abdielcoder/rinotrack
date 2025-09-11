<?php
/**
 * SCRIPT DE DEBUG PARA ENCONTRAR EL PROBLEMA CON LAS TAREAS
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
    <title>🔍 DEBUG - Tareas del Usuario</title>
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
            color: #dc3545; 
            border-bottom: 2px solid #dc3545;
            padding-bottom: 10px;
        }
        h2 { 
            color: #555; 
            margin-top: 30px;
            background: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #dc3545;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }
        th { 
            background: #dc3545; 
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
        .code { background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; white-space: pre-wrap; }
        .personal { background: #e7f3ff; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 DEBUG DEFINITIVO - Problema con Tareas</h1>
        <p><strong>Usuario:</strong> {$currentUser['full_name']} (ID: {$userId})</p>
        <p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";

try {
    $db = Database::getInstance()->getConnection();
    
    // 1. VERIFICAR CUÁNTAS TAREAS TIENE EL USUARIO EN TOTAL
    echo "<h2>📊 1. CONTEO TOTAL DE TAREAS DEL USUARIO</h2>";
    
    $totalStmt = $db->prepare("SELECT COUNT(*) as total FROM Tasks WHERE assigned_to_user_id = ?");
    $totalStmt->execute([$userId]);
    $totalTasks = $totalStmt->fetchColumn();
    
    echo "<div class='success'>Total de tareas asignadas directamente: <strong>{$totalTasks}</strong></div>";
    
    // 2. VERIFICAR TAREAS POR Task_Assignments
    $assignmentStmt = $db->prepare("SELECT COUNT(DISTINCT t.task_id) as total FROM Tasks t INNER JOIN Task_Assignments ta ON t.task_id = ta.task_id WHERE ta.user_id = ?");
    $assignmentStmt->execute([$userId]);
    $assignmentTasks = $assignmentStmt->fetchColumn();
    
    echo "<div class='success'>Total de tareas por Task_Assignments: <strong>{$assignmentTasks}</strong></div>";
    
    // 3. VERIFICAR TAREAS PERSONALES CREADAS
    $personalStmt = $db->prepare("SELECT COUNT(*) as total FROM Tasks WHERE is_personal = 1 AND created_by_user_id = ?");
    $personalStmt->execute([$userId]);
    $personalTasks = $personalStmt->fetchColumn();
    
    echo "<div class='success'>Total de tareas personales creadas: <strong>{$personalTasks}</strong></div>";
    
    // 4. CONSULTA SIMPLE Y DIRECTA - TODAS LAS TAREAS
    echo "<h2>📋 2. CONSULTA SIMPLE Y DIRECTA</h2>";
    
    $simpleQuery = "
        SELECT 
            t.task_id,
            t.task_name,
            t.status,
            t.is_personal,
            t.assigned_to_user_id,
            t.created_by_user_id,
            p.project_name,
            p.project_id,
            -- Si es personal, mostrar 'Personal', sino el nombre del proyecto
            CASE 
                WHEN t.is_personal = 1 THEN 'Personal'
                ELSE p.project_name
            END AS display_project_name
        FROM Tasks t
        LEFT JOIN Projects p ON t.project_id = p.project_id
        WHERE (
            -- Tareas asignadas directamente
            t.assigned_to_user_id = ?
            -- O tareas personales creadas por el usuario
            OR (t.is_personal = 1 AND t.created_by_user_id = ?)
            -- O tareas donde está en Task_Assignments
            OR t.task_id IN (SELECT task_id FROM Task_Assignments WHERE user_id = ?)
        )
        AND (t.is_subtask = 0 OR t.is_subtask IS NULL)
        ORDER BY t.task_id DESC
        LIMIT 20
    ";
    
    $simpleStmt = $db->prepare($simpleQuery);
    $simpleStmt->execute([$userId, $userId, $userId]);
    $simpleTasks = $simpleStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='success'>Consulta simple encontró: <strong>" . count($simpleTasks) . "</strong> tareas</div>";
    
    if (count($simpleTasks) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Tarea</th><th>Proyecto Original</th><th>Proyecto Mostrado</th><th>Estado</th><th>Es Personal</th><th>Asignado a</th></tr>";
        foreach ($simpleTasks as $task) {
            $personalClass = $task['is_personal'] == 1 ? 'personal' : '';
            echo "<tr class='{$personalClass}'>";
            echo "<td>{$task['task_id']}</td>";
            echo "<td>" . htmlspecialchars($task['task_name']) . "</td>";
            echo "<td>" . htmlspecialchars($task['project_name'] ?? 'Sin proyecto') . "</td>";
            echo "<td><strong>" . htmlspecialchars($task['display_project_name'] ?? 'Sin proyecto') . "</strong></td>";
            echo "<td>{$task['status']}</td>";
            echo "<td>" . ($task['is_personal'] ? 'SÍ' : 'NO') . "</td>";
            echo "<td>{$task['assigned_to_user_id']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 5. PROBAR LA FUNCIÓN ACTUAL
    echo "<h2>🔧 3. PROBAR FUNCIÓN ACTUAL getAllUserTasksForDashboard</h2>";
    
    $taskModel = new Task();
    $result = $taskModel->getAllUserTasksForDashboard($userId);
    
    if ($result['success']) {
        $tasks = $result['tasks'];
        echo "<div class='success'>Función actual encontró: <strong>" . count($tasks) . "</strong> tareas</div>";
        
        if (count($tasks) > 0) {
            echo "<p>Primeras 5 tareas:</p>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Tarea</th><th>Proyecto</th><th>Es Personal</th><th>Estado</th></tr>";
            for ($i = 0; $i < min(5, count($tasks)); $i++) {
                $task = $tasks[$i];
                $personalClass = $task['is_personal'] == 1 ? 'personal' : '';
                echo "<tr class='{$personalClass}'>";
                echo "<td>{$task['task_id']}</td>";
                echo "<td>" . htmlspecialchars($task['task_name']) . "</td>";
                echo "<td><strong>" . htmlspecialchars($task['project_name']) . "</strong></td>";
                echo "<td>" . ($task['is_personal'] ? 'SÍ' : 'NO') . "</td>";
                echo "<td>{$task['status']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<div class='error'>Error en función actual: " . ($result['error'] ?? 'Desconocido') . "</div>";
    }
    
    // 6. VERIFICAR getKanbanTasksForUser
    echo "<h2>📊 4. PROBAR FUNCIÓN getKanbanTasksForUser</h2>";
    
    $clanMemberController = new ClanMemberController();
    $reflection = new ReflectionClass($clanMemberController);
    $method = $reflection->getMethod('getKanbanTasksForUser');
    $method->setAccessible(true);
    
    $kanbanResult = $method->invoke($clanMemberController, $userId, null);
    
    $totalKanban = 0;
    foreach ($kanbanResult as $column => $tasks) {
        $count = count($tasks);
        $totalKanban += $count;
        echo "<div class='warning'>Columna '{$column}': <strong>{$count}</strong> tareas</div>";
    }
    echo "<div class='success'>Total en Kanban: <strong>{$totalKanban}</strong> tareas</div>";
    
    // 7. MOSTRAR ALGUNAS TAREAS ESPECÍFICAS DEL USUARIO
    echo "<h2>🎯 5. TAREAS ESPECÍFICAS DEL USUARIO {$userId}</h2>";
    
    $userTasksStmt = $db->prepare("
        SELECT task_id, task_name, status, is_personal, project_id, assigned_to_user_id, created_by_user_id
        FROM Tasks 
        WHERE assigned_to_user_id = ? 
        ORDER BY task_id DESC 
        LIMIT 10
    ");
    $userTasksStmt->execute([$userId]);
    $userSpecificTasks = $userTasksStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='success'>Tareas específicas del usuario: <strong>" . count($userSpecificTasks) . "</strong></div>";
    
    if (count($userSpecificTasks) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Tarea</th><th>Estado</th><th>Es Personal</th><th>Proyecto ID</th></tr>";
        foreach ($userSpecificTasks as $task) {
            $personalClass = $task['is_personal'] == 1 ? 'personal' : '';
            echo "<tr class='{$personalClass}'>";
            echo "<td>{$task['task_id']}</td>";
            echo "<td>" . htmlspecialchars($task['task_name']) . "</td>";
            echo "<td>{$task['status']}</td>";
            echo "<td>" . ($task['is_personal'] ? 'SÍ' : 'NO') . "</td>";
            echo "<td>{$task['project_id']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>ERROR CRÍTICO: " . $e->getMessage() . "</div>";
    echo "<div class='code'>Stack Trace:\n" . $e->getTraceAsString() . "</div>";
}

echo "
    </div>
</body>
</html>";
