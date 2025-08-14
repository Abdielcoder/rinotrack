-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 14-08-2025 a las 05:40:33
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
(205, 'project_assigned_to_clan', 33, 38, 'sistemas@rinorisk.com', NULL, '2025-08-13 23:57:31');

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
  `time_limit` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `Projects`
--

INSERT INTO `Projects` (`project_id`, `project_name`, `description`, `clan_id`, `created_by_user_id`, `status`, `total_tasks`, `completed_tasks`, `progress_percentage`, `created_at`, `updated_at`, `kpi_quarter_id`, `kpi_points`, `task_distribution_mode`, `time_limit`) VALUES
(24, 'RinoPlugin', 'Plugin de control de accesos de portales', 5, 58, 'open', 4, 4, 100.00, '2025-08-13 16:29:25', '2025-08-13 23:49:09', 11, 50, 'automatic', '2025-09-30'),
(25, 'Resegmentación', 'Utilidad fuera de linea, de consulta, comparación y resegmentación de prima y negocios', 5, 58, 'open', 4, 4, 200.00, '2025-08-13 16:30:52', '2025-08-13 23:49:18', 11, 50, 'automatic', '2025-09-30'),
(26, 'Nuevos Indicadores / procesos automaticos', 'Indicadores necesarios para procesos y sistemas', 5, 58, 'open', 0, 0, 0.00, '2025-08-13 16:31:33', '2025-08-13 23:49:26', 11, 350, 'automatic', '2025-09-30'),
(29, 'Metas promotoria etapa 1', 'Seguimiento al proyecto, consulta de promotoria.', 5, 1, 'open', 1, 0, 0.00, '2025-08-13 17:35:38', '2025-08-13 23:49:35', 11, 300, 'automatic', '2025-09-30'),
(32, 'Consulta de promotoria', 'Primera fase de proyecto promotoria', 5, 1, 'open', 0, 0, 0.00, '2025-08-13 23:54:53', '2025-08-13 23:56:08', 11, 250, 'automatic', '2025-08-19');

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
(42, NULL, 'Investigación Indicadores estrella', 'Investigación Indicadores estrella', 15, 11, 2, 'medium', '2025-09-05', NULL, NULL, 0.00, 100.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 22:20:26', '2025-08-12 21:06:30'),
(43, NULL, 'Elaboración de PHU', 'Crear PHU para su aprobación', 15, 2, 2, 'medium', '2025-08-15', NULL, NULL, 0.00, 100.00, 0.00, '#3B82F6', 0, 0, 'in_progress', 0, NULL, '2025-08-05 22:21:59', '2025-08-12 21:06:30'),
(44, NULL, 'Creación de PHU', 'Creación de PHU para su aprobación', 16, 2, 2, 'medium', '2025-08-29', NULL, NULL, 0.00, 250.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-05 22:22:38', '2025-08-05 22:30:14'),
(48, NULL, 'Distribución de tareas', 'Crear el backlog de tareas para todos los implicados en el proyecto', 16, NULL, 2, 'medium', '2025-08-14', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-07 18:07:44', '2025-08-13 06:50:08'),
(55, NULL, 'SEGUIMIENTO AGENCIAS RINO', 'SDFSDF', 22, 30, 22, 'medium', '2025-08-15', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-11 23:38:45', '2025-08-11 23:38:45'),
(58, NULL, 'Tarea de recluta de prueba.', 'Tienes que arreglar modal', 15, NULL, 1, 'medium', '2025-08-15', NULL, NULL, 0.00, 100.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-12 21:04:43', '2025-08-12 20:57:50', '2025-08-12 21:06:30'),
(59, NULL, 'Tarea numerp 5', 'Introducir datros a la aspp', 15, NULL, 1, 'medium', '2025-08-15', NULL, NULL, 0.00, 100.00, 0.00, '#3B82F6', 0, 0, 'in_progress', 0, NULL, '2025-08-12 21:01:12', '2025-08-12 21:06:30'),
(61, NULL, 'consulta de minuta', 'Se revisara minuta', 16, 2, 58, 'medium', '2025-08-13', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'in_progress', 0, NULL, '2025-08-12 22:14:50', '2025-08-13 06:53:08'),
(63, NULL, 'Tarea de prueba33', 'gfrefaef', 16, 2, 2, 'medium', '2025-08-13', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-13 06:53:56', '2025-08-13 06:53:56'),
(64, NULL, 'Mi tarea de prueba', 'asdfasdfads', 16, 2, 2, 'medium', '2025-08-14', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-13 06:54:20', '2025-08-13 06:54:20'),
(65, NULL, 'Tarea de prueba de asignacion multiple', 'Esto es solo una demostración', 19, 2, 1, 'medium', NULL, NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-13 07:05:56', '2025-08-13 07:05:56'),
(66, NULL, 'Tarea de prueba de asignacion multiple', 'Prueba', 19, 2, 1, 'medium', NULL, NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-13 07:09:20', '2025-08-13 07:09:20'),
(67, NULL, 'ccc', 'ccc', 18, 2, 1, 'medium', NULL, NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-13 07:10:26', '2025-08-13 07:10:26'),
(68, NULL, 'asdf', 'asdfas', 18, 2, 1, 'medium', NULL, NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-13 07:12:26', '2025-08-13 07:12:26'),
(69, NULL, 'Tarea y', 'Tarea y', 19, 2, 1, 'medium', '2025-08-15', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-13 07:16:00', '2025-08-13 07:16:00'),
(70, NULL, 'Prueba de Asignacion', 'Esto es una prueba de notificacion de asignacion', 19, 2, 1, 'medium', '2025-08-14', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-13 07:21:09', '2025-08-13 07:21:09'),
(71, NULL, 'bcvb', 'brweew', 19, 2, 1, 'medium', '2025-08-18', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-13 07:25:36', '2025-08-13 07:25:36'),
(72, NULL, 'Noti correo', 'Prueba de noti por correo', 18, 2, 1, 'medium', '2025-08-14', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-13 07:27:53', '2025-08-13 07:27:53'),
(73, NULL, 'sdfgsdf', 'sdfgsdf', 20, 2, 1, 'medium', '2025-08-14', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-13 16:06:17', '2025-08-13 07:31:58', '2025-08-13 16:06:17'),
(74, NULL, 'ewrqewrwqe', 'fasdfasdfasd', 20, 2, 1, 'medium', '2025-08-15', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-13 16:06:21', '2025-08-13 07:35:47', '2025-08-13 16:06:21'),
(75, NULL, 'dsfasdf', 'asdfadsfasd', 20, 2, 1, 'medium', '2025-08-15', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-13 16:06:20', '2025-08-13 07:36:15', '2025-08-13 16:06:20'),
(76, NULL, 'xxxx', 'xxx', 20, 2, 1, 'medium', NULL, NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-13 16:06:14', '2025-08-13 07:37:07', '2025-08-13 16:06:14'),
(77, NULL, 'sdfsadf', 'sadfasdfa', 20, 2, 1, 'medium', '2025-08-14', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-13 16:06:15', '2025-08-13 07:38:38', '2025-08-13 16:06:15'),
(78, NULL, 'Alta Usuarios', 'Alta Usuarios', 18, 2, 1, 'medium', '2025-08-15', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-13 07:46:17', '2025-08-13 07:46:17'),
(79, NULL, 'dfasdfas', 'asdfasdfas', 18, 2, 1, 'medium', '2025-08-15', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-13 07:50:01', '2025-08-13 07:50:01'),
(80, NULL, 'Revision plantilla', 'Esto es una revision de la plantilla', 22, 2, 1, 'medium', '2025-08-15', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-13 15:12:28', '2025-08-13 15:12:28'),
(81, NULL, 'Interfaz', 'Interfaz total del sistema', 24, 2, 2, 'medium', '2025-08-13', NULL, NULL, 0.00, 12.50, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-13 21:52:05', '2025-08-13 16:37:37', '2025-08-13 21:52:05'),
(83, NULL, '', '', 25, 4, 2, 'high', '2025-08-13', NULL, NULL, 0.00, 50.00, 0.00, '#3B82F6', 0, 0, 'completed', 0, '2025-08-13 22:57:45', '2025-08-13 16:38:19', '2025-08-13 22:57:45'),
(84, NULL, 'Interfaz', 'Interfaz fuera de linea', 25, 2, 2, 'medium', '2025-08-13', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-13 21:21:23', '2025-08-13 16:39:20', '2025-08-13 21:21:23'),
(85, NULL, 'QA', 'Pruebas unitarias', 25, 2, 1, 'medium', '2025-08-15', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-13 17:00:47', '2025-08-13 16:46:09', '2025-08-13 17:00:47'),
(86, NULL, 'Logica', '', 24, 9, 2, 'medium', '2025-08-14', NULL, NULL, 0.00, 12.50, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-13 21:52:01', '2025-08-13 17:01:57', '2025-08-13 21:52:01'),
(87, NULL, 'Publicacion', '', 24, 9, 2, 'medium', '2025-08-14', NULL, NULL, 0.00, 12.50, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-13 21:52:03', '2025-08-13 17:02:24', '2025-08-13 21:52:03'),
(88, NULL, 'Logica de permisos', '', 24, 9, 2, 'medium', '2025-08-14', NULL, NULL, 0.00, 12.50, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-13 21:52:04', '2025-08-13 17:02:54', '2025-08-13 21:52:04'),
(89, NULL, '', '', 29, 2, 2, 'medium', '2025-08-14', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-13 18:33:59', '2025-08-13 19:58:34'),
(90, NULL, 'Revision de QA', 'Neuvas pruebas', 25, 4, 4, 'medium', '2025-08-15', NULL, NULL, 0.00, 50.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-13 20:54:46', '2025-08-13 20:05:58', '2025-08-13 20:54:46');

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
(192, 89, 4, 50.00, '2025-08-13 18:33:59', 2, 'assigned', NULL);

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
(15, 83, 4, 37, '17. CVJONATHANGARCIA.pdf', 'uploads/task_attachments/att_689d17f3de6fc.pdf', NULL, 'application/pdf', NULL, '2025-08-13 22:55:47');

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
(38, 83, 2, 'Adelante', 'comment', NULL, NULL, NULL, 0, '2025-08-13 22:56:45');

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
(291, 83, 4, 'status_changed', 'status', 'in_progress', 'completed', NULL, 'Estado cambiado de in_progress a completed', '2025-08-13 22:57:45');

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
(1, 'super', '123456', 'desarrollo@rinorisk.com', 'Usuario Administrador', 1, '2025-08-13 23:32:43', '2025-07-29 22:45:12'),
(2, 'abdielc', '123456', 'redskullcoder@gmail.com', 'Abdiel Carrasco', 1, '2025-08-13 22:56:15', '2025-07-29 23:23:21'),
(4, 'franklinb', '123456', 'desarrollo2@rinorisk.com', 'Franklin Benitez', 1, '2025-08-13 23:08:05', '2025-07-30 17:10:05'),
(5, 'Rubend', '123456', 'desarrollo3@rinorisk.com', 'Ruben Dorado', 1, NULL, '2025-07-30 18:20:45'),
(6, 'gaelh', '123456', 'desarrollo.fulstack@rinorisk.com', 'Gael Herrera', 1, NULL, '2025-07-30 19:36:11'),
(9, 'manuels', '123456', 'desarrollo.frontjr@rinorisk.com', 'Manuel Saenz', 1, NULL, '2025-07-30 19:46:47'),
(10, 'piña', '123456', 'desarrollo.dataanalyst@rinorisk.com', 'Jaen Piña', 1, NULL, '2025-07-30 19:47:38'),
(11, 'isaccg', '123456', 'desarrollo.backend@rinorisk.com', 'Isaac Garcia', 1, NULL, '2025-07-30 19:48:29'),
(12, 'janathanm', '123456', 'desarrollo.fullstack.2@rinorisk.com', 'Jonathan Martinez', 1, NULL, '2025-07-30 19:51:19'),
(13, 'jessicam', '123456', 'gerente.mkt@rinorisk.com', 'Jessica Mejia', 1, '2025-08-13 22:02:51', '2025-07-30 19:54:06'),
(14, 'bereniceh', '123456', 'gerente.rh@rinorisk.com', 'Berenice Hernandez', 1, '2025-08-07 18:40:03', '2025-07-30 20:35:34'),
(15, 'Valeriag', '123456', 'cultura.organizacional@rinorisk.com', 'Valeria Garcia', 1, NULL, '2025-08-04 19:11:54'),
(16, 'Nora', '123456', 'sincorreo1@rinorisk.com', 'Nora', 1, NULL, '2025-08-06 21:01:29'),
(17, 'ricardov', '123456', 'redes@rinorisk.com', 'Ricardo Vidaño', 1, NULL, '2025-08-06 21:03:34'),
(18, 'israell', '123456', 'publicidad@rinorisk.com', 'Israel Lovato', 1, NULL, '2025-08-06 21:04:17'),
(19, 'arisbethc', '123456', 'supervisor.comercialtj@rinorisk.com', 'Arisbeth Cuevas', 1, '2025-08-07 20:20:36', '2025-08-06 21:05:25'),
(20, 'marlenef', '123456', 'cobranza@rinorisk.com', 'Marlene Flores', 1, NULL, '2025-08-06 21:06:16'),
(21, 'Beatrizi', '123456', 'servicio.vgmtj@rinorisk.com', 'Beatriz Ita', 1, NULL, '2025-08-06 21:07:46'),
(22, 'sofiag', 'Cur7ywhoi#!', 'Sofia@rinorisk.com', 'Sofia Gallardo', 1, '2025-08-12 22:02:04', '2025-08-06 21:09:03'),
(23, 'taniab', '123456', 'auxiliar.inmuebles@rinorisk.com', 'Tania Barboza', 1, NULL, '2025-08-06 21:09:43'),
(24, 'angelicav', '123456', 'gerente.administrativo@rinorisk.com', 'Angelica Vallejo', 1, NULL, '2025-08-06 21:10:52'),
(25, 'yazmint', '123456', 'contabilidad@rinorisk.com', 'Yazmin Trejo', 1, NULL, '2025-08-06 21:11:37'),
(26, 'mitchellq', '123456', 'auxiliar.contable@rinorisk.com', 'Mitchellq', 1, NULL, '2025-08-06 21:12:36'),
(27, 'evelynd', '123456', 'analista.financiero@rinorisk.com', 'Evelyn Duran', 1, NULL, '2025-08-06 21:13:10'),
(28, 'thanias', '123456', 'legal@rinorisk.com', 'Thania Sanchez', 1, NULL, '2025-08-06 21:14:19'),
(29, 'monsed', '123456', 'asistente.legal@rinorisk.com', 'Monserrat Diaz', 1, NULL, '2025-08-06 21:22:48'),
(30, 'Ivanm', 'Jicdx#$23H', 'procesos.operativos@rinorisk.com', 'Ivan Mosqueda', 1, '2025-08-13 21:17:59', '2025-08-06 21:23:48'),
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
(55, 'rosaliae', '123456', 'sincorreo20@rinorisk.com', 'Rosalia Enriquez', 1, NULL, '2025-08-06 23:21:11'),
(56, 'sonial', '123456', 'sincorreo21@rinorisk.com', 'Sonia Lopez', 1, NULL, '2025-08-06 23:24:15'),
(57, 'ivan2', '123456', 'sincorreo23@rinorsik.com', 'Ivan Mosqueda', 1, '2025-08-13 23:13:33', '2025-08-06 23:25:11'),
(58, 'norai', 'Vic$sing#$', 'asistente.direccion@rinorisk.com', 'Nora Imelda Covarrubias', 1, '2025-08-13 21:19:54', '2025-08-06 23:29:06'),
(63, 'ivan3', '123456', 'sincorreo29@rinorisk.com', 'Ivan Mozqueda', 1, '2025-08-13 23:16:02', '2025-08-13 23:14:55');

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;

--
-- AUTO_INCREMENT de la tabla `Projects`
--
ALTER TABLE `Projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `Tasks`
--
ALTER TABLE `Tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT de la tabla `Task_Assignments`
--
ALTER TABLE `Task_Assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;

--
-- AUTO_INCREMENT de la tabla `Task_Attachments`
--
ALTER TABLE `Task_Attachments`
  MODIFY `attachment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `Task_Comments`
--
ALTER TABLE `Task_Comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `Task_History`
--
ALTER TABLE `Task_History`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=292;

--
-- AUTO_INCREMENT de la tabla `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
