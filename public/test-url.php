<?php
/**
 * Archivo de prueba para verificar URLs y rutas
 */

echo "<h1>🔍 Test de URLs y Rutas</h1>";

echo "<h2>Información del Servidor:</h2>";
echo "<p><strong>SERVER_NAME:</strong> " . ($_SERVER['SERVER_NAME'] ?? 'N/A') . "</p>";
echo "<p><strong>SERVER_PORT:</strong> " . ($_SERVER['SERVER_PORT'] ?? 'N/A') . "</p>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "</p>";
echo "<p><strong>DOCUMENT_ROOT:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "</p>";

echo "<h2>URLs de Prueba:</h2>";

$baseUrls = [
    'https://rinotrack.rinorisk.com/',
    'https://rinotrack.rinorisk.com/public/',
    'https://rinotrack.rinorisk.com/rinotrack/public/',
    'https://rinotrack.rinorisk.com/desarrollo/rinotrack/public/'
];

foreach ($baseUrls as $baseUrl) {
    $testUrl = $baseUrl . 'create-clan-direct.php';
    echo "<p><strong>$baseUrl</strong> → <a href='$testUrl' target='_blank'>$testUrl</a></p>";
}

echo "<h2>Archivos en el Directorio:</h2>";

$files = [
    'create-clan-direct.php',
    'test-clan.php',
    'test-assets.php',
    'index.php'
];

foreach ($files as $file) {
    $filePath = __DIR__ . '/' . $file;
    if (file_exists($filePath)) {
        echo "<p style='color: green;'>✅ $file existe en " . dirname($filePath) . "</p>";
    } else {
        echo "<p style='color: red;'>❌ $file NO existe</p>";
    }
}

echo "<h2>Test de Acceso Directo:</h2>";
echo "<p>Si puedes ver este archivo, significa que el acceso directo a archivos PHP está funcionando.</p>";

echo "<h2>JavaScript Test:</h2>";
?>
<script>
console.log('=== TEST URL DEBUG ===');
console.log('window.location.href:', window.location.href);
console.log('window.location.pathname:', window.location.pathname);
console.log('window.location.origin:', window.location.origin);

// Función para construir URL
function buildFileUrl(filename) {
    const currentUrl = window.location.href;
    const urlObj = new URL(currentUrl);
    
    let basePath = urlObj.pathname;
    
    if (basePath.includes('?')) {
        basePath = basePath.split('?')[0];
    }
    
    if (basePath.includes('.php')) {
        basePath = basePath.substring(0, basePath.lastIndexOf('/') + 1);
    }
    
    const fileUrl = urlObj.origin + basePath + filename;
    console.log('URL construida para', filename, ':', fileUrl);
    
    return fileUrl;
}

// Probar la función
const testUrl = buildFileUrl('create-clan-direct.php');
console.log('URL de prueba:', testUrl);

// Crear enlaces de prueba
document.addEventListener('DOMContentLoaded', function() {
    const testDiv = document.createElement('div');
    testDiv.innerHTML = `
        <h3>Enlaces de Prueba:</h3>
        <p><a href="${testUrl}" target="_blank">Probar: ${testUrl}</a></p>
        <p><a href="./create-clan-direct.php" target="_blank">Ruta relativa: ./create-clan-direct.php</a></p>
        <p><a href="/rinotrack/public/create-clan-direct.php" target="_blank">Ruta absoluta: /rinotrack/public/create-clan-direct.php</a></p>
    `;
    document.body.appendChild(testDiv);
});
</script>
