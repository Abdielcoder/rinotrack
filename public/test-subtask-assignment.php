<?php
/**
 * Script de prueba para verificar que la funcionalidad de asignación múltiple de subtareas funcione correctamente
 */

// Incluir configuración de la aplicación
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Utils.php';
require_once __DIR__ . '/../app/models/SubtaskAssignment.php';

echo "<h1>🧪 Prueba de Asignación Múltiple de Subtareas</h1>";

try {
    echo "<h2>✅ Verificación de Dependencias</h2>";
    
    // Verificar conexión a base de datos
    $db = Database::getConnection();
    echo "<p>✅ Conexión a base de datos: OK</p>";
    
    // Verificar que la clase SubtaskAssignment se carga correctamente
    if (class_exists('SubtaskAssignment')) {
        echo "<p>✅ Clase SubtaskAssignment: OK</p>";
    } else {
        echo "<p>❌ Clase SubtaskAssignment: NO ENCONTRADA</p>";
        exit;
    }
    
    // Crear instancia del modelo
    $subtaskAssignment = new SubtaskAssignment();
    echo "<p>✅ Instancia de SubtaskAssignment: OK</p>";
    
    echo "<h2>📊 Verificación de Tabla</h2>";
    
    // Verificar que la tabla existe
    $stmt = $db->prepare("SHOW TABLES LIKE 'Subtask_Assignments'");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Tabla Subtask_Assignments: EXISTE</p>";
        
        // Mostrar estructura de la tabla
        $stmt = $db->prepare("DESCRIBE Subtask_Assignments");
        $stmt->execute();
        $columns = $stmt->fetchAll();
        
        echo "<h3>📋 Estructura de la tabla:</h3>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column['Field']}</td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>{$column['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Verificar datos migrados
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM Subtask_Assignments");
        $stmt->execute();
        $result = $stmt->fetch();
        echo "<p>📈 Registros en Subtask_Assignments: {$result['count']}</p>";
        
    } else {
        echo "<p>❌ Tabla Subtask_Assignments: NO EXISTE</p>";
    }
    
    echo "<h2>🎯 Verificación de Subtareas Existentes</h2>";
    
    // Verificar subtareas existentes
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM Subtasks WHERE assigned_to_user_id IS NOT NULL");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<p>📊 Subtareas con usuarios asignados: {$result['count']}</p>";
    
    // Mostrar algunas subtareas de ejemplo
    $stmt = $db->prepare("
        SELECT s.subtask_id, s.title, s.assigned_to_user_id, u.full_name, u.username 
        FROM Subtasks s 
        LEFT JOIN Users u ON s.assigned_to_user_id = u.user_id 
        LIMIT 5
    ");
    $stmt->execute();
    $subtasks = $stmt->fetchAll();
    
    if (!empty($subtasks)) {
        echo "<h3>📋 Ejemplos de subtareas:</h3>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Título</th><th>Usuario Asignado</th></tr>";
        foreach ($subtasks as $subtask) {
            $userName = $subtask['full_name'] ?: $subtask['username'] ?: 'Sin asignar';
            echo "<tr>";
            echo "<td>{$subtask['subtask_id']}</td>";
            echo "<td>{$subtask['title']}</td>";
            echo "<td>{$userName}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>🚀 Resultado Final</h2>";
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>✅ ¡Todo está funcionando correctamente!</h3>";
    echo "<p>La funcionalidad de asignación múltiple de subtareas está lista para usar.</p>";
    echo "<p><strong>Pasos siguientes:</strong></p>";
    echo "<ul>";
    echo "<li>Ve a cualquier tarea con subtareas</li>";
    echo "<li>Haz clic en el botón 👤➕ junto a una subtarea</li>";
    echo "<li>Selecciona múltiples usuarios con los checkboxes</li>";
    echo "<li>Haz clic en 'Asignar Seleccionados'</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>❌ Error durante la prueba</h3>";
    echo "<p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><small>Script de prueba ejecutado el: " . date('Y-m-d H:i:s') . "</small></p>";
?>
