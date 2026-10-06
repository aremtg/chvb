-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20260915.9e4dc5b5f4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 06, 2026 at 04:15 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `cargos`
--
CREATE TABLE `cargos` (
  `id` int UNSIGNED NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificados_consecutivos`
--
CREATE TABLE `certificados_consecutivos` (
  `id` tinyint UNSIGNED NOT NULL,
  `ultimo_numero` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificados_laborales`
--
CREATE TABLE `certificados_laborales` (
  `id` int UNSIGNED NOT NULL,
  `numero` int UNSIGNED NOT NULL,
  `consecutivo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('actual','retirado') COLLATE utf8mb4_unicode_ci NOT NULL,
  `cedula` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_snapshot` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cargo_snapshot` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_retiro` date DEFAULT NULL,
  `archivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `creado_por` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificados_laborales_funciones`
--
CREATE TABLE `certificados_laborales_funciones` (
  `certificado_id` int UNSIGNED NOT NULL,
  `posicion` tinyint UNSIGNED NOT NULL,
  `funcion_id` int UNSIGNED DEFAULT NULL,
  `texto_snapshot` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

--
-- Dumping data for table `documentos`
--

INSERT INTO `documentos` (`id`, `bolsillo_id`, `nombre_archivo`, `ruta`, `orden`, `fecha_subida`, `subido_por_cedula`, `subido_por_tipo`) VALUES
(1, 497, 'Certificado.pdf', 'hv_1118555586/hoja_de_vida/hv_formal_hv_1118555586/Certificado_1790728449.pdf', 1, '2026-09-30 00:33:51', NULL, 'panel'),
(2, 1320, 'Certificado_de_Afiliación.pdf', 'hv_80033385/documentos_contractuales/certificadoARL_hv_80033385/Certificado_de_Afiliaci__n_1790728762.pdf', 1, '2026-09-30 00:39:05', NULL, 'panel'),
(3, 621, 'Certificado_1790728449.pdf', 'hv_1118555586/hoja_de_vida/hv_formal_hv_1118555586/Certificado_1790728449.pdf', 1, '2026-10-01 19:07:27', NULL, 'panel'),
(4, 1506, 'certificado positiva.pdf', 'hv_80033385/documentos_contractuales/certificadoARL_hv_80033385/certificado_positiva_1790899379.pdf', 1, '2026-10-01 19:07:30', NULL, 'panel'),
(5, 1573, 'CONTRATO_JORGE_SEGURA_1791204830.pdf', 'hv_80033385/documentos_contractuales/contratosFirmados_hv_80033385/CONTRATO_JORGE_SEGURA_1791204830.pdf', 1, '2026-10-06 12:54:58', NULL, 'panel'),
(7, 901, 'CAMILO CORREDOR.pdf', 'hv_1118573216/hoja_de_vida/cedula_hv_1118573216/CAMILO_CORREDOR_1791297425.pdf', 1, '2026-10-06 14:37:05', NULL, 'panel'),
(8, 684, 'ANGEL GABRIEL CAMARGO PEZCA.pdf', 'hv_1118555586/hoja_de_vida/cedula_hv_1118555586/ANGEL_GABRIEL_CAMARGO_PEZCA_1791298074.pdf', 1, '2026-10-06 14:47:54', NULL, 'panel'),
(9, 932, 'ANGELA BRITHEY MALDONADO.pdf', 'hv_1118575006/hoja_de_vida/cedula_hv_1118575006/ANGELA_BRITHEY_MALDONADO_1791298114.pdf', 2, '2026-10-06 14:48:34', NULL, 'panel'),
(10, 157, 'YESID LARGO.pdf', 'hv_1007703611/hoja_de_vida/cedula_hv_1007703611/YESID_LARGO_1791298230.pdf', 1, '2026-10-06 14:50:30', NULL, 'panel');

-- --------------------------------------------------------

--
-- Table structure for table `empleados`
--
CREATE TABLE `empleados` (
  `cedula` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Max 10 caracteres, permite extranjera alfanumérica',
  `lugar_expedicion` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Municipio, Departamento (lista en public/assets/data/municipios.json)',
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sexo` enum('F','M') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo` enum('Auxiliar en Talento Humano','Director de talento humano','Auxiliar de Extintores','Enfermero/a','Practicante Sena','Practicante Fundetec','Practicante otra entidad','Servicios Generales','Maquinista','Guardia','Recepcionista','Administrativo','Auxiliar administrativo','Director Académico','Director de negocios','Tecnico en soporte sistemas','Tecnico archivista','Coordinador SST','Auxiliar SST','Jefe de prensa','Contador','Auxiliar de contaduría','Almacenista','Supervisor','Coordinador de banda','Conductor de ambulancia','Aspirante','Voluntario','Secretario recaudador','Auxiliar de enfermería','Docente de banda marcial','PAMEC','Revisor(a) fiscal','Comandante de estación','Directora administrativa y financiera','Bombero integral') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_de_personal` enum('Bombero','Civil') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eps` enum('Sanitas','Nueva EPS','Capresoca','Salud Total') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pension` enum('Colfondos','Porvenir','Colpensiones','Protección','NA') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `arl` enum('Positiva','SURA','Colmena','AXA Colpatria','Seguros Bolívar') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salario_basico` decimal(12,2) DEFAULT NULL,
  `es_bombero_integral` tinyint(1) DEFAULT '0',
  `tipo_jornada` enum('Turnos','Administrativa','Restringida') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Administrativa' COMMENT 'Cómo se cuentan horas/días en permisos: Turnos (operativo), Administrativa (07:00-17:24) o Restringida (horario reducido)',
  `jornada_hora_entrada` time DEFAULT NULL COMMENT 'Solo si tipo_jornada = Restringida',
  `jornada_hora_salida` time DEFAULT NULL COMMENT 'Solo si tipo_jornada = Restringida',
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

INSERT INTO `empleados` (`cedula`, `lugar_expedicion`, `nombre`, `sexo`, `cargo`, `tipo_de_personal`, `eps`, `pension`, `arl`, `salario_basico`, `es_bombero_integral`, `tipo_jornada`, `jornada_hora_entrada`, `jornada_hora_salida`, `tipo_de_contrato`, `fecha_inicio_contrato`, `fecha_fin_contrato`, `estado`, `celular`, `correo`, `fecha_nacimiento`, `created_at`, `foto`) VALUES
('1005719736', NULL, 'Carlos Augusto Triana Lozano', 'M', 'Tecnico en soporte sistemas', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'OPS', NULL, '2026-10-06', 'activo', NULL, NULL, NULL, '2026-09-29 23:30:02', NULL),
('1006555838', 'Yopal, Casanare', 'Lourdes Ester Guarin Garcia', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2025-05-05', '2025-11-04', 'activo', NULL, NULL, NULL, '2026-09-23 03:32:54', NULL),
('1006556137', NULL, 'Javier David Moreno', 'M', 'Auxiliar de Extintores', 'Civil', 'Nueva EPS', NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', '2026-03-12', '2026-09-11', 'activo', '3224045766', NULL, NULL, '2026-09-23 22:46:04', NULL),
('1006556671', NULL, 'Jhon Marco Rincon Castaño', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2026-01-08', NULL, 'activo', NULL, NULL, NULL, '2026-09-23 03:41:28', NULL),
('1006636306', NULL, 'Karen Lizeth Diaz Pineda', 'F', 'Auxiliar de Extintores', 'Bombero', 'Sanitas', 'Porvenir', NULL, NULL, 1, 'Turnos', NULL, NULL, 'Fijo', '2026-04-09', '2026-10-08', 'activo', NULL, NULL, NULL, '2026-09-23 23:07:53', NULL),
('1007703611', NULL, 'Eduard Yecid Largo', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 1, 'Turnos', NULL, NULL, 'Fijo', '2024-04-05', '2027-04-04', 'activo', NULL, NULL, '1995-11-15', '2026-09-29 21:01:48', NULL),
('1019024577', NULL, 'Nohora Rocio Duran Torres', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2026-08-01', NULL, 'activo', NULL, NULL, NULL, '2026-09-23 18:07:51', NULL),
('1029643799', NULL, 'Samuel Santiago Fonseca Patarroyo', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2026-08-07', NULL, 'activo', NULL, NULL, NULL, '2026-09-23 18:09:04', NULL),
('1029661794', NULL, 'Darwin Camilo Bedoya Gutierrez', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2026-08-07', NULL, 'activo', NULL, NULL, '2007-09-22', '2026-09-23 18:08:27', NULL),
('1115911058', NULL, 'Edwar Santiago Alfonso Ducon', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2026-08-01', NULL, 'activo', NULL, NULL, '2006-01-27', '2026-09-23 18:06:47', NULL),
('1115913555', NULL, 'Wilder Andrey Chaparro Chaparro', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2026-01-01', NULL, 'activo', NULL, NULL, '1991-11-28', '2026-09-23 03:43:10', NULL),
('1116043143', NULL, 'Lina Maria Aponte Fonseca', 'F', 'Auxiliar SST', 'Civil', 'Sanitas', NULL, 'Positiva', 1964430.00, 0, 'Administrativa', NULL, NULL, 'Fijo', '2024-05-15', '2024-08-14', 'activo', NULL, NULL, '1997-11-10', '2026-09-25 02:25:10', NULL),
('1116552720', 'Aguazul, Casanare', 'Juan Fernando Dominguez Ibarguen', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 1, 'Turnos', NULL, NULL, 'Fijo', '2026-01-08', NULL, 'activo', NULL, NULL, '1997-02-10', '2026-09-23 03:38:48', NULL),
('1118198423', NULL, 'Carlos Hugo Cubides Villalba', 'M', 'Auxiliar administrativo', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2025-02-04', '2025-05-03', 'activo', NULL, NULL, NULL, '2026-10-03 03:22:07', NULL),
('1118529611', NULL, 'Jimmy Alejandro Garcia Chinchilla', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2025-03-22', NULL, 'activo', NULL, NULL, NULL, '2026-09-23 02:08:05', NULL),
('1118530819', NULL, 'Omar David Linares Alvarez', 'M', 'Auxiliar de contaduría', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', '2025-10-01', '2026-04-30', 'activo', '3005484351', NULL, NULL, '2026-09-29 23:32:41', NULL),
('1118534974', NULL, 'Soraida Sepulveda Gordillo', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2026-04-15', NULL, 'activo', NULL, NULL, NULL, '2026-09-23 03:25:05', NULL),
('1118536550', NULL, 'Rodrigo Hernan Ramirez Morales', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2024-10-01', '2025-03-31', 'activo', NULL, NULL, NULL, '2026-10-05 21:13:59', NULL),
('1118543385', NULL, 'Lewis Arfrey Ardila Achagua', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2025-05-01', '2025-11-30', 'activo', NULL, NULL, '1989-11-28', '2026-09-23 03:30:44', NULL),
('1118544837', NULL, 'José Ferney Rodriguez Barrera', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2015-12-01', '2016-11-30', 'activo', NULL, NULL, NULL, '2026-09-23 02:05:57', NULL),
('1118547243', NULL, 'Tito Enrique Camargo Pezca', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2019-01-15', NULL, 'activo', NULL, NULL, '1991-04-21', '2026-09-23 01:11:57', 'hv_1118547243/perfil/foto.jpg'),
('1118547356', 'Yopal, Casanare', 'Adriana Marcela Galan Hernandez', 'F', 'Directora administrativa y financiera', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', NULL, NULL, 'no activo', NULL, NULL, NULL, '2026-10-06 15:10:32', NULL),
('1118550799', NULL, 'Deyna Yurany Torres Cuervo', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2024-01-17', NULL, 'activo', NULL, NULL, NULL, '2026-09-23 03:27:52', NULL),
('1118555586', 'Yopal, Casanare', 'Angel Gabriel Camargo Pezca', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 1, 'Turnos', NULL, NULL, 'Fijo', '2021-09-24', NULL, 'activo', NULL, NULL, '1993-10-05', '2026-09-23 02:06:39', 'hv_1118555586/perfil/foto.jpg'),
('111856453', 'Yopal, Casanare', 'Astrid Mariana Aquite Gómez', 'F', 'Auxiliar en Talento Humano', 'Bombero', 'Nueva EPS', 'Colfondos', 'Positiva', 1964430.00, 1, 'Restringida', '08:00:00', '12:00:00', 'Fijo', '2024-02-15', '2024-08-15', 'activo', '3209308877', NULL, '1996-04-13', '2026-09-23 00:46:12', NULL),
('1118564997', NULL, 'Kewin Alexis Adan Jeronimo', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', NULL, NULL, 'no activo', NULL, NULL, NULL, '2026-09-23 03:18:30', NULL),
('1118565906', NULL, 'Jeidi Carolina Acevedo Lopez', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2025-11-12', '2026-05-11', 'activo', NULL, NULL, NULL, '2026-09-23 03:44:42', NULL),
('1118565958', NULL, 'Yeritsa Tatiana Egue Chaparro', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2024-11-27', NULL, 'activo', NULL, NULL, NULL, '2026-09-30 15:04:28', NULL),
('1118567328', NULL, 'Nelson Fabian Chaparro Rincon', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2016-02-11', NULL, 'activo', NULL, NULL, NULL, '2026-09-23 18:11:40', NULL),
('1118572004', NULL, 'Luisa Fernanda Abril Bernal', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2024-09-05', NULL, 'activo', NULL, NULL, NULL, '2026-09-30 15:01:28', NULL),
('1118573216', 'Yopal, Casanare', 'Camilo Andres Corredor Garcia', 'M', 'Maquinista', 'Bombero', NULL, NULL, NULL, NULL, 1, 'Turnos', NULL, NULL, 'Fijo', '2024-11-01', '2025-04-30', 'activo', NULL, NULL, '1999-01-17', '2026-09-23 03:22:10', NULL),
('1118575006', 'Yopal, Casanare', 'Angela Brithey Maldonado', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2025-05-16', '2025-11-15', 'activo', NULL, NULL, '1999-08-24', '2026-09-23 03:37:51', 'hv_1118575006/perfil/foto.jpg'),
('1118775342', NULL, 'Daniel Fernando Gutierrez Riaño', 'M', 'Bombero integral', 'Bombero', 'Sanitas', 'Colpensiones', 'Positiva', 0.00, 0, 'Turnos', NULL, NULL, 'Fijo', '2024-01-17', '2025-01-17', 'activo', NULL, NULL, '1993-03-12', '2026-09-23 01:04:54', NULL),
('11206377', 'Yopal, Casanare', 'Juan Fernando Guzman Guzman', 'M', 'Bombero integral', 'Bombero', 'Sanitas', 'Colpensiones', 'Positiva', NULL, 1, 'Turnos', NULL, NULL, 'Fijo', '2023-07-21', NULL, 'activo', NULL, NULL, '1995-12-08', '2026-09-23 01:34:23', NULL),
('1121898640', NULL, 'Arlyn Johanna Sanchez Gutierrez', 'F', 'Auxiliar administrativo', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 22:47:28', NULL),
('1124989349', 'Aguazul, Casanare', 'Tatiana Andrea Guzman Galindo', 'F', 'Practicante Fundetec', 'Civil', 'Capresoca', 'NA', 'Positiva', NULL, 0, 'Administrativa', NULL, NULL, 'No aplica', '2026-05-04', NULL, 'activo', '3229496595', 'tgz57031@gmail.com', '2003-04-13', '2026-09-22 01:48:16', 'hv_1124989349/perfil/foto.jpg'),
('1143954094', NULL, 'Jonnathan Alexander Daza Barrera', 'M', 'Secretario recaudador', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', NULL, NULL, 'activo', NULL, NULL, '1993-02-02', '2026-09-25 02:29:26', NULL),
('16672796', NULL, 'Juan Carlos Santacoloma Piedrahita', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2025-05-15', '2025-11-14', 'activo', NULL, NULL, NULL, '2026-09-23 03:35:08', NULL),
('4284762', NULL, 'Jose Manuel Gutierrez Teatin', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', '1999-08-20', NULL, 'activo', NULL, NULL, '1970-09-29', '2026-09-23 18:03:27', NULL),
('47428604', NULL, 'Graciela Garcia Chinchilla', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', '2010-02-01', NULL, 'activo', NULL, NULL, NULL, '2026-09-23 17:57:55', NULL),
('47430097', NULL, 'Sthella Gutierrez', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', '2007-12-04', NULL, 'activo', NULL, NULL, NULL, '2026-09-23 18:01:10', NULL),
('47431008', 'Yopal, Casanare', 'Romelia Medina Martinez', 'F', 'Servicios Generales', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', NULL, '2027-02-04', 'activo', NULL, NULL, NULL, '2026-10-06 15:32:04', NULL),
('47441163', NULL, 'Sandra Milena Castaño Vargas', 'F', 'Administrativo', 'Civil', 'Sanitas', 'Porvenir', 'Positiva', 2071830.00, 0, 'Administrativa', NULL, NULL, 'Fijo', '2024-02-13', '2024-08-12', 'activo', NULL, NULL, '1983-06-13', '2026-09-23 00:56:25', NULL),
('47441979', NULL, 'Angela Maria Moreno', 'F', 'Comandante de estación', 'Bombero', NULL, NULL, NULL, NULL, 1, 'Turnos', NULL, NULL, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-29 21:33:52', 'hv_47441979/perfil/foto.jpg'),
('52308103', NULL, 'Fanny Paola Mercado Delgado', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2022-07-07', NULL, 'activo', NULL, NULL, '1975-10-14', '2026-09-23 18:10:43', NULL),
('7180789', NULL, 'Hector Favian Auzaque Parra', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', '2008-03-10', NULL, 'activo', NULL, NULL, NULL, '2026-09-23 03:45:50', NULL),
('7254795', NULL, 'Yobanis Alberto Castrillon Cano', 'M', 'Maquinista', 'Bombero', NULL, 'Colpensiones', 'Positiva', NULL, 1, 'Turnos', NULL, NULL, 'Fijo', '2024-07-01', '2025-06-30', 'activo', NULL, NULL, NULL, '2026-09-29 20:58:48', NULL),
('74770870', NULL, 'Ariosto Castelblanco Zorro', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', '2008-06-01', NULL, 'activo', NULL, NULL, NULL, '2026-09-23 17:56:03', NULL),
('74814305', 'Yopal, Casanare', 'Nelson Morales Cubides', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 1, 'Turnos', NULL, NULL, 'Indefinido', '2007-11-01', NULL, 'activo', NULL, NULL, '1979-11-14', '2026-09-23 18:04:03', NULL),
('74859815', NULL, 'Waldo Ramirez Avila', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', '2005-01-11', NULL, 'activo', NULL, NULL, NULL, '2026-09-23 18:04:43', NULL),
('74861664', NULL, 'Guillermo Enrique Guarin Fonseca', 'M', 'Director Académico', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', NULL, NULL, 'activo', '3123878482', NULL, '1979-09-25', '2026-09-25 02:19:35', NULL),
('74861711', NULL, 'Wilmar Vargas Teatin', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 18:05:33', NULL),
('80033385', NULL, 'Jorge Antonio Segura Poveda', 'M', 'Conductor de ambulancia', 'Bombero', 'Sanitas', 'Colpensiones', 'Positiva', NULL, 1, 'Turnos', NULL, NULL, 'Fijo', '2026-09-26', '2026-12-25', 'activo', '3212038841', NULL, '1982-05-13', '2026-09-25 02:02:05', NULL),
('9433076', NULL, 'Rafael Rojas Rico', 'M', 'Comandante de estación', 'Bombero', 'Sanitas', 'Colpensiones', 'Positiva', 7046.33, 1, 'Turnos', NULL, NULL, 'Indefinido', NULL, NULL, 'activo', '3216547896', NULL, NULL, '2026-09-29 21:05:27', 'hv_9433076/perfil/foto.jpg'),
('9434678', NULL, 'Jose Alejandro Fernandez Cardenas', 'M', 'Tecnico archivista', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', '2015-02-11', '2016-11-30', 'activo', NULL, NULL, NULL, '2026-10-05 20:04:25', NULL),
('9656509', NULL, 'Jose Orlando Gonzalez Gonzales', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', '2013-02-26', NULL, 'activo', NULL, NULL, NULL, '2026-09-23 17:59:42', NULL),
('9658799', NULL, 'Javier Fernando Fuquen Calderon', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', '2009-04-01', NULL, 'activo', NULL, NULL, '1971-12-05', '2026-09-23 17:57:02', NULL);

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
(19, '2026-12-25', 'Navidad', 2026);

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
-- Table structure for table `funciones_certificados`
--
CREATE TABLE `funciones_certificados` (
  `id` int UNSIGNED NOT NULL,
  `cargo_id` int UNSIGNED NOT NULL,
  `texto` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `funciones_contratos`
--
CREATE TABLE `funciones_contratos` (
  `id` int UNSIGNED NOT NULL,
  `cargo_id` int UNSIGNED NOT NULL,
  `texto` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
(2, 'Tatiana', '$2y$10$cQdMMnhoNeo3U58ygsXVCuLtBi2RKOF8D0kZWidtQHoeygqR6URNK', 'auxiliar_talento_humano', '2026-09-17 21:38:01', 0, NULL),
(3, 'Talento', '$2y$10$jTKCp2WcjVoE1cyGKmqqxekoeukZ6bKaJLyOdOzv67Tcw0a4C9Wmm', 'superadmin_talento_humano', '2026-09-29 21:24:40', 0, NULL),
(4, 'Omar', '$2y$10$Ub.R51IzfyUY0Rs4ROKP0OQsn9VjxxsmJfRZsaBo8oMouHdi49pZ2', 'teniente', '2026-09-29 22:43:28', 0, NULL);

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
-- Indexes for table `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cargos_nombre` (`nombre`);

--
-- Indexes for table `certificados_consecutivos`
--
ALTER TABLE `certificados_consecutivos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `certificados_laborales`
--
ALTER TABLE `certificados_laborales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cert_numero` (`numero`),
  ADD UNIQUE KEY `uq_cert_archivo` (`archivo`),
  ADD KEY `idx_cert_cedula` (`cedula`);

--
-- Indexes for table `certificados_laborales_funciones`
--
ALTER TABLE `certificados_laborales_funciones`
  ADD PRIMARY KEY (`certificado_id`,`posicion`),
  ADD KEY `fk_clf_func` (`funcion_id`);

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
-- Indexes for table `funciones_certificados`
--
ALTER TABLE `funciones_certificados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_fcert_cargo_texto` (`cargo_id`,`texto`),
  ADD KEY `idx_fcert_cargo` (`cargo_id`,`activo`,`orden`);

--
-- Indexes for table `funciones_contratos`
--
ALTER TABLE `funciones_contratos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fcont_cargo` (`cargo_id`,`activo`,`orden`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cargos`
--
ALTER TABLE `cargos`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificados_laborales`
--
ALTER TABLE `certificados_laborales`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
-- AUTO_INCREMENT for table `funciones_certificados`
--
ALTER TABLE `funciones_certificados`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `funciones_contratos`
--
ALTER TABLE `funciones_contratos`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- Constraints for table `certificados_laborales_funciones`
--
ALTER TABLE `certificados_laborales_funciones`
  ADD CONSTRAINT `fk_clf_cert` FOREIGN KEY (`certificado_id`) REFERENCES `certificados_laborales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_clf_func` FOREIGN KEY (`funcion_id`) REFERENCES `funciones_certificados` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `funciones_certificados`
--
ALTER TABLE `funciones_certificados`
  ADD CONSTRAINT `fk_fcert_cargo` FOREIGN KEY (`cargo_id`) REFERENCES `cargos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `funciones_contratos`
--
ALTER TABLE `funciones_contratos`
  ADD CONSTRAINT `fk_fcont_cargo` FOREIGN KEY (`cargo_id`) REFERENCES `cargos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
