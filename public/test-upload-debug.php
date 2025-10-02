<?php
/**
 * Script de debug para el upload de subtask attachments
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Headers para JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Log de inicio
error_log('=== DEBUG UPLOAD SUBTASK ATTACHMENT - INICIO ===');
error_log('Método HTTP: ' . $_SERVER['REQUEST_METHOD']);
error_log('URL: ' . $_SERVER['REQUEST_URI']);

try {
    // Incluir configuraciones
    require_once __DIR__ . '/../config/app.php';
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../app/models/Utils.php';
    require_once __DIR__ . '/../app/models/User.php';
    require_once __DIR__ . '/../app/models/Auth.php';
    require_once __DIR__ . '/../app/models/Subtask.php';

    error_log('✅ Configuraciones cargadas correctamente');

    // Verificar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido. Se requiere POST.');
    }

    // Log de datos recibidos
    error_log('POST data: ' . print_r($_POST, true));
    error_log('FILES data: ' . print_r($_FILES, true));

    // Verificar datos requeridos
    $subtaskId = $_POST['subtask_id'] ?? null;
    $description = trim($_POST['description'] ?? '');

    error_log('Subtask ID: ' . ($subtaskId ?? 'NULL'));
    error_log('Description: ' . $description);

    if (!$subtaskId) {
        throw new Exception('Subtask ID es requerido');
    }

    if (!isset($_FILES['file'])) {
        throw new Exception('Archivo es requerido');
    }

    $file = $_FILES['file'];
    error_log('Archivo recibido: ' . print_r($file, true));

    // Verificar errores del archivo
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'El archivo excede upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
            UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta directorio temporal',
            UPLOAD_ERR_CANT_WRITE => 'Error escribiendo archivo al disco',
            UPLOAD_ERR_EXTENSION => 'Extensión PHP detuvo la subida'
        ];
        throw new Exception('Error en upload: ' . ($errorMessages[$file['error']] ?? 'Error desconocido'));
    }

    // Verificar conexión a base de datos
    $db = Database::getConnection();
    error_log('✅ Conexión a BD establecida');

    // Verificar que existe la subtarea
    $stmt = $db->prepare("SELECT subtask_id, title FROM Subtasks WHERE subtask_id = ?");
    $stmt->execute([$subtaskId]);
    $subtask = $stmt->fetch();
    
    if (!$subtask) {
        throw new Exception('Subtarea no encontrada');
    }
    
    error_log('✅ Subtarea encontrada: ' . $subtask['title']);

    // Verificar directorio uploads
    $uploadsDir = __DIR__ . '/uploads/';
    error_log('Directorio uploads: ' . $uploadsDir);
    
    if (!file_exists($uploadsDir)) {
        error_log('Creando directorio uploads...');
        if (!mkdir($uploadsDir, 0755, true)) {
            throw new Exception('No se pudo crear directorio uploads');
        }
    }
    
    if (!is_writable($uploadsDir)) {
        throw new Exception('Directorio uploads no es escribible');
    }
    
    error_log('✅ Directorio uploads OK');

    // Intentar guardar archivo
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('test_subtask_' . $subtaskId . '_') . '.' . $extension;
    $filepath = $uploadsDir . $filename;
    
    error_log('Intentando mover archivo de ' . $file['tmp_name'] . ' a ' . $filepath);
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Error moviendo archivo subido');
    }
    
    error_log('✅ Archivo guardado exitosamente');

    // Intentar insertar en base de datos
    $stmt = $db->prepare("
        INSERT INTO Subtask_Attachments (subtask_id, user_id, comment_id, file_name, file_path, file_size, file_type, description) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $userId = 1; // Usuario de prueba
    $commentId = null;
    $publicPath = APP_URL . 'uploads/' . $filename;
    
    $result = $stmt->execute([
        $subtaskId, 
        $userId, 
        $commentId, 
        $file['name'], 
        $publicPath, 
        $file['size'], 
        $file['type'], 
        $description
    ]);
    
    if (!$result) {
        throw new Exception('Error insertando en base de datos');
    }
    
    $attachmentId = $db->lastInsertId();
    error_log('✅ Registro insertado con ID: ' . $attachmentId);

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Archivo subido exitosamente (modo debug)',
        'attachment_id' => $attachmentId,
        'file_info' => [
            'original_name' => $file['name'],
            'saved_name' => $filename,
            'public_path' => $publicPath,
            'file_size' => $file['size'],
            'file_type' => $file['type']
        ],
        'debug_info' => [
            'uploads_dir' => $uploadsDir,
            'uploads_writable' => is_writable($uploadsDir),
            'file_exists' => file_exists($filepath),
            'subtask_found' => true
        ]
    ]);

} catch (Exception $e) {
    error_log('❌ ERROR: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug_info' => [
            'uploads_dir' => $uploadsDir ?? 'N/A',
            'uploads_exists' => isset($uploadsDir) ? file_exists($uploadsDir) : false,
            'uploads_writable' => isset($uploadsDir) ? is_writable($uploadsDir) : false,
            'post_data' => $_POST,
            'files_data' => $_FILES
        ]
    ]);
}
?>
