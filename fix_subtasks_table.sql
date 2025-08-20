-- Script para corregir la tabla Subtasks y añadir AUTO_INCREMENT
-- Ejecutar este script en la base de datos rinotrack

USE rinotrack;

-- Verificar si hay datos en la tabla Subtasks
SELECT COUNT(*) as subtasks_count FROM Subtasks;

-- Si hay datos, respaldarlos temporalmente
CREATE TABLE IF NOT EXISTS Subtasks_backup AS SELECT * FROM Subtasks;

-- Añadir PRIMARY KEY y AUTO_INCREMENT al campo subtask_id
ALTER TABLE Subtasks 
ADD PRIMARY KEY (subtask_id),
MODIFY subtask_id int(11) NOT NULL AUTO_INCREMENT;

-- Verificar la estructura corregida
DESCRIBE Subtasks;

-- Mostrar información sobre AUTO_INCREMENT
SHOW CREATE TABLE Subtasks;
