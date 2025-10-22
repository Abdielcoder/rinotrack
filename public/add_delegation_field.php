<?php
require_once '../app/bootstrap.php';

session_start();

// Verificar si es admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Acceso denegado. Solo administradores pueden ejecutar este script.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Agregar Campo de Delegación</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background: #f5f5f5; 
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
        }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Agregar Campo allow_delegation a Projects</h1>
        
        <?php
        try {
            $db = Database::getInstance();
            
            echo '<h2>1. Verificando estructura actual...</h2>';
            
            // Verificar si el campo ya existe
            $stmt = $db->query("SHOW COLUMNS FROM Projects WHERE Field = 'allow_delegation'");
            $column = $stmt->fetch();
            
            if ($column) {
                echo '<p class="info">ℹ️ El campo allow_delegation ya existe en la tabla Projects</p>';
                echo '<pre>';
                print_r($column);
                echo '</pre>';
            } else {
                echo '<p class="info">📋 El campo allow_delegation no existe. Procediendo a agregarlo...</p>';
                
                // Agregar el campo
                $sql = "ALTER TABLE Projects 
                        ADD COLUMN `allow_delegation` TINYINT(1) DEFAULT 0 
                        COMMENT 'Permite que miembros del clan agreguen tareas al proyecto'
                        AFTER `is_personal`";
                
                echo '<h2>2. Ejecutando SQL:</h2>';
                echo '<pre>' . $sql . '</pre>';
                
                $db->exec($sql);
                
                echo '<p class="success">✅ Campo allow_delegation agregado exitosamente</p>';
                
                // Agregar índice para mejorar búsquedas
                try {
                    $db->exec("CREATE INDEX idx_allow_delegation ON Projects(allow_delegation)");
                    echo '<p class="success">✅ Índice idx_allow_delegation creado</p>';
                } catch (Exception $e) {
                    if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
                        echo '<p class="info">ℹ️ El índice ya existe</p>';
                    } else {
                        echo '<p class="error">⚠️ Error al crear índice: ' . $e->getMessage() . '</p>';
                    }
                }
            }
            
            // Verificar la estructura final
            echo '<h2>3. Estructura final de Projects:</h2>';
            $stmt = $db->query("DESCRIBE Projects");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<table border="1" cellpadding="5" cellspacing="0" style="width: 100%;">';
            echo '<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>';
            foreach ($columns as $col) {
                $highlight = ($col['Field'] == 'allow_delegation') ? 'style="background-color: #e8f5e9;"' : '';
                echo "<tr $highlight>";
                echo '<td>' . $col['Field'] . '</td>';
                echo '<td>' . $col['Type'] . '</td>';
                echo '<td>' . $col['Null'] . '</td>';
                echo '<td>' . $col['Key'] . '</td>';
                echo '<td>' . ($col['Default'] ?? 'NULL') . '</td>';
                echo '<td>' . $col['Extra'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            
            // Actualizar proyectos especiales para permitir delegación por defecto
            echo '<h2>4. Actualizando proyectos especiales...</h2>';
            
            $specialProjects = ['Mis Tareas Recurrentes', 'Tareas Recurrentes', 'Tareas Eventuales'];
            foreach ($specialProjects as $projectName) {
                $updateStmt = $db->prepare("
                    UPDATE Projects 
                    SET allow_delegation = 1 
                    WHERE project_name = ?
                ");
                $updateStmt->execute([$projectName]);
                $affected = $updateStmt->rowCount();
                if ($affected > 0) {
                    echo '<p class="success">✅ Proyecto "' . $projectName . '" actualizado (' . $affected . ' registros)</p>';
                }
            }
            
            echo '<h2>✅ Script completado exitosamente</h2>';
            
        } catch (Exception $e) {
            echo '<p class="error">❌ Error: ' . $e->getMessage() . '</p>';
            echo '<pre>' . $e->getTraceAsString() . '</pre>';
        }
        ?>
        
        <div style="margin-top: 30px;">
            <a href="?route=admin/dashboard" style="padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px;">
                ← Volver al Dashboard
            </a>
        </div>
    </div>
</body>
</html>
