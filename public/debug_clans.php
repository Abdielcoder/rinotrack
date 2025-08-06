<?php
/**
 * Script de diagnóstico para verificar el estado de clanes y usuarios
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
    
    echo "<h1>Diagnóstico de Clanes y Usuarios</h1>";
    
    // 1. Verificar clanes existentes
    echo "<h2>1. Clanes Existentes</h2>";
    $stmt = $db->prepare("SELECT * FROM Clans ORDER BY clan_id");
    $stmt->execute();
    $clans = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Departamento</th><th>Creado</th></tr>";
    foreach ($clans as $clan) {
        echo "<tr>";
        echo "<td>{$clan['clan_id']}</td>";
        echo "<td>{$clan['clan_name']}</td>";
        echo "<td>{$clan['clan_departamento']}</td>";
        echo "<td>{$clan['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 2. Verificar roles existentes
    echo "<h2>2. Roles Existentes</h2>";
    $stmt = $db->prepare("SELECT * FROM Roles ORDER BY role_id");
    $stmt->execute();
    $roles = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nombre</th></tr>";
    foreach ($roles as $role) {
        echo "<tr>";
        echo "<td>{$role['role_id']}</td>";
        echo "<td>{$role['role_name']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 3. Verificar usuarios con roles
    echo "<h2>3. Usuarios con Roles</h2>";
    $stmt = $db->prepare("
        SELECT 
            u.user_id,
            u.username,
            u.full_name,
            u.email,
            u.is_active,
            r.role_name
        FROM Users u
        LEFT JOIN User_Roles ur ON u.user_id = ur.user_id
        LEFT JOIN Roles r ON ur.role_id = r.role_id
        ORDER BY u.user_id
    ");
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Usuario</th><th>Nombre</th><th>Email</th><th>Activo</th><th>Rol</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>{$user['user_id']}</td>";
        echo "<td>{$user['username']}</td>";
        echo "<td>{$user['full_name']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td>" . ($user['is_active'] ? 'Sí' : 'No') . "</td>";
        echo "<td>{$user['role_name']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 4. Verificar miembros de clanes
    echo "<h2>4. Miembros de Clanes</h2>";
    $stmt = $db->prepare("
        SELECT 
            cm.clan_member_id,
            c.clan_name,
            u.username,
            u.full_name,
            r.role_name
        FROM Clan_Members cm
        JOIN Clans c ON cm.clan_id = c.clan_id
        JOIN Users u ON cm.user_id = u.user_id
        LEFT JOIN User_Roles ur ON u.user_id = ur.user_id
        LEFT JOIN Roles r ON ur.role_id = r.role_id
        ORDER BY c.clan_name, u.full_name
    ");
    $stmt->execute();
    $members = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Clan</th><th>Usuario</th><th>Nombre</th><th>Rol</th></tr>";
    foreach ($members as $member) {
        echo "<tr>";
        echo "<td>{$member['clan_member_id']}</td>";
        echo "<td>{$member['clan_name']}</td>";
        echo "<td>{$member['username']}</td>";
        echo "<td>{$member['full_name']}</td>";
        echo "<td>{$member['role_name']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 5. Verificar líderes de clan
    echo "<h2>5. Líderes de Clan</h2>";
    $stmt = $db->prepare("
        SELECT 
            u.user_id,
            u.username,
            u.full_name,
            u.email,
            c.clan_name
        FROM Users u
        JOIN User_Roles ur ON u.user_id = ur.user_id
        JOIN Roles r ON ur.role_id = r.role_id
        LEFT JOIN Clan_Members cm ON u.user_id = cm.user_id
        LEFT JOIN Clans c ON cm.clan_id = c.clan_id
        WHERE r.role_name = 'lider_clan'
        ORDER BY c.clan_name
    ");
    $stmt->execute();
    $leaders = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Usuario</th><th>Nombre</th><th>Email</th><th>Clan</th></tr>";
    foreach ($leaders as $leader) {
        echo "<tr>";
        echo "<td>{$leader['user_id']}</td>";
        echo "<td>{$leader['username']}</td>";
        echo "<td>{$leader['full_name']}</td>";
        echo "<td>{$leader['email']}</td>";
        echo "<td>" . ($leader['clan_name'] ?? 'Sin clan asignado') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 6. Verificar clanes sin líderes
    echo "<h2>6. Clanes sin Líderes</h2>";
    $stmt = $db->prepare("
        SELECT 
            c.clan_id,
            c.clan_name,
            c.clan_departamento
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
        echo "<p>Todos los clanes tienen líderes asignados.</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Departamento</th></tr>";
        foreach ($clansWithoutLeaders as $clan) {
            echo "<tr>";
            echo "<td>{$clan['clan_id']}</td>";
            echo "<td>{$clan['clan_name']}</td>";
            echo "<td>{$clan['clan_departamento']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 7. Verificar usuarios sin clan
    echo "<h2>7. Usuarios sin Clan</h2>";
    $stmt = $db->prepare("
        SELECT 
            u.user_id,
            u.username,
            u.full_name,
            u.email,
            r.role_name
        FROM Users u
        LEFT JOIN Clan_Members cm ON u.user_id = cm.user_id
        LEFT JOIN User_Roles ur ON u.user_id = ur.user_id
        LEFT JOIN Roles r ON ur.role_id = r.role_id
        WHERE cm.user_id IS NULL AND u.is_active = 1
        ORDER BY u.full_name
    ");
    $stmt->execute();
    $usersWithoutClan = $stmt->fetchAll();
    
    if (empty($usersWithoutClan)) {
        echo "<p>Todos los usuarios activos tienen clan asignado.</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Usuario</th><th>Nombre</th><th>Email</th><th>Rol</th></tr>";
        foreach ($usersWithoutClan as $user) {
            echo "<tr>";
            echo "<td>{$user['user_id']}</td>";
            echo "<td>{$user['username']}</td>";
            echo "<td>{$user['full_name']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['role_name']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 8. Verificar problemas específicos
    echo "<h2>8. Problemas Detectados</h2>";
    $problems = [];
    
    // Verificar si hay clanes mencionados en el problema
    $problemClans = ['ZEUS', 'DIRECCION', 'GAIA', 'OPERACION/PROYECTOS', 'SERVICIO'];
    
    foreach ($problemClans as $clanName) {
        $stmt = $db->prepare("SELECT * FROM Clans WHERE clan_name = ?");
        $stmt->execute([$clanName]);
        $clan = $stmt->fetch();
        
        if (!$clan) {
            $problems[] = "Clan '$clanName' no existe en la base de datos";
        } else {
            // Verificar si tiene líder
            $stmt = $db->prepare("
                SELECT COUNT(*) as count
                FROM Clan_Members cm
                JOIN User_Roles ur ON cm.user_id = ur.user_id
                JOIN Roles r ON ur.role_id = r.role_id
                WHERE cm.clan_id = ? AND r.role_name = 'lider_clan'
            ");
            $stmt->execute([$clan['clan_id']]);
            $leaderCount = $stmt->fetch()['count'];
            
            if ($leaderCount == 0) {
                $problems[] = "Clan '$clanName' no tiene líder asignado";
            }
        }
    }
    
    if (empty($problems)) {
        echo "<p>No se detectaron problemas específicos.</p>";
    } else {
        echo "<ul>";
        foreach ($problems as $problem) {
            echo "<li style='color: red;'>$problem</li>";
        }
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>Error al ejecutar diagnóstico: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?> 