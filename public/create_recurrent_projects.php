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
    <title>Crear Proyectos de Tareas Recurrentes</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background: #f5f5f5; 
            max-width: 1200px;
            margin: 0 auto;
        }
        .container { 
            background: white; 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 { color: #333; }
        .success { 
            color: green; 
            font-weight: bold; 
            padding: 10px;
            background: #e8f5e9;
            border-radius: 4px;
            margin: 10px 0;
        }
        .error { 
            color: red; 
            font-weight: bold;
            padding: 10px;
            background: #ffebee;
            border-radius: 4px;
            margin: 10px 0;
        }
        .info { 
            color: #1976d2;
            padding: 10px;
            background: #e3f2fd;
            border-radius: 4px;
            margin: 10px 0;
        }
        .warning { 
            color: #f57c00;
            padding: 10px;
            background: #fff3e0;
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
        }
        .btn:hover {
            background: #45a049;
        }
        .btn-danger {
            background: #f44336;
        }
        .btn-danger:hover {
            background: #da190b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 Crear Proyectos "Mis Tareas Recurrentes"</h1>
        
        <?php
        try {
            $db = Database::getInstance();
            
            // Verificar si ya existe el proyecto especial
            $checkStmt = $db->query("
                SELECT COUNT(*) as count 
                FROM Projects 
                WHERE project_name = 'Mis Tareas Recurrentes' 
                AND project_type = 'recurrent'
            ");
            $existingCount = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            if ($existingCount > 0) {
                echo '<div class="warning">⚠️ Ya existen ' . $existingCount . ' proyectos "Mis Tareas Recurrentes"</div>';
            }
            
            // Obtener todos los usuarios activos
            $usersStmt = $db->query("
                SELECT u.user_id, u.username, u.full_name, r.role_name,
                       (SELECT COUNT(*) FROM Projects p 
                        WHERE p.created_by_user_id = u.user_id 
                        AND p.project_name = 'Mis Tareas Recurrentes') as has_project
                FROM Users u
                JOIN Roles r ON u.role_id = r.role_id
                WHERE u.is_active = 1
                ORDER BY u.user_id
            ");
            $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<div class="info">ℹ️ Total de usuarios activos: ' . count($users) . '</div>';
            
            // Procesar creación si se envió el formulario
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
                if ($_POST['action'] === 'create_all') {
                    $created = 0;
                    $errors = 0;
                    
                    echo '<h2>Creando proyectos...</h2>';
                    
                    foreach ($users as $user) {
                        if ($user['has_project'] == 0) {
                            try {
                                // Crear proyecto para el usuario
                                $insertStmt = $db->prepare("
                                    INSERT INTO Projects (
                                        project_name,
                                        project_description,
                                        project_type,
                                        created_by_user_id,
                                        is_personal,
                                        created_at
                                    ) VALUES (
                                        'Mis Tareas Recurrentes',
                                        'Proyecto especial para gestionar tareas recurrentes personales',
                                        'recurrent',
                                        ?,
                                        1,
                                        NOW()
                                    )
                                ");
                                $insertStmt->execute([$user['user_id']]);
                                
                                $created++;
                                echo '<div class="success">✅ Proyecto creado para: ' . htmlspecialchars($user['full_name']) . ' (' . $user['username'] . ')</div>';
                            } catch (Exception $e) {
                                $errors++;
                                echo '<div class="error">❌ Error al crear proyecto para ' . htmlspecialchars($user['full_name']) . ': ' . $e->getMessage() . '</div>';
                            }
                        }
                    }
                    
                    echo '<div class="info">📊 Resumen: ' . $created . ' proyectos creados, ' . $errors . ' errores</div>';
                    
                    // Recargar lista de usuarios
                    $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                } elseif ($_POST['action'] === 'create_single' && isset($_POST['user_id'])) {
                    $userId = (int)$_POST['user_id'];
                    
                    try {
                        $insertStmt = $db->prepare("
                            INSERT INTO Projects (
                                project_name,
                                project_description,
                                project_type,
                                created_by_user_id,
                                is_personal,
                                created_at
                            ) VALUES (
                                'Mis Tareas Recurrentes',
                                'Proyecto especial para gestionar tareas recurrentes personales',
                                'recurrent',
                                ?,
                                1,
                                NOW()
                            )
                        ");
                        $insertStmt->execute([$userId]);
                        
                        echo '<div class="success">✅ Proyecto creado exitosamente</div>';
                    } catch (Exception $e) {
                        echo '<div class="error">❌ Error: ' . $e->getMessage() . '</div>';
                    }
                    
                    // Recargar lista
                    $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
                }
            }
            
            // Contar usuarios sin proyecto
            $usersWithoutProject = 0;
            foreach ($users as $user) {
                if ($user['has_project'] == 0) {
                    $usersWithoutProject++;
                }
            }
            
            ?>
            
            <h2>Estado Actual</h2>
            
            <?php if ($usersWithoutProject > 0): ?>
                <div class="warning">
                    ⚠️ Hay <?php echo $usersWithoutProject; ?> usuarios sin proyecto "Mis Tareas Recurrentes"
                </div>
                
                <form method="POST" style="margin: 20px 0;">
                    <input type="hidden" name="action" value="create_all">
                    <button type="submit" class="btn">
                        🚀 Crear Proyecto para Todos los Usuarios Sin Proyecto
                    </button>
                </form>
            <?php else: ?>
                <div class="success">
                    ✅ Todos los usuarios tienen su proyecto "Mis Tareas Recurrentes"
                </div>
            <?php endif; ?>
            
            <h2>Lista de Usuarios</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Nombre Completo</th>
                        <th>Rol</th>
                        <th>Tiene Proyecto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['role_name']); ?></td>
                            <td>
                                <?php if ($user['has_project'] > 0): ?>
                                    <span style="color: green;">✅ Sí</span>
                                <?php else: ?>
                                    <span style="color: red;">❌ No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['has_project'] == 0): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="create_single">
                                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                        <button type="submit" class="btn" style="padding: 5px 10px; font-size: 12px;">
                                            Crear Proyecto
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: #666;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <h2>Verificación de Estructura</h2>
            <?php
            // Verificar que la columna project_type existe
            $columnsStmt = $db->query("SHOW COLUMNS FROM Projects WHERE Field = 'project_type'");
            $projectTypeColumn = $columnsStmt->fetch();
            
            if ($projectTypeColumn) {
                echo '<div class="success">✅ La columna project_type existe en la tabla Projects</div>';
            } else {
                echo '<div class="error">❌ La columna project_type NO existe. Ejecutando alteración...</div>';
                
                try {
                    $db->exec("
                        ALTER TABLE Projects 
                        ADD COLUMN IF NOT EXISTS project_type VARCHAR(50) DEFAULT 'normal' 
                        COMMENT 'Tipo de proyecto: normal, personal, recurrent, eventual'
                    ");
                    echo '<div class="success">✅ Columna project_type agregada exitosamente</div>';
                } catch (Exception $e) {
                    echo '<div class="error">❌ Error al agregar columna: ' . $e->getMessage() . '</div>';
                }
            }
            
            // Verificar columna is_personal
            $columnsStmt = $db->query("SHOW COLUMNS FROM Projects WHERE Field = 'is_personal'");
            $isPersonalColumn = $columnsStmt->fetch();
            
            if ($isPersonalColumn) {
                echo '<div class="success">✅ La columna is_personal existe en la tabla Projects</div>';
            } else {
                echo '<div class="error">❌ La columna is_personal NO existe. Ejecutando alteración...</div>';
                
                try {
                    $db->exec("
                        ALTER TABLE Projects 
                        ADD COLUMN IF NOT EXISTS is_personal TINYINT(1) DEFAULT 0 
                        COMMENT '1 si es proyecto personal del usuario, 0 si es de clan'
                    ");
                    echo '<div class="success">✅ Columna is_personal agregada exitosamente</div>';
                } catch (Exception $e) {
                    echo '<div class="error">❌ Error al agregar columna: ' . $e->getMessage() . '</div>';
                }
            }
            ?>
            
        } catch (Exception $e) {
            echo '<div class="error">❌ Error general: ' . $e->getMessage() . '</div>';
            echo '<pre>' . $e->getTraceAsString() . '</pre>';
        }
        ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <a href="?route=admin/dashboard" class="btn">← Volver al Dashboard Admin</a>
        </div>
    </div>
</body>
</html>
