<?php
// Obtener el archivo de la base de datos
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? 0;
if (!$id) die('No ID');

$db = Database::getConnection();
$stmt = $db->prepare("SELECT * FROM Subtask_Attachments WHERE attachment_id = ?");
$stmt->execute([$id]);
$file = $stmt->fetch();

if (!$file) die('Not found');

// Buscar el archivo en el servidor
$paths = [
    __DIR__ . '/uploads/' . $file['file_path'],
    __DIR__ . '/uploads/task_attachments/' . $file['file_path'],
    __DIR__ . '/' . $file['file_path']
];

$found = null;
foreach ($paths as $path) {
    if (file_exists($path)) {
        $found = $path;
        break;
    }
}

if (!$found) {
    // Si no se encuentra, buscar cualquier archivo que coincida con el patrón
    $pattern = '*' . substr($file['file_path'], -20) . '*';
    $files = glob(__DIR__ . '/uploads/' . $pattern);
    if (!empty($files)) {
        $found = $files[0];
    }
}

if (!$found) {
    $files = glob(__DIR__ . '/uploads/task_attachments/' . $pattern);
    if (!empty($files)) {
        $found = $files[0];
    }
}

if (!$found) die('File not found');

// Servir el archivo
$mime = mime_content_type($found);
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($found));
readfile($found);
?>