<?php
/**
 * Visor universal de archivos - SISTEMA COMPLETAMENTE RENOVADO
 * Versión 2.0 - Simplificado y robusto
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
    error_log("🔍 file-viewer.php - Buscando attachment ID: $attachmentId");
    
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
        error_log("❌ Attachment no encontrado en BD: $attachmentId");
        http_response_code(404);
        die('Archivo no encontrado en la base de datos');
    }
    
    error_log("✅ Attachment encontrado: " . $attachment['file_name']);
    error_log("📁 File path en BD: " . $attachment['file_path']);
    
    // Determinar la ruta del archivo
    $filename = $attachment['file_path'];
    
    // Si el file_path es una URL completa, extraer solo el nombre del archivo
    if (strpos($filename, 'http') === 0) {
        $filename = basename(parse_url($filename, PHP_URL_PATH));
        error_log("🌐 URL detectada, filename extraído: $filename");
    }
    
    // Directorio principal de archivos
    $uploadsDir = __DIR__ . '/uploads/task_attachments/';
    
    // Ruta completa del archivo
    $filePath = $uploadsDir . $filename;
    
    error_log("🔍 Buscando archivo en: $filePath");
    
    // Verificar si el archivo existe
    if (!file_exists($filePath)) {
        error_log("❌ Archivo no encontrado en: $filePath");
        
        // Intentar en el directorio uploads principal
        $altPath = __DIR__ . '/uploads/' . $filename;
        error_log("🔍 Intentando en directorio alternativo: $altPath");
        
        if (file_exists($altPath)) {
            $filePath = $altPath;
            error_log("✅ Archivo encontrado en directorio alternativo");
        } else {
            error_log("❌ Archivo no encontrado en ningún directorio");
            http_response_code(404);
            die('Archivo físico no encontrado en el servidor');
        }
    } else {
        error_log("✅ Archivo encontrado en ubicación principal");
    }
    
    // Verificar que el archivo es legible
    if (!is_readable($filePath)) {
        error_log("❌ Archivo no es legible: $filePath");
        http_response_code(403);
        die('Archivo no accesible');
    }
    
    $filesize = filesize($filePath);
    $mimetype = $attachment['file_type'] ?: mime_content_type($filePath) ?: 'application/octet-stream';
    $filename = $attachment['file_name'];
    
    error_log("📊 Archivo info - Tamaño: $filesize bytes, Tipo: $mimetype");
    
    // Determinar si es visualizable
    $isImage = strpos($mimetype, 'image/') === 0;
    $isPdf = $mimetype === 'application/pdf';
    $isText = strpos($mimetype, 'text/') === 0;
    $isViewable = $isImage || $isPdf || $isText;
    
    error_log("🎯 Tipo de archivo - Imagen: " . ($isImage ? 'Sí' : 'No') . ", PDF: " . ($isPdf ? 'Sí' : 'No') . ", Texto: " . ($isText ? 'Sí' : 'No'));
    
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
        error_log("📥 Forzando descarga del archivo");
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . $filesize);
        header('Cache-Control: private, max-age=0');
        readfile($filePath);
        exit;
    }
    
    // Visualización en línea
    if ($isImage) {
        error_log("🖼️ Sirviendo imagen: $filename ($mimetype)");
        header('Content-Type: ' . $mimetype);
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Length: ' . $filesize);
        header('Cache-Control: public, max-age=3600');
        header('Access-Control-Allow-Origin: *');
        
        readfile($filePath);
        exit;
    }
    
    if ($isPdf) {
        error_log("📄 Sirviendo PDF: $filename");
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Length: ' . $filesize);
        header('Cache-Control: public, max-age=3600');
        readfile($filePath);
        exit;
    }
    
    if ($isText && $filesize < 1024 * 1024) { // Máximo 1MB para texto
        error_log("📝 Sirviendo texto: $filename");
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Length: ' . $filesize);
        readfile($filePath);
        exit;
    }
    
    // Si llegamos aquí, forzar descarga
    error_log("📥 Forzando descarga (fallback)");
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . $filesize);
    readfile($filePath);
    
} catch (Exception $e) {
    error_log("❌ Error en file-viewer: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    die('Error interno del servidor');
}
?>