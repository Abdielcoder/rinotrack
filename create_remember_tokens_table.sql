-- Script para crear la tabla remember_tokens para la funcionalidad "Recordarme"
-- Ejecutar este script en tu base de datos MySQL

CREATE TABLE IF NOT EXISTS `remember_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `token` (`token`),
  KEY `expires_at` (`expires_at`),
  CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear índice para mejorar el rendimiento de búsquedas
CREATE INDEX IF NOT EXISTS `idx_remember_tokens_expires` ON `remember_tokens` (`expires_at`);
CREATE INDEX IF NOT EXISTS `idx_remember_tokens_user_token` ON `remember_tokens` (`user_id`, `token`);

-- Comentarios sobre la tabla
-- Esta tabla almacena tokens seguros para la funcionalidad "Recordarme"
-- Los tokens se hashean con SHA-256 antes de almacenarse
-- Los tokens expiran después de 30 días
-- Se eliminan automáticamente cuando el usuario cierra sesión
-- Se limpian tokens expirados periódicamente
