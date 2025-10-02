<?php
/**
 * Script de prueba para file-viewer.php
 */

// Simular una petición GET
$_GET['id'] = '95';
$_GET['action'] = 'view';

echo "<h1>Prueba de file-viewer.php</h1>";
echo "<p>Probando con attachment_id: 95</p>";

// Capturar la salida
ob_start();
include 'file-viewer.php';
$output = ob_get_clean();

echo "<h2>Resultado:</h2>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

echo "<h2>Headers enviados:</h2>";
echo "<pre>";
print_r(headers_list());
echo "</pre>";
?>
