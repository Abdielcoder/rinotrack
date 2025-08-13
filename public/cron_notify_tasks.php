<?php
// Uso: ejecutar vía cron cada mañana
// php /Applications/MAMP/htdocs/RinoTrack/public/cron_notify_tasks.php | cat

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

// Cargar dependencias mínimas
require_once __DIR__ . '/../app/models/Notification.php';
require_once __DIR__ . '/../app/services/Mailer.php';
require_once __DIR__ . '/../app/services/EmailTemplate.php';
require_once __DIR__ . '/../app/services/NotificationService.php';

try {
    $service = new NotificationService();
    $dueSoon = $service->notifyTaskDueSoonMulti();
    $overdue = $service->notifyTaskOverdue();
    echo json_encode(['ok' => true, 'due_soon' => $dueSoon, 'overdue' => $overdue]) . "\n";
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]) . "\n";
}


