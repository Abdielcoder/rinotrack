-- =====================================================
-- Script SQL para implementar sistema de delegación
-- =====================================================

USE rinotrack;

-- 1. Agregar campo allow_delegation a la tabla Projects
ALTER TABLE Projects 
ADD COLUMN IF NOT EXISTS `allow_delegation` TINYINT(1) DEFAULT 0 
COMMENT 'Permite que miembros del clan agreguen tareas al proyecto'
AFTER `is_personal`;

-- 2. Agregar campo project_type si no existe
ALTER TABLE Projects 
ADD COLUMN IF NOT EXISTS `project_type` VARCHAR(50) DEFAULT 'normal' 
COMMENT 'Tipo de proyecto: normal, personal, recurrent, eventual'
AFTER `allow_delegation`;

-- 3. Agregar campo is_personal si no existe
ALTER TABLE Projects 
ADD COLUMN IF NOT EXISTS `is_personal` TINYINT(1) DEFAULT 0 
COMMENT '1 si es proyecto personal del usuario, 0 si es de clan'
AFTER `status`;

-- 4. Crear índice para mejorar búsquedas
CREATE INDEX IF NOT EXISTS idx_allow_delegation ON Projects(allow_delegation);
CREATE INDEX IF NOT EXISTS idx_project_type ON Projects(project_type);
CREATE INDEX IF NOT EXISTS idx_is_personal ON Projects(is_personal);

-- 5. Actualizar proyectos especiales para permitir delegación por defecto
UPDATE Projects 
SET allow_delegation = 1 
WHERE project_name IN ('Mis Tareas Recurrentes', 'Tareas Recurrentes', 'Tareas Eventuales');

-- 6. Crear proyecto "Mis Tareas Recurrentes" para usuarios que no lo tengan
-- (Solo si no existe para cada usuario)
INSERT INTO Projects (
    project_name,
    project_description,
    project_type,
    created_by_user_id,
    is_personal,
    allow_delegation,
    status,
    created_at
)
SELECT 
    'Mis Tareas Recurrentes',
    'Proyecto especial para gestionar tareas recurrentes personales',
    'recurrent',
    u.user_id,
    1,
    1,
    'active',
    NOW()
FROM Users u
WHERE u.is_active = 1
  AND NOT EXISTS (
    SELECT 1 
    FROM Projects p 
    WHERE p.created_by_user_id = u.user_id 
      AND p.project_name = 'Mis Tareas Recurrentes'
  );

-- 7. Verificar estructura final
SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'rinotrack' 
  AND TABLE_NAME = 'Projects'
  AND COLUMN_NAME IN ('allow_delegation', 'project_type', 'is_personal')
ORDER BY ORDINAL_POSITION;

-- 8. Mostrar estadísticas
SELECT 
    'Total de proyectos' as Metrica,
    COUNT(*) as Valor
FROM Projects
UNION ALL
SELECT 
    'Proyectos con delegación activa',
    COUNT(*)
FROM Projects
WHERE allow_delegation = 1
UNION ALL
SELECT 
    'Proyectos "Mis Tareas Recurrentes" creados',
    COUNT(*)
FROM Projects
WHERE project_name = 'Mis Tareas Recurrentes'
UNION ALL
SELECT 
    'Usuarios con proyecto recurrente',
    COUNT(DISTINCT created_by_user_id)
FROM Projects
WHERE project_name = 'Mis Tareas Recurrentes';

-- Mensaje de confirmación
SELECT '✅ Script de delegación ejecutado exitosamente' AS Resultado;
