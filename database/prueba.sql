-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20260915.9e4dc5b5f4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 22, 2026 at 10:52 PM
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
(682, '7180789', 'documentos_contractuales', 'otros', 'Otros', 18, NULL, NULL, 0, NULL, NULL, NULL, 35);

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
('1118573216', 'Camilo Andres Corredor Garcia', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 22:22:10', NULL),
('1118575006', 'Angela Brithey Maldonado', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 22:37:51', NULL),
('1118775342', 'Daniel Fernando Gutierrez Riaño', 'M', 'Bombero integral', 'Bombero', 'Sanitas', 'Colpensiones', 'Positiva', 0.00, 0, 'Fijo', '2024-01-17', '2024-06-17', 'activo', NULL, NULL, '1993-03-12', '2026-09-22 20:04:54', NULL),
('11206377', 'Juan Fernando Guzman Guzman', 'M', 'Bombero integral', 'Bombero', 'Sanitas', 'Colpensiones', 'Positiva', NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 20:34:23', NULL),
('1124989349', 'Tatiana Andrea Guzman Galindo', 'F', 'Practicante Fundetec', 'Civil', 'Capresoca', 'NA', 'Positiva', NULL, 0, 'No aplica', '2026-05-04', NULL, 'activo', '3229496595', 'tgz57031@gmail.com', '2003-04-13', '2026-09-21 20:48:16', NULL),
('16672796', 'Juan Carlos Santacoloma Piedrahita', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 22:35:08', NULL),
('47441163', 'Sandra Milena Castaño Vargas', 'F', 'Administrativo', 'Civil', 'Sanitas', 'Porvenir', 'Positiva', 2071830.00, 0, 'Fijo', '2024-02-13', '2024-08-12', 'activo', NULL, NULL, '1983-06-13', '2026-09-22 19:56:25', NULL),
('7180789', 'Hector Favian Auzaque Parra', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-22 22:45:50', NULL);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=683;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

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
