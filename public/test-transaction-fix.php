<?php
/**
 * Script de prueba para verificar que el problema de transacciones se ha solucionado
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

echo "<h1>Prueba de Solución de Transacciones</h1>";
echo "<p>Usuario actual ID: {$userId}</p>";
echo "<hr>";

try {
    // Crear instancia del modelo Task
    $taskModel = new Task();
    
    // Datos de prueba mínimos
    $projectId = 1; // Cambiar según tu proyecto de prueba
    $taskTitle = "Tarea de Prueba Transacciones - " . date('Y-m-d H:i:s');
    $taskDescription = "Prueba de transacciones";
    $dueDate = date('Y-m-d', strtotime('+7 days'));
    $clanId = 1; // Cambiar según tu clan
    $priority = 'medium';
    
    // Usuarios que se asignarán a la tarea principal
    $assignedUsers = [2, 4]; // Cambiar según los IDs de usuarios en tu sistema
    
    // Una sola subtarea de prueba
    $subtasks = [
        [
            'title' => 'Subtarea de Prueba',
            'description' => 'Prueba de transacciones',
            'completion_percentage' => 0,
            'due_date' => date('Y-m-d', strtotime('+3 days')),
            'priority' => 'medium'
        ]
    ];
    
    echo "<h2>1. Creando tarea con subtarea...</h2>";
    echo "<p><strong>Título:</strong> {$taskTitle}</p>";
    echo "<p><strong>Usuarios asignados:</strong> " . implode(', ', $assignedUsers) . "</p>";
    echo "<p><strong>Subtareas:</strong> " . count($subtasks) . "</p>";
    
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
        echo "<div style='padding: 10px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>✅ ÉXITO:</strong> Tarea creada exitosamente con ID: {$taskId}";
        echo "</div>";
        
        echo "<h2>2. Verificando asignaciones...</h2>";
        
        // Verificar que la subtarea fue creada
        $stmt = $taskModel->getDb()->prepare("
            SELECT subtask_id, title 
            FROM Subtasks 
            WHERE task_id = ? 
            ORDER BY subtask_order
        ");
        $stmt->execute([$taskId]);
        $createdSubtasks = $stmt->fetchAll();
        
        if (!empty($createdSubtasks)) {
            $subtaskAssignmentModel = new SubtaskAssignment();
            $subtask = $createdSubtasks[0];
            $assignedToSubtask = $subtaskAssignmentModel->getAssignedUsers($subtask['subtask_id']);
            $assignedUserIds = array_column($assignedToSubtask, 'user_id');
            
            echo "<p><strong>Subtarea creada:</strong> {$subtask['title']} (ID: {$subtask['subtask_id']})</p>";
            echo "<p><strong>Usuarios asignados a la subtarea:</strong> " . implode(', ', $assignedUserIds) . "</p>";
            
            // Verificar si todos los usuarios de la tarea principal están asignados a la subtarea
            $missingUsers = array_diff($assignedUsers, $assignedUserIds);
            
            if (empty($missingUsers)) {
                echo "<div style='padding: 10px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0;'>";
                echo "<strong>✅ PERFECTO:</strong> La subtarea fue asignada correctamente a todos los colaboradores de la tarea principal.";
                echo "</div>";
            } else {
                echo "<div style='padding: 10px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
                echo "<strong>⚠️ PARCIAL:</strong> Faltan usuarios: " . implode(', ', $missingUsers);
                echo "</div>";
            }
        } else {
            echo "<div style='padding: 10px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
            echo "<strong>❌ ERROR:</strong> No se encontraron subtareas creadas";
            echo "</div>";
        }
        
    } else {
        echo "<div style='padding: 10px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>❌ ERROR:</strong> No se pudo crear la tarea";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='padding: 10px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>❌ EXCEPCIÓN:</strong> " . $e->getMessage();
    echo "<br><br><strong>Stack trace:</strong>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 3px; font-size: 12px;'>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='index.php'>Volver al inicio</a> | <a href='test-subtask-multiple-assignment.php'>Prueba completa</a></p>";
?>
