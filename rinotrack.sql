-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 20-08-2025 a las 23:40:11
-- Versión del servidor: 11.8.2-MariaDB
-- Versión de PHP: 8.3.23

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
(5, 'Kratos (DTYS)', 'Desarrollo tecnológico y Sistemas', '2025-07-30 16:53:15'),
(6, 'Hermes (MKT)', 'Marketing', '2025-07-30 19:53:16'),
(7, 'Afrodita (RRHH)', 'Recursos Humanos', '2025-07-30 20:31:47'),
(8, 'Perséfone (SERV)', 'Servicio', '2025-08-02 17:50:21'),
(10, 'Deméter (ZAX)', 'ZAX', '2025-08-04 17:57:03'),
(11, 'Helios (COM)', 'Comercial', '2025-08-06 20:43:53'),
(12, 'GAIA', 'Operación/Proyectos', '2025-08-06 20:50:45'),
(13, 'Olympo', 'Dirección', '2025-08-06 20:56:30');

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
(138, 13, 2),
(137, 13, 4),
(136, 13, 5),
(135, 13, 6),
(134, 13, 9),
(133, 13, 10),
(132, 13, 11),
(131, 13, 12),
(130, 13, 13),
(129, 13, 14),
(128, 13, 15),
(127, 13, 16),
(126, 13, 17),
(125, 13, 18),
(124, 13, 19),
(123, 13, 20),
(122, 13, 21),
(121, 13, 22),
(120, 13, 23),
(119, 13, 24),
(118, 13, 25),
(117, 13, 26),
(116, 13, 27),
(115, 13, 28),
(114, 13, 29),
(112, 13, 31),
(111, 13, 32),
(110, 13, 33),
(109, 13, 34),
(108, 13, 35),
(107, 13, 36),
(106, 13, 37),
(105, 13, 38),
(104, 13, 39),
(103, 13, 40),
(102, 13, 41),
(101, 13, 42),
(100, 13, 43),
(99, 13, 44),
(98, 13, 45),
(97, 13, 46),
(95, 13, 47),
(94, 13, 48),
(93, 13, 49),
(92, 13, 50),
(91, 13, 55),
(90, 13, 56),
(96, 13, 58),
(139, 13, 63);

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
-- Estructura de tabla para la tabla `Notification_Log`
--

