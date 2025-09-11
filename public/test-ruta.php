<?php
/**
 * PRUEBA RÁPIDA DE LA NUEVA RUTA
 */

require_once '../app/bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    die('Inicia sesión');
}

echo "<h1>🔧 PRUEBA DE RUTA</h1>";

// Probar la ruta directamente
echo "<h2>Probando ruta: ?route=clan_leader/getMyKanbanTasksNew</h2>";

echo "<iframe src='?route=clan_leader/getMyKanbanTasksNew' width='100%' height='400' style='border:1px solid #ccc;'></iframe>";

echo "<h2>También puedes probar directamente:</h2>";
echo "<p><a href='?route=clan_leader/getMyKanbanTasksNew' target='_blank'>Abrir ruta en nueva pestaña</a></p>";

echo "<h2>🎯 Si funciona, deberías ver JSON con tus tareas</h2>";
?>
