<?php
// Script de prueba para verificar que los assets se cargan correctamente
require_once '../config/app.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test de Assets</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .test-item { margin: 10px 0; padding: 10px; border: 1px solid #ccc; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <h1>🔍 Test de Carga de Assets</h1>
    
    <div class="test-item info">
        <strong>APP_URL configurada:</strong> <?php echo APP_URL; ?>
    </div>
    
    <h2>📁 Verificación de Archivos CSS:</h2>
    
    <?php
    $cssFiles = [
        'theme.css' => APP_URL . 'assets/css/theme.css',
        'admin.css' => APP_URL . 'assets/css/admin.css'
    ];
    
    foreach ($cssFiles as $filename => $url) {
        $filePath = '../public/assets/css/' . $filename;
        if (file_exists($filePath)) {
            echo "<div class='test-item success'>✅ {$filename} existe en el servidor</div>";
        } else {
            echo "<div class='test-item error'>❌ {$filename} NO existe en el servidor</div>";
        }
    }
    ?>
    
    <h2>📁 Verificación de Archivos JS:</h2>
    
    <?php
    $jsFiles = [
        'script.js' => APP_URL . 'assets/js/script.js'
    ];
    
    foreach ($jsFiles as $filename => $url) {
        $filePath = '../public/assets/js/' . $filename;
        if (file_exists($filePath)) {
            echo "<div class='test-item success'>✅ {$filename} existe en el servidor</div>";
        } else {
            echo "<div class='test-item error'>❌ {$filename} NO existe en el servidor</div>";
        }
    }
    ?>
    
    <h2>🔗 URLs de Assets:</h2>
    
    <div class="test-item info">
        <strong>theme.css:</strong> <a href="<?php echo APP_URL; ?>assets/css/theme.css" target="_blank"><?php echo APP_URL; ?>assets/css/theme.css</a>
    </div>
    
    <div class="test-item info">
        <strong>admin.css:</strong> <a href="<?php echo APP_URL; ?>assets/css/admin.css" target="_blank"><?php echo APP_URL; ?>assets/css/admin.css</a>
    </div>
    
    <div class="test-item info">
        <strong>script.js:</strong> <a href="<?php echo APP_URL; ?>assets/js/script.js" target="_blank"><?php echo APP_URL; ?>assets/js/script.js</a>
    </div>
    
    <h2>🧪 Test de Carga:</h2>
    
    <div class="test-item">
        <button onclick="testJS()">Test JavaScript</button>
        <div id="js-test-result"></div>
    </div>
    
    <script>
        function testJS() {
            document.getElementById('js-test-result').innerHTML = '<div class="test-item success">✅ JavaScript funcionando correctamente</div>';
        }
        
        // Verificar que APP_URL esté disponible
        console.log('APP_URL desde JavaScript:', '<?php echo APP_URL; ?>');
    </script>
    
    <h2>📋 Resumen:</h2>
    
    <div class="test-item info">
        <strong>Problema identificado:</strong> Los archivos existen en el servidor pero las URLs no se están resolviendo correctamente.
    </div>
    
    <div class="test-item info">
        <strong>Solución aplicada:</strong> APP_URL ahora apunta a <code><?php echo APP_URL; ?></code>
    </div>
    
    <div class="test-item info">
        <strong>Próximo paso:</strong> Limpiar caché del navegador y probar nuevamente.
    </div>
</body>
</html>
