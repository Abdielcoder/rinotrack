<?php
/**
 * Script que redirige a la página de clanes y ejecuta verificaciones JavaScript
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Prueba deleteClan - Redirección</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn { padding: 12px 24px; margin: 8px; cursor: pointer; border: none; border-radius: 4px; font-size: 14px; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .info-box { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 4px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Prueba de Función deleteClan</h1>
        
        <div class='info-box'>
            <h3>📋 Instrucciones:</h3>
            <ol>
                <li>Haz clic en 'Ir a Página de Clanes' para abrir la página donde está definida la función</li>
                <li>Una vez en la página, abre las herramientas de desarrollador (F12)</li>
                <li>Ve a la consola y escribe: <code>typeof window.deleteClan</code></li>
                <li>Si devuelve 'function', la función está disponible</li>
                <li>Prueba hacer clic en un botón de eliminar clan</li>
            </ol>
        </div>
        
        <div style='text-align: center; margin: 30px 0;'>
            <a href='?route=admin/clans' target='_blank' class='btn btn-primary'>
                📄 Ir a Página de Clanes (Nueva Ventana)
            </a>
            
            <a href='?route=admin/clans' class='btn btn-success'>
                📄 Ir a Página de Clanes (Misma Ventana)
            </a>
        </div>
        
        <div class='info-box'>
            <h3>🔍 Verificaciones a realizar:</h3>
            <ul>
                <li><strong>En la consola del navegador:</strong></li>
                <ul>
                    <li><code>typeof window.deleteClan</code> → Debe devolver 'function'</li>
                    <li><code>window.deleteClan</code> → Debe mostrar la función</li>
                    <li><code>deleteClan</code> → Debe mostrar la función</li>
                </ul>
                <li><strong>En la página:</strong></li>
                <ul>
                    <li>Hacer clic en un botón de eliminar clan</li>
                    <li>Verificar que aparece el diálogo de confirmación</li>
                    <li>Verificar que no hay errores en la consola</li>
                </ul>
            </ul>
        </div>
        
        <div class='info-box'>
            <h3>🚨 Si hay errores:</h3>
            <ul>
                <li>Verificar que estás logueado como administrador</li>
                <li>Verificar que la URL es correcta: <code>?route=admin/clans</code></li>
                <li>Verificar que no hay errores de JavaScript en la consola</li>
                <li>Verificar que el archivo <code>app/views/admin/clans.php</code> tiene la función definida</li>
            </ul>
        </div>
        
        <script>
        // Función para verificar si estamos en la página correcta
        function checkCurrentPage() {
            if (window.location.href.includes('route=admin/clans')) {
                console.log('✅ Estamos en la página de clanes');
                
                // Esperar un momento para que se cargue todo el JavaScript
                setTimeout(function() {
                    console.log('🔍 Verificando función deleteClan...');
                    
                    if (typeof window.deleteClan === 'function') {
                        console.log('✅ Función deleteClan está disponible');
                        console.log('📝 Función:', window.deleteClan);
                        
                        // Mostrar mensaje en la página
                        const message = document.createElement('div');
                        message.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #28a745; color: white; padding: 15px; border-radius: 5px; z-index: 9999;';
                        message.innerHTML = '✅ Función deleteClan disponible';
                        document.body.appendChild(message);
                        
                        // Remover mensaje después de 3 segundos
                        setTimeout(function() {
                            if (message.parentNode) {
                                message.parentNode.removeChild(message);
                            }
                        }, 3000);
                        
                    } else {
                        console.error('❌ Función deleteClan NO está disponible');
                        
                        // Mostrar mensaje de error
                        const message = document.createElement('div');
                        message.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #dc3545; color: white; padding: 15px; border-radius: 5px; z-index: 9999;';
                        message.innerHTML = '❌ Función deleteClan NO disponible';
                        document.body.appendChild(message);
                        
                        // Remover mensaje después de 5 segundos
                        setTimeout(function() {
                            if (message.parentNode) {
                                message.parentNode.removeChild(message);
                            }
                        }, 5000);
                    }
                }, 1000);
                
            } else {
                console.log('ℹ️ No estamos en la página de clanes');
            }
        }
        
        // Verificar al cargar la página
        window.onload = function() {
            checkCurrentPage();
        };
        
        // Verificar también después de un delay
        setTimeout(function() {
            checkCurrentPage();
        }, 2000);
        </script>
    </div>
</body>
</html>";
?> 