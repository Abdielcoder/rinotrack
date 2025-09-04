<?php
/**
 * Script cron para generar instancias de tareas recurrentes
 * 
 * Ejecutar diariamente:
 * 0 6 * * * /usr/bin/php /path/to/rinotrack/public/cron_generate_recurrent_tasks.php
 */

require_once __DIR__ . '/../app/bootstrap.php';

// Solo permitir ejecución desde línea de comandos o localhost
if (php_sapi_name() !== 'cli' && $_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    die('Acceso denegado');
}

try {
    echo "=== CRON: Generación de Tareas Recurrentes ===\n";
    echo "Fecha/Hora: " . date('Y-m-d H:i:s') . "\n\n";
    
    $taskModel = new Task();
    $generatedCount = $taskModel->generateRecurrentInstances();
    
    if ($generatedCount !== false) {
        echo "✅ Proceso completado exitosamente\n";
        echo "📊 Instancias generadas: $generatedCount\n";
        
        // Log para el sistema
        error_log("CRON: Tareas recurrentes generadas: $generatedCount");
        
        // Si se ejecuta desde navegador, mostrar resultado HTML
        if (php_sapi_name() !== 'cli') {
            echo "<br><br><a href='?route=clan_member/tasks'>← Volver a Tareas</a>";
        }
        
    } else {
        echo "❌ Error en el proceso de generación\n";
        error_log("CRON: Error al generar tareas recurrentes");
    }
    
} catch (Exception $e) {
    echo "❌ Error crítico: " . $e->getMessage() . "\n";
    error_log("CRON: Error crítico en generación de tareas recurrentes: " . $e->getMessage());
}

echo "\n=== FIN CRON ===\n";
?>
