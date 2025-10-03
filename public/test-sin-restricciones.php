<?php
/**
 * PRUEBA SIN RESTRICCIONES
 */

echo "<h1>Sistema de Archivos - Sin Restricciones</h1>";
echo "<p>Ahora cualquier usuario puede subir archivos sin validación de permisos.</p>";

// Formulario de prueba directa
echo "<h2>Subir archivo de prueba (Sin login requerido):</h2>";
echo "<form method='POST' enctype='multipart/form-data'>";
echo "<input type='hidden' name='subtask_id' value='571'>";
echo "<input type='hidden' name='description' value='Prueba sin restricciones'>";
echo "<label>Archivo: <input type='file' name='file' required></label><br><br>";
echo "<button type='submit'>Subir Archivo</button>";
echo "</form>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    // Llamar directamente al endpoint de upload
    $ch = curl_init();
    $url = 'http://localhost/RinoTrack/public/?route=clan_member/upload-subtask-attachment';
    
    $postData = [
        'subtask_id' => $_POST['subtask_id'],
        'description' => $_POST['description'],
        'file' => new CURLFile($_FILES['file']['tmp_name'], $_FILES['file']['type'], $_FILES['file']['name'])
    ];
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<h3>Respuesta del servidor:</h3>";
    echo "<pre>HTTP Code: $httpCode</pre>";
    echo "<pre>Response: $response</pre>";
    
    $result = json_decode($response, true);
    if ($result && $result['success']) {
        echo "<h3 style='color: green;'>✅ Archivo subido exitosamente!</h3>";
        if (isset($result['attachment_id'])) {
            echo "<p>ID del archivo: " . $result['attachment_id'] . "</p>";
            echo "<p><a href='file-viewer.php?id=" . $result['attachment_id'] . "' target='_blank'>Ver archivo</a></p>";
        }
    } else {
        echo "<h3 style='color: red;'>❌ Error al subir archivo</h3>";
    }
}

// Mostrar archivos recientes
echo "<h2>Archivos Recientes:</h2>";
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getConnection();
    $stmt = $db->query("SELECT * FROM Subtask_Attachments WHERE subtask_id = 571 ORDER BY attachment_id DESC LIMIT 5");
    $attachments = $stmt->fetchAll();
    
    if (empty($attachments)) {
        echo "<p>No hay archivos</p>";
    } else {
        echo "<ul>";
        foreach ($attachments as $att) {
            echo "<li>";
            echo $att['file_name'] . " - ";
            echo "<a href='file-viewer.php?id=" . $att['attachment_id'] . "' target='_blank'>Ver</a>";
            echo "</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
