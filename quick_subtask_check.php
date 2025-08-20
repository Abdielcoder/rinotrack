<?php
// Verificación rápida de subtareas
require_once __DIR__ . '/app/bootstrap.php';

echo "<h2>Verificación Rápida: Subtareas de Tarea 247</h2>";

try {
    $db = new Database();
    
    // Verificar si la tarea existe
    $stmt = $db->prepare("SELECT task_id, task_name FROM Tasks WHERE task_id = ?");
    $stmt->execute([247]);
    $task = $stmt->fetch();
    
    if ($task) {
        echo "✅ Tarea encontrada: <strong>" . htmlspecialchars($task['task_name']) . "</strong><br><br>";
        
        // Verificar subtareas directamente
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM Subtasks WHERE task_id = ?");
        $stmt->execute([247]);
        $result = $stmt->fetch();
        
        echo "📊 <strong>Total de subtareas:</strong> " . $result['count'] . "<br><br>";
        
        if ($result['count'] > 0) {
            // Mostrar las subtareas
            $stmt = $db->prepare("SELECT * FROM Subtasks WHERE task_id = ? ORDER BY subtask_order");
            $stmt->execute([247]);
            $subtasks = $stmt->fetchAll();
            
            echo "<h3>Subtareas encontradas:</h3>";
            foreach ($subtasks as $subtask) {
                echo "<div style='margin: 10px; padding: 10px; border: 1px solid #ccc;'>";
                echo "<strong>ID:</strong> " . $subtask['subtask_id'] . "<br>";
                echo "<strong>Título:</strong> " . htmlspecialchars($subtask['title']) . "<br>";
                echo "<strong>Estado:</strong> " . $subtask['status'] . "<br>";
                echo "<strong>Porcentaje:</strong> " . $subtask['completion_percentage'] . "%<br>";
                echo "</div>";
            }
        } else {
            echo "⚠️ <strong>NO HAY SUBTAREAS</strong> para esta tarea<br>";
            echo "💡 <strong>SOLUCIÓN:</strong> Necesitas crear subtareas para la tarea 247<br>";
        }
        
    } else {
        echo "❌ Tarea 247 NO encontrada<br>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
}
?>
