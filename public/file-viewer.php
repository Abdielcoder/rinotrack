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
    $filename = basename($attachment['file_path']);
    
    // Si el file_path es una URL completa, extraer solo el nombre del archivo
    if (strpos($attachment['file_path'], 'http') === 0) {
        $filename = basename(parse_url($attachment['file_path'], PHP_URL_PATH));
    }
    
    $possiblePaths = [
        // Rutas desde el directorio public (más comunes)
        __DIR__ . '/uploads/task_attachments/' . $filename,
        __DIR__ . '/uploads/' . $filename,
        
        // Rutas temporales
        sys_get_temp_dir() . '/rinotrack_uploads/' . $filename,
        '/tmp/rinotrack_uploads/' . $filename,
        
        // Rutas absolutas del file_path (si no es URL)
        strpos($attachment['file_path'], 'http') !== 0 ? $attachment['file_path'] : null,
        
        // Buscar en subdirectorios comunes
        __DIR__ . '/uploads/subtask_attachments/' . $filename,
        __DIR__ . '/uploads/files/' . $filename
    ];
    
    // Filtrar rutas nulas
    $possiblePaths = array_filter($possiblePaths);
    
    $filePath = null;
    foreach ($possiblePaths as $path) {
        error_log("Buscando archivo en: " . $path);
        if (file_exists($path) && is_readable($path)) {
            $filePath = $path;
            error_log("Archivo encontrado en: " . $path);
            break;
        }
    }
    
    if (!$filePath) {
        error_log("Archivo no encontrado. File_path en DB: " . $attachment['file_path']);
        error_log("Filename extraído: " . $filename);
        http_response_code(404);
        die('Archivo físico no encontrado en el servidor');
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
        error_log("📸 Sirviendo imagen: " . $filename . " (" . $mimetype . ")");
        error_log("📂 Desde: " . $filePath);
        error_log("📦 Tamaño: " . $filesize . " bytes");
        
        header('Content-Type: ' . $mimetype);
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Length: ' . $filesize);
        header('Cache-Control: public, max-age=3600');
        header('Access-Control-Allow-Origin: *');
        
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
