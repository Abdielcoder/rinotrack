<?php
// Script para verificar que la salida está limpia
ini_set('display_errors', 0);
error_reporting(0);

// Limpiar todos los buffers de salida
while (ob_get_level()) {
    ob_end_clean();
}

// Incluir el index principal
$_GET['route'] = 'clan_leader/projects';
require_once __DIR__ . '/index.php';
?>
