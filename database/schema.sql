-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-10-2026 a las 17:43:36
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `chvb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bolsillos`
--

CREATE TABLE `bolsillos` (
  `id` int(11) NOT NULL,
  `cedula_empleado` varchar(10) NOT NULL,
  `seccion` enum('hoja_de_vida','documentos_contractuales') NOT NULL,
  `nombre` varchar(100) NOT NULL COMMENT 'slug interno, ej: certificados',
  `nombre_completo` varchar(150) NOT NULL COMMENT 'nombre visible, ej: Certificados',
  `orden` int(11) NOT NULL DEFAULT 0,
  `alarma_tipo` enum('1m','2m','6m','1a','custom') DEFAULT NULL,
  `alarma_fecha` date DEFAULT NULL,
  `alarma_activa` tinyint(1) DEFAULT 0,
  `alarma_valor` int(11) DEFAULT NULL COMMENT 'Cantidad numérica para alarma personalizada',
  `alarma_unidad` enum('dias','meses','anios') DEFAULT NULL COMMENT 'Unidad para alarma personalizada',
  `alarma_fecha_inicio` date DEFAULT NULL COMMENT 'Fecha desde la cual se cuenta el plazo',
  `alarma_dias_aviso` int(11) DEFAULT 35 COMMENT 'Días de anticipación para la alerta amarilla'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `bolsillos`
--

INSERT INTO `bolsillos` (`id`, `cedula_empleado`, `seccion`, `nombre`, `nombre_completo`, `orden`, `alarma_tipo`, `alarma_fecha`, `alarma_activa`, `alarma_valor`, `alarma_unidad`, `alarma_fecha_inicio`, `alarma_dias_aviso`) VALUES
(1, '1005719736', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(2, '1005719736', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(3, '1005719736', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(4, '1005719736', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(5, '1005719736', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(6, '1005719736', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(7, '1005719736', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(8, '1005719736', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(9, '1005719736', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(10, '1005719736', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(11, '1005719736', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(12, '1005719736', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(13, '1005719736', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(14, '1005719736', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(15, '1005719736', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(16, '1005719736', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(17, '1005719736', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(18, '1005719736', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(19, '1005719736', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(20, '1005719736', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(21, '1005719736', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(22, '1005719736', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(23, '1005719736', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(24, '1005719736', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(25, '1005719736', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(26, '1005719736', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(27, '1005719736', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(28, '1005719736', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(29, '1005719736', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(30, '1005719736', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(31, '1005719736', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(32, '1006554686', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(33, '1006554686', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(34, '1006554686', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(35, '1006554686', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(36, '1006554686', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(37, '1006554686', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(38, '1006554686', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(39, '1006554686', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(40, '1006554686', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(41, '1006554686', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(42, '1006554686', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(43, '1006554686', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(44, '1006554686', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(45, '1006554686', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(46, '1006554686', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(47, '1006554686', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(48, '1006554686', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(49, '1006554686', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(50, '1006554686', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(51, '1006554686', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(52, '1006554686', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(53, '1006554686', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(54, '1006554686', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(55, '1006554686', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(56, '1006554686', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(57, '1006554686', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(58, '1006554686', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(59, '1006554686', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(60, '1006554686', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(61, '1006554686', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(62, '1006554686', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(63, '1006555204', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(64, '1006555204', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(65, '1006555204', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(66, '1006555204', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(67, '1006555204', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(68, '1006555204', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(69, '1006555204', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(70, '1006555204', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(71, '1006555204', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(72, '1006555204', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(73, '1006555204', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(74, '1006555204', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(75, '1006555204', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(76, '1006555204', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(77, '1006555204', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(78, '1006555204', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(79, '1006555204', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(80, '1006555204', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(81, '1006555204', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(82, '1006555204', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(83, '1006555204', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(84, '1006555204', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(85, '1006555204', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(86, '1006555204', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(87, '1006555204', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(88, '1006555204', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(89, '1006555204', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(90, '1006555204', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(91, '1006555204', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(92, '1006555204', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(93, '1006555204', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(94, '1006555838', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(95, '1006555838', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(96, '1006555838', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(97, '1006555838', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(98, '1006555838', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(99, '1006555838', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(100, '1006555838', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(101, '1006555838', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(102, '1006555838', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(103, '1006555838', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(104, '1006555838', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(105, '1006555838', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(106, '1006555838', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(107, '1006555838', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(108, '1006555838', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(109, '1006555838', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(110, '1006555838', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(111, '1006555838', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(112, '1006555838', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(113, '1006555838', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(114, '1006555838', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(115, '1006555838', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(116, '1006555838', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(117, '1006555838', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(118, '1006555838', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(119, '1006555838', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(120, '1006555838', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(121, '1006555838', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(122, '1006555838', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(123, '1006555838', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(124, '1006555838', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(125, '1006556137', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(126, '1006556137', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(127, '1006556137', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(128, '1006556137', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(129, '1006556137', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(130, '1006556137', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(131, '1006556137', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(132, '1006556137', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(133, '1006556137', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(134, '1006556137', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(135, '1006556137', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(136, '1006556137', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(137, '1006556137', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(138, '1006556137', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(139, '1006556137', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(140, '1006556137', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(141, '1006556137', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(142, '1006556137', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(143, '1006556137', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(144, '1006556137', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(145, '1006556137', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(146, '1006556137', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(147, '1006556137', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(148, '1006556137', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(149, '1006556137', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(150, '1006556137', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(151, '1006556137', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(152, '1006556137', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(153, '1006556137', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(154, '1006556137', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(155, '1006556137', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(156, '1006556671', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(157, '1006556671', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(158, '1006556671', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(159, '1006556671', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(160, '1006556671', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(161, '1006556671', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(162, '1006556671', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(163, '1006556671', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(164, '1006556671', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(165, '1006556671', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(166, '1006556671', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(167, '1006556671', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(168, '1006556671', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(169, '1006556671', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(170, '1006556671', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(171, '1006556671', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(172, '1006556671', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(173, '1006556671', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(174, '1006556671', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(175, '1006556671', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(176, '1006556671', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(177, '1006556671', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(178, '1006556671', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(179, '1006556671', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(180, '1006556671', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(181, '1006556671', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(182, '1006556671', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(183, '1006556671', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(184, '1006556671', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(185, '1006556671', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(186, '1006556671', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(187, '1006636306', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(188, '1006636306', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(189, '1006636306', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(190, '1006636306', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(191, '1006636306', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(192, '1006636306', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(193, '1006636306', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(194, '1006636306', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(195, '1006636306', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(196, '1006636306', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(197, '1006636306', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(198, '1006636306', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(199, '1006636306', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(200, '1006636306', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(201, '1006636306', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(202, '1006636306', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(203, '1006636306', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(204, '1006636306', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(205, '1006636306', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(206, '1006636306', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(207, '1006636306', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(208, '1006636306', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(209, '1006636306', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(210, '1006636306', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(211, '1006636306', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(212, '1006636306', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(213, '1006636306', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(214, '1006636306', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(215, '1006636306', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(216, '1006636306', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(217, '1006636306', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(218, '1007013786', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(219, '1007013786', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(220, '1007013786', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(221, '1007013786', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(222, '1007013786', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(223, '1007013786', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(224, '1007013786', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(225, '1007013786', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(226, '1007013786', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(227, '1007013786', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(228, '1007013786', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(229, '1007013786', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(230, '1007013786', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(231, '1007013786', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(232, '1007013786', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(233, '1007013786', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(234, '1007013786', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(235, '1007013786', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(236, '1007013786', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(237, '1007013786', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(238, '1007013786', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(239, '1007013786', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(240, '1007013786', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(241, '1007013786', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(242, '1007013786', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(243, '1007013786', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(244, '1007013786', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(245, '1007013786', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(246, '1007013786', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(247, '1007013786', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(248, '1007013786', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(249, '1007703611', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(250, '1007703611', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(251, '1007703611', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(252, '1007703611', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(253, '1007703611', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(254, '1007703611', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(255, '1007703611', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(256, '1007703611', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(257, '1007703611', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(258, '1007703611', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(259, '1007703611', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(260, '1007703611', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(261, '1007703611', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(262, '1007703611', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(263, '1007703611', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(264, '1007703611', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(265, '1007703611', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(266, '1007703611', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(267, '1007703611', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(268, '1007703611', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(269, '1007703611', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(270, '1007703611', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(271, '1007703611', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(272, '1007703611', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(273, '1007703611', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(274, '1007703611', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(275, '1007703611', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(276, '1007703611', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(277, '1007703611', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(278, '1007703611', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(279, '1007703611', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(280, '1019024577', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(281, '1019024577', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(282, '1019024577', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(283, '1019024577', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(284, '1019024577', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(285, '1019024577', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(286, '1019024577', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(287, '1019024577', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(288, '1019024577', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(289, '1019024577', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(290, '1019024577', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(291, '1019024577', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(292, '1019024577', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(293, '1019024577', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(294, '1019024577', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(295, '1019024577', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(296, '1019024577', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(297, '1019024577', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(298, '1019024577', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(299, '1019024577', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(300, '1019024577', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(301, '1019024577', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(302, '1019024577', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(303, '1019024577', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(304, '1019024577', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(305, '1019024577', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(306, '1019024577', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(307, '1019024577', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(308, '1019024577', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(309, '1019024577', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(310, '1019024577', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(311, '1029643799', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(312, '1029643799', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(313, '1029643799', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(314, '1029643799', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(315, '1029643799', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(316, '1029643799', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(317, '1029643799', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(318, '1029643799', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(319, '1029643799', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(320, '1029643799', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(321, '1029643799', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(322, '1029643799', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(323, '1029643799', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(324, '1029643799', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(325, '1029643799', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(326, '1029643799', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(327, '1029643799', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(328, '1029643799', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(329, '1029643799', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(330, '1029643799', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(331, '1029643799', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(332, '1029643799', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(333, '1029643799', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(334, '1029643799', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(335, '1029643799', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(336, '1029643799', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(337, '1029643799', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(338, '1029643799', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(339, '1029643799', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(340, '1029643799', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(341, '1029643799', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(342, '1029661794', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(343, '1029661794', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(344, '1029661794', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(345, '1029661794', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(346, '1029661794', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(347, '1029661794', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(348, '1029661794', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(349, '1029661794', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(350, '1029661794', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(351, '1029661794', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(352, '1029661794', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(353, '1029661794', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(354, '1029661794', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(355, '1029661794', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(356, '1029661794', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(357, '1029661794', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(358, '1029661794', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(359, '1029661794', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(360, '1029661794', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(361, '1029661794', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(362, '1029661794', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(363, '1029661794', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(364, '1029661794', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(365, '1029661794', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(366, '1029661794', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(367, '1029661794', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(368, '1029661794', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(369, '1029661794', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(370, '1029661794', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(371, '1029661794', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(372, '1029661794', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(373, '1115911058', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(374, '1115911058', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(375, '1115911058', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(376, '1115911058', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(377, '1115911058', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(378, '1115911058', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(379, '1115911058', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(380, '1115911058', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(381, '1115911058', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(382, '1115911058', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(383, '1115911058', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(384, '1115911058', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(385, '1115911058', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(386, '1115911058', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(387, '1115911058', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(388, '1115911058', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(389, '1115911058', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(390, '1115911058', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(391, '1115911058', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(392, '1115911058', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(393, '1115911058', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(394, '1115911058', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(395, '1115911058', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(396, '1115911058', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(397, '1115911058', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(398, '1115911058', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(399, '1115911058', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(400, '1115911058', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(401, '1115911058', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(402, '1115911058', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(403, '1115911058', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35);
INSERT INTO `bolsillos` (`id`, `cedula_empleado`, `seccion`, `nombre`, `nombre_completo`, `orden`, `alarma_tipo`, `alarma_fecha`, `alarma_activa`, `alarma_valor`, `alarma_unidad`, `alarma_fecha_inicio`, `alarma_dias_aviso`) VALUES
(404, '1115913555', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(405, '1115913555', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(406, '1115913555', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(407, '1115913555', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(408, '1115913555', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(409, '1115913555', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(410, '1115913555', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(411, '1115913555', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(412, '1115913555', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(413, '1115913555', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(414, '1115913555', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(415, '1115913555', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(416, '1115913555', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(417, '1115913555', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(418, '1115913555', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(419, '1115913555', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(420, '1115913555', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(421, '1115913555', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(422, '1115913555', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(423, '1115913555', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(424, '1115913555', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(425, '1115913555', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(426, '1115913555', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(427, '1115913555', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(428, '1115913555', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(429, '1115913555', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(430, '1115913555', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(431, '1115913555', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(432, '1115913555', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(433, '1115913555', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(434, '1115913555', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(435, '1116043143', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(436, '1116043143', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(437, '1116043143', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(438, '1116043143', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(439, '1116043143', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(440, '1116043143', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(441, '1116043143', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(442, '1116043143', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(443, '1116043143', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(444, '1116043143', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(445, '1116043143', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(446, '1116043143', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(447, '1116043143', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(448, '1116043143', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(449, '1116043143', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(450, '1116043143', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(451, '1116043143', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(452, '1116043143', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(453, '1116043143', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(454, '1116043143', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(455, '1116043143', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(456, '1116043143', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(457, '1116043143', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(458, '1116043143', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(459, '1116043143', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(460, '1116043143', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(461, '1116043143', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(462, '1116043143', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(463, '1116043143', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(464, '1116043143', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(465, '1116043143', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(466, '1116552720', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(467, '1116552720', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(468, '1116552720', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(469, '1116552720', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(470, '1116552720', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(471, '1116552720', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(472, '1116552720', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(473, '1116552720', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(474, '1116552720', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(475, '1116552720', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(476, '1116552720', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(477, '1116552720', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(478, '1116552720', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(479, '1116552720', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(480, '1116552720', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(481, '1116552720', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(482, '1116552720', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(483, '1116552720', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(484, '1116552720', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(485, '1116552720', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(486, '1116552720', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(487, '1116552720', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(488, '1116552720', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(489, '1116552720', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(490, '1116552720', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(491, '1116552720', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(492, '1116552720', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(493, '1116552720', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(494, '1116552720', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(495, '1116552720', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(496, '1116552720', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(497, '1116992974', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(498, '1116992974', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(499, '1116992974', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(500, '1116992974', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(501, '1116992974', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(502, '1116992974', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(503, '1116992974', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(504, '1116992974', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(505, '1116992974', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(506, '1116992974', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(507, '1116992974', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(508, '1116992974', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(509, '1116992974', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(510, '1116992974', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(511, '1116992974', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(512, '1116992974', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(513, '1116992974', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(514, '1116992974', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(515, '1116992974', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(516, '1116992974', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(517, '1116992974', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(518, '1116992974', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(519, '1116992974', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(520, '1116992974', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(521, '1116992974', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(522, '1116992974', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(523, '1116992974', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(524, '1116992974', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(525, '1116992974', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(526, '1116992974', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(527, '1116992974', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(528, '1118198423', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(529, '1118198423', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(530, '1118198423', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(531, '1118198423', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(532, '1118198423', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(533, '1118198423', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(534, '1118198423', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(535, '1118198423', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(536, '1118198423', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(537, '1118198423', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(538, '1118198423', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(539, '1118198423', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(540, '1118198423', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(541, '1118198423', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(542, '1118198423', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(543, '1118198423', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(544, '1118198423', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(545, '1118198423', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(546, '1118198423', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(547, '1118198423', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(548, '1118198423', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(549, '1118198423', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(550, '1118198423', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(551, '1118198423', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(552, '1118198423', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(553, '1118198423', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(554, '1118198423', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(555, '1118198423', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(556, '1118198423', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(557, '1118198423', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(558, '1118198423', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(559, '1118529611', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(560, '1118529611', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(561, '1118529611', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(562, '1118529611', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(563, '1118529611', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(564, '1118529611', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(565, '1118529611', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(566, '1118529611', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(567, '1118529611', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(568, '1118529611', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(569, '1118529611', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(570, '1118529611', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(571, '1118529611', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(572, '1118529611', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(573, '1118529611', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(574, '1118529611', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(575, '1118529611', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(576, '1118529611', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(577, '1118529611', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(578, '1118529611', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(579, '1118529611', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(580, '1118529611', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(581, '1118529611', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(582, '1118529611', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(583, '1118529611', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(584, '1118529611', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(585, '1118529611', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(586, '1118529611', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(587, '1118529611', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(588, '1118529611', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(589, '1118529611', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(590, '1118530819', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(591, '1118530819', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(592, '1118530819', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(593, '1118530819', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(594, '1118530819', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(595, '1118530819', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(596, '1118530819', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(597, '1118530819', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(598, '1118530819', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(599, '1118530819', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(600, '1118530819', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(601, '1118530819', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(602, '1118530819', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(603, '1118530819', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(604, '1118530819', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(605, '1118530819', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(606, '1118530819', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(607, '1118530819', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(608, '1118530819', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(609, '1118530819', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(610, '1118530819', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(611, '1118530819', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(612, '1118530819', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(613, '1118530819', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(614, '1118530819', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(615, '1118530819', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(616, '1118530819', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(617, '1118530819', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(618, '1118530819', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(619, '1118530819', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(620, '1118530819', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(621, '1118534974', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(622, '1118534974', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(623, '1118534974', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(624, '1118534974', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(625, '1118534974', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(626, '1118534974', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(627, '1118534974', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(628, '1118534974', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(629, '1118534974', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(630, '1118534974', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(631, '1118534974', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(632, '1118534974', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(633, '1118534974', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(634, '1118534974', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(635, '1118534974', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(636, '1118534974', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(637, '1118534974', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(638, '1118534974', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(639, '1118534974', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(640, '1118534974', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(641, '1118534974', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(642, '1118534974', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(643, '1118534974', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(644, '1118534974', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(645, '1118534974', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(646, '1118534974', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(647, '1118534974', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(648, '1118534974', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(649, '1118534974', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(650, '1118534974', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(651, '1118534974', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(652, '1118536550', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(653, '1118536550', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(654, '1118536550', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(655, '1118536550', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(656, '1118536550', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(657, '1118536550', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(658, '1118536550', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(659, '1118536550', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(660, '1118536550', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(661, '1118536550', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(662, '1118536550', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(663, '1118536550', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(664, '1118536550', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(665, '1118536550', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(666, '1118536550', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(667, '1118536550', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(668, '1118536550', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(669, '1118536550', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(670, '1118536550', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(671, '1118536550', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(672, '1118536550', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(673, '1118536550', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(674, '1118536550', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(675, '1118536550', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(676, '1118536550', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(677, '1118536550', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(678, '1118536550', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(679, '1118536550', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(680, '1118536550', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(681, '1118536550', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(682, '1118536550', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(683, '1118543385', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(684, '1118543385', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(685, '1118543385', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(686, '1118543385', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(687, '1118543385', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(688, '1118543385', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(689, '1118543385', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(690, '1118543385', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(691, '1118543385', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(692, '1118543385', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(693, '1118543385', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(694, '1118543385', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(695, '1118543385', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(696, '1118543385', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(697, '1118543385', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(698, '1118543385', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(699, '1118543385', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(700, '1118543385', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(701, '1118543385', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(702, '1118543385', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(703, '1118543385', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(704, '1118543385', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(705, '1118543385', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(706, '1118543385', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(707, '1118543385', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(708, '1118543385', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(709, '1118543385', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(710, '1118543385', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(711, '1118543385', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(712, '1118543385', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(713, '1118543385', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(714, '1118544837', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(715, '1118544837', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(716, '1118544837', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(717, '1118544837', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(718, '1118544837', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(719, '1118544837', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(720, '1118544837', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(721, '1118544837', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(722, '1118544837', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(723, '1118544837', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(724, '1118544837', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(725, '1118544837', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(726, '1118544837', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(727, '1118544837', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(728, '1118544837', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(729, '1118544837', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(730, '1118544837', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(731, '1118544837', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(732, '1118544837', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(733, '1118544837', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(734, '1118544837', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(735, '1118544837', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(736, '1118544837', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(737, '1118544837', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(738, '1118544837', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(739, '1118544837', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(740, '1118544837', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(741, '1118544837', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(742, '1118544837', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(743, '1118544837', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(744, '1118544837', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(745, '1118547243', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(746, '1118547243', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(747, '1118547243', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(748, '1118547243', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(749, '1118547243', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(750, '1118547243', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(751, '1118547243', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(752, '1118547243', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(753, '1118547243', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(754, '1118547243', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(755, '1118547243', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(756, '1118547243', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(757, '1118547243', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(758, '1118547243', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(759, '1118547243', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(760, '1118547243', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(761, '1118547243', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(762, '1118547243', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(763, '1118547243', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(764, '1118547243', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(765, '1118547243', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(766, '1118547243', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(767, '1118547243', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(768, '1118547243', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(769, '1118547243', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(770, '1118547243', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(771, '1118547243', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(772, '1118547243', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(773, '1118547243', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(774, '1118547243', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(775, '1118547243', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(776, '1118547356', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(777, '1118547356', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(778, '1118547356', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(779, '1118547356', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(780, '1118547356', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(781, '1118547356', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(782, '1118547356', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(783, '1118547356', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(784, '1118547356', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(785, '1118547356', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(786, '1118547356', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(787, '1118547356', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(788, '1118547356', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(789, '1118547356', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(790, '1118547356', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(791, '1118547356', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(792, '1118547356', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(793, '1118547356', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(794, '1118547356', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(795, '1118547356', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(796, '1118547356', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(797, '1118547356', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(798, '1118547356', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(799, '1118547356', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(800, '1118547356', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(801, '1118547356', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(802, '1118547356', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(803, '1118547356', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(804, '1118547356', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(805, '1118547356', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35);
INSERT INTO `bolsillos` (`id`, `cedula_empleado`, `seccion`, `nombre`, `nombre_completo`, `orden`, `alarma_tipo`, `alarma_fecha`, `alarma_activa`, `alarma_valor`, `alarma_unidad`, `alarma_fecha_inicio`, `alarma_dias_aviso`) VALUES
(806, '1118547356', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(807, '1118550799', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(808, '1118550799', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(809, '1118550799', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(810, '1118550799', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(811, '1118550799', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(812, '1118550799', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(813, '1118550799', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(814, '1118550799', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(815, '1118550799', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(816, '1118550799', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(817, '1118550799', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(818, '1118550799', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(819, '1118550799', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(820, '1118550799', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(821, '1118550799', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(822, '1118550799', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(823, '1118550799', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(824, '1118550799', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(825, '1118550799', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(826, '1118550799', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(827, '1118550799', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(828, '1118550799', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(829, '1118550799', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(830, '1118550799', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(831, '1118550799', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(832, '1118550799', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(833, '1118550799', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(834, '1118550799', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(835, '1118550799', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(836, '1118550799', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(837, '1118550799', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(838, '1118555586', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(839, '1118555586', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(840, '1118555586', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(841, '1118555586', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(842, '1118555586', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(843, '1118555586', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(844, '1118555586', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(845, '1118555586', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(846, '1118555586', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(847, '1118555586', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(848, '1118555586', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(849, '1118555586', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(850, '1118555586', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(851, '1118555586', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(852, '1118555586', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(853, '1118555586', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(854, '1118555586', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(855, '1118555586', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(856, '1118555586', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(857, '1118555586', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(858, '1118555586', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(859, '1118555586', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(860, '1118555586', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(861, '1118555586', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(862, '1118555586', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(863, '1118555586', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(864, '1118555586', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(865, '1118555586', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(866, '1118555586', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(867, '1118555586', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(868, '1118555586', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(869, '1118564532', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(870, '1118564532', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(871, '1118564532', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(872, '1118564532', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(873, '1118564532', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(874, '1118564532', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(875, '1118564532', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(876, '1118564532', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(877, '1118564532', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(878, '1118564532', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(879, '1118564532', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(880, '1118564532', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(881, '1118564532', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(882, '1118564532', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(883, '1118564532', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(884, '1118564532', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(885, '1118564532', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(886, '1118564532', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(887, '1118564532', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(888, '1118564532', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(889, '1118564532', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(890, '1118564532', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(891, '1118564532', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(892, '1118564532', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(893, '1118564532', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(894, '1118564532', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(895, '1118564532', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(896, '1118564532', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(897, '1118564532', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(898, '1118564532', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(899, '1118564532', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(900, '1118564997', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(901, '1118564997', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(902, '1118564997', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(903, '1118564997', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(904, '1118564997', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(905, '1118564997', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(906, '1118564997', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(907, '1118564997', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(908, '1118564997', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(909, '1118564997', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(910, '1118564997', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(911, '1118564997', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(912, '1118564997', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(913, '1118564997', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(914, '1118564997', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(915, '1118564997', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(916, '1118564997', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(917, '1118564997', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(918, '1118564997', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(919, '1118564997', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(920, '1118564997', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(921, '1118564997', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(922, '1118564997', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(923, '1118564997', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(924, '1118564997', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(925, '1118564997', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(926, '1118564997', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(927, '1118564997', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(928, '1118564997', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(929, '1118564997', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(930, '1118564997', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(931, '1118565906', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(932, '1118565906', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(933, '1118565906', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(934, '1118565906', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(935, '1118565906', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(936, '1118565906', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(937, '1118565906', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(938, '1118565906', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(939, '1118565906', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(940, '1118565906', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(941, '1118565906', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(942, '1118565906', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(943, '1118565906', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(944, '1118565906', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(945, '1118565906', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(946, '1118565906', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(947, '1118565906', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(948, '1118565906', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(949, '1118565906', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(950, '1118565906', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(951, '1118565906', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(952, '1118565906', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(953, '1118565906', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(954, '1118565906', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(955, '1118565906', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(956, '1118565906', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(957, '1118565906', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(958, '1118565906', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(959, '1118565906', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(960, '1118565906', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(961, '1118565906', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(962, '1118565958', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(963, '1118565958', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(964, '1118565958', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(965, '1118565958', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(966, '1118565958', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(967, '1118565958', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(968, '1118565958', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(969, '1118565958', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(970, '1118565958', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(971, '1118565958', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(972, '1118565958', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(973, '1118565958', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(974, '1118565958', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(975, '1118565958', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(976, '1118565958', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(977, '1118565958', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(978, '1118565958', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(979, '1118565958', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(980, '1118565958', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(981, '1118565958', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(982, '1118565958', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(983, '1118565958', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(984, '1118565958', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(985, '1118565958', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(986, '1118565958', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(987, '1118565958', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(988, '1118565958', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(989, '1118565958', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(990, '1118565958', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(991, '1118565958', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(992, '1118565958', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(993, '1118567328', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(994, '1118567328', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(995, '1118567328', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(996, '1118567328', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(997, '1118567328', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(998, '1118567328', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(999, '1118567328', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1000, '1118567328', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1001, '1118567328', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1002, '1118567328', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1003, '1118567328', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1004, '1118567328', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1005, '1118567328', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1006, '1118567328', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1007, '1118567328', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1008, '1118567328', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1009, '1118567328', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1010, '1118567328', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1011, '1118567328', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1012, '1118567328', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1013, '1118567328', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1014, '1118567328', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1015, '1118567328', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1016, '1118567328', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1017, '1118567328', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1018, '1118567328', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1019, '1118567328', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1020, '1118567328', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1021, '1118567328', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1022, '1118567328', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1023, '1118567328', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1024, '1118572004', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1025, '1118572004', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1026, '1118572004', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1027, '1118572004', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1028, '1118572004', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1029, '1118572004', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1030, '1118572004', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1031, '1118572004', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1032, '1118572004', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1033, '1118572004', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1034, '1118572004', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1035, '1118572004', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1036, '1118572004', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1037, '1118572004', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1038, '1118572004', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1039, '1118572004', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1040, '1118572004', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1041, '1118572004', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1042, '1118572004', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1043, '1118572004', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1044, '1118572004', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1045, '1118572004', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1046, '1118572004', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1047, '1118572004', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1048, '1118572004', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1049, '1118572004', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1050, '1118572004', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1051, '1118572004', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1052, '1118572004', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1053, '1118572004', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1054, '1118572004', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1055, '1118573216', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1056, '1118573216', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1057, '1118573216', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1058, '1118573216', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1059, '1118573216', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1060, '1118573216', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1061, '1118573216', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1062, '1118573216', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1063, '1118573216', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1064, '1118573216', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1065, '1118573216', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1066, '1118573216', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1067, '1118573216', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1068, '1118573216', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1069, '1118573216', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1070, '1118573216', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1071, '1118573216', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1072, '1118573216', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1073, '1118573216', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1074, '1118573216', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1075, '1118573216', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1076, '1118573216', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1077, '1118573216', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1078, '1118573216', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1079, '1118573216', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1080, '1118573216', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1081, '1118573216', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1082, '1118573216', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1083, '1118573216', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1084, '1118573216', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1085, '1118573216', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1086, '1118575006', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1087, '1118575006', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1088, '1118575006', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1089, '1118575006', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1090, '1118575006', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1091, '1118575006', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1092, '1118575006', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1093, '1118575006', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1094, '1118575006', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1095, '1118575006', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1096, '1118575006', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1097, '1118575006', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1098, '1118575006', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1099, '1118575006', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1100, '1118575006', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1101, '1118575006', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1102, '1118575006', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1103, '1118575006', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1104, '1118575006', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1105, '1118575006', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1106, '1118575006', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1107, '1118575006', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1108, '1118575006', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1109, '1118575006', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1110, '1118575006', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1111, '1118575006', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1112, '1118575006', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1113, '1118575006', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1114, '1118575006', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1115, '1118575006', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1116, '1118575006', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1117, '1118775342', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1118, '1118775342', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1119, '1118775342', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1120, '1118775342', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1121, '1118775342', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1122, '1118775342', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1123, '1118775342', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1124, '1118775342', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1125, '1118775342', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1126, '1118775342', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1127, '1118775342', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1128, '1118775342', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1129, '1118775342', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1130, '1118775342', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1131, '1118775342', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1132, '1118775342', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1133, '1118775342', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1134, '1118775342', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1135, '1118775342', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1136, '1118775342', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1137, '1118775342', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1138, '1118775342', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1139, '1118775342', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1140, '1118775342', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1141, '1118775342', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1142, '1118775342', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1143, '1118775342', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1144, '1118775342', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1145, '1118775342', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1146, '1118775342', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1147, '1118775342', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1148, '11206377', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1149, '11206377', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1150, '11206377', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1151, '11206377', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1152, '11206377', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1153, '11206377', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1154, '11206377', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1155, '11206377', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1156, '11206377', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1157, '11206377', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1158, '11206377', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1159, '11206377', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1160, '11206377', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1161, '11206377', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1162, '11206377', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1163, '11206377', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1164, '11206377', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1165, '11206377', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1166, '11206377', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1167, '11206377', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1168, '11206377', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1169, '11206377', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1170, '11206377', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1171, '11206377', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1172, '11206377', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1173, '11206377', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1174, '11206377', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1175, '11206377', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1176, '11206377', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1177, '11206377', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1178, '11206377', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1179, '1121898640', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1180, '1121898640', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1181, '1121898640', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1182, '1121898640', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1183, '1121898640', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1184, '1121898640', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1185, '1121898640', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1186, '1121898640', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1187, '1121898640', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1188, '1121898640', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1189, '1121898640', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1190, '1121898640', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1191, '1121898640', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1192, '1121898640', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1193, '1121898640', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1194, '1121898640', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1195, '1121898640', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1196, '1121898640', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1197, '1121898640', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1198, '1121898640', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1199, '1121898640', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1200, '1121898640', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1201, '1121898640', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1202, '1121898640', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1203, '1121898640', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1204, '1121898640', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1205, '1121898640', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1206, '1121898640', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35);
INSERT INTO `bolsillos` (`id`, `cedula_empleado`, `seccion`, `nombre`, `nombre_completo`, `orden`, `alarma_tipo`, `alarma_fecha`, `alarma_activa`, `alarma_valor`, `alarma_unidad`, `alarma_fecha_inicio`, `alarma_dias_aviso`) VALUES
(1207, '1121898640', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1208, '1121898640', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1209, '1121898640', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1210, '1124989349', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1211, '1124989349', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1212, '1124989349', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1213, '1124989349', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1214, '1124989349', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1215, '1124989349', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1216, '1124989349', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1217, '1124989349', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1218, '1124989349', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1219, '1124989349', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1220, '1124989349', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1221, '1124989349', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1222, '1124989349', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1223, '1124989349', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1224, '1124989349', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1225, '1124989349', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1226, '1124989349', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1227, '1124989349', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1228, '1124989349', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1229, '1124989349', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1230, '1124989349', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1231, '1124989349', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1232, '1124989349', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1233, '1124989349', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1234, '1124989349', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1235, '1124989349', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1236, '1124989349', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1237, '1124989349', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1238, '1124989349', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1239, '1124989349', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1240, '1124989349', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1241, '1143954094', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1242, '1143954094', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1243, '1143954094', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1244, '1143954094', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1245, '1143954094', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1246, '1143954094', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1247, '1143954094', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1248, '1143954094', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1249, '1143954094', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1250, '1143954094', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1251, '1143954094', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1252, '1143954094', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1253, '1143954094', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1254, '1143954094', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1255, '1143954094', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1256, '1143954094', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1257, '1143954094', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1258, '1143954094', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1259, '1143954094', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1260, '1143954094', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1261, '1143954094', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1262, '1143954094', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1263, '1143954094', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1264, '1143954094', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1265, '1143954094', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1266, '1143954094', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1267, '1143954094', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1268, '1143954094', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1269, '1143954094', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1270, '1143954094', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1271, '1143954094', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1272, '16672796', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1273, '16672796', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1274, '16672796', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1275, '16672796', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1276, '16672796', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1277, '16672796', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1278, '16672796', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1279, '16672796', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1280, '16672796', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1281, '16672796', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1282, '16672796', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1283, '16672796', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1284, '16672796', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1285, '16672796', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1286, '16672796', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1287, '16672796', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1288, '16672796', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1289, '16672796', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1290, '16672796', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1291, '16672796', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1292, '16672796', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1293, '16672796', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1294, '16672796', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1295, '16672796', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1296, '16672796', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1297, '16672796', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1298, '16672796', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1299, '16672796', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1300, '16672796', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1301, '16672796', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1302, '16672796', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1303, '33445352', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1304, '33445352', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1305, '33445352', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1306, '33445352', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1307, '33445352', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1308, '33445352', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1309, '33445352', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1310, '33445352', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1311, '33445352', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1312, '33445352', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1313, '33445352', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1314, '33445352', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1315, '33445352', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1316, '33445352', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1317, '33445352', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1318, '33445352', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1319, '33445352', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1320, '33445352', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1321, '33445352', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1322, '33445352', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1323, '33445352', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1324, '33445352', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1325, '33445352', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1326, '33445352', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1327, '33445352', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1328, '33445352', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1329, '33445352', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1330, '33445352', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1331, '33445352', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1332, '33445352', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1333, '33445352', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1334, '4284762', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1335, '4284762', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1336, '4284762', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1337, '4284762', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1338, '4284762', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1339, '4284762', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1340, '4284762', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1341, '4284762', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1342, '4284762', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1343, '4284762', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1344, '4284762', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1345, '4284762', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1346, '4284762', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1347, '4284762', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1348, '4284762', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1349, '4284762', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1350, '4284762', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1351, '4284762', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1352, '4284762', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1353, '4284762', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1354, '4284762', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1355, '4284762', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1356, '4284762', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1357, '4284762', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1358, '4284762', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1359, '4284762', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1360, '4284762', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1361, '4284762', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1362, '4284762', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1363, '4284762', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1364, '4284762', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1365, '47428604', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1366, '47428604', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1367, '47428604', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1368, '47428604', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1369, '47428604', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1370, '47428604', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1371, '47428604', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1372, '47428604', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1373, '47428604', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1374, '47428604', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1375, '47428604', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1376, '47428604', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1377, '47428604', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1378, '47428604', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1379, '47428604', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1380, '47428604', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1381, '47428604', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1382, '47428604', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1383, '47428604', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1384, '47428604', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1385, '47428604', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1386, '47428604', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1387, '47428604', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1388, '47428604', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1389, '47428604', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1390, '47428604', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1391, '47428604', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1392, '47428604', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1393, '47428604', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1394, '47428604', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1395, '47428604', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1396, '47430097', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1397, '47430097', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1398, '47430097', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1399, '47430097', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1400, '47430097', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1401, '47430097', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1402, '47430097', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1403, '47430097', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1404, '47430097', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1405, '47430097', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1406, '47430097', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1407, '47430097', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1408, '47430097', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1409, '47430097', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1410, '47430097', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1411, '47430097', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1412, '47430097', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1413, '47430097', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1414, '47430097', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1415, '47430097', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1416, '47430097', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1417, '47430097', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1418, '47430097', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1419, '47430097', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1420, '47430097', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1421, '47430097', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1422, '47430097', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1423, '47430097', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1424, '47430097', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1425, '47430097', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1426, '47430097', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1427, '47431008', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1428, '47431008', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1429, '47431008', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1430, '47431008', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1431, '47431008', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1432, '47431008', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1433, '47431008', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1434, '47431008', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1435, '47431008', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1436, '47431008', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1437, '47431008', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1438, '47431008', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1439, '47431008', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1440, '47431008', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1441, '47431008', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1442, '47431008', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1443, '47431008', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1444, '47431008', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1445, '47431008', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1446, '47431008', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1447, '47431008', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1448, '47431008', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1449, '47431008', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1450, '47431008', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1451, '47431008', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1452, '47431008', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1453, '47431008', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1454, '47431008', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1455, '47431008', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1456, '47431008', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1457, '47431008', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1458, '47441163', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1459, '47441163', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1460, '47441163', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1461, '47441163', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1462, '47441163', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1463, '47441163', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1464, '47441163', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1465, '47441163', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1466, '47441163', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1467, '47441163', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1468, '47441163', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1469, '47441163', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1470, '47441163', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1471, '47441163', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1472, '47441163', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1473, '47441163', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1474, '47441163', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1475, '47441163', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1476, '47441163', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1477, '47441163', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1478, '47441163', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1479, '47441163', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1480, '47441163', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1481, '47441163', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1482, '47441163', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1483, '47441163', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1484, '47441163', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1485, '47441163', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1486, '47441163', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1487, '47441163', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1488, '47441163', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1489, '47441979', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1490, '47441979', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1491, '47441979', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1492, '47441979', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1493, '47441979', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1494, '47441979', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1495, '47441979', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1496, '47441979', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1497, '47441979', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1498, '47441979', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1499, '47441979', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1500, '47441979', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1501, '47441979', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1502, '47441979', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1503, '47441979', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1504, '47441979', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1505, '47441979', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1506, '47441979', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1507, '47441979', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1508, '47441979', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1509, '47441979', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1510, '47441979', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1511, '47441979', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1512, '47441979', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1513, '47441979', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1514, '47441979', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1515, '47441979', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1516, '47441979', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1517, '47441979', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1518, '47441979', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1519, '47441979', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1520, '52308103', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1521, '52308103', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1522, '52308103', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1523, '52308103', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1524, '52308103', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1525, '52308103', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1526, '52308103', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1527, '52308103', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1528, '52308103', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1529, '52308103', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1530, '52308103', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1531, '52308103', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1532, '52308103', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1533, '52308103', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1534, '52308103', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1535, '52308103', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1536, '52308103', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1537, '52308103', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1538, '52308103', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1539, '52308103', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1540, '52308103', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1541, '52308103', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1542, '52308103', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1543, '52308103', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1544, '52308103', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1545, '52308103', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1546, '52308103', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1547, '52308103', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1548, '52308103', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1549, '52308103', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1550, '52308103', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1551, '7180789', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1552, '7180789', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1553, '7180789', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1554, '7180789', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1555, '7180789', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1556, '7180789', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1557, '7180789', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1558, '7180789', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1559, '7180789', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1560, '7180789', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1561, '7180789', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1562, '7180789', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1563, '7180789', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1564, '7180789', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1565, '7180789', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1566, '7180789', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1567, '7180789', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1568, '7180789', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1569, '7180789', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1570, '7180789', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1571, '7180789', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1572, '7180789', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1573, '7180789', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1574, '7180789', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1575, '7180789', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1576, '7180789', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1577, '7180789', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1578, '7180789', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1579, '7180789', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1580, '7180789', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1581, '7180789', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1582, '7254795', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1583, '7254795', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1584, '7254795', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1585, '7254795', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1586, '7254795', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1587, '7254795', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1588, '7254795', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1589, '7254795', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1590, '7254795', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1591, '7254795', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1592, '7254795', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1593, '7254795', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1594, '7254795', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1595, '7254795', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1596, '7254795', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1597, '7254795', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1598, '7254795', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1599, '7254795', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1600, '7254795', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1601, '7254795', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1602, '7254795', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1603, '7254795', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1604, '7254795', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1605, '7254795', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1606, '7254795', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1607, '7254795', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1608, '7254795', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1609, '7254795', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1610, '7254795', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1611, '7254795', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35);
INSERT INTO `bolsillos` (`id`, `cedula_empleado`, `seccion`, `nombre`, `nombre_completo`, `orden`, `alarma_tipo`, `alarma_fecha`, `alarma_activa`, `alarma_valor`, `alarma_unidad`, `alarma_fecha_inicio`, `alarma_dias_aviso`) VALUES
(1612, '7254795', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1613, '74770870', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1614, '74770870', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1615, '74770870', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1616, '74770870', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1617, '74770870', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1618, '74770870', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1619, '74770870', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1620, '74770870', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1621, '74770870', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1622, '74770870', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1623, '74770870', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1624, '74770870', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1625, '74770870', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1626, '74770870', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1627, '74770870', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1628, '74770870', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1629, '74770870', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1630, '74770870', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1631, '74770870', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1632, '74770870', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1633, '74770870', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1634, '74770870', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1635, '74770870', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1636, '74770870', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1637, '74770870', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1638, '74770870', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1639, '74770870', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1640, '74770870', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1641, '74770870', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1642, '74770870', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1643, '74770870', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1644, '74814305', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1645, '74814305', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1646, '74814305', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1647, '74814305', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1648, '74814305', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1649, '74814305', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1650, '74814305', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1651, '74814305', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1652, '74814305', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1653, '74814305', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1654, '74814305', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1655, '74814305', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1656, '74814305', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1657, '74814305', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1658, '74814305', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1659, '74814305', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1660, '74814305', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1661, '74814305', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1662, '74814305', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1663, '74814305', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1664, '74814305', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1665, '74814305', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1666, '74814305', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1667, '74814305', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1668, '74814305', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1669, '74814305', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1670, '74814305', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1671, '74814305', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1672, '74814305', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1673, '74814305', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1674, '74814305', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1675, '74859815', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1676, '74859815', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1677, '74859815', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1678, '74859815', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1679, '74859815', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1680, '74859815', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1681, '74859815', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1682, '74859815', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1683, '74859815', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1684, '74859815', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1685, '74859815', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1686, '74859815', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1687, '74859815', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1688, '74859815', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1689, '74859815', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1690, '74859815', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1691, '74859815', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1692, '74859815', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1693, '74859815', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1694, '74859815', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1695, '74859815', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1696, '74859815', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1697, '74859815', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1698, '74859815', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1699, '74859815', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1700, '74859815', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1701, '74859815', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1702, '74859815', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1703, '74859815', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1704, '74859815', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1705, '74859815', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1706, '74861664', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1707, '74861664', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1708, '74861664', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1709, '74861664', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1710, '74861664', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1711, '74861664', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1712, '74861664', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1713, '74861664', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1714, '74861664', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1715, '74861664', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1716, '74861664', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1717, '74861664', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1718, '74861664', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1719, '74861664', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1720, '74861664', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1721, '74861664', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1722, '74861664', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1723, '74861664', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1724, '74861664', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1725, '74861664', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1726, '74861664', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1727, '74861664', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1728, '74861664', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1729, '74861664', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1730, '74861664', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1731, '74861664', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1732, '74861664', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1733, '74861664', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1734, '74861664', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1735, '74861664', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1736, '74861664', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1737, '74861711', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1738, '74861711', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1739, '74861711', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1740, '74861711', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1741, '74861711', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1742, '74861711', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1743, '74861711', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1744, '74861711', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1745, '74861711', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1746, '74861711', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1747, '74861711', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1748, '74861711', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1749, '74861711', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1750, '74861711', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1751, '74861711', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1752, '74861711', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1753, '74861711', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1754, '74861711', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1755, '74861711', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1756, '74861711', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1757, '74861711', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1758, '74861711', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1759, '74861711', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1760, '74861711', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1761, '74861711', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1762, '74861711', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1763, '74861711', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1764, '74861711', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1765, '74861711', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1766, '74861711', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1767, '74861711', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1768, '80033385', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1769, '80033385', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1770, '80033385', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1771, '80033385', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1772, '80033385', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1773, '80033385', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1774, '80033385', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1775, '80033385', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1776, '80033385', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1777, '80033385', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1778, '80033385', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1779, '80033385', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1780, '80033385', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1781, '80033385', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1782, '80033385', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1783, '80033385', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1784, '80033385', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1785, '80033385', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1786, '80033385', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1787, '80033385', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1788, '80033385', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1789, '80033385', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1790, '80033385', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1791, '80033385', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1792, '80033385', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1793, '80033385', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1794, '80033385', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1795, '80033385', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1796, '80033385', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1797, '80033385', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1798, '80033385', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1799, '9433076', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1800, '9433076', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1801, '9433076', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1802, '9433076', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1803, '9433076', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1804, '9433076', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1805, '9433076', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1806, '9433076', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1807, '9433076', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1808, '9433076', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1809, '9433076', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1810, '9433076', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1811, '9433076', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1812, '9433076', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1813, '9433076', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1814, '9433076', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1815, '9433076', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1816, '9433076', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1817, '9433076', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1818, '9433076', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1819, '9433076', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1820, '9433076', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1821, '9433076', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1822, '9433076', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1823, '9433076', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1824, '9433076', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1825, '9433076', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1826, '9433076', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1827, '9433076', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1828, '9433076', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1829, '9433076', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1830, '9434678', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1831, '9434678', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1832, '9434678', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1833, '9434678', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1834, '9434678', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1835, '9434678', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1836, '9434678', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1837, '9434678', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1838, '9434678', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1839, '9434678', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1840, '9434678', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1841, '9434678', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1842, '9434678', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1843, '9434678', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1844, '9434678', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1845, '9434678', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1846, '9434678', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1847, '9434678', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1848, '9434678', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1849, '9434678', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1850, '9434678', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1851, '9434678', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1852, '9434678', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1853, '9434678', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1854, '9434678', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1855, '9434678', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1856, '9434678', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1857, '9434678', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1858, '9434678', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1859, '9434678', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1860, '9434678', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1861, '9656509', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1862, '9656509', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1863, '9656509', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1864, '9656509', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1865, '9656509', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1866, '9656509', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1867, '9656509', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1868, '9656509', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1869, '9656509', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1870, '9656509', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1871, '9656509', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1872, '9656509', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1873, '9656509', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1874, '9656509', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1875, '9656509', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1876, '9656509', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1877, '9656509', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1878, '9656509', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1879, '9656509', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1880, '9656509', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1881, '9656509', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1882, '9656509', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1883, '9656509', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1884, '9656509', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1885, '9656509', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1886, '9656509', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1887, '9656509', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1888, '9656509', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1889, '9656509', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1890, '9656509', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1891, '9656509', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1892, '9658799', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1893, '9658799', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1894, '9658799', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1895, '9658799', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1896, '9658799', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1897, '9658799', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1898, '9658799', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1899, '9658799', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1900, '9658799', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1901, '9658799', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1902, '9658799', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1903, '9658799', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1904, '9658799', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1905, '9658799', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1906, '9658799', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1907, '9658799', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1908, '9658799', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1909, '9658799', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1910, '9658799', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1911, '9658799', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1912, '9658799', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1913, '9658799', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1914, '9658799', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1915, '9658799', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1916, '9658799', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1917, '9658799', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1918, '9658799', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1919, '9658799', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1920, '9658799', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1921, '9658799', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1922, '9658799', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos`
--

CREATE TABLE `cargos` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cargos`
--

INSERT INTO `cargos` (`id`, `nombre`, `activo`) VALUES
(1, 'Auxiliar en Gestion de Talento Humano', 1),
(2, 'Director de talento humano', 1),
(3, 'Auxiliar de Extintores', 1),
(4, 'Enfermero/a', 1),
(5, 'Practicante Sena', 1),
(6, 'Practicante Fundetec', 1),
(7, 'Practicante otra entidad', 1),
(8, 'Servicios Generales', 1),
(9, 'Maquinista', 1),
(10, 'Guardia', 1),
(11, 'Recepcionista', 1),
(12, 'Administrativo', 1),
(13, 'Auxiliar administrativo', 1),
(14, 'Director Académico', 1),
(15, 'Director de negocios', 1),
(16, 'Tecnico en soporte sistemas', 1),
(17, 'Tecnico archivista', 1),
(18, 'Coordinador SST', 1),
(19, 'Auxiliar SST', 1),
(20, 'Jefe de prensa', 1),
(21, 'Contador', 1),
(22, 'Auxiliar de contaduría', 1),
(23, 'Almacenista', 1),
(24, 'Supervisor', 1),
(25, 'Coordinador de banda', 1),
(26, 'Conductor de ambulancia', 1),
(27, 'Aspirante', 1),
(28, 'Voluntario', 1),
(29, 'Secretario recaudador', 1),
(30, 'Auxiliar de enfermería', 1),
(31, 'Docente de banda marcial', 1),
(32, 'PAMEC', 1),
(33, 'Revisor(a) fiscal', 1),
(34, 'Comandante de estación', 1),
(36, 'Bombero integral', 1),
(1621, 'Auxiliar en Talento Humano', 1),
(1655, 'Director administrativo y financiero', 1),
(1838, 'Subcomandante', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `certificados_consecutivos`
--

CREATE TABLE `certificados_consecutivos` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `ultimo_numero` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `certificados_consecutivos`
--

INSERT INTO `certificados_consecutivos` (`id`, `ultimo_numero`) VALUES
(1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos`
--

CREATE TABLE `documentos` (
  `id` int(11) NOT NULL,
  `bolsillo_id` int(11) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `ruta` varchar(500) NOT NULL COMMENT 'ruta relativa dentro de uploads/',
  `orden` int(11) NOT NULL DEFAULT 1,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp(),
  `subido_por_cedula` varchar(10) DEFAULT NULL,
  `subido_por_tipo` enum('empleado','panel') NOT NULL DEFAULT 'panel'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `documentos`
--

INSERT INTO `documentos` (`id`, `bolsillo_id`, `nombre_archivo`, `ruta`, `orden`, `fecha_subida`, `subido_por_cedula`, `subido_por_tipo`) VALUES
(1, 188, 'YESID_LARGO_1791298230.pdf', 'hv_1007703611/hoja_de_vida/cedula_hv_1007703611/YESID_LARGO_1791298230.pdf', 1, '2026-10-07 19:00:23', NULL, 'panel'),
(2, 777, 'ANGEL_GABRIEL_CAMARGO_PEZCA_1791298074.pdf', 'hv_1118555586/hoja_de_vida/cedula_hv_1118555586/ANGEL_GABRIEL_CAMARGO_PEZCA_1791298074.pdf', 1, '2026-10-07 19:00:24', NULL, 'panel'),
(3, 994, 'CAMILO_CORREDOR_1791297425.pdf', 'hv_1118573216/hoja_de_vida/cedula_hv_1118573216/CAMILO_CORREDOR_1791297425.pdf', 1, '2026-10-07 19:00:25', NULL, 'panel'),
(4, 1025, 'ANGELA_BRITHEY_MALDONADO_1791298114.pdf', 'hv_1118575006/hoja_de_vida/cedula_hv_1118575006/ANGELA_BRITHEY_MALDONADO_1791298114.pdf', 1, '2026-10-07 19:00:25', NULL, 'panel'),
(5, 1697, 'CONTRATO_JORGE_SEGURA_1791204830.pdf', 'hv_80033385/documentos_contractuales/contratosFirmados_hv_80033385/CONTRATO_JORGE_SEGURA_1791204830.pdf', 1, '2026-10-07 19:00:27', NULL, 'panel'),
(7, 1692, 'certificado_positiva_1790899379.pdf', 'hv_80033385/documentos_contractuales/certificadoARL_hv_80033385/certificado_positiva_1790899379.pdf', 1, '2026-10-08 00:10:12', NULL, 'panel'),
(8, 219, 'YESID_LARGO_1791298230.pdf', 'hv_1007703611/hoja_de_vida/cedula_hv_1007703611/YESID_LARGO_1791298230.pdf', 1, '2026-10-09 12:09:50', NULL, 'panel'),
(9, 808, 'ANGEL_GABRIEL_CAMARGO_PEZCA_1791298074.pdf', 'hv_1118555586/hoja_de_vida/cedula_hv_1118555586/ANGEL_GABRIEL_CAMARGO_PEZCA_1791298074.pdf', 1, '2026-10-09 12:09:52', NULL, 'panel'),
(10, 1025, 'CAMILO_CORREDOR_1791297425.pdf', 'hv_1118573216/hoja_de_vida/cedula_hv_1118573216/CAMILO_CORREDOR_1791297425.pdf', 2, '2026-10-09 12:09:52', NULL, 'panel'),
(11, 1056, 'ANGELA_BRITHEY_MALDONADO_1791298114.pdf', 'hv_1118575006/hoja_de_vida/cedula_hv_1118575006/ANGELA_BRITHEY_MALDONADO_1791298114.pdf', 1, '2026-10-09 12:09:52', NULL, 'panel'),
(12, 1759, 'CONTRATO_JORGE_SEGURA_1791204830.pdf', 'hv_80033385/documentos_contractuales/contratosFirmados_hv_80033385/CONTRATO_JORGE_SEGURA_1791204830.pdf', 1, '2026-10-09 12:09:55', NULL, 'panel'),
(14, 1785, 'certificado_positiva_1790899379.pdf', 'hv_80033385/documentos_contractuales/certificadoARL_hv_80033385/certificado_positiva_1790899379.pdf', 1, '2026-10-09 21:48:52', NULL, 'panel'),
(15, 250, 'YESID_LARGO_1791298230.pdf', 'hv_1007703611/hoja_de_vida/cedula_hv_1007703611/YESID_LARGO_1791298230.pdf', 1, '2026-10-13 13:44:59', NULL, 'panel'),
(16, 839, 'cedula.pdf', 'hv_1118555586/hoja_de_vida/cedula_hv_1118555586/cedula_1791907026.pdf', 1, '2026-10-13 13:45:01', NULL, 'panel'),
(17, 1056, 'CAMILO_CORREDOR_1791297425.pdf', 'hv_1118573216/hoja_de_vida/cedula_hv_1118573216/CAMILO_CORREDOR_1791297425.pdf', 2, '2026-10-13 13:45:02', NULL, 'panel'),
(18, 1087, 'ANGELA_BRITHEY_MALDONADO_1791298114.pdf', 'hv_1118575006/hoja_de_vida/cedula_hv_1118575006/ANGELA_BRITHEY_MALDONADO_1791298114.pdf', 1, '2026-10-13 13:45:02', NULL, 'panel'),
(19, 1790, 'CONTRATO_JORGE_SEGURA_1791204830.pdf', 'hv_80033385/documentos_contractuales/contratosFirmados_hv_80033385/CONTRATO_JORGE_SEGURA_1791204830.pdf', 1, '2026-10-13 13:45:04', NULL, 'panel'),
(20, 776, 'jose pablo rivera.pdf', 'hv_1118547356/hoja_de_vida/hv_formal_hv_1118547356/jose_pablo_rivera_1791899516.pdf', 2, '2026-10-13 13:51:56', NULL, 'panel'),
(21, 1215, 'cetificado.pdf', 'hv_1124989349/hoja_de_vida/certificados_hv_1124989349/cetificado_1791907523.pdf', 1, '2026-10-13 15:59:09', '1124989349', 'empleado'),
(22, 776, '1_1791944538.pdf', 'hv_1118547356/hoja_de_vida/hv_formal_hv_1118547356/1_1791944538.pdf', 3, '2026-10-16 21:28:23', NULL, 'panel'),
(23, 776, '2_1791944530.pdf', 'hv_1118547356/hoja_de_vida/hv_formal_hv_1118547356/2_1791944530.pdf', 4, '2026-10-16 21:28:23', NULL, 'panel'),
(24, 776, '3_1791944519.pdf', 'hv_1118547356/hoja_de_vida/hv_formal_hv_1118547356/3_1791944519.pdf', 5, '2026-10-16 21:28:23', NULL, 'panel'),
(25, 843, 'ELEMENTO_DE_PROTECCIONA_AUDITIVA_1791943676.pdf', 'hv_1118555586/hoja_de_vida/certificados_hv_1118555586/ELEMENTO_DE_PROTECCIONA_AUDITIVA_1791943676.pdf', 1, '2026-10-16 21:28:23', NULL, 'panel'),
(26, 843, 'GTC_185_2024_1791944174.pdf', 'hv_1118555586/hoja_de_vida/certificados_hv_1118555586/GTC_185_2024_1791944174.pdf', 2, '2026-10-16 21:28:23', NULL, 'panel'),
(27, 843, 'hoja_de_vida_tgz_1791944397.pdf', 'hv_1118555586/hoja_de_vida/certificados_hv_1118555586/hoja_de_vida_tgz_1791944397.pdf', 3, '2026-10-16 21:28:23', NULL, 'panel');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `cedula` varchar(10) NOT NULL COMMENT 'Max 10 caracteres, permite extranjera alfanumérica',
  `lugar_expedicion` varchar(120) DEFAULT NULL COMMENT 'Municipio, Departamento (lista en public/assets/data/municipios.json)',
  `nombre` varchar(150) NOT NULL,
  `sexo` enum('F','M') DEFAULT NULL,
  `cargo` varchar(100) NOT NULL,
  `tipo_de_personal` enum('Bombero','Civil') DEFAULT NULL,
  `eps` enum('Sanitas','Nueva EPS','Capresoca','Salud Total') DEFAULT NULL,
  `pension` enum('Colfondos','Porvenir','Colpensiones','Protección','NA') DEFAULT NULL,
  `arl` enum('Positiva','SURA','Colmena','AXA Colpatria','Seguros Bolívar') DEFAULT NULL,
  `salario_basico` decimal(12,2) DEFAULT NULL,
  `es_bombero_integral` tinyint(1) DEFAULT 0,
  `tipo_jornada` enum('Turnos','Administrativa','Restringida') NOT NULL DEFAULT 'Administrativa' COMMENT 'Cómo se cuentan horas/días en permisos: Turnos (operativo), Administrativa (07:00-17:24) o Restringida (horario reducido)',
  `jornada_hora_entrada` time DEFAULT NULL COMMENT 'Solo si tipo_jornada = Restringida',
  `jornada_hora_salida` time DEFAULT NULL COMMENT 'Solo si tipo_jornada = Restringida',
  `tipo_de_contrato` enum('Fijo','Indefinido','OPS','SENA','OPS SEMY','No aplica') DEFAULT NULL,
  `fecha_inicio_contrato` date DEFAULT NULL,
  `fecha_fin_contrato` date DEFAULT NULL,
  `estado` enum('activo','no activo') DEFAULT 'activo',
  `celular` varchar(15) DEFAULT NULL COMMENT 'Numero de celular, validar 10 digitos',
  `correo` varchar(150) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL COMMENT 'Para notificacion de cumpleaños',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto` varchar(255) DEFAULT NULL COMMENT 'Ruta relativa dentro de uploads/'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`cedula`, `lugar_expedicion`, `nombre`, `sexo`, `cargo`, `tipo_de_personal`, `eps`, `pension`, `arl`, `salario_basico`, `es_bombero_integral`, `tipo_jornada`, `jornada_hora_entrada`, `jornada_hora_salida`, `tipo_de_contrato`, `fecha_inicio_contrato`, `fecha_fin_contrato`, `estado`, `celular`, `correo`, `fecha_nacimiento`, `created_at`, `foto`) VALUES
('1005719736', 'Yopal, Casanare', 'Carlos Augusto Triana Lozano', 'M', 'Tecnico en soporte sistemas', 'Civil', 'Sanitas', NULL, 'Positiva', NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', '2026-10-07', '2027-04-06', 'activo', NULL, NULL, NULL, '2026-09-29 23:30:02', NULL),
('1006554686', 'Yopal, Casanare', 'Camila Jula Gonzalez', 'F', 'Jefe de prensa', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', NULL, NULL, 'activo', NULL, NULL, '2002-10-18', '2026-10-09 12:20:45', NULL),
('1006555204', 'Maní, Casanare', 'Usuario Prueba', 'M', 'Tecnico en soporte sistemas', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Indefinido', NULL, NULL, 'activo', '3216548975', NULL, NULL, '2026-10-07 00:28:58', NULL),
('1006555838', 'Yopal, Casanare', 'Lourdes Ester Guarin Garcia', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2025-05-05', '2025-11-04', 'activo', NULL, NULL, '2003-02-10', '2026-09-23 03:32:54', NULL),
('1006556137', 'Yopal, Casanare', 'Javier David Moreno', 'M', 'Auxiliar de Extintores', 'Civil', 'Nueva EPS', NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', '2026-03-12', '2026-09-11', 'activo', '3224045766', NULL, NULL, '2026-09-23 22:46:04', NULL),
('1006556671', 'Yopal, Casanare', 'Jhon Marco Rincon Castaño', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2026-01-08', NULL, 'activo', NULL, NULL, '2003-06-12', '2026-09-23 03:41:28', NULL),
('1006636306', 'Yopal, Casanare', 'Karen Lizeth Diaz Pineda', 'F', 'Auxiliar de Extintores', 'Bombero', 'Sanitas', 'Porvenir', NULL, NULL, 1, 'Turnos', NULL, NULL, 'Fijo', '2026-04-09', '2026-10-08', 'activo', NULL, NULL, '2001-04-24', '2026-09-23 23:07:53', NULL),
('1007013786', 'Yopal, Casanare', 'Laura Sofia Cisneros Arango', 'F', 'Auxiliar administrativo', 'Civil', NULL, NULL, NULL, 1750905.00, 0, 'Administrativa', NULL, NULL, 'SENA', NULL, NULL, 'activo', '3144823073', NULL, '2002-09-21', '2026-10-07 19:56:37', NULL),
('1007703611', 'Yopal, Casanare', 'Eduard Yecid Largo', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 1, 'Turnos', NULL, NULL, 'Fijo', '2024-04-05', '2027-04-04', 'activo', NULL, NULL, '1995-11-15', '2026-09-29 21:01:48', NULL),
('1019024577', 'Yopal, Casanare', 'Nohora Rocio Duran Torres', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2026-08-01', NULL, 'activo', NULL, NULL, '1988-05-29', '2026-09-23 18:07:51', NULL),
('1029643799', 'Yopal, Casanare', 'Samuel Santiago Fonseca Patarroyo', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2026-08-07', NULL, 'activo', NULL, NULL, '2005-11-15', '2026-09-23 18:09:04', NULL),
('1029661794', 'Yopal, Casanare', 'Darwin Camilo Bedoya Gutierrez', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2026-08-07', NULL, 'activo', NULL, NULL, '2007-09-22', '2026-09-23 18:08:27', NULL),
('1115911058', 'Yopal, Casanare', 'Edwar Santiago Alfonso Ducon', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2026-08-01', NULL, 'activo', NULL, NULL, '2006-01-27', '2026-09-23 18:06:47', NULL),
('1115913555', 'Yopal, Casanare', 'Wilder Andrey Chaparro Chaparro', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2026-01-01', NULL, 'activo', NULL, NULL, '1991-11-28', '2026-09-23 03:43:10', NULL),
('1116043143', 'Yopal, Casanare', 'Lina Maria Aponte Fonseca', 'F', 'Auxiliar SST', 'Civil', 'Sanitas', NULL, 'Positiva', 1964430.00, 0, 'Administrativa', NULL, NULL, 'Fijo', '2024-05-15', '2024-08-14', 'activo', NULL, NULL, '1997-11-10', '2026-09-25 02:25:10', NULL),
('1116552720', 'Yopal, Casanare', 'Juan Fernando Dominguez Ibarguen', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 1, 'Turnos', NULL, NULL, 'Fijo', '2026-01-08', NULL, 'activo', NULL, NULL, '1997-02-10', '2026-09-23 03:38:48', NULL),
('1116992974', 'Sabanalarga, Casanare', 'Angelica Alfonso Alfonso', 'F', 'Auxiliar administrativo', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', '2025-04-11', '2025-10-10', 'no activo', NULL, NULL, '1996-09-02', '2026-10-06 16:43:12', NULL),
('1118198423', 'Yopal, Casanare', 'Carlos Hugo Cubides Villalba', 'M', 'Auxiliar administrativo', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2025-02-04', '2025-05-03', 'activo', NULL, NULL, '1996-04-26', '2026-10-03 03:22:07', NULL),
('1118529611', 'Yopal, Casanare', 'Jimmy Alejandro Garcia Chinchilla', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2025-03-22', NULL, 'activo', NULL, NULL, '1986-04-07', '2026-09-23 02:08:05', NULL),
('1118530819', 'Yopal, Casanare', 'Omar David Linares Alvarez', 'M', 'Auxiliar de contaduría', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', '2025-10-01', '2026-04-30', 'activo', '3005484351', NULL, NULL, '2026-09-29 23:32:41', 'hv_1118530819/perfil/foto.jpg'),
('1118534974', 'Yopal, Casanare', 'Soraida Sepulveda Gordillo', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 1, 'Turnos', NULL, NULL, 'Fijo', '2026-04-15', '2026-10-14', 'activo', NULL, NULL, '1987-04-15', '2026-09-23 03:25:05', NULL),
('1118536550', 'Yopal, Casanare', 'Rodrigo Hernan Ramirez Morales', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2024-10-01', '2025-03-31', 'activo', NULL, NULL, '1987-11-03', '2026-10-05 21:13:59', NULL),
('1118543385', 'Yopal, Casanare', 'Lewis Arfrey Ardila Achagua', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2025-05-01', '2025-11-30', 'activo', NULL, NULL, '1989-11-28', '2026-09-23 03:30:44', NULL),
('1118544837', 'Yopal, Casanare', 'José Ferney Rodriguez Barrera', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2015-12-01', '2016-11-30', 'activo', NULL, NULL, '1990-07-25', '2026-09-23 02:05:57', NULL),
('1118547243', 'Yopal, Casanare', 'Tito Enrique Camargo Pezca', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2019-01-15', NULL, 'activo', NULL, NULL, '1991-04-21', '2026-09-23 01:11:57', NULL),
('1118547356', 'Yopal, Casanare', 'Adriana Marcela Galan Hernandez', 'F', 'Director administrativo y financiero', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', '2026-08-10', '2026-09-30', 'no activo', NULL, NULL, NULL, '2026-10-06 15:10:32', NULL),
('1118550799', 'Yopal, Casanare', 'Deyna Yurany Torres Cuervo', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2024-01-17', NULL, 'activo', NULL, NULL, '1992-05-01', '2026-09-23 03:27:52', NULL),
('1118555586', 'Yopal, Casanare', 'Angel Gabriel Camargo Pezca', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 1, 'Turnos', NULL, NULL, 'Fijo', '2021-09-24', NULL, 'activo', '3133691214', NULL, '1993-10-05', '2026-09-23 02:06:39', 'hv_1118555586/perfil/foto.png'),
('1118564532', 'Yopal, Casanare', 'Astrid Mariana Aquite Gómez', 'F', 'Auxiliar en Talento Humano', 'Bombero', 'Nueva EPS', 'Colfondos', 'Positiva', 1964430.00, 1, 'Restringida', '08:00:00', '12:00:00', 'Fijo', '2024-02-15', '2024-08-15', 'activo', '3209308877', NULL, '1996-04-13', '2026-09-23 00:46:12', NULL),
('1118564997', 'Yopal, Casanare', 'Kewin Alexis Adan Jeronimo', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', NULL, NULL, 'no activo', NULL, NULL, '1996-05-30', '2026-09-23 03:18:30', NULL),
('1118565906', 'Yopal, Casanare', 'Jeidi Carolina Acevedo Lopez', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2025-11-12', '2026-05-11', 'activo', NULL, NULL, '1996-08-26', '2026-09-23 03:44:42', NULL),
('1118565958', 'Yopal, Casanare', 'Yeritsa Tatiana Egue Chaparro', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2024-11-27', NULL, 'activo', NULL, NULL, '1996-09-04', '2026-09-30 15:04:28', NULL),
('1118567328', 'Yopal, Casanare', 'Nelson Fabian Chaparro Rincon', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2016-02-11', NULL, 'activo', NULL, NULL, '1997-02-10', '2026-09-23 18:11:40', NULL),
('1118572004', 'Yopal, Casanare', 'Luisa Fernanda Abril Bernal', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2024-09-05', NULL, 'activo', NULL, NULL, '1998-08-26', '2026-09-30 15:01:28', NULL),
('1118573216', 'Yopal, Casanare', 'Camilo Andres Corredor Garcia', 'M', 'Maquinista', 'Bombero', NULL, NULL, NULL, NULL, 1, 'Turnos', NULL, NULL, 'Fijo', '2024-11-01', '2025-04-30', 'activo', NULL, NULL, '1999-01-17', '2026-09-23 03:22:10', NULL),
('1118575006', 'Yopal, Casanare', 'Angela Brithey Maldonado', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2025-05-16', '2025-11-15', 'activo', NULL, NULL, '1999-08-24', '2026-09-23 03:37:51', 'hv_1118575006/perfil/foto.jpg'),
('1118775342', 'Yopal, Casanare', 'Daniel Fernando Gutierrez Riaño', 'M', 'Bombero integral', 'Bombero', 'Sanitas', 'Colpensiones', 'Positiva', 0.00, 0, 'Turnos', NULL, NULL, 'Fijo', '2024-01-17', '2025-01-17', 'activo', NULL, NULL, '1993-03-12', '2026-09-23 01:04:54', NULL),
('11206377', 'Yopal, Casanare', 'Juan Fernando Guzman Guzman', 'M', 'Bombero integral', 'Bombero', 'Sanitas', 'Colpensiones', 'Positiva', NULL, 1, 'Turnos', NULL, NULL, 'Fijo', '2023-07-21', NULL, 'activo', NULL, NULL, '1995-12-08', '2026-09-23 01:34:23', NULL),
('1121898640', 'Villavicencio, Meta', 'Arlyn Johanna Sanchez Gutierrez', 'F', 'Auxiliar administrativo', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 22:47:28', NULL),
('1124989349', 'Aguazul, Casanare', 'Tatiana Andrea Guzman Galindo', 'F', 'Practicante Fundetec', 'Civil', 'Capresoca', 'NA', 'Positiva', NULL, 0, 'Administrativa', NULL, NULL, 'No aplica', '2026-05-04', NULL, 'activo', '3229496595', 'tgz57031@gmail.com', '2003-04-13', '2026-09-22 01:48:16', 'hv_1124989349/perfil/foto.jpg'),
('1143954094', 'Yopal, Casanare', 'Jonnathan Alexander Daza Barrera', 'M', 'Secretario recaudador', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', NULL, NULL, 'activo', NULL, NULL, '1993-02-02', '2026-09-25 02:29:26', NULL),
('16672796', 'Yopal, Casanare', 'Juan Carlos Santacoloma Piedrahita', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2025-05-15', '2025-11-14', 'activo', NULL, NULL, '1962-08-27', '2026-09-23 03:35:08', NULL),
('33445352', 'Yopal, Casanare', 'Gladys Escobar De Hernandez', 'F', 'Revisor(a) fiscal', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', '2026-01-01', '2027-01-01', 'activo', NULL, NULL, '1951-07-13', '2026-10-07 19:41:51', NULL),
('4284762', 'Yopal, Casanare', 'Jose Manuel Gutierrez Teatin', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', '1999-08-20', NULL, 'activo', NULL, NULL, '1970-09-29', '2026-09-23 18:03:27', NULL),
('47428604', 'Nunchía, Casanare', 'Graciela Garcia Chinchilla', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', '2010-02-01', NULL, 'activo', NULL, NULL, '1968-07-26', '2026-09-23 17:57:55', NULL),
('47430097', 'Yopal, Casanare', 'Sthella Gutierrez', 'F', 'Guardia', 'Bombero', NULL, NULL, NULL, NULL, 1, 'Turnos', NULL, NULL, 'Indefinido', '2007-12-04', NULL, 'activo', NULL, NULL, '1972-05-12', '2026-09-23 18:01:10', NULL),
('47431008', 'Yopal, Casanare', 'Romelia Medina Martinez', 'F', 'Servicios Generales', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', '2019-02-18', '2020-02-18', 'activo', NULL, NULL, '1973-03-03', '2026-10-06 15:32:04', NULL),
('47441163', 'Yopal, Casanare', 'Sandra Milena Castaño Vargas', 'F', 'Administrativo', 'Civil', 'Sanitas', 'Porvenir', 'Positiva', 2071830.00, 0, 'Administrativa', NULL, NULL, 'Fijo', '2024-02-13', '2024-08-12', 'activo', NULL, NULL, '1983-06-13', '2026-09-23 00:56:25', NULL),
('47441979', 'Yopal, Casanare', 'Angela Maria Moreno', 'F', 'Subcomandante', 'Bombero', NULL, NULL, NULL, NULL, 1, 'Turnos', NULL, NULL, 'Indefinido', NULL, NULL, 'activo', '3126547898', NULL, '1983-06-09', '2026-09-29 21:33:52', 'hv_47441979/perfil/foto.jpg'),
('52308103', 'Yopal, Casanare', 'Fanny Paola Mercado Delgado', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2022-07-07', NULL, 'activo', NULL, NULL, '1975-10-14', '2026-09-23 18:10:43', NULL),
('7180789', 'Yopal, Casanare', 'Hector Favian Auzaque Parra', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', '2008-03-10', NULL, 'activo', NULL, NULL, '1982-02-11', '2026-09-23 03:45:50', NULL),
('7254795', 'Yopal, Casanare', 'Yobanis Alberto Castrillon Cano', 'M', 'Maquinista', 'Bombero', NULL, 'Colpensiones', 'Positiva', NULL, 1, 'Turnos', NULL, NULL, 'Fijo', '2024-07-01', '2025-06-30', 'activo', NULL, NULL, '1980-10-12', '2026-09-29 20:58:48', NULL),
('74770870', 'Yopal, Casanare', 'Ariosto Castelblanco Zorro', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', '2008-06-01', NULL, 'activo', NULL, NULL, NULL, '2026-09-23 17:56:03', NULL),
('74814305', 'Yopal, Casanare', 'Nelson Morales Cubides', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 1, 'Turnos', NULL, NULL, 'Indefinido', '2007-11-01', NULL, 'activo', NULL, NULL, '1979-11-14', '2026-09-23 18:04:03', NULL),
('74859815', 'Yopal, Casanare', 'Waldo Ramirez Avila', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', '2005-01-11', NULL, 'activo', NULL, NULL, '1977-08-15', '2026-09-23 18:04:43', NULL),
('74861664', 'Yopal, Casanare', 'Guillermo Enrique Guarin Fonseca', 'M', 'Director Académico', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', NULL, NULL, 'activo', '3123878482', NULL, '1979-09-25', '2026-09-25 02:19:35', 'hv_74861664/perfil/foto.png'),
('74861711', 'Yopal, Casanare', 'Wilmar Vargas Teatin', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, '1979-01-27', '2026-09-23 18:05:33', NULL),
('80033385', 'Yopal, Casanare', 'Jorge Antonio Segura Poveda', 'M', 'Conductor de ambulancia', 'Bombero', 'Sanitas', 'Colpensiones', 'Positiva', NULL, 1, 'Turnos', NULL, NULL, 'Fijo', '2026-09-26', '2026-12-25', 'activo', '3212038841', NULL, '1982-05-13', '2026-09-25 02:02:05', 'hv_80033385/perfil/foto.png'),
('9433076', 'Yopal, Casanare', 'Rafael Rojas Rico', 'M', 'Comandante de estación', 'Bombero', 'Sanitas', 'Colpensiones', 'Positiva', 7046.33, 1, 'Turnos', NULL, NULL, 'Indefinido', NULL, NULL, 'activo', '3216547896', NULL, '1984-06-21', '2026-09-29 21:05:27', 'hv_9433076/perfil/foto.jpg'),
('9434678', 'Yopal, Casanare', 'Jose Alejandro Fernandez Cardenas', 'M', 'Tecnico archivista', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', '2015-12-01', '2016-11-30', 'activo', NULL, NULL, '1985-11-14', '2026-10-05 20:04:25', NULL),
('9656509', 'Yopal, Casanare', 'Jose Orlando Gonzalez Gonzales', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', '2013-02-26', NULL, 'activo', NULL, NULL, '1967-12-13', '2026-09-23 17:59:42', NULL),
('9658799', 'Yopal, Casanare', 'Javier Fernando Fuquen Calderon', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', '2009-04-01', NULL, 'activo', NULL, NULL, '1971-12-05', '2026-09-23 17:57:02', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `firmas_guardadas`
--

CREATE TABLE `firmas_guardadas` (
  `id` int(11) NOT NULL,
  `cedula` varchar(10) NOT NULL,
  `ruta_imagen` varchar(255) NOT NULL COMMENT 'PNG del canvas o archivo subido, en uploads/hv_{cedula}/firma/',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `firmas_guardadas`
--

INSERT INTO `firmas_guardadas` (`id`, `cedula`, `ruta_imagen`, `created_at`) VALUES
(1, '1124989349', 'hv_1124989349/firma/firma_1791906180.png', '2026-10-13 15:43:00'),
(2, '47441979', 'hv_47441979/firma/firma_1791908736.png', '2026-10-13 16:25:36'),
(3, '1006555204', 'hv_1006555204/firma/firma_1791989272.png', '2026-10-14 14:47:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `funciones_certificados`
--

CREATE TABLE `funciones_certificados` (
  `id` int(10) UNSIGNED NOT NULL,
  `cargo_id` int(10) UNSIGNED NOT NULL,
  `texto` varchar(160) NOT NULL,
  `orden` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `funciones_certificados`
--

INSERT INTO `funciones_certificados` (`id`, `cargo_id`, `texto`, `orden`, `activo`, `created_at`, `updated_at`) VALUES
(1, 36, 'respuesta en incendios, rescates y atención de emergencias, y todas aquellas funciones de acuerdo a su cargo', 0, 1, '2026-10-07 21:12:38', '2026-10-07 21:13:07'),
(2, 13, 'getion de documentos', 0, 1, '2026-10-13 14:05:16', '2026-10-13 14:05:16'),
(3, 3, 'realizar el mantenimiento de extintores y diligenciar el registro documental del servicio', 0, 1, '2026-10-13 14:57:13', '2026-10-13 14:57:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `funciones_contratos`
--

CREATE TABLE `funciones_contratos` (
  `id` int(10) UNSIGNED NOT NULL,
  `cargo_id` int(10) UNSIGNED NOT NULL,
  `texto` text NOT NULL,
  `orden` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `destinatario_tipo` enum('talento_humano','empleado') NOT NULL DEFAULT 'talento_humano',
  `usuario_nombre` varchar(50) NOT NULL,
  `cedula_empleado` varchar(10) NOT NULL,
  `campo` varchar(50) NOT NULL,
  `mensaje` varchar(500) NOT NULL,
  `enlace` varchar(255) DEFAULT NULL COMMENT 'Ruta relativa a la que redirige la notificación',
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `usuario_id`, `destinatario_tipo`, `usuario_nombre`, `cedula_empleado`, `campo`, `mensaje`, `enlace`, `leida`, `created_at`) VALUES
(1, 5, 'talento_humano', 'talento', '1006555204', 'celular', '\"tatiana\" editó el celular de \"Usuario Prueba\": antes \"sin celular\", ahora \"3216548975\"', '/chvb/public/empleados.php?q=1006555204', 1, '2026-10-13 15:33:16'),
(2, NULL, 'empleado', '47441979', '47441979', 'permiso', '\"Tatiana Andrea Guzman Galindo\" te pidió un permiso de \"Permiso\"', '/chvb/public/permiso_ver.php?id=1', 1, '2026-10-13 15:43:00'),
(3, 5, 'talento_humano', 'Tatiana Andrea Guzman Galindo', '1124989349', 'permiso_nuevo', '\"Tatiana Andrea Guzman Galindo\" creó un permiso de \"Permiso\" (\"GH-FT-10-2026-0001\")', './permiso_ver.php?id=1', 1, '2026-10-13 15:43:00'),
(4, 6, 'talento_humano', 'Tatiana Andrea Guzman Galindo', '1124989349', 'permiso_nuevo', '\"Tatiana Andrea Guzman Galindo\" creó un permiso de \"Permiso\" (\"GH-FT-10-2026-0001\")', './permiso_ver.php?id=1', 0, '2026-10-13 15:43:00'),
(5, 5, 'talento_humano', 'talento', '1124989349', 'documento', 'El empleado \"Tatiana Andrea Guzman Galindo\" subió \"GH-FT-15  PAZ Y SALVO ALMACEN FUNCIONARIOS.pdf\" al bolsillo \"Certificados\"', '/chvb/public/api/documentos_ver.php?id=21', 1, '2026-10-13 15:59:09'),
(6, 6, 'talento_humano', 'tatiana', '1124989349', 'documento', 'El empleado \"Tatiana Andrea Guzman Galindo\" subió \"GH-FT-15  PAZ Y SALVO ALMACEN FUNCIONARIOS.pdf\" al bolsillo \"Certificados\"', '/chvb/public/api/documentos_ver.php?id=21', 0, '2026-10-13 15:59:09'),
(7, NULL, 'empleado', '1124989349', '1124989349', 'permiso', 'Tu permiso de \"Permiso\" fue firmado y aprobado.', '/chvb/public/permiso_ver.php?id=1', 1, '2026-10-13 16:25:36'),
(8, 5, 'talento_humano', 'talento', '1124989349', 'permiso_anulado', '\"Angela Maria Moreno\" anuló el permiso \"GH-FT-10-2026-0001\" del empleado \"Tatiana Andrea Guzman Galindo\". Motivo: jjj', '/chvb/public/permiso_ver.php?id=1', 1, '2026-10-13 16:25:50'),
(9, NULL, 'empleado', '1124989349', '1124989349', 'permiso', 'Tu permiso \"GH-FT-10-2026-0001\" fue anulado: jjj', '/chvb/public/permiso_ver.php?id=1', 1, '2026-10-13 16:25:50'),
(10, NULL, 'empleado', '74861664', '74861664', 'permiso', '\"Tatiana Andrea Guzman Galindo\" te pidió un permiso de \"Permiso\"', '/chvb/public/permiso_ver.php?id=2', 1, '2026-10-14 14:13:30'),
(11, 5, 'talento_humano', 'Tatiana Andrea Guzman Galindo', '1124989349', 'permiso_nuevo', '\"Tatiana Andrea Guzman Galindo\" creó un permiso de \"Permiso\" (\"GH-FT-10-2026-0002\")', './permiso_ver.php?id=2', 0, '2026-10-14 14:13:30'),
(12, 6, 'talento_humano', 'Tatiana Andrea Guzman Galindo', '1124989349', 'permiso_nuevo', '\"Tatiana Andrea Guzman Galindo\" creó un permiso de \"Permiso\" (\"GH-FT-10-2026-0002\")', './permiso_ver.php?id=2', 0, '2026-10-14 14:13:30'),
(13, NULL, 'empleado', '1124989349', '1124989349', 'permiso', 'Tu permiso de \"Permiso\" fue devuelto: Por favor tatiana la evidencia no es de la foto del pie, por favor adjunta el documento o boeta que te dieron en el hospital.', '/chvb/public/permiso_ver.php?id=2', 1, '2026-10-14 14:19:30'),
(14, NULL, 'empleado', '74861664', '74861664', 'permiso', '\"Tatiana Andrea Guzman Galindo\" te pidió un permiso de \"Permiso\"', '/chvb/public/permiso_ver.php?id=2', 1, '2026-10-14 14:21:24'),
(15, NULL, 'empleado', '1124989349', '1124989349', 'permiso', 'Tu permiso de \"Permiso\" fue devuelto: Repito lo mismo: la evidencia no es la foto del pie, sino el soporte del hospital. Y debe adjuntarlo cuando vuelva y agregar la hora de llegada.', '/chvb/public/permiso_ver.php?id=2', 1, '2026-10-14 14:24:16'),
(16, NULL, 'empleado', '47441979', '47441979', 'permiso', '\"Usuario Prueba\" te pidió un permiso de \"Permiso\"', '/chvb/public/permiso_ver.php?id=3', 0, '2026-10-14 14:47:52'),
(17, 5, 'talento_humano', 'Usuario Prueba', '1006555204', 'permiso_nuevo', '\"Usuario Prueba\" creó un permiso de \"Permiso\" (\"GH-FT-10-2026-0003\")', './permiso_ver.php?id=3', 1, '2026-10-14 14:47:52'),
(18, 6, 'talento_humano', 'Usuario Prueba', '1006555204', 'permiso_nuevo', '\"Usuario Prueba\" creó un permiso de \"Permiso\" (\"GH-FT-10-2026-0003\")', './permiso_ver.php?id=3', 0, '2026-10-14 14:47:52'),
(19, NULL, 'empleado', '1006555204', '1006555204', 'permiso', 'Tu permiso de \"Permiso\" fue firmado y aprobado.', '/chvb/public/permiso_ver.php?id=3', 0, '2026-10-16 23:19:32'),
(20, 5, 'talento_humano', 'talento', '1006555204', 'permiso_anulado', '\"TATIS\" anuló el permiso \"GH-FT-10-2026-0003\" del empleado \"Usuario Prueba\". Motivo: iojiuhyu', '/chvb/public/permiso_ver.php?id=3', 1, '2026-10-16 23:20:05'),
(21, NULL, 'empleado', '1006555204', '1006555204', 'permiso', 'Tu permiso \"GH-FT-10-2026-0003\" fue anulado: iojiuhyu', '/chvb/public/permiso_ver.php?id=3', 0, '2026-10-16 23:20:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `consecutivo` varchar(20) NOT NULL,
  `formato_codigo` varchar(20) DEFAULT NULL,
  `formato_version` int(10) UNSIGNED DEFAULT NULL,
  `formato_fecha` date DEFAULT NULL,
  `cedula_empleado` varchar(10) NOT NULL,
  `nombre_empleado_snapshot` varchar(150) NOT NULL,
  `cargo_empleado_snapshot` varchar(150) NOT NULL,
  `celular_empleado_snapshot` varchar(15) DEFAULT NULL,
  `tipo_permiso` enum('Permiso','Vacaciones','Licencia','Mision institucional') NOT NULL,
  `motivo` text NOT NULL,
  `fecha_inicio` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `total_horas` decimal(6,2) DEFAULT NULL,
  `incluye_festivo` tinyint(1) DEFAULT 0,
  `festivo_confirmado` tinyint(1) DEFAULT 0,
  `remunerado` tinyint(1) NOT NULL DEFAULT 0,
  `es_compensatorio` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_horas_extra` date DEFAULT NULL,
  `compensatorio_origen` varchar(30) DEFAULT NULL,
  `compensatorio_origen_detalle` varchar(120) DEFAULT NULL,
  `es_devolucion` tinyint(1) NOT NULL DEFAULT 0,
  `es_salida_pendiente_regreso` tinyint(1) NOT NULL DEFAULT 0,
  `devolucion_fecha` date DEFAULT NULL,
  `devolucion_hora_inicio` time DEFAULT NULL,
  `devolucion_hora_fin` time DEFAULT NULL,
  `devolucion_total_horas` decimal(6,2) DEFAULT NULL,
  `tiene_reemplazo` tinyint(1) NOT NULL DEFAULT 0,
  `cedula_reemplazo` varchar(10) DEFAULT NULL,
  `cedula_jefe` varchar(10) NOT NULL,
  `foto_solicitante` varchar(255) NOT NULL,
  `firma_solicitante` varchar(255) NOT NULL,
  `foto_reemplazo` varchar(255) DEFAULT NULL,
  `firma_reemplazo` varchar(255) DEFAULT NULL,
  `foto_jefe` varchar(255) DEFAULT NULL,
  `firma_jefe` varchar(255) DEFAULT NULL,
  `foto_jefe_prefirmado` varchar(255) DEFAULT NULL,
  `firma_jefe_prefirmado` varchar(255) DEFAULT NULL,
  `evidencia_archivo` varchar(255) DEFAULT NULL,
  `estado` enum('en_proceso','por_firmar_reemplazo','por_firmar_jefe','por_firmar_jefe_final','firmado','devuelto','devuelto_regreso','rechazado','aprobado_pendiente_regreso','anulado') NOT NULL DEFAULT 'en_proceso',
  `motivo_devolucion` text DEFAULT NULL,
  `motivo_rechazo` text DEFAULT NULL,
  `motivo_anulacion` text DEFAULT NULL,
  `anulado_por` varchar(50) DEFAULT NULL,
  `fecha_anulacion` timestamp NULL DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `consecutivo`, `formato_codigo`, `formato_version`, `formato_fecha`, `cedula_empleado`, `nombre_empleado_snapshot`, `cargo_empleado_snapshot`, `celular_empleado_snapshot`, `tipo_permiso`, `motivo`, `fecha_inicio`, `hora_inicio`, `fecha_fin`, `hora_fin`, `total_horas`, `incluye_festivo`, `festivo_confirmado`, `remunerado`, `es_compensatorio`, `fecha_horas_extra`, `compensatorio_origen`, `compensatorio_origen_detalle`, `es_devolucion`, `es_salida_pendiente_regreso`, `devolucion_fecha`, `devolucion_hora_inicio`, `devolucion_hora_fin`, `devolucion_total_horas`, `tiene_reemplazo`, `cedula_reemplazo`, `cedula_jefe`, `foto_solicitante`, `firma_solicitante`, `foto_reemplazo`, `firma_reemplazo`, `foto_jefe`, `firma_jefe`, `foto_jefe_prefirmado`, `firma_jefe_prefirmado`, `evidencia_archivo`, `estado`, `motivo_devolucion`, `motivo_rechazo`, `motivo_anulacion`, `anulado_por`, `fecha_anulacion`, `version`, `fecha_solicitud`, `fecha_actualizacion`) VALUES
(1, 'GH-FT-10-2026-0001', 'GH-FT-10', 1, '2026-02-10', '1124989349', 'Tatiana Andrea Guzman Galindo', 'Practicante Fundetec', '3229496595', 'Permiso', 'Cita odontólogo', '2026-10-14', '07:00:00', '2026-10-14', '17:24:00', 8.40, 0, 0, 1, 0, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, 0, NULL, '47441979', 'hv_1124989349/permisos/fotos/foto_1791906180.jpg', 'hv_1124989349/firma/firma_1791906180.png', NULL, NULL, 'hv_47441979/permisos/fotos/foto_1791908736.jpg', 'hv_47441979/firma/firma_1791908736.png', NULL, NULL, NULL, 'anulado', NULL, NULL, 'jjj', 'Angela Maria Moreno', '2026-10-13 16:25:50', 4, '2026-10-13 15:43:00', '2026-10-13 16:25:50'),
(2, 'GH-FT-10-2026-0002', 'GH-FT-10', 1, '2026-02-10', '1124989349', 'Tatiana Andrea Guzman Galindo', 'Practicante Fundetec', '3229496595', 'Permiso', 'Dolor intenso de pierna, me dirijo al hospital', '2026-10-14', '09:09:00', NULL, NULL, NULL, 0, 0, 1, 0, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, 0, NULL, '74861664', 'hv_1124989349/permisos/fotos/foto_1791987209.jpg', 'hv_1124989349/firma/firma_1791906180.png', NULL, NULL, NULL, NULL, NULL, NULL, 'hv_1124989349/permisos/evidencias/evidencia_1791987209.jpg', 'devuelto', 'Repito lo mismo: la evidencia no es la foto del pie, sino el soporte del hospital. Y debe adjuntarlo cuando vuelva y agregar la hora de llegada.', NULL, NULL, NULL, NULL, 5, '2026-10-14 14:13:30', '2026-10-14 14:24:16'),
(3, 'GH-FT-10-2026-0003', 'GH-FT-10', 1, '2026-02-10', '1006555204', 'Usuario Prueba', 'Tecnico en soporte sistemas', '3216548975', 'Permiso', 'reunion de padrs de familia', '2026-10-15', '07:00:00', '2026-10-15', '12:59:00', 5.00, 0, 0, 1, 0, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, 0, NULL, '47441979', 'hv_1006555204/permisos/fotos/foto_1791989272.jpg', 'hv_1006555204/firma/firma_1791989272.png', NULL, NULL, 'hv_47441979/permisos/fotos/foto_1792192772.jpg', 'hv_47441979/firma/firma_1791908736.png', NULL, NULL, NULL, 'anulado', NULL, NULL, 'iojiuhyu', 'TATIS', '2026-10-16 23:20:05', 4, '2026-10-14 14:47:52', '2026-10-16 23:20:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_consecutivos`
--

CREATE TABLE `permisos_consecutivos` (
  `anio` int(11) NOT NULL,
  `ultimo_numero` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permisos_consecutivos`
--

INSERT INTO `permisos_consecutivos` (`anio`, `ultimo_numero`) VALUES
(2026, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_devoluciones`
--

CREATE TABLE `permisos_devoluciones` (
  `id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `total_horas` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_dias`
--

CREATE TABLE `permisos_dias` (
  `id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `es_festivo` tinyint(1) NOT NULL DEFAULT 0,
  `festivo_nombre` varchar(150) DEFAULT NULL,
  `incluido` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'si es_festivo=1, el empleado decide; si es_festivo=0, siempre 1',
  `horas_brutas` decimal(6,2) NOT NULL,
  `horas_descuento_almuerzo` decimal(6,2) NOT NULL DEFAULT 0.00,
  `horas_netas` decimal(6,2) NOT NULL COMMENT '0 si incluido=0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permisos_dias`
--

INSERT INTO `permisos_dias` (`id`, `permiso_id`, `fecha`, `hora_inicio`, `hora_fin`, `es_festivo`, `festivo_nombre`, `incluido`, `horas_brutas`, `horas_descuento_almuerzo`, `horas_netas`) VALUES
(1, 1, '2026-10-14', '07:00:00', '17:24:00', 0, NULL, 1, 10.40, 2.00, 8.40),
(2, 3, '2026-10-15', '07:00:00', '12:59:00', 0, NULL, 1, 5.98, 0.98, 5.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_formato`
--

CREATE TABLE `permisos_formato` (
  `id` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `codigo` varchar(20) NOT NULL DEFAULT 'GH-FT-10',
  `version` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `fecha` date NOT NULL,
  `actualizado_por` varchar(100) DEFAULT NULL,
  `actualizado_en` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_historial`
--

CREATE TABLE `permisos_historial` (
  `id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL,
  `version_anterior` int(11) NOT NULL,
  `estado_anterior` varchar(30) NOT NULL,
  `estado_nuevo` varchar(30) NOT NULL,
  `actor_tipo` enum('empleado','reemplazo','jefe','talento_humano') NOT NULL,
  `actor_cedula_o_usuario` varchar(50) NOT NULL,
  `detalle` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permisos_historial`
--

INSERT INTO `permisos_historial` (`id`, `permiso_id`, `version_anterior`, `estado_anterior`, `estado_nuevo`, `actor_tipo`, `actor_cedula_o_usuario`, `detalle`, `created_at`) VALUES
(1, 1, 1, 'en_proceso', 'por_firmar_jefe', 'empleado', '1124989349', 'Permiso creado y enviado', '2026-10-13 15:43:00'),
(2, 1, 2, 'por_firmar_jefe', 'firmado', 'jefe', '47441979', 'Jefe firmó, permiso finalizado', '2026-10-13 16:25:36'),
(3, 1, 3, 'firmado', 'anulado', 'jefe', 'Angela Maria Moreno', 'jjj', '2026-10-13 16:25:50'),
(4, 2, 1, 'en_proceso', 'por_firmar_jefe', 'empleado', '1124989349', 'Permiso creado y enviado', '2026-10-14 14:13:30'),
(5, 2, 2, 'por_firmar_jefe', 'devuelto', 'jefe', '74861664', 'Por favor tatiana la evidencia no es de la foto del pie, por favor adjunta el documento o boeta que te dieron en el hospital.', '2026-10-14 14:19:30'),
(6, 2, 3, 'devuelto', 'por_firmar_jefe', 'empleado', '1124989349', 'Permiso enviado', '2026-10-14 14:21:24'),
(7, 2, 4, 'por_firmar_jefe', 'devuelto', 'jefe', '74861664', 'Repito lo mismo: la evidencia no es la foto del pie, sino el soporte del hospital. Y debe adjuntarlo cuando vuelva y agregar la hora de llegada.', '2026-10-14 14:24:16'),
(8, 3, 1, 'en_proceso', 'por_firmar_jefe', 'empleado', '1006555204', 'Permiso creado y enviado', '2026-10-14 14:47:52'),
(9, 3, 2, 'por_firmar_jefe', 'firmado', 'jefe', '47441979', 'Jefe firmó, permiso finalizado', '2026-10-16 23:19:32'),
(10, 3, 3, 'firmado', 'anulado', 'talento_humano', 'TATIS', 'iojiuhyu', '2026-10-16 23:20:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `renovaciones_contrato`
--

CREATE TABLE `renovaciones_contrato` (
  `id` int(10) UNSIGNED NOT NULL,
  `cedula` varchar(10) NOT NULL,
  `numero` tinyint(3) UNSIGNED NOT NULL COMMENT '1 = RNV1, 2 = RNV2, ...',
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `duracion_meses` smallint(5) UNSIGNED NOT NULL COMMENT 'Calculada al guardar (meses completos, redondeando hacia arriba)',
  `segun_historico` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = registrada tal cual el soporte histórico',
  `incluye_tiempo_previo` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = incluir en el acumulado el tiempo entre este periodo y el anterior',
  `observaciones` varchar(500) DEFAULT NULL,
  `creado_por` int(11) DEFAULT NULL COMMENT 'usuarios.id al momento de crear (sin FK a propósito)',
  `creado_por_nombre` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `renovaciones_contrato`
--

INSERT INTO `renovaciones_contrato` (`id`, `cedula`, `numero`, `fecha_inicio`, `fecha_fin`, `duracion_meses`, `segun_historico`, `incluye_tiempo_previo`, `observaciones`, `creado_por`, `creado_por_nombre`, `created_at`, `updated_at`) VALUES
(1, '1006636306', 1, '2026-10-09', '2027-04-08', 6, 0, 0, NULL, 3, 'Talento', '2026-10-08 00:49:52', '2026-10-08 00:49:52'),
(3, '47431008', 1, '2020-02-18', '2021-02-18', 13, 1, 0, NULL, 3, 'Talento', '2026-10-08 21:37:34', '2026-10-08 21:44:49'),
(4, '47431008', 2, '2021-02-18', '2022-02-18', 13, 1, 0, NULL, 3, 'Talento', '2026-10-08 21:45:20', '2026-10-08 21:45:20'),
(5, '47431008', 3, '2022-02-18', '2023-02-18', 13, 1, 0, NULL, 3, 'Talento', '2026-10-08 21:45:48', '2026-10-08 21:45:48'),
(6, '47431008', 4, '2023-02-18', '2024-02-18', 13, 1, 0, NULL, 3, 'Talento', '2026-10-08 22:07:43', '2026-10-08 22:07:43'),
(7, '47431008', 5, '2024-02-18', '2025-02-17', 12, 1, 0, NULL, 3, 'Talento', '2026-10-08 22:07:57', '2026-10-08 22:08:10'),
(8, '47431008', 6, '2025-02-18', '2026-02-17', 12, 0, 0, NULL, 3, 'Talento', '2026-10-08 22:08:21', '2026-10-08 22:08:21'),
(10, '47431008', 7, '2026-02-18', '2027-02-17', 12, 0, 0, NULL, 3, 'Talento', '2026-10-08 22:10:52', '2026-10-08 22:10:52'),
(12, '1118534974', 1, '2026-10-15', '2027-10-14', 12, 0, 0, NULL, 3, 'Talento', '2026-10-08 22:25:45', '2026-10-08 22:25:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `renovaciones_historial`
--

CREATE TABLE `renovaciones_historial` (
  `id` int(10) UNSIGNED NOT NULL,
  `cedula` varchar(10) NOT NULL,
  `renovacion_id` int(10) UNSIGNED DEFAULT NULL,
  `accion` enum('crear','editar','eliminar') NOT NULL,
  `detalle` varchar(500) NOT NULL,
  `actor_id` int(11) DEFAULT NULL,
  `actor_nombre` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `renovaciones_historial`
--

INSERT INTO `renovaciones_historial` (`id`, `cedula`, `renovacion_id`, `accion`, `detalle`, `actor_id`, `actor_nombre`, `created_at`) VALUES
(1, '1006636306', 1, 'crear', 'RNV1 registrada: 09/10/2026 a 08/04/2027 (6 meses)', 3, 'Talento', '2026-10-08 00:49:52'),
(2, '47431008', 2, 'crear', 'RNV1 registrada: 18/02/2020 a 18/02/2021 (1 año y 1 día) (fechas según soporte histórico)', 2, 'Tatiana', '2026-10-08 21:31:31'),
(3, '47431008', 2, 'eliminar', 'RNV1 eliminada: 18/02/2020 a 18/02/2021', 3, 'Talento', '2026-10-08 21:36:58'),
(4, '47431008', 3, 'crear', 'RNV1 registrada: 18/02/2020 a 18/02/2021 (1 año y 1 día) (según histórico)', 3, 'Talento', '2026-10-08 21:37:34'),
(5, '47431008', 3, 'editar', 'RNV1 editada: antes 18/02/2020 a 18/02/2021, ahora 17/02/2020 a 18/02/2021 (según histórico)', 3, 'Talento', '2026-10-08 21:44:24'),
(6, '47431008', 3, 'editar', 'RNV1 editada: antes 17/02/2020 a 18/02/2021, ahora 18/02/2020 a 18/02/2021 (según histórico)', 3, 'Talento', '2026-10-08 21:44:49'),
(7, '47431008', 4, 'crear', 'RNV2 registrada: 18/02/2021 a 18/02/2022 (1 año y 1 día) (según histórico)', 3, 'Talento', '2026-10-08 21:45:20'),
(8, '47431008', 5, 'crear', 'RNV3 registrada: 18/02/2022 a 18/02/2023 (1 año y 1 día) (según histórico)', 3, 'Talento', '2026-10-08 21:45:48'),
(9, '47431008', 6, 'crear', 'RNV4 registrada: 18/02/2023 a 18/02/2024 (1 año y 1 día) (según histórico)', 3, 'Talento', '2026-10-08 22:07:43'),
(10, '47431008', 7, 'crear', 'RNV5 registrada: 18/02/2024 a 18/02/2025 (1 año y 1 día) (según histórico)', 3, 'Talento', '2026-10-08 22:07:57'),
(11, '47431008', 7, 'editar', 'RNV5 editada: antes 18/02/2024 a 18/02/2025, ahora 18/02/2024 a 17/02/2025 (según histórico)', 3, 'Talento', '2026-10-08 22:08:10'),
(12, '47431008', 8, 'crear', 'RNV6 registrada: 18/02/2025 a 17/02/2026 (1 año)', 3, 'Talento', '2026-10-08 22:08:21'),
(13, '47431008', 9, 'crear', 'RNV7 registrada: 18/02/2026 a 17/02/2027 (1 año)', 3, 'Talento', '2026-10-08 22:08:32'),
(14, '47431008', 9, 'eliminar', 'RNV7 eliminada: 18/02/2026 a 17/02/2027', 3, 'Talento', '2026-10-08 22:08:41'),
(15, '47431008', 10, 'crear', 'RNV7 registrada: 18/02/2026 a 17/02/2027 (1 año)', 3, 'Talento', '2026-10-08 22:10:52'),
(16, '1006636306', 11, 'crear', 'RNV2 registrada: 09/04/2027 a 17/10/2029 (2 años, 6 meses y 9 días)', 3, 'Talento', '2026-10-08 22:24:22'),
(17, '1006636306', 11, 'eliminar', 'RNV2 eliminada: 09/04/2027 a 17/10/2029', 3, 'Talento', '2026-10-08 22:25:16'),
(18, '1118534974', 12, 'crear', 'RNV1 registrada: 15/10/2026 a 14/10/2027 (1 año)', 3, 'Talento', '2026-10-08 22:25:45'),
(19, '1118544837', 13, 'crear', 'RNV1 registrada: 01/12/2025 a 30/11/2026 (1 año)', 3, 'Talento', '2026-10-08 22:29:26'),
(20, '1118544837', 14, 'crear', 'RNV2 registrada: 01/12/2026 a 30/11/2027 (1 año)', 3, 'Talento', '2026-10-08 22:29:42'),
(21, '1118544837', 14, 'eliminar', 'RNV2 eliminada: 01/12/2026 a 30/11/2027', 3, 'Talento', '2026-10-08 22:29:45'),
(22, '1118544837', 13, 'eliminar', 'RNV1 eliminada: 01/12/2025 a 30/11/2026', 3, 'Talento', '2026-10-08 22:29:47'),
(23, '1118544837', 15, 'crear', 'RNV1 registrada: 01/12/2025 a 01/12/2027 (2 años y 1 día)', 3, 'Talento', '2026-10-08 22:30:33'),
(24, '1118544837', 15, 'editar', 'RNV1 editada: antes 01/12/2025 a 01/12/2027, ahora 01/12/2025 a 01/12/2026', 3, 'Talento', '2026-10-08 22:31:18'),
(25, '1118544837', 15, 'editar', 'RNV1 editada: antes 01/12/2025 a 01/12/2026, ahora 01/12/2025 a 30/12/2026', 3, 'Talento', '2026-10-08 22:31:42'),
(26, '1118544837', 15, 'editar', 'RNV1 editada: antes 01/12/2025 a 30/12/2026, ahora 01/12/2025 a 30/11/2026', 3, 'Talento', '2026-10-08 22:31:51'),
(27, '1118544837', 15, 'editar', 'RNV1 editada: antes 01/12/2025 a 30/11/2026, ahora 01/12/2025 a 30/11/2026. Según histórico: activado', 3, 'Talento', '2026-10-08 22:32:13'),
(28, '1118544837', 15, 'editar', 'RNV1 editada: antes 01/12/2025 a 30/11/2026, ahora 01/12/2025 a 30/11/2026 (según histórico)', 3, 'Talento', '2026-10-08 22:32:36'),
(29, '1118544837', 15, 'eliminar', 'RNV1 eliminada: 01/12/2025 a 30/11/2026', 3, 'Talento', '2026-10-08 22:32:55'),
(30, '1006556137', 16, 'crear', 'RNV1 registrada: 12/09/2026 a 11/09/2027 (1 año)', 3, 'Talento', '2026-10-09 21:53:08'),
(31, '1006556137', 17, 'crear', 'RNV2 registrada: 10/09/2027 a 11/03/2028 (6 meses y 2 días) (según histórico)', 3, 'Talento', '2026-10-09 21:53:25'),
(32, '1006556137', 17, 'eliminar', 'RNV2 eliminada: 10/09/2027 a 11/03/2028', 3, 'Talento', '2026-10-09 21:53:50'),
(33, '1006556137', 16, 'eliminar', 'RNV1 eliminada: 12/09/2026 a 11/09/2027', 3, 'Talento', '2026-10-09 21:53:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('superadmin_talento_humano','auxiliar_talento_humano','teniente') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `intentos_fallidos` int(11) DEFAULT 0,
  `bloqueado_hasta` datetime DEFAULT NULL,
  `nombre_completo` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `desactivado_at` datetime DEFAULT NULL,
  `debe_cambiar_password` tinyint(1) NOT NULL DEFAULT 0,
  `password_cambiada_at` datetime DEFAULT NULL,
  `ultimo_acceso` datetime DEFAULT NULL,
  `creado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password_hash`, `rol`, `created_at`, `intentos_fallidos`, `bloqueado_hasta`, `nombre_completo`, `activo`, `desactivado_at`, `debe_cambiar_password`, `password_cambiada_at`, `ultimo_acceso`, `creado_por`) VALUES
(5, 'talento', '$2y$10$Otls3lcR1hk6J.wHX/gZZOUr0dvlSQFRE1HrNbhfi8ntNex29H92K', 'superadmin_talento_humano', '2026-10-13 15:18:35', 0, NULL, NULL, 1, NULL, 0, '2026-10-16 16:48:22', '2026-10-17 09:20:51', NULL),
(6, 'tatiana', '$2y$10$Nckykk7Y0mJUPts3U8psV.S.AS/dtCmhnA00acoP6FXnYxf40vk7C', 'auxiliar_talento_humano', '2026-10-13 15:19:23', 0, NULL, NULL, 0, '2026-10-16 18:35:01', 0, NULL, '2026-10-16 16:51:11', NULL),
(7, 'angela.moreno', '$2y$10$cLzl6BbTa61LuzLN.LqFSuT1UtM7bwX4he.UDkeIsx/ntZyloKk1S', 'teniente', '2026-10-13 15:20:42', 0, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL),
(8, 'tatianaguzman', '$2y$10$FeAVohLGjO.Ho.cp.edlhuo6dqpg56TqCnTodjSHmw.XTJ86M8dyK', 'auxiliar_talento_humano', '2026-10-16 23:00:41', 0, NULL, 'Tatiana Guzman', 1, NULL, 0, '2026-10-16 18:05:41', '2026-10-16 18:19:49', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_auditoria`
--

CREATE TABLE `usuarios_auditoria` (
  `id` int(11) NOT NULL,
  `actor_id` int(11) DEFAULT NULL,
  `actor_username` varchar(50) NOT NULL,
  `objetivo_id` int(11) DEFAULT NULL,
  `objetivo_username` varchar(50) DEFAULT NULL,
  `accion` varchar(40) NOT NULL,
  `detalle` varchar(255) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios_auditoria`
--

INSERT INTO `usuarios_auditoria` (`id`, `actor_id`, `actor_username`, `objetivo_id`, `objetivo_username`, `accion`, `detalle`, `ip`, `created_at`) VALUES
(1, 5, 'talento', 5, 'talento', 'cambiar_password_propio', NULL, '::1', '2026-10-16 21:48:22'),
(2, 5, 'talento', 8, 'TATIS', 'crear_usuario', 'rol=auxiliar_talento_humano', '::1', '2026-10-16 23:00:41'),
(3, 8, 'TATIS', 8, 'TATIS', 'cambiar_password_propio', NULL, '::1', '2026-10-16 23:03:22'),
(4, 5, 'talento', 8, 'TATIS', 'resetear_password', NULL, '::1', '2026-10-16 23:04:46'),
(5, 8, 'TATIS', 8, 'TATIS', 'cambiar_password_propio', NULL, '::1', '2026-10-16 23:05:41'),
(6, 5, 'talento', 6, 'tatiana', 'desactivar', NULL, '::1', '2026-10-16 23:35:01'),
(7, 5, 'talento', 8, 'tatianaguzman', 'editar_datos', 'usuario: TATIS -> tatianaguzman; nombre: TATIANANDREA GUZMAN GALINDO -> Tatiana Guzman', '::1', '2026-10-17 15:21:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_empleados`
--

CREATE TABLE `usuarios_empleados` (
  `id` int(11) NOT NULL,
  `cedula` varchar(10) NOT NULL,
  `password_hash` varchar(255) NOT NULL COMMENT 'hash del PIN de 4 digitos',
  `activo` tinyint(1) DEFAULT 1,
  `intentos_fallidos` int(11) DEFAULT 0,
  `bloqueado_hasta` datetime DEFAULT NULL,
  `pin_encriptado` varchar(255) DEFAULT NULL COMMENT 'PIN cifrado reversible, solo visible para superadmin/auxiliar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios_empleados`
--

INSERT INTO `usuarios_empleados` (`id`, `cedula`, `password_hash`, `activo`, `intentos_fallidos`, `bloqueado_hasta`, `pin_encriptado`) VALUES
(1, '1124989349', '$2y$10$v1JjhkzezNRwYOA0QbJFSO/NHelRJLHINPOHWvxA7j886x5Ooakmi', 1, 0, NULL, 'saW94jW4M4KhgFnjH/6psoG3QYtDqHRNN98XXv3ECb0='),
(2, '47441979', '$2y$10$K8UbMvCG3OL.BDJLA4zahuXhCHH5fRkzkHZ6XzmyiDeJxCu1P81qW', 1, 0, NULL, '0qng9cvhQVEGzkPyw2Gm9t62vfFQQ+hrJgMjcXhxWsM='),
(3, '1121898640', '$2y$10$4wzJ6I9jiM25fCrXntHx2eY/10W1qhMwEVtM7igSOGKpQFIlPzyVm', 1, 0, NULL, '0TC//0tqaB0Mox4Sn0cmzb0VU0nwECgsgOzF4zJnBFI='),
(4, '74861664', '$2y$10$tMvZsCu5JDagEntZcrceseBn.2RhcxUE5gAYXG0/RrU85bL7McyEO', 1, 0, NULL, 'ZBWk0isiURdat7UapGWKv6jcagtLd68GBZG3R6H1uGM='),
(5, '1118575006', '$2y$10$qxHcHfXaY46vBoB4VqO00uhMhTqdw9sFxtpAeApqwlRUz45uMZFpa', 0, 0, NULL, 'mT4nB5+heTXOHcmYLt+fRHGFiXzmnX/CfLPsJgOMo10='),
(6, '1006555204', '$2y$10$DxLcLVzIoJq80kuqYkPoDOxG5pWqFNwNXbDTcMI9CxA9oasJ6Mtpq', 1, 0, NULL, 'KziQh7d4iPtIYRgTvGQySirqpLsxF5bPblCLWp1i4D0='),
(7, '1118555586', '$2y$10$qg/u3pzAbFu7D4wNhVk65OuKiks32Z0od6CrKgGAoDM.Jyzoci3oS', 1, 0, NULL, 'dC/vPAi/XHuxETIewkxUhaM1qeQQ+VwFYgoY1oEPWMs=');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bolsillos`
--
ALTER TABLE `bolsillos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cedula_seccion` (`cedula_empleado`,`seccion`);

--
-- Indices de la tabla `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cargos_nombre` (`nombre`);

--
-- Indices de la tabla `certificados_consecutivos`
--
ALTER TABLE `certificados_consecutivos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bolsillo_id` (`bolsillo_id`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`cedula`),
  ADD KEY `idx_empleados_cargo` (`cargo`);

--
-- Indices de la tabla `firmas_guardadas`
--
ALTER TABLE `firmas_guardadas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unica_por_empleado` (`cedula`);

--
-- Indices de la tabla `funciones_certificados`
--
ALTER TABLE `funciones_certificados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_fcert_cargo_texto` (`cargo_id`,`texto`),
  ADD KEY `idx_fcert_cargo` (`cargo_id`,`activo`,`orden`);

--
-- Indices de la tabla `funciones_contratos`
--
ALTER TABLE `funciones_contratos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fcont_cargo` (`cargo_id`,`activo`,`orden`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `consecutivo` (`consecutivo`),
  ADD KEY `idx_cedula_empleado` (`cedula_empleado`),
  ADD KEY `idx_cedula_reemplazo` (`cedula_reemplazo`),
  ADD KEY `idx_cedula_jefe` (`cedula_jefe`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha_inicio` (`fecha_inicio`);

--
-- Indices de la tabla `permisos_consecutivos`
--
ALTER TABLE `permisos_consecutivos`
  ADD PRIMARY KEY (`anio`);

--
-- Indices de la tabla `permisos_devoluciones`
--
ALTER TABLE `permisos_devoluciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_permiso` (`permiso_id`);

--
-- Indices de la tabla `permisos_dias`
--
ALTER TABLE `permisos_dias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_permiso` (`permiso_id`),
  ADD KEY `idx_fecha` (`fecha`);

--
-- Indices de la tabla `permisos_formato`
--
ALTER TABLE `permisos_formato`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `permisos_historial`
--
ALTER TABLE `permisos_historial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_permiso` (`permiso_id`);

--
-- Indices de la tabla `renovaciones_contrato`
--
ALTER TABLE `renovaciones_contrato`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_renov_cedula_numero` (`cedula`,`numero`),
  ADD KEY `idx_renov_fecha_fin` (`fecha_fin`);

--
-- Indices de la tabla `renovaciones_historial`
--
ALTER TABLE `renovaciones_historial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_renhist_cedula` (`cedula`,`created_at`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indices de la tabla `usuarios_auditoria`
--
ALTER TABLE `usuarios_auditoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_objetivo` (`objetivo_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indices de la tabla `usuarios_empleados`
--
ALTER TABLE `usuarios_empleados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula` (`cedula`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bolsillos`
--
ALTER TABLE `bolsillos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1923;

--
-- AUTO_INCREMENT de la tabla `cargos`
--
ALTER TABLE `cargos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1839;

--
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `firmas_guardadas`
--
ALTER TABLE `firmas_guardadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `funciones_certificados`
--
ALTER TABLE `funciones_certificados`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `funciones_contratos`
--
ALTER TABLE `funciones_contratos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `permisos_devoluciones`
--
ALTER TABLE `permisos_devoluciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos_dias`
--
ALTER TABLE `permisos_dias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `permisos_historial`
--
ALTER TABLE `permisos_historial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `renovaciones_contrato`
--
ALTER TABLE `renovaciones_contrato`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `renovaciones_historial`
--
ALTER TABLE `renovaciones_historial`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios_auditoria`
--
ALTER TABLE `usuarios_auditoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios_empleados`
--
ALTER TABLE `usuarios_empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bolsillos`
--
ALTER TABLE `bolsillos`
  ADD CONSTRAINT `bolsillos_ibfk_1` FOREIGN KEY (`cedula_empleado`) REFERENCES `empleados` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `fk_empleados_cargo` FOREIGN KEY (`cargo`) REFERENCES `cargos` (`nombre`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `funciones_certificados`
--
ALTER TABLE `funciones_certificados`
  ADD CONSTRAINT `fk_fcert_cargo` FOREIGN KEY (`cargo_id`) REFERENCES `cargos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `renovaciones_contrato`
--
ALTER TABLE `renovaciones_contrato`
  ADD CONSTRAINT `fk_renov_empleado` FOREIGN KEY (`cedula`) REFERENCES `empleados` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `renovaciones_historial`
--
ALTER TABLE `renovaciones_historial`
  ADD CONSTRAINT `fk_renhist_empleado` FOREIGN KEY (`cedula`) REFERENCES `empleados` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
