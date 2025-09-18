-- Script para crear la tabla Actions basada en la tabla Projects
-- Esta tabla clonará fielmente la funcionalidad de proyectos

CREATE TABLE `Actions` (
  `action_id` int(11) NOT NULL AUTO_INCREMENT,
  `action_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `clan_id` int(11) NOT NULL,
  `created_by_user_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'open',
  `total_tasks` int(11) DEFAULT 0,
  `completed_tasks` int(11) DEFAULT 0,
  `progress_percentage` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `kpi_quarter_id` int(11) DEFAULT NULL,
  `kpi_points` int(11) DEFAULT 0,
  `task_distribution_mode` enum('automatic','percentage') DEFAULT 'automatic',
  `time_limit` date DEFAULT NULL,
  `is_personal` tinyint(1) DEFAULT 0 COMMENT '1 si es acción personal, 0 si es acción normal',
  `allow_delegation` tinyint(1) DEFAULT 0 COMMENT 'Permite que miembros del clan agreguen tareas a la acción',
  `action_type` varchar(50) DEFAULT 'normal' COMMENT 'Tipo de acción: normal, personal, recurrent, eventual',
  PRIMARY KEY (`action_id`),
  KEY `idx_actions_clan` (`clan_id`),
  KEY `idx_actions_created_by` (`created_by_user_id`),
  KEY `idx_actions_status` (`status`),
  KEY `idx_actions_kpi` (`kpi_quarter_id`),
  CONSTRAINT `fk_actions_clan` FOREIGN KEY (`clan_id`) REFERENCES `Clans` (`clan_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_actions_user` FOREIGN KEY (`created_by_user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_actions_kpi` FOREIGN KEY (`kpi_quarter_id`) REFERENCES `Clan_KPIs` (`kpi_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla de participantes para acciones (similar a Project_Participants)
CREATE TABLE `Action_Participants` (
  `action_participant_id` int(11) NOT NULL AUTO_INCREMENT,
  `action_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `joined_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`action_participant_id`),
  UNIQUE KEY `unique_action_participant` (`action_id`, `user_id`),
  KEY `idx_action_participants_action` (`action_id`),
  KEY `idx_action_participants_user` (`user_id`),
  CONSTRAINT `fk_action_participants_action` FOREIGN KEY (`action_id`) REFERENCES `Actions` (`action_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_action_participants_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Modificar la tabla Tasks para incluir soporte para acciones
ALTER TABLE `Tasks` ADD COLUMN `action_id` int(11) DEFAULT NULL AFTER `project_id`;
ALTER TABLE `Tasks` ADD KEY `idx_tasks_action` (`action_id`);
ALTER TABLE `Tasks` ADD CONSTRAINT `fk_tasks_action` FOREIGN KEY (`action_id`) REFERENCES `Actions` (`action_id`) ON DELETE CASCADE;

-- Insertar algunas acciones de ejemplo para cada clan (similar a los proyectos especiales)
INSERT INTO `Actions` (`action_name`, `description`, `clan_id`, `created_by_user_id`, `status`, `action_type`) 
SELECT 
    'Acciones Eventuales' as action_name,
    'Acciones eventuales y tareas esporádicas del clan' as description,
    c.clan_id,
    (SELECT user_id FROM Users u 
     JOIN User_Roles ur ON u.user_id = ur.user_id 
     JOIN Roles r ON ur.role_id = r.role_id 
     JOIN Clan_Members cm ON u.user_id = cm.user_id 
     WHERE cm.clan_id = c.clan_id AND r.role_name = 'clan_leader' 
     LIMIT 1) as created_by_user_id,
    'active' as status,
    'eventual' as action_type
FROM Clans c
WHERE EXISTS (
    SELECT 1 FROM Clan_Members cm 
    JOIN User_Roles ur ON cm.user_id = ur.user_id 
    JOIN Roles r ON ur.role_id = r.role_id 
    WHERE cm.clan_id = c.clan_id AND r.role_name = 'clan_leader'
);

INSERT INTO `Actions` (`action_name`, `description`, `clan_id`, `created_by_user_id`, `status`, `action_type`) 
SELECT 
    'Acciones Recurrentes' as action_name,
    'Acciones recurrentes y tareas periódicas del clan' as description,
    c.clan_id,
    (SELECT user_id FROM Users u 
     JOIN User_Roles ur ON u.user_id = ur.user_id 
     JOIN Roles r ON ur.role_id = r.role_id 
     JOIN Clan_Members cm ON u.user_id = cm.user_id 
     WHERE cm.clan_id = c.clan_id AND r.role_name = 'clan_leader' 
     LIMIT 1) as created_by_user_id,
    'active' as status,
    'recurrent' as action_type
FROM Clans c
WHERE EXISTS (
    SELECT 1 FROM Clan_Members cm 
    JOIN User_Roles ur ON cm.user_id = ur.user_id 
    JOIN Roles r ON ur.role_id = r.role_id 
    WHERE cm.clan_id = c.clan_id AND r.role_name = 'clan_leader'
);
