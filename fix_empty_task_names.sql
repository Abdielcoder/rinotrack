-- Script para corregir tareas con nombres vacíos en el proyecto 220
-- "ELABORACION Y ESTANDARIZACION DE PROYECTOS"

-- Actualizar tarea 1101
UPDATE Tasks 
SET task_name = 'Tarea de Elaboración y Estandarización de Proyectos #1',
    description = 'Tarea pendiente de elaboración y estandarización de procesos del proyecto'
WHERE task_id = 1101 AND project_id = 220;

-- Actualizar tarea 1210  
UPDATE Tasks 
SET task_name = 'Tarea de Elaboración y Estandarización de Proyectos #2',
    description = 'Tarea pendiente de elaboración y estandarización de procesos del proyecto'
WHERE task_id = 1210 AND project_id = 220;

-- Actualizar tarea 1229
UPDATE Tasks 
SET task_name = 'Tarea de Elaboración y Estandarización de Proyectos #3', 
    description = 'Tarea pendiente de elaboración y estandarización de procesos del proyecto'
WHERE task_id = 1229 AND project_id = 220;

-- Actualizar tarea 1379
UPDATE Tasks 
SET task_name = 'Tarea de Elaboración y Estandarización de Proyectos #4',
    description = 'Tarea pendiente de elaboración y estandarización de procesos del proyecto'
WHERE task_id = 1379 AND project_id = 220;

-- Actualizar tarea 1380
UPDATE Tasks 
SET task_name = 'Tarea de Elaboración y Estandarización de Proyectos #5',
    description = 'Tarea pendiente de elaboración y estandarización de procesos del proyecto'
WHERE task_id = 1380 AND project_id = 220;

-- Verificar que las actualizaciones se realizaron correctamente
SELECT task_id, task_name, description, status, due_date 
FROM Tasks 
WHERE project_id = 220 
ORDER BY task_id;
