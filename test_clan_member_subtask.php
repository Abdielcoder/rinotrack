<?php
// Archivo de prueba para verificar actualización de subtareas del clan member
require_once __DIR__ . '/app/bootstrap.php';

echo "<h2>Test: Actualización de Subtareas - Clan Member</h2>";

// Simular una actualización de estado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subtaskId = (int)($_POST['subtask_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $completionPercentage = isset($_POST['completion_percentage']) ? (float)$_POST['completion_percentage'] : null;
    
    echo "<h3>Datos recibidos:</h3>";
    echo "Subtask ID: " . $subtaskId . "<br>";
    echo "Status: " . $status . "<br>";
    echo "Completion Percentage: " . $completionPercentage . "<br>";
    
    // Verificar si la subtarea existe
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT s.*, t.project_id, p.clan_id
            FROM Subtasks s
            JOIN Tasks t ON s.task_id = t.task_id
            JOIN Projects p ON t.project_id = p.project_id
            WHERE s.subtask_id = ?
        ");
        $stmt->execute([$subtaskId]);
        $subtask = $stmt->fetch();
        
        if ($subtask) {
            echo "<h3>Subtarea encontrada:</h3>";
            echo "ID: " . $subtask['subtask_id'] . "<br>";
            echo "Título: " . htmlspecialchars($subtask['title']) . "<br>";
            echo "Estado actual: " . $subtask['status'] . "<br>";
            echo "Porcentaje actual: " . $subtask['completion_percentage'] . "%<br>";
            echo "Proyecto ID: " . $subtask['project_id'] . "<br>";
            echo "Clan ID: " . $subtask['clan_id'] . "<br>";
            
            // Intentar actualizar
            $task = new Task();
            $result = $task->updateSubtaskStatus($subtaskId, $status, $completionPercentage);
            
            if ($result) {
                echo "<div style='color: green;'><strong>✅ Actualización exitosa!</strong></div>";
                
                // Verificar el cambio
                $stmt->execute([$subtaskId]);
                $updatedSubtask = $stmt->fetch();
                echo "Nuevo estado: " . $updatedSubtask['status'] . "<br>";
                echo "Nuevo porcentaje: " . $updatedSubtask['completion_percentage'] . "%<br>";
            } else {
                echo "<div style='color: red;'><strong>❌ Error en la actualización</strong></div>";
            }
        } else {
            echo "<div style='color: red;'>❌ Subtarea no encontrada</div>";
        }
    } catch (Exception $e) {
        echo "<div style='color: red;'>❌ Error: " . $e->getMessage() . "</div>";
    }
}

// Obtener subtareas de ejemplo
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        SELECT s.*, t.project_id, p.clan_id, p.project_name
        FROM Subtasks s
        JOIN Tasks t ON s.task_id = t.task_id
        JOIN Projects p ON t.project_id = p.project_id
        LIMIT 5
    ");
    $stmt->execute();
    $subtasks = $stmt->fetchAll();
    
    echo "<h3>Subtareas disponibles para prueba:</h3>";
    foreach ($subtasks as $subtask) {
        echo "<div style='margin-bottom: 10px; padding: 10px; border: 1px solid #ccc;'>";
        echo "<strong>ID: " . $subtask['subtask_id'] . "</strong><br>";
        echo "Título: " . htmlspecialchars($subtask['title']) . "<br>";
        echo "Estado: " . $subtask['status'] . "<br>";
        echo "Porcentaje: " . $subtask['completion_percentage'] . "%<br>";
        echo "Proyecto: " . htmlspecialchars($subtask['project_name']) . "<br>";
        
        echo "<form method='POST' style='margin-top: 10px;'>";
        echo "<input type='hidden' name='subtask_id' value='" . $subtask['subtask_id'] . "'>";
        echo "<label>Nuevo estado: ";
        echo "<select name='status'>";
        echo "<option value='pending'>Pendiente</option>";
        echo "<option value='in_progress'>En Progreso</option>";
        echo "<option value='completed'>Completada</option>";
        echo "</select>";
        echo "</label>";
        echo "<button type='submit'>Actualizar</button>";
        echo "</form>";
        echo "</div>";
    }
} catch (Exception $e) {
    echo "<div style='color: red;'>Error al obtener subtareas: " . $e->getMessage() . "</div>";
}
?>
