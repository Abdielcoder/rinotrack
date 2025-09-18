-- Script para agregar soporte de Acciones a la tabla Projects existente
-- Esto permitirá usar la misma estructura para proyectos y acciones

-- Agregar columna is_action para diferenciar entre proyectos y acciones
ALTER TABLE `Projects` ADD COLUMN `is_action` tinyint(1) DEFAULT 0 COMMENT '0 = Proyecto, 1 = Acción' AFTER `is_personal`;

-- Crear índice para mejorar las consultas por tipo
ALTER TABLE `Projects` ADD INDEX `idx_projects_is_action` (`is_action`);

-- Insertar acciones de ejemplo para cada clan (similar a los proyectos especiales)
-- Acciones Eventuales para cada clan
INSERT INTO `Projects` (`project_name`, `description`, `clan_id`, `created_by_user_id`, `status`, `project_type`, `is_action`) 
SELECT 
    'Acciones Eventuales' as project_name,
    'Acciones eventuales y tareas esporádicas del clan' as description,
    c.clan_id,
    COALESCE(
        (SELECT u.user_id FROM Users u 
         JOIN User_Roles ur ON u.user_id = ur.user_id 
         JOIN Roles r ON ur.role_id = r.role_id 
         JOIN Clan_Members cm ON u.user_id = cm.user_id 
         WHERE cm.clan_id = c.clan_id AND r.role_name = 'clan_leader' 
         LIMIT 1),
        (SELECT cm.user_id FROM Clan_Members cm WHERE cm.clan_id = c.clan_id LIMIT 1)
    ) as created_by_user_id,
    'active' as status,
    'eventual' as project_type,
    1 as is_action
FROM Clans c
WHERE NOT EXISTS (
    SELECT 1 FROM Projects p 
    WHERE p.clan_id = c.clan_id 
    AND p.project_name = 'Acciones Eventuales' 
    AND p.is_action = 1
);

-- Acciones Recurrentes para cada clan  
INSERT INTO `Projects` (`project_name`, `description`, `clan_id`, `created_by_user_id`, `status`, `project_type`, `is_action`) 
SELECT 
    'Acciones Recurrentes' as project_name,
    'Acciones recurrentes y tareas periódicas del clan' as description,
    c.clan_id,
    COALESCE(
        (SELECT u.user_id FROM Users u 
         JOIN User_Roles ur ON u.user_id = ur.user_id 
         JOIN Roles r ON ur.role_id = r.role_id 
         JOIN Clan_Members cm ON u.user_id = cm.user_id 
         WHERE cm.clan_id = c.clan_id AND r.role_name = 'clan_leader' 
         LIMIT 1),
        (SELECT cm.user_id FROM Clan_Members cm WHERE cm.clan_id = c.clan_id LIMIT 1)
    ) as created_by_user_id,
    'active' as status,
    'recurrent' as project_type,
    1 as is_action
FROM Clans c
WHERE NOT EXISTS (
    SELECT 1 FROM Projects p 
    WHERE p.clan_id = c.clan_id 
    AND p.project_name = 'Acciones Recurrentes' 
    AND p.is_action = 1
);

-- Verificar los cambios realizados
SELECT 'Verificación de la nueva columna:' as info;
DESCRIBE Projects;

SELECT 'Acciones creadas:' as info;
SELECT project_id, project_name, clan_id, is_action, project_type 
FROM Projects 
WHERE is_action = 1 
ORDER BY clan_id, project_name;
