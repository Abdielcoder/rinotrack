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
    <title>Solución Definitiva - Sistema de Delegación</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background: #f5f5f5; 
        }
        .container { 
            max-width: 1000px; 
            margin: 0 auto; 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success { 
            color: green; 
            font-weight: bold; 
            background: #e8f5e9;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .error { 
            color: red; 
            font-weight: bold;
            background: #ffebee;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .info { 
            color: #1976d2;
            background: #e3f2fd;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .warning { 
            color: #f57c00;
            background: #fff3e0;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0; 
        }
        th, td { 
            padding: 12px; 
            text-align: left; 
            border: 1px solid #ddd; 
        }
        th { 
            background: #4CAF50; 
            color: white; 
        }
        tr:nth-child(even) { 
            background: #f9f9f9; 
        }
        .btn {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background: #45a049;
        }
        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Solución Definitiva - Sistema de Delegación</h1>
        
        <?php
        try {
            $db = Database::getInstance();
            
            echo '<h2>1. Verificando estructura de la tabla Projects...</h2>';
            
            // Verificar campos existentes
            $stmt = $db->query("SHOW COLUMNS FROM Projects");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $hasAllowDelegation = false;
            $hasProjectType = false;
            
            echo '<table>';
            echo '<tr><th>Campo</th><th>Tipo</th><th>Default</th><th>Comentario</th></tr>';
            foreach ($columns as $col) {
                if ($col['Field'] == 'allow_delegation') $hasAllowDelegation = true;
                if ($col['Field'] == 'project_type') $hasProjectType = true;
                
                $highlight = in_array($col['Field'], ['allow_delegation', 'project_type']) ? 'style="background-color: #e8f5e9;"' : '';
                echo "<tr $highlight>";
                echo '<td>' . $col['Field'] . '</td>';
                echo '<td>' . $col['Type'] . '</td>';
                echo '<td>' . ($col['Default'] ?? 'NULL') . '</td>';
                echo '<td>' . ($col['Comment'] ?? '') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            
            if ($hasAllowDelegation && $hasProjectType) {
                echo '<div class="success">✅ Todos los campos necesarios existen en la tabla Projects</div>';
            } else {
                echo '<div class="error">❌ Faltan campos en la tabla Projects</div>';
                if (!$hasAllowDelegation) echo '<div class="error">- Falta: allow_delegation</div>';
                if (!$hasProjectType) echo '<div class="error">- Falta: project_type</div>';
            }
            
            echo '<h2>2. Verificando datos de proyectos...</h2>';
            
            // Obtener proyectos con información de delegación
            $stmt = $db->query("
                SELECT 
                    project_id, 
                    project_name, 
                    clan_id,
                    allow_delegation,
                    project_type,
                    is_personal
                FROM Projects 
                WHERE clan_id IS NOT NULL
                ORDER BY clan_id, project_name
            ");
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($projects) > 0) {
                echo '<p class="info">Se encontraron ' . count($projects) . ' proyectos</p>';
                echo '<table>';
                echo '<tr><th>ID</th><th>Nombre</th><th>Clan</th><th>Delegación</th><th>Tipo</th><th>Personal</th></tr>';
                foreach ($projects as $project) {
                    $delegationStatus = $project['allow_delegation'] ? '✅ Sí' : '❌ No';
                    $delegationColor = $project['allow_delegation'] ? 'color: green;' : 'color: red;';
                    
                    echo '<tr>';
                    echo '<td>' . $project['project_id'] . '</td>';
                    echo '<td>' . htmlspecialchars($project['project_name']) . '</td>';
                    echo '<td>' . $project['clan_id'] . '</td>';
                    echo '<td style="' . $delegationColor . '">' . $delegationStatus . '</td>';
                    echo '<td>' . ($project['project_type'] ?? 'normal') . '</td>';
                    echo '<td>' . ($project['is_personal'] ? 'Sí' : 'No') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<div class="warning">⚠️ No se encontraron proyectos</div>';
            }
            
            echo '<h2>3. Activar delegación para proyectos específicos</h2>';
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activate_delegation'])) {
                // Activar delegación para proyectos especiales
                $specialProjects = ['Tareas Recurrentes', 'Tareas Eventuales', 'Mis Tareas Recurrentes'];
                $activated = 0;
                
                foreach ($specialProjects as $projectName) {
                    $updateStmt = $db->prepare("
                        UPDATE Projects 
                        SET allow_delegation = 1 
                        WHERE project_name LIKE ?
                    ");
                    $updateStmt->execute(["%$projectName%"]);
                    $affected = $updateStmt->rowCount();
                    if ($affected > 0) {
                        echo '<div class="success">✅ Activada delegación para proyectos: "' . $projectName . '" (' . $affected . ' proyectos)</div>';
                        $activated += $affected;
                    }
                }
                
                if ($activated > 0) {
                    echo '<div class="success">🎉 Total de proyectos actualizados: ' . $activated . '</div>';
                } else {
                    echo '<div class="info">ℹ️ No se encontraron proyectos para actualizar</div>';
                }
            }
            
            echo '<form method="POST">';
            echo '<input type="hidden" name="activate_delegation" value="1">';
            echo '<button type="submit" class="btn">🚀 Activar Delegación para Proyectos Especiales</button>';
            echo '</form>';
            
            echo '<h2>4. Verificar modelo Project.php</h2>';
            
            // Verificar que el modelo incluya allow_delegation
            $modelPath = '../app/models/Project.php';
            if (file_exists($modelPath)) {
                $modelContent = file_get_contents($modelPath);
                if (strpos($modelContent, 'allow_delegation') !== false) {
                    echo '<div class="success">✅ El modelo Project.php incluye allow_delegation</div>';
                } else {
                    echo '<div class="error">❌ El modelo Project.php NO incluye allow_delegation</div>';
                }
                
                if (strpos($modelContent, 'COALESCE(p.allow_delegation, 0)') !== false) {
                    echo '<div class="success">✅ El modelo usa COALESCE para allow_delegation</div>';
                } else {
                    echo '<div class="warning">⚠️ El modelo no usa COALESCE para allow_delegation</div>';
                }
            }
            
            echo '<h2>5. Prueba del sistema</h2>';
            echo '<div class="info">';
            echo '<p><strong>Para probar el sistema:</strong></p>';
            echo '<ol>';
            echo '<li>Ve a <a href="?route=clan_leader/tasks" target="_blank">Gestión de Tareas del Líder</a></li>';
            echo '<li>Deberías ver checkboxes "Delegar tareas" en cada card de proyecto</li>';
            echo '<li>Al marcar/desmarcar, debería aparecer una notificación de éxito</li>';
            echo '<li>Los proyectos especiales deberían tener delegación activada por defecto</li>';
            echo '</ol>';
            echo '</div>';
            
            echo '<h2>6. Estadísticas finales</h2>';
            
            $stats = [
                'Total proyectos' => $db->query("SELECT COUNT(*) FROM Projects")->fetchColumn(),
                'Proyectos con delegación' => $db->query("SELECT COUNT(*) FROM Projects WHERE allow_delegation = 1")->fetchColumn(),
                'Proyectos especiales' => $db->query("SELECT COUNT(*) FROM Projects WHERE project_name IN ('Tareas Recurrentes', 'Tareas Eventuales', 'Mis Tareas Recurrentes')")->fetchColumn(),
            ];
            
            echo '<table>';
            echo '<tr><th>Métrica</th><th>Valor</th></tr>';
            foreach ($stats as $metric => $value) {
                echo '<tr><td>' . $metric . '</td><td><strong>' . $value . '</strong></td></tr>';
            }
            echo '</table>';
            
        } catch (Exception $e) {
            echo '<div class="error">❌ Error: ' . $e->getMessage() . '</div>';
            echo '<pre>' . $e->getTraceAsString() . '</pre>';
        }
        ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <a href="?route=admin/dashboard" class="btn">← Volver al Dashboard</a>
            <a href="?route=clan_leader/tasks" class="btn" target="_blank">🔍 Probar Sistema de Delegación</a>
        </div>
    </div>
</body>
</html>
