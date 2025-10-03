<?php
/**
 * SISTEMA ULTRA SIMPLE DE VISUALIZACIÓN DE ARCHIVOS
 */

require_once __DIR__ . '/../config/database.php';

// Obtener ID del archivo
$id = $_GET['id'] ?? 0;

if (!$id) {
    die('No ID');
}

try {
    // Conectar a la base de datos
    $db = Database::getConnection();
    
    // Obtener información del archivo
    $stmt = $db->prepare("SELECT * FROM Subtask_Attachments WHERE attachment_id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch();
    
    if (!$file) {
        die('File not found in DB');
    }
    
    // Ruta del archivo
    $uploadDir = dirname(__DIR__) . '/public/uploads/';
    $filePath = $uploadDir . $file['file_path'];
    
    // Verificar si existe
    if (!file_exists($filePath)) {
        die('File not found on disk: ' . $filePath);
    }
    
    // Obtener tipo MIME
    $mimeType = $file['file_type'] ?: mime_content_type($filePath);
    
    // Enviar headers
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . filesize($filePath));
    
    // Si es imagen, mostrar inline
    if (strpos($mimeType, 'image/') === 0) {
        header('Content-Disposition: inline; filename="' . $file['file_name'] . '"');
    } else {
        // Si no es imagen, descargar
        header('Content-Disposition: attachment; filename="' . $file['file_name'] . '"');
    }
    
    // Enviar archivo
    readfile($filePath);
    
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>