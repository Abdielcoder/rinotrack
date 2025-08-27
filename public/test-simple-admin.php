<?php
require_once '../app/bootstrap.php';

echo "<h1>Prueba Simple de Toggle User Status</h1>";

// Simular una petición POST
$_POST['userId'] = 1; // Cambiar por un ID de usuario válido
$_SERVER['REQUEST_METHOD'] = 'POST';

try {
    echo "<h2>1. Crear AdminController</h2>";
    $adminController = new AdminController();
    echo "<p style='color: green;'>✅ AdminController creado</p>";
    
    echo "<h2>2. Verificar autenticación</h2>";
    $auth = new Auth();
    $isLoggedIn = $auth->isLoggedIn();
    echo "<p>Usuario logueado: " . ($isLoggedIn ? 'Sí' : 'No') . "</p>";
    
    if ($isLoggedIn) {
        $currentUser = $auth->getCurrentUser();
        echo "<p>Usuario actual: " . print_r($currentUser, true) . "</p>";
        
        echo "<h2>3. Verificar permisos de admin</h2>";
        $roleModel = new Role();
        $hasAdminAccess = $roleModel->userHasMinimumRole($currentUser['user_id'], Role::ADMIN);
        echo "<p>¿Tiene acceso de admin?: " . ($hasAdminAccess ? 'Sí' : 'No') . "</p>";
        
        if ($hasAdminAccess) {
            echo "<h2>4. Probar toggleUserStatus</h2>";
            try {
                $adminController->toggleUserStatus();
                echo "<p style='color: green;'>✅ toggleUserStatus ejecutado sin errores</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Error en toggleUserStatus: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ El usuario no tiene permisos de admin</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ No hay usuario logueado</p>";
        echo "<p><a href='?route=login'>Iniciar sesión</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error general: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
}

echo "<hr>";
echo "<p><a href='?route=admin/users'>Ir a la página de usuarios</a></p>";
?>
