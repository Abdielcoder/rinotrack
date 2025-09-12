<?php
// Endpoint simplificado para completar tareas
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en producción

// Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Log para debugging
error_log("=== SIMPLE COMPLETE TASK ===");
error_log("POST: " . json_encode($_POST));
error_log("Session: " . json_encode($_SESSION));

try {
    // Verificar método
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Verificar task_id
    $taskId = (int)($_POST['task_id'] ?? 0);
    if ($taskId <= 0) {
        throw new Exception('Task ID inválido');
    }

    // Verificar sesión (opcional para testing)
    if (!isset($_SESSION['user_id'])) {
        error_log("Advertencia: No hay sesión de usuario");
        // No fallar por esto en testing
    }

    // Conexión a BD
    require_once '../config/database.php';
    $db = Database::getConnection();
    
    error_log("Conexión establecida para task_id: $taskId");

    // Primero verificar si es una tarea principal
    $stmt = $db->prepare("SELECT task_id, status FROM Tasks WHERE task_id = ?");
    $stmt->execute([$taskId]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($task) {
        // Es una tarea principal
        error_log("Procesando tarea principal ID: $taskId");
        
        $stmt = $db->prepare("
            UPDATE Tasks 
            SET status = 'completed', 
                is_completed = 1,
                completion_percentage = 100,
                completed_at = NOW(),
                updated_at = NOW() 
            WHERE task_id = ?
        ");
        
        $result = $stmt->execute([$taskId]);
        $rowCount = $stmt->rowCount();
        
        error_log("Tarea principal actualizada. Rows affected: $rowCount");

        if ($result && $rowCount > 0) {
            $response = [
                'success' => true, 
                'message' => 'Tarea completada exitosamente',
                'task_id' => $taskId,
                'type' => 'task'
            ];
            error_log("✅ Éxito tarea principal: " . json_encode($response));
            echo json_encode($response);
        } else {
            throw new Exception("No se pudo completar la tarea con ID $taskId");
        }
    } else {
        // Verificar si es una subtarea
        error_log("No es tarea principal, verificando si es subtarea ID: $taskId");
        
        $stmt = $db->prepare("SELECT subtask_id, status, task_id as parent_task_id FROM Subtasks WHERE subtask_id = ?");
        $stmt->execute([$taskId]);
        $subtask = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($subtask) {
            // Es una subtarea
            error_log("Procesando subtarea ID: $taskId, parent_task_id: " . $subtask['parent_task_id']);
            
            $stmt = $db->prepare("
                UPDATE Subtasks 
                SET status = 'completed',
                    completed_at = NOW(),
                    updated_at = NOW()
                WHERE subtask_id = ?
            ");
            
            $result = $stmt->execute([$taskId]);
            $rowCount = $stmt->rowCount();
            
            error_log("Subtarea actualizada. Rows affected: $rowCount");

            if ($result && $rowCount > 0) {
                $response = [
                    'success' => true, 
                    'message' => 'Subtarea completada exitosamente',
                    'task_id' => $taskId,
                    'parent_task_id' => $subtask['parent_task_id'],
                    'type' => 'subtask'
                ];
                error_log("✅ Éxito subtarea: " . json_encode($response));
                echo json_encode($response);
            } else {
                throw new Exception("No se pudo completar la subtarea con ID $taskId");
            }
        } else {
            throw new Exception("No se encontró la tarea o subtarea con ID $taskId");
        }
    }

} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("❌ Error: " . $error);
    echo json_encode([
        'success' => false, 
        'message' => $error,
        'debug' => 'Ver error_log para más detalles'
    ]);
}
?>
