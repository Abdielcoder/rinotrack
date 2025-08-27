<?php
require_once '../app/bootstrap.php';

echo "<h1>Verificación y Corrección de Roles de Administrador</h1>";

try {
    $db = Database::getConnection();
    $roleModel = new Role();
    $userModel = new User();
    
    echo "<h2>1. Verificar roles en la base de datos</h2>";
    
    // Verificar si existen los roles necesarios
    $stmt = $db->query("SELECT * FROM Roles ORDER BY role_id");
    $roles = $stmt->fetchAll();
    
    echo "<h3>Roles existentes:</h3>";
    echo "<ul>";
    foreach ($roles as $role) {
        echo "<li>ID: {$role['role_id']} - Nombre: {$role['role_name']} - Descripción: {$role['description']}</li>";
    }
    echo "</ul>";
    
    // Verificar si existen los roles requeridos
    $requiredRoles = ['super_admin', 'admin', 'lider_clan', 'usuario_normal'];
    $existingRoles = array_column($roles, 'role_name');
    
    echo "<h3>Verificando roles requeridos:</h3>";
    foreach ($requiredRoles as $roleName) {
        if (in_array($roleName, $existingRoles)) {
            echo "<p style='color: green;'>✅ Rol '$roleName' existe</p>";
        } else {
            echo "<p style='color: red;'>❌ Rol '$roleName' NO existe - creando...</p>";
            
            // Crear el rol faltante
            $displayNames = [
                'super_admin' => 'Super Administrador',
                'admin' => 'Administrador',
                'lider_clan' => 'Líder de Clan',
                'usuario_normal' => 'Usuario Normal'
            ];
            
            $descriptions = [
                'super_admin' => 'Acceso completo al sistema',
                'admin' => 'Puede gestionar usuarios, clanes y proyectos',
                'lider_clan' => 'Puede gestionar su clan y proyectos asignados',
                'usuario_normal' => 'Acceso básico, puede participar en proyectos'
            ];
            
            $stmt = $db->prepare("INSERT INTO Roles (role_name, description) VALUES (?, ?)");
            $stmt->execute([$roleName, $descriptions[$roleName]]);
            echo "<p style='color: green;'>✅ Rol '$roleName' creado exitosamente</p>";
        }
    }
    
    echo "<h2>2. Verificar usuarios y sus roles</h2>";
    
    // Obtener todos los usuarios con sus roles
    $stmt = $db->query("
        SELECT u.user_id, u.username, u.email, u.is_active, r.role_name
        FROM Users u
        LEFT JOIN User_Roles ur ON u.user_id = ur.user_id
        LEFT JOIN Roles r ON ur.role_id = r.role_id
        ORDER BY u.user_id
    ");
    $users = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Activo</th><th>Rol</th><th>Acción</th></tr>";
    
    foreach ($users as $user) {
        $activeStatus = $user['is_active'] ? 'Sí' : 'No';
        $roleName = $user['role_name'] ?: 'Sin rol';
        
        echo "<tr>";
        echo "<td>{$user['user_id']}</td>";
        echo "<td>{$user['username']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td>$activeStatus</td>";
        echo "<td>$roleName</td>";
        echo "<td>";
        
        if (!$user['role_name']) {
            echo "<a href='?action=assign_role&user_id={$user['user_id']}&role=admin'>Asignar Admin</a> | ";
            echo "<a href='?action=assign_role&user_id={$user['user_id']}&role=super_admin'>Asignar Super Admin</a>";
        }
        
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Procesar acciones si se solicitan
    if (isset($_GET['action']) && $_GET['action'] === 'assign_role') {
        $userId = (int)($_GET['user_id'] ?? 0);
        $roleName = $_GET['role'] ?? '';
        
        if ($userId > 0 && in_array($roleName, $requiredRoles)) {
            // Obtener el ID del rol
            $stmt = $db->prepare("SELECT role_id FROM Roles WHERE role_name = ?");
            $stmt->execute([$roleName]);
            $role = $stmt->fetch();
            
            if ($role) {
                // Eliminar roles existentes del usuario
                $stmt = $db->prepare("DELETE FROM User_Roles WHERE user_id = ?");
                $stmt->execute([$userId]);
                
                // Asignar el nuevo rol
                $stmt = $db->prepare("INSERT INTO User_Roles (user_id, role_id) VALUES (?, ?)");
                $stmt->execute([$userId, $role['role_id']]);
                
                echo "<p style='color: green;'>✅ Rol '$roleName' asignado al usuario ID $userId</p>";
                echo "<script>setTimeout(function(){ location.reload(); }, 2000);</script>";
            }
        }
    }
    
    echo "<h2>3. Probar autenticación y permisos</h2>";
    
    $auth = new Auth();
    $isLoggedIn = $auth->isLoggedIn();
    echo "<p>Usuario logueado: " . ($isLoggedIn ? 'Sí' : 'No') . "</p>";
    
    if ($isLoggedIn) {
        $currentUser = $auth->getCurrentUser();
        echo "<p>Usuario actual: " . print_r($currentUser, true) . "</p>";
        
        $hasAdminAccess = $roleModel->userHasMinimumRole($currentUser['user_id'], Role::ADMIN);
        echo "<p>¿Tiene acceso de admin?: " . ($hasAdminAccess ? 'Sí' : 'No') . "</p>";
        
        if (!$hasAdminAccess) {
            echo "<p style='color: orange;'>⚠️ El usuario actual no tiene permisos de admin</p>";
            echo "<p><a href='?action=assign_role&user_id={$currentUser['user_id']}&role=admin'>Asignar rol de Admin</a></p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ No hay usuario logueado</p>";
        echo "<p><a href='?route=login'>Iniciar sesión</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
}

echo "<hr>";
echo "<p><a href='?route=admin/users'>Ir a la página de usuarios</a></p>";
echo "<p><a href='test-admin-debug.php'>Ejecutar debug completo</a></p>";
?>
