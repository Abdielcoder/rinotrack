<?php

class NotificationService {
    private $mailer;

    public function __construct() {
        $this->mailer = new Mailer();
    }

    public function notifyTaskAssigned($taskId, $assignedUserIds) {
        $setting = Notification::getSetting('task_assigned');
        if (!(int)($setting['is_enabled'] ?? 0)) return 0;

        if (empty($assignedUserIds)) return 0;
        $assignedUserIds = array_values(array_unique(array_map('intval', $assignedUserIds)));
        $db = Database::getConnection();
        // Obtener info de tarea/proyecto
        $taskStmt = $db->prepare("SELECT t.task_id, t.task_name, t.due_date, t.project_id, p.project_name FROM Tasks t JOIN Projects p ON t.project_id = p.project_id WHERE t.task_id = ?");
        $taskStmt->execute([$taskId]);
        $taskInfo = $taskStmt->fetch();
        if (!$taskInfo) { return 0; }

        // Obtener usuarios destino con email
        $placeholders = implode(',', array_fill(0, count($assignedUserIds), '?'));
        $userStmt = $db->prepare("SELECT user_id, full_name, email FROM Users WHERE user_id IN ($placeholders) AND email IS NOT NULL AND email != '' AND is_active = 1");
        $userStmt->execute($assignedUserIds);
        $rows = $userStmt->fetchAll();
        $count = 0;
        foreach ($rows as $r) {
            $to = $r['email'];
            if (Notification::alreadySent('task_assigned', $taskId, $r['user_id'], $to)) continue;
            $html = EmailTemplate::render(
                'Nueva tarea asignada',
                '<p>Se te ha asignado una nueva tarea ✅.</p>',
                [
                    ['label' => 'Tarea', 'value' => $taskInfo['task_name']],
                    ['label' => 'Proyecto', 'value' => $taskInfo['project_name']],
                    ['label' => 'Fecha de entrega', 'value' => ($taskInfo['due_date'] ?? null) ?: 'No definida']
                ],
                null,
                ['emoji' => '🧩']
            );
            if ($this->mailer->sendHtml($to, 'Polaris • Tarea asignada', $html)) {
                Notification::logSent('task_assigned', $taskId, $r['user_id'], $to);
                $count++;
            } else {
                error_log('task_assigned mail fail to ' . $to . ' - ' . $this->mailer->getLastError());
            }
        }
        // Extras globales para notificación
        $extra = $this->parseRecipients($setting['recipients'] ?? null);
        if (!empty($extra)) {
            if ($taskInfo) {
                $html = EmailTemplate::render(
                    'Tarea asignada (copia)',
                    '<p>Se ha asignado una tarea.</p>',
                    [
                        ['label' => 'Tarea', 'value' => $taskInfo['task_name']],
                        ['label' => 'Proyecto', 'value' => $taskInfo['project_name']]
                    ],
                    null,
                    ['emoji' => '🧩']
                );
                foreach ($extra as $mail) { $this->mailer->sendHtml($mail, 'Polaris • Tarea asignada', $html); }
            }
        }
        return $count;
    }

    public function notifyProjectAssignedToClan($projectId) {
        $setting = Notification::getSetting('project_assigned_to_clan');
        if (!(int)$setting['is_enabled']) return;

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT p.project_id, p.project_name, p.description, p.created_by_user_id, c.clan_id, c.clan_name, u.full_name as creator_name FROM Projects p JOIN Clans c ON p.clan_id = c.clan_id LEFT JOIN Users u ON p.created_by_user_id = u.user_id WHERE p.project_id = ?");
        $stmt->execute([$projectId]);
        $project = $stmt->fetch();
        if (!$project) return;

        // Miembros del clan con email
        $stmt = $db->prepare("SELECT u.user_id, u.full_name, u.email FROM Clan_Members cm JOIN Users u ON cm.user_id = u.user_id WHERE cm.clan_id = ? AND u.email IS NOT NULL AND u.email != '' AND u.is_active = 1");
        $stmt->execute([$project['clan_id']]);
        $members = $stmt->fetchAll();

        // Destinatarios adicionales desde configuración
        $extra = $this->parseRecipients($setting['recipients']);

        foreach ($members as $m) {
            $to = $m['email'];
            if (Notification::alreadySent('project_assigned_to_clan', $projectId, $m['user_id'], $to)) continue;
            $html = EmailTemplate::render(
                'Nuevo proyecto asignado',
                '<p>Se te ha asignado un nuevo proyecto en tu clan 🚀.</p>',
                [
                    ['label' => 'Proyecto', 'value' => $project['project_name']],
                    ['label' => 'Clan', 'value' => $project['clan_name']],
                    ['label' => 'Creado por', 'value' => $project['creator_name'] ?? ''],
                    ['label' => 'Descripción', 'value' => $project['description'] ?? '']
                ],
                null,
                ['emoji' => '📁']
            );
            if ($this->mailer->sendHtml($to, 'Polaris • Proyecto asignado', $html)) {
                Notification::logSent('project_assigned_to_clan', $projectId, $m['user_id'], $to);
            }
        }

        foreach ($extra as $to) {
            $html = EmailTemplate::render(
                'Proyecto asignado a un clan',
                '<p>Se ha creado/asignado un proyecto.</p>',
                [
                    ['label' => 'Proyecto', 'value' => $project['project_name']],
                    ['label' => 'Clan', 'value' => $project['clan_name']],
                    ['label' => 'Creado por', 'value' => $project['creator_name'] ?? '']
                ],
                ['label' => 'Ver proyecto', 'url' => APP_URL . '?route=admin/project-details&projectId=' . urlencode($projectId)]
            );
            $this->mailer->sendHtml($to, 'Polaris • Proyecto asignado', $html);
        }
    }

