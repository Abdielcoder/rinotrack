<?php
/**
 * Script de prueba para verificar la función deleteClan
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
    
    echo "<h1>Prueba de Función deleteClan</h1>";
    
    // Verificar que la función deleteClan esté disponible en el JavaScript
    echo "<h2>1. Verificación de JavaScript</h2>";
    echo "<p>La función deleteClan debe estar definida en el objeto window.</p>";
    
    // Crear un botón de prueba
    echo "<h2>2. Botón de Prueba</h2>";
    echo "<p>Haz clic en el botón para probar la función deleteClan:</p>";
    echo "<button onclick='testDeleteClan()' style='padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;'>Probar deleteClan</button>";
    
    // Verificar clanes disponibles
    echo "<h2>3. Clanes Disponibles</h2>";
    $stmt = $db->prepare("SELECT clan_id, clan_name FROM Clans ORDER BY clan_name");
    $stmt->execute();
    $clans = $stmt->fetchAll();
    
    if (empty($clans)) {
        echo "<p style='color: red;'>No hay clanes disponibles para probar.</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Acción</th></tr>";
        
        foreach ($clans as $clan) {
            echo "<tr>";
            echo "<td>{$clan['clan_id']}</td>";
            echo "<td>{$clan['clan_name']}</td>";
            echo "<td><button onclick='deleteClan({$clan['clan_id']})' style='padding: 5px 10px; background: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer;'>Eliminar</button></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // JavaScript de prueba
    echo "<script>
    // Función de prueba
    function testDeleteClan() {
        if (typeof window.deleteClan === 'function') {
            alert('✓ La función deleteClan está disponible');
            console.log('Función deleteClan:', window.deleteClan);
        } else {
            alert('✗ La función deleteClan NO está disponible');
            console.error('Función deleteClan no encontrada');
        }
    }
    
    // Verificar al cargar la página
    window.onload = function() {
        console.log('Verificando función deleteClan...');
        if (typeof window.deleteClan === 'function') {
            console.log('✓ Función deleteClan disponible');
        } else {
            console.error('✗ Función deleteClan no disponible');
        }
    };
    </script>";
    
    echo "<h2>4. Instrucciones</h2>";
    echo "<ol>";
    echo "<li>Haz clic en 'Probar deleteClan' para verificar que la función esté disponible</li>";
    echo "<li>Si la función está disponible, puedes probar eliminando un clan</li>";
    echo "<li>Verifica la consola del navegador para más detalles</li>";
    echo "</ol>";
    
    echo "<h2>5. Enlaces de Prueba</h2>";
    echo "<p><a href='?route=admin/clans' target='_blank'>Ir a la página de clanes</a></p>";
    echo "<p><a href='javascript:testDeleteClan()'>Probar función deleteClan</a></p>";
    
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>Error al ejecutar prueba: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?> 