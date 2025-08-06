<?php
/**
 * Script para agregar los clanes faltantes y asignar líderes
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
    
    echo "<h1>Corrección de Clanes y Líderes</h1>";
    
    // Definir los clanes que necesitan ser creados
    $clansToCreate = [
        ['name' => 'DIRECCION', 'department' => 'Dirección'],
        ['name' => 'GAIA', 'department' => 'Operaciones'],
        ['name' => 'OPERACION/PROYECTOS', 'department' => 'Operaciones/Proyectos'],
        ['name' => 'SERVICIO', 'department' => 'Servicio al Cliente']
    ];
    
    // Verificar si ZEUS existe (debería ser Zeus)
    $stmt = $db->prepare("SELECT * FROM Clans WHERE clan_name = 'ZEUS' OR clan_name = 'Zeus'");
    $stmt->execute();
    $zeusClan = $stmt->fetch();
    
    if (!$zeusClan) {
        echo "<p style='color: red;'>Error: El clan ZEUS/Zeus no existe. Debe ser creado primero.</p>";
        exit;
    }
    
    echo "<h2>1. Verificando clanes existentes</h2>";
    
    // Verificar qué clanes ya existen
    foreach ($clansToCreate as $clan) {
        $stmt = $db->prepare("SELECT * FROM Clans WHERE clan_name = ?");
        $stmt->execute([$clan['name']]);
        $existingClan = $stmt->fetch();
        
        if ($existingClan) {
            echo "<p>✓ Clan '{$clan['name']}' ya existe (ID: {$existingClan['clan_id']})</p>";
        } else {
            echo "<p style='color: orange;'>⚠ Clan '{$clan['name']}' no existe - será creado</p>";
        }
    }
    
    echo "<h2>2. Creando clanes faltantes</h2>";
    
    // Crear clanes que no existen
    foreach ($clansToCreate as $clan) {
        $stmt = $db->prepare("SELECT * FROM Clans WHERE clan_name = ?");
        $stmt->execute([$clan['name']]);
        $existingClan = $stmt->fetch();
        
        if (!$existingClan) {
            $stmt = $db->prepare("INSERT INTO Clans (clan_name, clan_departamento, created_at) VALUES (?, ?, NOW())");
            $result = $stmt->execute([$clan['name'], $clan['department']]);
            
            if ($result) {
                $clanId = $db->lastInsertId();
                echo "<p style='color: green;'>✓ Clan '{$clan['name']}' creado exitosamente (ID: {$clanId})</p>";
            } else {
                echo "<p style='color: red;'>✗ Error al crear clan '{$clan['name']}'</p>";
            }
        }
    }
    
    echo "<h2>3. Verificando líderes de clan</h2>";
    
    // Obtener todos los usuarios con rol de líder de clan
    $stmt = $db->prepare("
        SELECT 
            u.user_id,
            u.username,
            u.full_name,
            u.email
        FROM Users u
        JOIN User_Roles ur ON u.user_id = ur.user_id
        JOIN Roles r ON ur.role_id = r.role_id
        WHERE r.role_name = 'lider_clan' AND u.is_active = 1
        ORDER BY u.full_name
    ");
    $stmt->execute();
    $leaders = $stmt->fetchAll();
    
    echo "<p>Líderes de clan disponibles:</p>";
    echo "<ul>";
    foreach ($leaders as $leader) {
        echo "<li>{$leader['full_name']} ({$leader['username']}) - ID: {$leader['user_id']}</li>";
    }
    echo "</ul>";
    
    // Verificar qué clanes tienen líderes
    $stmt = $db->prepare("
        SELECT 
            c.clan_id,
            c.clan_name,
            COUNT(CASE WHEN r.role_name = 'lider_clan' THEN 1 END) as leader_count
        FROM Clans c
        LEFT JOIN Clan_Members cm ON c.clan_id = cm.clan_id
        LEFT JOIN User_Roles ur ON cm.user_id = ur.user_id
        LEFT JOIN Roles r ON ur.role_id = r.role_id
        GROUP BY c.clan_id, c.clan_name
        ORDER BY c.clan_name
    ");
    $stmt->execute();
    $clansWithLeaders = $stmt->fetchAll();
    
    echo "<h2>4. Estado de líderes por clan</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Clan</th><th>Líderes</th><th>Estado</th></tr>";
    
    foreach ($clansWithLeaders as $clan) {
        $status = $clan['leader_count'] > 0 ? '✓ Tiene líder' : '⚠ Sin líder';
        $color = $clan['leader_count'] > 0 ? 'green' : 'red';
        echo "<tr>";
        echo "<td>{$clan['clan_name']}</td>";
        echo "<td>{$clan['leader_count']}</td>";
        echo "<td style='color: {$color};'>{$status}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>5. Asignando líderes a clanes sin líder</h2>";
    
    // Obtener clanes sin líder
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
        echo "<p style='color: green;'>✓ Todos los clanes tienen líderes asignados.</p>";
    } else {
        echo "<p>Clanes sin líder:</p>";
        echo "<ul>";
        foreach ($clansWithoutLeaders as $clan) {
            echo "<li>{$clan['clan_name']} (ID: {$clan['clan_id']})</li>";
        }
        echo "</ul>";
        
        // Asignar líderes disponibles a clanes sin líder
        $leaderIndex = 0;
        foreach ($clansWithoutLeaders as $clan) {
            if ($leaderIndex < count($leaders)) {
                $leader = $leaders[$leaderIndex];
                
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
    
    echo "<h2>6. Verificación final</h2>";
    
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
    
    echo "<h2>7. Instrucciones para probar</h2>";
    echo "<p>Para probar que las rutas funcionan correctamente:</p>";
    echo "<ol>";
    echo "<li>Inicia sesión con un usuario que tenga rol de líder de clan</li>";
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
    echo "<p>Error al ejecutar corrección: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?> 