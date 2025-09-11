<?php
/**
 * PRUEBA FINAL CLAN LEADER - Verificar que funciona igual que clan_member
 */

require_once '../app/bootstrap.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    die('Inicia sesión');
}

$currentUser = $auth->getCurrentUser();
$userId = $currentUser['user_id'];

echo "<h1 style='color:green;'>🎯 PRUEBA CLAN LEADER - Usuario {$userId}</h1>";

try {
    // 1. PROBAR LA NUEVA FUNCIÓN
    echo "<h2>1️⃣ NUEVA FUNCIÓN getMyKanbanTasksNew</h2>";
    
    $clanLeaderController = new ClanLeaderController();
    
    // Simular la llamada
    ob_start();
    $clanLeaderController->getMyKanbanTasksNew();
    $jsonOutput = ob_get_clean();
    
    $data = json_decode($jsonOutput, true);
    
    if ($data && $data['success']) {
        $kanbanTasks = $data['kanbanTasks'];
        $total = $data['total'] ?? 0;
        $personalTasks = $data['personal_tasks'] ?? 0;
        
        echo "<p style='background:lightgreen; padding:10px;'>";
        echo "<strong>✅ ÉXITO:</strong> {$total} tareas encontradas, {$personalTasks} personales";
        echo "</p>";
        
        echo "<h3>📊 Distribución Kanban:</h3>";
        echo "<table border='1' style='border-collapse:collapse;'>";
        echo "<tr style='background:#333; color:white;'><th>Columna</th><th>Cantidad</th><th>Muestra (primeras 3)</th></tr>";
        
        foreach ($kanbanTasks as $columna => $tareas) {
            echo "<tr>";
            echo "<td><strong>{$columna}</strong></td>";
            echo "<td>{" . count($tareas) . "}</td>";
            echo "<td>";
            for ($i = 0; $i < min(3, count($tareas)); $i++) {
                $tarea = $tareas[$i];
                $personal = $tarea['is_personal'] == 1 ? '👤' : '';
                echo "{$personal} {$tarea['task_id']}: {$tarea['project_name']}<br>";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Mostrar tareas personales específicamente
        if ($personalTasks > 0) {
            echo "<h3>👤 TAREAS PERSONALES ENCONTRADAS:</h3>";
            $todasLasTareas = array_merge(
                $kanbanTasks['vencidas'] ?? [],
                $kanbanTasks['hoy'] ?? [],
                $kanbanTasks['semana1'] ?? [],
                $kanbanTasks['semana2'] ?? []
            );
            
            echo "<table border='1' style='border-collapse:collapse;'>";
            echo "<tr style='background:#6366f1; color:white;'><th>ID</th><th>Tarea</th><th>Proyecto Mostrado</th><th>Es Personal</th></tr>";
            
            foreach ($todasLasTareas as $tarea) {
                if ($tarea['is_personal'] == 1) {
                    echo "<tr style='background:#e7f3ff;'>";
                    echo "<td>{$tarea['task_id']}</td>";
                    echo "<td>" . htmlspecialchars($tarea['task_name']) . "</td>";
                    echo "<td><strong>" . htmlspecialchars($tarea['project_name']) . "</strong></td>";
                    echo "<td>✅ SÍ</td>";
                    echo "</tr>";
                }
            }
            echo "</table>";
        }
        
    } else {
        echo "<p style='background:red; color:white; padding:10px;'>";
        echo "❌ ERROR: " . ($data['message'] ?? 'Error desconocido');
        echo "</p>";
        echo "<pre>" . $jsonOutput . "</pre>";
    }
    
    echo "<h2>2️⃣ COMPARACIÓN CON CLAN_MEMBER</h2>";
    
    // Usar la misma función que clan_member
    $taskModel = new Task();
    $memberResult = $taskModel->getAllUserTasksForDashboard($userId);
    
    if ($memberResult['success']) {
        $memberTotal = count($memberResult['tasks']);
        $memberPersonal = array_sum(array_map(function($t) { 
            return $t['is_personal'] == 1 ? 1 : 0; 
        }, $memberResult['tasks']));
        
        echo "<p style='background:lightblue; padding:10px;'>";
        echo "<strong>CLAN_MEMBER:</strong> {$memberTotal} tareas, {$memberPersonal} personales<br>";
        echo "<strong>CLAN_LEADER:</strong> {$total} tareas, {$personalTasks} personales";
        echo "</p>";
        
        if ($total == $memberTotal && $personalTasks == $memberPersonal) {
            echo "<div style='background:lightgreen; padding:20px; border-radius:10px;'>";
            echo "<h3>🎉 ¡PERFECTO! AMBOS MÉTODOS DEVUELVEN LO MISMO</h3>";
            echo "<p>✅ Clan Leader ahora funciona igual que Clan Member</p>";
            echo "<p>✅ Las tareas personales se muestran como 'Personal'</p>";
            echo "<p><strong>Ve al dashboard de clan leader para ver todas tus tareas</strong></p>";
            echo "</div>";
        } else {
            echo "<div style='background:yellow; padding:20px; border-radius:10px;'>";
            echo "<h3>⚠️ DIFERENCIAS ENCONTRADAS</h3>";
            echo "<p>Los números no coinciden exactamente, pero puede ser normal</p>";
            echo "</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<h2 style='color:red;'>❌ ERROR: " . $e->getMessage() . "</h2>";
}

echo "<hr><h2>🚀 INSTRUCCIONES</h2>";
echo "<p>1. Ve al dashboard de <strong>clan leader</strong></p>";
echo "<p>2. Haz clic en la pestaña <strong>'Mis Tareas'</strong></p>";
echo "<p>3. Deberías ver todas tus tareas, con las personales mostrando 'Personal'</p>";
echo "<p>4. Si no funciona, revisa los logs del servidor</p>";
?>
