-- Script rápido para corregir tabla Subtasks
-- Ejecutar en phpMyAdmin o cliente MySQL

USE rinotrack;

-- Verificar si la tabla existe
SELECT COUNT(*) as table_exists FROM information_schema.tables 
WHERE table_schema = 'rinotrack' AND table_name = 'Subtasks';

-- Corregir la tabla
ALTER TABLE Subtasks 
ADD PRIMARY KEY (subtask_id),
MODIFY subtask_id int(11) NOT NULL AUTO_INCREMENT;

-- Añadir índices si no existen
ALTER TABLE Subtasks
ADD KEY idx_task_id (task_id),
ADD KEY idx_assigned_to (assigned_to_user_id),
ADD KEY idx_created_by (created_by_user_id),
ADD KEY idx_status (status),
ADD KEY idx_due_date (due_date);

-- Verificar la corrección
DESCRIBE Subtasks;
SHOW CREATE TABLE Subtasks;
