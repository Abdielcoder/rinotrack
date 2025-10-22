<?php
/**
 * Script para agregar 'quarterly' (trimestral) a la columna recurrence_type
 * Ejecutar una sola vez para actualizar la base de datos
 */

require_once __DIR__ . '/../app/bootstrap.php';

echo "<h2>Agregando opción 'quarterly' (trimestral) a recurrencia</h2>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Verificar la estructura actual de la columna
    $stmt = $db->prepare("SHOW COLUMNS FROM Tasks WHERE Field = 'recurrence_type'");
    $stmt->execute();
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($column) {
        echo "<p>Tipo actual de columna: {$column['Type']}</p>";
        
        // Verificar si 'quarterly' ya está incluido
        if (strpos($column['Type'], 'quarterly') === false) {
            // Actualizar el ENUM para incluir 'quarterly'
            $alterQuery = "ALTER TABLE Tasks MODIFY COLUMN recurrence_type 
                          ENUM('daily', 'weekly', 'monthly', 'quarterly') DEFAULT NULL 
                          COMMENT 'Tipo de recurrencia: diaria, semanal, mensual, trimestral'";
            
            $db->exec($alterQuery);
            echo "<p style='color: green;'>✅ Columna actualizada exitosamente. Se agregó 'quarterly' (trimestral) a las opciones de recurrencia.</p>";
            
            // Verificar el cambio
            $stmt = $db->prepare("SHOW COLUMNS FROM Tasks WHERE Field = 'recurrence_type'");
            $stmt->execute();
            $newColumn = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p>Nuevo tipo de columna: {$newColumn['Type']}</p>";
        } else {
            echo "<p style='color: blue;'>ℹ️ La columna ya incluye 'quarterly' en sus opciones.</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ La columna recurrence_type no existe en la tabla Tasks.</p>";
    }
    
    // Mostrar estadísticas de tareas recurrentes
    echo "<h3>Estadísticas de Tareas Recurrentes</h3>";
    $stmt = $db->prepare("
        SELECT 
            recurrence_type, 
            COUNT(*) as total 
        FROM Tasks 
        WHERE is_recurrent = 1 
        GROUP BY recurrence_type
    ");
    $stmt->execute();
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($stats) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Tipo de Recurrencia</th><th>Cantidad</th></tr>";
        foreach ($stats as $stat) {
            $tipo = $stat['recurrence_type'] ?? 'Sin tipo';
            $tipoEspañol = [
                'daily' => 'Diaria',
                'weekly' => 'Semanal',
                'monthly' => 'Mensual',
                'quarterly' => 'Trimestral'
            ][$tipo] ?? $tipo;
            echo "<tr><td>{$tipoEspañol}</td><td>{$stat['total']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay tareas recurrentes en el sistema.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>Volver al inicio</a></p>";
?>
