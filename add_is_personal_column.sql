-- Agregar columna is_personal a la tabla Projects
ALTER TABLE `Projects` ADD COLUMN `is_personal` TINYINT(1) NOT NULL DEFAULT 0 AFTER `created_by_user_id`;

-- Actualizar proyectos existentes que son personales
-- Proyectos con nombres que contienen "Personal" o "Usuario"
UPDATE `Projects` 
SET `is_personal` = 1 
WHERE `project_name` LIKE '%Personal%' 
   OR `project_name` LIKE '%Usuario%' 
   OR `project_name` LIKE '%Prueba%';

-- Verificar la estructura actualizada
DESCRIBE `Projects`;
