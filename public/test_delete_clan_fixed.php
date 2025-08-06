<?php
/**
 * Script de prueba para verificar que la función deleteClan esté funcionando después de los cambios
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
        <title>Prueba deleteClan - Corregido</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
            .success { background-color: #d4edda; border-color: #c3e6cb; }
            .error { background-color: #f8d7da; border-color: #f5c6cb; }
            .warning { background-color: #fff3cd; border-color: #ffeaa7; }
            button { padding: 10px 20px; margin: 5px; cursor: pointer; }
            .delete-btn { background: #dc3545; color: white; border: none; border-radius: 3px; }
            table { width: 100%; border-collapse: collapse; margin: 10px 0; }
            th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        </style>
    </head>
    <body>";
    
    echo "<h1>Prueba de Función deleteClan - Versión Corregida</h1>";
    
    // Verificar clanes disponibles
    echo "<div class='test-section'>";
    echo "<h2>1. Clanes Disponibles para Prueba</h2>";
    $stmt = $db->prepare("SELECT clan_id, clan_name FROM Clans ORDER BY clan_name");
    $stmt->execute();
    $clans = $stmt->fetchAll();
    
    if (empty($clans)) {
        echo "<p class='error'>No hay clanes disponibles para probar.</p>";
    } else {
        echo "<table>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Acción</th></tr>";
        
        foreach ($clans as $clan) {
            echo "<tr>";
            echo "<td>{$clan['clan_id']}</td>";
            echo "<td>{$clan['clan_name']}</td>";
            echo "<td><button class='delete-btn' onclick='testDeleteClan({$clan['clan_id']})'>Probar Eliminar</button></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    echo "</div>";
    
    // JavaScript de prueba
    echo "<div class='test-section'>";
    echo "<h2>2. Verificación de JavaScript</h2>";
    echo "<button onclick='checkDeleteClanFunction()'>Verificar Función deleteClan</button>";
    echo "<button onclick='testGlobalScope()'>Verificar Scope Global</button>";
    echo "<div id='js-status'></div>";
    echo "</div>";
    
    // Enlaces de prueba
    echo "<div class='test-section'>";
    echo "<h2>3. Enlaces de Prueba</h2>";
    echo "<p><a href='?route=admin/clans' target='_blank'>Ir a la página de clanes (nueva ventana)</a></p>";
    echo "<p><a href='javascript:checkDeleteClanFunction()'>Verificar función deleteClan</a></p>";
    echo "</div>";
    
    // JavaScript de prueba
    echo "<script>
    // Función para verificar si deleteClan está disponible
    function checkDeleteClanFunction() {
        const statusDiv = document.getElementById('js-status');
        
        if (typeof window.deleteClan === 'function') {
            statusDiv.innerHTML = '<p class=\"success\">✓ La función deleteClan está disponible en window</p>';
            console.log('Función deleteClan disponible:', window.deleteClan);
        } else {
            statusDiv.innerHTML = '<p class=\"error\">✗ La función deleteClan NO está disponible en window</p>';
            console.error('Función deleteClan no encontrada en window');
        }
        
        // Verificar también en el scope global
        if (typeof deleteClan === 'function') {
            statusDiv.innerHTML += '<p class=\"success\">✓ La función deleteClan está disponible en scope global</p>';
        } else {
            statusDiv.innerHTML += '<p class=\"error\">✗ La función deleteClan NO está disponible en scope global</p>';
        }
    }
    
    // Función para probar el scope global
    function testGlobalScope() {
        const statusDiv = document.getElementById('js-status');
        statusDiv.innerHTML = '<p class=\"warning\">Verificando scope global...</p>';
        
        // Listar todas las funciones en window que contengan 'delete'
        const deleteFunctions = [];
        for (let key in window) {
            if (key.toLowerCase().includes('delete') && typeof window[key] === 'function') {
                deleteFunctions.push(key);
            }
        }
        
        if (deleteFunctions.length > 0) {
            statusDiv.innerHTML += '<p class=\"success\">Funciones con \"delete\" encontradas: ' + deleteFunctions.join(', ') + '</p>';
        } else {
            statusDiv.innerHTML += '<p class=\"error\">No se encontraron funciones con \"delete\"</p>';
        }
    }
    
    // Función de prueba para eliminar clan
    function testDeleteClan(clanId) {
        if (typeof window.deleteClan === 'function') {
            console.log('Probando deleteClan con ID:', clanId);
            window.deleteClan(clanId);
        } else {
            alert('La función deleteClan no está disponible. Verifica la consola para más detalles.');
            console.error('deleteClan no disponible para clan ID:', clanId);
        }
    }
    
    // Verificar al cargar la página
    window.onload = function() {
        console.log('Página cargada - verificando función deleteClan...');
        setTimeout(function() {
            checkDeleteClanFunction();
        }, 100);
    };
    
    // Verificar después de un breve delay
    setTimeout(function() {
        console.log('Verificación tardía de deleteClan...');
        checkDeleteClanFunction();
    }, 500);
    </script>";
    
    echo "</body></html>";
    
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>Error al ejecutar prueba: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?> 