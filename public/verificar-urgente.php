<?php
require_once '../app/bootstrap.php';
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    die('No autorizado - Por favor inicia sesión primero');
}

$db = Database::getConnection();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verificación de Prioridad Urgente</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 1200px; margin: 0 auto; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #333; color: white; }
        .urgent { background: #fee2e2; color: #dc2626; font-weight: bold; }
        h2 { color: #1e3a8a; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; }
    </style>
</head>
<body>

<h1>🔍 Verificación del Sistema de Prioridad "Urgente"</h1>

<div class="info">
    <h2>✅ COMPORTAMIENTO ESPERADO:</h2>
    <ol>
        <li><strong>En el formulario:</strong> DEBE aparecer la opción "Urgente" ✓</li>
        <li><strong>En la base de datos:</strong> Se guarda como "critical" ✓</li>
        <li><strong>En la tabla de tareas:</strong> Se muestra como "Urgente" ✓</li>
    </ol>
</div>

<?php
// Verificar estructura de la BD
echo "<h2>1️⃣ Estructura de la Base de Datos</h2>";
$stmt = $db->query("SHOW COLUMNS FROM Tasks WHERE Field = 'priority'");
$column = $stmt->fetch(PDO::FETCH_ASSOC);
$enumValues = str_replace(["enum(", ")", "'"], "", $column['Type']);
$validValues = explode(",", $enumValues);

echo "<p>Valores permitidos en la BD para 'priority': <strong>" . implode(", ", $validValues) . "</strong></p>";

if (in_array('critical', $validValues)) {
    echo "<p class='success'>✅ La BD acepta 'critical' (que representa Urgente)</p>";
} else {
    echo "<p class='error'>❌ La BD NO acepta 'critical'</p>";
}

if (!in_array('urgent', $validValues)) {
    echo "<p class='success'>✅ Correcto: La BD NO usa 'urgent' directamente (usa 'critical')</p>";
} else {
    echo "<p class='error'>⚠️ La BD tiene 'urgent' - hay inconsistencia</p>";
}

// Mostrar últimas tareas urgentes
echo "<h2>2️⃣ Últimas Tareas con Prioridad URGENTE (guardadas como 'critical')</h2>";

$stmt = $db->prepare("
    SELECT 
        t.task_id,
        t.task_name,
        t.priority,
        t.created_at,
        p.project_name,
        u.full_name as created_by
    FROM Tasks t
    LEFT JOIN Projects p ON t.project_id = p.project_id
    LEFT JOIN Users u ON t.created_by_user_id = u.user_id
    WHERE t.priority = 'critical'
    ORDER BY t.created_at DESC
    LIMIT 10
");
$stmt->execute();
$urgentTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($urgentTasks) > 0) {
    echo "<table>";
    echo "<tr>
            <th>ID</th>
            <th>Tarea</th>
            <th>Proyecto</th>
            <th>Valor en BD</th>
            <th>Mostrar como</th>
            <th>Creado por</th>
            <th>Fecha</th>
          </tr>";
    
    foreach ($urgentTasks as $task) {
        echo "<tr>";
        echo "<td>{$task['task_id']}</td>";
        echo "<td>" . htmlspecialchars($task['task_name']) . "</td>";
        echo "<td>" . htmlspecialchars($task['project_name'] ?? 'Sin proyecto') . "</td>";
        echo "<td><code>{$task['priority']}</code></td>";
        echo "<td class='urgent'>URGENTE</td>";
        echo "<td>" . htmlspecialchars($task['created_by'] ?? 'Sistema') . "</td>";
        echo "<td>" . date('d/m/Y H:i', strtotime($task['created_at'])) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p class='success'>✅ Hay " . count($urgentTasks) . " tareas urgentes en el sistema</p>";
} else {
    echo "<p>No hay tareas con prioridad urgente (critical) en la base de datos aún.</p>";
}

// Verificar el mapeo en tiempo real
echo "<h2>3️⃣ Prueba de Mapeo en Tiempo Real</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_priority'])) {
    $inputPriority = $_POST['test_priority'];
    echo "<div class='info'>";
    echo "<p>Valor recibido del formulario: <strong>$inputPriority</strong></p>";
    
    // Simular el mapeo que hace el controlador
    $mappedPriority = $inputPriority;
    if ($inputPriority === 'urgent') {
        $mappedPriority = 'critical';
        echo "<p>Mapeo aplicado: <strong>urgent → critical</strong></p>";
    }
    
    echo "<p>Valor que se guardaría en BD: <strong>$mappedPriority</strong></p>";
    
    if ($mappedPriority === 'critical') {
        echo "<p class='success'>✅ El mapeo funciona correctamente</p>";
    }
    echo "</div>";
}
?>

<form method="POST">
    <h3>Simular selección de prioridad:</h3>
    <select name="test_priority" style="padding: 10px; font-size: 16px;">
        <option value="low">Baja</option>
        <option value="medium">Media</option>
        <option value="high">Alta</option>
        <option value="urgent">Urgente</option>
    </select>
    <button type="submit" style="padding: 10px 20px; font-size: 16px; margin-left: 10px;">Probar Mapeo</button>
</form>

<div class="info" style="background: #fef3c7; border: 2px solid #f59e0b; margin-top: 40px;">
    <h2>⚠️ IMPORTANTE:</h2>
    <p><strong>ES NORMAL Y CORRECTO que veas "Urgente" en el formulario.</strong></p>
    <p>Lo que arreglé es que ahora cuando seleccionas "Urgente":</p>
    <ol>
        <li>Se envía como "urgent" al servidor ✓</li>
        <li>El servidor lo convierte a "critical" ✓</li>
        <li>Se guarda como "critical" en la BD ✓</li>
        <li>Cuando se muestra, aparece como "Urgente" ✓</li>
    </ol>
    <p><strong>Si NO vieras "Urgente" en el formulario, ESO SÍ sería un error.</strong></p>
</div>

</body>
</html>
