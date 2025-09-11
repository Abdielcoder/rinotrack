<?php
/**
 * Script de prueba para verificar la consulta de tareas personales
 * Muestra todas las tareas del usuario con manejo especial de tareas personales
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
    <title>Test - Tareas Personales</title>
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
            color: #333; 
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        h2 { 
            color: #555; 
            margin-top: 30px;
            background: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #007bff;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }
        th { 
            background: #007bff; 
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
        .personal-badge {
            background: #6c757d;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .project-badge {
            background: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending { color: #ffc107; font-weight: bold; }
        .status-in_progress { color: #17a2b8; font-weight: bold; }
        .status-completed { color: #28a745; font-weight: bold; }
        .priority-high { color: #dc3545; font-weight: bold; }
        .priority-medium { color: #ffc107; font-weight: bold; }
        .priority-low { color: #6c757d; font-weight: bold; }
        .overdue { background: #ffebee !important; }
        .stats-box {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🧪 Test de Consulta de Tareas con Manejo de Tareas Personales</h1>
        <p><strong>Usuario:</strong> {$currentUser['full_name']} (ID: {$userId})</p>
        <p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";

try {
    // Crear instancia del modelo Task
    $taskModel = new Task();
    
    // Obtener todas las tareas del usuario usando la nueva función
    $result = $taskModel->getAllUserTasksForDashboard($userId);
    
    if ($result['success']) {
        $tasks = $result['tasks'];
        $stats = $result['stats'];
        $tasksByProject = $result['tasks_by_project'];
        
        // Mostrar estadísticas
        echo "<h2>📊 Estadísticas Generales</h2>";
        echo "<div class='stats-box'>";
        echo "<div class='stat-card'><div class='stat-value'>{$stats['total_tasks']}</div><div class='stat-label'>Total de Tareas</div></div>";
        echo "<div class='stat-card'><div class='stat-value'>{$stats['personal_tasks']}</div><div class='stat-label'>Tareas Personales</div></div>";
        echo "<div class='stat-card'><div class='stat-value'>{$stats['project_tasks']}</div><div class='stat-label'>Tareas de Proyecto</div></div>";
        echo "<div class='stat-card'><div class='stat-value'>{$stats['completed_tasks']}</div><div class='stat-label'>Completadas</div></div>";
        echo "<div class='stat-card'><div class='stat-value'>{$stats['pending_tasks']}</div><div class='stat-label'>Pendientes</div></div>";
        echo "<div class='stat-card'><div class='stat-value'>{$stats['in_progress_tasks']}</div><div class='stat-label'>En Progreso</div></div>";
        echo "<div class='stat-card'><div class='stat-value'>{$stats['overdue_tasks']}</div><div class='stat-label'>Vencidas</div></div>";
        echo "</div>";
        
        // Mostrar resumen por proyecto
        echo "<h2>📁 Tareas por Proyecto</h2>";
        echo "<table>";
        echo "<tr><th>Proyecto</th><th>Clan</th><th>Cantidad de Tareas</th></tr>";
        foreach ($tasksByProject as $project) {
            $projectName = htmlspecialchars($project['project_name']);
            $clanName = htmlspecialchars($project['clan_name'] ?? 'N/A');
            $taskCount = count($project['tasks']);
            echo "<tr>";
            echo "<td>{$projectName}</td>";
            echo "<td>{$clanName}</td>";
            echo "<td>{$taskCount}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Mostrar todas las tareas
        echo "<h2>📋 Listado Completo de Tareas</h2>";
        echo "<div class='success'>Se encontraron " . count($tasks) . " tareas en total</div>";
        
        if (count($tasks) > 0) {
            echo "<table>";
            echo "<tr>
                    <th>ID</th>
                    <th>Tarea</th>
                    <th>Proyecto</th>
                    <th>Tipo</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Fecha Vencimiento</th>
                    <th>Días Restantes</th>
                    <th>Urgencia</th>
                    <th>Es Personal</th>
                  </tr>";
            
            foreach ($tasks as $task) {
                $rowClass = $task['urgency_status'] == 'overdue' ? 'overdue' : '';
                $isPersonal = $task['is_personal'] == 1;
                $projectDisplay = htmlspecialchars($task['project_name']);
                
                echo "<tr class='{$rowClass}'>";
                echo "<td>{$task['task_id']}</td>";
                echo "<td>" . htmlspecialchars($task['task_name']) . "</td>";
                echo "<td>{$projectDisplay}</td>";
                echo "<td>";
                if ($isPersonal || $task['project_name'] == 'Personal') {
                    echo "<span class='personal-badge'>PERSONAL</span>";
                } else {
                    echo "<span class='project-badge'>PROYECTO</span>";
                }
                echo "</td>";
                echo "<td class='priority-{$task['priority']}'>{$task['priority']}</td>";
                echo "<td class='status-{$task['status']}'>{$task['status']}</td>";
                echo "<td>" . ($task['due_date'] ?? 'Sin fecha') . "</td>";
                echo "<td>{$task['days_until_due']}</td>";
                echo "<td>{$task['urgency_status']}</td>";
                echo "<td>" . ($isPersonal ? 'Sí' : 'No') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Mostrar tareas que son personales (is_personal = 1)
            $personalTasks = array_filter($tasks, function($task) {
                return $task['is_personal'] == 1;
            });
            
            if (count($personalTasks) > 0) {
                echo "<h2>👤 Tareas Marcadas como Personales (is_personal = 1)</h2>";
                echo "<div class='success'>Se encontraron " . count($personalTasks) . " tareas personales</div>";
                echo "<table>";
                echo "<tr><th>ID</th><th>Tarea</th><th>Proyecto Mostrado</th><th>Estado</th></tr>";
                foreach ($personalTasks as $task) {
                    echo "<tr>";
                    echo "<td>{$task['task_id']}</td>";
                    echo "<td>" . htmlspecialchars($task['task_name']) . "</td>";
                    echo "<td><strong>" . htmlspecialchars($task['project_name']) . "</strong></td>";
                    echo "<td class='status-{$task['status']}'>{$task['status']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            
        } else {
            echo "<p>No se encontraron tareas para este usuario.</p>";
        }
        
    } else {
        echo "<div class='error'>Error al obtener las tareas: " . ($result['error'] ?? 'Error desconocido') . "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
}

echo "
    </div>
</body>
</html>";
