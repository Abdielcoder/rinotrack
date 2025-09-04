-- =====================================================
-- Script para agregar campos de recurrencia a RinoTrack
-- =====================================================

USE rinotrack;

-- Verificar y agregar campo is_recurrent
ALTER TABLE Tasks 
ADD COLUMN IF NOT EXISTS `is_recurrent` TINYINT(1) DEFAULT 0 
COMMENT 'Indica si la tarea es recurrente (1) o no (0)';

-- Verificar y agregar campo recurrence_type
ALTER TABLE Tasks 
ADD COLUMN IF NOT EXISTS `recurrence_type` ENUM('daily', 'weekly', 'monthly') DEFAULT NULL 
COMMENT 'Tipo de recurrencia: diaria, semanal o mensual';

-- Verificar y agregar campo recurrence_start_date
ALTER TABLE Tasks 
ADD COLUMN IF NOT EXISTS `recurrence_start_date` DATE DEFAULT NULL 
COMMENT 'Fecha de inicio de la recurrencia';

-- Verificar y agregar campo recurrence_end_date
ALTER TABLE Tasks 
ADD COLUMN IF NOT EXISTS `recurrence_end_date` DATE DEFAULT NULL 
COMMENT 'Fecha de fin de la recurrencia (vigencia)';

-- Verificar y agregar campo last_generated_date
ALTER TABLE Tasks 
ADD COLUMN IF NOT EXISTS `last_generated_date` DATE DEFAULT NULL 
COMMENT 'Última fecha en que se generaron instancias';

-- Verificar y agregar campo parent_recurrent_task_id
ALTER TABLE Tasks 
ADD COLUMN IF NOT EXISTS `parent_recurrent_task_id` INT(11) DEFAULT NULL 
COMMENT 'ID de la tarea recurrente padre (para instancias generadas)';

-- Crear índice para mejorar el rendimiento
CREATE INDEX IF NOT EXISTS idx_parent_recurrent ON Tasks(parent_recurrent_task_id);
CREATE INDEX IF NOT EXISTS idx_recurrent_tasks ON Tasks(is_recurrent, recurrence_type);

-- Mostrar estructura actualizada
DESCRIBE Tasks;

-- Mensaje de confirmación
SELECT 'Campos de recurrencia agregados exitosamente' AS mensaje;