CREATE TABLE `Notification_Log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_type` varchar(100) NOT NULL,
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sent_to` varchar(255) NOT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Volcado de datos para la tabla `Notification_Log`
--

INSERT INTO `Notification_Log` (`id`, `event_type`, `entity_id`, `user_id`, `sent_to`, `meta`, `created_at`) VALUES
(1, 'project_assigned_to_clan', 18, 1, 'desarrollo@rinorisk.com', NULL, '2025-08-13 06:31:58'),
(2, 'project_assigned_to_clan', 18, 2, 'abdiel@astrasoft.mx', NULL, '2025-08-13 06:31:59'),
(3, 'project_assigned_to_clan', 18, 4, 'desarollo2@rinorisk.com', NULL, '2025-08-13 06:32:00'),
(4, 'project_assigned_to_clan', 18, 5, 'desarrollo3@rinorisk.com', NULL, '2025-08-13 06:32:02'),
(5, 'project_assigned_to_clan', 18, 6, 'desarrollo.fulstack@rinorisk.com', NULL, '2025-08-13 06:32:03'),
(6, 'project_assigned_to_clan', 18, 9, 'desarrollo.frontjr@rinorisk.com', NULL, '2025-08-13 06:32:04'),
(7, 'project_assigned_to_clan', 18, 10, 'desarrollo.dataanalyst@rinorisk.com', NULL, '2025-08-13 06:32:05'),
(8, 'project_assigned_to_clan', 18, 11, 'desarrollo.backend@rinorisk.com', NULL, '2025-08-13 06:32:06'),
(9, 'project_assigned_to_clan', 18, 12, 'desarrollo.fullstack.2@rinorisk.com', NULL, '2025-08-13 06:32:07'),
(10, 'project_assigned_to_clan', 18, 38, 'sistemas@rinorisk.com', NULL, '2025-08-13 06:32:08'),
(11, 'project_assigned_to_clan', 19, 1, 'desarrollo@rinorisk.com', NULL, '2025-08-13 06:36:21'),
(12, 'project_assigned_to_clan', 19, 2, 'abdiel@astrasoft.mx', NULL, '2025-08-13 06:36:22'),
(13, 'project_assigned_to_clan', 19, 4, 'desarollo2@rinorisk.com', NULL, '2025-08-13 06:36:23'),
(14, 'project_assigned_to_clan', 19, 5, 'desarrollo3@rinorisk.com', NULL, '2025-08-13 06:36:24'),
(15, 'project_assigned_to_clan', 19, 6, 'desarrollo.fulstack@rinorisk.com', NULL, '2025-08-13 06:36:24'),
(16, 'project_assigned_to_clan', 19, 9, 'desarrollo.frontjr@rinorisk.com', NULL, '2025-08-13 06:36:25'),
(17, 'project_assigned_to_clan', 19, 10, 'desarrollo.dataanalyst@rinorisk.com', NULL, '2025-08-13 06:36:26'),
(18, 'project_assigned_to_clan', 19, 11, 'desarrollo.backend@rinorisk.com', NULL, '2025-08-13 06:36:27'),
(19, 'project_assigned_to_clan', 19, 12, 'desarrollo.fullstack.2@rinorisk.com', NULL, '2025-08-13 06:36:28'),
(20, 'project_assigned_to_clan', 19, 38, 'sistemas@rinorisk.com', NULL, '2025-08-13 06:36:29'),
(21, 'task_assigned', 70, 2, 'abdiel@astrasoft.mx', NULL, '2025-08-13 07:21:10'),
(22, 'task_assigned', 71, 2, 'abdiel@astrasoft.mx', NULL, '2025-08-13 07:25:38'),
(23, 'task_assigned', 72, 2, 'abdiel@astrasoft.mx', NULL, '2025-08-13 07:27:54'),
(24, 'project_assigned_to_clan', 20, 1, 'desarrollo@rinorisk.com', NULL, '2025-08-13 07:29:23'),
(25, 'project_assigned_to_clan', 20, 2, 'abdiel@astrasoft.mx', NULL, '2025-08-13 07:29:25'),
(26, 'project_assigned_to_clan', 20, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-13 07:29:26'),
(27, 'project_assigned_to_clan', 20, 6, 'desarrollo.fulstack@rinorisk.com', NULL, '2025-08-13 07:29:31'),
(28, 'project_assigned_to_clan', 20, 9, 'desarrollo.frontjr@rinorisk.com', NULL, '2025-08-13 07:29:33'),
(29, 'project_assigned_to_clan', 20, 10, 'desarrollo.dataanalyst@rinorisk.com', NULL, '2025-08-13 07:29:35'),
(30, 'project_assigned_to_clan', 20, 11, 'desarrollo.backend@rinorisk.com', NULL, '2025-08-13 07:29:37'),
(31, 'project_assigned_to_clan', 20, 12, 'desarrollo.fullstack.2@rinorisk.com', NULL, '2025-08-13 07:29:38'),
(32, 'project_assigned_to_clan', 20, 38, 'sistemas@rinorisk.com', NULL, '2025-08-13 07:29:39'),
(33, 'task_assigned', 73, 2, 'abdiel@astrasoft.mx', NULL, '2025-08-13 07:31:59'),
(34, 'task_assigned', 74, 2, 'abdiel@astrasoft.mx', NULL, '2025-08-13 07:35:48'),
(35, 'task_assigned', 75, 2, 'abdiel@astrasoft.mx', NULL, '2025-08-13 07:36:16'),
(36, 'task_assigned', 75, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-13 07:36:17'),
(37, 'task_assigned', 75, 6, 'desarrollo.fulstack@rinorisk.com', NULL, '2025-08-13 07:36:17'),
(38, 'task_assigned', 75, 10, 'desarrollo.dataanalyst@rinorisk.com', NULL, '2025-08-13 07:36:19'),
(39, 'task_assigned', 75, 11, 'desarrollo.backend@rinorisk.com', NULL, '2025-08-13 07:36:20'),
(40, 'task_assigned', 75, 12, 'desarrollo.fullstack.2@rinorisk.com', NULL, '2025-08-13 07:36:23'),
(41, 'task_assigned', 76, 2, 'abdiel@astrasoft.mx', NULL, '2025-08-13 07:37:08'),
(42, 'task_assigned', 76, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-13 07:37:09'),
(43, 'task_assigned', 76, 11, 'desarrollo.backend@rinorisk.com', NULL, '2025-08-13 07:37:11'),
(44, 'task_assigned', 77, 2, 'redskullcoder@gmail.com', NULL, '2025-08-13 07:38:40'),
(45, 'project_assigned_to_clan', 21, 1, 'desarrollo@rinorisk.com', NULL, '2025-08-13 07:45:15'),
(46, 'project_assigned_to_clan', 21, 2, 'redskullcoder@gmail.com', NULL, '2025-08-13 07:45:16'),
(47, 'project_assigned_to_clan', 21, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-13 07:45:17'),
(48, 'project_assigned_to_clan', 21, 5, 'desarrollo3@rinorisk.com', NULL, '2025-08-13 07:45:20'),
(49, 'project_assigned_to_clan', 21, 6, 'desarrollo.fulstack@rinorisk.com', NULL, '2025-08-13 07:45:21'),
(50, 'project_assigned_to_clan', 21, 9, 'desarrollo.frontjr@rinorisk.com', NULL, '2025-08-13 07:45:22'),
(51, 'project_assigned_to_clan', 21, 10, 'desarrollo.dataanalyst@rinorisk.com', NULL, '2025-08-13 07:45:23'),
(52, 'project_assigned_to_clan', 21, 11, 'desarrollo.backend@rinorisk.com', NULL, '2025-08-13 07:45:25'),
(53, 'project_assigned_to_clan', 21, 12, 'desarrollo.fullstack.2@rinorisk.com', NULL, '2025-08-13 07:45:26'),
(54, 'project_assigned_to_clan', 21, 38, 'sistemas@rinorisk.com', NULL, '2025-08-13 07:45:27'),
(55, 'task_assigned', 78, 2, 'redskullcoder@gmail.com', NULL, '2025-08-13 07:46:21'),
(56, 'task_assigned', 79, 2, 'redskullcoder@gmail.com', NULL, '2025-08-13 07:50:02'),
(57, 'project_assigned_to_clan', 22, 1, 'desarrollo@rinorisk.com', NULL, '2025-08-13 15:11:30'),
(58, 'project_assigned_to_clan', 22, 2, 'redskullcoder@gmail.com', NULL, '2025-08-13 15:11:31'),
(59, 'project_assigned_to_clan', 22, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-13 15:11:33'),
(60, 'project_assigned_to_clan', 22, 5, 'desarrollo3@rinorisk.com', NULL, '2025-08-13 15:11:34'),
(61, 'project_assigned_to_clan', 22, 6, 'desarrollo.fulstack@rinorisk.com', NULL, '2025-08-13 15:11:35'),
(62, 'project_assigned_to_clan', 22, 9, 'desarrollo.frontjr@rinorisk.com', NULL, '2025-08-13 15:11:36'),
(63, 'project_assigned_to_clan', 22, 10, 'desarrollo.dataanalyst@rinorisk.com', NULL, '2025-08-13 15:11:37'),
(64, 'project_assigned_to_clan', 22, 11, 'desarrollo.backend@rinorisk.com', NULL, '2025-08-13 15:11:38'),
(65, 'project_assigned_to_clan', 22, 12, 'desarrollo.fullstack.2@rinorisk.com', NULL, '2025-08-13 15:11:39'),
(66, 'project_assigned_to_clan', 22, 38, 'sistemas@rinorisk.com', NULL, '2025-08-13 15:11:41'),
(67, 'task_assigned', 80, 2, 'redskullcoder@gmail.com', NULL, '2025-08-13 15:12:29'),
(68, 'task_assigned', 80, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-13 15:12:30'),
(69, 'task_assigned', 80, 11, 'desarrollo.backend@rinorisk.com', NULL, '2025-08-13 15:12:31'),
(70, 'project_assigned_to_clan', 23, 1, 'desarrollo@rinorisk.com', NULL, '2025-08-13 16:26:31'),
(71, 'project_assigned_to_clan', 23, 2, 'redskullcoder@gmail.com', NULL, '2025-08-13 16:26:32'),
(72, 'project_assigned_to_clan', 23, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-13 16:26:33'),
(73, 'project_assigned_to_clan', 23, 5, 'desarrollo3@rinorisk.com', NULL, '2025-08-13 16:26:34'),
(74, 'project_assigned_to_clan', 23, 6, 'desarrollo.fulstack@rinorisk.com', NULL, '2025-08-13 16:26:35'),
(75, 'project_assigned_to_clan', 23, 9, 'desarrollo.frontjr@rinorisk.com', NULL, '2025-08-13 16:26:36'),
(76, 'project_assigned_to_clan', 23, 10, 'desarrollo.dataanalyst@rinorisk.com', NULL, '2025-08-13 16:26:37'),
(77, 'project_assigned_to_clan', 23, 11, 'desarrollo.backend@rinorisk.com', NULL, '2025-08-13 16:26:38'),
(78, 'project_assigned_to_clan', 23, 12, 'desarrollo.fullstack.2@rinorisk.com', NULL, '2025-08-13 16:26:39'),
(79, 'project_assigned_to_clan', 23, 38, 'sistemas@rinorisk.com', NULL, '2025-08-13 16:26:40'),
(80, 'project_assigned_to_clan', 24, 1, 'desarrollo@rinorisk.com', NULL, '2025-08-13 16:29:26'),
(81, 'project_assigned_to_clan', 24, 2, 'redskullcoder@gmail.com', NULL, '2025-08-13 16:29:28'),
(82, 'project_assigned_to_clan', 24, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-13 16:29:29'),
(83, 'project_assigned_to_clan', 24, 5, 'desarrollo3@rinorisk.com', NULL, '2025-08-13 16:29:30'),
(84, 'project_assigned_to_clan', 24, 6, 'desarrollo.fulstack@rinorisk.com', NULL, '2025-08-13 16:29:31'),
(85, 'project_assigned_to_clan', 24, 9, 'desarrollo.frontjr@rinorisk.com', NULL, '2025-08-13 16:29:32'),
(86, 'project_assigned_to_clan', 24, 10, 'desarrollo.dataanalyst@rinorisk.com', NULL, '2025-08-13 16:29:33'),
(87, 'project_assigned_to_clan', 24, 11, 'desarrollo.backend@rinorisk.com', NULL, '2025-08-13 16:29:34'),
(88, 'project_assigned_to_clan', 24, 12, 'desarrollo.fullstack.2@rinorisk.com', NULL, '2025-08-13 16:29:35'),
(89, 'project_assigned_to_clan', 24, 38, 'sistemas@rinorisk.com', NULL, '2025-08-13 16:29:36'),
(90, 'project_assigned_to_clan', 25, 1, 'desarrollo@rinorisk.com', NULL, '2025-08-13 16:30:53'),
(91, 'project_assigned_to_clan', 25, 2, 'redskullcoder@gmail.com', NULL, '2025-08-13 16:30:54'),
(92, 'project_assigned_to_clan', 25, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-13 16:30:55'),
(93, 'project_assigned_to_clan', 25, 5, 'desarrollo3@rinorisk.com', NULL, '2025-08-13 16:30:56'),
(94, 'project_assigned_to_clan', 25, 6, 'desarrollo.fulstack@rinorisk.com', NULL, '2025-08-13 16:30:57'),
(95, 'project_assigned_to_clan', 25, 9, 'desarrollo.frontjr@rinorisk.com', NULL, '2025-08-13 16:30:58'),
(96, 'project_assigned_to_clan', 25, 10, 'desarrollo.dataanalyst@rinorisk.com', NULL, '2025-08-13 16:30:59'),
(97, 'project_assigned_to_clan', 25, 11, 'desarrollo.backend@rinorisk.com', NULL, '2025-08-13 16:31:00'),
(98, 'project_assigned_to_clan', 25, 12, 'desarrollo.fullstack.2@rinorisk.com', NULL, '2025-08-13 16:31:01'),
(99, 'project_assigned_to_clan', 25, 38, 'sistemas@rinorisk.com', NULL, '2025-08-13 16:31:02'),
(100, 'project_assigned_to_clan', 26, 1, 'desarrollo@rinorisk.com', NULL, '2025-08-13 16:31:34'),
(101, 'project_assigned_to_clan', 26, 2, 'redskullcoder@gmail.com', NULL, '2025-08-13 16:31:35'),
(102, 'project_assigned_to_clan', 26, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-13 16:31:36'),
(103, 'project_assigned_to_clan', 26, 5, 'desarrollo3@rinorisk.com', NULL, '2025-08-13 16:31:37'),
(104, 'project_assigned_to_clan', 26, 6, 'desarrollo.fulstack@rinorisk.com', NULL, '2025-08-13 16:31:38'),
(105, 'project_assigned_to_clan', 26, 9, 'desarrollo.frontjr@rinorisk.com', NULL, '2025-08-13 16:31:39'),
(106, 'project_assigned_to_clan', 26, 10, 'desarrollo.dataanalyst@rinorisk.com', NULL, '2025-08-13 16:31:40'),
(107, 'project_assigned_to_clan', 26, 11, 'desarrollo.backend@rinorisk.com', NULL, '2025-08-13 16:31:41'),
(108, 'project_assigned_to_clan', 26, 12, 'desarrollo.fullstack.2@rinorisk.com', NULL, '2025-08-13 16:31:42'),
(109, 'project_assigned_to_clan', 26, 38, 'sistemas@rinorisk.com', NULL, '2025-08-13 16:31:44'),
(110, 'project_assigned_to_clan', 27, 1, 'desarrollo@rinorisk.com', NULL, '2025-08-13 16:32:06'),
(111, 'project_assigned_to_clan', 27, 2, 'redskullcoder@gmail.com', NULL, '2025-08-13 16:32:07'),
(112, 'project_assigned_to_clan', 27, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-13 16:32:08'),
(113, 'project_assigned_to_clan', 27, 5, 'desarrollo3@rinorisk.com', NULL, '2025-08-13 16:32:10'),
(114, 'project_assigned_to_clan', 27, 6, 'desarrollo.fulstack@rinorisk.com', NULL, '2025-08-13 16:32:11'),
(115, 'project_assigned_to_clan', 27, 9, 'desarrollo.frontjr@rinorisk.com', NULL, '2025-08-13 16:32:12'),
(116, 'project_assigned_to_clan', 27, 10, 'desarrollo.dataanalyst@rinorisk.com', NULL, '2025-08-13 16:32:13'),
(117, 'project_assigned_to_clan', 27, 11, 'desarrollo.backend@rinorisk.com', NULL, '2025-08-13 16:32:14'),
(118, 'project_assigned_to_clan', 27, 12, 'desarrollo.fullstack.2@rinorisk.com', NULL, '2025-08-13 16:32:15'),
(119, 'project_assigned_to_clan', 27, 38, 'sistemas@rinorisk.com', NULL, '2025-08-13 16:32:16'),
(120, 'task_assigned', 81, 2, 'redskullcoder@gmail.com', NULL, '2025-08-13 16:37:38'),
(121, 'task_assigned', 82, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-13 16:38:15'),
(122, 'task_assigned', 82, 10, 'desarrollo.dataanalyst@rinorisk.com', NULL, '2025-08-13 16:38:16'),
(123, 'task_assigned', 82, 11, 'desarrollo.backend@rinorisk.com', NULL, '2025-08-13 16:38:18'),
(124, 'task_assigned', 82, 12, 'desarrollo.fullstack.2@rinorisk.com', NULL, '2025-08-13 16:38:19'),
(125, 'task_assigned', 83, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-13 16:38:20'),
(126, 'task_assigned', 83, 10, 'desarrollo.dataanalyst@rinorisk.com', NULL, '2025-08-13 16:38:21'),
(127, 'task_assigned', 83, 11, 'desarrollo.backend@rinorisk.com', NULL, '2025-08-13 16:38:22'),
(128, 'task_assigned', 83, 12, 'desarrollo.fullstack.2@rinorisk.com', NULL, '2025-08-13 16:38:23'),
(129, 'task_assigned', 84, 2, 'redskullcoder@gmail.com', NULL, '2025-08-13 16:39:21'),
(130, 'task_assigned', 85, 1, 'desarrollo@rinorisk.com', NULL, '2025-08-13 16:46:10'),
(131, 'task_assigned', 85, 2, 'redskullcoder@gmail.com', NULL, '2025-08-13 16:46:11'),
(132, 'task_assigned', 85, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-13 16:46:12'),
(133, 'task_assigned', 85, 5, 'desarrollo3@rinorisk.com', NULL, '2025-08-13 16:46:13'),
(134, 'task_assigned', 85, 6, 'desarrollo.fulstack@rinorisk.com', NULL, '2025-08-13 16:46:14'),
(135, 'task_assigned', 85, 9, 'desarrollo.frontjr@rinorisk.com', NULL, '2025-08-13 16:46:15'),
(136, 'task_assigned', 85, 10, 'desarrollo.dataanalyst@rinorisk.com', NULL, '2025-08-13 16:46:16'),
(137, 'task_assigned', 85, 11, 'desarrollo.backend@rinorisk.com', NULL, '2025-08-13 16:46:17'),
(138, 'task_assigned', 85, 12, 'desarrollo.fullstack.2@rinorisk.com', NULL, '2025-08-13 16:46:18'),
(139, 'task_assigned', 85, 38, 'sistemas@rinorisk.com', NULL, '2025-08-13 16:46:18'),
(140, 'project_assigned_to_clan', 28, 13, 'gerente.mkt@rinorisk.com', NULL, '2025-08-13 17:01:11'),
(141, 'project_assigned_to_clan', 28, 15, 'cultura.organizacional@rinorisk.com', NULL, '2025-08-13 17:01:12'),
(142, 'project_assigned_to_clan', 28, 17, 'redes@rinorisk.com', NULL, '2025-08-13 17:01:12'),
(143, 'project_assigned_to_clan', 28, 18, 'publicidad@rinorisk.com', NULL, '2025-08-13 17:01:14'),
(144, 'project_assigned_to_clan', 28, 44, 'sincorreo13@rinorisk.com', NULL, '2025-08-13 17:01:15'),
(145, 'task_assigned', 86, 5, 'desarrollo3@rinorisk.com', NULL, '2025-08-13 17:01:58'),
(146, 'task_assigned', 86, 9, 'desarrollo.frontjr@rinorisk.com', NULL, '2025-08-13 17:01:59'),
(147, 'task_assigned', 87, 5, 'desarrollo3@rinorisk.com', NULL, '2025-08-13 17:02:25'),
(148, 'task_assigned', 87, 9, 'desarrollo.frontjr@rinorisk.com', NULL, '2025-08-13 17:02:27'),
(149, 'task_assigned', 88, 5, 'desarrollo3@rinorisk.com', NULL, '2025-08-13 17:02:55'),
(150, 'task_assigned', 88, 9, 'desarrollo.frontjr@rinorisk.com', NULL, '2025-08-13 17:02:56'),
(151, 'project_assigned_to_clan', 29, 1, 'desarrollo@rinorisk.com', NULL, '2025-08-13 17:35:39'),
(152, 'project_assigned_to_clan', 29, 2, 'redskullcoder@gmail.com', NULL, '2025-08-13 17:35:40'),
(153, 'project_assigned_to_clan', 29, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-13 17:35:41'),
(154, 'project_assigned_to_clan', 29, 5, 'desarrollo3@rinorisk.com', NULL, '2025-08-13 17:35:42'),
(155, 'project_assigned_to_clan', 29, 6, 'desarrollo.fulstack@rinorisk.com', NULL, '2025-08-13 17:35:43'),
(156, 'project_assigned_to_clan', 29, 9, 'desarrollo.frontjr@rinorisk.com', NULL, '2025-08-13 17:35:44'),
(157, 'project_assigned_to_clan', 29, 10, 'desarrollo.dataanalyst@rinorisk.com', NULL, '2025-08-13 17:35:45'),
(158, 'project_assigned_to_clan', 29, 11, 'desarrollo.backend@rinorisk.com', NULL, '2025-08-13 17:35:46'),
(159, 'project_assigned_to_clan', 29, 12, 'desarrollo.fullstack.2@rinorisk.com', NULL, '2025-08-13 17:35:47'),
(160, 'project_assigned_to_clan', 29, 38, 'sistemas@rinorisk.com', NULL, '2025-08-13 17:35:48'),
(161, 'task_assigned', 89, 2, 'redskullcoder@gmail.com', NULL, '2025-08-13 18:34:00'),
(162, 'task_assigned', 89, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-13 18:34:01'),
(163, 'project_assigned_to_clan', 30, 13, 'gerente.mkt@rinorisk.com', NULL, '2025-08-13 21:21:49'),
(164, 'project_assigned_to_clan', 30, 15, 'cultura.organizacional@rinorisk.com', NULL, '2025-08-13 21:21:49'),
(165, 'project_assigned_to_clan', 30, 17, 'redes@rinorisk.com', NULL, '2025-08-13 21:21:50'),
(166, 'project_assigned_to_clan', 30, 18, 'publicidad@rinorisk.com', NULL, '2025-08-13 21:21:52'),
(167, 'project_assigned_to_clan', 30, 44, 'sincorreo13@rinorisk.com', NULL, '2025-08-13 21:21:53'),
(168, 'task_assigned', 91, 44, 'sincorreo13@rinorisk.com', NULL, '2025-08-13 21:27:35'),
(169, 'project_assigned_to_clan', 31, 22, 'Sofia@rinorisk.com', NULL, '2025-08-13 22:46:22'),
(170, 'project_assigned_to_clan', 31, 23, 'auxiliar.inmuebles@rinorisk.com', NULL, '2025-08-13 22:46:23'),
(171, 'project_assigned_to_clan', 31, 24, 'gerente.administrativo@rinorisk.com', NULL, '2025-08-13 22:46:24'),
(172, 'project_assigned_to_clan', 31, 25, 'contabilidad@rinorisk.com', NULL, '2025-08-13 22:46:27'),
(173, 'project_assigned_to_clan', 31, 26, 'auxiliar.contable@rinorisk.com', NULL, '2025-08-13 22:46:27'),
(174, 'project_assigned_to_clan', 31, 27, 'analista.financiero@rinorisk.com', NULL, '2025-08-13 22:46:29'),
(175, 'project_assigned_to_clan', 31, 28, 'legal@rinorisk.com', NULL, '2025-08-13 22:46:30'),
(176, 'project_assigned_to_clan', 31, 29, 'asistente.legal@rinorisk.com', NULL, '2025-08-13 22:46:33'),
(177, 'project_assigned_to_clan', 31, 30, 'procesos.operativos@rinorisk.com', NULL, '2025-08-13 22:46:34'),
(178, 'project_assigned_to_clan', 31, 31, 'sincorreo2@rinorisk.com', NULL, '2025-08-13 22:46:35'),
(179, 'project_assigned_to_clan', 31, 32, 'sincorreo3@rinorisk.com', NULL, '2025-08-13 22:46:36'),
(180, 'project_assigned_to_clan', 31, 33, 'sincorreo4@rinorisk.com', NULL, '2025-08-13 22:46:37'),
(181, 'project_assigned_to_clan', 31, 34, 'sincorreo5@rinorisk.com', NULL, '2025-08-13 22:46:39'),
(182, 'project_assigned_to_clan', 31, 35, 'sincorreo6@rinorisk.com', NULL, '2025-08-13 22:46:40'),
(183, 'project_assigned_to_clan', 31, 36, 'sincorreo7@rinorisk.com', NULL, '2025-08-13 22:46:41'),
(184, 'project_assigned_to_clan', 31, 37, 'sincorreo8@rinorisk.com', NULL, '2025-08-13 22:46:42'),
(185, 'project_assigned_to_clan', 31, 49, 'sincorreo18@rinorisk.com', NULL, '2025-08-13 22:46:43'),
(186, 'project_assigned_to_clan', 32, 1, 'desarrollo@rinorisk.com', NULL, '2025-08-13 23:54:55'),
(187, 'project_assigned_to_clan', 32, 2, 'redskullcoder@gmail.com', NULL, '2025-08-13 23:54:56'),
(188, 'project_assigned_to_clan', 32, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-13 23:54:57'),
(189, 'project_assigned_to_clan', 32, 5, 'desarrollo3@rinorisk.com', NULL, '2025-08-13 23:54:58'),
(190, 'project_assigned_to_clan', 32, 6, 'desarrollo.fulstack@rinorisk.com', NULL, '2025-08-13 23:54:59'),
(191, 'project_assigned_to_clan', 32, 9, 'desarrollo.frontjr@rinorisk.com', NULL, '2025-08-13 23:55:00'),
(192, 'project_assigned_to_clan', 32, 10, 'desarrollo.dataanalyst@rinorisk.com', NULL, '2025-08-13 23:55:01'),
(193, 'project_assigned_to_clan', 32, 11, 'desarrollo.backend@rinorisk.com', NULL, '2025-08-13 23:55:02'),
(194, 'project_assigned_to_clan', 32, 12, 'desarrollo.fullstack.2@rinorisk.com', NULL, '2025-08-13 23:55:03'),
(195, 'project_assigned_to_clan', 32, 38, 'sistemas@rinorisk.com', NULL, '2025-08-13 23:55:05'),
(196, 'project_assigned_to_clan', 33, 1, 'desarrollo@rinorisk.com', NULL, '2025-08-13 23:57:21'),
(197, 'project_assigned_to_clan', 33, 2, 'redskullcoder@gmail.com', NULL, '2025-08-13 23:57:22'),
(198, 'project_assigned_to_clan', 33, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-13 23:57:23'),
(199, 'project_assigned_to_clan', 33, 5, 'desarrollo3@rinorisk.com', NULL, '2025-08-13 23:57:24'),
(200, 'project_assigned_to_clan', 33, 6, 'desarrollo.fulstack@rinorisk.com', NULL, '2025-08-13 23:57:25'),
(201, 'project_assigned_to_clan', 33, 9, 'desarrollo.frontjr@rinorisk.com', NULL, '2025-08-13 23:57:26'),
(202, 'project_assigned_to_clan', 33, 10, 'desarrollo.dataanalyst@rinorisk.com', NULL, '2025-08-13 23:57:27'),
(203, 'project_assigned_to_clan', 33, 11, 'desarrollo.backend@rinorisk.com', NULL, '2025-08-13 23:57:28'),
(204, 'project_assigned_to_clan', 33, 12, 'desarrollo.fullstack.2@rinorisk.com', NULL, '2025-08-13 23:57:29'),
(205, 'project_assigned_to_clan', 33, 38, 'sistemas@rinorisk.com', NULL, '2025-08-13 23:57:31'),
(206, 'task_assigned', 92, 2, 'redskullcoder@gmail.com', NULL, '2025-08-14 05:50:09'),
(207, 'task_assigned', 93, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-14 05:51:25'),
(208, 'task_assigned', 94, 2, 'redskullcoder@gmail.com', NULL, '2025-08-14 06:11:29'),
(209, 'task_assigned', 95, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-14 06:12:09'),
(210, 'task_assigned', 96, 2, 'redskullcoder@gmail.com', NULL, '2025-08-14 16:28:32'),
(211, 'task_assigned', 96, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-14 16:28:33'),
(212, 'task_assigned', 97, 2, 'redskullcoder@gmail.com', NULL, '2025-08-14 16:39:50'),
(213, 'task_assigned', 104, 2, 'redskullcoder@gmail.com', NULL, '2025-08-14 16:45:34'),
(214, 'task_assigned', 105, 2, 'redskullcoder@gmail.com', NULL, '2025-08-14 16:47:14'),
(215, 'task_assigned', 111, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-14 18:31:31'),
(216, 'task_assigned', 112, 2, 'redskullcoder@gmail.com', NULL, '2025-08-14 19:22:02'),
(217, 'task_assigned', 112, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-14 19:22:03'),
(218, 'task_assigned', 113, 2, 'redskullcoder@gmail.com', NULL, '2025-08-14 19:31:13'),
(219, 'task_assigned', 120, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-14 20:12:19'),
(220, 'task_assigned', 120, 6, 'desarrollo.fulstack@rinorisk.com', NULL, '2025-08-14 20:12:20'),
(221, 'task_assigned', 129, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-18 21:22:50'),
(222, 'task_assigned', 130, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-18 21:24:04'),
(223, 'task_assigned', 131, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-18 21:57:55'),
(224, 'task_assigned', 138, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-18 22:05:22'),
(225, 'task_assigned', 145, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-18 22:11:06'),
(226, 'task_assigned', 152, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-18 22:11:28'),
(227, 'task_assigned', 153, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-18 22:18:11'),
(228, 'task_assigned', 164, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-18 22:30:27'),
(229, 'task_assigned', 165, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-18 22:31:41'),
(230, 'task_assigned', 166, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-18 22:39:49'),
(231, 'task_assigned', 167, 2, 'redskullcoder@gmail.com', NULL, '2025-08-18 23:31:30'),
(232, 'task_assigned', 174, 2, 'redskullcoder@gmail.com', NULL, '2025-08-19 15:08:23'),
(233, 'task_assigned', 175, 2, 'redskullcoder@gmail.com', NULL, '2025-08-19 15:09:30'),
(234, 'task_assigned', 176, 2, 'redskullcoder@gmail.com', NULL, '2025-08-19 15:10:18'),
(235, 'task_assigned', 182, 2, 'redskullcoder@gmail.com', NULL, '2025-08-19 15:15:25'),
(236, 'task_assigned', 185, 2, 'redskullcoder@gmail.com', NULL, '2025-08-19 16:50:44'),
(237, 'task_assigned', 186, 2, 'redskullcoder@gmail.com', NULL, '2025-08-19 16:52:29'),
(238, 'task_assigned', 192, 2, 'redskullcoder@gmail.com', NULL, '2025-08-19 17:22:15'),
(239, 'task_assigned', 198, 2, 'redskullcoder@gmail.com', NULL, '2025-08-19 17:23:34'),
(240, 'task_assigned', 199, 2, 'redskullcoder@gmail.com', NULL, '2025-08-19 17:50:12'),
(241, 'task_assigned', 205, 2, 'redskullcoder@gmail.com', NULL, '2025-08-19 18:27:12'),
(242, 'task_assigned', 205, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-19 18:27:13'),
(243, 'task_assigned', 206, 2, 'redskullcoder@gmail.com', NULL, '2025-08-19 18:30:27'),
(244, 'task_assigned', 212, 2, 'redskullcoder@gmail.com', NULL, '2025-08-19 20:20:27'),
(245, 'task_assigned', 219, 2, 'redskullcoder@gmail.com', NULL, '2025-08-19 20:21:50'),
(246, 'task_assigned', 220, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-19 20:22:49'),
(247, 'task_assigned', 223, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-19 20:25:12'),
(248, 'task_assigned', 227, 2, 'redskullcoder@gmail.com', NULL, '2025-08-20 15:36:54'),
(249, 'task_assigned', 234, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-20 15:40:36'),
(250, 'task_assigned', 240, 24, 'gerente.administrativo@rinorisk.com', NULL, '2025-08-20 18:12:59'),
(251, 'task_assigned', 240, 27, 'analista.financiero@rinorisk.com', NULL, '2025-08-20 18:13:00'),
(252, 'task_assigned', 242, 2, 'redskullcoder@gmail.com', NULL, '2025-08-20 18:35:46'),
(253, 'task_assigned', 243, 2, 'redskullcoder@gmail.com', NULL, '2025-08-20 18:49:31'),
(254, 'task_assigned', 244, 2, 'redskullcoder@gmail.com', NULL, '2025-08-20 18:56:37'),
(255, 'task_assigned', 245, 2, 'redskullcoder@gmail.com', NULL, '2025-08-20 19:00:48'),
(256, 'task_assigned', 246, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-20 19:40:00'),
(257, 'task_assigned', 247, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-20 20:11:34'),
(258, 'task_assigned', 248, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-20 20:21:49'),
(259, 'task_assigned', 249, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-20 20:27:54'),
(260, 'task_assigned', 250, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-20 20:32:17'),
(261, 'task_assigned', 251, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-20 20:43:14'),
(262, 'task_assigned', 252, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-20 20:44:14'),
(263, 'task_assigned', 253, 22, 'Sofia@rinorisk.com', NULL, '2025-08-20 20:49:34'),
(264, 'task_assigned', 254, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-20 20:49:47'),
(265, 'task_assigned', 255, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-20 22:18:46'),
(266, 'task_assigned', 256, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-20 22:24:26'),
(267, 'task_assigned', 257, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-20 22:26:37'),
(268, 'task_assigned', 258, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-20 22:32:35'),
(269, 'task_assigned', 259, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-20 22:34:03'),
(270, 'task_assigned', 260, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-20 22:39:41'),
(271, 'task_assigned', 261, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-20 23:04:17'),
(272, 'task_assigned', 262, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-20 23:09:08'),
(273, 'task_assigned', 263, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-20 23:14:33'),
(274, 'task_assigned', 264, 4, 'desarrollo2@rinorisk.com', NULL, '2025-08-20 23:24:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Notification_Settings`
--

