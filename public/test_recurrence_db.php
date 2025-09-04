<?php
// Configuración de conexión para MAMP
$host = 'localhost';
$port = '8889'; // Puerto MySQL de MAMP
$dbname = 'rinotrack';
$username = 'root';
$password = 'root';

try {
    // Conexión usando el socket de MAMP
    $dsn = "mysql:unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conexión exitosa a la base de datos\n\n";
    
    // Verificar estructura actual de la tabla Tasks
    echo "📋 Verificando estructura actual de la tabla Tasks...\n";
    $stmt = $pdo->query("DESCRIBE Tasks");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Columnas actuales: " . implode(', ', $columns) . "\n\n";
    
    // Verificar si los campos de recurrencia existen
    $recurrenceFields = ['is_recurrent', 'recurrence_type', 'recurrence_start_date', 'recurrence_end_date', 'last_generated_date', 'parent_recurrent_task_id'];
    $missingFields = array_diff($recurrenceFields, $columns);
    
    if (empty($missingFields)) {
        echo "✅ Todos los campos de recurrencia ya existen.\n";
    } else {
        echo "⚠️ Campos faltantes: " . implode(', ', $missingFields) . "\n\n";
        echo "🔧 Agregando campos de recurrencia...\n";
        
        // Agregar campos faltantes
        $alterQueries = [];
        
        if (!in_array('is_recurrent', $columns)) {
            $alterQueries[] = "ADD COLUMN `is_recurrent` TINYINT(1) DEFAULT 0";
        }
        
        if (!in_array('recurrence_type', $columns)) {
            $alterQueries[] = "ADD COLUMN `recurrence_type` ENUM('daily', 'weekly', 'monthly') DEFAULT NULL";
        }
        
        if (!in_array('recurrence_start_date', $columns)) {
            $alterQueries[] = "ADD COLUMN `recurrence_start_date` DATE DEFAULT NULL";
        }
        
        if (!in_array('recurrence_end_date', $columns)) {
            $alterQueries[] = "ADD COLUMN `recurrence_end_date` DATE DEFAULT NULL";
        }
        
        if (!in_array('last_generated_date', $columns)) {
            $alterQueries[] = "ADD COLUMN `last_generated_date` DATE DEFAULT NULL";
        }
        
        if (!in_array('parent_recurrent_task_id', $columns)) {
            $alterQueries[] = "ADD COLUMN `parent_recurrent_task_id` INT(11) DEFAULT NULL";
        }
        
        if (!empty($alterQueries)) {
            $sql = "ALTER TABLE Tasks " . implode(", ", $alterQueries);
            echo "Ejecutando: $sql\n";
            $pdo->exec($sql);
            echo "✅ Campos agregados exitosamente.\n\n";
            
            // Agregar índice si no existe
            try {
                $pdo->exec("CREATE INDEX idx_parent_recurrent ON Tasks(parent_recurrent_task_id)");
                echo "✅ Índice idx_parent_recurrent creado.\n";
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
                    echo "ℹ️ El índice idx_parent_recurrent ya existe.\n";
                } else {
                    echo "⚠️ Error al crear índice: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    // Verificar estructura final
    echo "\n📋 Estructura final de la tabla Tasks:\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM Tasks WHERE Field IN ('is_recurrent', 'recurrence_type', 'recurrence_start_date', 'recurrence_end_date', 'last_generated_date', 'parent_recurrent_task_id')");
    $finalColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($finalColumns as $col) {
        echo "  - {$col['Field']}: {$col['Type']} (Default: " . ($col['Default'] ?? 'NULL') . ")\n";
    }
    
    echo "\n✅ Script completado exitosamente.\n";
    
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "\n";
    echo "Intentando con configuración alternativa...\n";
    
    // Intento alternativo con localhost:8889
    try {
        $dsn = "mysql:host=127.0.0.1;port=8889;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);
        echo "✅ Conexión exitosa con puerto 8889\n";
    } catch (PDOException $e2) {
        echo "❌ Error: " . $e2->getMessage() . "\n";
    }
}
?>
