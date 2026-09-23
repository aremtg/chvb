-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20260915.9e4dc5b5f4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 23, 2026 at 05:20 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chvb`
--

-- --------------------------------------------------------

--
-- Table structure for table `bolsillos`
--
CREATE TABLE `bolsillos` (
  `id` int NOT NULL,
  `cedula_empleado` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seccion` enum('hoja_de_vida','documentos_contractuales') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'slug interno, ej: certificados',
  `nombre_completo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'nombre visible, ej: Certificados',
  `orden` int NOT NULL DEFAULT '0',
  `alarma_tipo` enum('1m','2m','6m','1a','custom') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alarma_fecha` date DEFAULT NULL,
  `alarma_activa` tinyint(1) DEFAULT '0',
  `alarma_valor` int DEFAULT NULL COMMENT 'Cantidad numérica para alarma personalizada',
  `alarma_unidad` enum('dias','meses','anios') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Unidad para alarma personalizada',
  `alarma_fecha_inicio` date DEFAULT NULL COMMENT 'Fecha desde la cual se cuenta el plazo',
  `alarma_dias_aviso` int DEFAULT '35' COMMENT 'Días de anticipación para la alerta amarilla'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bolsillos`
--

INSERT INTO `bolsillos` (`id`, `cedula_empleado`, `seccion`, `nombre`, `nombre_completo`, `orden`, `alarma_tipo`, `alarma_fecha`, `alarma_activa`, `alarma_valor`, `alarma_unidad`, `alarma_fecha_inicio`, `alarma_dias_aviso`) VALUES
(1, '1124989349', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(2, '1124989349', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(3, '1124989349', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(4, '1124989349', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(5, '1124989349', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(6, '1124989349', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(7, '1124989349', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(8, '1124989349', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(9, '1124989349', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(10, '1124989349', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(11, '1124989349', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(12, '1124989349', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(13, '1124989349', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(14, '1124989349', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(15, '1124989349', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(16, '1124989349', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(17, '1124989349', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(18, '1124989349', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(19, '1124989349', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(20, '1124989349', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(21, '1124989349', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(22, '1124989349', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(23, '1124989349', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(24, '1124989349', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(25, '1124989349', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(26, '1124989349', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(27, '1124989349', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(28, '1124989349', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(29, '1124989349', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(30, '1124989349', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(31, '1124989349', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(32, '111856453', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(33, '111856453', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(34, '111856453', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(35, '111856453', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(36, '111856453', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(37, '111856453', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(38, '111856453', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(39, '111856453', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(40, '111856453', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(41, '111856453', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(42, '111856453', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(43, '111856453', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(44, '111856453', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(45, '111856453', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(46, '111856453', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(47, '111856453', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(48, '111856453', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(49, '111856453', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(50, '111856453', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(51, '111856453', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(52, '111856453', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(53, '111856453', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(54, '111856453', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(55, '111856453', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(56, '111856453', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(57, '111856453', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(58, '111856453', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(59, '111856453', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(60, '111856453', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(61, '111856453', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(62, '111856453', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(63, '47441163', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(64, '47441163', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(65, '47441163', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(66, '47441163', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(67, '47441163', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(68, '47441163', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(69, '47441163', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(70, '47441163', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(71, '47441163', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(72, '47441163', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(73, '47441163', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(74, '47441163', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(75, '47441163', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(76, '47441163', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(77, '47441163', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(78, '47441163', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(79, '47441163', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(80, '47441163', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(81, '47441163', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(82, '47441163', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(83, '47441163', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(84, '47441163', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(85, '47441163', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(86, '47441163', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(87, '47441163', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(88, '47441163', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(89, '47441163', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(90, '47441163', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(91, '47441163', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(92, '47441163', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(93, '47441163', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(94, '1118775342', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(95, '1118775342', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(96, '1118775342', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(97, '1118775342', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(98, '1118775342', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(99, '1118775342', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(100, '1118775342', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(101, '1118775342', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(102, '1118775342', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(103, '1118775342', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(104, '1118775342', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(105, '1118775342', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(106, '1118775342', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(107, '1118775342', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(108, '1118775342', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(109, '1118775342', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(110, '1118775342', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(111, '1118775342', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(112, '1118775342', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(113, '1118775342', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(114, '1118775342', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(115, '1118775342', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(116, '1118775342', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(117, '1118775342', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(118, '1118775342', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(119, '1118775342', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(120, '1118775342', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(121, '1118775342', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(122, '1118775342', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(123, '1118775342', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(124, '1118775342', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(125, '1118547243', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(126, '1118547243', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(127, '1118547243', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(128, '1118547243', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(129, '1118547243', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(130, '1118547243', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(131, '1118547243', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(132, '1118547243', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(133, '1118547243', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(134, '1118547243', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(135, '1118547243', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(136, '1118547243', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(137, '1118547243', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(138, '1118547243', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(139, '1118547243', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(140, '1118547243', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(141, '1118547243', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(142, '1118547243', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(143, '1118547243', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(144, '1118547243', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(145, '1118547243', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(146, '1118547243', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(147, '1118547243', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(148, '1118547243', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(149, '1118547243', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(150, '1118547243', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(151, '1118547243', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(152, '1118547243', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(153, '1118547243', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(154, '1118547243', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(155, '1118547243', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(156, '11206377', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(157, '11206377', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(158, '11206377', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(159, '11206377', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(160, '11206377', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(161, '11206377', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(162, '11206377', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(163, '11206377', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(164, '11206377', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(165, '11206377', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(166, '11206377', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(167, '11206377', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(168, '11206377', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(169, '11206377', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(170, '11206377', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(171, '11206377', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(172, '11206377', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(173, '11206377', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(174, '11206377', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(175, '11206377', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(176, '11206377', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(177, '11206377', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(178, '11206377', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(179, '11206377', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(180, '11206377', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(181, '11206377', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(182, '11206377', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(183, '11206377', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(184, '11206377', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(185, '11206377', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(186, '11206377', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(187, '1118544837', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(188, '1118544837', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(189, '1118544837', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(190, '1118544837', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(191, '1118544837', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(192, '1118544837', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(193, '1118544837', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(194, '1118544837', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(195, '1118544837', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(196, '1118544837', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(197, '1118544837', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(198, '1118544837', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(199, '1118544837', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(200, '1118544837', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(201, '1118544837', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(202, '1118544837', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(203, '1118544837', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(204, '1118544837', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(205, '1118544837', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(206, '1118544837', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(207, '1118544837', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(208, '1118544837', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(209, '1118544837', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(210, '1118544837', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(211, '1118544837', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(212, '1118544837', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(213, '1118544837', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(214, '1118544837', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(215, '1118544837', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(216, '1118544837', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(217, '1118544837', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(218, '1118555586', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(219, '1118555586', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(220, '1118555586', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(221, '1118555586', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(222, '1118555586', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(223, '1118555586', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(224, '1118555586', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(225, '1118555586', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(226, '1118555586', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(227, '1118555586', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(228, '1118555586', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(229, '1118555586', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(230, '1118555586', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(231, '1118555586', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(232, '1118555586', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(233, '1118555586', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(234, '1118555586', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(235, '1118555586', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(236, '1118555586', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(237, '1118555586', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(238, '1118555586', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(239, '1118555586', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(240, '1118555586', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(241, '1118555586', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(242, '1118555586', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(243, '1118555586', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(244, '1118555586', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(245, '1118555586', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(246, '1118555586', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(247, '1118555586', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(248, '1118555586', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(249, '1118529611', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(250, '1118529611', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(251, '1118529611', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(252, '1118529611', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(253, '1118529611', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(254, '1118529611', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(255, '1118529611', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(256, '1118529611', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(257, '1118529611', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(258, '1118529611', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(259, '1118529611', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(260, '1118529611', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(261, '1118529611', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(262, '1118529611', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(263, '1118529611', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(264, '1118529611', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(265, '1118529611', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(266, '1118529611', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(267, '1118529611', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(268, '1118529611', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(269, '1118529611', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(270, '1118529611', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(271, '1118529611', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(272, '1118529611', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(273, '1118529611', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(274, '1118529611', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(275, '1118529611', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(276, '1118529611', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(277, '1118529611', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(278, '1118529611', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(279, '1118529611', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(280, '1118564997', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(281, '1118564997', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(282, '1118564997', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(283, '1118564997', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(284, '1118564997', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(285, '1118564997', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(286, '1118564997', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(287, '1118564997', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(288, '1118564997', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(289, '1118564997', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(290, '1118564997', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(291, '1118564997', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(292, '1118564997', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(293, '1118564997', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(294, '1118564997', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(295, '1118564997', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(296, '1118564997', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(297, '1118564997', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(298, '1118564997', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(299, '1118564997', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(300, '1118564997', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(301, '1118564997', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(302, '1118564997', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(303, '1118564997', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(304, '1118564997', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(305, '1118564997', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(306, '1118564997', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(307, '1118564997', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(308, '1118564997', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(309, '1118564997', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(310, '1118564997', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(311, '1118573216', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(312, '1118573216', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(313, '1118573216', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(314, '1118573216', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(315, '1118573216', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(316, '1118573216', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(317, '1118573216', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(318, '1118573216', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(319, '1118573216', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(320, '1118573216', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(321, '1118573216', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(322, '1118573216', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(323, '1118573216', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(324, '1118573216', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(325, '1118573216', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(326, '1118573216', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(327, '1118573216', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(328, '1118573216', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(329, '1118573216', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(330, '1118573216', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(331, '1118573216', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(332, '1118573216', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(333, '1118573216', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(334, '1118573216', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(335, '1118573216', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(336, '1118573216', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(337, '1118573216', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(338, '1118573216', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(339, '1118573216', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(340, '1118573216', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(341, '1118573216', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(342, '1118534974', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(343, '1118534974', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(344, '1118534974', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(345, '1118534974', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(346, '1118534974', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(347, '1118534974', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(348, '1118534974', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(349, '1118534974', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(350, '1118534974', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(351, '1118534974', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(352, '1118534974', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(353, '1118534974', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(354, '1118534974', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(355, '1118534974', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(356, '1118534974', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(357, '1118534974', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(358, '1118534974', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(359, '1118534974', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(360, '1118534974', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(361, '1118534974', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(362, '1118534974', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(363, '1118534974', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(364, '1118534974', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(365, '1118534974', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(366, '1118534974', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(367, '1118534974', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(368, '1118534974', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(369, '1118534974', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(370, '1118534974', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(371, '1118534974', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(372, '1118534974', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(373, '1118550799', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(374, '1118550799', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(375, '1118550799', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(376, '1118550799', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(377, '1118550799', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(378, '1118550799', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(379, '1118550799', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(380, '1118550799', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(381, '1118550799', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(382, '1118550799', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(383, '1118550799', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(384, '1118550799', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(385, '1118550799', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(386, '1118550799', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(387, '1118550799', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(388, '1118550799', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(389, '1118550799', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(390, '1118550799', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(391, '1118550799', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(392, '1118550799', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(393, '1118550799', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(394, '1118550799', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(395, '1118550799', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(396, '1118550799', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(397, '1118550799', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(398, '1118550799', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(399, '1118550799', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(400, '1118550799', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(401, '1118550799', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(402, '1118550799', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(403, '1118550799', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(404, '1118543385', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(405, '1118543385', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35);
INSERT INTO `bolsillos` (`id`, `cedula_empleado`, `seccion`, `nombre`, `nombre_completo`, `orden`, `alarma_tipo`, `alarma_fecha`, `alarma_activa`, `alarma_valor`, `alarma_unidad`, `alarma_fecha_inicio`, `alarma_dias_aviso`) VALUES
(406, '1118543385', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(407, '1118543385', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(408, '1118543385', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(409, '1118543385', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(410, '1118543385', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(411, '1118543385', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(412, '1118543385', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(413, '1118543385', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(414, '1118543385', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(415, '1118543385', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(416, '1118543385', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(417, '1118543385', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(418, '1118543385', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(419, '1118543385', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(420, '1118543385', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(421, '1118543385', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(422, '1118543385', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(423, '1118543385', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(424, '1118543385', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(425, '1118543385', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(426, '1118543385', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(427, '1118543385', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(428, '1118543385', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(429, '1118543385', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(430, '1118543385', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(431, '1118543385', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(432, '1118543385', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(433, '1118543385', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(434, '1118543385', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(435, '1006555838', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(436, '1006555838', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(437, '1006555838', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(438, '1006555838', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(439, '1006555838', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(440, '1006555838', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(441, '1006555838', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(442, '1006555838', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(443, '1006555838', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(444, '1006555838', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(445, '1006555838', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(446, '1006555838', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(447, '1006555838', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(448, '1006555838', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(449, '1006555838', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(450, '1006555838', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(451, '1006555838', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(452, '1006555838', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(453, '1006555838', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(454, '1006555838', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(455, '1006555838', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(456, '1006555838', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(457, '1006555838', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(458, '1006555838', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(459, '1006555838', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(460, '1006555838', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(461, '1006555838', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(462, '1006555838', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(463, '1006555838', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(464, '1006555838', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(465, '1006555838', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(466, '16672796', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(467, '16672796', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(468, '16672796', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(469, '16672796', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(470, '16672796', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(471, '16672796', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(472, '16672796', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(473, '16672796', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(474, '16672796', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(475, '16672796', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(476, '16672796', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(477, '16672796', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(478, '16672796', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(479, '16672796', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(480, '16672796', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(481, '16672796', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(482, '16672796', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(483, '16672796', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(484, '16672796', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(485, '16672796', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(486, '16672796', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(487, '16672796', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(488, '16672796', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(489, '16672796', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(490, '16672796', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(491, '16672796', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(492, '16672796', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(493, '16672796', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(494, '16672796', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(495, '16672796', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(496, '16672796', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(497, '1118575006', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(498, '1118575006', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(499, '1118575006', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(500, '1118575006', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(501, '1118575006', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(502, '1118575006', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(503, '1118575006', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(504, '1118575006', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(505, '1118575006', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(506, '1118575006', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(507, '1118575006', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(508, '1118575006', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(509, '1118575006', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(510, '1118575006', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(511, '1118575006', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(512, '1118575006', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(513, '1118575006', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(514, '1118575006', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(515, '1118575006', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(516, '1118575006', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(517, '1118575006', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(518, '1118575006', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(519, '1118575006', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(520, '1118575006', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(521, '1118575006', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(522, '1118575006', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(523, '1118575006', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(524, '1118575006', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(525, '1118575006', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(526, '1118575006', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(527, '1118575006', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(528, '1116552720', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(529, '1116552720', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(530, '1116552720', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(531, '1116552720', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(532, '1116552720', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(533, '1116552720', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(534, '1116552720', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(535, '1116552720', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(536, '1116552720', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(537, '1116552720', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(538, '1116552720', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(539, '1116552720', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(540, '1116552720', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(541, '1116552720', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(542, '1116552720', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(543, '1116552720', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(544, '1116552720', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(545, '1116552720', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(546, '1116552720', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(547, '1116552720', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(548, '1116552720', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(549, '1116552720', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(550, '1116552720', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(551, '1116552720', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(552, '1116552720', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(553, '1116552720', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(554, '1116552720', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(555, '1116552720', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(556, '1116552720', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(557, '1116552720', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(558, '1116552720', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(559, '1006556671', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(560, '1006556671', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(561, '1006556671', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(562, '1006556671', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(563, '1006556671', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(564, '1006556671', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(565, '1006556671', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(566, '1006556671', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(567, '1006556671', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(568, '1006556671', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(569, '1006556671', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(570, '1006556671', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(571, '1006556671', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(572, '1006556671', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(573, '1006556671', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(574, '1006556671', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(575, '1006556671', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(576, '1006556671', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(577, '1006556671', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(578, '1006556671', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(579, '1006556671', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(580, '1006556671', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(581, '1006556671', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(582, '1006556671', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(583, '1006556671', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(584, '1006556671', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(585, '1006556671', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(586, '1006556671', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(587, '1006556671', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(588, '1006556671', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(589, '1006556671', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(590, '1115913555', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(591, '1115913555', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(592, '1115913555', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(593, '1115913555', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(594, '1115913555', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(595, '1115913555', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(596, '1115913555', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(597, '1115913555', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(598, '1115913555', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(599, '1115913555', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(600, '1115913555', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(601, '1115913555', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(602, '1115913555', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(603, '1115913555', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(604, '1115913555', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(605, '1115913555', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(606, '1115913555', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(607, '1115913555', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(608, '1115913555', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(609, '1115913555', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(610, '1115913555', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(611, '1115913555', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(612, '1115913555', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(613, '1115913555', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(614, '1115913555', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(615, '1115913555', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(616, '1115913555', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(617, '1115913555', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(618, '1115913555', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(619, '1115913555', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(620, '1115913555', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(621, '1118565906', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(622, '1118565906', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(623, '1118565906', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(624, '1118565906', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(625, '1118565906', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(626, '1118565906', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(627, '1118565906', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(628, '1118565906', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(629, '1118565906', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(630, '1118565906', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(631, '1118565906', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(632, '1118565906', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(633, '1118565906', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(634, '1118565906', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(635, '1118565906', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(636, '1118565906', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(637, '1118565906', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(638, '1118565906', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(639, '1118565906', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(640, '1118565906', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(641, '1118565906', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(642, '1118565906', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(643, '1118565906', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(644, '1118565906', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(645, '1118565906', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(646, '1118565906', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(647, '1118565906', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(648, '1118565906', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(649, '1118565906', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(650, '1118565906', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(651, '1118565906', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(652, '7180789', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(653, '7180789', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(654, '7180789', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(655, '7180789', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(656, '7180789', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(657, '7180789', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(658, '7180789', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(659, '7180789', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(660, '7180789', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(661, '7180789', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(662, '7180789', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(663, '7180789', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(664, '7180789', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(665, '7180789', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(666, '7180789', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(667, '7180789', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(668, '7180789', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(669, '7180789', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(670, '7180789', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(671, '7180789', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(672, '7180789', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(673, '7180789', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(674, '7180789', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(675, '7180789', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(676, '7180789', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(677, '7180789', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(678, '7180789', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(679, '7180789', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(680, '7180789', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(681, '7180789', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(682, '7180789', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(683, '74770870', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(684, '74770870', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(685, '74770870', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(686, '74770870', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(687, '74770870', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(688, '74770870', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(689, '74770870', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(690, '74770870', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(691, '74770870', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(692, '74770870', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(693, '74770870', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(694, '74770870', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(695, '74770870', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(696, '74770870', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(697, '74770870', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(698, '74770870', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(699, '74770870', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(700, '74770870', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(701, '74770870', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(702, '74770870', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(703, '74770870', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(704, '74770870', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(705, '74770870', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(706, '74770870', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(707, '74770870', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(708, '74770870', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(709, '74770870', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(710, '74770870', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(711, '74770870', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(712, '74770870', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(713, '74770870', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(714, '9658799', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(715, '9658799', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(716, '9658799', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(717, '9658799', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(718, '9658799', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(719, '9658799', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(720, '9658799', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(721, '9658799', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(722, '9658799', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(723, '9658799', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(724, '9658799', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(725, '9658799', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(726, '9658799', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(727, '9658799', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(728, '9658799', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(729, '9658799', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(730, '9658799', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(731, '9658799', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(732, '9658799', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(733, '9658799', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(734, '9658799', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(735, '9658799', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(736, '9658799', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(737, '9658799', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(738, '9658799', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(739, '9658799', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(740, '9658799', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(741, '9658799', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(742, '9658799', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(743, '9658799', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(744, '9658799', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(745, '47428604', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(746, '47428604', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(747, '47428604', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(748, '47428604', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(749, '47428604', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(750, '47428604', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(751, '47428604', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(752, '47428604', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(753, '47428604', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(754, '47428604', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(755, '47428604', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(756, '47428604', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(757, '47428604', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(758, '47428604', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(759, '47428604', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(760, '47428604', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(761, '47428604', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(762, '47428604', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(763, '47428604', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(764, '47428604', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(765, '47428604', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(766, '47428604', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(767, '47428604', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(768, '47428604', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(769, '47428604', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(770, '47428604', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(771, '47428604', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(772, '47428604', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(773, '47428604', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(774, '47428604', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(775, '47428604', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(776, '9656509', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(777, '9656509', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(778, '9656509', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(779, '9656509', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(780, '9656509', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(781, '9656509', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(782, '9656509', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(783, '9656509', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(784, '9656509', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(785, '9656509', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(786, '9656509', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(787, '9656509', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(788, '9656509', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(789, '9656509', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(790, '9656509', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(791, '9656509', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(792, '9656509', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(793, '9656509', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(794, '9656509', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(795, '9656509', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(796, '9656509', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(797, '9656509', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(798, '9656509', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(799, '9656509', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(800, '9656509', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(801, '9656509', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(802, '9656509', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(803, '9656509', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(804, '9656509', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(805, '9656509', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(806, '9656509', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(807, '47430097', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(808, '47430097', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(809, '47430097', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(810, '47430097', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(811, '47430097', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35);
INSERT INTO `bolsillos` (`id`, `cedula_empleado`, `seccion`, `nombre`, `nombre_completo`, `orden`, `alarma_tipo`, `alarma_fecha`, `alarma_activa`, `alarma_valor`, `alarma_unidad`, `alarma_fecha_inicio`, `alarma_dias_aviso`) VALUES
(812, '47430097', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(813, '47430097', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(814, '47430097', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(815, '47430097', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(816, '47430097', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(817, '47430097', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(818, '47430097', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(819, '47430097', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(820, '47430097', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(821, '47430097', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(822, '47430097', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(823, '47430097', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(824, '47430097', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(825, '47430097', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(826, '47430097', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(827, '47430097', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(828, '47430097', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(829, '47430097', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(830, '47430097', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(831, '47430097', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(832, '47430097', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(833, '47430097', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(834, '47430097', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(835, '47430097', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(836, '47430097', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(837, '47430097', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(838, '4284762', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(839, '4284762', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(840, '4284762', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(841, '4284762', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(842, '4284762', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(843, '4284762', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(844, '4284762', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(845, '4284762', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(846, '4284762', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(847, '4284762', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(848, '4284762', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(849, '4284762', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(850, '4284762', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(851, '4284762', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(852, '4284762', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(853, '4284762', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(854, '4284762', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(855, '4284762', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(856, '4284762', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(857, '4284762', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(858, '4284762', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(859, '4284762', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(860, '4284762', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(861, '4284762', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(862, '4284762', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(863, '4284762', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(864, '4284762', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(865, '4284762', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(866, '4284762', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(867, '4284762', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(868, '4284762', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(869, '74814305', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(870, '74814305', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(871, '74814305', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(872, '74814305', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(873, '74814305', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(874, '74814305', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(875, '74814305', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(876, '74814305', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(877, '74814305', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(878, '74814305', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(879, '74814305', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(880, '74814305', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(881, '74814305', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(882, '74814305', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(883, '74814305', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(884, '74814305', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(885, '74814305', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(886, '74814305', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(887, '74814305', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(888, '74814305', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(889, '74814305', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(890, '74814305', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(891, '74814305', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(892, '74814305', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(893, '74814305', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(894, '74814305', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(895, '74814305', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(896, '74814305', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(897, '74814305', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(898, '74814305', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(899, '74814305', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(900, '74859815', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(901, '74859815', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(902, '74859815', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(903, '74859815', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(904, '74859815', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(905, '74859815', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(906, '74859815', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(907, '74859815', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(908, '74859815', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(909, '74859815', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(910, '74859815', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(911, '74859815', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(912, '74859815', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(913, '74859815', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(914, '74859815', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(915, '74859815', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(916, '74859815', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(917, '74859815', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(918, '74859815', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(919, '74859815', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(920, '74859815', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(921, '74859815', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(922, '74859815', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(923, '74859815', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(924, '74859815', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(925, '74859815', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(926, '74859815', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(927, '74859815', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(928, '74859815', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(929, '74859815', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(930, '74859815', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(931, '74861711', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(932, '74861711', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(933, '74861711', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(934, '74861711', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(935, '74861711', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(936, '74861711', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(937, '74861711', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(938, '74861711', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(939, '74861711', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(940, '74861711', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(941, '74861711', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(942, '74861711', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(943, '74861711', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(944, '74861711', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(945, '74861711', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(946, '74861711', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(947, '74861711', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(948, '74861711', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(949, '74861711', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(950, '74861711', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(951, '74861711', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(952, '74861711', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(953, '74861711', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(954, '74861711', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(955, '74861711', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(956, '74861711', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(957, '74861711', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(958, '74861711', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(959, '74861711', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(960, '74861711', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(961, '74861711', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(962, '1115911058', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(963, '1115911058', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(964, '1115911058', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(965, '1115911058', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(966, '1115911058', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(967, '1115911058', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(968, '1115911058', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(969, '1115911058', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(970, '1115911058', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(971, '1115911058', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(972, '1115911058', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(973, '1115911058', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(974, '1115911058', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(975, '1115911058', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(976, '1115911058', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(977, '1115911058', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(978, '1115911058', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(979, '1115911058', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(980, '1115911058', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(981, '1115911058', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(982, '1115911058', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(983, '1115911058', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(984, '1115911058', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(985, '1115911058', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(986, '1115911058', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(987, '1115911058', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(988, '1115911058', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(989, '1115911058', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(990, '1115911058', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(991, '1115911058', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(992, '1115911058', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(993, '1019024577', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(994, '1019024577', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(995, '1019024577', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(996, '1019024577', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(997, '1019024577', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(998, '1019024577', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(999, '1019024577', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1000, '1019024577', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1001, '1019024577', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1002, '1019024577', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1003, '1019024577', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1004, '1019024577', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1005, '1019024577', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1006, '1019024577', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1007, '1019024577', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1008, '1019024577', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1009, '1019024577', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1010, '1019024577', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1011, '1019024577', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1012, '1019024577', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1013, '1019024577', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1014, '1019024577', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1015, '1019024577', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1016, '1019024577', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1017, '1019024577', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1018, '1019024577', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1019, '1019024577', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1020, '1019024577', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1021, '1019024577', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1022, '1019024577', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1023, '1019024577', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1024, '1029661794', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1025, '1029661794', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1026, '1029661794', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1027, '1029661794', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1028, '1029661794', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1029, '1029661794', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1030, '1029661794', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1031, '1029661794', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1032, '1029661794', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1033, '1029661794', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1034, '1029661794', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1035, '1029661794', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1036, '1029661794', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1037, '1029661794', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1038, '1029661794', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1039, '1029661794', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1040, '1029661794', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1041, '1029661794', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1042, '1029661794', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1043, '1029661794', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1044, '1029661794', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1045, '1029661794', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1046, '1029661794', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1047, '1029661794', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1048, '1029661794', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1049, '1029661794', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1050, '1029661794', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1051, '1029661794', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1052, '1029661794', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1053, '1029661794', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1054, '1029661794', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1055, '1029643799', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1056, '1029643799', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1057, '1029643799', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1058, '1029643799', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1059, '1029643799', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1060, '1029643799', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1061, '1029643799', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1062, '1029643799', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1063, '1029643799', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1064, '1029643799', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1065, '1029643799', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1066, '1029643799', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1067, '1029643799', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1068, '1029643799', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1069, '1029643799', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1070, '1029643799', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1071, '1029643799', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1072, '1029643799', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1073, '1029643799', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1074, '1029643799', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1075, '1029643799', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1076, '1029643799', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1077, '1029643799', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1078, '1029643799', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1079, '1029643799', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1080, '1029643799', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1081, '1029643799', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1082, '1029643799', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1083, '1029643799', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1084, '1029643799', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1085, '1029643799', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1086, '52308103', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1087, '52308103', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1088, '52308103', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1089, '52308103', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1090, '52308103', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1091, '52308103', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1092, '52308103', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1093, '52308103', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1094, '52308103', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1095, '52308103', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1096, '52308103', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1097, '52308103', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1098, '52308103', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1099, '52308103', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1100, '52308103', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1101, '52308103', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1102, '52308103', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1103, '52308103', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1104, '52308103', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1105, '52308103', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1106, '52308103', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1107, '52308103', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1108, '52308103', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1109, '52308103', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1110, '52308103', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1111, '52308103', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1112, '52308103', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1113, '52308103', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1114, '52308103', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1115, '52308103', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1116, '52308103', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1117, '1118567328', 'hoja_de_vida', 'hv_formal', 'Hoja de Vida Formal', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1118, '1118567328', 'hoja_de_vida', 'cedula', 'Cédula', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1119, '1118567328', 'hoja_de_vida', 'hv_libretaMilitar', 'Libreta Militar', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1120, '1118567328', 'hoja_de_vida', 'licenciaDeConduccion', 'Licencia de Conducción', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1121, '1118567328', 'hoja_de_vida', 'bachillerYotrosestudios', 'Bachiller y Otros Estudios', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1122, '1118567328', 'hoja_de_vida', 'certificados', 'Certificados', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1123, '1118567328', 'hoja_de_vida', 'formularioDeIngreso', 'Formulario de Ingreso', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1124, '1118567328', 'hoja_de_vida', 'resolucionesDeAscenso', 'Resoluciones de Ascenso', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1125, '1118567328', 'hoja_de_vida', 'hv_bomberil', 'Hoja de Vida Bomberil', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1126, '1118567328', 'hoja_de_vida', 'autorizacion', 'Autorización', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1127, '1118567328', 'hoja_de_vida', 'antecedentes', 'Antecedentes', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1128, '1118567328', 'hoja_de_vida', 'vacunas', 'Vacunas', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1129, '1118567328', 'hoja_de_vida', 'entregaDeDotacion', 'Entrega de Dotación', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1130, '1118567328', 'documentos_contractuales', 'examenesMedicos', 'Exámenes Médicos', 1, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1131, '1118567328', 'documentos_contractuales', 'entrevistaDeSeleccion', 'Entrevista de Selección', 2, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1132, '1118567328', 'documentos_contractuales', 'certificadoEPS', 'Certificado EPS', 3, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1133, '1118567328', 'documentos_contractuales', 'certificadoFP', 'Certificado FP', 4, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1134, '1118567328', 'documentos_contractuales', 'certificadoARL', 'Certificado ARL', 5, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1135, '1118567328', 'documentos_contractuales', 'certificadoCCF', 'Certificado CCF', 6, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1136, '1118567328', 'documentos_contractuales', 'funcionesDelCargo', 'Funciones del Cargo', 7, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1137, '1118567328', 'documentos_contractuales', 'induccionSST', 'Inducción SST', 8, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1138, '1118567328', 'documentos_contractuales', 'induccionAlCargo', 'Inducción al Cargo', 9, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1139, '1118567328', 'documentos_contractuales', 'contratosFirmados', 'Contratos Firmados', 10, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1140, '1118567328', 'documentos_contractuales', 'notificacionDeTerminacion', 'Notificación de Terminación', 11, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1141, '1118567328', 'documentos_contractuales', 'renovacion', 'Renovación', 12, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1142, '1118567328', 'documentos_contractuales', 'evaluacionesDeDesempeno', 'Evaluaciones de Desempeño', 13, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1143, '1118567328', 'documentos_contractuales', 'planDeMejoramiento', 'Plan de Mejoramiento', 14, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1144, '1118567328', 'documentos_contractuales', 'felicitacionesYLlamadosDeAtencion', 'Felicitaciones y Llamados de Atención', 15, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1145, '1118567328', 'documentos_contractuales', 'novedadesEIncapacidades', 'Novedades e Incapacidades', 16, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1146, '1118567328', 'documentos_contractuales', 'pazYSalvos', 'Paz y Salvos', 17, NULL, NULL, 0, NULL, NULL, NULL, 35),
(1147, '1118567328', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35);

-- --------------------------------------------------------

--
-- Table structure for table `documentos`
--
CREATE TABLE `documentos` (
  `id` int NOT NULL,
  `bolsillo_id` int NOT NULL,
  `nombre_archivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruta` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ruta relativa dentro de uploads/',
  `orden` int NOT NULL DEFAULT '1',
  `fecha_subida` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subido_por_cedula` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subido_por_tipo` enum('empleado','panel') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'panel'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `empleados`
--
CREATE TABLE `empleados` (
  `cedula` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Max 10 caracteres, permite extranjera alfanumérica',
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sexo` enum('F','M') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo` enum('Auxiliar en Talento Humano','Director de talento humano','Auxiliar de Extintores','Enfermero/a','Practicante Sena','Practicante Fundetec','Practicante otra entidad','Servicios Generales','Maquinista','Guardia','Recepcionista','Administrativo','Auxiliar administrativo','Director Académico','Director de negocios','Tecnico en soporte sistemas','Tecnico archivista','Coordinador SST','Auxiliar SST','Jefe de prensa','Contador','Auxiliar de contaduría','Almacenista','Supervisor','Coordinador de banda','Conductor de ambulancia','Aspirante','Voluntario','Secretario recaudador','Auxiliar de enfermería','Docente de banda marcial','PAMEC','Revisor(a) fiscal','Comandante de estación','Directora administrativa y financiera','Bombero integral') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_de_personal` enum('Bombero','Civil') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eps` enum('Sanitas','Nueva EPS','Capresoca','Salud Total') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pension` enum('Colfondos','Porvenir','Colpensiones','Protección','NA') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `arl` enum('Positiva','SURA','Colmena','AXA Colpatria','Seguros Bolívar') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salario_basico` decimal(12,2) DEFAULT NULL,
  `es_bombero_integral` tinyint(1) DEFAULT '0',
  `tipo_de_contrato` enum('Fijo','Indefinido','OPS','SENA','OPS SEMY','No aplica') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_inicio_contrato` date DEFAULT NULL,
  `fecha_fin_contrato` date DEFAULT NULL,
  `estado` enum('activo','no activo') COLLATE utf8mb4_unicode_ci DEFAULT 'activo',
  `celular` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numero de celular, validar 10 digitos',
  `correo` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL COMMENT 'Para notificacion de cumpleaños',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ruta relativa dentro de uploads/'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `empleados`
