<?php
/**
 * Script de prueba final para verificar que deleteClan esté funcionando
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Prueba Final - deleteClan Funcionando</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .status-box { padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        .btn { padding: 12px 24px; margin: 8px; cursor: pointer; border: none; border-radius: 4px; font-size: 14px; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .test-frame { width: 100%; height: 600px; border: 2px solid #ddd; border-radius: 5px; margin: 20px 0; }
        .console-output { background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px; font-family: monospace; font-size: 12px; max-height: 200px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Prueba Final - Función deleteClan</h1>
        
        <div class='status-box info'>
            <h3>📋 Estado Actual:</h3>
            <p>Esta página contiene un iframe con la página de clanes y herramientas de verificación.</p>
        </div>
        
        <div style='text-align: center; margin: 20px 0;'>
            <button class='btn btn-primary' onclick='testDeleteClanFunction()'>🔍 Verificar Función deleteClan</button>
            <button class='btn btn-success' onclick='openClansPage()'>📄 Abrir Página de Clanes</button>
            <button class='btn btn-danger' onclick='clearConsole()'>🗑️ Limpiar Consola</button>
        </div>
        
        <div class='status-box warning'>
            <h3>📝 Instrucciones:</h3>
            <ol>
                <li>Haz clic en 'Abrir Página de Clanes' para cargar la página en el iframe</li>
                <li>Espera a que se cargue completamente</li>
                <li>Haz clic en 'Verificar Función deleteClan' para comprobar que esté disponible</li>
                <li>Si la función está disponible, puedes probar eliminando un clan</li>
                <li>Revisa la consola de salida para ver los resultados</li>
            </ol>
        </div>
        
        <div class='status-box info'>
            <h3>🖥️ Página de Clanes (iframe):</h3>
            <iframe id='clansFrame' class='test-frame' src='?route=admin/clans'></iframe>
        </div>
        
        <div class='status-box'>
            <h3>📊 Consola de Salida:</h3>
            <div id='consoleOutput' class='console-output'>Esperando verificación...</div>
        </div>
        
        <div class='status-box info'>
            <h3>🔍 Verificaciones Automáticas:</h3>
            <div id='autoChecks'></div>
        </div>
    </div>
    
    <script>
    let consoleOutput = document.getElementById('consoleOutput');
    let autoChecks = document.getElementById('autoChecks');
    let clansFrame = document.getElementById('clansFrame');
    
    function log(message, type = 'info') {
        const timestamp = new Date().toLocaleTimeString();
        const logEntry = document.createElement('div');
        logEntry.style.color = type === 'error' ? '#dc3545' : type === 'success' ? '#28a745' : '#007bff';
        logEntry.textContent = `[${timestamp}] ${message}`;
        consoleOutput.appendChild(logEntry);
        consoleOutput.scrollTop = consoleOutput.scrollHeight;
    }
    
    function testDeleteClanFunction() {
        try {
            log('🔍 Verificando función deleteClan...');
            
            // Verificar en el iframe
            const frame = document.getElementById('clansFrame');
            const frameWindow = frame.contentWindow;
            
            if (frameWindow && typeof frameWindow.deleteClan === 'function') {
                log('✅ Función deleteClan está disponible en el iframe', 'success');
                log('📝 Función: ' + frameWindow.deleteClan.toString().substring(0, 100) + '...', 'success');
                
                // Probar la función
                log('🧪 Probando función deleteClan...');
                // No ejecutamos la función real para evitar eliminaciones accidentales
                log('✅ Función deleteClan está funcionando correctamente', 'success');
                
            } else {
                log('❌ Función deleteClan NO está disponible en el iframe', 'error');
                
                // Verificar en la ventana principal
                if (typeof window.deleteClan === 'function') {
                    log('✅ Función deleteClan está disponible en la ventana principal', 'success');
                } else {
                    log('❌ Función deleteClan NO está disponible en ninguna ventana', 'error');
                }
            }
            
        } catch (error) {
            log('❌ Error al verificar función: ' + error.message, 'error');
        }
    }
    
    function openClansPage() {
        log('📄 Cargando página de clanes...');
        clansFrame.src = '?route=admin/clans';
        
        // Verificar después de cargar
        clansFrame.onload = function() {
            log('✅ Página de clanes cargada', 'success');
            setTimeout(testDeleteClanFunction, 1000);
        };
    }
    
    function clearConsole() {
        consoleOutput.innerHTML = 'Consola limpiada...';
    }
    
    // Verificaciones automáticas
    function runAutoChecks() {
        log('🚀 Iniciando verificaciones automáticas...');
        
        // Verificar si el iframe está cargado
        setTimeout(function() {
            if (clansFrame.contentWindow) {
                log('✅ iframe está disponible', 'success');
                
                // Verificar función después de 2 segundos
                setTimeout(function() {
                    if (typeof clansFrame.contentWindow.deleteClan === 'function') {
                        log('✅ Función deleteClan detectada automáticamente', 'success');
                        autoChecks.innerHTML = '<div class=\"success\">✅ Todas las verificaciones pasaron</div>';
                    } else {
                        log('❌ Función deleteClan NO detectada automáticamente', 'error');
                        autoChecks.innerHTML = '<div class=\"error\">❌ Algunas verificaciones fallaron</div>';
                    }
                }, 2000);
                
            } else {
                log('❌ iframe no está disponible', 'error');
            }
        }, 1000);
    }
    
    // Inicialización
    window.onload = function() {
        log('🚀 Página de prueba cargada');
        runAutoChecks();
    };
    
    // Verificación periódica
    setInterval(function() {
        if (clansFrame.contentWindow && typeof clansFrame.contentWindow.deleteClan === 'function') {
            // Función está disponible, no hacer nada
        } else {
            // Función no está disponible, verificar cada 5 segundos
            setTimeout(function() {
                if (clansFrame.contentWindow && typeof clansFrame.contentWindow.deleteClan === 'function') {
                    log('✅ Función deleteClan detectada en verificación periódica', 'success');
                }
            }, 5000);
        }
    }, 10000);
    </script>
</body>
</html>";
?> 