-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 11-09-2025 a las 07:53:55
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
  `is_personal` tinyint(1) DEFAULT 0 COMMENT '1 si es tarea personal, 0 si es tarea normal',
  `is_recurrent` tinyint(1) DEFAULT 0 COMMENT 'Indica si la tarea es recurrente',
  `recurrence_type` enum('daily','weekly','monthly') DEFAULT NULL COMMENT 'Tipo de recurrencia: diaria, semanal, mensual',
  `recurrence_start_date` date DEFAULT NULL COMMENT 'Fecha de inicio de la recurrencia',
  `recurrence_end_date` date DEFAULT NULL COMMENT 'Fecha de fin de la recurrencia',
  `last_generated_date` date DEFAULT NULL COMMENT 'Última fecha en que se generó una instancia',
  `parent_recurrent_task_id` int(11) DEFAULT NULL COMMENT 'ID de la tarea recurrente padre (para instancias generadas)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Tasks`
--

INSERT INTO `Tasks` (`task_id`, `parent_task_id`, `task_name`, `description`, `project_id`, `assigned_to_user_id`, `created_by_user_id`, `priority`, `due_date`, `estimated_hours`, `actual_hours`, `completion_percentage`, `automatic_points`, `assigned_percentage`, `color_tag`, `is_subtask`, `subtask_order`, `status`, `is_completed`, `completed_at`, `created_at`, `updated_at`, `is_personal`, `is_recurrent`, `recurrence_type`, `recurrence_start_date`, `recurrence_end_date`, `last_generated_date`, `parent_recurrent_task_id`) VALUES
(266, NULL, 'RECLUTAMIENTO DE AGENTES', 'Agentes Novatos y Consolidados', 44, 22, 22, 'medium', '2025-10-31', NULL, NULL, 30.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-21 07:03:38', '2025-08-21 21:05:40', 0, 0, NULL, NULL, NULL, NULL, NULL),
(267, NULL, 'PERFILES DE ORGANIGRAMA DE AGENCIA', 'DIRECTOR DE AGENCIA \r\nCOORDINADOR DE AGENCIA', 44, 22, 22, 'medium', '2025-10-31', NULL, NULL, 7.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-21 07:12:18', '2025-09-08 21:14:44', 0, 0, NULL, NULL, NULL, NULL, NULL),
(268, NULL, 'DISCIPLINA COMERCIAL', 'Crear una disciplina comercial basada en el exito de la supervision y seguimiento a la fuerza de ventas', 44, 22, 22, 'medium', '2025-10-31', NULL, NULL, 25.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-21 07:20:10', '2025-08-21 16:29:37', 0, 0, NULL, NULL, NULL, NULL, NULL),
(269, NULL, 'ACADEMIA RINO', 'Plataforma de estudio para Agentes', 44, 22, 22, 'medium', '2025-10-31', NULL, NULL, 33.33, 0.00, 0.00, '#3B82F6', 0, 0, 'in_progress', 0, NULL, '2025-08-21 07:24:07', '2025-08-21 16:40:53', 0, 0, NULL, NULL, NULL, NULL, NULL),
(270, NULL, 'CAPACITACION Y RRHH', 'El objetivo es contar un plan de capacitacion y conocimientos para cada uno de los colaboradores que pertenezcan a la agencia.', 44, 22, 22, 'medium', '2025-10-31', NULL, NULL, 12.50, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-21 15:56:50', '2025-08-21 17:26:26', 0, 0, NULL, NULL, NULL, NULL, NULL),
(287, NULL, 'INDUCCIÓN', 'EDITABLE', 51, 40, 14, 'medium', '2025-09-01', NULL, NULL, 3.50, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-27 18:04:38', '2025-09-03 15:37:36', 0, 0, NULL, NULL, NULL, NULL, NULL),
(290, NULL, 'Nueva dinámica carreras', 'Desarrollar el proceso a seguir para las carreras con los integrantes activos de la Rino Estampida.', 53, 44, 13, 'medium', '2025-09-02', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-27 20:11:27', '2025-08-27 22:07:36', 0, 0, NULL, NULL, NULL, NULL, NULL),
(294, NULL, 'Diseños Generales Evento', 'Imagen/linea de diseño oficial del evento social.', 54, 18, 13, 'medium', '2025-09-10', NULL, NULL, 60.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-27 22:29:33', '2025-08-28 16:18:45', 0, 0, NULL, NULL, NULL, NULL, NULL),
(295, NULL, 'Requisición Evento/Entrevista', '', 54, 15, 13, 'medium', '2025-08-27', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-27 23:00:52', '2025-08-27 22:37:34', '2025-08-27 23:56:39', 0, 0, NULL, NULL, NULL, NULL, NULL),
(300, NULL, 'Planeación Evento', '', 54, 15, 13, 'medium', '2025-09-05', NULL, NULL, 58.33, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-27 23:50:04', '2025-09-01 19:48:01', 0, 0, NULL, NULL, NULL, NULL, NULL),
(301, NULL, 'Comunicación del Evento', '', 54, 17, 13, 'medium', '2025-09-05', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-27 23:56:20', '2025-08-28 15:27:57', 0, 0, NULL, NULL, NULL, NULL, NULL),
(302, NULL, 'Documentar Evento', '', 54, 17, 13, 'medium', '2025-09-12', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-27 23:59:53', '2025-08-27 23:59:53', 0, 0, NULL, NULL, NULL, NULL, NULL),
(303, NULL, 'Resultados del Evento', '', 54, 15, 13, 'medium', '2025-09-17', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-28 00:08:29', '2025-08-28 00:08:29', 0, 0, NULL, NULL, NULL, NULL, NULL),
(305, NULL, 'Guia de Producto Daños', 'Generar las herramientas de venta necesarias para comercializar el producto.', 57, 18, 13, 'medium', '2025-08-28', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-28 20:15:00', '2025-08-28 16:30:51', '2025-08-28 20:15:00', 0, 0, NULL, NULL, NULL, NULL, NULL),
(309, NULL, 'Flex Plus', 'Generar las herramientas de venta necesarias para comercializar el producto.', 57, 18, 13, 'medium', '2025-09-30', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-28 20:10:20', '2025-08-28 20:10:20', 0, 0, NULL, NULL, NULL, NULL, NULL),
(310, NULL, 'Flex Plus Integral', 'Generar las herramientas de venta necesarias para comercializar el producto.', 57, 18, 13, 'medium', '2025-09-30', NULL, NULL, 33.33, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-28 20:11:27', '2025-08-29 19:49:30', 0, 0, NULL, NULL, NULL, NULL, NULL),
(311, NULL, 'Planeación de Proyecto', 'Desarrollar todos los requerimientos del proyecto.', 61, 13, 13, 'medium', '2025-08-29', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-29 19:06:20', '2025-08-29 19:02:05', '2025-08-29 19:06:20', 0, 0, NULL, NULL, NULL, NULL, NULL),
(312, NULL, 'Visita y Detección de Necesidades', '', 61, 13, 13, 'medium', '2025-08-29', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-29 19:06:12', '2025-08-29 19:03:06', '2025-08-29 19:06:12', 0, 0, NULL, NULL, NULL, NULL, NULL),
(313, NULL, 'Propuesta de Diseño Oficial', '', 61, 13, 13, 'medium', '2025-08-29', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-29 19:06:15', '2025-08-29 19:03:36', '2025-08-29 19:06:15', 0, 0, NULL, NULL, NULL, NULL, NULL),
(314, NULL, 'Cotizaciones', '', 61, 13, 13, 'medium', '2025-08-29', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-29 19:06:16', '2025-08-29 19:04:06', '2025-08-29 19:06:16', 0, 0, NULL, NULL, NULL, NULL, NULL),
(315, NULL, 'Presupuesto Tentativo del Proyecto', '', 61, 13, 13, 'medium', '2025-08-29', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-29 19:06:17', '2025-08-29 19:04:32', '2025-08-29 19:06:17', 0, 0, NULL, NULL, NULL, NULL, NULL),
(316, NULL, 'Propuesta de Proyecto Final', '', 61, 13, 13, 'medium', '2025-08-29', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-29 19:06:19', '2025-08-29 19:05:18', '2025-08-29 19:06:19', 0, 0, NULL, NULL, NULL, NULL, NULL),
(317, NULL, 'Relación de Gastos a Finanzas', '', 61, 13, 13, 'medium', '2025-08-29', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-29 19:08:40', '2025-08-29 19:06:50', '2025-08-29 19:08:40', 0, 0, NULL, NULL, NULL, NULL, NULL),
(318, NULL, 'Gestión de pago al agente (si aplica)', '', 61, 13, 13, 'medium', '2025-08-29', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-29 19:24:38', '2025-08-29 19:07:13', '2025-08-29 19:24:38', 0, 0, NULL, NULL, NULL, NULL, NULL),
(319, NULL, 'Coordinación de Instalación con Proveedores', '', 61, 13, 13, 'medium', '2025-08-29', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-29 19:08:02', '2025-08-29 19:08:02', 0, 0, NULL, NULL, NULL, NULL, NULL),
(320, NULL, 'Entrega Final de Oficina y Documentación', '', 61, 13, 13, 'medium', '2025-08-29', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-29 19:08:28', '2025-08-29 19:08:28', 0, 0, NULL, NULL, NULL, NULL, NULL),
(321, NULL, 'Visita y Detección de Necesidades', '', 62, 13, 13, 'medium', '2025-08-29', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-08-29 19:25:56', '2025-08-29 19:15:52', '2025-08-29 19:25:56', 0, 0, NULL, NULL, NULL, NULL, NULL),
(322, NULL, 'Planeación de Proyecto', '', 62, 13, 13, 'medium', '2025-08-29', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-29 19:16:32', '2025-08-29 19:16:32', 0, 0, NULL, NULL, NULL, NULL, NULL),
(323, NULL, 'Propuesta de Diseño Oficial', '', 62, 13, 13, 'medium', '2025-08-29', NULL, NULL, 50.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-29 19:16:55', '2025-08-29 19:26:54', 0, 0, NULL, NULL, NULL, NULL, NULL),
(324, NULL, 'Cotizaciones', '', 62, 13, 13, 'medium', '2025-08-29', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-29 19:17:44', '2025-08-29 19:17:44', 0, 0, NULL, NULL, NULL, NULL, NULL),
(325, NULL, 'Presupuesto Tentativo del Proyecto', '', 62, 13, 13, 'medium', '2025-08-29', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-29 19:18:11', '2025-08-29 19:18:11', 0, 0, NULL, NULL, NULL, NULL, NULL),
(326, NULL, 'Propuesta de Proyecto Final', '', 62, 13, 13, 'medium', '2025-08-29', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-29 19:24:06', '2025-08-29 19:24:06', 0, 0, NULL, NULL, NULL, NULL, NULL),
(327, NULL, 'Relación de Gastos a Finanzas', '', 62, 13, 13, 'medium', '2025-08-29', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-29 19:24:23', '2025-08-29 19:24:23', 0, 0, NULL, NULL, NULL, NULL, NULL),
(328, NULL, 'Gestión de pago al agente (si aplica)', '', 62, 13, 13, 'medium', '2025-08-29', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-29 19:24:59', '2025-08-29 19:24:59', 0, 0, NULL, NULL, NULL, NULL, NULL),
(329, NULL, 'Coordinación de Instalación con Proveedores', '', 62, 13, 13, 'medium', '2025-08-29', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-29 19:25:24', '2025-08-29 19:25:24', 0, 0, NULL, NULL, NULL, NULL, NULL),
(330, NULL, 'Entrega Final de Oficina y Documentación', '', 62, 13, 13, 'medium', '2025-08-29', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-29 19:25:45', '2025-08-29 19:25:45', 0, 0, NULL, NULL, NULL, NULL, NULL),
(331, NULL, 'Carrera 20 de Septiembre', '', 53, 44, 13, 'medium', '2025-09-20', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-29 19:35:24', '2025-08-29 19:35:24', 0, 0, NULL, NULL, NULL, NULL, NULL),
(332, NULL, 'Calendario de Comunicación Septiembre', '', 53, 44, 13, 'medium', '2025-09-05', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-08-29 19:38:27', '2025-08-29 19:38:27', 0, 0, NULL, NULL, NULL, NULL, NULL),
(339, NULL, 'Modulo 2: Día 2', '', 46, 40, 40, 'medium', '2025-09-09', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'in_progress', 0, NULL, '2025-09-03 17:40:24', '2025-09-03 17:53:06', 1, 0, NULL, NULL, NULL, NULL, NULL),
(340, NULL, 'Módulo 3: Dia 3', '', 46, 40, 40, 'medium', '2025-09-09', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'in_progress', 0, NULL, '2025-09-03 18:00:13', '2025-09-03 18:22:52', 1, 0, NULL, NULL, NULL, NULL, NULL),
(341, NULL, 'Módulo 4: Día 4', '', 46, 40, 40, 'medium', '2025-09-09', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-03 18:29:50', '2025-09-03 18:29:50', 1, 0, NULL, NULL, NULL, NULL, NULL),
(342, NULL, 'Modulo 5: Día 5', '', 46, 40, 40, 'medium', '2025-09-09', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-03 18:35:59', '2025-09-03 18:35:59', 1, 0, NULL, NULL, NULL, NULL, NULL),
(343, NULL, 'Módulo 6: Día 6', '', 46, 40, 40, 'medium', '2025-09-09', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-03 18:38:31', '2025-09-03 18:38:31', 1, 0, NULL, NULL, NULL, NULL, NULL),
(344, NULL, 'Módulo 7: Seguimiento Comercial', '', 46, 40, 40, 'medium', '2025-10-09', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-03 18:50:28', '2025-09-03 18:50:28', 1, 0, NULL, NULL, NULL, NULL, NULL),
(345, NULL, 'Módulo 8: Capacitación Post Arranque', '', 46, 40, 40, 'medium', '2025-09-22', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-03 19:22:43', '2025-09-03 19:22:43', 1, 0, NULL, NULL, NULL, NULL, NULL),
(346, NULL, 'Módulo 9: Universidad AXA', '', 46, 40, 40, 'medium', '2025-10-10', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-03 19:23:46', '2025-09-03 19:36:09', 1, 0, NULL, NULL, NULL, NULL, NULL),
(347, NULL, 'Módulo 10: Seguimiento General', '', 46, 40, 40, 'medium', '2025-12-09', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-03 19:24:37', '2025-09-03 19:24:37', 1, 0, NULL, NULL, NULL, NULL, NULL),
(348, NULL, 'Módulo 11: Herramientas Digitales', '', 46, 40, 40, 'medium', '2025-12-09', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-03 19:25:07', '2025-09-03 19:25:07', 1, 0, NULL, NULL, NULL, NULL, NULL),
(351, NULL, 'Auditorias', 'Auditar los diferentes procesos en la promotoría', 34, 63, 63, 'medium', '2025-09-05', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-04 18:44:15', '2025-09-04 18:44:15', 0, 0, NULL, NULL, NULL, NULL, NULL),
(352, NULL, 'Auditorias', 'Auditar los diferentes procesos en la promotoría', 34, 63, 63, 'medium', '2025-09-12', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-04 18:44:15', '2025-09-04 18:44:15', 0, 0, NULL, NULL, NULL, NULL, NULL),
(353, NULL, 'Auditorias', 'Auditar los diferentes procesos en la promotoría', 34, 63, 63, 'medium', '2025-09-19', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-04 18:44:15', '2025-09-04 18:44:15', 0, 0, NULL, NULL, NULL, NULL, NULL),
(354, NULL, 'Auditorias', 'Auditar los diferentes procesos en la promotoría', 34, 63, 63, 'medium', '2025-09-26', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-04 18:44:15', '2025-09-04 18:44:15', 0, 0, NULL, NULL, NULL, NULL, NULL),
(466, NULL, 'Reportes', '', 198, 38, 38, 'medium', '2026-01-09', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-05 18:11:27', '2025-09-05 18:17:27', 0, 0, NULL, NULL, NULL, NULL, NULL),
(513, NULL, 'Vuelos CDMX-CANCUN', 'Comprar vuelos cdmx-cancun y viceversa para viaje a Playa del carmen.', 200, 16, 16, 'medium', '2025-09-10', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 0, '2025-09-09 22:56:43', '2025-09-08 20:45:53', '2025-09-09 22:56:43', 1, 0, NULL, NULL, NULL, NULL, NULL),
(514, NULL, 'Pendientes', 'Pago convención Miami\r\nHacer bitácoras CDN / LEGAL / DESARROLLO\r\nCita Psicóloga Ashley\r\nRelación de pendientes de juntas de la semana pasada.', 200, 16, 16, 'medium', '2025-09-08', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 0, '2025-09-09 21:09:09', '2025-09-08 20:48:18', '2025-09-09 21:09:09', 1, 0, NULL, NULL, NULL, NULL, NULL),
(515, NULL, 'Pase de abordar MTY-TJ', 'Sacar pase de abordar y enviarlo a CC con agenda de llegada al hotel en MTY, y reuniones.', 200, 16, 16, 'medium', '2025-09-09', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 0, '2025-09-09 21:07:59', '2025-09-08 20:51:51', '2025-09-09 21:07:59', 1, 0, NULL, NULL, NULL, NULL, NULL),
(524, NULL, 'Pagos IA', 'Checar con Abdiel pago a Carlos y Jhonatan\r\nAvisar a Angelica', 200, 16, 16, 'medium', '2025-09-11', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'in_progress', 0, NULL, '2025-09-08 21:06:33', '2025-09-08 21:06:33', 1, 1, 'weekly', '2025-09-11', '2025-12-18', '2025-09-08', NULL),
(525, NULL, 'Pagos IA (11/09/2025)', 'Checar con Abdiel pago a Carlos y Jhonatan\r\nAvisar a Angelica', 200, 16, 16, 'medium', '2025-09-11', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-08 21:06:33', '2025-09-08 21:06:33', 1, 0, NULL, NULL, NULL, NULL, 524),
(526, NULL, 'Pagos IA (18/09/2025)', 'Checar con Abdiel pago a Carlos y Jhonatan\r\nAvisar a Angelica', 200, 16, 16, 'medium', '2025-09-18', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-08 21:06:33', '2025-09-08 21:06:33', 1, 0, NULL, NULL, NULL, NULL, 524),
(527, NULL, 'Pagos IA (25/09/2025)', 'Checar con Abdiel pago a Carlos y Jhonatan\r\nAvisar a Angelica', 200, 16, 16, 'medium', '2025-09-25', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-08 21:06:33', '2025-09-08 21:06:33', 1, 0, NULL, NULL, NULL, NULL, 524),
(528, NULL, 'Pagos IA (02/10/2025)', 'Checar con Abdiel pago a Carlos y Jhonatan\r\nAvisar a Angelica', 200, 16, 16, 'medium', '2025-10-02', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-08 21:06:33', '2025-09-08 21:06:33', 1, 0, NULL, NULL, NULL, NULL, 524),
(529, NULL, 'Pagos IA (09/10/2025)', 'Checar con Abdiel pago a Carlos y Jhonatan\r\nAvisar a Angelica', 200, 16, 16, 'medium', '2025-10-09', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-08 21:06:33', '2025-09-08 21:06:33', 1, 0, NULL, NULL, NULL, NULL, 524),
(530, NULL, 'Pagos IA (16/10/2025)', 'Checar con Abdiel pago a Carlos y Jhonatan\r\nAvisar a Angelica', 200, 16, 16, 'medium', '2025-10-16', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-08 21:06:33', '2025-09-08 21:06:33', 1, 0, NULL, NULL, NULL, NULL, 524),
(531, NULL, 'Pagos IA (23/10/2025)', 'Checar con Abdiel pago a Carlos y Jhonatan\r\nAvisar a Angelica', 200, 16, 16, 'medium', '2025-10-23', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-08 21:06:33', '2025-09-08 21:06:33', 1, 0, NULL, NULL, NULL, NULL, 524),
(532, NULL, 'Pagos IA (30/10/2025)', 'Checar con Abdiel pago a Carlos y Jhonatan\r\nAvisar a Angelica', 200, 16, 16, 'medium', '2025-10-30', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-08 21:06:33', '2025-09-08 21:06:33', 1, 0, NULL, NULL, NULL, NULL, 524),
(533, NULL, 'Pagos IA (06/11/2025)', 'Checar con Abdiel pago a Carlos y Jhonatan\r\nAvisar a Angelica', 200, 16, 16, 'medium', '2025-11-06', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-08 21:06:33', '2025-09-08 21:06:33', 1, 0, NULL, NULL, NULL, NULL, 524),
(534, NULL, 'Pagos IA (13/11/2025)', 'Checar con Abdiel pago a Carlos y Jhonatan\r\nAvisar a Angelica', 200, 16, 16, 'medium', '2025-11-13', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-08 21:06:33', '2025-09-08 21:06:33', 1, 0, NULL, NULL, NULL, NULL, 524),
(535, NULL, 'Pagos IA (20/11/2025)', 'Checar con Abdiel pago a Carlos y Jhonatan\r\nAvisar a Angelica', 200, 16, 16, 'medium', '2025-11-20', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-08 21:06:33', '2025-09-08 21:06:33', 1, 0, NULL, NULL, NULL, NULL, 524),
(536, NULL, 'Pagos IA (27/11/2025)', 'Checar con Abdiel pago a Carlos y Jhonatan\r\nAvisar a Angelica', 200, 16, 16, 'medium', '2025-11-27', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-08 21:06:33', '2025-09-08 21:06:33', 1, 0, NULL, NULL, NULL, NULL, 524),
(537, NULL, 'Pagos IA (04/12/2025)', 'Checar con Abdiel pago a Carlos y Jhonatan\r\nAvisar a Angelica', 200, 16, 16, 'medium', '2025-12-04', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-08 21:06:33', '2025-09-08 21:06:33', 1, 0, NULL, NULL, NULL, NULL, 524),
(538, NULL, 'Pagos IA (11/12/2025)', 'Checar con Abdiel pago a Carlos y Jhonatan\r\nAvisar a Angelica', 200, 16, 16, 'medium', '2025-12-11', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-08 21:06:33', '2025-09-08 21:06:33', 1, 0, NULL, NULL, NULL, NULL, 524),
(539, NULL, 'Pagos IA (18/12/2025)', 'Checar con Abdiel pago a Carlos y Jhonatan\r\nAvisar a Angelica', 200, 16, 16, 'medium', '2025-12-18', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-08 21:06:33', '2025-09-08 21:06:33', 1, 0, NULL, NULL, NULL, NULL, 524),
(543, NULL, 'Rino Monedas', 'Control y seguimiento de Rino monedas.', 65, 2, 2, 'medium', '2025-12-31', NULL, NULL, 51.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-09-09 21:38:30', '2025-09-09 17:38:03', '2025-09-10 22:09:05', 0, 0, NULL, NULL, NULL, NULL, NULL),
(560, NULL, 'Tarea personal de seguimiento.', 'Esto es una tarea personal de seguimiento', 42, 2, 2, 'medium', '2025-09-10', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 0, '2025-09-10 16:35:14', '2025-09-10 17:34:34', '2025-09-10 22:35:14', 1, 0, NULL, NULL, NULL, NULL, NULL),
(561, NULL, 'Actualizacion final para primera etapa', 'Avance', 66, 2, 2, 'medium', '2025-09-10', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 1, '2025-09-10 18:20:51', '2025-09-10 17:41:43', '2025-09-10 18:20:51', 0, 0, NULL, NULL, NULL, NULL, NULL),
(562, NULL, 'ARQUITECTO NURIEL', 'Sin descripción', 204, 49, 22, 'medium', '2026-03-31', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-10 18:02:51', '2025-09-10 18:44:50', 0, 0, NULL, NULL, NULL, NULL, NULL),
(563, NULL, 'ING. ARMANDO ELECTRICIDAD', '', 204, 49, 22, 'medium', '2026-03-10', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-10 18:51:16', '2025-09-10 18:54:09', 0, 0, NULL, NULL, NULL, NULL, NULL),
(564, NULL, 'Tarea de seguimiento 2', 'Sin descripcion', 66, 2, 2, 'medium', '2025-09-10', NULL, NULL, 80.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-10 18:56:12', '2025-09-10 22:46:55', 0, 0, NULL, NULL, NULL, NULL, NULL),
(565, NULL, 'INSTALACION DE CAMARAS DE SEGURIDAD', 'Reemplazo de camaras existentes e instalacion de nuevas en areas especificas', 204, 49, 22, 'medium', '2025-10-10', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-10 18:56:40', '2025-09-10 19:03:29', 0, 0, NULL, NULL, NULL, NULL, NULL),
(566, NULL, 'PRROVEEDOR DE RIEGO', 'Buscar 3 propuestas para nueva instalacion de riego', 204, 49, 22, 'critical', '2025-09-16', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-10 19:05:33', '2025-09-10 19:06:38', 0, 0, NULL, NULL, NULL, NULL, NULL),
(567, NULL, 'BOMBA DE AGUA PARA FUENTE', 'Instalacion de bomba de agua', 204, 49, 22, 'medium', '2025-09-10', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-10 19:07:45', '2025-09-10 19:07:45', 0, 0, NULL, NULL, NULL, NULL, NULL),
(568, NULL, 'INSTALACION DE GRADAS', 'Entrega e instalacion de gradas en la cancha', 204, 49, 22, 'medium', '2025-09-12', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-10 19:09:00', '2025-09-10 19:09:00', 0, 0, NULL, NULL, NULL, NULL, NULL),
(569, NULL, 'PINTURA DE CANCHA DE TENIS', 'Revisar temas de garantia con el proveedor', 204, 49, 22, 'medium', '2025-09-30', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-10 19:10:32', '2025-09-10 19:10:32', 0, 0, NULL, NULL, NULL, NULL, NULL),
(570, NULL, 'ALBERCA', 'FUNCIONAMIENTO TOTAL DE ALBERCA', 204, 49, 22, 'critical', '2025-09-19', NULL, NULL, 1.88, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-10 19:16:59', '2025-09-10 20:56:08', 0, 0, NULL, NULL, NULL, NULL, NULL),
(571, NULL, 'NUEVAS PLANTAS', 'Siembra de nuevas plantas', 204, 49, 22, 'medium', '2025-09-21', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-10 19:49:56', '2025-09-10 19:49:56', 0, 0, NULL, NULL, NULL, NULL, NULL),
(574, NULL, 'Buscar server', 'Sin descripcion', 64, 2, 2, 'medium', '2025-09-11', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 0, '2025-09-10 22:50:39', '2025-09-10 20:18:22', '2025-09-11 04:50:39', 0, 0, NULL, NULL, NULL, NULL, NULL),
(575, NULL, 'Politicas de almacenamiento', 'Sin descripción', 64, 2, 2, 'medium', '2025-09-10', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-10 20:19:26', '2025-09-10 20:19:26', 0, 0, NULL, NULL, NULL, NULL, NULL),
(576, NULL, 'Hacer QA', 'Sin descripción', 64, 4, 2, 'medium', '2025-09-10', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-10 20:20:25', '2025-09-10 20:20:25', 0, 0, NULL, NULL, NULL, NULL, NULL),
(577, NULL, 'Pagos IA', 'Pagos semanales correspondientes por avance de Jhonatan y Carlos.', 34, 2, 58, 'medium', '2025-09-11', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-10 22:48:59', '2025-09-10 22:48:59', 0, 0, NULL, NULL, NULL, NULL, NULL),
(578, NULL, 'Pagos IA', 'Pagos semanales correspondientes por avance de Jhonatan y Carlos.', 34, 2, 58, 'medium', '2025-09-18', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-10 22:48:59', '2025-09-10 22:48:59', 0, 0, NULL, NULL, NULL, NULL, NULL),
(579, NULL, 'Pagos IA', 'Pagos semanales correspondientes por avance de Jhonatan y Carlos.', 34, 2, 58, 'medium', '2025-09-25', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-10 22:48:59', '2025-09-10 22:48:59', 0, 0, NULL, NULL, NULL, NULL, NULL),
(580, NULL, 'DESARROLLAR PROCESO DE TARJETAS VIATICOS', '', 205, 30, 1, 'medium', '2025-09-11', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-10 23:46:52', '2025-09-10 23:46:52', 0, 0, NULL, NULL, NULL, NULL, NULL),
(581, NULL, 'EJECUTAR NUEVA DINAMICA DE REUNIONES', '', 205, 58, 1, 'medium', '2025-09-15', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-10 23:51:26', '2025-09-10 23:51:26', 0, 0, NULL, NULL, NULL, NULL, NULL),
(582, NULL, 'afdasfasdf', 'asdfdasfasd', 202, 2, 2, 'medium', '2025-09-11', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 0, '2025-09-10 23:08:09', '2025-09-11 05:00:03', '2025-09-11 05:08:09', 0, 0, NULL, NULL, NULL, NULL, NULL),
(583, NULL, 'fgdhdfghdfg', 'gfdsgsdfgsdf', 202, 2, 2, 'high', '2025-09-17', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 0, '2025-09-10 23:08:13', '2025-09-11 05:00:58', '2025-09-11 05:08:13', 0, 0, NULL, NULL, NULL, NULL, NULL),
(584, NULL, 'gs2', 'sadfsadf', 202, 2, 2, 'medium', '2025-09-11', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 0, '2025-09-10 23:08:10', '2025-09-11 05:06:16', '2025-09-11 05:08:10', 0, 0, NULL, NULL, NULL, NULL, NULL),
(585, NULL, 'fasdf', 'fsadf32', 202, 2, 2, 'medium', '2025-09-11', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 0, '2025-09-10 23:12:51', '2025-09-11 05:09:04', '2025-09-11 05:12:51', 0, 0, NULL, NULL, NULL, NULL, NULL),
(586, NULL, 'xcvb2', 'sdfa', 202, 2, 2, 'medium', '2025-09-11', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 0, '2025-09-10 23:12:52', '2025-09-11 05:09:48', '2025-09-11 05:12:52', 0, 0, NULL, NULL, NULL, NULL, NULL),
(587, NULL, 'sadfasdf', 'sdfasdfsadf', 42, 2, 2, 'medium', '2025-09-11', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 0, '2025-09-10 23:25:17', '2025-09-11 05:24:27', '2025-09-11 05:25:17', 0, 0, NULL, NULL, NULL, NULL, NULL),
(588, NULL, 'Tarea de modal', 'Esto es una tarea de modal', 42, 2, 2, 'medium', '2025-09-11', NULL, NULL, 100.00, 0.00, 0.00, '#3B82F6', 0, 0, 'completed', 0, '2025-09-10 23:31:24', '2025-09-11 05:25:38', '2025-09-11 05:31:24', 0, 0, NULL, NULL, NULL, NULL, NULL),
(590, NULL, 'Edicion tarea modal', 'asdfasd', 42, 2, 2, 'medium', '2025-09-11', NULL, NULL, 50.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-11 06:00:44', '2025-09-11 06:49:04', 0, 0, NULL, NULL, NULL, NULL, NULL),
(591, NULL, 'Correjir WS', 'PARA  dos desarrollos', 63, 2, 2, 'medium', '2025-09-11', NULL, NULL, 44.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-11 06:08:13', '2025-09-11 06:09:32', 0, 0, NULL, NULL, NULL, NULL, NULL),
(592, NULL, 'Prueba de Asignacion', 'dfasdfasd', 34, 2, 1, 'medium', '2025-09-12', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-11 06:56:48', '2025-09-11 06:56:48', 0, 0, NULL, NULL, NULL, NULL, NULL),
(593, NULL, 'Prueba de Asignacion', 'dfasdfasd', 34, 2, 1, 'medium', '2025-09-19', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-11 06:56:48', '2025-09-11 06:56:48', 0, 0, NULL, NULL, NULL, NULL, NULL),
(594, NULL, 'Prueba de Asignacion', 'dfasdfasd', 34, 2, 1, 'medium', '2025-09-26', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-11 06:56:48', '2025-09-11 06:56:48', 0, 0, NULL, NULL, NULL, NULL, NULL),
(595, NULL, 'thrtgsesdfgsdfg', 'fgsdfgdsf', 42, 2, 2, 'medium', '2025-09-11', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-11 07:05:08', '2025-09-11 07:05:08', 0, 0, NULL, NULL, NULL, NULL, NULL),
(596, NULL, 'asdfsadfasd', 'sadfsadfsadfsadf', 42, 2, 2, 'medium', '2025-09-11', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-11 07:05:20', '2025-09-11 07:05:20', 0, 0, NULL, NULL, NULL, NULL, NULL),
(597, NULL, 'sdfasdfasdf', 'sadfsadfasdf', 42, 2, 2, 'medium', '2025-09-11', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-11 07:05:30', '2025-09-11 07:05:30', 0, 0, NULL, NULL, NULL, NULL, NULL),
(598, NULL, 'asdfdsafasd', 'asdfsdafasdf', 42, 2, 2, 'medium', '2025-09-11', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-11 07:05:46', '2025-09-11 07:05:46', 0, 0, NULL, NULL, NULL, NULL, NULL),
(599, NULL, 'asdfdsafsadfaerter', 'dfdsafadsfsda', 42, 2, 2, 'medium', '2025-09-11', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-11 07:06:02', '2025-09-11 07:06:02', 0, 0, NULL, NULL, NULL, NULL, NULL),
(600, NULL, 'rev', 'sfdsdafs', 35, 2, 1, 'medium', '2025-09-11', NULL, NULL, 0.00, 0.00, 0.00, '#3B82F6', 0, 0, 'pending', 0, NULL, '2025-09-11 07:11:33', '2025-09-11 07:11:33', 0, 0, NULL, NULL, NULL, NULL, NULL);

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

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Tasks`
--
ALTER TABLE `Tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `idx_parent_recurrent` (`parent_recurrent_task_id`),
  ADD KEY `idx_recurrent_tasks` (`is_recurrent`,`recurrence_type`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Tasks`
--
ALTER TABLE `Tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=601;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
