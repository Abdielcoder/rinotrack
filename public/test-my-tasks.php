<?php
/**
 * Script de prueba para verificar las consultas de "Mis Tareas"
 */

// Incluir configuraciones
require_once __DIR__ . '/../config/database.php';

// Datos de prueba
$userId = 2;
$clanId = 5;

try {
    $db = Database::getInstance();
    
    echo "<h2>Prueba de consultas para Mis Tareas</h2>";
    echo "<p>User ID: $userId</p>";
    echo "<p>Clan ID: $clanId</p>";
    echo "<hr>";
    
    // ============================================
    // CONSULTA 1: Solo tareas asignadas directamente
    // ============================================
    echo "<h3>1. Tareas asignadas directamente (assigned_to_user_id = $userId)</h3>";
    
    $sql1 = "
        SELECT 
            t.task_id,
            t.task_name,
            t.assigned_to_user_id,
            t.created_by_user_id,
            t.status,
            t.due_date,
            p.project_name,
            p.clan_id
        FROM Tasks t
        LEFT JOIN Projects p ON t.project_id = p.project_id
        WHERE t.assigned_to_user_id = ?
            AND (t.is_subtask = 0 OR t.is_subtask IS NULL)
        ORDER BY t.task_id DESC
        LIMIT 20
    ";
    
    $stmt1 = $db->prepare($sql1);
    $stmt1->execute([$userId]);
    $tasks1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Total encontradas: " . count($tasks1) . "</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Proyecto</th><th>Asignado a</th><th>Creado por</th><th>Estado</th><th>Vencimiento</th></tr>";
    foreach ($tasks1 as $task) {
        echo "<tr>";
        echo "<td>{$task['task_id']}</td>";
        echo "<td>{$task['task_name']}</td>";
        echo "<td>{$task['project_name']}</td>";
        echo "<td>{$task['assigned_to_user_id']}</td>";
        echo "<td>{$task['created_by_user_id']}</td>";
        echo "<td>{$task['status']}</td>";
        echo "<td>{$task['due_date']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // ============================================
    // CONSULTA 2: Subtareas asignadas directamente
    // ============================================
    echo "<h3>2. Subtareas asignadas directamente (assigned_to_user_id = $userId)</h3>";
    
    $sql2 = "
        SELECT 
            s.subtask_id,
            s.title as subtask_name,
            s.assigned_to_user_id,
            s.status,
            s.due_date,
            t.task_name as parent_task,
            p.project_name
        FROM Subtasks s
        JOIN Tasks t ON s.task_id = t.task_id
        JOIN Projects p ON t.project_id = p.project_id
        WHERE s.assigned_to_user_id = ?
        LIMIT 20
    ";
    
    $stmt2 = $db->prepare($sql2);
    $stmt2->execute([$userId]);
    $subtasks = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Total encontradas: " . count($subtasks) . "</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Tarea Padre</th><th>Proyecto</th><th>Asignado a</th><th>Estado</th><th>Vencimiento</th></tr>";
    foreach ($subtasks as $task) {
        echo "<tr>";
        echo "<td>{$task['subtask_id']}</td>";
        echo "<td>{$task['subtask_name']}</td>";
        echo "<td>{$task['parent_task']}</td>";
        echo "<td>{$task['project_name']}</td>";
        echo "<td>{$task['assigned_to_user_id']}</td>";
        echo "<td>{$task['status']}</td>";
        echo "<td>{$task['due_date']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // ============================================
    // CONSULTA 3: Verificar Task_Assignments (para comparación)
    // ============================================
    echo "<h3>3. Task_Assignments para user_id = $userId (para comparación)</h3>";
    
    $sql3 = "
        SELECT 
            ta.task_id,
            ta.user_id,
            t.task_name,
            t.assigned_to_user_id as primary_assigned,
            p.project_name
        FROM Task_Assignments ta
        JOIN Tasks t ON ta.task_id = t.task_id
        JOIN Projects p ON t.project_id = p.project_id
        WHERE ta.user_id = ?
        LIMIT 20
    ";
    
    $stmt3 = $db->prepare($sql3);
    $stmt3->execute([$userId]);
    $taskAssignments = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Total en Task_Assignments: " . count($taskAssignments) . "</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Task ID</th><th>Nombre</th><th>Proyecto</th><th>Asignado Principal</th><th>En Task_Assignments</th></tr>";
    foreach ($taskAssignments as $task) {
        echo "<tr>";
        echo "<td>{$task['task_id']}</td>";
        echo "<td>{$task['task_name']}</td>";
        echo "<td>{$task['project_name']}</td>";
        echo "<td>{$task['primary_assigned']}</td>";
        echo "<td>{$task['user_id']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>
