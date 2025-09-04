<?php
require_once '../app/bootstrap.php';
require_once '../app/models/Task.php';

session_start();

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    die('No autorizado. Por favor inicia sesión.');
}

$taskModel = new Task();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Recurrencia</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #333; }
        .section { margin: 20px 0; padding: 15px; background: #f9f9f9; border-radius: 5px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        .warning { color: orange; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        th { background: #4CAF50; color: white; }
        .btn { padding: 10px 20px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        .btn:hover { background: #45a049; }
        .btn-danger { background: #f44336; }
        .btn-danger:hover { background: #da190b; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .code { font-family: monospace; background: #f0f0f0; padding: 2px 4px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Debug Sistema de Recurrencia</h1>
        
        <?php
        // 1. Verificar estructura de la base de datos
        echo '<div class="section">';
        echo '<h2>1. Estructura de la Base de Datos</h2>';
        
        try {
            $db = Database::getInstance();
            $stmt = $db->query("SHOW COLUMNS FROM Tasks WHERE Field IN ('is_recurrent', 'recurrence_type', 'recurrence_start_date', 'recurrence_end_date', 'last_generated_date', 'parent_recurrent_task_id')");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($columns) == 6) {
                echo '<p class="success">✅ Todos los campos de recurrencia existen en la tabla Tasks</p>';
                echo '<table>';
                echo '<tr><th>Campo</th><th>Tipo</th><th>Default</th></tr>';
                foreach ($columns as $col) {
                    echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>" . ($col['Default'] ?? 'NULL') . "</td></tr>";
                }
                echo '</table>';
            } else {
                echo '<p class="error">❌ Faltan campos de recurrencia. Solo se encontraron ' . count($columns) . ' de 6 campos.</p>';
                echo '<p>Ejecuta el siguiente SQL en phpMyAdmin:</p>';
                echo '<pre>-- Copiar y ejecutar en phpMyAdmin
ALTER TABLE Tasks 
ADD COLUMN IF NOT EXISTS `is_recurrent` TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS `recurrence_type` ENUM(\'daily\', \'weekly\', \'monthly\') DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `recurrence_start_date` DATE DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `recurrence_end_date` DATE DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `last_generated_date` DATE DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `parent_recurrent_task_id` INT(11) DEFAULT NULL;</pre>';
            }
        } catch (Exception $e) {
            echo '<p class="error">❌ Error al verificar estructura: ' . $e->getMessage() . '</p>';
        }
        echo '</div>';
        
        // 2. Verificar tareas recurrentes existentes
        echo '<div class="section">';
        echo '<h2>2. Tareas Recurrentes Existentes</h2>';
        
        try {
            $stmt = $db->query("
                SELECT task_id, task_name, recurrence_type, recurrence_start_date, 
                       recurrence_end_date, last_generated_date, created_by_user_id
                FROM Tasks 
                WHERE is_recurrent = 1
                ORDER BY task_id DESC
            ");
            $recurrentTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($recurrentTasks) > 0) {
                echo '<p class="success">Se encontraron ' . count($recurrentTasks) . ' tareas recurrentes:</p>';
                echo '<table>';
                echo '<tr><th>ID</th><th>Nombre</th><th>Tipo</th><th>Inicio</th><th>Fin</th><th>Última Gen.</th><th>Acciones</th></tr>';
                foreach ($recurrentTasks as $task) {
                    echo '<tr>';
                    echo '<td>' . $task['task_id'] . '</td>';
                    echo '<td>' . htmlspecialchars($task['task_name']) . '</td>';
                    echo '<td>' . ($task['recurrence_type'] ?? 'NO DEFINIDO') . '</td>';
                    echo '<td>' . ($task['recurrence_start_date'] ?? 'NO DEFINIDO') . '</td>';
                    echo '<td>' . ($task['recurrence_end_date'] ?? 'Sin límite') . '</td>';
                    echo '<td>' . ($task['last_generated_date'] ?? 'Nunca') . '</td>';
                    echo '<td>';
                    echo '<form method="post" style="display:inline;">';
                    echo '<input type="hidden" name="action" value="generate_instances">';
                    echo '<input type="hidden" name="task_id" value="' . $task['task_id'] . '">';
                    echo '<button type="submit" class="btn">Generar Instancias</button>';
                    echo '</form>';
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p class="warning">⚠️ No hay tareas recurrentes creadas aún.</p>';
            }
            
            // Verificar instancias generadas
            $stmt = $db->query("
                SELECT COUNT(*) as count 
                FROM Tasks 
                WHERE parent_recurrent_task_id IS NOT NULL
            ");
            $instanceCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo '<p class="info">📊 Total de instancias generadas: ' . $instanceCount . '</p>';
            
        } catch (Exception $e) {
            echo '<p class="error">❌ Error al consultar tareas: ' . $e->getMessage() . '</p>';
        }
        echo '</div>';
        
        // 3. Procesar acciones
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo '<div class="section">';
            echo '<h2>3. Resultado de la Acción</h2>';
            
            if ($_POST['action'] === 'generate_instances') {
                $taskId = (int)$_POST['task_id'];
                echo "<p>Generando instancias para tarea ID: $taskId...</p>";
                
                try {
                    $count = $taskModel->generateInstancesForTask($taskId);
                    echo '<p class="success">✅ Se generaron ' . $count . ' instancias nuevas.</p>';
                    
                    // Mostrar las instancias generadas
                    $stmt = $db->prepare("
                        SELECT task_id, task_name, due_date, status 
                        FROM Tasks 
                        WHERE parent_recurrent_task_id = ?
                        ORDER BY due_date
                    ");
                    $stmt->execute([$taskId]);
                    $instances = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($instances) > 0) {
                        echo '<p>Instancias creadas:</p>';
                        echo '<table>';
                        echo '<tr><th>ID</th><th>Nombre</th><th>Fecha Vencimiento</th><th>Estado</th></tr>';
                        foreach ($instances as $inst) {
                            echo '<tr>';
                            echo '<td>' . $inst['task_id'] . '</td>';
                            echo '<td>' . htmlspecialchars($inst['task_name']) . '</td>';
                            echo '<td>' . $inst['due_date'] . '</td>';
                            echo '<td>' . $inst['status'] . '</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                } catch (Exception $e) {
                    echo '<p class="error">❌ Error al generar instancias: ' . $e->getMessage() . '</p>';
                    echo '<pre>' . $e->getTraceAsString() . '</pre>';
                }
                
            } elseif ($_POST['action'] === 'create_test') {
                echo '<p>Creando tarea de prueba...</p>';
                
                try {
                    // Crear una tarea recurrente de prueba
                    $testData = [
                        'task_name' => 'Tarea Recurrente de Prueba - ' . date('Y-m-d H:i:s'),
                        'description' => 'Esta es una tarea de prueba para verificar el sistema de recurrencia',
                        'priority' => 'medium',
                        'due_date' => date('Y-m-d'),
                        'project_id' => $_POST['project_id'] ?? null,
                        'assigned_to_user_id' => $_SESSION['user_id'],
                        'created_by_user_id' => $_SESSION['user_id'],
                        'is_personal' => $_POST['is_personal'] ?? 0,
                        'is_recurrent' => 1,
                        'recurrence_type' => $_POST['recurrence_type'],
                        'recurrence_start_date' => $_POST['recurrence_start_date'],
                        'recurrence_end_date' => $_POST['recurrence_end_date']
                    ];
                    
                    $taskId = $taskModel->createPersonalTaskSimple($testData);
                    
                    if ($taskId) {
                        echo '<p class="success">✅ Tarea recurrente creada con ID: ' . $taskId . '</p>';
                        
                        // Generar instancias
                        $count = $taskModel->generateInstancesForTask($taskId);
                        echo '<p class="success">✅ Se generaron ' . $count . ' instancias.</p>';
                    } else {
                        echo '<p class="error">❌ Error al crear la tarea de prueba.</p>';
                    }
                } catch (Exception $e) {
                    echo '<p class="error">❌ Error: ' . $e->getMessage() . '</p>';
                }
            }
            echo '</div>';
        }
        
        // 4. Formulario para crear tarea de prueba
        echo '<div class="section">';
        echo '<h2>4. Crear Tarea Recurrente de Prueba</h2>';
        ?>
        
        <form method="post">
            <input type="hidden" name="action" value="create_test">
            <input type="hidden" name="is_personal" value="1">
            
            <p>
                <label>Tipo de Recurrencia:</label><br>
                <select name="recurrence_type" required>
                    <option value="daily">Diaria</option>
                    <option value="weekly">Semanal</option>
                    <option value="monthly">Mensual</option>
                </select>
            </p>
            
            <p>
                <label>Fecha de Inicio:</label><br>
                <input type="date" name="recurrence_start_date" value="<?php echo date('Y-m-d'); ?>" required>
            </p>
            
            <p>
                <label>Fecha de Fin (Vigencia):</label><br>
                <input type="date" name="recurrence_end_date" value="<?php echo date('Y-m-d', strtotime('+1 month')); ?>">
            </p>
            
            <button type="submit" class="btn">Crear Tarea de Prueba</button>
        </form>
        
        <?php
        echo '</div>';
        
        // 5. Información de diagnóstico
        echo '<div class="section">';
        echo '<h2>5. Información de Diagnóstico</h2>';
        echo '<ul>';
        echo '<li>Usuario actual: ' . $_SESSION['user_id'] . '</li>';
        echo '<li>Rol: ' . ($_SESSION['role'] ?? 'No definido') . '</li>';
        echo '<li>Fecha/Hora servidor: ' . date('Y-m-d H:i:s') . '</li>';
        echo '<li>Zona horaria: ' . date_default_timezone_get() . '</li>';
        echo '</ul>';
        
        // Verificar método generateInstancesForTask
        if (method_exists($taskModel, 'generateInstancesForTask')) {
            echo '<p class="success">✅ Método generateInstancesForTask existe en el modelo Task</p>';
        } else {
            echo '<p class="error">❌ Método generateInstancesForTask NO existe en el modelo Task</p>';
        }
        
        echo '</div>';
        ?>
    </div>
</body>
</html>
