<?php
/**
 * Script de prueba para verificar que el endpoint getTeamTasks funciona
 */

// Incluir bootstrap para configurar la aplicación
require_once '../app/bootstrap.php';

// Simular una sesión de clan leader
session_start();

echo "<h1>Prueba de Endpoint getTeamTasks</h1>";

// Verificar si hay una sesión activa
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: red;'>No hay sesión activa. Por favor inicia sesión como clan leader.</p>";
    echo "<p><a href='?route=auth/login'>Iniciar Sesión</a></p>";
    exit;
}

echo "<h2>Información de Sesión:</h2>";
echo "<ul>";
echo "<li>User ID: " . $_SESSION['user_id'] . "</li>";
echo "<li>Username: " . $_SESSION['username'] . "</li>";
echo "<li>Role: " . $_SESSION['role'] . "</li>";
echo "<li>Clan ID: " . ($_SESSION['clan_id'] ?? 'No asignado') . "</li>";
echo "</ul>";

// Probar el endpoint directamente
echo "<h2>Prueba del Endpoint:</h2>";

try {
    // Crear una instancia del controlador
    $controller = new ClanLeaderController();
    
    // Intentar llamar al método getTeamTasks
    echo "<p>Intentando obtener tareas del equipo...</p>";
    
    // Capturar la salida
    ob_start();
    $controller->getTeamTasks();
    $output = ob_get_clean();
    
    echo "<h3>Respuesta del Endpoint:</h3>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<h2>Probar via AJAX:</h2>";
echo "<button onclick='testAjax()'>Probar getTeamTasks via AJAX</button>";
echo "<div id='ajax-result'></div>";

echo "<script>
function testAjax() {
    fetch('?route=clan_leader/get-team-tasks')
        .then(response => response.json())
        .then(data => {
            document.getElementById('ajax-result').innerHTML = 
                '<h3>Resultado AJAX:</h3><pre>' + JSON.stringify(data, null, 2) + '</pre>';
        })
        .catch(error => {
            document.getElementById('ajax-result').innerHTML = 
                '<p style=\"color: red;\">Error AJAX: ' + error.message + '</p>';
        });
}
</script>";
?>
