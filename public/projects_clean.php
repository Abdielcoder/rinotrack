<?php
// ELIMINAR TODO OUTPUT PREVIO
while (ob_get_level()) {
    ob_end_clean();
}

// Iniciar buffer limpio
ob_start();

// Suprimir TODOS los errores
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

// Incluir configuración mínima
define('APP_URL', '/desarrollo/rinotrack/public/');
session_start();

// Conectar a la base de datos directamente
try {
    $db = new PDO('mysql:host=localhost;dbname=rinotrack_db', 'rinotrack_user', 'RinoTrack#2024$Dev');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
} catch (Exception $e) {
    die('Error de conexión');
}

// Obtener proyectos directamente
$projects = [];
if (isset($_SESSION['user_id'])) {
    try {
        // Obtener clan del usuario
        $stmt = $db->prepare("SELECT clan_id FROM Users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if ($user && $user['clan_id']) {
            // Obtener proyectos del clan
            $stmt = $db->prepare("
                SELECT project_id, project_name, description, status, created_at 
                FROM Projects 
                WHERE clan_id = ? AND (is_personal IS NULL OR is_personal = 0)
                ORDER BY created_at DESC
            ");
            $stmt->execute([$user['clan_id']]);
            $projects = $stmt->fetchAll();
        }
    } catch (Exception $e) {
        // Silenciar errores
    }
}

// Limpiar cualquier output previo
ob_clean();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Proyectos</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; }
        .header { margin-bottom: 20px; }
        .project { background: #f8f9fa; padding: 15px; margin-bottom: 10px; border-radius: 5px; }
        .btn { padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gestionar Proyectos</h1>
            <a href="?route=clan_leader/dashboard" class="btn">Volver</a>
        </div>
        
        <?php if (empty($projects)): ?>
            <p>No hay proyectos</p>
        <?php else: ?>
            <?php foreach ($projects as $p): ?>
                <div class="project">
                    <h3><?php echo htmlspecialchars($p['project_name']); ?></h3>
                    <p><?php echo htmlspecialchars($p['description']); ?></p>
                    <small>Estado: <?php echo $p['status']; ?></small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
