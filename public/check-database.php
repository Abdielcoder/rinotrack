<?php
require_once '../app/bootstrap.php';

echo "<h1>Verificación de Base de Datos</h1>";

try {
    $db = Database::getConnection();
    
    echo "<h2>1. Verificar tablas principales</h2>";
    
    $requiredTables = ['Users', 'Roles', 'User_Roles', 'Clans', 'Projects', 'Tasks', 'remember_tokens'];
    
    foreach ($requiredTables as $table) {
        try {
            $stmt = $db->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch()['count'];
            echo "<p style='color: green;'>✅ Tabla '$table' existe con $count registros</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Tabla '$table' NO existe o hay error: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<h2>2. Verificar estructura de tabla Users</h2>";
    try {
        $stmt = $db->query("DESCRIBE Users");
        $columns = $stmt->fetchAll();
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column['Field']}</td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>{$column['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error al verificar estructura de Users: " . $e->getMessage() . "</p>";
    }
    
    echo "<h2>3. Verificar estructura de tabla Roles</h2>";
    try {
        $stmt = $db->query("DESCRIBE Roles");
        $columns = $stmt->fetchAll();
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column['Field']}</td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>{$column['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error al verificar estructura de Roles: " . $e->getMessage() . "</p>";
    }
    
    echo "<h2>4. Verificar estructura de tabla User_Roles</h2>";
    try {
        $stmt = $db->query("DESCRIBE User_Roles");
        $columns = $stmt->fetchAll();
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column['Field']}</td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>{$column['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error al verificar estructura de User_Roles: " . $e->getMessage() . "</p>";
    }
    
    echo "<h2>5. Verificar datos de ejemplo</h2>";
    
    // Verificar usuarios
    $stmt = $db->query("SELECT user_id, username, email, is_active FROM Users LIMIT 5");
    $users = $stmt->fetchAll();
    echo "<h3>Primeros 5 usuarios:</h3>";
    echo "<ul>";
    foreach ($users as $user) {
        $activeStatus = $user['is_active'] ? 'Activo' : 'Inactivo';
        echo "<li>ID: {$user['user_id']} - Username: {$user['username']} - Email: {$user['email']} - Estado: $activeStatus</li>";
    }
    echo "</ul>";
    
    // Verificar roles
    $stmt = $db->query("SELECT role_id, role_name, description FROM Roles");
    $roles = $stmt->fetchAll();
    echo "<h3>Roles disponibles:</h3>";
    echo "<ul>";
    foreach ($roles as $role) {
        echo "<li>ID: {$role['role_id']} - Nombre: {$role['role_name']} - Descripción: {$role['description']}</li>";
    }
    echo "</ul>";
    
    // Verificar asignaciones de roles
    $stmt = $db->query("
        SELECT u.username, r.role_name 
        FROM Users u 
        JOIN User_Roles ur ON u.user_id = ur.user_id 
        JOIN Roles r ON ur.role_id = r.role_id 
        LIMIT 10
    ");
    $assignments = $stmt->fetchAll();
    echo "<h3>Asignaciones de roles (primeros 10):</h3>";
    echo "<ul>";
    foreach ($assignments as $assignment) {
        echo "<li>Usuario: {$assignment['username']} - Rol: {$assignment['role_name']}</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error general: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
}

echo "<hr>";
echo "<p><a href='fix-admin-roles.php'>Verificar y corregir roles</a></p>";
echo "<p><a href='test-admin-debug.php'>Debug completo</a></p>";
?>
