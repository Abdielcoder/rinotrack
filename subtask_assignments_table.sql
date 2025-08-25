-- Tabla para asignaciones múltiples de subtareas
CREATE TABLE `Subtask_Assignments` (
  `assignment_id` int(11) NOT NULL AUTO_INCREMENT,
  `subtask_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assigned_percentage` decimal(5,2) DEFAULT 100.00,
  `assigned_by_user_id` int(11) NOT NULL,
  `assigned_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`assignment_id`),
  UNIQUE KEY `unique_subtask_user` (`subtask_id`, `user_id`),
  KEY `idx_subtask_id` (`subtask_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_assigned_by` (`assigned_by_user_id`),
  CONSTRAINT `fk_subtask_assignments_subtask` FOREIGN KEY (`subtask_id`) REFERENCES `Subtasks` (`subtask_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_subtask_assignments_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_subtask_assignments_assigned_by` FOREIGN KEY (`assigned_by_user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Asignaciones múltiples de usuarios a subtareas';

-- Migrar datos existentes de assigned_to_user_id a la nueva tabla
INSERT INTO Subtask_Assignments (subtask_id, user_id, assigned_percentage, assigned_by_user_id, assigned_at)
SELECT 
    subtask_id, 
    assigned_to_user_id, 
    100.00, 
    created_by_user_id, 
    created_at
FROM Subtasks 
WHERE assigned_to_user_id IS NOT NULL;

-- Opcional: Mantener assigned_to_user_id por compatibilidad temporal
-- En el futuro se puede eliminar esta columna cuando todos los sistemas usen la nueva tabla
