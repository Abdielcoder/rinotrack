<?php
/**
 * Visor universal de archivos con popup
 */

// Configuración de seguridad
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Auth.php';
require_once __DIR__ . '/../app/models/Subtask.php';

// Verificar autenticación
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    http_response_code(403);
    die('Acceso denegado');
}

$attachmentId = $_GET['id'] ?? '';
$action = $_GET['action'] ?? 'view'; // view, download, info

if (empty($attachmentId)) {
    http_response_code(400);
    die('ID de adjunto no especificado');
}

try {
    // Obtener información del adjunto desde la base de datos
    $db = Database::getConnection();
    $stmt = $db->prepare("
        SELECT sa.*, s.title as subtask_title, u.full_name as uploaded_by_name
        FROM Subtask_Attachments sa
        JOIN Subtasks s ON sa.subtask_id = s.subtask_id
        JOIN Users u ON sa.user_id = u.user_id
        WHERE sa.attachment_id = ?
    ");
    $stmt->execute([$attachmentId]);
    $attachment = $stmt->fetch();
    
    if (!$attachment) {
        http_response_code(404);
        die('Archivo no encontrado');
    }
    
    // Buscar el archivo en diferentes ubicaciones posibles
    $possiblePaths = [
        __DIR__ . '/' . $attachment['file_path'], // Ruta relativa desde public
        $attachment['file_path'], // Ruta absoluta
        __DIR__ . '/uploads/task_attachments/' . basename($attachment['file_path']),
        __DIR__ . '/uploads/' . basename($attachment['file_path']),
        sys_get_temp_dir() . '/rinotrack_uploads/' . basename($attachment['file_path'])
    ];
    
    $filePath = null;
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $filePath = $path;
            break;
        }
    }
    
    if (!$filePath) {
        http_response_code(404);
        die('Archivo físico no encontrado');
    }
    
    $filesize = filesize($filePath);
    $mimetype = $attachment['file_type'] ?: mime_content_type($filePath) ?: 'application/octet-stream';
    $filename = $attachment['file_name'];
    
    // Determinar si es visualizable
    $isImage = strpos($mimetype, 'image/') === 0;
    $isPdf = $mimetype === 'application/pdf';
    $isText = strpos($mimetype, 'text/') === 0;
    $isViewable = $isImage || $isPdf || $isText;
    
    if ($action === 'info') {
        // Retornar información del archivo como JSON
        header('Content-Type: application/json');
        echo json_encode([
            'id' => $attachment['attachment_id'],
            'name' => $filename,
            'size' => $filesize,
            'type' => $mimetype,
            'is_viewable' => $isViewable,
            'is_image' => $isImage,
            'is_pdf' => $isPdf,
            'is_text' => $isText,
            'uploaded_by' => $attachment['uploaded_by_name'],
            'uploaded_at' => $attachment['uploaded_at'],
            'subtask' => $attachment['subtask_title']
        ]);
        exit;
    }
    
    if ($action === 'download' || !$isViewable) {
        // Forzar descarga
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . $filesize);
        header('Cache-Control: private, max-age=0');
        readfile($filePath);
        exit;
    }
    
    // Visualización en línea
    if ($isImage) {
        header('Content-Type: ' . $mimetype);
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Length: ' . $filesize);
        header('Cache-Control: public, max-age=3600');
        readfile($filePath);
        exit;
    }
    
    if ($isPdf) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Length: ' . $filesize);
        header('Cache-Control: public, max-age=3600');
        readfile($filePath);
        exit;
    }
    
    if ($isText && $filesize < 1024 * 1024) { // Máximo 1MB para texto
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Length: ' . $filesize);
        readfile($filePath);
        exit;
    }
    
    // Si llegamos aquí, forzar descarga
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . $filesize);
    readfile($filePath);
    
} catch (Exception $e) {
    error_log("Error en file-viewer: " . $e->getMessage());
    http_response_code(500);
    die('Error interno del servidor');
}
?>
