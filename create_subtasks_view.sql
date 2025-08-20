-- Script para crear la vista v_subtasks_complete correctamente
USE rinotrack;

-- Eliminar la tabla incorrecta si existe
DROP TABLE IF EXISTS v_subtasks_complete;

-- Crear la vista correcta
CREATE VIEW v_subtasks_complete AS
SELECT 
    s.subtask_id,
    s.task_id,
    t.task_name as parent_task_name,
    s.title,
    s.description,
    s.completion_percentage,
    s.estimated_hours,
    s.actual_hours,
    s.status,
    s.priority,
    s.due_date,
    s.assigned_to_user_id,
    s.created_by_user_id,
    s.subtask_order,
    s.created_at,
    s.updated_at,
    u_assigned.full_name as assigned_to_fullname,
    u_assigned.username as assigned_to_username,
    u_created.full_name as created_by_fullname,
    u_created.username as created_by_username
FROM Subtasks s
LEFT JOIN Tasks t ON s.task_id = t.task_id
LEFT JOIN Users u_assigned ON s.assigned_to_user_id = u_assigned.user_id
LEFT JOIN Users u_created ON s.created_by_user_id = u_created.user_id;

-- Verificar que la vista funciona
SELECT COUNT(*) as total_subtasks FROM v_subtasks_complete;
SELECT * FROM v_subtasks_complete WHERE task_id = 242 LIMIT 5;
