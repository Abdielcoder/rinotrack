-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:8889
-- Tiempo de generación: 30-07-2025 a las 22:30:08
-- Versión del servidor: 8.0.40
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `rinotrack`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Clans`
--

CREATE TABLE `Clans` (
  `clan_id` int NOT NULL,
  `clan_name` varchar(100) NOT NULL,
  `clan_departamento` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Clans`
--

INSERT INTO `Clans` (`clan_id`, `clan_name`, `clan_departamento`, `created_at`) VALUES
(5, 'Zeus', 'Desarrollo', '2025-07-30 16:53:15'),
(6, 'Artemisa', 'Marketing', '2025-07-30 19:53:16'),
(7, 'Afrodita', 'Recursos Humanos', '2025-07-30 20:31:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Clan_KPIs`
--

CREATE TABLE `Clan_KPIs` (
  `kpi_id` int NOT NULL,
  `clan_id` int NOT NULL,
  `year` int NOT NULL,
  `quarter` int NOT NULL,
  `total_points` int DEFAULT '1000',
  `assigned_points` int DEFAULT '0',
  `status` enum('planning','active','completed','closed') DEFAULT 'planning',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Clan_Members`
--

CREATE TABLE `Clan_Members` (
  `clan_member_id` int NOT NULL,
  `clan_id` int NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Clan_Members`
--

INSERT INTO `Clan_Members` (`clan_member_id`, `clan_id`, `user_id`) VALUES
(1, 5, 2),
(2, 5, 4),
(7, 5, 5),
(6, 5, 6),
(5, 5, 9),
(4, 5, 10),
(3, 5, 11),
(8, 5, 12),
(9, 6, 13),
(10, 7, 14);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `KPI_History`
--

CREATE TABLE `KPI_History` (
  `history_id` int NOT NULL,
  `kpi_id` int NOT NULL,
  `project_id` int DEFAULT NULL,
  `task_id` int DEFAULT NULL,
  `action_type` enum('project_assigned','project_updated','task_completed','task_updated','points_redistributed') NOT NULL,
  `old_value` decimal(8,2) DEFAULT NULL,
  `new_value` decimal(8,2) DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `KPI_Quarters`
--

CREATE TABLE `KPI_Quarters` (
  `kpi_quarter_id` int NOT NULL,
  `quarter` enum('Q1','Q2','Q3','Q4') COLLATE utf8mb4_general_ci NOT NULL,
  `year` int NOT NULL,
  `total_points` int NOT NULL DEFAULT '1000',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'planning',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `KPI_Quarters`
--

INSERT INTO `KPI_Quarters` (`kpi_quarter_id`, `quarter`, `year`, `total_points`, `is_active`, `status`, `created_at`) VALUES
(2, 'Q2', 2025, 3000, 0, 'planning', '2025-07-30 22:00:58'),
(4, 'Q3', 2025, 3000, 1, 'planning', '2025-07-30 22:07:02'),
(5, 'Q1', 2025, 3000, 0, 'planning', '2025-07-30 22:07:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `success` tinyint(1) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `username`, `success`, `ip_address`, `attempt_time`) VALUES
(1, 'super', 0, '::1', '2025-07-29 23:24:04'),
(2, 'desarrollo@rinorisk.com', 0, '::1', '2025-07-29 23:24:30'),
(3, 'super', 0, '::1', '2025-07-29 23:28:37'),
(4, 'super', 0, '::1', '2025-07-30 15:56:37'),
(5, 'super', 0, '::1', '2025-07-30 16:02:56'),
(6, 'supersad', 0, '::1', '2025-07-30 16:07:27'),
(7, 'super', 1, '::1', '2025-07-30 16:10:11'),
(8, 'admin', 1, '::1', '2025-07-30 16:14:10'),
(9, 'user1', 0, '::1', '2025-07-30 16:14:32'),
(10, 'usuario1', 1, '::1', '2025-07-30 16:14:52'),
(11, 'super', 1, '::1', '2025-07-30 16:46:09'),
(12, 'super', 1, '::1', '2025-07-30 17:04:32'),
(13, 'super', 0, '::1', '2025-07-30 17:10:28'),
(14, 'super', 1, '::1', '2025-07-30 17:10:35'),
(15, 'super', 1, '::1', '2025-07-30 17:12:13'),
(16, 'abdielc', 1, '::1', '2025-07-30 17:39:47'),
(17, 'super', 1, '::1', '2025-07-30 17:40:03'),
(18, 'super', 1, '::1', '2025-07-30 18:10:22'),
(19, 'super', 0, '::1', '2025-07-30 19:40:10'),
(20, 'super', 1, '::1', '2025-07-30 19:40:17'),
(21, 'super', 1, '::1', '2025-07-30 20:03:06'),
(22, 'super', 1, '::1', '2025-07-30 20:12:10'),
(23, 'super', 1, '::1', '2025-07-30 20:45:26'),
(24, 'super', 1, '::1', '2025-07-30 20:56:46'),
(25, 'super', 1, '::1', '2025-07-30 21:13:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Projects`
--

CREATE TABLE `Projects` (
  `project_id` int NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `description` text,
  `clan_id` int NOT NULL,
  `created_by_user_id` int NOT NULL,
  `status` varchar(50) DEFAULT 'open',
  `total_tasks` int DEFAULT '0',
  `completed_tasks` int DEFAULT '0',
  `progress_percentage` decimal(5,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `kpi_quarter_id` int DEFAULT NULL,
  `kpi_points` int DEFAULT '0',
  `task_distribution_mode` enum('automatic','percentage') DEFAULT 'automatic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Projects`
--

INSERT INTO `Projects` (`project_id`, `project_name`, `description`, `clan_id`, `created_by_user_id`, `status`, `total_tasks`, `completed_tasks`, `progress_percentage`, `created_at`, `updated_at`, `kpi_quarter_id`, `kpi_points`, `task_distribution_mode`) VALUES
(1, 'Consulta estadistica', 'Módulo del portal RinoRisk', 5, 1, 'open', 0, 0, 0.00, '2025-07-30 17:29:12', '2025-07-30 22:28:12', NULL, 0, 'automatic'),
(2, 'Modulo promotoria fase 2', 'Modulo segunda parte continuacion de consulta estadistica.', 5, 1, 'open', 0, 0, 0.00, '2025-07-30 17:30:26', '2025-07-30 22:28:12', NULL, 0, 'automatic'),
(3, 'Convención MTY', 'Convención Rino', 6, 1, 'open', 0, 0, 0.00, '2025-07-30 22:09:19', '2025-07-30 22:09:19', NULL, 0, 'automatic'),
(4, 'Recluta de Agentes', 'Proyecto para reclutar Agentes.', 7, 1, 'open', 0, 0, 0.00, '2025-07-30 22:09:41', '2025-07-30 22:09:41', NULL, 0, 'automatic'),
(5, 'Consulta Promotoria', 'Fase 1 de modulo de promotoria', 5, 1, 'open', 0, 0, 0.00, '2025-07-30 22:11:09', '2025-07-30 22:11:09', NULL, 0, 'automatic'),
(6, 'Consulta de promotoria fase 2', 'Continuación de fase anterior', 5, 1, 'open', 0, 0, 0.00, '2025-07-30 22:11:37', '2025-07-30 22:11:37', NULL, 0, 'automatic');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Project_Participants`
--

CREATE TABLE `Project_Participants` (
  `project_id` int NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `user_id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Roles`
--

CREATE TABLE `Roles` (
  `role_id` int NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Roles`
--

INSERT INTO `Roles` (`role_id`, `role_name`) VALUES
(2, 'admin'),
(3, 'lider_clan'),
(1, 'super_admin'),
(4, 'usuario_normal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Tasks`
--

CREATE TABLE `Tasks` (
  `task_id` int NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `description` text,
  `project_id` int NOT NULL,
  `assigned_to_user_id` int DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Users`
--

CREATE TABLE `Users` (
  `user_id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Users`
--

INSERT INTO `Users` (`user_id`, `username`, `password_hash`, `email`, `full_name`, `is_active`, `last_login`, `created_at`) VALUES
(1, 'super', 'S4m3sg33k', 'desarrollo@rinorisk.com', 'Usuario Administrador', 1, '2025-07-30 21:13:31', '2025-07-29 22:45:12'),
(2, 'abdielc', '123456', 'abdiel@astrasoft.mx', 'Abdiel Carrasco', 1, '2025-07-30 17:39:47', '2025-07-29 23:23:21'),
(3, 'usuario1', '123456', 'usuario1@ejemplo.com', 'Juan Pérez García', 1, '2025-07-30 16:14:52', '2025-07-29 23:23:21'),
(4, 'franklinb', '$2y$10$LLd8V.r/yvPmvOKqzVcfDOubbOn10GumgRo2oeZeaLHQibe49ylZ6', 'desarollo2@rinorisk.com', 'Franklin Benitez', 1, NULL, '2025-07-30 17:10:05'),
(5, 'Rubend', '$2y$10$CPSSW.WgnObJiJD1Q56breqkrpzNO5cz8lk787y7tqInrGhyrGz/C', 'desarrollo3@rinorisk.com', 'Ruben Dorado', 1, NULL, '2025-07-30 18:20:45'),
(6, 'gaelh', '$2y$10$zzOHmc/fGsufawJpaFMybe/ZEmxFE.5ZJw4JapoxKj/MdyVxj5efW', 'desarrollo.fulstack@rinorisk.com', 'Gael Herrera', 1, NULL, '2025-07-30 19:36:11'),
(7, 'test_user_1753904255', '$2y$12$5imeZBL6zfOj.tHxHzVyl.nItqT6jF6mu49PtP/VU8T4E1dzPuQRy', 'test1753904255@example.com', 'Usuario de Prueba', 0, NULL, '2025-07-30 19:37:36'),
(9, 'manuels', '$2y$10$tcoYFWXaX1nbnBWosf5BD.YK5Tgdg3GeL9uqz9y75VtpfTr.WtOkq', 'desarrollo.frontjr@rinorisk.com', 'Manuel Saenz', 1, NULL, '2025-07-30 19:46:47'),
(10, 'piña', '$2y$10$P1vRT7hTEjcjz2qSoYA.feIKunD/RlObvCO9p53rCvxMVHhXfmbny', 'desarrollo.dataanalyst@rinorisk.com', 'Jaen Piña', 1, NULL, '2025-07-30 19:47:38'),
(11, 'isaccg', '$2y$10$GnU7HjvlBuZjURI45W2xCePOaroIXwx6RHkDvkJlQ8FryO/dSpaOG', 'desarrollo.backend@rinorisk.com', 'Isaac Garcia', 1, NULL, '2025-07-30 19:48:29'),
(12, 'janathanm', '$2y$10$NKs3M1V9Mpva0PCkRn9M0uDuiliIUjO.bBvazpZtdwIJclHVnNshW', 'desarrollo.fullstack.2@rinorisk.com', 'Jonathan Martinez', 1, NULL, '2025-07-30 19:51:19'),
(13, 'jessicam', '$2y$10$VNkjYILfSFR3.g2SLLX0YOXjbcUxmdAT3bwhO/tBvPrW9ETdUC/tm', 'gerente.mkt@rinorisk.com', 'Jessica Mejia', 1, NULL, '2025-07-30 19:54:06'),
(14, 'bereniceh', '$2y$10$e3RgLtOf5qrPKf8opaUA.O2j9LYm/dT0oO6TG5CamkihhSk1kg.Tm', 'gerente.rh@rinorisk.com', 'Berenice Hernandez', 1, NULL, '2025-07-30 20:35:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `User_Roles`
--

CREATE TABLE `User_Roles` (
  `user_id` int NOT NULL,
  `role_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `User_Roles`
--

INSERT INTO `User_Roles` (`user_id`, `role_id`) VALUES
(1, 1),
(2, 3),
(13, 3),
(14, 3),
(3, 4),
(4, 4),
(5, 4),
(6, 4),
(9, 4),
(10, 4),
(11, 4),
(12, 4);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Clans`
--
ALTER TABLE `Clans`
  ADD PRIMARY KEY (`clan_id`),
  ADD UNIQUE KEY `clan_name` (`clan_name`);

--
-- Indices de la tabla `Clan_KPIs`
--
ALTER TABLE `Clan_KPIs`
  ADD PRIMARY KEY (`kpi_id`),
  ADD UNIQUE KEY `unique_clan_quarter` (`clan_id`,`year`,`quarter`),
  ADD KEY `idx_clan_kpis_year_quarter` (`year`,`quarter`);

--
-- Indices de la tabla `Clan_Members`
--
ALTER TABLE `Clan_Members`
  ADD PRIMARY KEY (`clan_member_id`),
  ADD UNIQUE KEY `clan_id` (`clan_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `KPI_History`
--
ALTER TABLE `KPI_History`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `kpi_id` (`kpi_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `KPI_Quarters`
--
ALTER TABLE `KPI_Quarters`
  ADD PRIMARY KEY (`kpi_quarter_id`),
  ADD UNIQUE KEY `unique_quarter_year` (`quarter`,`year`);

--
-- Indices de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_ip_time` (`ip_address`,`attempt_time`),
  ADD KEY `idx_attempt_time` (`attempt_time`);

--
-- Indices de la tabla `Projects`
--
ALTER TABLE `Projects`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `clan_id` (`clan_id`),
  ADD KEY `created_by_user_id` (`created_by_user_id`),
  ADD KEY `idx_projects_kpi_quarter` (`kpi_quarter_id`);

--
-- Indices de la tabla `Project_Participants`
--
ALTER TABLE `Project_Participants`
  ADD PRIMARY KEY (`project_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indices de la tabla `Roles`
--
ALTER TABLE `Roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indices de la tabla `Tasks`
--
ALTER TABLE `Tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `idx_tasks_project` (`project_id`),
  ADD KEY `idx_tasks_assigned_user` (`assigned_to_user_id`);

--
-- Indices de la tabla `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `User_Roles`
--
ALTER TABLE `User_Roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Clans`
--
ALTER TABLE `Clans`
  MODIFY `clan_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `Clan_KPIs`
--
ALTER TABLE `Clan_KPIs`
  MODIFY `kpi_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Clan_Members`
--
ALTER TABLE `Clan_Members`
  MODIFY `clan_member_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `KPI_History`
--
ALTER TABLE `KPI_History`
  MODIFY `history_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `KPI_Quarters`
--
ALTER TABLE `KPI_Quarters`
  MODIFY `kpi_quarter_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `Projects`
--
ALTER TABLE `Projects`
  MODIFY `project_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `Roles`
--
ALTER TABLE `Roles`
  MODIFY `role_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `Tasks`
--
ALTER TABLE `Tasks`
  MODIFY `task_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Clan_KPIs`
--
ALTER TABLE `Clan_KPIs`
  ADD CONSTRAINT `clan_kpis_ibfk_1` FOREIGN KEY (`clan_id`) REFERENCES `Clans` (`clan_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Clan_Members`
--
ALTER TABLE `Clan_Members`
  ADD CONSTRAINT `clan_members_ibfk_1` FOREIGN KEY (`clan_id`) REFERENCES `Clans` (`clan_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `clan_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `KPI_History`
--
ALTER TABLE `KPI_History`
  ADD CONSTRAINT `kpi_history_ibfk_1` FOREIGN KEY (`kpi_id`) REFERENCES `Clan_KPIs` (`kpi_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kpi_history_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`project_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kpi_history_ibfk_3` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`task_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kpi_history_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `Projects`
--
ALTER TABLE `Projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`clan_id`) REFERENCES `Clans` (`clan_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`created_by_user_id`) REFERENCES `Users` (`user_id`) ON DELETE RESTRICT;

--
-- Filtros para la tabla `Project_Participants`
--
ALTER TABLE `Project_Participants`
  ADD CONSTRAINT `project_participants_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Tasks`
--
ALTER TABLE `Tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `Users` (`user_id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `User_Roles`
--
ALTER TABLE `User_Roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `Roles` (`role_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
