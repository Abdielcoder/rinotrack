<?php
/**
 * Script de prueba final para verificar que la función deleteClan esté funcionando
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
    
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Prueba Final - deleteClan</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
            .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
            .success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
            .error { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
            .warning { background-color: #fff3cd; border-color: #ffeaa7; color: #856404; }
            .info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
            button { padding: 12px 24px; margin: 8px; cursor: pointer; border: none; border-radius: 4px; font-size: 14px; }
            .btn-primary { background: #007bff; color: white; }
            .btn-danger { background: #dc3545; color: white; }
            .btn-success { background: #28a745; color: white; }
            .btn-warning { background: #ffc107; color: #212529; }
            table { width: 100%; border-collapse: collapse; margin: 15px 0; }
            th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
            th { background-color: #f8f9fa; font-weight: bold; }
            .status-indicator { display: inline-block; width: 12px; height: 12px; border-radius: 50%; margin-right: 8px; }
            .status-ok { background-color: #28a745; }
            .status-error { background-color: #dc3545; }
            .status-warning { background-color: #ffc107; }
        </style>
    </head>
    <body>
        <div class='container'>";
    
    echo "<h1>🔧 Prueba Final - Función deleteClan</h1>";
    echo "<p>Esta página verifica que la función deleteClan esté funcionando correctamente después de todas las correcciones.</p>";
    
    // Verificar clanes disponibles
    echo "<div class='test-section info'>";
    echo "<h2>📋 Clanes Disponibles para Prueba</h2>";
    $stmt = $db->prepare("SELECT clan_id, clan_name FROM Clans ORDER BY clan_name");
    $stmt->execute();
    $clans = $stmt->fetchAll();
    
    if (empty($clans)) {
        echo "<p class='error'>❌ No hay clanes disponibles para probar.</p>";
    } else {
        echo "<table>";
        echo "<tr><th>ID</th><th>Nombre del Clan</th><th>Estado</th><th>Acción</th></tr>";
        
        foreach ($clans as $clan) {
            echo "<tr>";
            echo "<td>{$clan['clan_id']}</td>";
            echo "<td>{$clan['clan_name']}</td>";
            echo "<td><span class='status-indicator status-ok'></span>Disponible</td>";
            echo "<td><button class='btn-danger' onclick='testDeleteClan({$clan['clan_id']})'>🗑️ Probar Eliminar</button></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    echo "</div>";
    
    // Panel de verificación
    echo "<div class='test-section'>";
    echo "<h2>🔍 Panel de Verificación</h2>";
    echo "<button class='btn-primary' onclick='checkDeleteClanFunction()'>🔍 Verificar Función deleteClan</button>";
    echo "<button class='btn-warning' onclick='testGlobalScope()'>🌐 Verificar Scope Global</button>";
    echo "<button class='btn-success' onclick='testNetworkConnection()'>🌐 Probar Conexión</button>";
    echo "<div id='js-status'></div>";
    echo "</div>";
    
    // Enlaces de prueba
    echo "<div class='test-section'>";
    echo "<h2>🔗 Enlaces de Prueba</h2>";
    echo "<p><a href='?route=admin/clans' target='_blank' style='color: #007bff; text-decoration: none;'>📄 Ir a la página de clanes (nueva ventana)</a></p>";
    echo "<p><a href='javascript:checkDeleteClanFunction()' style='color: #007bff; text-decoration: none;'>🔍 Verificar función deleteClan</a></p>";
    echo "</div>";
    
    // Instrucciones
    echo "<div class='test-section warning'>";
    echo "<h2>📝 Instrucciones de Prueba</h2>";
    echo "<ol>";
    echo "<li>Haz clic en 'Verificar Función deleteClan' para confirmar que la función esté disponible</li>";
    echo "<li>Si la función está disponible, puedes probar eliminando un clan</li>";
    echo "<li>Verifica la consola del navegador (F12) para más detalles</li>";
    echo "<li>Si hay errores, revisa los logs del servidor</li>";
    echo "</ol>";
    echo "</div>";
    
    // JavaScript de prueba
    echo "<script>
    // Función para verificar si deleteClan está disponible
    function checkDeleteClanFunction() {
        const statusDiv = document.getElementById('js-status');
        let status = '';
        
        // Verificar en window
        if (typeof window.deleteClan === 'function') {
            status += '<p class=\"success\">✅ La función deleteClan está disponible en window</p>';
            console.log('✅ Función deleteClan disponible en window:', window.deleteClan);
        } else {
            status += '<p class=\"error\">❌ La función deleteClan NO está disponible en window</p>';
            console.error('❌ Función deleteClan no encontrada en window');
        }
        
        // Verificar en scope global
        if (typeof deleteClan === 'function') {
            status += '<p class=\"success\">✅ La función deleteClan está disponible en scope global</p>';
        } else {
            status += '<p class=\"error\">❌ La función deleteClan NO está disponible en scope global</p>';
        }
        
        // Verificar si es una función válida
        if (typeof window.deleteClan === 'function') {
            try {
                const functionString = window.deleteClan.toString();
                if (functionString.includes('confirm') && functionString.includes('fetch')) {
                    status += '<p class=\"success\">✅ La función deleteClan parece estar implementada correctamente</p>';
                } else {
                    status += '<p class=\"warning\">⚠️ La función deleteClan existe pero puede no estar implementada correctamente</p>';
                }
            } catch (e) {
                status += '<p class=\"error\">❌ Error al verificar la implementación de deleteClan</p>';
            }
        }
        
        statusDiv.innerHTML = status;
    }
    
    // Función para probar el scope global
    function testGlobalScope() {
        const statusDiv = document.getElementById('js-status');
        statusDiv.innerHTML = '<p class=\"info\">🔍 Verificando scope global...</p>';
        
        // Listar todas las funciones en window que contengan 'delete'
        const deleteFunctions = [];
        for (let key in window) {
            if (key.toLowerCase().includes('delete') && typeof window[key] === 'function') {
                deleteFunctions.push(key);
            }
        }
        
        if (deleteFunctions.length > 0) {
            statusDiv.innerHTML += '<p class=\"success\">✅ Funciones con \"delete\" encontradas: ' + deleteFunctions.join(', ') + '</p>';
        } else {
            statusDiv.innerHTML += '<p class=\"error\">❌ No se encontraron funciones con \"delete\"</p>';
        }
        
        // Verificar otras funciones importantes
        const importantFunctions = ['viewClanDetails', 'openCreateClanModal', 'editClan'];
        const availableFunctions = [];
        
        importantFunctions.forEach(func => {
            if (typeof window[func] === 'function') {
                availableFunctions.push(func);
            }
        });
        
        if (availableFunctions.length > 0) {
            statusDiv.innerHTML += '<p class=\"success\">✅ Funciones importantes disponibles: ' + availableFunctions.join(', ') + '</p>';
        } else {
            statusDiv.innerHTML += '<p class=\"warning\">⚠️ No se encontraron funciones importantes</p>';
        }
    }
    
    // Función para probar la conexión de red
    function testNetworkConnection() {
        const statusDiv = document.getElementById('js-status');
        statusDiv.innerHTML = '<p class=\"info\">🌐 Probando conexión de red...</p>';
        
        fetch('?route=admin/clans')
            .then(response => {
                if (response.ok) {
                    statusDiv.innerHTML += '<p class=\"success\">✅ Conexión al servidor exitosa</p>';
                } else {
                    statusDiv.innerHTML += '<p class=\"error\">❌ Error en la respuesta del servidor: ' + response.status + '</p>';
                }
            })
            .catch(error => {
                statusDiv.innerHTML += '<p class=\"error\">❌ Error de conexión: ' + error.message + '</p>';
            });
    }
    
    // Función de prueba para eliminar clan
    function testDeleteClan(clanId) {
        if (typeof window.deleteClan === 'function') {
            console.log('🔧 Probando deleteClan con ID:', clanId);
            window.deleteClan(clanId);
        } else {
            alert('❌ La función deleteClan no está disponible. Verifica la consola para más detalles.');
            console.error('❌ deleteClan no disponible para clan ID:', clanId);
            checkDeleteClanFunction();
        }
    }
    
    // Verificar al cargar la página
    window.onload = function() {
        console.log('🚀 Página cargada - verificando función deleteClan...');
        setTimeout(function() {
            checkDeleteClanFunction();
        }, 100);
    };
    
    // Verificación tardía
    setTimeout(function() {
        console.log('⏰ Verificación tardía de deleteClan...');
        checkDeleteClanFunction();
    }, 1000);
    
    // Verificación adicional después de 3 segundos
    setTimeout(function() {
        console.log('🔍 Verificación final de deleteClan...');
        if (typeof window.deleteClan === 'function') {
            console.log('✅ deleteClan está disponible después de 3 segundos');
        } else {
            console.error('❌ deleteClan NO está disponible después de 3 segundos');
        }
    }, 3000);
    </script>";
    
    echo "</div></body></html>";
    
} catch (Exception $e) {
    echo "<h1>❌ Error</h1>";
    echo "<p>Error al ejecutar prueba: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?> 