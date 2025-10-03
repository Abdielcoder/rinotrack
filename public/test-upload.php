<?php
/**
 * SCRIPT DE PRUEBA DE UPLOAD
 */

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['test_file'])) {
    die('No file uploaded');
}

$file = $_FILES['test_file'];

// Directorio de uploads
$uploadDir = __DIR__ . '/uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Generar nombre único
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$uniqueName = 'test_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
$targetPath = $uploadDir . $uniqueName;

// Mover archivo
if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo "<h1>Archivo subido exitosamente</h1>";
    echo "<p>Nombre original: " . $file['name'] . "</p>";
    echo "<p>Nombre guardado: " . $uniqueName . "</p>";
    echo "<p>Tamaño: " . $file['size'] . " bytes</p>";
    echo "<p>Tipo: " . $file['type'] . "</p>";
    
    // Guardar en BD
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO Subtask_Attachments 
            (subtask_id, user_id, file_name, file_path, file_size, file_type, description) 
            VALUES (569, 18, ?, ?, ?, ?, 'Test upload')
        ");
        $stmt->execute([$file['name'], $uniqueName, $file['size'], $file['type']]);
        $id = $db->lastInsertId();
        
        echo "<h2>Guardado en BD con ID: $id</h2>";
        echo "<p><a href='file-viewer.php?id=$id' target='_blank'>Ver archivo</a></p>";
        
        if (strpos($file['type'], 'image/') === 0) {
            echo "<h2>Vista previa:</h2>";
            echo "<img src='file-viewer.php?id=$id' style='max-width: 400px; border: 1px solid #000;'>";
        }
        
    } catch (Exception $e) {
        echo "<p>Error BD: " . $e->getMessage() . "</p>";
    }
    
} else {
    echo "<h1>Error al subir archivo</h1>";
}

echo "<p><a href='test-simple.php'>Volver</a></p>";
?>
