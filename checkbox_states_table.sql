-- Tabla para almacenar estados de checkboxes en comentarios
-- Esta tabla guarda el estado (marcado/desmarcado) de cada checkbox en los comentarios
-- tanto de tareas como de subtareas, permitiendo persistencia por usuario

CREATE TABLE IF NOT EXISTS `Comment_Checkbox_States` (
  `state_id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) NOT NULL COMMENT 'ID del comentario (Task_Comments o Subtask_Comments)',
  `comment_type` enum('task','subtask') NOT NULL COMMENT 'Tipo de comentario: task o subtask',
  `checkbox_index` int(11) NOT NULL COMMENT 'Índice del checkbox dentro del comentario (0, 1, 2...)',
  `checkbox_text` text NOT NULL COMMENT 'Texto de la tarea del checkbox',
  `is_checked` tinyint(1) DEFAULT 0 COMMENT 'Estado del checkbox: 0=desmarcado, 1=marcado',
  `user_id` int(11) NOT NULL COMMENT 'Usuario que marcó/desmarcó el checkbox',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`state_id`),
  UNIQUE KEY `unique_checkbox` (`comment_id`, `comment_type`, `checkbox_index`) COMMENT 'Un checkbox por comentario y posición',
  KEY `idx_comment_type` (`comment_id`, `comment_type`) COMMENT 'Índice para búsquedas por comentario',
  KEY `idx_user` (`user_id`) COMMENT 'Índice para búsquedas por usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Estados de checkboxes en comentarios de tareas y subtareas';

-- Comentarios sobre la estructura:
-- 
-- state_id: Clave primaria autoincremental
-- comment_id: Referencia al ID del comentario (Task_Comments.comment_id o Subtask_Comments.comment_id)
-- comment_type: Distingue si es comentario de tarea ('task') o subtarea ('subtask')
-- checkbox_index: Posición del checkbox dentro del comentario (0=primer checkbox, 1=segundo, etc.)
-- checkbox_text: Texto de la tarea asociada al checkbox
-- is_checked: Estado actual del checkbox (0=pendiente, 1=completado)
-- user_id: Usuario que realizó la última modificación del estado
-- 
-- UNIQUE KEY: Evita duplicados - solo puede haber un estado por checkbox específico
-- Índices: Optimizan las consultas por comentario y usuario
