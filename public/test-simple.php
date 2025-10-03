<?php
/**
 * PRUEBA ULTRA SIMPLE
 */

require_once __DIR__ . '/../config/database.php';

echo "<h1>Test Simple de Archivos</h1>";

// Verificar directorio
$uploadDir = __DIR__ . '/uploads/';
echo "<h2>Directorio de uploads:</h2>";
echo "<p>Path: $uploadDir</p>";
echo "<p>Existe: " . (file_exists($uploadDir) ? "SI" : "NO") . "</p>";

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
    echo "<p>Directorio creado</p>";
}

// Listar archivos
echo "<h2>Archivos en el directorio:</h2>";
$files = glob($uploadDir . '*');
if (empty($files)) {
    echo "<p>No hay archivos</p>";
} else {
    echo "<ul>";
    foreach ($files as $file) {
        echo "<li>" . basename($file) . " - " . filesize($file) . " bytes</li>";
    }
    echo "</ul>";
}

// Ver archivos en BD
echo "<h2>Archivos en Base de Datos:</h2>";
try {
    $db = Database::getConnection();
    $stmt = $db->query("SELECT * FROM Subtask_Attachments ORDER BY attachment_id DESC LIMIT 5");
    $attachments = $stmt->fetchAll();
    
    if (empty($attachments)) {
        echo "<p>No hay archivos en BD</p>";
    } else {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Path</th><th>Tipo</th><th>Ver</th></tr>";
        foreach ($attachments as $att) {
            echo "<tr>";
            echo "<td>{$att['attachment_id']}</td>";
            echo "<td>{$att['file_name']}</td>";
            echo "<td>{$att['file_path']}</td>";
            echo "<td>{$att['file_type']}</td>";
            echo "<td><a href='file-viewer.php?id={$att['attachment_id']}' target='_blank'>Ver</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Probar con imagen
    $lastImage = null;
    foreach ($attachments as $att) {
        if (strpos($att['file_type'], 'image/') === 0) {
            $lastImage = $att;
            break;
        }
    }
    
    if ($lastImage) {
        echo "<h2>Prueba de Imagen (ID: {$lastImage['attachment_id']}):</h2>";
        echo "<img src='file-viewer.php?id={$lastImage['attachment_id']}' style='max-width: 400px; border: 1px solid #000;' onerror='alert(\"Error al cargar imagen\");'>";
    }
    
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

// Formulario de prueba
echo "<h2>Subir archivo de prueba:</h2>";
echo "<form method='POST' enctype='multipart/form-data' action='test-upload.php'>";
echo "<input type='file' name='test_file' required>";
echo "<button type='submit'>Subir</button>";
echo "</form>";
?>