--

INSERT INTO `empleados` (`cedula`, `nombre`, `sexo`, `cargo`, `tipo_de_personal`, `eps`, `pension`, `arl`, `salario_basico`, `es_bombero_integral`, `tipo_de_contrato`, `fecha_inicio_contrato`, `fecha_fin_contrato`, `estado`, `celular`, `correo`, `fecha_nacimiento`, `created_at`, `foto`) VALUES
('1006555838', 'Lourdes Ester Guarin Garcia', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 22:32:54', NULL),
('1006556671', 'Jhon Marco Rincon Castaño', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 22:41:28', NULL),
('1019024577', 'Nohora Rocio Duran Torres', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 13:07:51', NULL),
('1029643799', 'Samuel Santiago Fonseca Patarroyo', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 13:09:04', NULL),
('1029661794', 'Darwin Camilo Bedoya Gutierrez', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, '2007-09-22', '2026-09-23 13:08:27', NULL),
('1115911058', 'Edwar Santiago Alfonso Ducon', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 13:06:47', NULL),
('1115913555', 'Wilder Andrey Chaparro Chaparro', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 22:43:10', NULL),
('1116552720', 'Juan Fernando Dominguez Ibarguen', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 22:38:48', NULL),
('1118529611', 'Jimmy Alejandro Garcia Chinchilla', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 21:08:05', NULL),
('1118534974', 'Soraida Sepulveda Gordillo', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 22:25:05', NULL),
('1118543385', 'Lewis Arfrey Ardila Achagua', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 22:30:44', NULL),
('1118544837', 'José Ferney Rodriguez Barrera', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 21:05:57', NULL),
('1118547243', 'Tito Enrique Camargo Pesca', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 20:11:57', NULL),
('1118550799', 'Deyna Yurany Torres Cuervo', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 22:27:52', NULL),
('1118555586', 'Angel Gabriel Camargo Pezca', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 21:06:39', NULL),
('111856453', 'Astrid Mariana Aquite Gómez', 'F', 'Auxiliar en Talento Humano', 'Civil', 'Nueva EPS', 'Colfondos', 'Positiva', 1964430.00, 1, 'Fijo', '2024-02-15', '2024-08-15', 'activo', '3209308877', NULL, '2001-04-13', '2026-09-22 19:46:12', NULL),
('1118564997', 'Kewin Alexis Adan Jeronimo', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 22:18:30', NULL),
('1118565906', 'Jeidi Carolina Acevedo Lopez', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 22:44:42', NULL),
('1118567328', 'Nelson Fabian Chaparro Rincon', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 13:11:40', NULL),
('1118573216', 'Camilo Andres Corredor Garcia', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 22:22:10', NULL),
('1118575006', 'Angela Brithey Maldonado', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 22:37:51', NULL),
('1118775342', 'Daniel Fernando Gutierrez Riaño', 'M', 'Bombero integral', 'Bombero', 'Sanitas', 'Colpensiones', 'Positiva', 0.00, 0, 'Fijo', '2024-01-17', '2024-06-17', 'activo', NULL, NULL, '1993-03-12', '2026-09-22 20:04:54', NULL),
('11206377', 'Juan Fernando Guzman Guzman', 'M', 'Bombero integral', 'Bombero', 'Sanitas', 'Colpensiones', 'Positiva', NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 20:34:23', NULL),
('1124989349', 'Tatiana Andrea Guzman Galindo', 'F', 'Practicante Fundetec', 'Civil', 'Capresoca', 'NA', 'Positiva', NULL, 0, 'No aplica', '2026-05-04', NULL, 'activo', '3229496595', 'tgz57031@gmail.com', '2003-04-13', '2026-09-21 20:48:16', NULL),
('16672796', 'Juan Carlos Santacoloma Piedrahita', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 22:35:08', NULL),
('4284762', 'Jose Manuel Gutierrez Teatin', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 13:03:27', NULL),
('47428604', 'Graciela Garcia Chinchilla', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 12:57:55', NULL),
('47430097', 'Sthella Gutierrez', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 13:01:10', NULL),
('47441163', 'Sandra Milena Castaño Vargas', 'F', 'Administrativo', 'Civil', 'Sanitas', 'Porvenir', 'Positiva', 2071830.00, 0, 'Fijo', '2024-02-13', '2024-08-12', 'activo', NULL, NULL, '1983-06-13', '2026-09-22 19:56:25', NULL),
('52308103', 'Fanny Paola Mercado Delgado', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 13:10:43', NULL),
('7180789', 'Hector Favian Auzaque Parra', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 22:45:50', NULL),
('74770870', 'Ariosto Castelblanco Zorro', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 12:56:03', NULL),
('74814305', 'Nelson Morales Cubides', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 13:04:03', NULL),
('74859815', 'Waldo Ramirez Avila', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 13:04:43', NULL),
('74861711', 'Wilmar Vargas Teatin', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 13:05:33', NULL),
('9656509', 'Jose Orlando Gonzalez Gonzales', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 12:59:42', NULL),
('9658799', 'Javier Fernando Fuquen Calderon', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 12:57:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `festivos_colombia`
--
CREATE TABLE `festivos_colombia` (
  `id` int NOT NULL,
  `fecha` date NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `anio` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `festivos_colombia`
--

INSERT INTO `festivos_colombia` (`id`, `fecha`, `nombre`, `anio`) VALUES
(1, '2026-01-01', 'Año Nuevo', 2026),
(2, '2026-01-12', 'Reyes Magos', 2026),
(3, '2026-03-23', 'Día de San José', 2026),
(4, '2026-04-02', 'Jueves Santo', 2026),
(5, '2026-04-03', 'Viernes Santo', 2026),
(6, '2026-05-01', 'Día del Trabajo', 2026),
(7, '2026-05-18', 'Ascensión de Jesús', 2026),
(8, '2026-06-08', 'Corpus Christi', 2026),
(9, '2026-06-15', 'Sagrado Corazón', 2026),
(10, '2026-06-29', 'San Pedro y San Pablo', 2026),
(11, '2026-07-13', 'Día de Nuestra Señora de Chiquinquirá', 2026),
(12, '2026-07-20', 'Día de la Independencia', 2026),
(13, '2026-08-07', 'Batalla de Boyacá', 2026),
(14, '2026-08-17', 'Asunción de la Virgen', 2026),
(15, '2026-10-12', 'Día de la Raza', 2026),
(16, '2026-11-02', 'Todos los Santos', 2026),
(17, '2026-11-16', 'Independencia de Cartagena', 2026),
(18, '2026-12-08', 'Inmaculada Concepción', 2026),
(19, '2026-12-25', 'Navidad', 2026),
(20, '2027-01-01', 'Año Nuevo', 2027),
(21, '2027-01-11', 'Reyes Magos', 2027),
(22, '2027-03-22', 'Día de San José', 2027),
(23, '2027-03-25', 'Jueves Santo', 2027),
(24, '2027-03-26', 'Viernes Santo', 2027),
(25, '2027-05-01', 'Día del Trabajo', 2027),
(26, '2027-05-10', 'Ascensión de Jesús', 2027),
(27, '2027-05-31', 'Corpus Christi', 2027),
(28, '2027-06-07', 'Sagrado Corazón', 2027),
(29, '2027-07-05', 'San Pedro y San Pablo', 2027),
(30, '2027-07-12', 'Día de Nuestra Señora de Chiquinquirá', 2027),
(31, '2027-07-20', 'Día de la Independencia', 2027),
(32, '2027-08-07', 'Batalla de Boyacá', 2027),
(33, '2027-08-16', 'Asunción de la Virgen', 2027),
(34, '2027-10-18', 'Día de la Raza', 2027),
(35, '2027-11-01', 'Todos los Santos', 2027),
(36, '2027-11-15', 'Independencia de Cartagena', 2027),
(37, '2027-12-08', 'Inmaculada Concepción', 2027),
(38, '2027-12-25', 'Navidad', 2027);

-- --------------------------------------------------------

--
-- Table structure for table `firmas_guardadas`
--
CREATE TABLE `firmas_guardadas` (
  `id` int NOT NULL,
  `cedula` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruta_imagen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'PNG del canvas o archivo subido, en uploads/hv_{cedula}/firma/',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notificaciones`
--
CREATE TABLE `notificaciones` (
  `id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `destinatario_tipo` enum('talento_humano','empleado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'talento_humano',
  `usuario_nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cedula_empleado` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `campo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enlace` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ruta relativa a la que redirige la notificación',
  `leida` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `usuario_id`, `destinatario_tipo`, `usuario_nombre`, `cedula_empleado`, `campo`, `mensaje`, `enlace`, `leida`, `created_at`) VALUES
(1, 1, 'talento_humano', 'Talento', '74770870', 'creacion', '\"Tatiana\" creó un nuevo empleado llamado \"Ariosto Castelblanco Zorro\", con CC 74770870', '/chvb/public/empleados.php?q=74770870', 0, '2026-09-23 12:56:03'),
(2, 1, 'talento_humano', 'Talento', '9658799', 'creacion', '\"Tatiana\" creó un nuevo empleado llamado \"Javier Fernando Fuquen Calderon\", con CC 9658799', '/chvb/public/empleados.php?q=9658799', 0, '2026-09-23 12:57:02'),
(3, 1, 'talento_humano', 'Talento', '47428604', 'creacion', '\"Tatiana\" creó un nuevo empleado llamado \"Graciela Garcia Chinchilla\", con CC 47428604', '/chvb/public/empleados.php?q=47428604', 0, '2026-09-23 12:57:55'),
(4, 1, 'talento_humano', 'Talento', '9656509', 'creacion', '\"Tatiana\" creó un nuevo empleado llamado \"Jose Orlando Gonzalez Gonzales\", con CC 9656509', '/chvb/public/empleados.php?q=9656509', 0, '2026-09-23 12:59:42'),
(5, 1, 'talento_humano', 'Talento', '9656509', 'tipo_de_contrato', '\"Tatiana\" editó el tipo de contrato de \"Jose Orlando Gonzalez Gonzales\": antes \"sin definir\", ahora \"Fijo\"', '/chvb/public/empleados.php?q=9656509', 0, '2026-09-23 13:00:01'),
(6, 1, 'talento_humano', 'Talento', '47430097', 'creacion', '\"Tatiana\" creó un nuevo empleado llamado \"Sthella Gutierrez\", con CC 47430097', '/chvb/public/empleados.php?q=47430097', 0, '2026-09-23 13:01:10'),
(7, 1, 'talento_humano', 'Talento', '9656509', 'tipo_de_contrato', '\"Tatiana\" editó el tipo de contrato de \"Jose Orlando Gonzalez Gonzales\": antes \"Fijo\", ahora \"Indefinido\"', '/chvb/public/empleados.php?q=9656509', 0, '2026-09-23 13:01:26'),
(8, 1, 'talento_humano', 'Talento', '7180789', 'tipo_de_contrato', '\"Tatiana\" editó el tipo de contrato de \"Hector Favian Auzaque Parra\": antes \"Fijo\", ahora \"Indefinido\"', '/chvb/public/empleados.php?q=7180789', 0, '2026-09-23 13:01:39'),
(9, 1, 'talento_humano', 'Talento', '74770870', 'tipo_de_contrato', '\"Tatiana\" editó el tipo de contrato de \"Ariosto Castelblanco Zorro\": antes \"Fijo\", ahora \"Indefinido\"', '/chvb/public/empleados.php?q=74770870', 0, '2026-09-23 13:01:50'),
(10, 1, 'talento_humano', 'Talento', '9658799', 'tipo_de_contrato', '\"Tatiana\" editó el tipo de contrato de \"Javier Fernando Fuquen Calderon\": antes \"Fijo\", ahora \"Indefinido\"', '/chvb/public/empleados.php?q=9658799', 0, '2026-09-23 13:02:11'),
(11, 1, 'talento_humano', 'Talento', '47428604', 'tipo_de_contrato', '\"Tatiana\" editó el tipo de contrato de \"Graciela Garcia Chinchilla\": antes \"Fijo\", ahora \"Indefinido\"', '/chvb/public/empleados.php?q=47428604', 0, '2026-09-23 13:02:24'),
(12, 1, 'talento_humano', 'Talento', '4284762', 'creacion', '\"Tatiana\" creó un nuevo empleado llamado \"Jose Manuel Gutierrez Teatin\", con CC 4284762', '/chvb/public/empleados.php?q=4284762', 0, '2026-09-23 13:03:27'),
(13, 1, 'talento_humano', 'Talento', '74814305', 'creacion', '\"Tatiana\" creó un nuevo empleado llamado \"Nelson Morales Cubides\", con CC 74814305', '/chvb/public/empleados.php?q=74814305', 0, '2026-09-23 13:04:03'),
(14, 1, 'talento_humano', 'Talento', '74859815', 'creacion', '\"Tatiana\" creó un nuevo empleado llamado \"Waldo Ramirez Avila\", con CC 74859815', '/chvb/public/empleados.php?q=74859815', 0, '2026-09-23 13:04:43'),
(15, 1, 'talento_humano', 'Talento', '74861711', 'creacion', '\"Tatiana\" creó un nuevo empleado llamado \"Wilmar Vargas Teatin\", con CC 74861711', '/chvb/public/empleados.php?q=74861711', 0, '2026-09-23 13:05:33'),
(16, 1, 'talento_humano', 'Talento', '1115911058', 'creacion', '\"Tatiana\" creó un nuevo empleado llamado \"Edwar Santiago Alfonso Ducon\", con CC 1115911058', '/chvb/public/empleados.php?q=1115911058', 0, '2026-09-23 13:06:47'),
(17, 1, 'talento_humano', 'Talento', '1115911058', 'tipo_de_contrato', '\"Tatiana\" editó el tipo de contrato de \"Edwar Santiago Alfonso Ducon\": antes \"Indefinido\", ahora \"Fijo\"', '/chvb/public/empleados.php?q=1115911058', 0, '2026-09-23 13:07:05'),
(18, 1, 'talento_humano', 'Talento', '1019024577', 'creacion', '\"Tatiana\" creó un nuevo empleado llamado \"Nohora Rocio Duran Torres\", con CC 1019024577', '/chvb/public/empleados.php?q=1019024577', 0, '2026-09-23 13:07:52'),
(19, 1, 'talento_humano', 'Talento', '1029661794', 'creacion', '\"Tatiana\" creó un nuevo empleado llamado \"Darwin Camilo Bedoya Gutierrez\", con CC 1029661794', '/chvb/public/empleados.php?q=1029661794', 0, '2026-09-23 13:08:27'),
(20, 1, 'talento_humano', 'Talento', '1029643799', 'creacion', '\"Tatiana\" creó un nuevo empleado llamado \"Samuel Santiago Fonseca Patarroyo\", con CC 1029643799', '/chvb/public/empleados.php?q=1029643799', 0, '2026-09-23 13:09:04'),
(21, 1, 'talento_humano', 'Talento', '52308103', 'creacion', '\"Tatiana\" creó un nuevo empleado llamado \"Fanny Paola Mercado Delgado\", con CC 52308103', '/chvb/public/empleados.php?q=52308103', 0, '2026-09-23 13:10:43'),
(22, 1, 'talento_humano', 'Talento', '1118567328', 'creacion', '\"Tatiana\" creó un nuevo empleado llamado \"Nelson Fabian Chaparro Rincon\", con CC 1118567328', '/chvb/public/empleados.php?q=1118567328', 0, '2026-09-23 13:11:40'),
(23, 1, 'talento_humano', 'Talento', '1029661794', 'fecha_nacimiento', '\"Tatiana\" editó la fecha de nacimiento de \"Darwin Camilo Bedoya Gutierrez\": antes \"-\", ahora \"22 de septiembre de 2001\"', '/chvb/public/empleados.php?q=1029661794', 0, '2026-09-23 14:29:06'),
(24, 1, 'talento_humano', 'Talento', '1029661794', 'fecha_nacimiento', '\"Tatiana\" editó la fecha de nacimiento de \"Darwin Camilo Bedoya Gutierrez\": antes \"22 de septiembre de 2001\", ahora \"22 de septiembre de 2006\"', '/chvb/public/empleados.php?q=1029661794', 0, '2026-09-23 14:40:12'),
(25, 1, 'talento_humano', 'Talento', '1029661794', 'fecha_nacimiento', '\"Tatiana\" editó la fecha de nacimiento de \"Darwin Camilo Bedoya Gutierrez\": antes \"22 de septiembre de 2006\", ahora \"22 de septiembre de 2007\"', '/chvb/public/empleados.php?q=1029661794', 0, '2026-09-23 14:41:02');

-- --------------------------------------------------------

--
-- Table structure for table `permisos`
--
CREATE TABLE `permisos` (
  `id` int NOT NULL,
  `consecutivo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cedula_empleado` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_empleado_snapshot` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cargo_empleado_snapshot` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `celular_empleado_snapshot` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_permiso` enum('Permiso','Vacaciones','Licencia','Mision institucional') COLLATE utf8mb4_unicode_ci NOT NULL,
  `motivo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `total_horas` decimal(6,2) DEFAULT NULL,
  `incluye_festivo` tinyint(1) DEFAULT '0',
  `festivo_confirmado` tinyint(1) DEFAULT '0',
  `remunerado` tinyint(1) NOT NULL DEFAULT '0',
  `es_compensatorio` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_horas_extra` date DEFAULT NULL,
  `es_devolucion` tinyint(1) NOT NULL DEFAULT '0',
  `es_salida_pendiente_regreso` tinyint(1) NOT NULL DEFAULT '0',
  `devolucion_fecha` date DEFAULT NULL,
  `devolucion_hora_inicio` time DEFAULT NULL,
  `devolucion_hora_fin` time DEFAULT NULL,
  `devolucion_total_horas` decimal(6,2) DEFAULT NULL,
  `tiene_reemplazo` tinyint(1) NOT NULL DEFAULT '0',
  `cedula_reemplazo` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cedula_jefe` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_solicitante` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firma_solicitante` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_reemplazo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firma_reemplazo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_jefe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firma_jefe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_jefe_prefirmado` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firma_jefe_prefirmado` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `evidencia_archivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('en_proceso','por_firmar_reemplazo','por_firmar_jefe','por_firmar_jefe_final','firmado','devuelto','devuelto_regreso','rechazado','aprobado_pendiente_regreso','anulado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_proceso',
  `motivo_devolucion` text COLLATE utf8mb4_unicode_ci,
  `motivo_rechazo` text COLLATE utf8mb4_unicode_ci,
  `motivo_anulacion` text COLLATE utf8mb4_unicode_ci,
  `anulado_por` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_anulacion` timestamp NULL DEFAULT NULL,
  `version` int NOT NULL DEFAULT '1',
  `fecha_solicitud` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permisos_consecutivos`
--
CREATE TABLE `permisos_consecutivos` (
  `anio` int NOT NULL,
  `ultimo_numero` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permisos_devoluciones`
--
CREATE TABLE `permisos_devoluciones` (
  `id` int NOT NULL,
  `permiso_id` int NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `total_horas` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permisos_dias`
--
CREATE TABLE `permisos_dias` (
  `id` int NOT NULL,
  `permiso_id` int NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `es_festivo` tinyint(1) NOT NULL DEFAULT '0',
  `festivo_nombre` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incluido` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'si es_festivo=1, el empleado decide; si es_festivo=0, siempre 1',
  `horas_brutas` decimal(6,2) NOT NULL,
  `horas_descuento_almuerzo` decimal(6,2) NOT NULL DEFAULT '0.00',
  `horas_netas` decimal(6,2) NOT NULL COMMENT '0 si incluido=0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permisos_historial`
--
CREATE TABLE `permisos_historial` (
  `id` int NOT NULL,
  `permiso_id` int NOT NULL,
  `version_anterior` int NOT NULL,
  `estado_anterior` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado_nuevo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actor_tipo` enum('empleado','reemplazo','jefe','talento_humano') COLLATE utf8mb4_unicode_ci NOT NULL,
  `actor_cedula_o_usuario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detalle` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `presencia_empleados`
--
CREATE TABLE `presencia_empleados` (
  `cedula` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ultima_actividad` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--
CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('superadmin_talento_humano','auxiliar_talento_humano','teniente') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `intentos_fallidos` int DEFAULT '0',
  `bloqueado_hasta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password_hash`, `rol`, `created_at`, `intentos_fallidos`, `bloqueado_hasta`) VALUES
(1, 'Talento', '$2y$10$NGyVALKNFTFSp2RCTHsb8OkWNq5687eAzdD1fTtTEuSngyyk9lzQC', 'superadmin_talento_humano', '2026-09-17 19:26:28', 0, NULL),
(2, 'Tatiana', '$2y$10$cQdMMnhoNeo3U58ygsXVCuLtBi2RKOF8D0kZWidtQHoeygqR6URNK', 'auxiliar_talento_humano', '2026-09-17 21:38:01', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios_empleados`
--
CREATE TABLE `usuarios_empleados` (
  `id` int NOT NULL,
  `cedula` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'hash del PIN de 4 digitos',
  `activo` tinyint(1) DEFAULT '1',
  `intentos_fallidos` int DEFAULT '0',
  `bloqueado_hasta` datetime DEFAULT NULL,
  `pin_encriptado` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'PIN cifrado reversible, solo visible para superadmin/auxiliar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bolsillos`
--
ALTER TABLE `bolsillos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cedula_seccion` (`cedula_empleado`,`seccion`);

--
-- Indexes for table `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bolsillo_id` (`bolsillo_id`);

--
-- Indexes for table `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`cedula`);

--
-- Indexes for table `festivos_colombia`
--
ALTER TABLE `festivos_colombia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fecha` (`fecha`),
  ADD KEY `idx_anio` (`anio`);

--
-- Indexes for table `firmas_guardadas`
--
ALTER TABLE `firmas_guardadas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unica_por_empleado` (`cedula`);

--
-- Indexes for table `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `permisos`
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
-- Indexes for table `permisos_consecutivos`
--
ALTER TABLE `permisos_consecutivos`
  ADD PRIMARY KEY (`anio`);

--
-- Indexes for table `permisos_devoluciones`
--
ALTER TABLE `permisos_devoluciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_permiso` (`permiso_id`);

--
-- Indexes for table `permisos_dias`
--
ALTER TABLE `permisos_dias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_permiso` (`permiso_id`),
  ADD KEY `idx_fecha` (`fecha`);

--
-- Indexes for table `permisos_historial`
--
ALTER TABLE `permisos_historial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_permiso` (`permiso_id`);

--
-- Indexes for table `presencia_empleados`
--
ALTER TABLE `presencia_empleados`
  ADD PRIMARY KEY (`cedula`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `usuarios_empleados`
--
ALTER TABLE `usuarios_empleados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula` (`cedula`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bolsillos`
--
ALTER TABLE `bolsillos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1148;

--
-- AUTO_INCREMENT for table `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `festivos_colombia`
--
ALTER TABLE `festivos_colombia`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `firmas_guardadas`
--
ALTER TABLE `firmas_guardadas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permisos_devoluciones`
--
ALTER TABLE `permisos_devoluciones`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permisos_dias`
--
ALTER TABLE `permisos_dias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permisos_historial`
--
ALTER TABLE `permisos_historial`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `usuarios_empleados`
--
ALTER TABLE `usuarios_empleados`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bolsillos`
--
ALTER TABLE `bolsillos`
  ADD CONSTRAINT `bolsillos_ibfk_1` FOREIGN KEY (`cedula_empleado`) REFERENCES `empleados` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`bolsillo_id`) REFERENCES `bolsillos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `firmas_guardadas`
--
ALTER TABLE `firmas_guardadas`
  ADD CONSTRAINT `firmas_guardadas_ibfk_1` FOREIGN KEY (`cedula`) REFERENCES `empleados` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permisos`
--
ALTER TABLE `permisos`
  ADD CONSTRAINT `permisos_ibfk_1` FOREIGN KEY (`cedula_empleado`) REFERENCES `empleados` (`cedula`) ON UPDATE CASCADE,
  ADD CONSTRAINT `permisos_ibfk_2` FOREIGN KEY (`cedula_reemplazo`) REFERENCES `empleados` (`cedula`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `permisos_ibfk_3` FOREIGN KEY (`cedula_jefe`) REFERENCES `empleados` (`cedula`) ON UPDATE CASCADE;

--
-- Constraints for table `permisos_devoluciones`
--
ALTER TABLE `permisos_devoluciones`
  ADD CONSTRAINT `permisos_devoluciones_ibfk_1` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permisos_dias`
--
ALTER TABLE `permisos_dias`
  ADD CONSTRAINT `permisos_dias_ibfk_1` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permisos_historial`
--
ALTER TABLE `permisos_historial`
  ADD CONSTRAINT `permisos_historial_ibfk_1` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `presencia_empleados`
--
ALTER TABLE `presencia_empleados`
  ADD CONSTRAINT `presencia_empleados_ibfk_1` FOREIGN KEY (`cedula`) REFERENCES `empleados` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `usuarios_empleados`
--
ALTER TABLE `usuarios_empleados`
  ADD CONSTRAINT `usuarios_empleados_ibfk_1` FOREIGN KEY (`cedula`) REFERENCES `empleados` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
