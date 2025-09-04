<?php
require_once __DIR__ . '/../app/bootstrap.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Agregar Campos de Recurrencia</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
    </style>
</head>
<body>
    <h1>Agregar Campos de Recurrencia a la Tabla Tasks</h1>
    
    <?php
    try {
        $db = Database::getConnection();
        
        echo "<h2>1. Verificando estructura actual de la tabla Tasks:</h2>";
        $stmt = $db->query("DESCRIBE Tasks");
        $columns = $stmt->fetchAll();
        
        $existingColumns = array_column($columns, 'Field');
        $recurrenceFields = ['is_recurrent', 'recurrence_type', 'recurrence_start_date', 'recurrence_end_date', 'last_generated_date', 'parent_recurrent_task_id'];
        
        echo "<p class='info'>Columnas existentes: " . implode(', ', $existingColumns) . "</p>";
        
        foreach ($recurrenceFields as $field) {
            if (in_array($field, $existingColumns)) {
                echo "<p class='info'>✅ Campo '$field' ya existe</p>";
            } else {
                echo "<p class='error'>❌ Campo '$field' no existe - será agregado</p>";
            }
        }
        
        echo "<h2>2. Agregando campos de recurrencia:</h2>";
        
        // Agregar campos uno por uno - compatibles con rinotrack.sql
        $alterQueries = [
            "ALTER TABLE Tasks ADD COLUMN is_recurrent TINYINT(1) DEFAULT 0 COMMENT 'Indica si la tarea es recurrente' AFTER is_personal",
            "ALTER TABLE Tasks ADD COLUMN recurrence_type ENUM('daily', 'weekly', 'monthly') DEFAULT NULL COMMENT 'Tipo de recurrencia: diaria, semanal, mensual' AFTER is_recurrent",
            "ALTER TABLE Tasks ADD COLUMN recurrence_start_date DATE DEFAULT NULL COMMENT 'Fecha de inicio de la recurrencia' AFTER recurrence_type",
            "ALTER TABLE Tasks ADD COLUMN recurrence_end_date DATE DEFAULT NULL COMMENT 'Fecha de fin de la recurrencia' AFTER recurrence_start_date",
            "ALTER TABLE Tasks ADD COLUMN last_generated_date DATE DEFAULT NULL COMMENT 'Última fecha en que se generó una instancia' AFTER recurrence_end_date",
            "ALTER TABLE Tasks ADD COLUMN parent_recurrent_task_id INT(11) DEFAULT NULL COMMENT 'ID de la tarea recurrente padre (para instancias generadas)' AFTER last_generated_date"
        ];
        
        foreach ($alterQueries as $index => $query) {
            $fieldName = $recurrenceFields[$index];
            
            if (!in_array($fieldName, $existingColumns)) {
                try {
                    $db->exec($query);
                    echo "<p class='success'>✅ Campo '$fieldName' agregado exitosamente</p>";
                } catch (Exception $e) {
                    echo "<p class='error'>❌ Error al agregar campo '$fieldName': " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p class='info'>⏭️ Campo '$fieldName' ya existe, omitiendo</p>";
            }
        }
        
        echo "<h2>3. Verificando estructura final:</h2>";
        $stmt = $db->query("DESCRIBE Tasks");
        $finalColumns = $stmt->fetchAll();
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Por defecto</th><th>Extra</th></tr>";
        foreach ($finalColumns as $col) {
            $isNew = in_array($col['Field'], $recurrenceFields);
            $rowStyle = $isNew ? 'style="background-color: #e6ffe6;"' : '';
            echo "<tr $rowStyle>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "<td>{$col['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h2>4. Resultado:</h2>";
        echo "<p class='success'>✅ Estructura de base de datos actualizada correctamente</p>";
        echo "<p class='info'>Los campos de recurrencia han sido agregados y están listos para usar.</p>";
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error general: " . $e->getMessage() . "</p>";
    }
    ?>
    
    <hr>
    <p><a href="?route=clan_member/tasks">← Volver a Tareas</a></p>
</body>
</html>
