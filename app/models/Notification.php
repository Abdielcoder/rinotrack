<?php

class Notification {
    public static function ensureTables() {
        $db = Database::getConnection();
        $db->exec(
            "CREATE TABLE IF NOT EXISTS Notification_Settings (
                setting_key VARCHAR(100) PRIMARY KEY,
                is_enabled TINYINT(1) NOT NULL DEFAULT 0,
                recipients TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }

    public static function getAllSettings() {
        self::ensureTables();
        $db = Database::getConnection();
        $stmt = $db->query('SELECT setting_key, is_enabled, recipients FROM Notification_Settings');
        $rows = $stmt->fetchAll();
        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['setting_key']] = [
                'is_enabled' => (int)$r['is_enabled'],
                'recipients' => $r['recipients']
            ];
        }
        // Asegurar claves por defecto
        $defaults = [
            'project_assigned_to_clan' => ['is_enabled' => 1, 'recipients' => null],
            'task_due_soon' => ['is_enabled' => 1, 'recipients' => null],
            'task_overdue' => ['is_enabled' => 1, 'recipients' => null]
        ];
        foreach ($defaults as $k => $v) {
            if (!isset($settings[$k])) {
                $settings[$k] = $v;
            }
        }
        return $settings;
    }

    public static function getSetting($key) {
        $all = self::getAllSettings();
        return $all[$key] ?? ['is_enabled' => 0, 'recipients' => null];
    }

    public static function setSetting($key, $isEnabled, $recipients = null) {
        self::ensureTables();
        $db = Database::getConnection();
        $stmt = $db->prepare('INSERT INTO Notification_Settings (setting_key, is_enabled, recipients) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE is_enabled = VALUES(is_enabled), recipients = VALUES(recipients)');
        return $stmt->execute([$key, (int)$isEnabled, $recipients]);
    }
}


