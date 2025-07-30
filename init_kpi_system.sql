-- =====================================
-- SCRIPT DE INICIALIZACIÓN KPI SYSTEM
-- =====================================
-- Este script inicializa el sistema KPI con un trimestre de ejemplo

-- Crear primer trimestre KPI (Q1 2024)
INSERT INTO KPI_Quarters (quarter, year, total_points, is_active, created_at) 
VALUES ('Q1', 2024, 1000, 1, NOW());

-- Obtener el ID del trimestre creado
SET @kpi_quarter_id = LAST_INSERT_ID();

-- Ejemplo: Asignar KPIs a proyectos existentes (si los hay)
-- Nota: Esto asume que ya existen proyectos en la tabla Projects

-- Actualizar algunos proyectos con KPIs de ejemplo
UPDATE Projects 
SET 
    kpi_quarter_id = @kpi_quarter_id,
    kpi_points = 200,
    task_distribution_mode = 'automatic'
WHERE project_id IN (
    SELECT project_id FROM (
        SELECT project_id FROM Projects 
        ORDER BY created_at DESC 
        LIMIT 3
    ) AS temp_projects
);

-- Crear algunas tareas de ejemplo para los proyectos con KPI
INSERT INTO Tasks (project_id, task_name, description, assigned_to_user_id, task_points, percentage_points, is_completed, created_at)
SELECT 
    p.project_id,
    CONCAT('Tarea ', (FLOOR(RAND() * 5) + 1), ' - ', p.project_name),
    CONCAT('Descripción de tarea para el proyecto ', p.project_name),
    p.created_by_user_id,
    CASE 
        WHEN p.task_distribution_mode = 'automatic' THEN FLOOR(p.kpi_points / 3)
        ELSE 0
    END,
    CASE 
        WHEN p.task_distribution_mode = 'percentage' THEN 33.33
        ELSE 0
    END,
    FLOOR(RAND() * 2), -- 0 o 1 (completado o no)
    NOW()
FROM Projects p
WHERE p.kpi_quarter_id = @kpi_quarter_id
UNION ALL
SELECT 
    p.project_id,
    CONCAT('Tarea ', (FLOOR(RAND() * 5) + 6), ' - ', p.project_name),
    CONCAT('Segunda tarea para el proyecto ', p.project_name),
    p.created_by_user_id,
    CASE 
        WHEN p.task_distribution_mode = 'automatic' THEN FLOOR(p.kpi_points / 3)
        ELSE 0
    END,
    CASE 
        WHEN p.task_distribution_mode = 'percentage' THEN 33.33
        ELSE 0
    END,
    FLOOR(RAND() * 2),
    NOW()
FROM Projects p
WHERE p.kpi_quarter_id = @kpi_quarter_id
UNION ALL
SELECT 
    p.project_id,
    CONCAT('Tarea ', (FLOOR(RAND() * 5) + 11), ' - ', p.project_name),
    CONCAT('Tercera tarea para el proyecto ', p.project_name),
    p.created_by_user_id,
    CASE 
        WHEN p.task_distribution_mode = 'automatic' THEN (p.kpi_points - (FLOOR(p.kpi_points / 3) * 2))
        ELSE 0
    END,
    CASE 
        WHEN p.task_distribution_mode = 'percentage' THEN 33.34
        ELSE 0
    END,
    FLOOR(RAND() * 2),
    NOW()
FROM Projects p
WHERE p.kpi_quarter_id = @kpi_quarter_id;

-- Mostrar resumen de la inicialización
SELECT 
    CONCAT('✅ Sistema KPI inicializado exitosamente') AS status,
    CONCAT('Trimestre: ', quarter, ' ', year) AS periodo,
    CONCAT(total_points, ' puntos totales') AS puntos,
    (SELECT COUNT(*) FROM Projects WHERE kpi_quarter_id = @kpi_quarter_id) AS proyectos_asignados,
    (SELECT COUNT(*) FROM Tasks WHERE project_id IN (
        SELECT project_id FROM Projects WHERE kpi_quarter_id = @kpi_quarter_id
    )) AS tareas_creadas
FROM KPI_Quarters 
WHERE kpi_quarter_id = @kpi_quarter_id;

-- =====================================
-- CONSULTAS ÚTILES PARA VERIFICACIÓN
-- =====================================

-- Ver estado actual del KPI
SELECT 
    kq.quarter,
    kq.year,
    kq.total_points,
    kq.is_active,
    COUNT(DISTINCT p.project_id) as proyectos_con_kpi,
    COALESCE(SUM(p.kpi_points), 0) as puntos_asignados,
    (kq.total_points - COALESCE(SUM(p.kpi_points), 0)) as puntos_disponibles
FROM KPI_Quarters kq
LEFT JOIN Projects p ON kq.kpi_quarter_id = p.kpi_quarter_id
WHERE kq.is_active = 1
GROUP BY kq.kpi_quarter_id;

-- Ver ranking de clanes (si hay datos)
SELECT 
    c.clan_name,
    COUNT(DISTINCT p.project_id) as proyectos,
    COALESCE(SUM(p.kpi_points), 0) as puntos_totales,
    COALESCE(SUM(CASE WHEN t.is_completed = 1 THEN t.task_points ELSE 0 END), 0) as puntos_completados,
    ROUND(
        CASE 
            WHEN COALESCE(SUM(p.kpi_points), 0) > 0 
            THEN (COALESCE(SUM(CASE WHEN t.is_completed = 1 THEN t.task_points ELSE 0 END), 0) / COALESCE(SUM(p.kpi_points), 0)) * 100
            ELSE 0 
        END, 2
    ) as progreso_porcentaje
FROM Clans c
LEFT JOIN Projects p ON c.clan_id = p.clan_id AND p.kpi_quarter_id = @kpi_quarter_id
LEFT JOIN Tasks t ON p.project_id = t.project_id
GROUP BY c.clan_id, c.clan_name
HAVING puntos_totales > 0
ORDER BY puntos_completados DESC;

-- =====================================
-- LIMPIEZA (USAR SOLO SI ES NECESARIO)
-- =====================================
/*
-- Para limpiar todo el sistema KPI:

-- DELETE FROM Tasks WHERE project_id IN (
--     SELECT project_id FROM Projects WHERE kpi_quarter_id IS NOT NULL
-- );

-- UPDATE Projects SET 
--     kpi_quarter_id = NULL,
--     kpi_points = 0,
--     task_distribution_mode = 'automatic'
-- WHERE kpi_quarter_id IS NOT NULL;

-- DELETE FROM KPI_Quarters;
*/