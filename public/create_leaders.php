<?php
/**
 * Script para crear usuarios líderes adicionales
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir configuraciones
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

// Incluir modelos
require_once __DIR__ . '/../app/models/Utils.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/Auth.php';
require_once __DIR__ . '/../app/models/Project.php';
require_once __DIR__ . '/../app/models/Clan.php';
require_once __DIR__ . '/../app/models/Role.php';
require_once __DIR__ . '/../app/models/KPI.php';
require_once __DIR__ . '/../app/models/Task.php';

try {
    $db = Database::getConnection();
    
    echo "<h1>Creación de Usuarios Líderes</h1>";
    
    // Definir usuarios líderes a crear
    $leadersToCreate = [
        [
            'username' => 'lider_direccion',
            'email' => 'lider.direccion@rinorisk.com',
            'full_name' => 'Líder Dirección',
            'password' => '123456'
        ],
        [
            'username' => 'lider_gaia',
            'email' => 'lider.gaia@rinorisk.com',
            'full_name' => 'Líder Gaia',
            'password' => '123456'
        ],
        [
            'username' => 'lider_operaciones',
            'email' => 'lider.operaciones@rinorisk.com',
            'full_name' => 'Líder Operaciones',
            'password' => '123456'
        ],
        [
            'username' => 'lider_servicio',
            'email' => 'lider.servicio@rinorisk.com',
            'full_name' => 'Líder Servicio',
            'password' => '123456'
        ]
    ];
    
    echo "<h2>1. Verificando usuarios existentes</h2>";
    
    $userModel = new User();
    $roleModel = new Role();
    
    foreach ($leadersToCreate as $leader) {
        $existingUser = $userModel->findByUsernameOrEmail($leader['username']);
        
        if ($existingUser) {
            echo "<p>✓ Usuario '{$leader['username']}' ya existe</p>";
        } else {
            echo "<p style='color: orange;'>⚠ Usuario '{$leader['username']}' no existe - será creado</p>";
        }
    }
    
    echo "<h2>2. Creando usuarios líderes</h2>";
    
    $createdUsers = [];
    
    foreach ($leadersToCreate as $leader) {
        $existingUser = $userModel->findByUsernameOrEmail($leader['username']);
        
        if (!$existingUser) {
            // Crear usuario
            $userId = $userModel->create(
                $leader['username'],
                $leader['email'],
                $leader['password'],
                $leader['full_name']
            );
            
            if ($userId) {
                // Asignar rol de líder de clan
                $roleId = 3; // ID del rol 'lider_clan'
                $roleAssigned = $roleModel->assignToUser($userId, $roleId);
                
                if ($roleAssigned) {
                    echo "<p style='color: green;'>✓ Usuario '{$leader['username']}' creado y asignado como líder de clan (ID: {$userId})</p>";
                    $createdUsers[] = [
                        'user_id' => $userId,
                        'username' => $leader['username'],
                        'full_name' => $leader['full_name']
                    ];
                } else {
                    echo "<p style='color: red;'>✗ Error al asignar rol de líder a '{$leader['username']}'</p>";
                }
            } else {
                echo "<p style='color: red;'>✗ Error al crear usuario '{$leader['username']}'</p>";
            }
        } else {
            // Verificar si ya tiene rol de líder
            $userRole = $roleModel->getUserRole($existingUser['user_id']);
            
            if ($userRole && $userRole['role_name'] === 'lider_clan') {
                echo "<p>✓ Usuario '{$leader['username']}' ya es líder de clan</p>";
                $createdUsers[] = [
                    'user_id' => $existingUser['user_id'],
                    'username' => $leader['username'],
                    'full_name' => $leader['full_name']
                ];
            } else {
                // Asignar rol de líder
                $roleId = 3; // ID del rol 'lider_clan'
                $roleAssigned = $roleModel->assignToUser($existingUser['user_id'], $roleId);
                
                if ($roleAssigned) {
                    echo "<p style='color: green;'>✓ Rol de líder asignado a '{$leader['username']}'</p>";
                    $createdUsers[] = [
                        'user_id' => $existingUser['user_id'],
                        'username' => $leader['username'],
                        'full_name' => $leader['full_name']
                    ];
                } else {
                    echo "<p style='color: red;'>✗ Error al asignar rol de líder a '{$leader['username']}'</p>";
                }
            }
        }
    }
    
    echo "<h2>3. Asignando líderes a clanes</h2>";
    
    // Obtener clanes que necesitan líderes
    $stmt = $db->prepare("
        SELECT 
            c.clan_id,
            c.clan_name
        FROM Clans c
        LEFT JOIN Clan_Members cm ON c.clan_id = cm.clan_id
        LEFT JOIN User_Roles ur ON cm.user_id = ur.user_id
        LEFT JOIN Roles r ON ur.role_id = r.role_id AND r.role_name = 'lider_clan'
        WHERE r.role_id IS NULL
        ORDER BY c.clan_name
    ");
    $stmt->execute();
    $clansWithoutLeaders = $stmt->fetchAll();
    
    if (empty($clansWithoutLeaders)) {
        echo "<p style='color: green;'>✓ Todos los clanes ya tienen líderes asignados.</p>";
    } else {
        echo "<p>Clanes que necesitan líderes:</p>";
        echo "<ul>";
        foreach ($clansWithoutLeaders as $clan) {
            echo "<li>{$clan['clan_name']} (ID: {$clan['clan_id']})</li>";
        }
        echo "</ul>";
        
        // Asignar líderes a clanes
        $leaderIndex = 0;
        foreach ($clansWithoutLeaders as $clan) {
            if ($leaderIndex < count($createdUsers)) {
                $leader = $createdUsers[$leaderIndex];
                
                // Verificar si el líder ya está en algún clan
                $stmt = $db->prepare("SELECT COUNT(*) as count FROM Clan_Members WHERE user_id = ?");
                $stmt->execute([$leader['user_id']]);
                $memberCount = $stmt->fetch()['count'];
                
                if ($memberCount == 0) {
                    // Agregar líder al clan
                    $stmt = $db->prepare("INSERT INTO Clan_Members (clan_id, user_id) VALUES (?, ?)");
                    $result = $stmt->execute([$clan['clan_id'], $leader['user_id']]);
                    
                    if ($result) {
                        echo "<p style='color: green;'>✓ Líder '{$leader['full_name']}' asignado al clan '{$clan['clan_name']}'</p>";
                    } else {
                        echo "<p style='color: red;'>✗ Error al asignar líder '{$leader['full_name']}' al clan '{$clan['clan_name']}'</p>";
                    }
                } else {
                    echo "<p style='color: orange;'>⚠ Líder '{$leader['full_name']}' ya está en otro clan</p>";
                }
                
                $leaderIndex++;
            } else {
                echo "<p style='color: red;'>✗ No hay suficientes líderes para asignar al clan '{$clan['clan_name']}'</p>";
            }
        }
    }
    
    echo "<h2>4. Verificación final</h2>";
    
    // Verificar estado final
    $stmt = $db->prepare("
        SELECT 
            c.clan_name,
            COUNT(CASE WHEN r.role_name = 'lider_clan' THEN 1 END) as leader_count,
            GROUP_CONCAT(CASE WHEN r.role_name = 'lider_clan' THEN u.full_name END) as leaders
        FROM Clans c
        LEFT JOIN Clan_Members cm ON c.clan_id = cm.clan_id
        LEFT JOIN User_Roles ur ON cm.user_id = ur.user_id
        LEFT JOIN Roles r ON ur.role_id = r.role_id
        LEFT JOIN Users u ON cm.user_id = u.user_id
        GROUP BY c.clan_id, c.clan_name
        ORDER BY c.clan_name
    ");
    $stmt->execute();
    $finalStatus = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Clan</th><th>Líderes</th><th>Nombres de Líderes</th></tr>";
    
    foreach ($finalStatus as $status) {
        $leaderCount = $status['leader_count'];
        $leaders = $status['leaders'] ?: 'Sin líder';
        $color = $leaderCount > 0 ? 'green' : 'red';
        
        echo "<tr>";
        echo "<td>{$status['clan_name']}</td>";
        echo "<td style='color: {$color};'>{$leaderCount}</td>";
        echo "<td>{$leaders}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>5. Credenciales de acceso</h2>";
    echo "<p>Usuarios líderes creados:</p>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Usuario</th><th>Email</th><th>Contraseña</th></tr>";
    
    foreach ($leadersToCreate as $leader) {
        echo "<tr>";
        echo "<td>{$leader['username']}</td>";
        echo "<td>{$leader['email']}</td>";
        echo "<td>{$leader['password']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>6. Instrucciones para probar</h2>";
    echo "<p>Para probar que las rutas funcionan correctamente:</p>";
    echo "<ol>";
    echo "<li>Inicia sesión con cualquiera de los usuarios líderes creados</li>";
    echo "<li>Intenta acceder a las siguientes rutas:</li>";
    echo "<ul>";
    echo "<li><a href='?route=clan_leader/dashboard'>Dashboard del líder de clan</a></li>";
    echo "<li><a href='?route=clan_leader/members'>Gestión de miembros</a></li>";
    echo "<li><a href='?route=clan_leader/projects'>Gestión de proyectos</a></li>";
    echo "<li><a href='?route=clan_leader/tasks'>Gestión de tareas</a></li>";
    echo "<li><a href='?route=clan_leader/kpi-dashboard'>Dashboard KPI</a></li>";
    echo "</ul>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>Error al ejecutar creación de líderes: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?> 