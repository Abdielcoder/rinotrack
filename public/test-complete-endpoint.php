<?php
// Test directo del endpoint completeTask
session_start();

// Simular sesión de usuario
$_SESSION['user_id'] = 1; // Ajusta según tu usuario actual

// Simular datos POST
$_POST['task_id'] = '577'; // Usa un ID de tarea que veas en el Kanban

echo "<h2>Test del Endpoint completeTask</h2>";
echo "<p>Task ID: " . $_POST['task_id'] . "</p>";
echo "<p>User ID en sesión: " . ($_SESSION['user_id'] ?? 'NO_SESSION') . "</p>";

try {
    // Incluir bootstrap
    require_once '../app/bootstrap.php';
    
    echo "<p>✅ Bootstrap cargado</p>";
    
    // Crear controlador
    $controller = new ClanLeaderController();
    echo "<p>✅ Controlador creado</p>";
    
    // Llamar método
    echo "<p>🔄 Ejecutando completeTask...</p>";
    $controller->completeTask();
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Trace: " . $e->getTraceAsString() . "</p>";
} catch (Error $e) {
    echo "<p>❌ Fatal Error: " . $e->getMessage() . "</p>";
    echo "<p>Trace: " . $e->getTraceAsString() . "</p>";
}
?>
