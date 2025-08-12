<?php
// Script de prueba para verificar que los assets se cargan correctamente
require_once '../config/app.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test de Assets - Detección Automática</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .test-item { margin: 10px 0; padding: 15px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
        .warning { background: #fff3cd; color: #856404; border-left: 4px solid #ffc107; }
        .code { background: #f8f9fa; padding: 10px; border-radius: 3px; font-family: monospace; margin: 10px 0; }
        button { padding: 10px 20px; margin: 5px; border: none; border-radius: 4px; background: #007bff; color: white; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test de Carga de Assets - Detección Automática</h1>
        
        <div class="test-item info">
            <strong>APP_URL configurada en PHP:</strong> <code><?php echo APP_URL; ?></code>
        </div>
        
        <div class="test-item info">
            <strong>URL actual del navegador:</strong> <code id="current-url"></code>
        </div>
        
        <div class="test-item info">
            <strong>URL base detectada por JavaScript:</strong> <code id="detected-url"></code>
        </div>
        
        <h2>🧪 Test de Detección Automática:</h2>
        
        <div class="test-item">
            <button onclick="testDetection()">Probar Detección</button>
            <button onclick="testAssets()">Probar Carga de Assets</button>
            <button onclick="showConsole()">Mostrar Consola</button>
        </div>
        
        <div id="test-results"></div>
        
        <h2>📋 Información del Sistema:</h2>
        
        <div class="test-item info">
            <strong>Servidor:</strong> <?php echo $_SERVER['SERVER_NAME'] ?? 'N/A'; ?>
        </div>
        
        <div class="test-item info">
            <strong>Puerto:</strong> <?php echo $_SERVER['SERVER_PORT'] ?? 'N/A'; ?>
        </div>
        
        <div class="test-item info">
            <strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'N/A'; ?>
        </div>
        
        <div class="test-item info">
            <strong>Script Name:</strong> <?php echo $_SERVER['SCRIPT_NAME'] ?? 'N/A'; ?>
        </div>
        
        <div class="test-item info">
            <strong>Request URI:</strong> <?php echo $_SERVER['REQUEST_URI'] ?? 'N/A'; ?>
        </div>
        
        <h2>🔧 Solución Implementada:</h2>
        
        <div class="test-item success">
            <strong>✅ Detección Automática:</strong> El sistema ahora detecta automáticamente la URL base correcta basándose en la ruta actual del navegador.
        </div>
        
        <div class="test-item success">
            <strong>✅ Fallback Inteligente:</strong> Si una ruta falla, el sistema intenta con rutas alternativas automáticamente.
        </div>
        
        <div class="test-item success">
            <strong>✅ Logging Detallado:</strong> La consola del navegador mostrará exactamente qué rutas se están probando.
        </div>
        
        <div class="test-item warning">
            <strong>⚠️ Próximo Paso:</strong> Después de verificar que funciona, limpiar la caché del navegador y probar la página de clanes.
        </div>
    </div>
    
    <script>
        // Mostrar información actual
        document.getElementById('current-url').textContent = window.location.href;
        
        // Función para probar la detección
        function testDetection() {
            const results = document.getElementById('test-results');
            results.innerHTML = '<div class="test-item info">🧪 Probando detección automática...</div>';
            
            // Simular la detección
            const currentPath = window.location.pathname;
            let baseUrl = '';
            
            if (currentPath.includes('/rinotrack/public/')) {
                baseUrl = '/rinotrack/public/';
            } else if (currentPath.includes('/public/')) {
                baseUrl = '/public/';
            } else if (currentPath === '/' || currentPath === '') {
                baseUrl = '/';
            } else {
                baseUrl = currentPath.endsWith('/') ? currentPath : currentPath + '/';
            }
            
            document.getElementById('detected-url').textContent = baseUrl;
            
            results.innerHTML = `
                <div class="test-item success">
                    <strong>✅ Detección exitosa:</strong> URL base detectada: <code>${baseUrl}</code>
                </div>
                <div class="test-item info">
                    <strong>📁 Rutas que se probarán:</strong>
                </div>
                <div class="code">
                    CSS: ${baseUrl}assets/css/theme.css<br>
                    CSS: ${baseUrl}assets/css/admin.css<br>
                    JS: ${baseUrl}assets/js/script.js
                </div>
            `;
        }
        
        // Función para probar assets
        function testAssets() {
            const results = document.getElementById('test-results');
            results.innerHTML = '<div class="test-item info">🧪 Probando carga de assets...</div>';
            
            // Probar cargar un CSS
            const testCSS = document.createElement('link');
            testCSS.rel = 'stylesheet';
            testCSS.href = './assets/css/theme.css';
            
            testCSS.onload = function() {
                results.innerHTML += '<div class="test-item success">✅ CSS cargado correctamente con ruta relativa</div>';
            };
            
            testCSS.onerror = function() {
                results.innerHTML += '<div class="test-item error">❌ CSS no se pudo cargar con ruta relativa</div>';
            };
            
            document.head.appendChild(testCSS);
        }
        
        // Función para mostrar consola
        function showConsole() {
            alert('Abre las herramientas de desarrollo (F12) y ve a la pestaña Console para ver los logs detallados de la detección automática.');
        }
        
        // Log inicial
        console.log('=== TEST ASSETS - DETECCIÓN AUTOMÁTICA ===');
        console.log('URL actual:', window.location.href);
        console.log('Path actual:', window.location.pathname);
        console.log('APP_URL desde PHP:', '<?php echo APP_URL; ?>');
    </script>
</body>
</html>
