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
  `clan_id` int(11) NOT NULL,
  `clan_name` varchar(100) NOT NULL,
  `clan_departamento` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
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
  `kpi_id` int(11) NOT NULL,
  `clan_id` int(11) NOT NULL,
  `kpi_quarter_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `quarter` int(11) NOT NULL,
  `total_points` int(11) DEFAULT 1000,
  `assigned_points` int(11) DEFAULT 0,
  `status` enum('planning','active','completed','closed') DEFAULT 'planning',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Clan_Members`
--

CREATE TABLE `Clan_Members` (
  `clan_member_id` int(11) NOT NULL,
  `clan_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Clan_Members`
--

INSERT INTO `Clan_Members` (`clan_member_id`, `clan_id`, `user_id`) VALUES
(89, 5, 1),
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
(50, 12, 49),
(86, 13, 16),
(88, 13, 58);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `KPI_History`
--

CREATE TABLE `KPI_History` (
  `history_id` int(11) NOT NULL,
  `kpi_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `task_id` int(11) DEFAULT NULL,
  `action_type` enum('project_assigned','project_updated','task_completed','task_updated','points_redistributed') NOT NULL,
  `old_value` decimal(8,2) DEFAULT NULL,
  `new_value` decimal(8,2) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `KPI_Quarters`
--

CREATE TABLE `KPI_Quarters` (
  `kpi_quarter_id` int(11) NOT NULL,
  `quarter` enum('Q1','Q2','Q3','Q4') NOT NULL,
  `year` int(11) NOT NULL,
  `total_points` int(11) NOT NULL DEFAULT 1000,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(50) DEFAULT 'planning',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `KPI_Quarters`
--

INSERT INTO `KPI_Quarters` (`kpi_quarter_id`, `quarter`, `year`, `total_points`, `is_active`, `status`, `created_at`) VALUES
(11, 'Q3', 2025, 8000, 1, 'active', '2025-08-05 15:55:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `success` tinyint(1) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` timestamp NULL DEFAULT current_timestamp()
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
  `project_id` int(11) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `clan_id` int(11) NOT NULL,
  `created_by_user_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'open',
  `total_tasks` int(11) DEFAULT 0,
  `completed_tasks` int(11) DEFAULT 0,
  `progress_percentage` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `kpi_quarter_id` int(11) DEFAULT NULL,
  `kpi_points` int(11) DEFAULT 0,
  `task_distribution_mode` enum('automatic','percentage') DEFAULT 'automatic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Projects`
--

INSERT INTO `Projects` (`project_id`, `project_name`, `description`, `clan_id`, `created_by_user_id`, `status`, `total_tasks`, `completed_tasks`, `progress_percentage`, `created_at`, `updated_at`, `kpi_quarter_id`, `kpi_points`, `task_distribution_mode`) VALUES
(14, 'Consulta de promotoria Fase 1', 'Primera fase operativa de relaciones de colaboradores', 5, 2, 'open', 18, 7, 50.00, '2025-08-05 21:01:49', '2025-08-07 22:34:56', 11, 400, 'automatic'),
(15, 'Nuevos indicadores / Procesos automaticos', 'Investigación y desarrollo de robots para obtener nuevas fuentes de información', 5, 2, 'open', 2, 0, 0.00, '2025-08-05 22:08:11', '2025-08-07 20:16:05', 11, 350, 'automatic'),
(16, 'Consulta de promotoria fase /2', 'Continuación de Fase 1-', 5, 2, 'open', 0, 0, 0.00, '2025-08-05 22:08:59', '2025-08-07 18:06:52', 11, 250, 'automatic'),
(19, 'Recluta de Agentes', 'Recluta de Agentes', 7, 14, 'open', 0, 0, 0.00, '2025-08-07 18:40:18', '2025-08-07 20:13:35', 11, 500, 'automatic'),
(20, 'Proyecto Inicial', 'Proyecto Inicial', 10, 50, 'open', 0, 0, 0.00, '2025-08-07 20:20:23', '2025-08-07 20:20:23', NULL, 0, 'automatic'),
(21, 'Proyecto Incial', 'Proyecto Incial', 8, 19, 'open', 0, 0, 0.00, '2025-08-07 20:20:51', '2025-08-07 20:20:51', NULL, 0, 'automatic'),
(22, 'Proyecto Inicial', 'Proyecto inicial', 12, 22, 'open', 0, 0, 0.00, '2025-08-07 20:21:31', '2025-08-07 22:26:24', 11, 100, 'automatic'),
(23, 'Proyecto Inicial', 'Proyecto Inicial', 6, 13, 'open', 0, 0, 0.00, '2025-08-07 20:22:19', '2025-08-07 22:23:37', 11, 200, 'automatic'),
(24, 'Proyecto Inicial', 'Proyecto Inicial', 11, 57, 'open', 0, 0, 0.00, '2025-08-07 20:22:48', '2025-08-07 20:22:48', NULL, 0, 'automatic'),
(25, 'prueba', 'prueba', 5, 2, 'open', 1, 0, 0.00, '2025-08-07 20:39:57', '2025-08-07 20:41:57', NULL, 0, 'automatic'),
(26, 'capacitacion', 'capacitacion', 5, 2, 'open', 0, 0, 0.00, '2025-08-07 22:29:34', '2025-08-07 22:29:34', NULL, 0, 'automatic');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Project_Participants`
--

CREATE TABLE `Project_Participants` (
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Roles`
--

CREATE TABLE `Roles` (
  `role_id` int(11) NOT NULL,
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
  `subtask_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `completion_percentage` decimal(5,2) DEFAULT 0.00,
  `estimated_hours` decimal(5,2) DEFAULT NULL,
  `actual_hours` decimal(5,2) DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `due_date` date DEFAULT NULL,
  `assigned_to_user_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) NOT NULL,
  `subtask_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
  `task_id` int(11) NOT NULL,
  `parent_task_id` int(11) DEFAULT NULL,
  `task_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `project_id` int(11) NOT NULL,
  `assigned_to_user_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `due_date` date DEFAULT NULL,
  `estimated_hours` decimal(5,2) DEFAULT NULL,
  `actual_hours` decimal(5,2) DEFAULT NULL,
  `completion_percentage` decimal(5,2) DEFAULT 0.00,
  `automatic_points` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Puntos KPI calculados para modo automático',
  `assigned_percentage` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Porcentaje de puntos KPI asignado manualmente',
  `color_tag` varchar(7) DEFAULT '#3B82F6',
  `is_subtask` tinyint(1) DEFAULT 0,
  `subtask_order` int(11) DEFAULT 0,
  `status` enum('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Tasks`
--

INSERT INTO `Tasks` (`task_id`, `parent_task_id`, `task_name`, `description`, `project_id`, `assigned_to_user_id`, `created_by_user_id`, `priority`, `due_date`, `estimated_hours`, `actual_hours`, `completion_percentage`, `automatic_points`, `assigned_percentage`, `color_tag`, `is_subtask`, `subtask_order`, `status`, `is_completed`, `completed_at`, `created_at`, `updated_at`) VALUES
(28, NULL, 'QA', 'Realizar pruebas de datos, estructura información, y schemas.', 14, 2, 2, 'medium', '2025-08-19', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-07 18:04:59', '2025-08-05 21:03:15', '2025-08-07 18:04:59'),
(29, NULL, 'Nuevos accesos en interfaz para nueva logica de usuarios', 'Gestion de interfaz para manejar visualmente los accesos.', 14, 9, 2, 'medium', '2025-08-08', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-05 22:33:37', '2025-08-05 21:08:13', '2025-08-05 22:33:37'),
(30, NULL, 'Filtros por ROL', 'Nueva logica de filtro por rol', 14, 9, 2, 'medium', '2025-08-11', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'in_progress', 0, NULL, '2025-08-05 21:08:58', '2025-08-07 18:05:55'),
(31, NULL, 'Selector de Usuario', 'Logica que de termina visualmente la forma en que los usuarios y claves se asignan bajo ciertas circunstancias.', 14, 5, 2, 'medium', '2025-08-15', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 21:10:31', '2025-08-05 22:29:24'),
(32, NULL, 'Ajustes de integración', 'Ajustes de integración', 14, 6, 2, 'medium', '2025-08-15', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'in_progress', 0, NULL, '2025-08-05 21:16:51', '2025-08-07 18:05:27'),
(33, NULL, 'Merge a la rama FEAT consulta nuevos resultados 2', 'Merge a la rama FEAT consulta nuevos resultados 2', 14, 6, 2, 'medium', '2025-08-07', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-05 22:33:32', '2025-08-05 21:18:46', '2025-08-05 22:33:32'),
(34, NULL, 'Implementar filtros de la rama (Nuevos filtros)', 'Implementar filtros de la rama (Nuevos filtros)', 14, 6, 2, 'medium', '2025-08-15', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-07 18:05:15', '2025-08-05 21:19:48', '2025-08-07 18:05:15'),
(35, NULL, 'Gestión de agentes (Usuarios de gran volumen)', 'Gestión de agentes (Usuarios de gran volumen)', 14, 9, 2, 'medium', '2025-08-15', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 21:21:08', '2025-08-05 22:29:24'),
(36, NULL, 'Separación resultados Eject. Agente.', 'Separación resultados Eject. Agente.', 14, 6, 2, 'medium', '2025-08-13', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 21:23:45', '2025-08-05 22:29:24'),
(37, NULL, 'Resegmentación de Robots', 'Resegmentación de Robots', 14, 11, 2, 'medium', '2025-08-12', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 21:24:59', '2025-08-05 22:29:24'),
(38, NULL, 'Filtros por mes API', 'Filtros por mes API', 14, 12, 2, 'medium', '2025-08-08', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'in_progress', 0, NULL, '2025-08-05 21:25:30', '2025-08-07 18:05:44'),
(39, NULL, 'Relación usuarios Backend', 'Relación usuarios Backend', 14, 12, 2, 'medium', '2025-08-14', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-07 17:58:32', '2025-08-05 21:26:23', '2025-08-07 17:58:32'),
(40, NULL, 'Administracion Usuarios', 'Administracion Usuarios', 14, 4, 2, 'medium', '2025-08-15', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-05 22:33:52', '2025-08-05 21:27:36', '2025-08-05 22:33:52'),
(41, NULL, 'Figmas de propuesta', '', 14, 5, 2, 'medium', '2025-08-05', NULL, NULL, 0.00, 28.57, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-07 22:34:56', '2025-08-05 22:03:53', '2025-08-07 22:34:56'),
(42, NULL, 'Investigación Indicadores estrella', 'Investigación Indicadores estrella', 15, 11, 2, 'medium', '2025-09-05', NULL, NULL, 0.00, 175.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 22:20:26', '2025-08-05 22:29:38'),
(43, NULL, 'Elaboración de PHU', 'Crear PHU para su aprobación', 15, 2, 2, 'medium', '2025-08-15', NULL, NULL, 0.00, 175.00, 0.00, '#3B82F6', 0, 0, 'in_progress', 0, NULL, '2025-08-05 22:21:59', '2025-08-07 20:16:05'),
(44, NULL, 'Creación de PHU', 'Creación de PHU para su aprobación', 16, 2, 2, 'medium', '2025-08-29', NULL, NULL, 0.00, 250.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 22:22:38', '2025-08-05 22:30:14'),
(46, NULL, 'Tarea de Prueba', 'Descripción de la tarea de prueba', 14, NULL, 2, 'medium', '2025-12-31', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-07 17:57:02', '2025-08-07 17:57:02'),
(47, NULL, 'Tarea de Prueba Simple', 'Descripción de la tarea de prueba simple', 14, NULL, 2, 'medium', '2025-12-31', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-07 17:58:01', '2025-08-07 17:58:01'),
(48, NULL, 'Distribución de tareas', 'Crear el backlog de tareas para todos los implicados en el proyecto', 16, NULL, 2, 'medium', '2025-08-29', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-07 18:07:44', '2025-08-07 18:07:44'),
(49, NULL, 'Mi tarea de prueba', 'asfsfw', 25, NULL, 2, 'medium', '2025-08-29', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'in_progress', 0, NULL, '2025-08-07 20:40:50', '2025-08-07 20:41:57'),
(50, NULL, 'pruebas de usuario', '....', 14, NULL, 2, 'medium', '2025-08-15', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-07 22:30:59', '2025-08-07 22:30:59'),
(51, NULL, 'control', 'dsfdasfsadf', 14, NULL, 2, 'medium', '2025-08-15', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-07 22:32:59', '2025-08-07 22:32:59');

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
  `assignment_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assigned_percentage` decimal(5,2) DEFAULT 0.00,
  `assigned_at` timestamp NULL DEFAULT current_timestamp(),
  `assigned_by_user_id` int(11) DEFAULT NULL,
  `status` enum('assigned','accepted','declined','completed') DEFAULT 'assigned',
  `notes` text DEFAULT NULL
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
  `attachment_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Task_Attachments`
--

INSERT INTO `Task_Attachments` (`attachment_id`, `task_id`, `user_id`, `comment_id`, `file_name`, `file_path`, `file_size`, `file_type`, `description`, `uploaded_at`) VALUES
(1, 51, 2, 9, 'Captura de pantalla 2025-08-08 a las 9.34.46 a. m..png', 'uploads/task_attachments/att_689a6a91e66d8.png', NULL, 'image/png', NULL, '2025-08-11 22:11:29'),
(2, 51, 2, 10, 'ACTIVIDADES PRINCIPALES DE UN COORDINADOR DE SISTEMAS.docx', 'uploads/task_attachments/att_689a6b6de1fdc.docx', NULL, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', NULL, '2025-08-11 22:15:09'),
(3, 51, 2, 11, '10. CVTomasMiguelRodriguezMoreno.pdf', 'uploads/task_attachments/att_689a6b891d5aa.pdf', NULL, 'application/pdf', NULL, '2025-08-11 22:15:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Task_Comments`
--

CREATE TABLE `Task_Comments` (
  `comment_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `comment_type` enum('comment','status_change','assignment','completion','system') DEFAULT 'comment',
  `related_user_id` int(11) DEFAULT NULL,
  `old_value` varchar(255) DEFAULT NULL,
  `new_value` varchar(255) DEFAULT NULL,
  `is_private` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Task_Comments`
--

INSERT INTO `Task_Comments` (`comment_id`, `task_id`, `user_id`, `comment_text`, `comment_type`, `related_user_id`, `old_value`, `new_value`, `is_private`, `created_at`) VALUES
(1, 51, 2, 'fsadfasd', 'comment', NULL, NULL, NULL, 0, '2025-08-11 21:18:24'),
(2, 51, 2, 'Esto es nuevo comentario 2', 'comment', NULL, NULL, NULL, 0, '2025-08-11 21:18:41'),
(3, 51, 2, 'Hola esto es un archivo', 'comment', NULL, NULL, NULL, 0, '2025-08-11 21:28:22'),
(4, 51, 2, 'Hola esto es un archivo', 'comment', NULL, NULL, NULL, 0, '2025-08-11 21:54:08'),
(5, 51, 2, 'Comentario con adjuncion de archivo', 'comment', NULL, NULL, NULL, 0, '2025-08-11 22:00:29'),
(6, 51, 2, 'archivo', 'comment', NULL, NULL, NULL, 0, '2025-08-11 22:02:41'),
(7, 51, 2, 'Docs', 'comment', NULL, NULL, NULL, 0, '2025-08-11 22:02:58'),
(8, 51, 2, 'documentos', 'comment', NULL, NULL, NULL, 0, '2025-08-11 22:05:57'),
(9, 51, 2, 'documentacion', 'comment', NULL, NULL, NULL, 0, '2025-08-11 22:11:29'),
(10, 51, 2, 'docx', 'comment', NULL, NULL, NULL, 0, '2025-08-11 22:15:09'),
(11, 51, 2, 'pdfs', 'comment', NULL, NULL, NULL, 0, '2025-08-11 22:15:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Task_History`
--

CREATE TABLE `Task_History` (
  `history_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action_type` enum('created','updated','status_changed','assigned','unassigned','commented','completed','reopened','deleted') NOT NULL,
  `field_name` varchar(50) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `related_user_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Task_History`
--

INSERT INTO `Task_History` (`history_id`, `task_id`, `user_id`, `action_type`, `field_name`, `old_value`, `new_value`, `related_user_id`, `notes`, `created_at`) VALUES
(1, 39, 12, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-07 17:58:32'),
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
(116, 40, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-05 22:33:52'),
(117, 28, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-07 18:04:59'),
(118, 34, 6, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-07 18:05:15'),
(119, 32, 6, 'status_changed', 'status', 'pending', 'in_progress', NULL, 'Estado cambiado de pending a in_progress', '2025-08-07 18:05:27'),
(120, 38, 12, 'status_changed', 'status', 'pending', 'in_progress', NULL, 'Estado cambiado de pending a in_progress', '2025-08-07 18:05:44'),
(121, 30, 9, 'status_changed', 'status', 'pending', 'in_progress', NULL, 'Estado cambiado de pending a in_progress', '2025-08-07 18:05:55'),
(122, 48, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-07 18:07:44'),
(123, 43, 2, 'status_changed', 'status', 'pending', 'in_progress', NULL, 'Estado cambiado de pending a in_progress', '2025-08-07 20:16:05'),
(124, 49, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-07 20:40:50'),
(125, 49, 2, 'status_changed', 'status', 'pending', 'in_progress', NULL, 'Estado cambiado de pending a in_progress', '2025-08-07 20:41:57'),
(126, 41, 5, 'status_changed', 'status', 'completed', 'pending', NULL, 'Estado cambiado de completed a pending', '2025-08-07 22:24:41'),
(127, 50, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-07 22:30:59'),
(128, 51, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-07 22:32:59'),
(129, 41, 5, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-07 22:34:56'),
(130, 51, 2, 'commented', 'comment', NULL, 'fsadfasd', NULL, 'Comentario agregado', '2025-08-11 21:18:24'),
(131, 51, 2, 'commented', 'comment', NULL, 'Esto es nuevo comentario 2', NULL, 'Comentario agregado', '2025-08-11 21:18:41'),
(132, 51, 2, 'commented', 'comment', NULL, 'Hola esto es un archivo', NULL, 'Comentario agregado', '2025-08-11 21:28:22'),
(133, 51, 2, 'commented', 'comment', NULL, 'Hola esto es un archivo', NULL, 'Comentario agregado', '2025-08-11 21:54:08'),
(134, 51, 2, 'commented', 'comment', NULL, 'Comentario con adjuncion de archivo', NULL, 'Comentario agregado', '2025-08-11 22:00:29'),
(135, 51, 2, 'commented', 'comment', NULL, 'archivo', NULL, 'Comentario agregado', '2025-08-11 22:02:41'),
(136, 51, 2, 'commented', 'comment', NULL, 'Docs', NULL, 'Comentario agregado', '2025-08-11 22:02:58'),
(137, 51, 2, 'commented', 'comment', NULL, 'documentos', NULL, 'Comentario agregado', '2025-08-11 22:05:57'),
(138, 51, 2, 'commented', 'comment', NULL, 'documentacion', NULL, 'Comentario agregado', '2025-08-11 22:11:29'),
(139, 51, 2, 'commented', 'comment', NULL, 'docx', NULL, 'Comentario agregado', '2025-08-11 22:15:09'),
(140, 51, 2, 'commented', 'comment', NULL, 'pdfs', NULL, 'Comentario agregado', '2025-08-11 22:15:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Task_Labels`
--

CREATE TABLE `Task_Labels` (
  `label_id` int(11) NOT NULL,
  `label_name` varchar(50) NOT NULL,
  `label_color` varchar(7) DEFAULT '#3B82F6',
  `clan_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
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
  `task_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL,
  `assigned_at` timestamp NULL DEFAULT current_timestamp(),
  `assigned_by_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Users`
--

CREATE TABLE `Users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Users`
--

INSERT INTO `Users` (`user_id`, `username`, `password_hash`, `email`, `full_name`, `is_active`, `last_login`, `created_at`) VALUES
(1, 'super', '123456', 'desarrollo@rinorisk.com', 'Usuario Administrador', 1, '2025-08-11 21:00:00', '2025-07-29 22:45:12'),
(2, 'abdielc', '123456', 'abdiel@astrasoft.mx', 'Abdiel Carrasco', 1, '2025-08-11 20:55:47', '2025-07-29 23:23:21'),
(3, 'usuario1', '123456', 'usuario1@ejemplo.com', 'Juan Pérez García', 1, '2025-07-30 16:14:52', '2025-07-29 23:23:21'),
(4, 'franklinb', '123456', 'desarollo2@rinorisk.com', 'Franklin Benitez', 1, NULL, '2025-07-30 17:10:05'),
(5, 'Rubend', '123456', 'desarrollo3@rinorisk.com', 'Ruben Dorado', 1, NULL, '2025-07-30 18:20:45'),
(6, 'gaelh', '123456', 'desarrollo.fulstack@rinorisk.com', 'Gael Herrera', 1, NULL, '2025-07-30 19:36:11'),
(7, 'test_user_1753904255', '123456', 'test1753904255@example.com', 'Usuario de Prueba', 0, NULL, '2025-07-30 19:37:36'),
(9, 'manuels', '123456', 'desarrollo.frontjr@rinorisk.com', 'Manuel Saenz', 1, NULL, '2025-07-30 19:46:47'),
(10, 'piña', '123456', 'desarrollo.dataanalyst@rinorisk.com', 'Jaen Piña', 1, NULL, '2025-07-30 19:47:38'),
(11, 'isaccg', '123456', 'desarrollo.backend@rinorisk.com', 'Isaac Garcia', 1, NULL, '2025-07-30 19:48:29'),
(12, 'janathanm', '123456', 'desarrollo.fullstack.2@rinorisk.com', 'Jonathan Martinez', 1, NULL, '2025-07-30 19:51:19'),
(13, 'jessicam', '123456', 'gerente.mkt@rinorisk.com', 'Jessica Mejia', 1, '2025-08-07 20:54:40', '2025-07-30 19:54:06'),
(14, 'bereniceh', '123456', 'gerente.rh@rinorisk.com', 'Berenice Hernandez', 1, '2025-08-07 18:40:03', '2025-07-30 20:35:34'),
(15, 'Valeriag', '123456', 'cultura.organizacional@rinorisk.com', 'Valeria Garcia', 1, NULL, '2025-08-04 19:11:54'),
(16, 'Nora', '123456', 'sincorreo1@rinorisk.com', 'Nora', 1, NULL, '2025-08-06 21:01:29'),
(17, 'ricardov', '123456', 'redes@rinorisk.com', 'Ricardo Vidaño', 1, NULL, '2025-08-06 21:03:34'),
(18, 'israell', '123456', 'publicidad@rinorisk.com', 'Israel Lovato', 1, NULL, '2025-08-06 21:04:17'),
(19, 'arisbethc', '123456', 'supervisor.comercialtj@rinorisk.com', 'Arisbeth Cuevas', 1, '2025-08-07 20:20:36', '2025-08-06 21:05:25'),
(20, 'marlenef', '123456', 'cobranza@rinorisk.com', 'Marlene Flores', 1, NULL, '2025-08-06 21:06:16'),
(21, 'Beatrizi', '123456', 'servicio.vgmtj@rinorisk.com', 'Beatriz Ita', 1, NULL, '2025-08-06 21:07:46'),
(22, 'sofiag', '123456', 'Sofia@rinorisk.com', 'Sofia Gallardo', 1, '2025-08-11 20:54:22', '2025-08-06 21:09:03'),
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
(50, 'karenf', '123456', 'sincorreo19@rinorisk.com', 'Karen Fletes', 1, '2025-08-07 20:20:10', '2025-08-06 21:54:25'),
(51, 'lider_direccion', '123456', 'lider.direccion@rinorisk.com', 'Líder Dirección', 1, NULL, '2025-08-06 22:19:51'),
(52, 'lider_gaia', '123456', 'lider.gaia@rinorisk.com', 'Líder Gaia', 1, NULL, '2025-08-06 22:19:51'),
(53, 'lider_operaciones', '123456', 'lider.operaciones@rinorisk.com', 'Líder Operaciones', 1, NULL, '2025-08-06 22:19:51'),
(54, 'lider_servicio', '123456', 'lider.servicio@rinorisk.com', 'Líder Servicio', 1, NULL, '2025-08-06 22:19:52'),
(55, 'rosaliae', '123456', 'sincorreo20@rinorisk.com', 'Rosalia Enriquez', 1, NULL, '2025-08-06 23:21:11'),
(56, 'sonial', '123456', 'sincorreo21@rinorisk.com', 'Sonia Lopez', 1, NULL, '2025-08-06 23:24:15'),
(57, 'ivan2', '123456', 'sincorreo23@rinorsik.com', 'Ivan Mosqueda', 1, '2025-08-07 20:22:35', '2025-08-06 23:25:11'),
(58, 'ivan3', '123456', 'sincorreo27@rinorisk.com', 'Ivan Mozqueda', 1, '2025-08-07 15:32:18', '2025-08-06 23:29:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `User_Roles`
--

CREATE TABLE `User_Roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
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
(15, 3),
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
-- Estructura de tabla para la tabla `v_subtasks_complete`
--

CREATE TABLE `v_subtasks_complete` (
  `subtask_id` int(11) DEFAULT NULL,
  `task_id` int(11) DEFAULT NULL,
  `parent_task_name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `completion_percentage` decimal(5,2) DEFAULT NULL,
  `estimated_hours` decimal(5,2) DEFAULT NULL,
  `actual_hours` decimal(5,2) DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `assigned_to_user_id` int(11) DEFAULT NULL,
  `assigned_user_name` varchar(100) DEFAULT NULL,
  `assigned_username` varchar(100) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `created_by_name` varchar(100) DEFAULT NULL,
  `subtask_order` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `project_name` varchar(255) DEFAULT NULL,
  `clan_id` int(11) DEFAULT NULL,
  `clan_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `v_tasks_complete`
--

CREATE TABLE `v_tasks_complete` (
  `task_id` int(11) DEFAULT NULL,
  `task_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `project_name` varchar(255) DEFAULT NULL,
  `parent_task_id` int(11) DEFAULT NULL,
  `parent_task_name` varchar(255) DEFAULT NULL,
  `assigned_to_user_id` int(11) DEFAULT NULL,
  `assigned_user_name` varchar(100) DEFAULT NULL,
  `assigned_username` varchar(100) DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT NULL,
  `priority` enum('low','medium','high','critical') DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `estimated_hours` decimal(5,2) DEFAULT NULL,
  `actual_hours` decimal(5,2) DEFAULT NULL,
  `completion_percentage` decimal(5,2) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `created_by_name` varchar(100) DEFAULT NULL,
  `assigned_percentage` decimal(5,2) DEFAULT NULL,
  `color_tag` varchar(7) DEFAULT NULL,
  `is_subtask` tinyint(1) DEFAULT NULL,
  `subtask_order` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `clan_id` int(11) DEFAULT NULL,
  `clan_name` varchar(100) DEFAULT NULL,
  `subtasks_count` bigint(20) DEFAULT NULL,
  `subtasks_completed` bigint(20) DEFAULT NULL,
  `comments_count` bigint(20) DEFAULT NULL,
  `attachments_count` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Tasks`
--
ALTER TABLE `Tasks`
  ADD PRIMARY KEY (`task_id`);

--
-- Indices de la tabla `Task_Attachments`
--
ALTER TABLE `Task_Attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `idx_comment_id` (`comment_id`);

--
-- Indices de la tabla `Task_Comments`
--
ALTER TABLE `Task_Comments`
  ADD PRIMARY KEY (`comment_id`);

--
-- Indices de la tabla `Task_History`
--
ALTER TABLE `Task_History`
  ADD PRIMARY KEY (`history_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Tasks`
--
ALTER TABLE `Tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `Task_Attachments`
--
ALTER TABLE `Task_Attachments`
  MODIFY `attachment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `Task_Comments`
--
ALTER TABLE `Task_Comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `Task_History`
--
ALTER TABLE `Task_History`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
