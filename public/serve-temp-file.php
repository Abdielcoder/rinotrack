<?php
/**
 * Servir archivos desde directorio temporal cuando el directorio uploads no es escribible
 */

// Configuración de seguridad
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Auth.php';

// Verificar autenticación
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    http_response_code(403);
    die('Acceso denegado');
}

$filename = $_GET['file'] ?? '';
if (empty($filename)) {
    http_response_code(400);
    die('Archivo no especificado');
}

// Validar nombre de archivo para prevenir ataques de path traversal
if (preg_match('/[^a-zA-Z0-9._-]/', $filename) || strpos($filename, '..') !== false) {
    http_response_code(400);
    die('Nombre de archivo inválido');
}

// Buscar el archivo en el directorio temporal
$tempDir = sys_get_temp_dir() . '/rinotrack_uploads/';
$filepath = $tempDir . $filename;

if (!file_exists($filepath)) {
    http_response_code(404);
    die('Archivo no encontrado');
}

// Verificar que el usuario tiene permisos para ver este archivo
// (aquí podrías agregar lógica adicional de verificación de permisos)

// Obtener información del archivo
$filesize = filesize($filepath);
$mimetype = mime_content_type($filepath) ?: 'application/octet-stream';

// Configurar headers para descarga
header('Content-Type: ' . $mimetype);
header('Content-Length: ' . $filesize);
header('Content-Disposition: inline; filename="' . basename($filename) . '"');
header('Cache-Control: private, max-age=3600');

// Servir el archivo
readfile($filepath);
exit;
?>
