-- Script para revertir completamente los cambios de Acciones
-- Ejecutar este script para volver al estado anterior

-- 1. Eliminar todas las acciones creadas (registros con is_action = 1)
DELETE FROM Projects WHERE is_action = 1;

-- 2. Eliminar la columna action_id de la tabla Tasks (si se agregó)
-- Verificar primero si existe la columna
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'Tasks' 
  AND COLUMN_NAME = 'action_id' 
  AND TABLE_SCHEMA = DATABASE();

-- Si la consulta anterior devuelve resultados, ejecutar:
-- ALTER TABLE Tasks DROP FOREIGN KEY fk_tasks_action;
-- ALTER TABLE Tasks DROP INDEX idx_tasks_action;
-- ALTER TABLE Tasks DROP COLUMN action_id;

-- 3. Eliminar el índice de is_action
ALTER TABLE Projects DROP INDEX IF EXISTS idx_projects_is_action;

-- 4. Eliminar la columna is_action de la tabla Projects
ALTER TABLE Projects DROP COLUMN IF EXISTS is_action;

-- 5. Verificar que los cambios se revirtieron correctamente
SELECT 'Verificación de reversión completada:' as info;

-- Verificar que no existen columnas relacionadas con acciones
DESCRIBE Projects;

-- Verificar que no hay registros de acciones
SELECT COUNT(*) as acciones_restantes FROM Projects WHERE is_action = 1;

-- Mostrar estructura actual de la tabla Projects
SHOW CREATE TABLE Projects;

SELECT 'Reversión completada exitosamente' as resultado;
