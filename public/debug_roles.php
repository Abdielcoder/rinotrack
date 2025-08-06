<?php
// Script de diagnóstico para verificar roles y clanes
require_once 'config/database.php';
require_once 'app/models/Role.php';
require_once 'app/models/User.php';
require_once 'app/models/Clan.php';

echo "<h1>Diagnóstico de Roles y Clanes</h1>";

// Verificar conexión a la base de datos
try {
    $db = Database::getConnection();
    echo "<p style='color: green;'>✅ Conexión a la base de datos exitosa</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error de conexión: " . $e->getMessage() . "</p>";
    exit;
}

// Verificar roles
echo "<h2>Roles en la Base de Datos</h2>";
$roleModel = new Role();
$roles = $roleModel->getAll();

if (empty($roles)) {
    echo "<p style='color: red;'>❌ No se encontraron roles en la base de datos</p>";
} else {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Usuarios</th></tr>";
    foreach ($roles as $role) {
        echo "<tr>";
        echo "<td>" . $role['role_id'] . "</td>";
        echo "<td>" . $role['role_name'] . "</td>";
        echo "<td>" . $role['user_count'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Verificar clanes
echo "<h2>Clanes en la Base de Datos</h2>";
$clanModel = new Clan();
$clans = $clanModel->getAll();

if (empty($clans)) {
    echo "<p style='color: red;'>❌ No se encontraron clanes en la base de datos</p>";
} else {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Departamento</th><th>Miembros</th></tr>";
    foreach ($clans as $clan) {
        $memberCount = $clanModel->getMemberCount($clan['clan_id']);
        echo "<tr>";
        echo "<td>" . $clan['clan_id'] . "</td>";
        echo "<td>" . $clan['clan_name'] . "</td>";
        echo "<td>" . $clan['clan_departamento'] . "</td>";
        echo "<td>" . $memberCount . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Verificar usuarios con rol de líder de clan
echo "<h2>Usuarios con Rol de Líder de Clan</h2>";
$lideresClan = $roleModel->getUsersByRole('lider_clan');

if (empty($lideresClan)) {
    echo "<p style='color: red;'>❌ No se encontraron usuarios con rol de líder de clan</p>";
} else {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Usuario</th><th>Nombre</th><th>Email</th><th>Activo</th></tr>";
    foreach ($lideresClan as $lider) {
        echo "<tr>";
        echo "<td>" . $lider['user_id'] . "</td>";
        echo "<td>" . $lider['username'] . "</td>";
        echo "<td>" . $lider['full_name'] . "</td>";
        echo "<td>" . $lider['email'] . "</td>";
        echo "<td>" . ($lider['is_active'] ? 'Sí' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Verificar todos los usuarios y sus roles
echo "<h2>Todos los Usuarios y sus Roles</h2>";
$userModel = new User();
$users = $userModel->getAllWithRoles();

if (empty($users)) {
    echo "<p style='color: red;'>❌ No se encontraron usuarios en la base de datos</p>";
} else {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Usuario</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Activo</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . $user['user_id'] . "</td>";
        echo "<td>" . $user['username'] . "</td>";
        echo "<td>" . $user['full_name'] . "</td>";
        echo "<td>" . $user['email'] . "</td>";
        echo "<td>" . ($user['role_name'] ?? 'Sin rol') . "</td>";
        echo "<td>" . ($user['is_active'] ? 'Sí' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Verificar si los nuevos clanes mencionados existen
echo "<h2>Búsqueda de Nuevos Clanes</h2>";
$nuevosClanes = ['ZEUS', 'DIRECCION', 'GAIA', 'OPERACION', 'PROYECTOS', 'SERVICIO'];

foreach ($nuevosClanes as $nombreClan) {
    $clanEncontrado = false;
    foreach ($clans as $clan) {
        if (stripos($clan['clan_name'], $nombreClan) !== false || 
            stripos($clan['clan_departamento'], $nombreClan) !== false) {
            echo "<p style='color: green;'>✅ Clan encontrado: " . $clan['clan_name'] . " (" . $clan['clan_departamento'] . ")</p>";
            $clanEncontrado = true;
            break;
        }
    }
    if (!$clanEncontrado) {
        echo "<p style='color: orange;'>⚠️ Clan no encontrado: " . $nombreClan . "</p>";
    }
}

echo "<h2>Recomendaciones</h2>";
echo "<ul>";
echo "<li>Si los nuevos clanes no existen, deben ser creados en la base de datos</li>";
echo "<li>Si los usuarios no tienen el rol correcto, deben ser asignados como líderes de clan</li>";
echo "<li>Verificar que los usuarios estén activos (is_active = 1)</li>";
echo "</ul>";
?> 