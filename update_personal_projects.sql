-- Actualizar proyectos existentes que son personales
-- Proyectos con nombres que contienen "Personal", "Usuario" o "Prueba"
UPDATE Projects 
SET is_personal = 1 
WHERE project_name LIKE '%Personal%' 
   OR project_name LIKE '%Usuario%' 
   OR project_name LIKE '%Prueba%';

-- Verificar el resultado de la actualización
SELECT 
    project_id,
    project_name,
    created_by_user_id,
    is_personal,
    clan_id
FROM Projects 
WHERE is_personal = 1
ORDER BY project_name;

-- Verificar que la columna is_personal existe y tiene valores
DESCRIBE Projects;