CREATE TABLE `Notification_Settings` (
  `setting_key` varchar(100) NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `recipients` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `value_int` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Volcado de datos para la tabla `Notification_Settings`
--

INSERT INTO `Notification_Settings` (`setting_key`, `is_enabled`, `recipients`, `created_at`, `updated_at`, `value_int`) VALUES
('project_assigned_to_clan', 1, NULL, '2025-08-13 06:35:38', '2025-08-13 06:40:36', NULL),
('task_due_soon', 0, NULL, '2025-08-13 06:35:38', '2025-08-13 06:55:50', NULL),
('task_due_soon_1', 1, NULL, '2025-08-13 06:55:50', '2025-08-13 06:55:50', 5),
('task_due_soon_2', 1, NULL, '2025-08-13 06:55:50', '2025-08-13 06:55:50', 3),
('task_due_soon_3', 1, NULL, '2025-08-13 06:55:50', '2025-08-13 06:55:50', 1),
('task_overdue', 1, NULL, '2025-08-13 06:35:38', '2025-08-13 06:35:38', NULL);

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
  `task_distribution_mode` enum('automatic','percentage') DEFAULT 'automatic',
  `time_limit` date DEFAULT NULL,
  `is_personal` tinyint(1) DEFAULT 0 COMMENT '1 si es proyecto personal, 0 si es proyecto normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `Projects`
--

INSERT INTO `Projects` (`project_id`, `project_name`, `description`, `clan_id`, `created_by_user_id`, `status`, `total_tasks`, `completed_tasks`, `progress_percentage`, `created_at`, `updated_at`, `kpi_quarter_id`, `kpi_points`, `task_distribution_mode`, `time_limit`, `is_personal`) VALUES
(24, 'RinoPlugin', 'Plugin de control de accesos de portales', 5, 58, 'open', 4, 4, 100.00, '2025-08-13 16:29:25', '2025-08-13 23:49:09', 11, 50, 'automatic', '2025-09-30', 0),
(25, 'Resegmentación', 'Utilidad fuera de linea, de consulta, comparación y resegmentación de prima y negocios', 5, 58, 'open', 5, 5, 200.00, '2025-08-13 16:30:52', '2025-08-19 15:07:25', 11, 50, 'automatic', '2025-09-30', 0),
(26, 'Nuevos Indicadores / procesos automaticos', 'Indicadores necesarios para procesos y sistemas', 5, 58, 'open', 0, 0, 0.00, '2025-08-13 16:31:33', '2025-08-13 23:49:26', 11, 350, 'automatic', '2025-09-30', 0),
(29, 'Metas promotoria etapa 1', 'Seguimiento al proyecto, consulta de promotoria.', 5, 1, 'open', 3, 0, 0.00, '2025-08-13 17:35:38', '2025-08-20 22:31:23', 11, 300, 'automatic', '2025-09-30', 0),
(34, 'Tareas Recurrentes', 'Tareas Recurrentes', 13, 1, 'open', 10, 1, 0.00, '2025-08-14 05:48:31', '2025-08-19 20:28:02', NULL, 0, 'automatic', NULL, 0),
(35, 'Tareas Eventuales', 'Tareas Eventuales', 13, 1, 'open', 2, 2, 0.00, '2025-08-14 05:48:31', '2025-08-19 20:29:15', NULL, 0, 'automatic', NULL, 0),
(42, 'Tareas Personales', 'Proyecto personal para tareas individuales del usuario', 5, 2, 'active', 0, 0, 0.00, '2025-08-19 20:29:39', '2025-08-19 20:29:39', NULL, 0, 'automatic', NULL, 1),
(43, 'Tareas Personales', 'Proyecto personal para tareas individuales del usuario', 5, 4, 'active', 0, 0, 0.00, '2025-08-20 15:09:12', '2025-08-20 15:09:12', NULL, 0, 'automatic', NULL, 1),
(44, 'AGENCIAS RINO', 'Proyeccion de Crecimiento 2026', 12, 22, 'open', 1, 0, 0.00, '2025-08-20 18:09:56', '2025-08-20 18:13:55', NULL, 0, 'automatic', NULL, 0);

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

--
-- Volcado de datos para la tabla `remember_tokens`
--

INSERT INTO `remember_tokens` (`user_id`, `token`, `expires_at`, `created_at`) VALUES
(58, 'b551da8952042d7c51f66a4a425042bdab49fb45ab2a038129d26ee6c8f8f853', '2025-09-18 17:34:07', '2025-08-19 17:34:07');

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
-- Volcado de datos para la tabla `Subtasks`
--

INSERT INTO `Subtasks` (`subtask_id`, `task_id`, `title`, `description`, `completion_percentage`, `estimated_hours`, `actual_hours`, `status`, `priority`, `due_date`, `assigned_to_user_id`, `created_by_user_id`, `subtask_order`, `created_at`, `updated_at`) VALUES
(1, 242, 'Prueba1', '', 0.00, NULL, NULL, 'pending', 'medium', NULL, NULL, 1, 1, '2025-08-20 18:35:45', '2025-08-20 18:35:45'),
(2, 242, 'Prueba2', '', 0.00, NULL, NULL, 'pending', 'medium', NULL, NULL, 1, 2, '2025-08-20 18:35:45', '2025-08-20 18:35:45'),
(3, 242, 'Prueba3', '', 0.00, NULL, NULL, 'pending', 'medium', NULL, NULL, 1, 3, '2025-08-20 18:35:45', '2025-08-20 18:35:45'),
(4, 242, 'Prueba4', '', 0.00, NULL, NULL, 'pending', 'medium', NULL, NULL, 1, 4, '2025-08-20 18:35:45', '2025-08-20 18:35:45'),
(5, 243, 'TT2', '', 100.00, NULL, NULL, 'completed', 'medium', NULL, NULL, 1, 1, '2025-08-20 18:49:30', '2025-08-20 19:38:21'),
(6, 243, 'TT3', '', 50.00, NULL, NULL, 'in_progress', 'medium', NULL, NULL, 1, 2, '2025-08-20 18:49:30', '2025-08-20 19:38:14'),
(7, 243, 'TT4', '', 0.00, NULL, NULL, 'pending', 'medium', NULL, NULL, 1, 3, '2025-08-20 18:49:30', '2025-08-20 18:49:30'),
(8, 244, 'lol1', '', 0.00, NULL, NULL, 'pending', 'medium', NULL, NULL, 1, 1, '2025-08-20 18:57:06', '2025-08-20 18:57:06'),
(9, 244, 'lol2', '', 0.00, NULL, NULL, 'pending', 'medium', NULL, NULL, 1, 2, '2025-08-20 18:57:06', '2025-08-20 18:57:06'),
(10, 245, 'rr1', '', 0.00, NULL, NULL, 'pending', 'medium', NULL, NULL, 1, 1, '2025-08-20 19:01:10', '2025-08-20 19:01:10'),
(11, 245, 'rr2', '', 0.00, NULL, NULL, 'pending', 'medium', NULL, NULL, 1, 2, '2025-08-20 19:01:10', '2025-08-20 19:01:10'),
(12, 245, 'rr3', '', 0.00, NULL, NULL, 'pending', 'medium', NULL, NULL, 1, 3, '2025-08-20 19:01:10', '2025-08-20 19:01:10'),
(13, 246, 'ww1', '', 100.00, NULL, NULL, 'completed', 'medium', NULL, NULL, 1, 1, '2025-08-20 19:39:59', '2025-08-20 20:01:19'),
(14, 246, 'ww2', '', 50.00, NULL, NULL, 'in_progress', 'medium', NULL, NULL, 1, 2, '2025-08-20 19:39:59', '2025-08-20 20:20:40'),
(15, 256, 'erer432', 'dffds23424', 0.00, NULL, NULL, 'pending', 'medium', NULL, NULL, 2, 1, '2025-08-20 22:24:25', '2025-08-20 22:24:25'),
(16, 256, 'erer432', 'dffds23424', 0.00, NULL, NULL, 'pending', 'medium', NULL, NULL, 2, 2, '2025-08-20 22:24:25', '2025-08-20 22:24:25'),
(17, 264, 'ggg', '', 50.00, NULL, NULL, 'in_progress', 'medium', NULL, NULL, 2, 1, '2025-08-20 23:24:46', '2025-08-20 23:25:06'),
(18, 264, 'ggg', '', 0.00, NULL, NULL, 'pending', 'medium', NULL, NULL, 2, 2, '2025-08-20 23:24:46', '2025-08-20 23:24:46'),
(19, 253, 'Plan de Capacitacion Director Agencia', '', 0.00, NULL, NULL, 'pending', 'medium', NULL, NULL, 1, 1, '2025-08-20 23:28:48', '2025-08-20 23:28:48');

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
-- Estructura de tabla para la tabla `Subtasks_backup`
--

CREATE TABLE `Subtasks_backup` (
  `subtask_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `completion_percentage` decimal(5,2) DEFAULT 0.00,
  `estimated_hours` decimal(5,2) DEFAULT NULL,
  `actual_hours` decimal(5,2) DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'pending',
  `priority` enum('low','medium','high','urgent') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'medium',
  `due_date` date DEFAULT NULL,
  `assigned_to_user_id` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) NOT NULL,
  `subtask_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Tasks`
--

CREATE TABLE `Tasks` (
  `task_id` int(11) NOT NULL,
  `parent_task_id` int(11) DEFAULT NULL,
  `task_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL COMMENT 'NULL para tareas personales sin proyecto',
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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_personal` tinyint(1) DEFAULT 0 COMMENT '1 si es tarea personal, 0 si es tarea normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Tasks`
--

INSERT INTO `Tasks` (`task_id`, `parent_task_id`, `task_name`, `description`, `project_id`, `assigned_to_user_id`, `created_by_user_id`, `priority`, `due_date`, `estimated_hours`, `actual_hours`, `completion_percentage`, `automatic_points`, `assigned_percentage`, `color_tag`, `is_subtask`, `subtask_order`, `status`, `is_completed`, `completed_at`, `created_at`, `updated_at`, `is_personal`) VALUES
(240, NULL, 'METAS Y OBJETIVOS', 'El objetivo de esta tarea es establecer los objetivos que se tendran que plantear para cada direccion de agencia, los reportes que deberán llevar los directores y los reportes que se les presentaran desde el area de estadísticas.', 44, 22, 22, 'medium', '2025-10-31', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-20 18:12:58', '2025-08-20 18:34:49', 0),
(242, NULL, 'QA', 'Pruebas funcionales.', 29, 2, 1, 'medium', '2025-08-22', NULL, NULL, 0.00, 100.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-20 18:35:45', '2025-08-20 22:31:23', 0),
(243, NULL, 'Q2', 'Q2', 29, 2, 1, 'medium', NULL, NULL, NULL, 50.00, 100.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-20 18:49:30', '2025-08-20 22:31:23', 0),
(248, NULL, 'Tarea con subs en clan', 'fdfsfsad', 29, 4, 2, 'medium', '2025-08-20', NULL, NULL, 0.00, 100.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-20 20:21:48', '2025-08-20 22:31:23', 0),
(253, NULL, 'CAPACITACION Y RRHH', 'El objetivo es contar un plan de capacitacion y conocimientos para cada uno de los colaboradores que pertenezcan a la agencia.', 44, 22, 22, 'medium', '2025-10-31', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-20 20:49:33', '2025-08-20 20:49:33', 0),
(258, NULL, 'Tarea de clan 33', 'Esto es un ejemplo de tarea de clan', 29, 4, 2, 'medium', '2025-08-20', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-20 22:32:33', '2025-08-20 22:32:33', 0),
(259, NULL, 'Tarea de prueba332', '', 29, 4, 2, 'medium', '2025-08-20', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-20 22:34:02', '2025-08-20 22:34:02', 0),
(260, NULL, 'xq2', 'fsasfasdf', 29, 4, 2, 'medium', '2025-08-20', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-20 22:39:40', '2025-08-20 22:39:40', 0),
(261, NULL, 'wer', 'ewsdfdsf', 29, 4, 2, 'medium', '2025-08-20', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-20 23:04:16', '2025-08-20 23:04:16', 0),
(262, NULL, 'vcvcvcvcv', 'vfdvdfsvdfs', 29, 4, 2, 'medium', '2025-08-20', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-20 23:09:06', '2025-08-20 23:09:06', 0),
(263, NULL, 'g3w4gerger', 'sdafsadfsadfasd', 29, 4, 2, 'medium', '2025-08-20', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-20 23:14:32', '2025-08-20 23:14:32', 0),
(264, NULL, 'rrrere', 'dsfaerw', 29, 4, 2, 'medium', '2025-08-20', NULL, NULL, 25.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-20 23:24:46', '2025-08-20 23:25:06', 0);

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
(80, 42, 11, 50.00, '2025-08-05 22:20:26', 2, 'assigned', NULL),
(81, 42, 10, 50.00, '2025-08-05 22:20:26', 2, 'assigned', NULL),
(82, 43, 2, 100.00, '2025-08-05 22:21:59', 2, 'assigned', NULL),
(83, 44, 2, 100.00, '2025-08-05 22:22:38', 2, 'assigned', NULL),
(84, 53, 2, 33.33, '2025-08-11 23:01:44', 2, 'assigned', NULL),
(85, 53, 4, 33.33, '2025-08-11 23:01:44', 2, 'assigned', NULL),
(86, 53, 6, 33.33, '2025-08-11 23:01:44', 2, 'assigned', NULL),
(87, 54, 2, 10.00, '2025-08-11 23:33:14', 2, 'assigned', NULL),
(88, 54, 4, 10.00, '2025-08-11 23:33:14', 2, 'assigned', NULL),
(89, 54, 6, 10.00, '2025-08-11 23:33:14', 2, 'assigned', NULL),
(90, 54, 11, 10.00, '2025-08-11 23:33:14', 2, 'assigned', NULL),
(91, 54, 10, 10.00, '2025-08-11 23:33:14', 2, 'assigned', NULL),
(92, 54, 12, 10.00, '2025-08-11 23:33:14', 2, 'assigned', NULL),
(93, 54, 38, 10.00, '2025-08-11 23:33:14', 2, 'assigned', NULL),
(94, 54, 9, 10.00, '2025-08-11 23:33:14', 2, 'assigned', NULL),
(95, 54, 5, 10.00, '2025-08-11 23:33:14', 2, 'assigned', NULL),
(96, 54, 1, 10.00, '2025-08-11 23:33:14', 2, 'assigned', NULL),
(97, 55, 30, 100.00, '2025-08-11 23:38:45', 22, 'assigned', NULL),
(98, 56, 2, 10.00, '2025-08-12 16:13:29', 2, 'assigned', NULL),
(99, 56, 4, 10.00, '2025-08-12 16:13:29', 2, 'assigned', NULL),
(100, 56, 6, 10.00, '2025-08-12 16:13:29', 2, 'assigned', NULL),
(101, 56, 11, 10.00, '2025-08-12 16:13:29', 2, 'assigned', NULL),
(102, 56, 10, 10.00, '2025-08-12 16:13:29', 2, 'assigned', NULL),
(103, 56, 12, 10.00, '2025-08-12 16:13:29', 2, 'assigned', NULL),
(104, 56, 38, 10.00, '2025-08-12 16:13:29', 2, 'assigned', NULL),
(105, 56, 9, 10.00, '2025-08-12 16:13:29', 2, 'assigned', NULL),
(106, 56, 5, 10.00, '2025-08-12 16:13:29', 2, 'assigned', NULL),
(127, 61, 35, 100.00, '2025-08-12 22:18:01', 2, 'assigned', NULL),
(128, 63, 2, 100.00, '2025-08-13 06:53:56', 2, 'assigned', NULL),
(129, 64, 2, 100.00, '2025-08-13 06:54:20', 2, 'assigned', NULL),
(130, 65, 2, 25.00, '2025-08-13 07:05:56', 1, 'assigned', NULL),
(131, 65, 4, 25.00, '2025-08-13 07:05:56', 1, 'assigned', NULL),
(132, 65, 6, 25.00, '2025-08-13 07:05:56', 1, 'assigned', NULL),
(133, 65, 11, 25.00, '2025-08-13 07:05:56', 1, 'assigned', NULL),
(134, 66, 2, 25.00, '2025-08-13 07:09:20', 1, 'assigned', NULL),
(135, 66, 4, 25.00, '2025-08-13 07:09:20', 1, 'assigned', NULL),
(136, 66, 6, 25.00, '2025-08-13 07:09:20', 1, 'assigned', NULL),
(137, 66, 11, 25.00, '2025-08-13 07:09:20', 1, 'assigned', NULL),
(138, 67, 2, 33.33, '2025-08-13 07:10:26', 1, 'assigned', NULL),
(139, 67, 9, 33.33, '2025-08-13 07:10:26', 1, 'assigned', NULL),
(140, 67, 5, 33.33, '2025-08-13 07:10:26', 1, 'assigned', NULL),
(141, 68, 2, 50.00, '2025-08-13 07:12:26', 1, 'assigned', NULL),
(142, 68, 4, 50.00, '2025-08-13 07:12:26', 1, 'assigned', NULL),
(143, 69, 2, 50.00, '2025-08-13 07:16:00', 1, 'assigned', NULL),
(144, 69, 4, 50.00, '2025-08-13 07:16:00', 1, 'assigned', NULL),
(145, 70, 2, 100.00, '2025-08-13 07:21:09', 1, 'assigned', NULL),
(146, 71, 2, 100.00, '2025-08-13 07:25:36', 1, 'assigned', NULL),
(147, 72, 2, 100.00, '2025-08-13 07:27:53', 1, 'assigned', NULL),
(148, 73, 2, 100.00, '2025-08-13 07:31:58', 1, 'assigned', NULL),
(149, 74, 2, 100.00, '2025-08-13 07:35:47', 1, 'assigned', NULL),
(150, 75, 2, 16.67, '2025-08-13 07:36:15', 1, 'assigned', NULL),
(151, 75, 4, 16.67, '2025-08-13 07:36:15', 1, 'assigned', NULL),
(152, 75, 6, 16.67, '2025-08-13 07:36:15', 1, 'assigned', NULL),
(153, 75, 11, 16.67, '2025-08-13 07:36:15', 1, 'assigned', NULL),
(154, 75, 10, 16.67, '2025-08-13 07:36:15', 1, 'assigned', NULL),
(155, 75, 12, 16.67, '2025-08-13 07:36:15', 1, 'assigned', NULL),
(156, 76, 2, 33.33, '2025-08-13 07:37:07', 1, 'assigned', NULL),
(157, 76, 4, 33.33, '2025-08-13 07:37:07', 1, 'assigned', NULL),
(158, 76, 11, 33.33, '2025-08-13 07:37:07', 1, 'assigned', NULL),
(159, 77, 2, 100.00, '2025-08-13 07:38:38', 1, 'assigned', NULL),
(160, 78, 2, 100.00, '2025-08-13 07:46:17', 1, 'assigned', NULL),
(161, 79, 2, 100.00, '2025-08-13 07:50:01', 1, 'assigned', NULL),
(162, 80, 2, 33.33, '2025-08-13 15:12:28', 1, 'assigned', NULL),
(163, 80, 4, 33.33, '2025-08-13 15:12:28', 1, 'assigned', NULL),
(164, 80, 11, 33.33, '2025-08-13 15:12:28', 1, 'assigned', NULL),
(165, 81, 2, 100.00, '2025-08-13 16:37:37', 2, 'assigned', NULL),
(166, 82, 4, 25.00, '2025-08-13 16:38:14', 2, 'assigned', NULL),
(167, 82, 11, 25.00, '2025-08-13 16:38:14', 2, 'assigned', NULL),
(168, 82, 10, 25.00, '2025-08-13 16:38:14', 2, 'assigned', NULL),
(169, 82, 12, 25.00, '2025-08-13 16:38:14', 2, 'assigned', NULL),
(170, 83, 4, 25.00, '2025-08-13 16:38:19', 2, 'assigned', NULL),
(171, 83, 11, 25.00, '2025-08-13 16:38:19', 2, 'assigned', NULL),
(172, 83, 10, 25.00, '2025-08-13 16:38:19', 2, 'assigned', NULL),
(173, 83, 12, 25.00, '2025-08-13 16:38:19', 2, 'assigned', NULL),
(174, 84, 2, 100.00, '2025-08-13 16:39:20', 2, 'assigned', NULL),
(175, 85, 2, 10.00, '2025-08-13 16:46:09', 1, 'assigned', NULL),
(176, 85, 4, 10.00, '2025-08-13 16:46:09', 1, 'assigned', NULL),
(177, 85, 6, 10.00, '2025-08-13 16:46:09', 1, 'assigned', NULL),
(178, 85, 11, 10.00, '2025-08-13 16:46:09', 1, 'assigned', NULL),
(179, 85, 10, 10.00, '2025-08-13 16:46:09', 1, 'assigned', NULL),
(180, 85, 12, 10.00, '2025-08-13 16:46:09', 1, 'assigned', NULL),
(181, 85, 38, 10.00, '2025-08-13 16:46:09', 1, 'assigned', NULL),
(182, 85, 9, 10.00, '2025-08-13 16:46:09', 1, 'assigned', NULL),
(183, 85, 5, 10.00, '2025-08-13 16:46:09', 1, 'assigned', NULL),
(184, 85, 1, 10.00, '2025-08-13 16:46:09', 1, 'assigned', NULL),
(185, 86, 9, 50.00, '2025-08-13 17:01:57', 2, 'assigned', NULL),
(186, 86, 5, 50.00, '2025-08-13 17:01:57', 2, 'assigned', NULL),
(187, 87, 9, 50.00, '2025-08-13 17:02:24', 2, 'assigned', NULL),
(188, 87, 5, 50.00, '2025-08-13 17:02:24', 2, 'assigned', NULL),
(189, 88, 9, 50.00, '2025-08-13 17:02:54', 2, 'assigned', NULL),
(190, 88, 5, 50.00, '2025-08-13 17:02:54', 2, 'assigned', NULL),
(191, 89, 2, 50.00, '2025-08-13 18:33:59', 2, 'assigned', NULL),
(192, 89, 4, 50.00, '2025-08-13 18:33:59', 2, 'assigned', NULL),
(194, 92, 2, 100.00, '2025-08-14 05:50:08', 1, 'assigned', NULL),
(195, 93, 4, 100.00, '2025-08-14 05:51:24', 1, 'assigned', NULL),
(196, 94, 2, 100.00, '2025-08-14 06:11:27', 1, 'assigned', NULL),
(197, 95, 4, 100.00, '2025-08-14 06:12:08', 1, 'assigned', NULL),
(198, 96, 2, 50.00, '2025-08-14 16:28:31', 1, 'assigned', NULL),
(199, 96, 4, 50.00, '2025-08-14 16:28:31', 1, 'assigned', NULL),
(200, 97, 2, 100.00, '2025-08-14 16:39:48', 1, 'assigned', NULL),
(201, 98, 2, 100.00, '2025-08-14 16:39:48', 1, 'assigned', NULL),
(202, 99, 2, 100.00, '2025-08-14 16:39:48', 1, 'assigned', NULL),
(203, 100, 2, 100.00, '2025-08-14 16:39:48', 1, 'assigned', NULL),
(204, 101, 2, 100.00, '2025-08-14 16:39:48', 1, 'assigned', NULL),
(205, 102, 2, 100.00, '2025-08-14 16:39:48', 1, 'assigned', NULL),
(206, 103, 2, 100.00, '2025-08-14 16:39:48', 1, 'assigned', NULL),
(207, 104, 2, 100.00, '2025-08-14 16:45:33', 1, 'assigned', NULL),
(208, 105, 2, 100.00, '2025-08-14 16:47:11', 1, 'assigned', NULL),
(209, 106, 2, 100.00, '2025-08-14 16:47:11', 1, 'assigned', NULL),
(210, 107, 2, 100.00, '2025-08-14 16:47:11', 1, 'assigned', NULL),
(211, 108, 2, 100.00, '2025-08-14 16:47:11', 1, 'assigned', NULL),
(212, 109, 2, 100.00, '2025-08-14 16:47:11', 1, 'assigned', NULL),
(213, 110, 2, 100.00, '2025-08-14 16:47:11', 1, 'assigned', NULL),
(214, 111, 4, 100.00, '2025-08-14 18:31:30', 2, 'assigned', NULL),
(215, 112, 2, 50.00, '2025-08-14 19:22:01', 1, 'assigned', NULL),
(216, 112, 4, 50.00, '2025-08-14 19:22:01', 1, 'assigned', NULL),
(217, 113, 2, 100.00, '2025-08-14 19:31:12', 1, 'assigned', NULL),
(218, 114, 2, 100.00, '2025-08-14 19:31:12', 1, 'assigned', NULL),
(219, 115, 2, 100.00, '2025-08-14 19:31:12', 1, 'assigned', NULL),
(220, 116, 2, 100.00, '2025-08-14 19:31:12', 1, 'assigned', NULL),
(221, 117, 2, 100.00, '2025-08-14 19:31:12', 1, 'assigned', NULL),
(222, 118, 2, 100.00, '2025-08-14 19:31:12', 1, 'assigned', NULL),
(223, 119, 2, 100.00, '2025-08-14 19:31:12', 1, 'assigned', NULL),
(224, 120, 4, 50.00, '2025-08-14 20:12:17', 2, 'assigned', NULL),
(225, 120, 6, 50.00, '2025-08-14 20:12:17', 2, 'assigned', NULL),
(226, 129, 4, 100.00, '2025-08-18 21:22:49', 1, 'assigned', NULL),
(227, 130, 4, 100.00, '2025-08-18 21:24:03', 1, 'assigned', NULL),
(228, 131, 4, 100.00, '2025-08-18 21:57:53', 1, 'assigned', NULL),
(229, 132, 4, 100.00, '2025-08-18 21:57:53', 1, 'assigned', NULL),
(230, 133, 4, 100.00, '2025-08-18 21:57:53', 1, 'assigned', NULL),
(231, 134, 4, 100.00, '2025-08-18 21:57:53', 1, 'assigned', NULL),
(232, 135, 4, 100.00, '2025-08-18 21:57:53', 1, 'assigned', NULL),
(233, 136, 4, 100.00, '2025-08-18 21:57:53', 1, 'assigned', NULL),
(234, 137, 4, 100.00, '2025-08-18 21:57:53', 1, 'assigned', NULL),
(235, 138, 4, 100.00, '2025-08-18 22:05:21', 1, 'assigned', NULL),
(236, 139, 4, 100.00, '2025-08-18 22:05:21', 1, 'assigned', NULL),
(237, 140, 4, 100.00, '2025-08-18 22:05:21', 1, 'assigned', NULL),
(238, 141, 4, 100.00, '2025-08-18 22:05:21', 1, 'assigned', NULL),
(239, 142, 4, 100.00, '2025-08-18 22:05:21', 1, 'assigned', NULL),
(240, 143, 4, 100.00, '2025-08-18 22:05:21', 1, 'assigned', NULL),
(241, 144, 4, 100.00, '2025-08-18 22:05:21', 1, 'assigned', NULL),
(242, 145, 4, 100.00, '2025-08-18 22:11:05', 1, 'assigned', NULL),
(243, 146, 4, 100.00, '2025-08-18 22:11:05', 1, 'assigned', NULL),
(244, 147, 4, 100.00, '2025-08-18 22:11:05', 1, 'assigned', NULL),
(245, 148, 4, 100.00, '2025-08-18 22:11:05', 1, 'assigned', NULL),
(246, 149, 4, 100.00, '2025-08-18 22:11:05', 1, 'assigned', NULL),
(247, 150, 4, 100.00, '2025-08-18 22:11:05', 1, 'assigned', NULL),
(248, 151, 4, 100.00, '2025-08-18 22:11:05', 1, 'assigned', NULL),
(249, 152, 4, 100.00, '2025-08-18 22:11:27', 1, 'assigned', NULL),
(250, 153, 4, 100.00, '2025-08-18 22:18:10', 1, 'assigned', NULL),
(251, 154, 4, 100.00, '2025-08-18 22:18:10', 1, 'assigned', NULL),
(252, 155, 4, 100.00, '2025-08-18 22:18:10', 1, 'assigned', NULL),
(253, 156, 4, 100.00, '2025-08-18 22:18:10', 1, 'assigned', NULL),
(254, 157, 4, 100.00, '2025-08-18 22:18:10', 1, 'assigned', NULL),
(255, 158, 4, 100.00, '2025-08-18 22:18:10', 1, 'assigned', NULL),
(256, 159, 4, 100.00, '2025-08-18 22:18:10', 1, 'assigned', NULL),
(257, 164, 4, 100.00, '2025-08-18 22:30:26', 1, 'assigned', NULL),
(258, 165, 4, 100.00, '2025-08-18 22:31:40', 1, 'assigned', NULL),
(259, 166, 4, 100.00, '2025-08-18 22:39:48', 2, 'assigned', NULL),
(260, 167, 2, 100.00, '2025-08-18 23:31:29', 1, 'assigned', NULL),
(261, 168, 2, 100.00, '2025-08-18 23:31:29', 1, 'assigned', NULL),
(262, 169, 2, 100.00, '2025-08-18 23:31:29', 1, 'assigned', NULL),
(263, 170, 2, 100.00, '2025-08-18 23:31:29', 1, 'assigned', NULL),
(264, 171, 2, 100.00, '2025-08-18 23:31:29', 1, 'assigned', NULL),
(265, 172, 2, 100.00, '2025-08-18 23:31:29', 1, 'assigned', NULL),
(266, 173, 2, 100.00, '2025-08-18 23:31:29', 1, 'assigned', NULL),
(267, 174, 2, 100.00, '2025-08-19 15:08:22', 2, 'assigned', NULL),
(268, 175, 2, 100.00, '2025-08-19 15:09:29', 1, 'assigned', NULL),
(269, 176, 2, 100.00, '2025-08-19 15:10:17', 1, 'assigned', NULL),
(270, 177, 2, 100.00, '2025-08-19 15:10:17', 1, 'assigned', NULL),
(271, 178, 2, 100.00, '2025-08-19 15:10:17', 1, 'assigned', NULL),
(272, 179, 2, 100.00, '2025-08-19 15:10:17', 1, 'assigned', NULL),
(273, 180, 2, 100.00, '2025-08-19 15:10:17', 1, 'assigned', NULL),
(274, 181, 2, 100.00, '2025-08-19 15:10:17', 1, 'assigned', NULL),
(275, 182, 2, 100.00, '2025-08-19 15:15:24', 2, 'assigned', NULL),
(276, 185, 2, 100.00, '2025-08-19 16:50:43', 2, 'assigned', NULL),
(277, 186, 2, 100.00, '2025-08-19 16:52:28', 1, 'assigned', NULL),
(278, 187, 2, 100.00, '2025-08-19 16:52:28', 1, 'assigned', NULL),
(279, 188, 2, 100.00, '2025-08-19 16:52:28', 1, 'assigned', NULL),
(280, 189, 2, 100.00, '2025-08-19 16:52:28', 1, 'assigned', NULL),
(281, 190, 2, 100.00, '2025-08-19 16:52:28', 1, 'assigned', NULL),
(282, 191, 2, 100.00, '2025-08-19 16:52:28', 1, 'assigned', NULL),
(283, 192, 2, 100.00, '2025-08-19 17:22:14', 1, 'assigned', NULL),
(284, 193, 2, 100.00, '2025-08-19 17:22:14', 1, 'assigned', NULL),
(285, 194, 2, 100.00, '2025-08-19 17:22:14', 1, 'assigned', NULL),
(286, 195, 2, 100.00, '2025-08-19 17:22:14', 1, 'assigned', NULL),
(287, 196, 2, 100.00, '2025-08-19 17:22:14', 1, 'assigned', NULL),
(288, 197, 2, 100.00, '2025-08-19 17:22:14', 1, 'assigned', NULL),
(289, 198, 2, 100.00, '2025-08-19 17:23:33', 58, 'assigned', NULL),
(290, 199, 2, 100.00, '2025-08-19 17:50:11', 1, 'assigned', NULL),
(291, 200, 2, 100.00, '2025-08-19 17:50:11', 1, 'assigned', NULL),
(292, 201, 2, 100.00, '2025-08-19 17:50:11', 1, 'assigned', NULL),
(293, 202, 2, 100.00, '2025-08-19 17:50:11', 1, 'assigned', NULL),
(294, 203, 2, 100.00, '2025-08-19 17:50:11', 1, 'assigned', NULL),
(295, 204, 2, 100.00, '2025-08-19 17:50:11', 1, 'assigned', NULL),
(296, 205, 2, 50.00, '2025-08-19 18:27:11', 2, 'assigned', NULL),
(297, 205, 4, 50.00, '2025-08-19 18:27:11', 2, 'assigned', NULL),
(298, 206, 2, 100.00, '2025-08-19 18:30:26', 1, 'assigned', NULL),
(299, 207, 2, 100.00, '2025-08-19 18:30:26', 1, 'assigned', NULL),
(300, 208, 2, 100.00, '2025-08-19 18:30:26', 1, 'assigned', NULL),
(301, 209, 2, 100.00, '2025-08-19 18:30:26', 1, 'assigned', NULL),
(302, 210, 2, 100.00, '2025-08-19 18:30:26', 1, 'assigned', NULL),
(303, 211, 2, 100.00, '2025-08-19 18:30:26', 1, 'assigned', NULL),
(304, 212, 2, 100.00, '2025-08-19 20:20:26', 1, 'assigned', NULL),
(305, 213, 2, 100.00, '2025-08-19 20:20:26', 1, 'assigned', NULL),
(306, 214, 2, 100.00, '2025-08-19 20:20:26', 1, 'assigned', NULL),
(307, 215, 2, 100.00, '2025-08-19 20:20:26', 1, 'assigned', NULL),
(308, 216, 2, 100.00, '2025-08-19 20:20:26', 1, 'assigned', NULL),
(309, 217, 2, 100.00, '2025-08-19 20:20:26', 1, 'assigned', NULL),
(310, 218, 2, 100.00, '2025-08-19 20:20:26', 1, 'assigned', NULL),
(311, 219, 2, 100.00, '2025-08-19 20:21:49', 1, 'assigned', NULL),
(312, 220, 4, 100.00, '2025-08-19 20:22:48', 1, 'assigned', NULL),
(313, 221, 4, 100.00, '2025-08-19 20:22:48', 1, 'assigned', NULL),
(314, 222, 4, 100.00, '2025-08-19 20:22:48', 1, 'assigned', NULL),
(315, 223, 4, 100.00, '2025-08-19 20:25:11', 1, 'assigned', NULL),
(316, 227, 2, 100.00, '2025-08-20 15:36:52', 1, 'assigned', NULL),
(317, 228, 2, 100.00, '2025-08-20 15:36:52', 1, 'assigned', NULL),
(318, 229, 2, 100.00, '2025-08-20 15:36:52', 1, 'assigned', NULL),
(319, 230, 2, 100.00, '2025-08-20 15:36:52', 1, 'assigned', NULL),
(320, 231, 2, 100.00, '2025-08-20 15:36:52', 1, 'assigned', NULL),
(321, 232, 2, 100.00, '2025-08-20 15:36:52', 1, 'assigned', NULL),
(322, 234, 4, 100.00, '2025-08-20 15:40:35', 1, 'assigned', NULL),
(323, 235, 4, 100.00, '2025-08-20 15:40:35', 1, 'assigned', NULL),
(324, 236, 4, 100.00, '2025-08-20 15:40:35', 1, 'assigned', NULL),
(325, 237, 4, 100.00, '2025-08-20 15:40:35', 1, 'assigned', NULL),
(326, 238, 4, 100.00, '2025-08-20 15:40:35', 1, 'assigned', NULL),
(327, 239, 4, 100.00, '2025-08-20 15:40:35', 1, 'assigned', NULL),
(328, 240, 24, 50.00, '2025-08-20 18:12:58', 22, 'assigned', NULL),
(329, 240, 27, 50.00, '2025-08-20 18:12:58', 22, 'assigned', NULL),
(331, 242, 2, 100.00, '2025-08-20 18:35:45', 1, 'assigned', NULL),
(332, 243, 2, 100.00, '2025-08-20 18:49:30', 1, 'assigned', NULL),
(333, 244, 2, 100.00, '2025-08-20 18:56:36', 1, 'assigned', NULL),
(334, 245, 2, 100.00, '2025-08-20 19:00:47', 1, 'assigned', NULL),
(335, 246, 4, 100.00, '2025-08-20 19:39:59', 1, 'assigned', NULL),
(336, 247, 4, 100.00, '2025-08-20 20:11:33', 2, 'assigned', NULL),
(337, 248, 4, 100.00, '2025-08-20 20:21:48', 2, 'assigned', NULL),
(338, 249, 4, 100.00, '2025-08-20 20:27:53', 2, 'assigned', NULL),
(339, 250, 4, 100.00, '2025-08-20 20:32:16', 2, 'assigned', NULL),
(340, 251, 4, 100.00, '2025-08-20 20:43:13', 2, 'assigned', NULL),
(341, 252, 4, 100.00, '2025-08-20 20:44:13', 2, 'assigned', NULL),
(342, 253, 22, 100.00, '2025-08-20 20:49:33', 22, 'assigned', NULL),
(343, 254, 4, 100.00, '2025-08-20 20:49:46', 2, 'assigned', NULL),
(344, 255, 4, 100.00, '2025-08-20 22:18:45', 2, 'assigned', NULL),
(345, 256, 4, 100.00, '2025-08-20 22:24:25', 2, 'assigned', NULL),
(346, 257, 4, 100.00, '2025-08-20 22:26:36', 2, 'assigned', NULL),
(347, 258, 4, 100.00, '2025-08-20 22:32:33', 2, 'assigned', NULL),
(348, 259, 4, 100.00, '2025-08-20 22:34:02', 2, 'assigned', NULL),
(349, 260, 4, 100.00, '2025-08-20 22:39:40', 2, 'assigned', NULL),
(350, 261, 4, 100.00, '2025-08-20 23:04:16', 2, 'assigned', NULL),
(351, 262, 4, 100.00, '2025-08-20 23:09:06', 2, 'assigned', NULL),
(352, 263, 4, 100.00, '2025-08-20 23:14:32', 2, 'assigned', NULL),
(353, 264, 4, 100.00, '2025-08-20 23:24:46', 2, 'assigned', NULL);

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
(3, 51, 2, 11, '10. CVTomasMiguelRodriguezMoreno.pdf', 'uploads/task_attachments/att_689a6b891d5aa.pdf', NULL, 'application/pdf', NULL, '2025-08-11 22:15:37'),
(4, 53, 2, 12, 'ChatGPT Image 11 ago 2025, 12_55_19 p.m..png', 'uploads/task_attachments/att_689a76956c2bd.png', NULL, 'image/png', NULL, '2025-08-11 23:02:45'),
(5, 56, 2, 22, 'ChatGPT Image 11 ago 2025, 12_55_19 p.m..png', 'uploads/task_attachments/att_689b738580c8e.png', NULL, 'image/png', NULL, '2025-08-12 17:01:57'),
(6, 56, 2, 23, 'ChatGPT Image 11 ago 2025, 01_02_12 p.m..png', 'uploads/task_attachments/att_689b74237b27c.png', NULL, 'image/png', NULL, '2025-08-12 17:04:35'),
(7, 56, 2, 24, 'ChatGPT Image 11 ago 2025, 12_55_19 p.m..png', 'uploads/task_attachments/att_689b7443eaa36.png', NULL, 'image/png', NULL, '2025-08-12 17:05:07'),
(8, 56, 2, 25, 'b.pdf', 'uploads/task_attachments/att_689b757071a92.pdf', NULL, 'application/pdf', NULL, '2025-08-12 17:10:08'),
(9, 56, 2, 26, 'b.pdf', 'uploads/task_attachments/att_689b87bb28523.pdf', NULL, 'application/pdf', NULL, '2025-08-12 18:28:11'),
(11, 61, 2, 29, '15. CVDanielManrique.pdf', 'uploads/task_attachments/att_689bbdb202ff5.pdf', NULL, 'application/pdf', NULL, '2025-08-12 22:18:26'),
(12, 89, 2, 32, 'image.png', 'uploads/task_attachments/att_689ceb3a83ffd.png', NULL, 'image/png', NULL, '2025-08-13 19:44:58'),
(13, 89, 4, 34, 'image-56.png', 'uploads/task_attachments/att_689ced14c4e3a.png', NULL, 'image/png', NULL, '2025-08-13 19:52:52'),
(14, 90, 4, 36, 'image-56.png', 'uploads/task_attachments/att_689cf66ff1f65.png', NULL, 'image/png', NULL, '2025-08-13 20:32:47'),
(15, 83, 4, 37, '17. CVJONATHANGARCIA.pdf', 'uploads/task_attachments/att_689d17f3de6fc.pdf', NULL, 'application/pdf', NULL, '2025-08-13 22:55:47'),
(16, 120, 2, 39, 'image-56.png', 'uploads/task_attachments/att_689e435847aa1.png', NULL, 'image/png', NULL, '2025-08-14 20:13:12'),
(17, 120, 2, 40, '17. CVJONATHANGARCIA.pdf', 'uploads/task_attachments/att_689e4379ae36f.pdf', NULL, 'application/pdf', NULL, '2025-08-14 20:13:45'),
(18, 122, 4, 41, 'Captura de pantalla 2025-08-13 a las 10.22.33 a. m..png', 'uploads/task_attachments/att_68a38a8151028.png', NULL, 'image/png', NULL, '2025-08-18 20:18:09'),
(19, 122, 4, 42, 'IMG-20250818-WA0024.jpg', 'uploads/task_attachments/att_68a39237c01b9.jpg', NULL, 'image/jpeg', NULL, '2025-08-18 20:51:03'),
(20, 166, 4, 43, '10. CVGENAROMARTINEZ.pdf', 'uploads/task_attachments/att_68a3ac0143e29.pdf', NULL, 'application/pdf', NULL, '2025-08-18 22:41:05'),
(21, 212, 2, 44, 'Captura de pantalla 2025-08-13 a las 10.22.33 a. m..png', 'uploads/task_attachments/att_68a4deeb2ae25.png', NULL, 'image/png', NULL, '2025-08-19 20:30:35'),
(22, 212, 2, 45, '1755635510184383600976329649813.jpg', 'uploads/task_attachments/att_68a4df49b9b5f.jpg', NULL, 'image/jpeg', NULL, '2025-08-19 20:32:09');

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
(11, 51, 2, 'pdfs', 'comment', NULL, NULL, NULL, 0, '2025-08-11 22:15:37'),
(12, 53, 2, 'Foto', 'comment', NULL, NULL, NULL, 0, '2025-08-11 23:02:45'),
(13, 54, 2, 'asdfasdfasdfa', 'comment', NULL, NULL, NULL, 0, '2025-08-11 23:38:33'),
(14, 54, 2, 'hdfghfghdfg', 'comment', NULL, NULL, NULL, 0, '2025-08-12 15:49:36'),
(15, 54, 2, 'fdasdfasdf', 'comment', NULL, NULL, NULL, 0, '2025-08-12 16:03:10'),
(16, 54, 2, 'Esto es un comentario de prueba', 'comment', NULL, NULL, NULL, 0, '2025-08-12 16:12:23'),
(17, 56, 2, 'Esto es un comentario con imagen', 'comment', NULL, NULL, NULL, 0, '2025-08-12 16:13:55'),
(18, 56, 2, 'Prueba de adjunto.', 'comment', NULL, NULL, NULL, 0, '2025-08-12 16:19:37'),
(19, 56, 2, 'Otra prueba de archi adjunto', 'comment', NULL, NULL, NULL, 0, '2025-08-12 16:29:08'),
(20, 56, 2, 'Otro adjunto.', 'comment', NULL, NULL, NULL, 0, '2025-08-12 16:41:01'),
(21, 56, 2, 'De nuevo adjunto.', 'comment', NULL, NULL, NULL, 0, '2025-08-12 16:50:28'),
(22, 56, 2, 'Comentario de sistema', 'comment', NULL, NULL, NULL, 0, '2025-08-12 17:01:57'),
(23, 56, 2, 'adjuntos', 'comment', NULL, NULL, NULL, 0, '2025-08-12 17:04:35'),
(24, 56, 2, 'dsfsdf', 'comment', NULL, NULL, NULL, 0, '2025-08-12 17:05:07'),
(25, 56, 2, 'asdfasfasd', 'comment', NULL, NULL, NULL, 0, '2025-08-12 17:10:08'),
(26, 56, 2, 'Multiples.', 'comment', NULL, NULL, NULL, 0, '2025-08-12 18:28:11'),
(29, 61, 2, 'Adjunto', 'comment', NULL, NULL, NULL, 0, '2025-08-12 22:18:26'),
(30, 84, 4, 'Librerias recomendadas, QYT', 'comment', NULL, NULL, NULL, 0, '2025-08-13 17:56:29'),
(31, 89, 4, 'Pendiente de analisis', 'comment', NULL, NULL, NULL, 0, '2025-08-13 19:40:05'),
(32, 89, 2, 'En espera', 'comment', NULL, NULL, NULL, 0, '2025-08-13 19:44:58'),
(33, 89, 4, 'Evidencia', 'comment', NULL, NULL, NULL, 0, '2025-08-13 19:48:18'),
(34, 89, 4, 'Adjunto', 'comment', NULL, NULL, NULL, 0, '2025-08-13 19:52:52'),
(35, 90, 4, 'Mejorar la funcionalidad', 'comment', NULL, NULL, NULL, 0, '2025-08-13 20:31:00'),
(36, 90, 4, 'Adjunto codigo', 'comment', NULL, NULL, NULL, 0, '2025-08-13 20:32:47'),
(37, 83, 4, 'se reactivo la tarea por que no cumplio la funcion', 'comment', NULL, NULL, NULL, 0, '2025-08-13 22:55:47'),
(38, 83, 2, 'Adelante', 'comment', NULL, NULL, NULL, 0, '2025-08-13 22:56:45'),
(39, 120, 2, 'REVISEN CODIGO', 'comment', NULL, NULL, NULL, 0, '2025-08-14 20:13:12'),
(40, 120, 2, 'documentacion', 'comment', NULL, NULL, NULL, 0, '2025-08-14 20:13:45'),
(41, 122, 4, 'Comentario de prueba y documentacion', 'comment', NULL, NULL, NULL, 0, '2025-08-18 20:18:09'),
(42, 122, 4, 'Comentario desde el celular', 'comment', NULL, NULL, NULL, 0, '2025-08-18 20:51:03'),
(43, 166, 4, 'Comentario desde el perfil de usuario normal adjunto documento', 'comment', NULL, NULL, NULL, 0, '2025-08-18 22:41:05'),
(44, 212, 2, 'Ya acabe rutina', 'comment', NULL, NULL, NULL, 0, '2025-08-19 20:30:35'),
(45, 212, 2, 'Djdjdj', 'comment', NULL, NULL, NULL, 0, '2025-08-19 20:32:09');

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
(107, 42, 2, 'assigned', 'assigned_users', NULL, '11,10', NULL, 'Múltiples usuarios asignados', '2025-08-05 22:20:26'),
(108, 42, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 22:20:26'),
(109, 43, 2, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-05 22:21:59'),
(110, 43, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 22:21:59'),
(111, 44, 2, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-05 22:22:38'),
(112, 44, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-05 22:22:38'),
(122, 48, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-07 18:07:44'),
(123, 43, 2, 'status_changed', 'status', 'pending', 'in_progress', NULL, 'Estado cambiado de pending a in_progress', '2025-08-07 20:16:05'),
(124, 49, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-07 20:40:50'),
(125, 49, 2, 'status_changed', 'status', 'pending', 'in_progress', NULL, 'Estado cambiado de pending a in_progress', '2025-08-07 20:41:57'),
(128, 51, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-07 22:32:59'),
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
(140, 51, 2, 'commented', 'comment', NULL, 'pdfs', NULL, 'Comentario agregado', '2025-08-11 22:15:37'),
(141, 53, 2, 'assigned', 'assigned_users', NULL, '2,4,6', NULL, 'Múltiples usuarios asignados', '2025-08-11 23:01:44'),
(142, 53, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-11 23:01:44'),
(143, 53, 2, 'commented', 'comment', NULL, 'Foto', NULL, 'Comentario agregado', '2025-08-11 23:02:45'),
(144, 53, 2, 'status_changed', 'status', 'pending', 'in_progress', NULL, 'Estado cambiado de pending a in_progress', '2025-08-11 23:03:11'),
(147, 47, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-11 23:05:43'),
(148, 54, 2, 'assigned', 'assigned_users', NULL, '2,4,6,11,10,12,38,9,5,1', NULL, 'Múltiples usuarios asignados', '2025-08-11 23:33:14'),
(149, 54, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-11 23:33:14'),
(150, 54, 2, 'commented', 'comment', NULL, 'asdfasdfasdfa', NULL, 'Comentario agregado', '2025-08-11 23:38:33'),
(151, 55, 22, 'assigned', 'assigned_users', NULL, '30', NULL, 'Múltiples usuarios asignados', '2025-08-11 23:38:45'),
(152, 55, 22, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-11 23:38:45'),
(153, 54, 2, 'commented', 'comment', NULL, 'hdfghfghdfg', NULL, 'Comentario agregado', '2025-08-12 15:49:36'),
(154, 54, 2, 'commented', 'comment', NULL, 'fdasdfasdf', NULL, 'Comentario agregado', '2025-08-12 16:03:10'),
(155, 54, 2, 'commented', 'comment', NULL, 'Esto es un comentario de prueba', NULL, 'Comentario agregado', '2025-08-12 16:12:23'),
(156, 56, 2, 'assigned', 'assigned_users', NULL, '2,4,6,11,10,12,38,9,5,1', NULL, 'Múltiples usuarios asignados', '2025-08-12 16:13:29'),
(157, 56, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-12 16:13:29'),
(158, 56, 2, 'commented', 'comment', NULL, 'Esto es un comentario con imagen', NULL, 'Comentario agregado', '2025-08-12 16:13:55'),
(159, 56, 2, 'status_changed', 'status', 'pending', 'in_progress', NULL, 'Estado cambiado de pending a in_progress', '2025-08-12 16:14:06'),
(160, 56, 2, 'commented', 'comment', NULL, 'Prueba de adjunto.', NULL, 'Comentario agregado', '2025-08-12 16:19:37'),
(161, 56, 2, 'commented', 'comment', NULL, 'Otra prueba de archi adjunto', NULL, 'Comentario agregado', '2025-08-12 16:29:08'),
(162, 56, 2, 'commented', 'comment', NULL, 'Otro adjunto.', NULL, 'Comentario agregado', '2025-08-12 16:41:01'),
(163, 56, 2, 'commented', 'comment', NULL, 'De nuevo adjunto.', NULL, 'Comentario agregado', '2025-08-12 16:50:28'),
(164, 56, 2, 'status_changed', 'status', 'in_progress', 'pending', NULL, 'Estado cambiado de in_progress a pending', '2025-08-12 17:01:08'),
(165, 56, 2, 'commented', 'comment', NULL, 'Comentario de sistema', NULL, 'Comentario agregado', '2025-08-12 17:01:57'),
(166, 56, 2, 'commented', 'comment', NULL, 'adjuntos', NULL, 'Comentario agregado', '2025-08-12 17:04:35'),
(167, 56, 2, 'commented', 'comment', NULL, 'dsfsdf', NULL, 'Comentario agregado', '2025-08-12 17:05:07'),
(168, 56, 2, 'commented', 'comment', NULL, 'asdfasfasd', NULL, 'Comentario agregado', '2025-08-12 17:10:08'),
(169, 56, 2, 'commented', 'comment', NULL, 'Multiples.', NULL, 'Comentario agregado', '2025-08-12 18:28:11'),
(175, 59, 1, 'status_changed', 'status', 'pending', 'in_progress', NULL, 'Estado cambiado de pending a in_progress', '2025-08-12 21:04:23'),
(176, 58, 1, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-12 21:04:43'),
(181, 61, 2, 'assigned', 'assigned_users', NULL, '35', NULL, 'Múltiples usuarios asignados', '2025-08-12 22:18:01'),
(182, 61, 2, 'commented', 'comment', NULL, 'Adjunto', NULL, 'Comentario agregado', '2025-08-12 22:18:26'),
(183, 61, 35, 'status_changed', 'status', 'pending', 'in_progress', NULL, 'Estado cambiado de pending a in_progress', '2025-08-13 06:49:40'),
(184, 48, 2, 'updated', 'due_date', '2025-08-29', '2025-08-14', NULL, 'Fecha de vencimiento actualizada', '2025-08-13 06:50:08'),
(185, 61, 2, 'assigned', 'assigned_to_user_id', '35', '2', 2, 'Usuario asignado a la tarea', '2025-08-13 06:53:08'),
(186, 63, 2, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-13 06:53:56'),
(187, 63, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 06:53:56'),
(188, 64, 2, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-13 06:54:20'),
(189, 64, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 06:54:20'),
(190, 65, 1, 'assigned', 'assigned_users', NULL, '2,4,6,11', NULL, 'Múltiples usuarios asignados', '2025-08-13 07:05:56'),
(191, 65, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 07:05:56'),
(192, 66, 1, 'assigned', 'assigned_users', NULL, '2,4,6,11', NULL, 'Múltiples usuarios asignados', '2025-08-13 07:09:20'),
(193, 66, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 07:09:20'),
(194, 67, 1, 'assigned', 'assigned_users', NULL, '2,9,5', NULL, 'Múltiples usuarios asignados', '2025-08-13 07:10:26'),
(195, 67, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 07:10:26'),
(196, 68, 1, 'assigned', 'assigned_users', NULL, '2,4', NULL, 'Múltiples usuarios asignados', '2025-08-13 07:12:26'),
(197, 68, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 07:12:26'),
(198, 69, 1, 'assigned', 'assigned_users', NULL, '2,4', NULL, 'Múltiples usuarios asignados', '2025-08-13 07:16:00'),
(199, 69, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 07:16:00'),
(200, 70, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-13 07:21:09'),
(201, 70, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 07:21:09'),
(202, 71, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-13 07:25:36'),
(203, 71, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 07:25:36'),
(204, 72, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-13 07:27:53'),
(205, 72, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 07:27:53'),
(206, 73, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-13 07:31:58'),
(207, 73, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 07:31:58'),
(208, 74, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-13 07:35:47'),
(209, 74, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 07:35:47'),
(210, 75, 1, 'assigned', 'assigned_users', NULL, '2,4,6,11,10,12', NULL, 'Múltiples usuarios asignados', '2025-08-13 07:36:15'),
(211, 75, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 07:36:15'),
(212, 76, 1, 'assigned', 'assigned_users', NULL, '2,4,11', NULL, 'Múltiples usuarios asignados', '2025-08-13 07:37:07'),
(213, 76, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 07:37:07'),
(214, 77, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-13 07:38:38'),
(215, 77, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 07:38:38'),
(216, 78, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-13 07:46:17'),
(217, 78, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 07:46:17'),
(218, 79, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-13 07:50:01'),
(219, 79, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 07:50:01'),
(220, 80, 1, 'assigned', 'assigned_users', NULL, '2,4,11', NULL, 'Múltiples usuarios asignados', '2025-08-13 15:12:28'),
(221, 80, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 15:12:28'),
(222, 76, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-13 16:06:14'),
(223, 77, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-13 16:06:15'),
(224, 73, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-13 16:06:17'),
(225, 75, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-13 16:06:20'),
(226, 74, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-13 16:06:21'),
(227, 81, 2, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-13 16:37:37'),
(228, 81, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 16:37:37'),
(229, 82, 2, 'assigned', 'assigned_users', NULL, '4,11,10,12', NULL, 'Múltiples usuarios asignados', '2025-08-13 16:38:14'),
(230, 82, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 16:38:14'),
(231, 83, 2, 'assigned', 'assigned_users', NULL, '4,11,10,12', NULL, 'Múltiples usuarios asignados', '2025-08-13 16:38:19'),
(232, 83, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 16:38:19'),
(233, 84, 2, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-13 16:39:20'),
(234, 84, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 16:39:20'),
(235, 85, 1, 'assigned', 'assigned_users', NULL, '2,4,6,11,10,12,38,9,5,1', NULL, 'Múltiples usuarios asignados', '2025-08-13 16:46:09'),
(236, 85, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 16:46:09'),
(237, 83, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-13 17:00:35'),
(238, 85, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-13 17:00:47'),
(239, 84, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-13 17:00:55'),
(240, 86, 2, 'assigned', 'assigned_users', NULL, '9,5', NULL, 'Múltiples usuarios asignados', '2025-08-13 17:01:57'),
(241, 86, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 17:01:57'),
(242, 87, 2, 'assigned', 'assigned_users', NULL, '9,5', NULL, 'Múltiples usuarios asignados', '2025-08-13 17:02:24'),
(243, 87, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 17:02:24'),
(244, 88, 2, 'assigned', 'assigned_users', NULL, '9,5', NULL, 'Múltiples usuarios asignados', '2025-08-13 17:02:54'),
(245, 88, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 17:02:54'),
(246, 81, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-13 17:07:04'),
(247, 88, 9, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-13 17:07:05'),
(248, 87, 9, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-13 17:07:06'),
(249, 86, 9, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-13 17:07:07'),
(250, 84, 4, 'commented', 'comment', NULL, 'Librerias recomendadas, QYT', NULL, 'Comentario agregado', '2025-08-13 17:56:29'),
(251, 89, 2, 'assigned', 'assigned_users', NULL, '2,4', NULL, 'Múltiples usuarios asignados', '2025-08-13 18:33:59'),
(252, 89, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-13 18:33:59'),
(253, 83, 4, 'status_changed', 'status', 'completed', 'pending', NULL, 'Estado cambiado de completed a pending', '2025-08-13 19:36:18'),
(254, 83, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-13 19:36:27'),
(255, 89, 4, 'commented', 'comment', NULL, 'Pendiente de analisis', NULL, 'Comentario agregado', '2025-08-13 19:40:05'),
(256, 89, 2, 'commented', 'comment', NULL, 'En espera', NULL, 'Comentario agregado', '2025-08-13 19:44:58'),
(257, 89, 4, 'commented', 'comment', NULL, 'Evidencia', NULL, 'Comentario agregado', '2025-08-13 19:48:18'),
(258, 89, 4, 'commented', 'comment', NULL, 'Adjunto', NULL, 'Comentario agregado', '2025-08-13 19:52:52'),
(259, 89, 2, 'status_changed', 'status', 'pending', 'in_progress', NULL, 'Estado cambiado de pending a in_progress', '2025-08-13 19:56:31'),
(260, 89, 2, 'status_changed', 'status', 'in_progress', 'pending', NULL, 'Estado cambiado de in_progress a pending', '2025-08-13 19:58:20'),
(261, 89, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-13 19:58:25'),
(262, 89, 2, 'status_changed', 'status', 'completed', 'pending', NULL, 'Estado cambiado de completed a pending', '2025-08-13 19:58:34'),
(263, 90, 4, 'commented', 'comment', NULL, 'Mejorar la funcionalidad', NULL, 'Comentario agregado', '2025-08-13 20:31:00'),
(264, 90, 4, 'commented', 'comment', NULL, 'Adjunto codigo', NULL, 'Comentario agregado', '2025-08-13 20:32:47'),
(265, 83, 4, 'status_changed', 'status', 'completed', 'in_progress', NULL, 'Estado cambiado de completed a in_progress', '2025-08-13 20:41:10'),
(266, 90, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-13 20:54:46'),
(267, 84, 2, 'status_changed', 'status', 'completed', 'in_progress', NULL, 'Estado cambiado de completed a in_progress', '2025-08-13 21:03:10'),
(268, 83, 4, 'status_changed', 'status', 'in_progress', 'completed', NULL, 'Estado cambiado de in_progress a completed', '2025-08-13 21:03:21'),
(269, 83, 4, 'status_changed', 'status', 'completed', 'pending', NULL, 'Estado cambiado de completed a pending', '2025-08-13 21:03:24'),
(270, 83, 4, 'status_changed', 'status', 'pending', 'in_progress', NULL, 'Estado cambiado de pending a in_progress', '2025-08-13 21:03:29'),
(271, 83, 4, 'status_changed', 'status', 'in_progress', 'completed', NULL, 'Estado cambiado de in_progress a completed', '2025-08-13 21:03:48'),
(272, 83, 4, 'status_changed', 'status', 'completed', 'pending', NULL, 'Estado cambiado de completed a pending', '2025-08-13 21:03:51'),
(273, 83, 4, 'status_changed', 'status', 'pending', 'in_progress', NULL, 'Estado cambiado de pending a in_progress', '2025-08-13 21:03:54'),
(274, 83, 4, 'status_changed', 'status', 'in_progress', 'pending', NULL, 'Estado cambiado de in_progress a pending', '2025-08-13 21:04:22'),
(275, 83, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-13 21:04:24'),
(276, 83, 4, 'status_changed', 'status', 'completed', 'pending', NULL, 'Estado cambiado de completed a pending', '2025-08-13 21:04:40'),
(277, 83, 4, 'status_changed', 'status', 'pending', 'in_progress', NULL, 'Estado cambiado de pending a in_progress', '2025-08-13 21:04:45'),
(278, 83, 4, 'status_changed', 'status', 'in_progress', 'completed', NULL, 'Estado cambiado de in_progress a completed', '2025-08-13 21:07:04'),
(279, 83, 4, 'status_changed', 'status', 'completed', 'pending', NULL, 'Estado cambiado de completed a pending', '2025-08-13 21:07:06'),
(280, 83, 4, 'status_changed', 'status', 'pending', 'in_progress', NULL, 'Estado cambiado de pending a in_progress', '2025-08-13 21:07:08'),
(281, 83, 4, 'status_changed', 'status', 'in_progress', 'completed', NULL, 'Estado cambiado de in_progress a completed', '2025-08-13 21:07:10'),
(282, 83, 4, 'status_changed', 'status', 'completed', 'pending', NULL, 'Estado cambiado de completed a pending', '2025-08-13 21:07:12'),
(283, 83, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-13 21:07:13'),
(284, 84, 2, 'status_changed', 'status', 'in_progress', 'completed', NULL, 'Estado cambiado de in_progress a completed', '2025-08-13 21:21:23'),
(287, 83, 4, 'status_changed', 'status', 'completed', 'pending', NULL, 'Estado cambiado de completed a pending', '2025-08-13 22:55:10'),
(288, 83, 4, 'status_changed', 'status', 'pending', 'in_progress', NULL, 'Estado cambiado de pending a in_progress', '2025-08-13 22:55:15'),
(289, 83, 4, 'commented', 'comment', NULL, 'se reactivo la tarea por que no cumplio la funcion', NULL, 'Comentario agregado', '2025-08-13 22:55:47'),
(290, 83, 2, 'commented', 'comment', NULL, 'Adelante', NULL, 'Comentario agregado', '2025-08-13 22:56:45'),
(291, 83, 4, 'status_changed', 'status', 'in_progress', 'completed', NULL, 'Estado cambiado de in_progress a completed', '2025-08-13 22:57:45'),
(292, 92, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 05:50:08'),
(293, 92, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 05:50:08'),
(294, 93, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-14 05:51:24'),
(295, 93, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 05:51:24'),
(296, 94, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 06:11:27'),
(297, 94, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 06:11:27'),
(298, 95, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-14 06:12:08'),
(299, 95, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 06:12:08'),
(300, 95, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-14 16:12:17'),
(301, 93, 4, 'status_changed', 'status', 'pending', 'in_progress', NULL, 'Estado cambiado de pending a in_progress', '2025-08-14 16:13:03'),
(302, 96, 1, 'assigned', 'assigned_users', NULL, '2,4', NULL, 'Múltiples usuarios asignados', '2025-08-14 16:28:31'),
(303, 96, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 16:28:31'),
(304, 96, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-14 16:28:58'),
(305, 92, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-14 16:29:39'),
(306, 97, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 16:39:48'),
(307, 97, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 16:39:48'),
(308, 98, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 16:39:48'),
(309, 98, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 16:39:48'),
(310, 99, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 16:39:48'),
(311, 99, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 16:39:48'),
(312, 100, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 16:39:48'),
(313, 100, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 16:39:48'),
(314, 101, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 16:39:48'),
(315, 101, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 16:39:48'),
(316, 102, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 16:39:48'),
(317, 102, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 16:39:48'),
(318, 103, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 16:39:48'),
(319, 103, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 16:39:48'),
(320, 94, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-14 16:41:03'),
(321, 97, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-14 16:41:04'),
(322, 98, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-14 16:41:05'),
(323, 99, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-14 16:41:08'),
(324, 102, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-14 16:41:12'),
(325, 101, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-14 16:41:14'),
(326, 100, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-14 16:41:17'),
(327, 104, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 16:45:33'),
(328, 104, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 16:45:33'),
(329, 105, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 16:47:11'),
(330, 105, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 16:47:11'),
(331, 106, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 16:47:11'),
(332, 106, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 16:47:11'),
(333, 107, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 16:47:11'),
(334, 107, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 16:47:11'),
(335, 108, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 16:47:11'),
(336, 108, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 16:47:11'),
(337, 109, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 16:47:11'),
(338, 109, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 16:47:11'),
(339, 110, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 16:47:11'),
(340, 110, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 16:47:11'),
(341, 103, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-14 16:47:33'),
(342, 110, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-14 16:47:36'),
(343, 109, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-14 16:47:39'),
(344, 108, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-14 16:47:47'),
(345, 107, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-14 16:47:49'),
(346, 106, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-14 16:47:52'),
(347, 111, 2, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-14 18:31:30'),
(348, 111, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 18:31:30'),
(349, 112, 1, 'assigned', 'assigned_users', NULL, '2,4', NULL, 'Múltiples usuarios asignados', '2025-08-14 19:22:01'),
(350, 112, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 19:22:01'),
(351, 113, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 19:31:12'),
(352, 113, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 19:31:12'),
(353, 114, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 19:31:12'),
(354, 114, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 19:31:12'),
(355, 115, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 19:31:12'),
(356, 115, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 19:31:12'),
(357, 116, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 19:31:12'),
(358, 116, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 19:31:12'),
(359, 117, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 19:31:12'),
(360, 117, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 19:31:12'),
(361, 118, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 19:31:12'),
(362, 118, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 19:31:12'),
(363, 119, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-14 19:31:12'),
(364, 119, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 19:31:12'),
(365, 120, 2, 'assigned', 'assigned_users', NULL, '4,6', NULL, 'Múltiples usuarios asignados', '2025-08-14 20:12:17'),
(366, 120, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-14 20:12:17'),
(367, 120, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-14 20:12:34'),
(368, 120, 2, 'commented', 'comment', NULL, 'REVISEN CODIGO', NULL, 'Comentario agregado', '2025-08-14 20:13:12'),
(369, 120, 2, 'commented', 'comment', NULL, 'documentacion', NULL, 'Comentario agregado', '2025-08-14 20:13:45'),
(370, 83, 4, 'status_changed', 'status', 'completed', 'in_progress', NULL, 'Estado cambiado de completed a in_progress', '2025-08-14 20:14:42'),
(371, 83, 4, 'status_changed', 'status', 'in_progress', 'completed', NULL, 'Estado cambiado de in_progress a completed', '2025-08-18 18:21:30'),
(372, 123, 9, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-18 20:02:42'),
(373, 122, 4, 'commented', 'comment', NULL, 'Comentario de prueba y documentacion', NULL, 'Comentario agregado', '2025-08-18 20:18:09'),
(374, 126, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-18 20:20:19'),
(375, 121, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-18 20:29:23'),
(376, 122, 4, 'commented', 'comment', NULL, 'Comentario desde el celular', NULL, 'Comentario agregado', '2025-08-18 20:51:03'),
(377, 125, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-18 20:51:27'),
(378, 129, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 21:22:49'),
(379, 129, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 21:22:49'),
(380, 130, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 21:24:03'),
(381, 130, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 21:24:03'),
(382, 131, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 21:57:53'),
(383, 131, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 21:57:53'),
(384, 132, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 21:57:53'),
(385, 132, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 21:57:53'),
(386, 133, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 21:57:53'),
(387, 133, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 21:57:53'),
(388, 134, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 21:57:53'),
(389, 134, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 21:57:53'),
(390, 135, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 21:57:53'),
(391, 135, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 21:57:53'),
(392, 136, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 21:57:53'),
(393, 136, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 21:57:53'),
(394, 137, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 21:57:53'),
(395, 137, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 21:57:53'),
(396, 138, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:05:21'),
(397, 138, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:05:21'),
(398, 139, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:05:21'),
(399, 139, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:05:21'),
(400, 140, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:05:21'),
(401, 140, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:05:21'),
(402, 141, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:05:21'),
(403, 141, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:05:21'),
(404, 142, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:05:21'),
(405, 142, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:05:21'),
(406, 143, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:05:21'),
(407, 143, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:05:21'),
(408, 144, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:05:21'),
(409, 144, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:05:21'),
(410, 132, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-18 22:08:33'),
(411, 139, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-18 22:08:34'),
(412, 133, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-18 22:08:36'),
(413, 140, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-18 22:08:37'),
(414, 129, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-18 22:08:40'),
(415, 130, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-18 22:08:42'),
(416, 131, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-18 22:08:44'),
(417, 138, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-18 22:08:45'),
(418, 145, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:11:05'),
(419, 145, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:11:05'),
(420, 146, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:11:05'),
(421, 146, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:11:05'),
(422, 147, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:11:05'),
(423, 147, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:11:05'),
(424, 148, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:11:05'),
(425, 148, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:11:05'),
(426, 149, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:11:05'),
(427, 149, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:11:05'),
(428, 150, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:11:05'),
(429, 150, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:11:05'),
(430, 151, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:11:05'),
(431, 151, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:11:05'),
(432, 152, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:11:27'),
(433, 152, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:11:27'),
(434, 153, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:18:10'),
(435, 153, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:18:10'),
(436, 154, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:18:10'),
(437, 154, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:18:10'),
(438, 155, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:18:10'),
(439, 155, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:18:10'),
(440, 156, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:18:10'),
(441, 156, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:18:10'),
(442, 157, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:18:10'),
(443, 157, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:18:10'),
(444, 158, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:18:10'),
(445, 158, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:18:10'),
(446, 159, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:18:10'),
(447, 159, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:18:10'),
(448, 124, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-18 22:22:24'),
(449, 122, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-18 22:22:32'),
(450, 127, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-18 22:22:38'),
(451, 128, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-18 22:22:41'),
(452, 160, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-18 22:25:05'),
(453, 161, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-18 22:25:08'),
(454, 164, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:30:26'),
(455, 164, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:30:26'),
(456, 165, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:31:40'),
(457, 165, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:31:40'),
(458, 166, 2, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-18 22:39:48'),
(459, 166, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 22:39:48'),
(460, 166, 4, 'commented', 'comment', NULL, 'Comentario desde el perfil de usuario normal adjunto documento', NULL, 'Comentario agregado', '2025-08-18 22:41:05'),
(461, 167, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-18 23:31:29'),
(462, 167, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 23:31:29'),
(463, 168, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-18 23:31:29'),
(464, 168, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 23:31:29'),
(465, 169, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-18 23:31:29'),
(466, 169, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 23:31:29'),
(467, 170, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-18 23:31:29'),
(468, 170, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 23:31:29'),
(469, 171, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-18 23:31:29'),
(470, 171, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 23:31:29'),
(471, 172, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-18 23:31:29'),
(472, 172, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 23:31:29'),
(473, 173, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-18 23:31:29'),
(474, 173, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-18 23:31:29'),
(475, 89, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-18 23:57:50'),
(476, 162, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 01:42:48'),
(477, 168, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 15:02:11'),
(478, 115, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 15:02:32'),
(479, 169, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 15:02:46'),
(480, 111, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 15:07:22'),
(481, 166, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 15:07:23'),
(482, 112, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 15:07:25'),
(483, 174, 2, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 15:08:22'),
(484, 174, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 15:08:22'),
(485, 175, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 15:09:29'),
(486, 175, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 15:09:29'),
(487, 176, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 15:10:17'),
(488, 176, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 15:10:17'),
(489, 177, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 15:10:17'),
(490, 177, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 15:10:17'),
(491, 178, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 15:10:17'),
(492, 178, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 15:10:17'),
(493, 179, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 15:10:17'),
(494, 179, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 15:10:17'),
(495, 180, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 15:10:17'),
(496, 180, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 15:10:17'),
(497, 181, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 15:10:17'),
(498, 181, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 15:10:17'),
(499, 104, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 15:13:07'),
(500, 113, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 15:13:09'),
(501, 167, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 15:13:11'),
(502, 182, 2, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 15:15:24'),
(503, 182, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 15:15:24'),
(504, 163, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 15:58:34'),
(505, 145, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 15:58:36'),
(506, 153, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 15:58:38'),
(507, 93, 4, 'status_changed', 'status', 'in_progress', 'completed', NULL, 'Estado cambiado de in_progress a completed', '2025-08-19 15:58:41'),
(508, 146, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 15:58:44'),
(509, 185, 2, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 16:50:43'),
(510, 185, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 16:50:43'),
(511, 186, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 16:52:28'),
(512, 186, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 16:52:28'),
(513, 187, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 16:52:28'),
(514, 187, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 16:52:28'),
(515, 188, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 16:52:28'),
(516, 188, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 16:52:28'),
(517, 189, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 16:52:28'),
(518, 189, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 16:52:28'),
(519, 190, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 16:52:28'),
(520, 190, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 16:52:28'),
(521, 191, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 16:52:28'),
(522, 191, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 16:52:28'),
(523, 186, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 16:52:52'),
(524, 175, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 16:57:53'),
(525, 114, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 16:58:00'),
(526, 187, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 16:58:06'),
(527, 192, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 17:22:14'),
(528, 192, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 17:22:14'),
(529, 193, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 17:22:14'),
(530, 193, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 17:22:14'),
(531, 194, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 17:22:14'),
(532, 194, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 17:22:14'),
(533, 195, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 17:22:14'),
(534, 195, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 17:22:14'),
(535, 196, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 17:22:14'),
(536, 196, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 17:22:14'),
(537, 197, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 17:22:14'),
(538, 197, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 17:22:14'),
(539, 198, 58, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 17:23:33'),
(540, 198, 58, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 17:23:33'),
(541, 182, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 17:25:15'),
(542, 199, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 17:50:11'),
(543, 199, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 17:50:11'),
(544, 200, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 17:50:11'),
(545, 200, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 17:50:11'),
(546, 201, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 17:50:11'),
(547, 201, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 17:50:11'),
(548, 202, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 17:50:11'),
(549, 202, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 17:50:11'),
(550, 203, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 17:50:11'),
(551, 203, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 17:50:11'),
(552, 204, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 17:50:11'),
(553, 204, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 17:50:11'),
(554, 205, 2, 'assigned', 'assigned_users', NULL, '2,4', NULL, 'Múltiples usuarios asignados', '2025-08-19 18:27:11'),
(555, 205, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 18:27:11'),
(556, 206, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 18:30:26'),
(557, 206, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 18:30:26'),
(558, 207, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 18:30:26'),
(559, 207, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 18:30:26'),
(560, 208, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 18:30:26'),
(561, 208, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 18:30:26'),
(562, 209, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 18:30:26'),
(563, 209, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 18:30:26'),
(564, 210, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 18:30:26'),
(565, 210, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 18:30:26'),
(566, 211, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 18:30:26'),
(567, 211, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 18:30:26'),
(568, 212, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 20:20:26'),
(569, 212, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 20:20:26'),
(570, 213, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 20:20:26'),
(571, 213, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 20:20:26'),
(572, 214, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 20:20:26'),
(573, 214, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 20:20:26'),
(574, 215, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 20:20:26'),
(575, 215, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 20:20:26'),
(576, 216, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 20:20:26'),
(577, 216, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 20:20:26'),
(578, 217, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 20:20:26');
INSERT INTO `Task_History` (`history_id`, `task_id`, `user_id`, `action_type`, `field_name`, `old_value`, `new_value`, `related_user_id`, `notes`, `created_at`) VALUES
(579, 217, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 20:20:26'),
(580, 218, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 20:20:26'),
(581, 218, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 20:20:26'),
(582, 219, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-19 20:21:49'),
(583, 219, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 20:21:49'),
(584, 220, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-19 20:22:48'),
(585, 220, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 20:22:48'),
(586, 221, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-19 20:22:48'),
(587, 221, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 20:22:48'),
(588, 222, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-19 20:22:48'),
(589, 222, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 20:22:48'),
(590, 223, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-19 20:25:11'),
(591, 223, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-19 20:25:11'),
(592, 223, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 20:27:56'),
(593, 220, 4, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 20:28:02'),
(594, 219, 2, 'status_changed', 'status', 'pending', 'completed', NULL, 'Estado cambiado de pending a completed', '2025-08-19 20:29:15'),
(595, 212, 2, 'commented', 'comment', NULL, 'Ya acabe rutina', NULL, 'Comentario agregado', '2025-08-19 20:30:35'),
(596, 212, 2, 'commented', 'comment', NULL, 'Djdjdj', NULL, 'Comentario agregado', '2025-08-19 20:32:09'),
(597, 227, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-20 15:36:52'),
(598, 227, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 15:36:52'),
(599, 228, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-20 15:36:52'),
(600, 228, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 15:36:52'),
(601, 229, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-20 15:36:52'),
(602, 229, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 15:36:52'),
(603, 230, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-20 15:36:52'),
(604, 230, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 15:36:52'),
(605, 231, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-20 15:36:52'),
(606, 231, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 15:36:52'),
(607, 232, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-20 15:36:52'),
(608, 232, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 15:36:52'),
(609, 234, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 15:40:35'),
(610, 234, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 15:40:35'),
(611, 235, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 15:40:35'),
(612, 235, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 15:40:35'),
(613, 236, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 15:40:35'),
(614, 236, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 15:40:35'),
(615, 237, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 15:40:35'),
(616, 237, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 15:40:35'),
(617, 238, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 15:40:35'),
(618, 238, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 15:40:35'),
(619, 239, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 15:40:35'),
(620, 239, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 15:40:35'),
(621, 240, 22, 'assigned', 'assigned_users', NULL, '24,27', NULL, 'Múltiples usuarios asignados', '2025-08-20 18:12:58'),
(622, 240, 22, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 18:12:58'),
(624, 240, 22, 'assigned', 'assigned_to_user_id', '24', '22', 22, 'Usuario asignado a la tarea', '2025-08-20 18:34:49'),
(625, 242, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-20 18:35:45'),
(626, 242, 1, 'created', 'subtask', NULL, 'Prueba1', NULL, 'Subtarea creada: Prueba1', '2025-08-20 18:35:45'),
(627, 242, 1, 'created', 'subtask', NULL, 'Prueba2', NULL, 'Subtarea creada: Prueba2', '2025-08-20 18:35:45'),
(628, 242, 1, 'created', 'subtask', NULL, 'Prueba3', NULL, 'Subtarea creada: Prueba3', '2025-08-20 18:35:45'),
(629, 242, 1, 'created', 'subtask', NULL, 'Prueba4', NULL, 'Subtarea creada: Prueba4', '2025-08-20 18:35:45'),
(630, 242, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 18:35:45'),
(631, 243, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-20 18:49:30'),
(632, 243, 1, 'created', 'subtask', NULL, 'TT2', NULL, 'Subtarea creada: TT2', '2025-08-20 18:49:30'),
(633, 243, 1, 'created', 'subtask', NULL, 'TT3', NULL, 'Subtarea creada: TT3', '2025-08-20 18:49:30'),
(634, 243, 1, 'created', 'subtask', NULL, 'TT4', NULL, 'Subtarea creada: TT4', '2025-08-20 18:49:30'),
(635, 243, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 18:49:30'),
(636, 244, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-20 18:56:36'),
(637, 244, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 18:56:36'),
(638, 244, 1, 'created', 'subtask', NULL, 'lol1', NULL, 'Subtarea creada: lol1', '2025-08-20 18:57:06'),
(639, 244, 1, 'created', 'subtask', NULL, 'lol2', NULL, 'Subtarea creada: lol2', '2025-08-20 18:57:06'),
(640, 245, 1, 'assigned', 'assigned_users', NULL, '2', NULL, 'Múltiples usuarios asignados', '2025-08-20 19:00:47'),
(641, 245, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 19:00:47'),
(642, 245, 1, 'created', 'subtask', NULL, 'rr1', NULL, 'Subtarea creada: rr1', '2025-08-20 19:01:10'),
(643, 245, 1, 'created', 'subtask', NULL, 'rr2', NULL, 'Subtarea creada: rr2', '2025-08-20 19:01:10'),
(644, 245, 1, 'created', 'subtask', NULL, 'rr3', NULL, 'Subtarea creada: rr3', '2025-08-20 19:01:10'),
(645, 243, 2, 'updated', 'subtask_status', NULL, 'in_progress', NULL, 'Estado de subtarea actualizado', '2025-08-20 19:33:37'),
(646, 243, 2, 'updated', 'subtask_status', NULL, 'completed', NULL, 'Estado de subtarea actualizado', '2025-08-20 19:36:45'),
(647, 243, 2, 'updated', 'subtask_status', NULL, 'in_progress', NULL, 'Estado de subtarea actualizado', '2025-08-20 19:38:14'),
(648, 243, 2, 'updated', 'subtask_status', NULL, 'in_progress', NULL, 'Estado de subtarea actualizado', '2025-08-20 19:38:18'),
(649, 243, 2, 'updated', 'subtask_status', NULL, 'completed', NULL, 'Estado de subtarea actualizado', '2025-08-20 19:38:21'),
(650, 246, 1, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 19:39:59'),
(651, 246, 1, 'created', 'subtask', NULL, 'ww1', NULL, 'Subtarea creada: ww1', '2025-08-20 19:39:59'),
(652, 246, 1, 'created', 'subtask', NULL, 'ww2', NULL, 'Subtarea creada: ww2', '2025-08-20 19:39:59'),
(653, 246, 1, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 19:39:59'),
(654, 246, 4, 'updated', 'subtask_status', NULL, 'in_progress', NULL, 'Estado de subtarea actualizado', '2025-08-20 19:46:24'),
(655, 246, 4, 'updated', 'subtask_status', NULL, 'completed', NULL, 'Estado de subtarea actualizado', '2025-08-20 19:46:27'),
(656, 246, 4, 'updated', 'subtask_status', NULL, 'pending', NULL, 'Estado de subtarea actualizado', '2025-08-20 20:01:13'),
(657, 246, 4, 'updated', 'subtask_status', NULL, 'in_progress', NULL, 'Estado de subtarea actualizado', '2025-08-20 20:01:15'),
(658, 246, 4, 'updated', 'subtask_status', NULL, 'completed', NULL, 'Estado de subtarea actualizado', '2025-08-20 20:01:19'),
(659, 247, 2, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 20:11:33'),
(660, 247, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 20:11:33'),
(661, 246, 2, 'updated', 'subtask_status', NULL, 'in_progress', NULL, 'Estado de subtarea actualizado', '2025-08-20 20:20:40'),
(662, 248, 2, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 20:21:48'),
(663, 248, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 20:21:48'),
(664, 249, 2, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 20:27:53'),
(665, 249, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 20:27:53'),
(666, 250, 2, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 20:32:16'),
(667, 250, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 20:32:16'),
(668, 251, 2, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 20:43:13'),
(669, 251, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 20:43:13'),
(670, 252, 2, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 20:44:13'),
(671, 252, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 20:44:13'),
(672, 253, 22, 'assigned', 'assigned_users', NULL, '22', NULL, 'Múltiples usuarios asignados', '2025-08-20 20:49:33'),
(673, 253, 22, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 20:49:33'),
(674, 254, 2, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 20:49:46'),
(675, 254, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 20:49:46'),
(676, 255, 2, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 22:18:45'),
(677, 255, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 22:18:45'),
(678, 256, 2, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 22:24:25'),
(679, 256, 2, 'created', 'subtask', NULL, 'erer432', NULL, 'Subtarea creada: erer432', '2025-08-20 22:24:25'),
(680, 256, 2, 'created', 'subtask', NULL, 'erer432', NULL, 'Subtarea creada: erer432', '2025-08-20 22:24:25'),
(681, 256, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 22:24:25'),
(682, 257, 2, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 22:26:36'),
(683, 257, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 22:26:36'),
(684, 258, 2, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 22:32:33'),
(685, 258, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 22:32:33'),
(686, 259, 2, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 22:34:02'),
(687, 259, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 22:34:02'),
(688, 260, 2, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 22:39:40'),
(689, 260, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 22:39:40'),
(690, 261, 2, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 23:04:16'),
(691, 261, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 23:04:16'),
(692, 262, 2, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 23:09:06'),
(693, 262, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 23:09:06'),
(694, 263, 2, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 23:14:32'),
(695, 263, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 23:14:32'),
(696, 264, 2, 'assigned', 'assigned_users', NULL, '4', NULL, 'Múltiples usuarios asignados', '2025-08-20 23:24:46'),
(697, 264, 2, 'created', 'subtask', NULL, 'ggg', NULL, 'Subtarea creada: ggg', '2025-08-20 23:24:46'),
(698, 264, 2, 'created', 'subtask', NULL, 'ggg', NULL, 'Subtarea creada: ggg', '2025-08-20 23:24:46'),
(699, 264, 2, 'created', NULL, NULL, NULL, NULL, 'Tarea creada', '2025-08-20 23:24:46'),
(700, 264, 2, 'updated', 'subtask_status', NULL, 'in_progress', NULL, 'Estado de subtarea actualizado', '2025-08-20 23:25:06'),
(701, 253, 1, 'created', 'subtask', NULL, 'Plan de Capacitacion Director Agencia', NULL, 'Subtarea creada: Plan de Capacitacion Director Agencia', '2025-08-20 23:28:48');

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
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `avatar_path` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Users`
--

INSERT INTO `Users` (`user_id`, `username`, `password_hash`, `email`, `full_name`, `is_active`, `last_login`, `created_at`, `avatar_path`) VALUES
(1, 'super', '123456', 'desarrollo@rinorisk.com', 'Usuario Administrador', 1, '2025-08-20 23:27:14', '2025-07-29 22:45:12', ''),
(2, 'abdielc', '123456', 'redskullcoder@gmail.com', 'Abdiel Carrasco', 1, '2025-08-20 23:11:02', '2025-07-29 23:23:21', ''),
(4, 'franklinb', 'Grok2024', 'desarrollo2@rinorisk.com', 'Franklin Benitez', 1, '2025-08-20 20:00:58', '2025-07-30 17:10:05', 'uploads/avatar_4_1755619752.jpeg'),
(5, 'Rubend', '123456', 'desarrollo3@rinorisk.com', 'Ruben Dorado', 1, NULL, '2025-07-30 18:20:45', ''),
(6, 'gaelh', '123456', 'desarrollo.fulstack@rinorisk.com', 'Gael Herrera', 1, NULL, '2025-07-30 19:36:11', ''),
(9, 'manuels', '123456', 'desarrollo.frontjr@rinorisk.com', 'Manuel Saenz', 1, '2025-08-18 23:03:16', '2025-07-30 19:46:47', ''),
(10, 'piña', '123456', 'desarrollo.dataanalyst@rinorisk.com', 'Jaen Piña', 1, NULL, '2025-07-30 19:47:38', ''),
(11, 'isaccg', '123456', 'desarrollo.backend@rinorisk.com', 'Isaac Garcia', 1, NULL, '2025-07-30 19:48:29', ''),
(12, 'janathanm', '123456', 'desarrollo.fullstack.2@rinorisk.com', 'Jonathan Martinez', 1, NULL, '2025-07-30 19:51:19', ''),
(13, 'jessicam', '123456', 'gerente.mkt@rinorisk.com', 'Jessica Mejia', 1, '2025-08-13 22:02:51', '2025-07-30 19:54:06', ''),
(14, 'bereniceh', '123456', 'gerente.rh@rinorisk.com', 'Berenice Hernandez', 1, '2025-08-07 18:40:03', '2025-07-30 20:35:34', ''),
(15, 'Valeriag', '123456', 'cultura.organizacional@rinorisk.com', 'Valeria Garcia', 1, NULL, '2025-08-04 19:11:54', ''),
(16, 'Nora', '123456', 'sincorreo1@rinorisk.com', 'Nora', 1, NULL, '2025-08-06 21:01:29', ''),
(17, 'ricardov', '123456', 'redes@rinorisk.com', 'Ricardo Vidaño', 1, NULL, '2025-08-06 21:03:34', ''),
(18, 'israell', '123456', 'publicidad@rinorisk.com', 'Israel Lovato', 1, NULL, '2025-08-06 21:04:17', ''),
(19, 'arisbethc', '123456', 'supervisor.comercialtj@rinorisk.com', 'Arisbeth Cuevas', 1, '2025-08-07 20:20:36', '2025-08-06 21:05:25', ''),
(20, 'marlenef', '123456', 'cobranza@rinorisk.com', 'Marlene Flores', 1, NULL, '2025-08-06 21:06:16', ''),
(21, 'Beatrizi', '123456', 'servicio.vgmtj@rinorisk.com', 'Beatriz Ita', 1, NULL, '2025-08-06 21:07:46', ''),
(22, 'sofiag', 'Cur7ywhoi#!', 'Sofia@rinorisk.com', 'Sofia Gallardo', 1, '2025-08-20 23:32:18', '2025-08-06 21:09:03', ''),
(23, 'taniab', '123456', 'auxiliar.inmuebles@rinorisk.com', 'Tania Barboza', 1, NULL, '2025-08-06 21:09:43', ''),
(24, 'angelicav', '123456', 'gerente.administrativo@rinorisk.com', 'Angelica Vallejo', 1, NULL, '2025-08-06 21:10:52', ''),
(25, 'yazmint', '123456', 'contabilidad@rinorisk.com', 'Yazmin Trejo', 1, NULL, '2025-08-06 21:11:37', ''),
(26, 'mitchellq', '123456', 'auxiliar.contable@rinorisk.com', 'Mitchellq', 1, NULL, '2025-08-06 21:12:36', ''),
(27, 'evelynd', '123456', 'analista.financiero@rinorisk.com', 'Evelyn Duran', 1, '2025-08-20 18:16:45', '2025-08-06 21:13:10', ''),
(28, 'thanias', '123456', 'legal@rinorisk.com', 'Thania Sanchez', 1, NULL, '2025-08-06 21:14:19', ''),
(29, 'monsed', '123456', 'asistente.legal@rinorisk.com', 'Monserrat Diaz', 1, NULL, '2025-08-06 21:22:48', ''),
(30, 'Ivanm', 'Jicdx#$23H', 'procesos.operativos@rinorisk.com', 'Ivan Mosqueda', 1, '2025-08-13 21:17:59', '2025-08-06 21:23:48', ''),
(31, 'marisoll', '123456', 'sincorreo2@rinorisk.com', 'Marisol Lopez', 1, NULL, '2025-08-06 21:24:54', ''),
(32, 'joseo', '123456', 'sincorreo3@rinorisk.com', 'Jose Ovando', 1, NULL, '2025-08-06 21:25:29', ''),
(33, 'isidorob', '123456', 'sincorreo4@rinorisk.com', 'Isidoro Bravo', 1, NULL, '2025-08-06 21:26:07', ''),
(34, 'vannesat', '123456', 'sincorreo5@rinorisk.com', 'Vanessa Torres', 1, NULL, '2025-08-06 21:26:40', ''),
(35, 'hildan', '123456', 'sincorreo6@rinorisk.com', 'Hilda Nuñez', 1, NULL, '2025-08-06 21:27:48', ''),
(36, 'porfiriog', '123456', 'sincorreo7@rinorisk.com', 'Porfirio Gonzales', 1, NULL, '2025-08-06 21:28:22', ''),
(37, 'ronym', '123456', 'sincorreo8@rinorisk.com', 'Rony Marquez', 1, NULL, '2025-08-06 21:29:47', ''),
(38, 'juanl', '123456', 'sistemas@rinorisk.com', 'Juan Lopez', 1, NULL, '2025-08-06 21:30:21', ''),
(39, 'yesseniav', '123456', 'capacitacion@rinorisk.com', 'Yessenia', 1, NULL, '2025-08-06 21:46:27', ''),
(40, 'karenv', '123456', 'sincorreo9@rinorisk.com', 'Karen Vazques', 1, NULL, '2025-08-06 21:47:02', ''),
(41, 'angelr', '123456', 'sincorreo10@rinorisk.com', 'Angel Ramos', 1, NULL, '2025-08-06 21:47:43', ''),
(42, 'fernandam', '123456', 'sincorreo11@rinorisk.com', 'Fernanda Moreno', 1, NULL, '2025-08-06 21:48:14', ''),
(43, 'dulces', '123456', 'sincorreo12@rinorisk.com', 'Dulce de santiago', 1, NULL, '2025-08-06 21:48:55', ''),
(44, 'Keyla', '123456', 'sincorreo13@rinorisk.com', 'Keyla', 1, NULL, '2025-08-06 21:49:46', ''),
(45, 'samanthac', '123456', 'sincorrreo14@rinorisk.com', 'Samantha Carrizales', 1, NULL, '2025-08-06 21:50:22', ''),
(46, 'lisbethv', '123456', 'sincorreo15@rinorisk.com', 'Lisbeth Vega', 1, NULL, '2025-08-06 21:50:57', ''),
(47, 'alejandrar', '123456', 'sincorreo16@rinorisk.com', 'Alejandra Ramos', 1, NULL, '2025-08-06 21:51:48', ''),
(48, 'myriamt', '123456', 'sincorreo17@rinorisk.com', 'Myriam Torres', 1, NULL, '2025-08-06 21:52:21', ''),
(49, 'dianag', '123456', 'sincorreo18@rinorisk.com', 'Diana Gonzales', 1, NULL, '2025-08-06 21:52:51', ''),
(50, 'karenf', '123456', 'sincorreo19@rinorisk.com', 'Karen Fletes', 1, '2025-08-07 20:20:10', '2025-08-06 21:54:25', ''),
(55, 'rosaliae', '123456', 'sincorreo20@rinorisk.com', 'Rosalia Enriquez', 1, NULL, '2025-08-06 23:21:11', ''),
(56, 'sonial', '123456', 'sincorreo21@rinorisk.com', 'Sonia Lopez', 1, NULL, '2025-08-06 23:24:15', ''),
(57, 'ivan2', '123456', 'sincorreo23@rinorsik.com', 'Ivan Mosqueda', 1, '2025-08-20 18:09:17', '2025-08-06 23:25:11', ''),
(58, 'norai', 'Vic$sing#$', 'asistente.direccion@rinorisk.com', 'Nora Imelda Covarrubias', 1, '2025-08-19 17:34:07', '2025-08-06 23:29:06', ''),
(63, 'ivan3', '123456', 'sincorreo29@rinorisk.com', 'Ivan Mozqueda', 1, '2025-08-20 18:08:51', '2025-08-13 23:14:55', '');

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
(13, 3),
(14, 3),
(15, 3),
(19, 3),
(22, 3),
(50, 3),
(57, 3),
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
(56, 4),
(30, 1),
(58, 1),
(4, 4),
(2, 3),
(63, 4);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_subtasks_complete`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_subtasks_complete` (
`subtask_id` int(11)
,`task_id` int(11)
,`parent_task_name` varchar(255)
,`title` varchar(255)
,`description` text
,`completion_percentage` decimal(5,2)
,`estimated_hours` decimal(5,2)
,`actual_hours` decimal(5,2)
,`status` enum('pending','in_progress','completed','cancelled')
,`priority` enum('low','medium','high','urgent')
,`due_date` date
,`assigned_to_user_id` int(11)
,`created_by_user_id` int(11)
,`subtask_order` int(11)
,`created_at` timestamp
,`updated_at` timestamp
,`assigned_to_fullname` varchar(100)
,`assigned_to_username` varchar(100)
,`created_by_fullname` varchar(100)
,`created_by_username` varchar(100)
);

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
-- Indices de la tabla `Clans`
--
ALTER TABLE `Clans`
  ADD PRIMARY KEY (`clan_id`),
  ADD UNIQUE KEY `uniq_clan_name` (`clan_name`);

--
-- Indices de la tabla `Clan_Members`
--
ALTER TABLE `Clan_Members`
  ADD PRIMARY KEY (`clan_member_id`),
  ADD UNIQUE KEY `uniq_clan_user` (`clan_id`,`user_id`);

--
-- Indices de la tabla `Notification_Log`
--
ALTER TABLE `Notification_Log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_event` (`event_type`,`entity_id`,`user_id`,`sent_to`);

--
-- Indices de la tabla `Notification_Settings`
--
ALTER TABLE `Notification_Settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indices de la tabla `Projects`
--
ALTER TABLE `Projects`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `idx_clan_id` (`clan_id`),
  ADD KEY `idx_created_by` (`created_by_user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_projects_time_limit` (`time_limit`);

--
-- Indices de la tabla `Subtasks`
--
ALTER TABLE `Subtasks`
  ADD PRIMARY KEY (`subtask_id`);

--
-- Indices de la tabla `Tasks`
--
ALTER TABLE `Tasks`
  ADD PRIMARY KEY (`task_id`);

--
-- Indices de la tabla `Task_Assignments`
--
ALTER TABLE `Task_Assignments`
  ADD PRIMARY KEY (`assignment_id`);

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
-- Indices de la tabla `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `uniq_username` (`username`),
  ADD UNIQUE KEY `uniq_email` (`email`);

--
-- Indices de la tabla `User_Roles`
--
ALTER TABLE `User_Roles`
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_role` (`role_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Clans`
--
ALTER TABLE `Clans`
  MODIFY `clan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `Clan_Members`
--
ALTER TABLE `Clan_Members`
  MODIFY `clan_member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT de la tabla `Notification_Log`
--
ALTER TABLE `Notification_Log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=275;

--
-- AUTO_INCREMENT de la tabla `Projects`
--
ALTER TABLE `Projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `Subtasks`
--
ALTER TABLE `Subtasks`
  MODIFY `subtask_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `Tasks`
--
ALTER TABLE `Tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=265;

--
-- AUTO_INCREMENT de la tabla `Task_Assignments`
--
ALTER TABLE `Task_Assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=354;

--
-- AUTO_INCREMENT de la tabla `Task_Attachments`
--
ALTER TABLE `Task_Attachments`
  MODIFY `attachment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `Task_Comments`
--
ALTER TABLE `Task_Comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `Task_History`
--
ALTER TABLE `Task_History`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=702;

--
-- AUTO_INCREMENT de la tabla `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_subtasks_complete`
--
DROP TABLE IF EXISTS `v_subtasks_complete`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_subtasks_complete`  AS SELECT `s`.`subtask_id` AS `subtask_id`, `s`.`task_id` AS `task_id`, `t`.`task_name` AS `parent_task_name`, `s`.`title` AS `title`, `s`.`description` AS `description`, `s`.`completion_percentage` AS `completion_percentage`, `s`.`estimated_hours` AS `estimated_hours`, `s`.`actual_hours` AS `actual_hours`, `s`.`status` AS `status`, `s`.`priority` AS `priority`, `s`.`due_date` AS `due_date`, `s`.`assigned_to_user_id` AS `assigned_to_user_id`, `s`.`created_by_user_id` AS `created_by_user_id`, `s`.`subtask_order` AS `subtask_order`, `s`.`created_at` AS `created_at`, `s`.`updated_at` AS `updated_at`, `u_assigned`.`full_name` AS `assigned_to_fullname`, `u_assigned`.`username` AS `assigned_to_username`, `u_created`.`full_name` AS `created_by_fullname`, `u_created`.`username` AS `created_by_username` FROM (((`Subtasks` `s` left join `Tasks` `t` on(`s`.`task_id` = `t`.`task_id`)) left join `Users` `u_assigned` on(`s`.`assigned_to_user_id` = `u_assigned`.`user_id`)) left join `Users` `u_created` on(`s`.`created_by_user_id` = `u_created`.`user_id`)) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Subtask_Comments`
--

CREATE TABLE `Subtask_Comments` (
  `comment_id` int(11) NOT NULL,
  `subtask_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `comment_type` enum('comment','status_change','assignment','completion','system') DEFAULT 'comment',
  `related_user_id` int(11) DEFAULT NULL,
  `old_value` varchar(255) DEFAULT NULL,
  `new_value` varchar(255) DEFAULT NULL,
  `is_private` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Subtask_Attachments`
--

CREATE TABLE `Subtask_Attachments` (
  `attachment_id` int(11) NOT NULL,
  `subtask_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Subtask_Comments`
--
ALTER TABLE `Subtask_Comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `subtask_id` (`subtask_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `related_user_id` (`related_user_id`),
  ADD KEY `idx_subtask_comments_created_at` (`created_at`);

--
-- Indices de la tabla `Subtask_Attachments`
--
ALTER TABLE `Subtask_Attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `subtask_id` (`subtask_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `idx_subtask_attachments_uploaded_at` (`uploaded_at`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Subtask_Comments`
--
ALTER TABLE `Subtask_Comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Subtask_Attachments`
--
ALTER TABLE `Subtask_Attachments`
  MODIFY `attachment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Subtask_Comments`
--
ALTER TABLE `Subtask_Comments`
  ADD CONSTRAINT `subtask_comments_ibfk_1` FOREIGN KEY (`subtask_id`) REFERENCES `Subtasks` (`subtask_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subtask_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subtask_comments_ibfk_3` FOREIGN KEY (`related_user_id`) REFERENCES `Users` (`user_id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `Subtask_Attachments`
--
ALTER TABLE `Subtask_Attachments`
  ADD CONSTRAINT `subtask_attachments_ibfk_1` FOREIGN KEY (`subtask_id`) REFERENCES `Subtasks` (`subtask_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subtask_attachments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subtask_attachments_ibfk_3` FOREIGN KEY (`comment_id`) REFERENCES `Subtask_Comments` (`comment_id`) ON DELETE SET NULL;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
