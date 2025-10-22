<?php
/**
 * CONSULTA SIMPLE Y DIRECTA - SIN MAMADAS
 * Como debe ser: SELECT * FROM Tasks WHERE assigned_to_user_id = 2
 */

require_once '../app/bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    die('Inicia sesión primero');
}

$currentUser = $auth->getCurrentUser();
$userId = $currentUser['user_id'];

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>🎯 CONSULTA SIMPLE - Sin Mamadas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #dc3545; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #dc3545; color: white; padding: 10px; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f8f9fa; }
        .personal { background: #e7f3ff; font-weight: bold; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🎯 CONSULTA SIMPLE - Usuario {$userId}</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h2>1️⃣ PASO 1: Todas las tareas del usuario {$userId}</h2>";
    
    // CONSULTA SIMPLE Y DIRECTA
    $stmt = $db->prepare("SELECT * FROM Tasks WHERE assigned_to_user_id = ?");
    $stmt->execute([$userId]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='success'>✅ Encontradas: <strong>" . count($tasks) . "</strong> tareas</div>";
    
    if (count($tasks) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Tarea</th><th>Proyecto ID</th><th>Estado</th><th>Es Personal</th><th>Fecha Venc.</th></tr>";
        
        foreach ($tasks as $task) {
            $personalClass = $task['is_personal'] == 1 ? 'personal' : '';
            echo "<tr class='{$personalClass}'>";
            echo "<td>{$task['task_id']}</td>";
            echo "<td>" . htmlspecialchars($task['task_name']) . "</td>";
            echo "<td>{$task['project_id']}</td>";
            echo "<td>{$task['status']}</td>";
            echo "<td>" . ($task['is_personal'] ? '✅ SÍ' : '❌ NO') . "</td>";
            echo "<td>{$task['due_date']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>2️⃣ PASO 2: Información de los proyectos</h2>";
    
    // Obtener IDs únicos de proyectos
    $projectIds = array_unique(array_filter(array_column($tasks, 'project_id')));
    
    if (!empty($projectIds)) {
        $placeholders = implode(',', array_fill(0, count($projectIds), '?'));
        $projectStmt = $db->prepare("SELECT * FROM Projects WHERE project_id IN ({$placeholders})");
        $projectStmt->execute($projectIds);
        $projects = $projectStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Crear array asociativo para fácil acceso
        $projectsById = [];
        foreach ($projects as $project) {
            $projectsById[$project['project_id']] = $project;
        }
        
        echo "<div class='success'>✅ Proyectos encontrados: <strong>" . count($projects) . "</strong></div>";
        
        echo "<table>";
        echo "<tr><th>Proyecto ID</th><th>Nombre Proyecto</th><th>Clan ID</th><th>Estado</th><th>Es Personal</th></tr>";
        foreach ($projects as $project) {
            $personalClass = $project['is_personal'] == 1 ? 'personal' : '';
            echo "<tr class='{$personalClass}'>";
            echo "<td>{$project['project_id']}</td>";
            echo "<td>" . htmlspecialchars($project['project_name']) . "</td>";
            echo "<td>{$project['clan_id']}</td>";
            echo "<td>{$project['status']}</td>";
            echo "<td>" . ($project['is_personal'] ? '✅ SÍ' : '❌ NO') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>3️⃣ PASO 3: CONSULTA COMBINADA SIMPLE</h2>";
    
    // CONSULTA COMBINADA PERO SIMPLE
    $combinedStmt = $db->prepare("
        SELECT 
            t.task_id,
            t.task_name,
            t.status,
            t.is_personal as task_is_personal,
            t.due_date,
            t.priority,
            p.project_id,
            p.project_name,
            p.is_personal as project_is_personal,
            p.clan_id,
            -- AQUÍ LA MAGIA: Si la tarea es personal, mostrar 'Personal'
            CASE 
                WHEN t.is_personal = 1 THEN 'Personal'
                ELSE p.project_name
            END AS display_project_name
        FROM Tasks t
        LEFT JOIN Projects p ON t.project_id = p.project_id
        WHERE t.assigned_to_user_id = ?
        ORDER BY t.task_id DESC
    ");
    
    $combinedStmt->execute([$userId]);
    $combinedTasks = $combinedStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='success'>✅ Consulta combinada: <strong>" . count($combinedTasks) . "</strong> tareas con info de proyecto</div>";
    
    if (count($combinedTasks) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Tarea</th><th>Proyecto Original</th><th>Proyecto Mostrado</th><th>Estado</th><th>Es Personal</th></tr>";
        
        foreach ($combinedTasks as $task) {
            $personalClass = $task['task_is_personal'] == 1 ? 'personal' : '';
            echo "<tr class='{$personalClass}'>";
            echo "<td>{$task['task_id']}</td>";
            echo "<td>" . htmlspecialchars($task['task_name']) . "</td>";
            echo "<td>" . htmlspecialchars($task['project_name'] ?? 'Sin proyecto') . "</td>";
            echo "<td><strong>" . htmlspecialchars($task['display_project_name'] ?? 'Sin proyecto') . "</strong></td>";
            echo "<td>{$task['status']}</td>";
            echo "<td>" . ($task['task_is_personal'] ? '✅ SÍ' : '❌ NO') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Contar tareas personales
        $personalCount = 0;
        foreach ($combinedTasks as $task) {
            if ($task['task_is_personal'] == 1) {
                $personalCount++;
            }
        }
        
        if ($personalCount > 0) {
            echo "<div class='success'>🎯 <strong>RESULTADO:</strong> {$personalCount} tareas personales que mostrarán 'Personal' como proyecto</div>";
        }
    }
    
    echo "<h2>4️⃣ IMPLEMENTACIÓN PARA EL DASHBOARD</h2>";
    
    echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 5px; font-family: monospace;'>";
    echo "<strong>Consulta SQL que debes usar en el dashboard:</strong><br><br>";
    echo htmlspecialchars("
SELECT 
    t.task_id,
    t.task_name,
    t.status,
    t.is_personal,
    t.due_date,
    t.priority,
    t.completion_percentage,
    p.project_id,
    p.project_name,
    p.clan_id,
    -- AQUÍ LA CLAVE: Si is_personal = 1, mostrar 'Personal'
    CASE 
        WHEN t.is_personal = 1 THEN 'Personal'
        ELSE p.project_name
    END AS display_project_name,
    DATEDIFF(t.due_date, CURDATE()) as days_until_due
FROM Tasks t
LEFT JOIN Projects p ON t.project_id = p.project_id
WHERE t.assigned_to_user_id = ?
    AND (t.is_subtask = 0 OR t.is_subtask IS NULL)
ORDER BY 
    CASE WHEN t.status != 'completed' AND DATEDIFF(t.due_date, CURDATE()) < 0 THEN 0 ELSE 1 END,
    CASE t.priority 
        WHEN 'critical' THEN 1
        WHEN 'high' THEN 2
        WHEN 'medium' THEN 3
        WHEN 'low' THEN 4
        ELSE 5
    END,
    t.due_date ASC
    ");
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ ERROR: " . $e->getMessage() . "</div>";
}

echo "
    </div>
</body>
</html>";
?>
