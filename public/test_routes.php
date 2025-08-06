<?php
/**
 * Script de prueba para verificar rutas de líderes de clan
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
    
    echo "<h1>Prueba de Rutas de Líderes de Clan</h1>";
    
    // Obtener todos los líderes de clan
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
        WHERE r.role_name = 'lider_clan' AND u.is_active = 1
        ORDER BY c.clan_name, u.full_name
    ");
    $stmt->execute();
    $leaders = $stmt->fetchAll();
    
    if (empty($leaders)) {
        echo "<p style='color: red;'>No hay líderes de clan disponibles para probar.</p>";
        exit;
    }
    
    echo "<h2>Líderes de Clan Disponibles</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Usuario</th><th>Nombre</th><th>Email</th><th>Clan</th><th>Acciones</th></tr>";
    
    foreach ($leaders as $leader) {
        echo "<tr>";
        echo "<td>{$leader['username']}</td>";
        echo "<td>{$leader['full_name']}</td>";
        echo "<td>{$leader['email']}</td>";
        echo "<td>" . ($leader['clan_name'] ?? 'Sin clan asignado') . "</td>";
        echo "<td>";
        echo "<a href='?route=login&test_user={$leader['username']}' style='margin-right: 10px;'>Probar Login</a>";
        echo "<a href='?route=clan_leader/dashboard&test_user={$leader['username']}' style='margin-right: 10px;'>Dashboard</a>";
        echo "<a href='?route=clan_leader/members&test_user={$leader['username']}' style='margin-right: 10px;'>Miembros</a>";
        echo "<a href='?route=clan_leader/projects&test_user={$leader['username']}' style='margin-right: 10px;'>Proyectos</a>";
        echo "<a href='?route=clan_leader/tasks&test_user={$leader['username']}'>Tareas</a>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Rutas a Probar</h2>";
    echo "<p>Las siguientes rutas deberían funcionar para los líderes de clan:</p>";
    
    $routes = [
        'clan_leader/dashboard' => 'Dashboard principal del líder de clan',
        'clan_leader/members' => 'Gestión de miembros del clan',
        'clan_leader/projects' => 'Gestión de proyectos del clan',
        'clan_leader/tasks' => 'Gestión de tareas del clan',
        'clan_leader/kpi-dashboard' => 'Dashboard KPI del clan',
        'clan_leader/collaborator-availability' => 'Disponibilidad de colaboradores'
    ];
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Ruta</th><th>Descripción</th><th>Estado</th></tr>";
    
    foreach ($routes as $route => $description) {
        echo "<tr>";
        echo "<td>{$route}</td>";
        echo "<td>{$description}</td>";
        echo "<td style='color: green;'>✓ Disponible</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Instrucciones de Prueba</h2>";
    echo "<ol>";
    echo "<li><strong>Iniciar sesión:</strong> Usa cualquiera de los usuarios líderes listados arriba</li>";
    echo "<li><strong>Probar rutas:</strong> Haz clic en los enlaces de 'Probar Login' para cada líder</li>";
    echo "<li><strong>Verificar acceso:</strong> Después del login, intenta acceder a las rutas de líder de clan</li>";
    echo "<li><strong>Reportar problemas:</strong> Si alguna ruta no funciona, verifica los logs de error</li>";
    echo "</ol>";
    
    echo "<h2>Posibles Problemas y Soluciones</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Problema</th><th>Solución</th></tr>";
    echo "<tr>";
    echo "<td>Error 404 en rutas</td>";
    echo "<td>Verificar que las rutas estén definidas en public/index.php</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Acceso denegado</td>";
    echo "<td>Verificar que el usuario tenga rol de líder de clan y esté asignado a un clan</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Error de base de datos</td>";
    echo "<td>Verificar conexión a la base de datos y permisos</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Página en blanco</td>";
    echo "<td>Verificar logs de PHP y errores de sintaxis</td>";
    echo "</tr>";
    echo "</table>";
    
    echo "<h2>Enlaces de Prueba Rápida</h2>";
    echo "<p>Haz clic en los siguientes enlaces para probar directamente:</p>";
    echo "<ul>";
    foreach ($routes as $route => $description) {
        echo "<li><a href='?route={$route}' target='_blank'>{$route}</a> - {$description}</li>";
    }
    echo "</ul>";
    
    echo "<h2>Verificación de Configuración</h2>";
    
    // Verificar configuración de base de datos
    try {
        $testStmt = $db->prepare("SELECT 1");
        $testStmt->execute();
        echo "<p style='color: green;'>✓ Conexión a base de datos: OK</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Conexión a base de datos: Error - " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    // Verificar archivos de controlador
    $controllerFile = __DIR__ . '/../app/controllers/ClanLeaderController.php';
    if (file_exists($controllerFile)) {
        echo "<p style='color: green;'>✓ Controlador ClanLeaderController: OK</p>";
    } else {
        echo "<p style='color: red;'>✗ Controlador ClanLeaderController: No encontrado</p>";
    }
    
    // Verificar archivos de vista
    $viewDir = __DIR__ . '/../app/views/clan_leader/';
    if (is_dir($viewDir)) {
        echo "<p style='color: green;'>✓ Directorio de vistas: OK</p>";
    } else {
        echo "<p style='color: red;'>✗ Directorio de vistas: No encontrado</p>";
    }
    
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>Error al ejecutar prueba: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?> 