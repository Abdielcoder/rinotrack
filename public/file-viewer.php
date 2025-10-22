<?php
// Obtener el archivo de la base de datos
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? 0;
if (!$id) die('No ID');

$db = Database::getConnection();

// Buscar en ambas tablas y usar el que realmente existe
$file = null;

// Intentar primero con Task_Attachments
$stmt = $db->prepare("SELECT * FROM Task_Attachments WHERE attachment_id = ?");
$stmt->execute([$id]);
$taskFile = $stmt->fetch();

// Intentar con Subtask_Attachments
$stmt = $db->prepare("SELECT * FROM Subtask_Attachments WHERE attachment_id = ?");
$stmt->execute([$id]);
$subtaskFile = $stmt->fetch();

// Usar el que realmente existe físicamente
if ($taskFile && $subtaskFile) {
    // Ambos existen, verificar cuál archivo físico existe
    $taskPath = __DIR__ . '/' . $taskFile['file_path'];
    $subtaskPath = __DIR__ . '/' . $subtaskFile['file_path'];
    
    $taskExists = file_exists($taskPath);
    $subtaskExists = file_exists($subtaskPath);
    
    if ($taskExists && $subtaskExists) {
        // Ambos existen físicamente, usar el más reciente
        $file = $taskFile['uploaded_at'] > $subtaskFile['uploaded_at'] ? $taskFile : $subtaskFile;
    } elseif ($taskExists) {
        $file = $taskFile;
    } elseif ($subtaskExists) {
        $file = $subtaskFile;
    } else {
        // Ninguno existe físicamente, usar el más reciente
        $file = $taskFile['uploaded_at'] > $subtaskFile['uploaded_at'] ? $taskFile : $subtaskFile;
    }
} elseif ($taskFile) {
    $file = $taskFile;
} elseif ($subtaskFile) {
    $file = $subtaskFile;
}

if (!$file) die('Not found');

// Buscar el archivo en el servidor
$filePath = $file['file_path'];

// Si la ruta ya incluye 'uploads/', usarla directamente
if (strpos($filePath, 'uploads/') === 0) {
    $paths = [
        __DIR__ . '/' . $filePath,
        __DIR__ . '/uploads/' . str_replace('uploads/', '', $filePath),
        __DIR__ . '/uploads/task_attachments/' . str_replace('uploads/', '', $filePath)
    ];
} else {
    // Si no incluye 'uploads/', buscar en las ubicaciones estándar
    $paths = [
        __DIR__ . '/uploads/' . $filePath,
        __DIR__ . '/uploads/task_attachments/' . $filePath,
        __DIR__ . '/' . $filePath
    ];
}

$found = null;
foreach ($paths as $path) {
    if (file_exists($path)) {
        $found = $path;
        break;
    }
}


// Solo buscar con patrón si no se encontró en la búsqueda principal
if (!$found) {
    // Si no se encuentra, buscar cualquier archivo que coincida con el patrón
    $pattern = '*' . substr($file['file_path'], -20) . '*';
    $files = glob(__DIR__ . '/uploads/' . $pattern);
    if (!empty($files)) {
        $found = $files[0];
    }
    
    if (!$found) {
        $files = glob(__DIR__ . '/uploads/task_attachments/' . $pattern);
        if (!empty($files)) {
            $found = $files[0];
        }
    }
}

if (!$found) die('File not found');

// Servir el archivo
$mime = mime_content_type($found);

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($found));
readfile($found);
?>