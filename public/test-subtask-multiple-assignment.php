<?php
/**
 * Script de prueba para verificar la asignación múltiple de subtareas
 * cuando se crea una tarea con subtareas
 */

session_start();
require_once '../app/bootstrap.php';
require_once '../app/models/Task.php';
require_once '../app/models/SubtaskAssignment.php';

// Configurar para mostrar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    die("Error: Debes iniciar sesión primero");
}

$userId = $_SESSION['user_id'];

echo "<h1>Prueba de Asignación Múltiple de Subtareas</h1>";
echo "<p>Usuario actual ID: {$userId}</p>";
echo "<hr>";

try {
    // Crear instancia del modelo Task
    $taskModel = new Task();
    $subtaskAssignmentModel = new SubtaskAssignment();
    
    // Datos de prueba
    $projectId = 1; // Cambiar según tu proyecto de prueba
    $taskTitle = "Tarea de Prueba - " . date('Y-m-d H:i:s');
    $taskDescription = "Esta es una tarea de prueba para verificar la asignación múltiple de subtareas";
    $dueDate = date('Y-m-d', strtotime('+7 days'));
    $clanId = 1; // Cambiar según tu clan
    $priority = 'high';
    
    // Usuarios que se asignarán a la tarea principal
    $assignedUsers = [2, 4, 5]; // Cambiar según los IDs de usuarios en tu sistema
    
    echo "<h2>0. Verificando configuración...</h2>";
    echo "<p><strong>Proyecto ID:</strong> {$projectId}</p>";
    echo "<p><strong>Clan ID:</strong> {$clanId}</p>";
    echo "<p><strong>Usuario creador ID:</strong> {$userId}</p>";
    echo "<p><strong>Usuarios a asignar:</strong> " . implode(', ', $assignedUsers) . "</p>";
    
    // Subtareas de prueba
    $subtasks = [
        [
            'title' => 'Subtarea 1 - Investigación',
            'description' => 'Realizar investigación inicial',
            'completion_percentage' => 0,
            'due_date' => date('Y-m-d', strtotime('+3 days')),
            'priority' => 'high'
        ],
        [
            'title' => 'Subtarea 2 - Desarrollo',
            'description' => 'Desarrollar la solución',
            'completion_percentage' => 0,
            'due_date' => date('Y-m-d', strtotime('+5 days')),
            'priority' => 'medium'
        ],
        [
            'title' => 'Subtarea 3 - Pruebas',
            'description' => 'Realizar pruebas de la solución',
            'completion_percentage' => 0,
            'due_date' => date('Y-m-d', strtotime('+6 days')),
            'priority' => 'low'
        ]
    ];
    
    echo "<h2>1. Creando tarea principal con subtareas...</h2>";
    echo "<p><strong>Título:</strong> {$taskTitle}</p>";
    echo "<p><strong>Usuarios asignados:</strong> " . implode(', ', $assignedUsers) . "</p>";
    echo "<p><strong>Número de subtareas:</strong> " . count($subtasks) . "</p>";
    
    // Crear la tarea con subtareas
    $taskId = $taskModel->createAdvanced(
        $projectId,
        $taskTitle,
        $taskDescription,
        $dueDate,
        $clanId,
        $priority,
        $userId,
        $assignedUsers,
        $subtasks,
        [] // labels vacías
    );
    
    if ($taskId) {
        echo "<p style='color: green;'>✅ Tarea creada exitosamente con ID: {$taskId}</p>";
        
        echo "<h2>2. Verificando asignaciones de subtareas...</h2>";
        
        // Obtener las subtareas creadas
        $stmt = $taskModel->getDb()->prepare("
            SELECT subtask_id, title 
            FROM Subtasks 
            WHERE task_id = ? 
            ORDER BY subtask_order
        ");
        $stmt->execute([$taskId]);
        $createdSubtasks = $stmt->fetchAll();
        
        echo "<table border='1' cellpadding='5' cellspacing='0' style='margin: 20px 0;'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Subtarea ID</th>";
        echo "<th>Título</th>";
        echo "<th>Usuarios Asignados</th>";
        echo "<th>Estado</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        $allCorrect = true;
        
        foreach ($createdSubtasks as $subtask) {
            $assignedToSubtask = $subtaskAssignmentModel->getAssignedUsers($subtask['subtask_id']);
            $assignedUserIds = array_column($assignedToSubtask, 'user_id');
            $assignedUserNames = array_column($assignedToSubtask, 'full_name');
            
            // Verificar si todos los usuarios de la tarea principal están asignados a la subtarea
            $missingUsers = array_diff($assignedUsers, $assignedUserIds);
            $status = empty($missingUsers) ? 
                "<span style='color: green;'>✅ Correcto</span>" : 
                "<span style='color: red;'>❌ Faltan usuarios: " . implode(', ', $missingUsers) . "</span>";
            
            if (!empty($missingUsers)) {
                $allCorrect = false;
            }
            
            echo "<tr>";
            echo "<td>{$subtask['subtask_id']}</td>";
            echo "<td>{$subtask['title']}</td>";
            echo "<td>";
            if (!empty($assignedUserNames)) {
                echo implode(', ', $assignedUserNames) . " (IDs: " . implode(', ', $assignedUserIds) . ")";
            } else {
                echo "<span style='color: red;'>Sin asignaciones</span>";
            }
            echo "</td>";
            echo "<td>{$status}</td>";
            echo "</tr>";
        }
        
        echo "</tbody>";
        echo "</table>";
        
        echo "<h2>3. Resultado Final</h2>";
        if ($allCorrect) {
            echo "<div style='padding: 10px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px;'>";
            echo "<strong>✅ ÉXITO:</strong> Todas las subtareas fueron asignadas correctamente a todos los colaboradores de la tarea principal.";
            echo "</div>";
        } else {
            echo "<div style='padding: 10px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;'>";
            echo "<strong>❌ ERROR:</strong> Algunas subtareas no fueron asignadas correctamente a todos los colaboradores.";
            echo "</div>";
        }
        
        // Mostrar información adicional de debug
        echo "<h3>Información de Debug</h3>";
        echo "<details>";
        echo "<summary>Ver detalles técnicos</summary>";
        echo "<pre>";
        echo "Tarea ID: {$taskId}\n";
        echo "Usuarios asignados a tarea principal: " . print_r($assignedUsers, true);
        echo "\nSubtareas creadas:\n";
        foreach ($createdSubtasks as $subtask) {
            echo "  - ID: {$subtask['subtask_id']}, Título: {$subtask['title']}\n";
            $assignments = $subtaskAssignmentModel->getAssignedUsers($subtask['subtask_id']);
            echo "    Asignaciones:\n";
            foreach ($assignments as $assignment) {
                echo "      * Usuario ID: {$assignment['user_id']}, Nombre: {$assignment['full_name']}\n";
            }
        }
        echo "</pre>";
        echo "</details>";
        
    } else {
        echo "<p style='color: red;'>❌ Error al crear la tarea</p>";
    }
    
} catch (Exception $e) {
    echo "<div style='padding: 10px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<strong>Error:</strong> " . $e->getMessage();
    echo "<br><pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='index.php'>Volver al inicio</a></p>";
?>
