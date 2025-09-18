-- Script simplificado para revertir cambios de Acciones
-- Solo las operaciones esenciales sin consultas a INFORMATION_SCHEMA

-- 1. Eliminar todas las acciones creadas (si existen)
DELETE FROM Projects WHERE is_action = 1;

-- 2. Eliminar el índice de is_action (si existe)
ALTER TABLE Projects DROP INDEX IF EXISTS idx_projects_is_action;

-- 3. Eliminar la columna is_action de la tabla Projects (si existe)
ALTER TABLE Projects DROP COLUMN IF EXISTS is_action;

-- 4. Verificación simple
SELECT 'Reversión completada exitosamente' as resultado;

-- 5. Mostrar estructura actual de Projects para confirmar
DESCRIBE Projects;