    public function notifyTaskDueSoonMulti() {
        $db = Database::getConnection();
        $total = 0;
        for ($i=1; $i<=3; $i++) {
            $key = 'task_due_soon_' . $i;
            $setting = Notification::getSetting($key);
            if (!(int)($setting['is_enabled'] ?? 0)) continue;
            $daysAhead = (int)($setting['value'] ?? 0);
            if ($daysAhead < 0 || $daysAhead > 365) continue;
            $sql = "SELECT t.task_id, t.task_name, t.due_date, t.project_id, p.project_name, t.assigned_to_user_id, u.full_name, u.email FROM Tasks t JOIN Projects p ON t.project_id = p.project_id LEFT JOIN Users u ON t.assigned_to_user_id = u.user_id WHERE t.status != 'completed' AND t.due_date IS NOT NULL AND t.due_date = DATE_ADD(CURDATE(), INTERVAL ? DAY) AND u.email IS NOT NULL AND u.email != ''";
            $stmt = $db->prepare($sql);
            $stmt->execute([$daysAhead]);
            $rows = $stmt->fetchAll();
            foreach ($rows as $r) {
                $to = $r['email'];
                if (Notification::alreadySent('task_due_soon_' . $daysAhead, $r['task_id'], $r['assigned_to_user_id'], $to)) continue;
            $html = EmailTemplate::render(
                'Recordatorio: tarea próxima a vencer',
                '<p>Tienes una tarea próxima a vencer ⏰.</p>',
                [
                    ['label' => 'Tarea', 'value' => $r['task_name']],
                    ['label' => 'Proyecto', 'value' => $r['project_name']],
                    ['label' => 'Vence', 'value' => $r['due_date']],
                    ['label' => 'Aviso', 'value' => 'En ' . $daysAhead . ' día(s)']
                ],
                null,
                ['emoji' => '⏳']
            );
                if ($this->mailer->sendHtml($to, 'Polaris • Tarea próxima a vencer (' . $daysAhead . ' días)', $html)) {
                    Notification::logSent('task_due_soon_' . $daysAhead, $r['task_id'], $r['assigned_to_user_id'], $to);
                    $total++;
                }
            }
        }
        return $total;
    }

    public function notifyTaskOverdue() {
        $setting = Notification::getSetting('task_overdue');
        if (!(int)$setting['is_enabled']) return 0;
        $db = Database::getConnection();
        $sql = "SELECT t.task_id, t.task_name, t.due_date, t.project_id, p.project_name, t.assigned_to_user_id, u.full_name, u.email FROM Tasks t JOIN Projects p ON t.project_id = p.project_id LEFT JOIN Users u ON t.assigned_to_user_id = u.user_id WHERE t.status != 'completed' AND t.due_date IS NOT NULL AND t.due_date < CURDATE() AND u.email IS NOT NULL AND u.email != ''";
        $rows = $db->query($sql)->fetchAll();
        $count = 0;
        foreach ($rows as $r) {
            $to = $r['email'];
            if (Notification::alreadySent('task_overdue', $r['task_id'], $r['assigned_to_user_id'], $to)) continue;
            $html = EmailTemplate::render(
                'Alerta: tarea vencida',
                '<p>Tienes una tarea vencida ⚠️. Por favor atiéndela cuanto antes.</p>',
                [
                    ['label' => 'Tarea', 'value' => $r['task_name']],
                    ['label' => 'Proyecto', 'value' => $r['project_name']],
                    ['label' => 'Venció', 'value' => $r['due_date']]
                ],
                null,
                ['emoji' => '⚠️']
            );
            if ($this->mailer->sendHtml($to, 'Polaris • Tarea vencida', $html)) {
                Notification::logSent('task_overdue', $r['task_id'], $r['assigned_to_user_id'], $to);
                $count++;
            }
        }
        return $count;
    }

    private function parseRecipients($csv) {
        if (!$csv) return [];
        $parts = array_map('trim', explode(',', $csv));
        return array_values(array_filter($parts, function($e){ return filter_var($e, FILTER_VALIDATE_EMAIL); }));
    }
}


