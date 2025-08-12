<?php
// Script de prueba para diagnosticar problemas con la creación de clanes
require_once '../config/app.php';
require_once '../config/database.php';

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Test de Creación de Clanes</h1>";

try {
    // Probar conexión a la base de datos
    echo "<h2>1. Prueba de Conexión a la Base de Datos</h2>";
    
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<p style='color: green;'>✅ Conexión a la base de datos exitosa</p>";
    
    // Verificar estructura de la tabla Clans
    echo "<h2>2. Verificación de la Tabla Clans</h2>";
    
    $stmt = $pdo->query("DESCRIBE Clans");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar datos existentes
    echo "<h2>3. Clanes Existentes</h2>";
    
    $stmt = $pdo->query("SELECT * FROM Clans ORDER BY clan_id DESC LIMIT 5");
    $clans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($clans)) {
        echo "<p style='color: orange;'>⚠️ No hay clanes en la base de datos</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Departamento</th><th>Creado</th></tr>";
        
        foreach ($clans as $clan) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($clan['clan_id']) . "</td>";
            echo "<td>" . htmlspecialchars($clan['clan_name']) . "</td>";
            echo "<td>" . htmlspecialchars($clan['clan_departamento']) . "</td>";
            echo "<td>" . htmlspecialchars($clan['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Probar inserción manual
    echo "<h2>4. Prueba de Inserción Manual</h2>";
    
    $testName = 'Clan de Prueba ' . date('Y-m-d H:i:s');
    $testDepartamento = 'Departamento de Prueba';
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO Clans (clan_name, clan_departamento, created_at) 
            VALUES (?, ?, NOW())
        ");
        
        $result = $stmt->execute([$testName, $testDepartamento]);
        
        if ($result) {
            $clanId = $pdo->lastInsertId();
            echo "<p style='color: green;'>✅ Inserción manual exitosa. Clan ID: $clanId</p>";
            
            // Limpiar el clan de prueba
            $stmt = $pdo->prepare("DELETE FROM Clans WHERE clan_id = ?");
            $stmt->execute([$clanId]);
            echo "<p style='color: blue;'>🗑️ Clan de prueba eliminado</p>";
        } else {
            echo "<p style='color: red;'>❌ Inserción manual falló</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Error en inserción manual: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    // Verificar permisos de usuario
    echo "<h2>5. Verificación de Usuario Actual</h2>";
    
    session_start();
    if (isset($_SESSION['user_id'])) {
        echo "<p style='color: green;'>✅ Usuario autenticado: ID " . $_SESSION['user_id'] . "</p>";
        
        // Verificar si el usuario tiene rol de admin
        $stmt = $pdo->prepare("
            SELECT r.role_name 
            FROM Users u 
            JOIN User_Roles ur ON u.user_id = ur.user_id 
            JOIN Roles r ON ur.role_id = r.role_id 
            WHERE u.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($role) {
            echo "<p style='color: green;'>✅ Rol del usuario: " . htmlspecialchars($role['role_name']) . "</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Usuario sin rol asignado</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Usuario no autenticado</p>";
    }
    
    // Verificar logs de error
    echo "<h2>6. Verificación de Logs</h2>";
    
    $errorLog = ini_get('error_log');
    if ($errorLog && file_exists($errorLog)) {
        echo "<p style='color: blue;'>📋 Archivo de log: $errorLog</p>";
        
        // Mostrar las últimas líneas del log
        $logLines = file($errorLog);
        $recentLines = array_slice($logLines, -10);
        
        echo "<h3>Últimas 10 líneas del log:</h3>";
        echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px; max-height: 200px; overflow-y: auto;'>";
        foreach ($recentLines as $line) {
            echo htmlspecialchars($line);
        }
        echo "</pre>";
    } else {
        echo "<p style='color: orange;'>⚠️ No se pudo acceder al archivo de log</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error de base de datos: " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error general: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>📋 Resumen del Diagnóstico</h2>";
echo "<p>Este script ha verificado:</p>";
echo "<ul>";
echo "<li>✅ Conexión a la base de datos</li>";
echo "<li>✅ Estructura de la tabla Clans</li>";
echo "<li>✅ Datos existentes</li>";
echo "<li>✅ Capacidad de inserción</li>";
echo "<li>✅ Estado de autenticación</li>";
echo "<li>✅ Logs de error</li>";
echo "</ul>";

echo "<p><strong>Próximo paso:</strong> Si todo está bien aquí, el problema puede estar en el controlador o en la validación de datos.</p>";
?>
