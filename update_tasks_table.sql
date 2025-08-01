-- =============================================
-- MIGRACIÓN: ACTUALIZAR TABLA TASKS (v3)
-- =============================================
-- Este script agrega las columnas necesarias para la gestión avanzada de tareas y KPIs.
-- Es seguro de ejecutar múltiples veces y compatible con MySQL 8.

SELECT '🚀 ACTUALIZANDO LA TABLA `Tasks`...' AS status;

-- OBTENER NOMBRE DE LA BASE DE DATOS
SET @db_name = DATABASE();

-- FUNCIÓN AUXILIAR PARA AGREGAR COLUMNA SI NO EXISTE
DROP PROCEDURE IF EXISTS AddColumnIfNotExists;
DELIMITER $$
CREATE PROCEDURE AddColumnIfNotExists(
    IN tableName VARCHAR(255),
    IN colName VARCHAR(255),
    IN colDef TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_schema = @db_name
          AND table_name = tableName
          AND column_name = colName
    )
    THEN
        SET @sql = CONCAT('ALTER TABLE `', tableName, '` ADD COLUMN `', colName, '` ', colDef);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
        SELECT CONCAT('✅ Columna `', colName, '` agregada a `', tableName, '`') AS result;
    ELSE
        SELECT CONCAT('ℹ️ Columna `', colName, '` ya existe en `', tableName, '`') AS result;
    END IF;
END$$
DELIMITER ;

-- AGREGAR COLUMNAS NECESARIAS
CALL AddColumnIfNotExists('Tasks', 'is_completed', 'TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`');
CALL AddColumnIfNotExists('Tasks', 'completed_at', 'TIMESTAMP NULL DEFAULT NULL AFTER `is_completed`');
CALL AddColumnIfNotExists('Tasks', 'created_by_user_id', 'INT NULL AFTER `assigned_to_user_id`');
CALL AddColumnIfNotExists('Tasks', 'priority', "ENUM('low', 'medium', 'high', 'critical') NOT NULL DEFAULT 'medium' AFTER `created_by_user_id`");
CALL AddColumnIfNotExists('Tasks', 'due_date', 'DATE NULL DEFAULT NULL AFTER `priority`');
CALL AddColumnIfNotExists('Tasks', 'automatic_points', 'DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT "Puntos KPI calculados para modo automático" AFTER `due_date`');
CALL AddColumnIfNotExists('Tasks', 'assigned_percentage', 'DECIMAL(5, 2) NOT NULL DEFAULT 0.00 COMMENT "Porcentaje de puntos KPI asignado manualmente" AFTER `automatic_points`');

-- MODIFICAR LA COLUMNA `status` PARA USAR UN ENUM
ALTER TABLE `Tasks` 
MODIFY COLUMN `status` ENUM('pending', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending';
SELECT '✅ Columna `status` modificada a tipo ENUM' AS result;

-- LIMPIAR PROCEDIMIENTO DE COLUMNAS
DROP PROCEDURE IF EXISTS AddColumnIfNotExists;

-- PROCEDIMIENTO PARA AGREGAR ÍNDICE SI NO EXISTE
DROP PROCEDURE IF EXISTS AddIndexIfNotExists;
DELIMITER $$
CREATE PROCEDURE AddIndexIfNotExists(
    IN tableName VARCHAR(255),
    IN indexName VARCHAR(255),
    IN indexDef TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT * FROM INFORMATION_SCHEMA.STATISTICS
        WHERE table_schema = @db_name
          AND table_name = tableName
          AND index_name = indexName
    )
    THEN
        SET @sql = CONCAT('ALTER TABLE `', tableName, '` ADD INDEX `', indexName, '` (', indexDef, ')');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
        SELECT CONCAT('✅ Índice `', indexName, '` agregado a `', tableName, '`') AS result;
    ELSE
        SELECT CONCAT('ℹ️ Índice `', indexName, '` ya existe en `', tableName, '`') AS result;
    END IF;
END$$
DELIMITER ;

-- AGREGAR ÍNDICES PARA MEJORAR RENDIMIENTO
CALL AddIndexIfNotExists('Tasks', 'idx_tasks_status', '`status`');
CALL AddIndexIfNotExists('Tasks', 'idx_tasks_priority', '`priority`');
CALL AddIndexIfNotExists('Tasks', 'idx_tasks_due_date', '`due_date`');

-- LIMPIAR PROCEDIMIENTO DE ÍNDICES
DROP PROCEDURE IF EXISTS AddIndexIfNotExists;

-- PROCEDIMIENTO PARA AGREGAR CLAVE FORÁNEA SI NO EXISTE
DROP PROCEDURE IF EXISTS AddForeignKeyIfNotExists;
DELIMITER $$
CREATE PROCEDURE AddForeignKeyIfNotExists(
    IN tableName VARCHAR(255),
    IN constraintName VARCHAR(255),
    IN fkDef TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE table_schema = @db_name
          AND table_name = tableName
          AND constraint_name = constraintName
    )
    THEN
        SET @sql = CONCAT('ALTER TABLE `', tableName, '` ADD CONSTRAINT `', constraintName, '` ', fkDef);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
        SELECT CONCAT('✅ Clave foránea `', constraintName, '` agregada a `', tableName, '`') AS result;
    ELSE
        SELECT CONCAT('ℹ️ Clave foránea `', constraintName, '` ya existe en `', tableName, '`') AS result;
    END IF;
END$$
DELIMITER ;

-- AGREGAR CLAVE FORÁNEA
CALL AddForeignKeyIfNotExists('Tasks', 'tasks_created_by_fk', 'FOREIGN KEY (`created_by_user_id`) REFERENCES `Users`(`user_id`) ON DELETE SET NULL');

-- LIMPIAR PROCEDIMIENTO DE CLAVE FORÁNEA
DROP PROCEDURE IF EXISTS AddForeignKeyIfNotExists;

SELECT '🎉 MIGRACIÓN DE `Tasks` COMPLETADA EXITOSAMENTE!' AS status;
