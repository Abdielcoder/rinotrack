-- Verificar el estado actual de la columna is_personal
SELECT 
    project_id,
    project_name,
    created_by_user_id,
    is_personal,
    clan_id
FROM Projects 
WHERE project_name LIKE '%Personal%' 
   OR project_name LIKE '%Usuario%' 
   OR project_name LIKE '%Prueba%'
ORDER BY project_name;

-- Ver todos los proyectos para verificar la estructura
SELECT 
    project_id,
    project_name,
    created_by_user_id,
    is_personal,
    clan_id
FROM Projects 
ORDER BY project_name;
