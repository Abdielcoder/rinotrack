<?php
// Archivo de prueba para debuggear subtareas
require_once __DIR__ . '/app/bootstrap.php';

echo "<h2>Debug: Subtareas de Tarea 247</h2>";

try {
    $taskModel = new Task();
    
    echo "<h3>1. Verificando si la tarea existe:</h3>";
    $task = $taskModel->findById(247);
    if ($task) {
        echo "✅ Tarea encontrada: <strong>" . htmlspecialchars($task['task_name']) . "</strong><br>";
        echo "ID: " . $task['task_id'] . "<br>";
        echo "Proyecto ID: " . $task['project_id'] . "<br>";
        echo "Estado: " . $task['status'] . "<br>";
    } else {
        echo "❌ Tarea NO encontrada<br>";
        exit;
    }
    
    echo "<h3>2. Verificando método getSubtasks:</h3>";
    
    // Verificar que el método existe
    if (method_exists($taskModel, 'getSubtasks')) {
        echo "✅ Método getSubtasks existe<br>";
    } else {
        echo "❌ Método getSubtasks NO existe<br>";
        exit;
    }
    
    echo "<h3>3. Obteniendo subtareas:</h3>";
    $subtasks = $taskModel->getSubtasks(247);
    
    if (is_array($subtasks)) {
        echo "✅ getSubtasks retornó un array con " . count($subtasks) . " elementos<br>";
        
        if (empty($subtasks)) {
            echo "⚠️ El array está vacío - no hay subtareas para esta tarea<br>";
        } else {
            echo "<h4>Subtareas encontradas:</h4>";
            foreach ($subtasks as $index => $subtask) {
                echo "<div style='margin: 10px; padding: 10px; border: 1px solid #ccc;'>";
                echo "<strong>Subtarea " . ($index + 1) . ":</strong><br>";
                echo "ID: " . $subtask['subtask_id'] . "<br>";
                echo "Título: " . htmlspecialchars($subtask['title']) . "<br>";
                echo "Estado: " . $subtask['status'] . "<br>";
                echo "Porcentaje: " . $subtask['completion_percentage'] . "%<br>";
                echo "</div>";
            }
        }
    } else {
        echo "❌ getSubtasks NO retornó un array. Tipo: " . gettype($subtasks) . "<br>";
        echo "Valor: " . var_export($subtasks, true) . "<br>";
    }
    
    echo "<h3>4. Verificando base de datos directamente:</h3>";
    
    // Consulta directa a la base de datos
    $db = new Database();
    $sql = "SELECT * FROM Subtasks WHERE task_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([247]);
    $directSubtasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Consulta directa a Subtasks WHERE task_id = 247:<br>";
    if (empty($directSubtasks)) {
        echo "⚠️ No hay subtareas en la tabla Subtasks para task_id = 247<br>";
    } else {
        echo "✅ Encontradas " . count($directSubtasks) . " subtareas en la tabla:<br>";
        foreach ($directSubtasks as $subtask) {
            echo "- ID: " . $subtask['subtask_id'] . ", Título: " . htmlspecialchars($subtask['title']) . "<br>";
        }
    }
    
    echo "<h3>5. Verificando vista v_subtasks_complete:</h3>";
    
    // Verificar si existe la vista
    $sql = "SHOW TABLES LIKE 'v_subtasks_complete'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $viewExists = $stmt->fetch();
    
    if ($viewExists) {
        echo "✅ Vista v_subtasks_complete existe<br>";
        
        // Consultar la vista
        $sql = "SELECT * FROM v_subtasks_complete WHERE task_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([247]);
        $viewSubtasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($viewSubtasks)) {
            echo "⚠️ Vista v_subtasks_complete no retorna subtareas para task_id = 247<br>";
        } else {
            echo "✅ Vista retorna " . count($viewSubtasks) . " subtareas:<br>";
            foreach ($viewSubtasks as $subtask) {
                echo "- ID: " . $subtask['subtask_id'] . ", Título: " . htmlspecialchars($subtask['title']) . "<br>";
            }
        }
    } else {
        echo "❌ Vista v_subtasks_complete NO existe<br>";
    }
    
    echo "<h3>6. Resumen del problema:</h3>";
    
    if (empty($subtasks) && empty($directSubtasks)) {
        echo "🔍 <strong>PROBLEMA IDENTIFICADO:</strong> La tarea 247 no tiene subtareas en la base de datos<br>";
        echo "💡 <strong>SOLUCIÓN:</strong> Necesitas crear subtareas para esta tarea o verificar que estés en la tarea correcta<br>";
    } elseif (empty($subtasks) && !empty($directSubtasks)) {
        echo "🔍 <strong>PROBLEMA IDENTIFICADO:</strong> Hay subtareas en la base de datos pero getSubtasks() no las retorna<br>";
        echo "💡 <strong>SOLUCIÓN:</strong> Revisar el método getSubtasks() en el modelo Task<br>";
    } else {
        echo "✅ <strong>NO HAY PROBLEMA:</strong> Las subtareas se están obteniendo correctamente<br>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
    echo "<div style='color: red;'>Stack trace: " . $e->getTraceAsString() . "</div>";
}
?>
