<?php
/**
 * Script para verificar configuración PHP relevante para uploads
 */

header('Content-Type: application/json');

$config = [
    'file_uploads' => ini_get('file_uploads'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_execution_time' => ini_get('max_execution_time'),
    'memory_limit' => ini_get('memory_limit'),
    'upload_tmp_dir' => ini_get('upload_tmp_dir') ?: sys_get_temp_dir(),
    'max_file_uploads' => ini_get('max_file_uploads'),
    'display_errors' => ini_get('display_errors'),
    'log_errors' => ini_get('log_errors'),
    'error_log' => ini_get('error_log'),
    'php_version' => phpversion(),
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'
];

// Verificar directorio uploads
$uploadsDir = __DIR__ . '/uploads/';
$config['uploads_dir'] = $uploadsDir;
$config['uploads_exists'] = file_exists($uploadsDir);
$config['uploads_writable'] = is_writable($uploadsDir);
$config['uploads_permissions'] = $config['uploads_exists'] ? substr(sprintf('%o', fileperms($uploadsDir)), -4) : 'N/A';

// Verificar directorio temporal
$tmpDir = $config['upload_tmp_dir'];
$config['tmp_dir_exists'] = file_exists($tmpDir);
$config['tmp_dir_writable'] = is_writable($tmpDir);

// Verificar extensiones necesarias
$config['extensions'] = [
    'pdo' => extension_loaded('pdo'),
    'pdo_mysql' => extension_loaded('pdo_mysql'),
    'fileinfo' => extension_loaded('fileinfo'),
    'json' => extension_loaded('json')
];

// Verificar constantes de la aplicación
$config['app_constants'] = [];
if (file_exists(__DIR__ . '/../config/app.php')) {
    require_once __DIR__ . '/../config/app.php';
    $config['app_constants'] = [
        'APP_URL' => defined('APP_URL') ? APP_URL : 'NOT_DEFINED',
        'APP_DEBUG' => defined('APP_DEBUG') ? APP_DEBUG : 'NOT_DEFINED'
    ];
}

echo json_encode($config, JSON_PRETTY_PRINT);
?>
