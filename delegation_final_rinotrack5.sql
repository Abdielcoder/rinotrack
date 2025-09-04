-- =====================================================
-- SQL DEFINITIVO para Sistema de Delegación
-- Basado en estructura de rinotrack5.sql
-- =====================================================

USE rinotrack;

-- Los campos allow_delegation y project_type YA EXISTEN en rinotrack5.sql
-- Solo necesitamos activar la delegación para proyectos específicos

-- 1. Activar delegación para proyectos especiales
UPDATE Projects 
SET allow_delegation = 1 
WHERE project_name IN (
    'Tareas Recurrentes', 
    'Tareas Eventuales', 
    'Mis Tareas Recurrentes'
);

-- 2. Activar delegación para proyectos que contengan estas palabras
UPDATE Projects 
SET allow_delegation = 1 
WHERE project_name LIKE '%Tareas Recurrentes%' 
   OR project_name LIKE '%Tareas Eventuales%'
   OR project_name LIKE '%Recurrentes%';

-- 3. Crear índices si no existen (para optimizar consultas)
CREATE INDEX IF NOT EXISTS idx_allow_delegation ON Projects(allow_delegation);
CREATE INDEX IF NOT EXISTS idx_project_type ON Projects(project_type);

-- 4. Verificar que los campos existen
SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'rinotrack' 
  AND TABLE_NAME = 'Projects'
  AND COLUMN_NAME IN ('allow_delegation', 'project_type')
ORDER BY ORDINAL_POSITION;

-- 5. Mostrar proyectos con delegación activada
SELECT 
    project_id,
    project_name,
    clan_id,
    allow_delegation,
    project_type,
    is_personal
FROM Projects
WHERE allow_delegation = 1
ORDER BY clan_id, project_name;

-- 6. Estadísticas finales
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
    'Proyectos especiales encontrados',
    COUNT(*)
FROM Projects
WHERE project_name IN ('Tareas Recurrentes', 'Tareas Eventuales', 'Mis Tareas Recurrentes')
   OR project_name LIKE '%Recurrentes%';

-- Mensaje de confirmación
SELECT '✅ Sistema de delegación activado correctamente' AS Resultado;
