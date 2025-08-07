-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:8889
-- Tiempo de generación: 07-08-2025 a las 15:35:45
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
(5, 'Kratos', 'Desarrollo/Transformación/Sistemas', '2025-07-30 16:53:15'),
(6, 'Artemisa', 'Marketing', '2025-07-30 19:53:16'),
(7, 'Afrodita', 'Recursos Humanos', '2025-07-30 20:31:47'),
(8, 'Persefone', 'Servicio', '2025-08-02 17:50:21'),
(10, 'Aura', 'ZAX', '2025-08-04 17:57:03'),
(11, 'Hermes', 'Comercial', '2025-08-06 20:43:53'),
(12, 'GAIA', 'Operación/Proyectos', '2025-08-06 20:50:45'),
(13, 'ZEUS', 'Dirección', '2025-08-06 20:56:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Clan_KPIs`
--

CREATE TABLE `Clan_KPIs` (
  `kpi_id` int NOT NULL,
  `clan_id` int NOT NULL,
  `kpi_quarter_id` int NOT NULL,
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
(82, 5, 38),
(9, 6, 13),
(11, 6, 15),
(17, 6, 17),
(19, 6, 18),
(21, 6, 44),
(10, 7, 14),
(13, 7, 39),
(15, 7, 40),
(68, 8, 19),
(69, 8, 20),
(77, 8, 21),
(71, 8, 45),
(73, 8, 46),
(75, 8, 47),
(79, 8, 48),
(81, 8, 55),
(31, 10, 41),
(83, 10, 42),
(30, 10, 43),
(32, 10, 50),
(84, 11, 56),
(85, 11, 57),
(33, 12, 22),
(34, 12, 23),
(36, 12, 24),
(38, 12, 25),
(40, 12, 26),
(42, 12, 27),
(44, 12, 28),
(46, 12, 29),
(48, 12, 30),
(54, 12, 31),
(56, 12, 32),
(58, 12, 33),
(60, 12, 34),
(62, 12, 35),
(64, 12, 36),
(66, 12, 37),
(52, 12, 48),
(50, 12, 49),
(86, 13, 16),
(88, 13, 58);

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
(11, 'Q3', 2025, 20000, 1, 'active', '2025-08-05 15:55:52');

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
(25, 'super', 1, '::1', '2025-07-30 21:13:31'),
(26, 'super', 0, '::1', '2025-07-31 20:15:21'),
(27, 'super', 0, '::1', '2025-07-31 20:15:24'),
(28, 'super', 1, '::1', '2025-07-31 20:16:24'),
(29, 'super', 1, '::1', '2025-07-31 21:55:35'),
(30, 'super', 1, '::1', '2025-07-31 22:29:40'),
(31, 'super', 1, '::1', '2025-07-31 23:09:01'),
(32, 'super', 1, '::1', '2025-07-31 23:11:55'),
(33, 'super', 1, '::1', '2025-08-01 16:47:07'),
(34, 'super', 1, '::1', '2025-08-01 17:02:12'),
(35, 'super', 1, '::1', '2025-08-01 17:03:36'),
(36, 'super', 1, '::1', '2025-08-01 17:18:28'),
(37, 'abdielc', 1, '::1', '2025-08-01 17:33:12'),
(38, 'abdielc', 1, '::1', '2025-08-01 20:43:34'),
(39, 'abdielc', 1, '::1', '2025-08-01 20:57:44'),
(40, 'abdielc', 1, '::1', '2025-08-01 20:58:51'),
(41, 'abdielc@rinorisk.com', 0, '::1', '2025-08-02 17:36:35'),
(42, 'abdielc', 1, '::1', '2025-08-02 17:36:45'),
(43, 'super', 1, '::1', '2025-08-02 17:47:51'),
(44, 'abdielc', 1, '::1', '2025-08-04 17:03:08'),
(45, 'super', 1, '::1', '2025-08-04 17:49:15'),
(46, 'abdielc', 1, '::1', '2025-08-04 17:57:26'),
(47, 'jessicam', 0, '::1', '2025-08-04 19:09:04'),
(48, 'super', 1, '::1', '2025-08-04 19:09:12'),
(49, 'jessicam', 0, '::1', '2025-08-04 19:09:35'),
(50, 'jessicam', 1, '::1', '2025-08-04 19:09:57'),
(51, 'super', 1, '::1', '2025-08-04 19:10:39'),
(52, 'jessicam', 1, '::1', '2025-08-04 19:12:19'),
(53, 'abdielc', 1, '::1', '2025-08-04 19:29:07'),
(54, 'abdielc', 1, '::1', '2025-08-04 20:15:56'),
(55, 'super', 1, '::1', '2025-08-04 21:52:57'),
(56, 'super', 0, '::1', '2025-08-04 22:09:10'),
(57, 'super', 1, '::1', '2025-08-04 22:09:18'),
(58, 'abdielc', 1, '::1', '2025-08-04 22:37:58'),
(59, 'super', 1, '::1', '2025-08-04 22:39:30'),
(60, 'abdielc', 1, '::1', '2025-08-04 22:42:35'),
(61, 'super', 1, '::1', '2025-08-04 22:43:25'),
(62, 'abdielc', 1, '::1', '2025-08-04 22:48:32'),
(63, 'jessicam', 1, '::1', '2025-08-04 23:05:48'),
(64, 'abdielc', 1, '::1', '2025-08-04 23:06:21'),
(65, 'super', 1, '::1', '2025-08-04 23:20:49'),
(66, 'abdielc', 1, '::1', '2025-08-04 23:30:18'),
(67, 'super', 1, '::1', '2025-08-05 15:55:32'),
(68, 'abdielc', 1, '::1', '2025-08-05 16:22:37'),
(69, 'super', 1, '::1', '2025-08-05 16:27:57'),
(70, 'abdielc', 1, '::1', '2025-08-05 17:42:14'),
(71, 'super', 1, '::1', '2025-08-05 17:51:18'),
(72, 'abdielc', 1, '::1', '2025-08-05 19:13:32'),
(73, 'abdielc', 1, '::1', '2025-08-05 19:33:28'),
(74, 'super', 1, '::1', '2025-08-05 19:33:42'),
(75, 'super', 1, '::1', '2025-08-05 19:35:01'),
(76, 'super', 1, '::1', '2025-08-05 19:37:34'),
(77, 'abdielc', 1, '::1', '2025-08-05 20:51:08'),
(78, 'super', 1, '::1', '2025-08-06 19:34:12'),
(79, 'super', 1, '::1', '2025-08-06 21:11:48'),
(80, 'bereniceh', 0, '::1', '2025-08-06 21:55:08'),
(81, 'bereniceh', 1, '::1', '2025-08-06 21:57:04'),
(82, 'jessicam', 1, '::1', '2025-08-06 22:01:01'),
(83, 'sofiag', 1, '::1', '2025-08-06 22:02:26'),
(84, 'arisbethc', 1, '::1', '2025-08-06 22:03:40'),
(85, 'abdielc', 1, '::1', '2025-08-06 22:21:14'),
(86, 'super', 1, '::1', '2025-08-06 22:21:28'),
(87, 'arisbethc', 1, '::1', '2025-08-06 22:58:27'),
(88, 'bereniceh', 1, '::1', '2025-08-06 22:59:36'),
(89, 'super', 1, '::1', '2025-08-06 23:00:16'),
(90, 'karenf', 1, '::1', '2025-08-06 23:06:37'),
(91, 'jessicam', 1, '::1', '2025-08-06 23:07:26'),
(92, 'karenf', 1, '::1', '2025-08-06 23:07:53'),
(93, 'super', 1, '::1', '2025-08-06 23:08:42'),
(94, 'sofiag', 1, '::1', '2025-08-06 23:09:09'),
(95, 'super', 1, '::1', '2025-08-06 23:09:42'),
(96, 'sofiag', 1, '::1', '2025-08-06 23:10:23'),
(97, 'super', 1, '::1', '2025-08-06 23:14:40'),
(98, 'arisbethc', 1, '::1', '2025-08-06 23:16:08'),
(99, 'super', 1, '::1', '2025-08-06 23:18:43'),
(100, 'ivan2', 0, '::1', '2025-08-06 23:26:34'),
(101, 'ivan2', 1, '::1', '2025-08-06 23:27:16'),
(102, 'super', 1, '::1', '2025-08-06 23:28:01'),
(103, 'ivan2', 1, '::1', '2025-08-06 23:29:34'),
(104, 'ivan3', 1, '::1', '2025-08-06 23:30:00'),
(105, 'super', 1, '::1', '2025-08-06 23:30:41'),
(106, 'ivan3', 1, '::1', '2025-08-06 23:31:27'),
(107, 'super', 1, '::1', '2025-08-06 23:32:50'),
(108, 'abdielc', 1, '::1', '2025-08-06 23:34:39'),
(109, 'abdielc', 1, '::1', '2025-08-07 15:28:03'),
(110, 'jessicam', 1, '::1', '2025-08-07 15:28:31'),
(111, 'arisbethc', 1, '::1', '2025-08-07 15:28:49'),
(112, 'sofiag', 1, '::1', '2025-08-07 15:29:09'),
(113, 'bereniceh', 1, '::1', '2025-08-07 15:29:26'),
(114, 'karenf', 1, '::1', '2025-08-07 15:29:47'),
(115, 'ivanm', 1, '::1', '2025-08-07 15:30:04'),
(116, 'super', 1, '::1', '2025-08-07 15:30:49'),
(117, 'ivan2', 1, '::1', '2025-08-07 15:31:57'),
(118, 'ivan3', 1, '::1', '2025-08-07 15:32:18'),
(119, 'ivanm', 1, '::1', '2025-08-07 15:33:14');

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
(14, 'Consulta de promotoria Fase 1', 'Primera fase operativa de relaciones de colaboradores', 5, 2, 'open', 0, 0, 0.00, '2025-08-05 21:01:49', '2025-08-05 22:29:24', 11, 400, 'automatic'),
(15, 'Nuevos indicadores / Procesos automaticos', 'Investigación y desarrollo de robots para obtener nuevas fuentes de información', 5, 2, 'open', 0, 0, 0.00, '2025-08-05 22:08:11', '2025-08-05 22:29:38', 11, 350, 'automatic'),
(16, 'Consulta de promotoria fase 2', 'Continuación de Fase 1-', 5, 2, 'open', 0, 0, 0.00, '2025-08-05 22:08:59', '2025-08-06 23:34:59', 11, 250, 'automatic');

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
-- Estructura de tabla para la tabla `Subtasks`
--

CREATE TABLE `Subtasks` (
  `subtask_id` int NOT NULL,
  `task_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `completion_percentage` decimal(5,2) DEFAULT '0.00',
  `estimated_hours` decimal(5,2) DEFAULT NULL,
  `actual_hours` decimal(5,2) DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `due_date` date DEFAULT NULL,
  `assigned_to_user_id` int DEFAULT NULL,
  `created_by_user_id` int NOT NULL,
  `subtask_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Disparadores `Subtasks`
--
DELIMITER $$
CREATE TRIGGER `update_parent_task_completion` AFTER UPDATE ON `Subtasks` FOR EACH ROW BEGIN
    DECLARE parent_completion DECIMAL(5,2);
    
    
    SELECT COALESCE(AVG(completion_percentage), 0) INTO parent_completion
    FROM Subtasks 
    WHERE task_id = NEW.task_id;
    
    
    UPDATE Tasks 
    SET completion_percentage = parent_completion,
        updated_at = CURRENT_TIMESTAMP
    WHERE task_id = NEW.task_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Tasks`
--

CREATE TABLE `Tasks` (
  `task_id` int NOT NULL,
  `parent_task_id` int DEFAULT NULL,
  `task_name` varchar(255) NOT NULL,
  `description` text,
  `project_id` int NOT NULL,
  `assigned_to_user_id` int DEFAULT NULL,
  `created_by_user_id` int DEFAULT NULL,
  `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `due_date` date DEFAULT NULL,
  `estimated_hours` decimal(5,2) DEFAULT NULL,
  `actual_hours` decimal(5,2) DEFAULT NULL,
  `completion_percentage` decimal(5,2) DEFAULT '0.00',
  `automatic_points` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Puntos KPI calculados para modo automático',
  `assigned_percentage` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'Porcentaje de puntos KPI asignado manualmente',
  `color_tag` varchar(7) DEFAULT '#3B82F6',
  `is_subtask` tinyint(1) DEFAULT '0',
  `subtask_order` int DEFAULT '0',
  `status` enum('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `is_completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Tasks`
--

INSERT INTO `Tasks` (`task_id`, `parent_task_id`, `task_name`, `description`, `project_id`, `assigned_to_user_id`, `created_by_user_id`, `priority`, `due_date`, `estimated_hours`, `actual_hours`, `completion_percentage`, `automatic_points`, `assigned_percentage`, `color_tag`, `is_subtask`, `subtask_order`, `status`, `is_completed`, `completed_at`, `created_at`, `updated_at`) VALUES
(28, NULL, 'QA', 'Realizar pruebas de datos, estructura información, y schemas.', 14, 2, 2, 'medium', '2025-08-19', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 21:03:15', '2025-08-05 22:29:24'),
(29, NULL, 'Nuevos accesos en interfaz para nueva logica de usuarios', 'Gestion de interfaz para manejar visualmente los accesos.', 14, 9, 2, 'medium', '2025-08-08', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-05 22:33:37', '2025-08-05 21:08:13', '2025-08-05 22:33:37'),
(30, NULL, 'Filtros por ROL', 'Nueva logica de filtro por rol', 14, 9, 2, 'medium', '2025-08-11', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 21:08:58', '2025-08-05 22:29:24'),
(31, NULL, 'Selector de Usuario', 'Logica que de termina visualmente la forma en que los usuarios y claves se asignan bajo ciertas circunstancias.', 14, 5, 2, 'medium', '2025-08-15', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 21:10:31', '2025-08-05 22:29:24'),
(32, NULL, 'Ajustes de integración', 'Ajustes de integración', 14, 6, 2, 'medium', '2025-08-15', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 21:16:51', '2025-08-05 22:29:24'),
(33, NULL, 'Merge a la rama FEAT consulta nuevos resultados 2', 'Merge a la rama FEAT consulta nuevos resultados 2', 14, 6, 2, 'medium', '2025-08-07', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-05 22:33:32', '2025-08-05 21:18:46', '2025-08-05 22:33:32'),
(34, NULL, 'Implementar filtros de la rama (Nuevos filtros)', 'Implementar filtros de la rama (Nuevos filtros)', 14, 6, 2, 'medium', '2025-08-15', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 21:19:48', '2025-08-05 22:29:24'),
(35, NULL, 'Gestión de agentes (Usuarios de gran volumen)', 'Gestión de agentes (Usuarios de gran volumen)', 14, 9, 2, 'medium', '2025-08-15', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 21:21:08', '2025-08-05 22:29:24'),
(36, NULL, 'Separación resultados Eject. Agente.', 'Separación resultados Eject. Agente.', 14, 6, 2, 'medium', '2025-08-13', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 21:23:45', '2025-08-05 22:29:24'),
(37, NULL, 'Resegmentación de Robots', 'Resegmentación de Robots', 14, 11, 2, 'medium', '2025-08-12', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 21:24:59', '2025-08-05 22:29:24'),
(38, NULL, 'Filtros por mes API', 'Filtros por mes API', 14, 12, 2, 'medium', '2025-08-08', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 21:25:30', '2025-08-05 22:29:24'),
(39, NULL, 'Relación usuarios Backend', 'Relación usuarios Backend', 14, 12, 2, 'medium', '2025-08-14', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 21:26:23', '2025-08-05 22:29:24'),
(40, NULL, 'Administracion Usuarios', 'Administracion Usuarios', 14, 4, 2, 'medium', '2025-08-15', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-05 22:33:52', '2025-08-05 21:27:36', '2025-08-05 22:33:52'),
(41, NULL, 'Figmas de propuesta', '', 14, 5, 2, 'medium', '2025-08-05', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-05 22:33:24', '2025-08-05 22:03:53', '2025-08-05 22:33:24'),
(42, NULL, 'Investigación Indicadores estrella', 'Investigación Indicadores estrella', 15, 11, 2, 'medium', '2025-09-05', NULL, NULL, 0.00, 175.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 22:20:26', '2025-08-05 22:29:38'),
(43, NULL, 'Elaboración de PHU', 'Crear PHU para su aprobación', 15, 2, 2, 'medium', '2025-08-15', NULL, NULL, 0.00, 175.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 22:21:59', '2025-08-05 22:29:38'),
(44, NULL, 'Creación de PHU', 'Creación de PHU para su aprobación', 16, 2, 2, 'medium', '2025-08-29', NULL, NULL, 0.00, 250.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 22:22:38', '2025-08-05 22:30:14');

--
-- Disparadores `Tasks`
--
DELIMITER $$
CREATE TRIGGER `log_task_changes` AFTER UPDATE ON `Tasks` FOR EACH ROW BEGIN
    
    IF OLD.status != NEW.status THEN
        INSERT INTO Task_History (task_id, user_id, action_type, field_name, old_value, new_value, notes)
        VALUES (NEW.task_id, COALESCE(NEW.assigned_to_user_id, NEW.created_by_user_id), 'status_changed', 'status', OLD.status, NEW.status, CONCAT('Estado cambiado de ', OLD.status, ' a ', NEW.status));
    END IF;
    
    
    IF OLD.assigned_to_user_id != NEW.assigned_to_user_id THEN
        INSERT INTO Task_History (task_id, user_id, action_type, field_name, old_value, new_value, related_user_id, notes)
        VALUES (NEW.task_id, COALESCE(NEW.assigned_to_user_id, NEW.created_by_user_id), 'assigned', 'assigned_to_user_id', OLD.assigned_to_user_id, NEW.assigned_to_user_id, NEW.assigned_to_user_id, 'Usuario asignado a la tarea');
    END IF;
    
    
    IF OLD.due_date != NEW.due_date THEN
        INSERT INTO Task_History (task_id, user_id, action_type, field_name, old_value, new_value, notes)
        VALUES (NEW.task_id, COALESCE(NEW.assigned_to_user_id, NEW.created_by_user_id), 'updated', 'due_date', OLD.due_date, NEW.due_date, 'Fecha de vencimiento actualizada');
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Task_Assignments`
--

CREATE TABLE `Task_Assignments` (
  `assignment_id` int NOT NULL,
  `task_id` int NOT NULL,
  `user_id` int NOT NULL,
  `assigned_percentage` decimal(5,2) DEFAULT '0.00',
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `assigned_by_user_id` int DEFAULT NULL,
  `status` enum('assigned','accepted','declined','completed') DEFAULT 'assigned',
  `notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Task_Assignments`
--

INSERT INTO `Task_Assignments` (`assignment_id`, `task_id`, `user_id`, `assigned_percentage`, `assigned_at`, `assigned_by_user_id`, `status`, `notes`) VALUES
(49, 28, 2, 12.50, '2025-08-05 21:03:15', 2, 'assigned', NULL),
(50, 28, 4, 12.50, '2025-08-05 21:03:15', 2, 'assigned', NULL),
(51, 28, 6, 12.50, '2025-08-05 21:03:15', 2, 'assigned', NULL),
(52, 28, 11, 12.50, '2025-08-05 21:03:15', 2, 'assigned', NULL),
(53, 28, 10, 12.50, '2025-08-05 21:03:15', 2, 'assigned', NULL),
(54, 28, 12, 12.50, '2025-08-05 21:03:15', 2, 'assigned', NULL),
(55, 28, 9, 12.50, '2025-08-05 21:03:15', 2, 'assigned', NULL),
(56, 28, 5, 12.50, '2025-08-05 21:03:15', 2, 'assigned', NULL),
(57, 29, 9, 50.00, '2025-08-05 21:08:13', 2, 'assigned', NULL),
(58, 29, 5, 50.00, '2025-08-05 21:08:13', 2, 'assigned', NULL),
(59, 30, 9, 50.00, '2025-08-05 21:08:58', 2, 'assigned', NULL),
(60, 30, 5, 50.00, '2025-08-05 21:08:58', 2, 'assigned', NULL),
(61, 31, 5, 100.00, '2025-08-05 21:10:31', 2, 'assigned', NULL),
(62, 32, 6, 33.33, '2025-08-05 21:16:51', 2, 'assigned', NULL),
(63, 32, 10, 33.33, '2025-08-05 21:16:51', 2, 'assigned', NULL),
(64, 32, 12, 33.33, '2025-08-05 21:16:51', 2, 'assigned', NULL),
(65, 33, 6, 50.00, '2025-08-05 21:18:46', 2, 'assigned', NULL),
(66, 33, 12, 50.00, '2025-08-05 21:18:46', 2, 'assigned', NULL),
(67, 34, 6, 50.00, '2025-08-05 21:19:48', 2, 'assigned', NULL),
(68, 34, 12, 50.00, '2025-08-05 21:19:48', 2, 'assigned', NULL),
(69, 35, 9, 50.00, '2025-08-05 21:21:08', 2, 'assigned', NULL),
(70, 35, 5, 50.00, '2025-08-05 21:21:08', 2, 'assigned', NULL),
(71, 36, 6, 50.00, '2025-08-05 21:23:45', 2, 'assigned', NULL),
(72, 36, 10, 50.00, '2025-08-05 21:23:45', 2, 'assigned', NULL),
(73, 37, 11, 50.00, '2025-08-05 21:24:59', 2, 'assigned', NULL),
(74, 37, 10, 50.00, '2025-08-05 21:24:59', 2, 'assigned', NULL),
(75, 38, 12, 100.00, '2025-08-05 21:25:30', 2, 'assigned', NULL),
(76, 39, 12, 100.00, '2025-08-05 21:26:23', 2, 'assigned', NULL),
(77, 40, 4, 50.00, '2025-08-05 21:27:36', 2, 'assigned', NULL),
(78, 40, 9, 50.00, '2025-08-05 21:27:36', 2, 'assigned', NULL),
(79, 41, 5, 100.00, '2025-08-05 22:03:53', 2, 'assigned', NULL),
(80, 42, 11, 50.00, '2025-08-05 22:20:26', 2, 'assigned', NULL),
(81, 42, 10, 50.00, '2025-08-05 22:20:26', 2, 'assigned', NULL),
(82, 43, 2, 100.00, '2025-08-05 22:21:59', 2, 'assigned', NULL),
(83, 44, 2, 100.00, '2025-08-05 22:22:38', 2, 'assigned', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Task_Attachments`
--

CREATE TABLE `Task_Attachments` (
  `attachment_id` int NOT NULL,
  `task_id` int NOT NULL,
  `user_id` int NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `description` text,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Task_Comments`
--

CREATE TABLE `Task_Comments` (
  `comment_id` int NOT NULL,
  `task_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment_text` text NOT NULL,
  `comment_type` enum('comment','status_change','assignment','completion','system') DEFAULT 'comment',
  `related_user_id` int DEFAULT NULL,
  `old_value` varchar(255) DEFAULT NULL,
  `new_value` varchar(255) DEFAULT NULL,
  `is_private` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Task_History`
--

CREATE TABLE `Task_History` (
  `history_id` int NOT NULL,
  `task_id` int NOT NULL,
  `user_id` int NOT NULL,
  `action_type` enum('created','updated','status_changed','assigned','unassigned','commented','completed','reopened','deleted') NOT NULL,
  `field_name` varchar(50) DEFAULT NULL,
  `old_value` text,
  `new_value` text,
  `related_user_id` int DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Task_History`
--

INSERT INTO `Task_History` (`history_id`, `task_id`, `user_id`, `action_type`, `field_name`, `old_value`, `new_value`, `related_user_id`, `notes`, `created_at`) VALUES
(79, 28, 2, 'assigned', 'assigned_users', NULL, '2,4,6,11,10,12,9,5', NULL, 'Múltiples usuarios asignados', '2025-08-05 21:03:15'),
(80, 28, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 21:03:15'),
(81, 29, 2, 'assigned', 'assigned_users', NULL, '9,5', NULL, 'Múltiples usuarios asignados', '2025-08-05 21:08:13'),
(82, 29, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 21:08:13'),
(83, 30, 2, 'assigned', 'assigned_users', NULL, '9,5', NULL, 'Múltiples usuarios asignados', '2025-08-05 21:08:58'),
(84, 30, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 21:08:58'),
(85, 31, 2, 'assigned', 'assigned_users', NULL, '5', NULL, 'Múltiples usuarios asignados', '2025-08-05 21:10:31'),
(86, 31, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 21:10:31'),
(87, 32, 2, 'assigned', 'assigned_users', NULL, '6,10,12', NULL, 'Múltiples usuarios asignados', '2025-08-05 21:16:51'),
(88, 32, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 21:16:51'),
(89, 33, 2, 'assigned', 'assigned_users', NULL, '6,12', NULL, 'Múltiples usuarios asignados', '2025-08-05 21:18:46'),
(90, 33, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 21:18:46'),
(91, 34, 2, 'assigned', 'assigned_users', NULL, '6,12', NULL, 'Múltiples usuarios asignados', '2025-08-05 21:19:48'),
(92, 34, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 21:19:48'),
(93, 35, 2, 'assigned', 'assigned_users', NULL, '9,5', NULL, 'Múltiples usuarios asignados', '2025-08-05 21:21:08'),
(94, 35, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 21:21:08'),
(95, 36, 2, 'assigned', 'assigned_users', NULL, '6,10', NULL, 'Múltiples usuarios asignados', '2025-08-05 21:23:45'),
(96, 36, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 21:23:45'),
(97, 37, 2, 'assigned', 'assigned_users', NULL, '11,10', NULL, 'Múltiples usuarios asignados', '2025-08-05 21:24:59'),
(98, 37, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 21:24:59'),
(99, 38, 2, 'assigned', 'assigned_users', NULL, '12', NULL, 'Múltiples usuarios asignados', '2025-08-05 21:25:30'),
(100, 38, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 21:25:30'),
(101, 39, 2, 'assigned', 'assigned_users', NULL, '12', NULL, 'Múltiples usuarios asignados', '2025-08-05 21:26:23'),
(102, 39, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 21:26:23'),
(103, 40, 2, 'assigned', 'assigned_users', NULL, '4,9', NULL, 'Múltiples usuarios asignados', '2025-08-05 21:27:36'),
(104, 40, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 21:27:36'),
(105, 41, 2, 'assigned', 'assigned_users', NULL, '5', NULL, 'Múltiples usuarios asignados', '2025-08-05 22:03:53'),
(106, 41, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 22:03:53'),
(107, 42, 2, 'assigned', 'assigned_users', NULL, '11,10', NULL, 'Múltiples usuarios asignados', '2025-08-05 22:20:26'),
(108, 42, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 22:20:26'),
(109, 43, 2, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-05 22:21:59'),
(110, 43, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 22:21:59'),
(111, 44, 2, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-05 22:22:38'),
(112, 44, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 22:22:38'),
(113, 41, 5, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-05 22:33:24'),
(114, 33, 6, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-05 22:33:32'),
(115, 29, 9, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-05 22:33:37'),
(116, 40, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-05 22:33:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Task_Labels`
--

CREATE TABLE `Task_Labels` (
  `label_id` int NOT NULL,
  `label_name` varchar(50) NOT NULL,
  `label_color` varchar(7) DEFAULT '#3B82F6',
  `clan_id` int DEFAULT NULL,
  `created_by_user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Task_Labels`
--

INSERT INTO `Task_Labels` (`label_id`, `label_name`, `label_color`, `clan_id`, `created_by_user_id`, `created_at`) VALUES
(1, 'Bug', '#EF4444', 5, 1, '2025-08-01 20:02:00'),
(2, 'Feature', '#10B981', 5, 1, '2025-08-01 20:02:00'),
(3, 'Refactor', '#F59E0B', 5, 1, '2025-08-01 20:02:00'),
(4, 'Documentation', '#8B5CF6', 5, 1, '2025-08-01 20:02:00'),
(5, 'Testing', '#06B6D4', 5, 1, '2025-08-01 20:02:00'),
(6, 'Campaign', '#EF4444', 6, 1, '2025-08-01 20:02:00'),
(7, 'Content', '#10B981', 6, 1, '2025-08-01 20:02:00'),
(8, 'Design', '#F59E0B', 6, 1, '2025-08-01 20:02:00'),
(9, 'Social Media', '#8B5CF6', 6, 1, '2025-08-01 20:02:00'),
(10, 'Analytics', '#06B6D4', 6, 1, '2025-08-01 20:02:00'),
(11, 'Recruitment', '#EF4444', 7, 1, '2025-08-01 20:02:00'),
(12, 'Training', '#10B981', 7, 1, '2025-08-01 20:02:00'),
(13, 'Benefits', '#F59E0B', 7, 1, '2025-08-01 20:02:00'),
(14, 'Compliance', '#8B5CF6', 7, 1, '2025-08-01 20:02:00'),
(15, 'Employee Relations', '#06B6D4', 7, 1, '2025-08-01 20:02:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Task_Label_Assignments`
--

CREATE TABLE `Task_Label_Assignments` (
  `task_id` int NOT NULL,
  `label_id` int NOT NULL,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `assigned_by_user_id` int DEFAULT NULL
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
(1, 'super', '123456', 'desarrollo@rinorisk.com', 'Usuario Administrador', 1, '2025-08-07 15:30:49', '2025-07-29 22:45:12'),
(2, 'abdielc', '123456', 'abdiel@astrasoft.mx', 'Abdiel Carrasco', 1, '2025-08-07 15:28:03', '2025-07-29 23:23:21'),
(3, 'usuario1', '123456', 'usuario1@ejemplo.com', 'Juan Pérez García', 1, '2025-07-30 16:14:52', '2025-07-29 23:23:21'),
(4, 'franklinb', '123456', 'desarollo2@rinorisk.com', 'Franklin Benitez', 1, NULL, '2025-07-30 17:10:05'),
(5, 'Rubend', '123456', 'desarrollo3@rinorisk.com', 'Ruben Dorado', 1, NULL, '2025-07-30 18:20:45'),
(6, 'gaelh', '123456', 'desarrollo.fulstack@rinorisk.com', 'Gael Herrera', 1, NULL, '2025-07-30 19:36:11'),
(7, 'test_user_1753904255', '123456', 'test1753904255@example.com', 'Usuario de Prueba', 0, NULL, '2025-07-30 19:37:36'),
(9, 'manuels', '123456', 'desarrollo.frontjr@rinorisk.com', 'Manuel Saenz', 1, NULL, '2025-07-30 19:46:47'),
(10, 'piña', '123456', 'desarrollo.dataanalyst@rinorisk.com', 'Jaen Piña', 1, NULL, '2025-07-30 19:47:38'),
(11, 'isaccg', '123456', 'desarrollo.backend@rinorisk.com', 'Isaac Garcia', 1, NULL, '2025-07-30 19:48:29'),
(12, 'janathanm', '123456', 'desarrollo.fullstack.2@rinorisk.com', 'Jonathan Martinez', 1, NULL, '2025-07-30 19:51:19'),
(13, 'jessicam', '123456', 'gerente.mkt@rinorisk.com', 'Jessica Mejia', 1, '2025-08-07 15:28:31', '2025-07-30 19:54:06'),
(14, 'bereniceh', '123456', 'gerente.rh@rinorisk.com', 'Berenice Hernandez', 1, '2025-08-07 15:29:26', '2025-07-30 20:35:34'),
(15, 'Valeriag', '123456', 'cultura.organizacional@rinorisk.com', 'Valeria Garcia', 1, NULL, '2025-08-04 19:11:54'),
(16, 'Nora', '123456', 'sincorreo1@rinorisk.com', 'Nora', 1, NULL, '2025-08-06 21:01:29'),
(17, 'ricardov', '123456', 'redes@rinorisk.com', 'Ricardo Vidaño', 1, NULL, '2025-08-06 21:03:34'),
(18, 'israell', '123456', 'publicidad@rinorisk.com', 'Israel Lovato', 1, NULL, '2025-08-06 21:04:17'),
(19, 'arisbethc', '123456', 'supervisor.comercialtj@rinorisk.com', 'Arisbeth Cuevas', 1, '2025-08-07 15:28:49', '2025-08-06 21:05:25'),
(20, 'marlenef', '123456', 'cobranza@rinorisk.com', 'Marlene Flores', 1, NULL, '2025-08-06 21:06:16'),
(21, 'Beatrizi', '123456', 'servicio.vgmtj@rinorisk.com', 'Beatriz Ita', 1, NULL, '2025-08-06 21:07:46'),
(22, 'sofiag', '123456', 'Sofia@rinorisk.com', 'Sofia Gallardo', 1, '2025-08-07 15:29:09', '2025-08-06 21:09:03'),
(23, 'taniab', '123456', 'auxiliar.inmuebles@rinorisk.com', 'Tania Barboza', 1, NULL, '2025-08-06 21:09:43'),
(24, 'angelicav', '123456', 'gerente.administrativo@rinorisk.com', 'Angelica Vallejo', 1, NULL, '2025-08-06 21:10:52'),
(25, 'yazmint', '123456', 'contabilidad@rinorisk.com', 'Yazmin Trejo', 1, NULL, '2025-08-06 21:11:37'),
(26, 'mitchellq', '123456', 'auxiliar.contable@rinorisk.com', 'Mitchellq', 1, NULL, '2025-08-06 21:12:36'),
(27, 'evelynd', '123456', 'analista.financiero@rinorisk.com', 'Evelyn Duran', 1, NULL, '2025-08-06 21:13:10'),
(28, 'thanias', '123456', 'legal@rinorisk.com', 'Thania Sanchez', 1, NULL, '2025-08-06 21:14:19'),
(29, 'monsed', '123456', 'asistente.legal@rinorisk.com', 'Monserrat Diaz', 1, NULL, '2025-08-06 21:22:48'),
(30, 'Ivanm', '123456', 'procesos.operativos@rinorisk.com', 'Ivan Mozqueda', 1, '2025-08-07 15:33:14', '2025-08-06 21:23:48'),
(31, 'marisoll', '123456', 'sincorreo2@rinorisk.com', 'Marisol Lopez', 1, NULL, '2025-08-06 21:24:54'),
(32, 'joseo', '123456', 'sincorreo3@rinorisk.com', 'Jose Ovando', 1, NULL, '2025-08-06 21:25:29'),
(33, 'isidorob', '123456', 'sincorreo4@rinorisk.com', 'Isidoro Bravo', 1, NULL, '2025-08-06 21:26:07'),
(34, 'vannesat', '123456', 'sincorreo5@rinorisk.com', 'Vanessa Torres', 1, NULL, '2025-08-06 21:26:40'),
(35, 'hildan', '123456', 'sincorreo6@rinorisk.com', 'Hilda Nuñez', 1, NULL, '2025-08-06 21:27:48'),
(36, 'porfiriog', '123456', 'sincorreo7@rinorisk.com', 'Porfirio Gonzales', 1, NULL, '2025-08-06 21:28:22'),
(37, 'ronym', '123456', 'sincorreo8@rinorisk.com', 'Rony Marquez', 1, NULL, '2025-08-06 21:29:47'),
(38, 'juanl', '123456', 'sistemas@rinorisk.com', 'Juan Lopez', 1, NULL, '2025-08-06 21:30:21'),
(39, 'yesseniav', '123456', 'capacitacion@rinorisk.com', 'Yessenia', 1, NULL, '2025-08-06 21:46:27'),
(40, 'karenv', '123456', 'sincorreo9@rinorisk.com', 'Karen Vazques', 1, NULL, '2025-08-06 21:47:02'),
(41, 'angelr', '123456', 'sincorreo10@rinorisk.com', 'Angel Ramos', 1, NULL, '2025-08-06 21:47:43'),
(42, 'fernandam', '123456', 'sincorreo11@rinorisk.com', 'Fernanda Moreno', 1, NULL, '2025-08-06 21:48:14'),
(43, 'dulces', '123456', 'sincorreo12@rinorisk.com', 'Dulce de santiago', 1, NULL, '2025-08-06 21:48:55'),
(44, 'Keyla', '123456', 'sincorreo13@rinorisk.com', 'Keyla', 1, NULL, '2025-08-06 21:49:46'),
(45, 'samanthac', '123456', 'sincorrreo14@rinorisk.com', 'Samantha Carrizales', 1, NULL, '2025-08-06 21:50:22'),
(46, 'lisbethv', '123456', 'sincorreo15@rinorisk.com', 'Lisbeth Vega', 1, NULL, '2025-08-06 21:50:57'),
(47, 'alejandrar', '123456', 'sincorreo16@rinorisk.com', 'Alejandra Ramos', 1, NULL, '2025-08-06 21:51:48'),
(48, 'myriamt', '123456', 'sincorreo17@rinorisk.com', 'Myriam Torres', 1, NULL, '2025-08-06 21:52:21'),
(49, 'dianag', '123456', 'sincorreo18@rinorisk.com', 'Diana Gonzales', 1, NULL, '2025-08-06 21:52:51'),
(50, 'karenf', '123456', 'sincorreo19@rinorisk.com', 'Karen Fletes', 1, '2025-08-07 15:29:47', '2025-08-06 21:54:25'),
(51, 'lider_direccion', '$2y$10$6EeY1mJ2kZQB4wQwHsv/9O8XVGAHdSXF5Vby41xb4yoZWvBmsc0Ye', 'lider.direccion@rinorisk.com', 'Líder Dirección', 1, NULL, '2025-08-06 22:19:51'),
(52, 'lider_gaia', '$2y$10$IckMuWz0zex8ycuGgVEAO./OWwjcS58zkWm4hAPgYR/ZUjxvZEtge', 'lider.gaia@rinorisk.com', 'Líder Gaia', 1, NULL, '2025-08-06 22:19:51'),
(53, 'lider_operaciones', '$2y$10$GX/NOwNxP85LzDlKFftl6eeNFlLnIdh5m962zNqf95TanV.sL3i3q', 'lider.operaciones@rinorisk.com', 'Líder Operaciones', 1, NULL, '2025-08-06 22:19:51'),
(54, 'lider_servicio', '$2y$10$X75nWRTaNskjIC7A5LM01uI37fCN2EYU7wthiPvUAk8p1leLkBo3S', 'lider.servicio@rinorisk.com', 'Líder Servicio', 1, NULL, '2025-08-06 22:19:52'),
(55, 'rosaliae', '123456', 'sincorreo20@rinorisk.com', 'Rosalia Enriquez', 1, NULL, '2025-08-06 23:21:11'),
(56, 'sonial', '123456', 'sincorreo21@rinorisk.com', 'Sonia Lopez', 1, NULL, '2025-08-06 23:24:15'),
(57, 'ivan2', '123456', 'sincorreo23@rinorsik.com', 'Ivan Mosqueda', 1, '2025-08-07 15:31:57', '2025-08-06 23:25:11'),
(58, 'ivan3', '123456', 'sincorreo24@rinorisk.com', 'Ivan Mozqueda', 1, '2025-08-07 15:32:18', '2025-08-06 23:29:06');

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
(30, 1),
(2, 3),
(13, 3),
(14, 3),
(19, 3),
(22, 3),
(50, 3),
(51, 3),
(52, 3),
(53, 3),
(54, 3),
(57, 3),
(58, 3),
(3, 4),
(4, 4),
(5, 4),
(6, 4),
(9, 4),
(10, 4),
(11, 4),
(12, 4),
(15, 4),
(16, 4),
(17, 4),
(18, 4),
(20, 4),
(21, 4),
(23, 4),
(24, 4),
(25, 4),
(26, 4),
(27, 4),
(28, 4),
(29, 4),
(31, 4),
(32, 4),
(33, 4),
(34, 4),
(35, 4),
(36, 4),
(37, 4),
(38, 4),
(39, 4),
(40, 4),
(41, 4),
(42, 4),
(43, 4),
(44, 4),
(45, 4),
(46, 4),
(47, 4),
(48, 4),
(49, 4),
(55, 4),
(56, 4);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_overdue_tasks`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_overdue_tasks` (
`task_id` int
,`parent_task_id` int
,`task_name` varchar(255)
,`description` text
,`project_id` int
,`assigned_to_user_id` int
,`created_by_user_id` int
,`priority` enum('low','medium','high','critical')
,`due_date` date
,`estimated_hours` decimal(5,2)
,`actual_hours` decimal(5,2)
,`completion_percentage` decimal(5,2)
,`automatic_points` decimal(10,2)
,`assigned_percentage` decimal(5,2)
,`color_tag` varchar(7)
,`is_subtask` tinyint(1)
,`subtask_order` int
,`status` enum('pending','in_progress','completed','cancelled')
,`is_completed` tinyint(1)
,`completed_at` timestamp
,`created_at` timestamp
,`updated_at` timestamp
,`project_name` varchar(255)
,`assigned_user_name` varchar(100)
,`clan_name` varchar(100)
,`days_overdue` int
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_subtasks_complete`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_subtasks_complete` (
`subtask_id` int
,`task_id` int
,`parent_task_name` varchar(255)
,`title` varchar(255)
,`description` text
,`completion_percentage` decimal(5,2)
,`estimated_hours` decimal(5,2)
,`actual_hours` decimal(5,2)
,`status` enum('pending','in_progress','completed','cancelled')
,`priority` enum('low','medium','high','urgent')
,`due_date` date
,`assigned_to_user_id` int
,`assigned_user_name` varchar(100)
,`assigned_username` varchar(100)
,`created_by_user_id` int
,`created_by_name` varchar(100)
,`subtask_order` int
,`created_at` timestamp
,`updated_at` timestamp
,`project_id` int
,`project_name` varchar(255)
,`clan_id` int
,`clan_name` varchar(100)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_tasks_complete`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_tasks_complete` (
`task_id` int
,`task_name` varchar(255)
,`description` text
,`project_id` int
,`project_name` varchar(255)
,`parent_task_id` int
,`parent_task_name` varchar(255)
,`assigned_to_user_id` int
,`assigned_user_name` varchar(100)
,`assigned_username` varchar(100)
,`status` enum('pending','in_progress','completed','cancelled')
,`priority` enum('low','medium','high','critical')
,`due_date` date
,`estimated_hours` decimal(5,2)
,`actual_hours` decimal(5,2)
,`completion_percentage` decimal(5,2)
,`created_by_user_id` int
,`created_by_name` varchar(100)
,`assigned_percentage` decimal(5,2)
,`color_tag` varchar(7)
,`is_subtask` tinyint(1)
,`subtask_order` int
,`created_at` timestamp
,`updated_at` timestamp
,`clan_id` int
,`clan_name` varchar(100)
,`subtasks_count` bigint
,`subtasks_completed` bigint
,`comments_count` bigint
,`attachments_count` bigint
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_overdue_tasks`
--
DROP TABLE IF EXISTS `v_overdue_tasks`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_overdue_tasks`  AS SELECT `t`.`task_id` AS `task_id`, `t`.`parent_task_id` AS `parent_task_id`, `t`.`task_name` AS `task_name`, `t`.`description` AS `description`, `t`.`project_id` AS `project_id`, `t`.`assigned_to_user_id` AS `assigned_to_user_id`, `t`.`created_by_user_id` AS `created_by_user_id`, `t`.`priority` AS `priority`, `t`.`due_date` AS `due_date`, `t`.`estimated_hours` AS `estimated_hours`, `t`.`actual_hours` AS `actual_hours`, `t`.`completion_percentage` AS `completion_percentage`, `t`.`automatic_points` AS `automatic_points`, `t`.`assigned_percentage` AS `assigned_percentage`, `t`.`color_tag` AS `color_tag`, `t`.`is_subtask` AS `is_subtask`, `t`.`subtask_order` AS `subtask_order`, `t`.`status` AS `status`, `t`.`is_completed` AS `is_completed`, `t`.`completed_at` AS `completed_at`, `t`.`created_at` AS `created_at`, `t`.`updated_at` AS `updated_at`, `p`.`project_name` AS `project_name`, `u`.`full_name` AS `assigned_user_name`, `c`.`clan_name` AS `clan_name`, (to_days(curdate()) - to_days(`t`.`due_date`)) AS `days_overdue` FROM (((`tasks` `t` left join `projects` `p` on((`t`.`project_id` = `p`.`project_id`))) left join `users` `u` on((`t`.`assigned_to_user_id` = `u`.`user_id`))) left join `clans` `c` on((`p`.`clan_id` = `c`.`clan_id`))) WHERE ((`t`.`due_date` < curdate()) AND (`t`.`status` not in ('completed','cancelled')) AND (`t`.`is_subtask` = 0)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_subtasks_complete`
--
DROP TABLE IF EXISTS `v_subtasks_complete`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_subtasks_complete`  AS SELECT `s`.`subtask_id` AS `subtask_id`, `s`.`task_id` AS `task_id`, `t`.`task_name` AS `parent_task_name`, `s`.`title` AS `title`, `s`.`description` AS `description`, `s`.`completion_percentage` AS `completion_percentage`, `s`.`estimated_hours` AS `estimated_hours`, `s`.`actual_hours` AS `actual_hours`, `s`.`status` AS `status`, `s`.`priority` AS `priority`, `s`.`due_date` AS `due_date`, `s`.`assigned_to_user_id` AS `assigned_to_user_id`, `u`.`full_name` AS `assigned_user_name`, `u`.`username` AS `assigned_username`, `s`.`created_by_user_id` AS `created_by_user_id`, `cu`.`full_name` AS `created_by_name`, `s`.`subtask_order` AS `subtask_order`, `s`.`created_at` AS `created_at`, `s`.`updated_at` AS `updated_at`, `p`.`project_id` AS `project_id`, `p`.`project_name` AS `project_name`, `c`.`clan_id` AS `clan_id`, `c`.`clan_name` AS `clan_name` FROM (((((`subtasks` `s` join `tasks` `t` on((`s`.`task_id` = `t`.`task_id`))) left join `users` `u` on((`s`.`assigned_to_user_id` = `u`.`user_id`))) left join `users` `cu` on((`s`.`created_by_user_id` = `cu`.`user_id`))) left join `projects` `p` on((`t`.`project_id` = `p`.`project_id`))) left join `clans` `c` on((`p`.`clan_id` = `c`.`clan_id`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_tasks_complete`
--
DROP TABLE IF EXISTS `v_tasks_complete`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_tasks_complete`  AS SELECT `t`.`task_id` AS `task_id`, `t`.`task_name` AS `task_name`, `t`.`description` AS `description`, `t`.`project_id` AS `project_id`, `p`.`project_name` AS `project_name`, `t`.`parent_task_id` AS `parent_task_id`, `pt`.`task_name` AS `parent_task_name`, `t`.`assigned_to_user_id` AS `assigned_to_user_id`, `u`.`full_name` AS `assigned_user_name`, `u`.`username` AS `assigned_username`, `t`.`status` AS `status`, `t`.`priority` AS `priority`, `t`.`due_date` AS `due_date`, `t`.`estimated_hours` AS `estimated_hours`, `t`.`actual_hours` AS `actual_hours`, `t`.`completion_percentage` AS `completion_percentage`, `t`.`created_by_user_id` AS `created_by_user_id`, `cu`.`full_name` AS `created_by_name`, `t`.`assigned_percentage` AS `assigned_percentage`, `t`.`color_tag` AS `color_tag`, `t`.`is_subtask` AS `is_subtask`, `t`.`subtask_order` AS `subtask_order`, `t`.`created_at` AS `created_at`, `t`.`updated_at` AS `updated_at`, `c`.`clan_id` AS `clan_id`, `c`.`clan_name` AS `clan_name`, (select count(0) from `subtasks` `s` where (`s`.`task_id` = `t`.`task_id`)) AS `subtasks_count`, (select count(0) from `subtasks` `s` where ((`s`.`task_id` = `t`.`task_id`) and (`s`.`status` = 'completed'))) AS `subtasks_completed`, (select count(0) from `task_comments` `tc` where (`tc`.`task_id` = `t`.`task_id`)) AS `comments_count`, (select count(0) from `task_attachments` `ta` where (`ta`.`task_id` = `t`.`task_id`)) AS `attachments_count` FROM (((((`tasks` `t` left join `projects` `p` on((`t`.`project_id` = `p`.`project_id`))) left join `tasks` `pt` on((`t`.`parent_task_id` = `pt`.`task_id`))) left join `users` `u` on((`t`.`assigned_to_user_id` = `u`.`user_id`))) left join `users` `cu` on((`t`.`created_by_user_id` = `cu`.`user_id`))) left join `clans` `c` on((`p`.`clan_id` = `c`.`clan_id`))) ;

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
  ADD KEY `idx_clan_kpis_year_quarter` (`year`,`quarter`),
  ADD KEY `idx_kpi_quarter_id` (`kpi_quarter_id`);

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
  ADD KEY `idx_projects_kpi_quarter` (`kpi_quarter_id`),
  ADD KEY `idx_kpi_quarter` (`kpi_quarter_id`);

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
-- Indices de la tabla `Subtasks`
--
ALTER TABLE `Subtasks`
  ADD PRIMARY KEY (`subtask_id`),
  ADD KEY `idx_task_id` (`task_id`),
  ADD KEY `idx_assigned_user` (`assigned_to_user_id`),
  ADD KEY `idx_created_by` (`created_by_user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_due_date` (`due_date`);

--
-- Indices de la tabla `Tasks`
--
ALTER TABLE `Tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `idx_tasks_project` (`project_id`),
  ADD KEY `idx_tasks_assigned_user` (`assigned_to_user_id`),
  ADD KEY `idx_tasks_status` (`status`),
  ADD KEY `idx_tasks_priority` (`priority`),
  ADD KEY `idx_tasks_due_date` (`due_date`),
  ADD KEY `idx_parent_task` (`parent_task_id`),
  ADD KEY `idx_status_priority` (`status`,`priority`),
  ADD KEY `idx_project_status` (`project_id`,`status`),
  ADD KEY `idx_created_by` (`created_by_user_id`);

--
-- Indices de la tabla `Task_Assignments`
--
ALTER TABLE `Task_Assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD UNIQUE KEY `unique_task_user` (`task_id`,`user_id`),
  ADD KEY `idx_task_id` (`task_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_assigned_by` (`assigned_by_user_id`);

--
-- Indices de la tabla `Task_Attachments`
--
ALTER TABLE `Task_Attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `idx_task_id` (`task_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_uploaded_at` (`uploaded_at`);

--
-- Indices de la tabla `Task_Comments`
--
ALTER TABLE `Task_Comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `idx_task_id` (`task_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_comment_type` (`comment_type`),
  ADD KEY `fk_task_comments_related_user` (`related_user_id`);

--
-- Indices de la tabla `Task_History`
--
ALTER TABLE `Task_History`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `idx_task_id` (`task_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action_type` (`action_type`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `fk_task_history_related_user` (`related_user_id`);

--
-- Indices de la tabla `Task_Labels`
--
ALTER TABLE `Task_Labels`
  ADD PRIMARY KEY (`label_id`),
  ADD UNIQUE KEY `unique_label_clan` (`label_name`,`clan_id`),
  ADD KEY `idx_clan_id` (`clan_id`),
  ADD KEY `idx_created_by` (`created_by_user_id`);

--
-- Indices de la tabla `Task_Label_Assignments`
--
ALTER TABLE `Task_Label_Assignments`
  ADD PRIMARY KEY (`task_id`,`label_id`),
  ADD KEY `idx_label_id` (`label_id`),
  ADD KEY `idx_assigned_by` (`assigned_by_user_id`);

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
  MODIFY `clan_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `Clan_KPIs`
--
ALTER TABLE `Clan_KPIs`
  MODIFY `kpi_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Clan_Members`
--
ALTER TABLE `Clan_Members`
  MODIFY `clan_member_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT de la tabla `KPI_History`
--
ALTER TABLE `KPI_History`
  MODIFY `history_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `KPI_Quarters`
--
ALTER TABLE `KPI_Quarters`
  MODIFY `kpi_quarter_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT de la tabla `Projects`
--
ALTER TABLE `Projects`
  MODIFY `project_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `Roles`
--
ALTER TABLE `Roles`
  MODIFY `role_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `Subtasks`
--
ALTER TABLE `Subtasks`
  MODIFY `subtask_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `Tasks`
--
ALTER TABLE `Tasks`
  MODIFY `task_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `Task_Assignments`
--
ALTER TABLE `Task_Assignments`
  MODIFY `assignment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT de la tabla `Task_Attachments`
--
ALTER TABLE `Task_Attachments`
  MODIFY `attachment_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Task_Comments`
--
ALTER TABLE `Task_Comments`
  MODIFY `comment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `Task_History`
--
ALTER TABLE `Task_History`
  MODIFY `history_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT de la tabla `Task_Labels`
--
ALTER TABLE `Task_Labels`
  MODIFY `label_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Clan_KPIs`
--
ALTER TABLE `Clan_KPIs`
  ADD CONSTRAINT `clan_kpis_ibfk_1` FOREIGN KEY (`clan_id`) REFERENCES `Clans` (`clan_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `clan_kpis_quarter_fk` FOREIGN KEY (`kpi_quarter_id`) REFERENCES `KPI_Quarters` (`kpi_quarter_id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`created_by_user_id`) REFERENCES `Users` (`user_id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `projects_kpi_quarter_fk` FOREIGN KEY (`kpi_quarter_id`) REFERENCES `KPI_Quarters` (`kpi_quarter_id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `Project_Participants`
--
ALTER TABLE `Project_Participants`
  ADD CONSTRAINT `project_participants_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Subtasks`
--
ALTER TABLE `Subtasks`
  ADD CONSTRAINT `fk_subtasks_assigned_user` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `Users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_subtasks_created_by` FOREIGN KEY (`created_by_user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_subtasks_task` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`task_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Tasks`
--
ALTER TABLE `Tasks`
  ADD CONSTRAINT `fk_tasks_parent` FOREIGN KEY (`parent_task_id`) REFERENCES `Tasks` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_created_by_fk` FOREIGN KEY (`created_by_user_id`) REFERENCES `Users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `Projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `Users` (`user_id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `Task_Assignments`
--
ALTER TABLE `Task_Assignments`
  ADD CONSTRAINT `fk_task_assignments_assigned_by` FOREIGN KEY (`assigned_by_user_id`) REFERENCES `Users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_task_assignments_task` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_task_assignments_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Task_Attachments`
--
ALTER TABLE `Task_Attachments`
  ADD CONSTRAINT `fk_task_attachments_task` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_task_attachments_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Task_Comments`
--
ALTER TABLE `Task_Comments`
  ADD CONSTRAINT `fk_task_comments_related_user` FOREIGN KEY (`related_user_id`) REFERENCES `Users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_task_comments_task` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_task_comments_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Task_History`
--
ALTER TABLE `Task_History`
  ADD CONSTRAINT `fk_task_history_related_user` FOREIGN KEY (`related_user_id`) REFERENCES `Users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_task_history_task` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_task_history_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Task_Labels`
--
ALTER TABLE `Task_Labels`
  ADD CONSTRAINT `fk_task_labels_clan` FOREIGN KEY (`clan_id`) REFERENCES `Clans` (`clan_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_task_labels_created_by` FOREIGN KEY (`created_by_user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Task_Label_Assignments`
--
ALTER TABLE `Task_Label_Assignments`
  ADD CONSTRAINT `fk_task_label_assignments_assigned_by` FOREIGN KEY (`assigned_by_user_id`) REFERENCES `Users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_task_label_assignments_label` FOREIGN KEY (`label_id`) REFERENCES `Task_Labels` (`label_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_task_label_assignments_task` FOREIGN KEY (`task_id`) REFERENCES `Tasks` (`task_id`) ON DELETE CASCADE;

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
