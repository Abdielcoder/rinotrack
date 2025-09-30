<?php
// Archivo de prueba para verificar la conexión
header('Content-Type: application/json');

try {
    require_once '../config/app.php';
    
    // Probar conexión a base de datos
    $db = Database::getConnection();
    $stmt = $db->query("SELECT COUNT(*) as count FROM Tasks LIMIT 1");
    $result = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'message' => 'Conexión exitosa a la base de datos',
        'tasks_count' => $result['count'],
        'app_url' => APP_URL,
        'db_host' => DB_HOST
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión: ' . $e->getMessage(),
        'app_url' => APP_URL,
        'db_host' => DB_HOST
    ]);
}
?>
