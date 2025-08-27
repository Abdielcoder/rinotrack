<?php
require_once '../app/bootstrap.php';

echo "<h1>Debug de Autenticación y Permisos de Admin</h1>";

try {
    // Verificar conexión a la base de datos
    echo "<h2>1. Verificación de Base de Datos</h2>";
    $db = Database::getConnection();
    echo "<p style='color: green;'>✅ Conexión a la base de datos exitosa</p>";
    
    // Verificar si hay usuarios en la base de datos
    $stmt = $db->query("SELECT COUNT(*) as count FROM Users");
    $userCount = $stmt->fetch()['count'];
    echo "<p>Total de usuarios en la base de datos: $userCount</p>";
    
    // Verificar si hay roles en la base de datos
    $stmt = $db->query("SELECT COUNT(*) as count FROM Roles");
    $roleCount = $stmt->fetch()['count'];
    echo "<p>Total de roles en la base de datos: $roleCount</p>";
    
    // Verificar roles disponibles
    $stmt = $db->query("SELECT * FROM Roles ORDER BY role_id");
    $roles = $stmt->fetchAll();
    echo "<h3>Roles disponibles:</h3>";
    echo "<ul>";
    foreach ($roles as $role) {
        echo "<li>ID: {$role['role_id']} - Nombre: {$role['role_name']} - Descripción: {$role['description']}</li>";
    }
    echo "</ul>";
    
    // Verificar usuarios con sus roles
    $stmt = $db->query("
        SELECT u.user_id, u.username, u.email, u.is_active, r.role_name
        FROM Users u
        LEFT JOIN User_Roles ur ON u.user_id = ur.user_id
        LEFT JOIN Roles r ON ur.role_id = r.role_id
        ORDER BY u.user_id
    ");
    $users = $stmt->fetchAll();
    
    echo "<h2>2. Usuarios y sus Roles</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Activo</th><th>Rol</th></tr>";
    foreach ($users as $user) {
        $activeStatus = $user['is_active'] ? 'Sí' : 'No';
        $roleName = $user['role_name'] ?: 'Sin rol';
        echo "<tr>";
        echo "<td>{$user['user_id']}</td>";
        echo "<td>{$user['username']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td>$activeStatus</td>";
        echo "<td>$roleName</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Probar autenticación
    echo "<h2>3. Prueba de Autenticación</h2>";
    $auth = new Auth();
    echo "<p>Clase Auth creada exitosamente</p>";
    
    // Verificar si hay sesión activa
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    echo "<p>Estado de sesión: " . session_status() . "</p>";
    echo "<p>Datos de sesión: " . print_r($_SESSION, true) . "</p>";
    
    // Verificar si el usuario está logueado
    $isLoggedIn = $auth->isLoggedIn();
    echo "<p>Usuario logueado: " . ($isLoggedIn ? 'Sí' : 'No') . "</p>";
    
    if ($isLoggedIn) {
        $currentUser = $auth->getCurrentUser();
        echo "<p>Usuario actual: " . print_r($currentUser, true) . "</p>";
        
        // Probar verificación de permisos de admin
        echo "<h2>4. Prueba de Permisos de Admin</h2>";
        $roleModel = new Role();
        $hasAdminAccess = $roleModel->userHasMinimumRole($currentUser['user_id'], Role::ADMIN);
        echo "<p>¿Tiene acceso de admin?: " . ($hasAdminAccess ? 'Sí' : 'No') . "</p>";
        
        // Verificar rol específico del usuario
        $userRole = $roleModel->getUserRole($currentUser['user_id']);
        if ($userRole) {
            echo "<p>Rol del usuario: {$userRole['role_name']}</p>";
            echo "<p>Nivel del rol: " . Role::ROLE_HIERARCHY[$userRole['role_name']] . "</p>";
            echo "<p>Nivel mínimo requerido: " . Role::ROLE_HIERARCHY[Role::ADMIN] . "</p>";
        } else {
            echo "<p style='color: red;'>❌ No se encontró rol para el usuario</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ No hay usuario logueado - prueba iniciando sesión primero</p>";
    }
    
    // Probar creación de AdminController
    echo "<h2>5. Prueba de AdminController</h2>";
    try {
        $adminController = new AdminController();
        echo "<p style='color: green;'>✅ AdminController creado exitosamente</p>";
        
        // Probar método hasAdminAccess
        if (method_exists($adminController, 'hasAdminAccess')) {
            echo "<p style='color: green;'>✅ Método hasAdminAccess existe</p>";
        } else {
            echo "<p style='color: red;'>❌ Método hasAdminAccess NO existe</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error al crear AdminController: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error general: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
}

echo "<hr>";
echo "<p><a href='?route=admin/users'>Ir a la página de usuarios</a></p>";
echo "<p><a href='?route=login'>Ir al login</a></p>";
?>
