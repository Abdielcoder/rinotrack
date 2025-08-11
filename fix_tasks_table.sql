-- Corregir todas las tablas que tienen problemas con AUTO_INCREMENT y PRIMARY KEY
-- Algunas tablas necesitan PRIMARY KEY primero, otras solo AUTO_INCREMENT

-- 1. Tabla Tasks (solo agregar AUTO_INCREMENT - PRIMARY KEY ya existe)
ALTER TABLE Tasks 
MODIFY COLUMN task_id INT(11) NOT NULL AUTO_INCREMENT;

-- 2. Tabla Task_Assignments (necesita PRIMARY KEY y AUTO_INCREMENT)
ALTER TABLE Task_Assignments 
ADD PRIMARY KEY (assignment_id);

ALTER TABLE Task_Assignments 
MODIFY COLUMN assignment_id INT(11) NOT NULL AUTO_INCREMENT;

-- 3. Tabla Task_Attachments (verificar si necesita PRIMARY KEY primero)
-- Intentar solo AUTO_INCREMENT primero
ALTER TABLE Task_Attachments 
MODIFY COLUMN attachment_id INT(11) NOT NULL AUTO_INCREMENT;

-- 4. Tabla Task_Comments (verificar si necesita PRIMARY KEY primero)
-- Intentar solo AUTO_INCREMENT primero
ALTER TABLE Task_Comments 
MODIFY COLUMN comment_id INT(11) NOT NULL AUTO_INCREMENT;

-- 5. Tabla Task_History (verificar si necesita PRIMARY KEY primero)
-- Intentar solo AUTO_INCREMENT primero
ALTER TABLE Task_History 
MODIFY COLUMN history_id INT(11) NOT NULL AUTO_INCREMENT;

-- Verificar las estructuras
DESCRIBE Tasks;
DESCRIBE Task_Assignments;
DESCRIBE Task_Attachments;
DESCRIBE Task_Comments;
DESCRIBE Task_History;
