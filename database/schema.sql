-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-10-2026 a las 01:45:32
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
(6, 776, 'Certificado_1790728449.pdf', 'hv_1118555586/hoja_de_vida/hv_formal_hv_1118555586/Certificado_1790728449.pdf', 1, '2026-10-08 00:10:12', NULL, 'panel'),
(7, 1692, 'certificado_positiva_1790899379.pdf', 'hv_80033385/documentos_contractuales/certificadoARL_hv_80033385/certificado_positiva_1790899379.pdf', 1, '2026-10-08 00:10:12', NULL, 'panel');

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
('1005719736', 'Yopal, Casanare', 'Carlos Augusto Triana Lozano', 'M', 'Tecnico en soporte sistemas', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'OPS', NULL, '2026-10-06', 'activo', NULL, NULL, NULL, '2026-09-29 23:30:02', NULL),
('1006555204', 'Maní, Casanare', 'Usuario Prueba', 'M', 'Tecnico en soporte sistemas', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-10-07 00:28:58', NULL),
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
('1118575006', 'Yopal, Casanare', 'Angela Brithey Maldonado', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2025-05-16', '2025-11-15', 'activo', NULL, NULL, '1999-08-24', '2026-09-23 03:37:51', NULL),
('1118775342', 'Yopal, Casanare', 'Daniel Fernando Gutierrez Riaño', 'M', 'Bombero integral', 'Bombero', 'Sanitas', 'Colpensiones', 'Positiva', 0.00, 0, 'Turnos', NULL, NULL, 'Fijo', '2024-01-17', '2025-01-17', 'activo', NULL, NULL, '1993-03-12', '2026-09-23 01:04:54', NULL),
('11206377', 'Yopal, Casanare', 'Juan Fernando Guzman Guzman', 'M', 'Bombero integral', 'Bombero', 'Sanitas', 'Colpensiones', 'Positiva', NULL, 1, 'Turnos', NULL, NULL, 'Fijo', '2023-07-21', NULL, 'activo', NULL, NULL, '1995-12-08', '2026-09-23 01:34:23', NULL),
('1121898640', 'Villavicencio, Meta', 'Arlyn Johanna Sanchez Gutierrez', 'F', 'Auxiliar administrativo', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 22:47:28', NULL),
('1124989349', 'Aguazul, Casanare', 'Tatiana Andrea Guzman Galindo', 'F', 'Practicante Fundetec', 'Civil', 'Capresoca', 'NA', 'Positiva', NULL, 0, 'Administrativa', NULL, NULL, 'No aplica', '2026-05-04', NULL, 'activo', '3229496595', 'tgz57031@gmail.com', '2003-04-13', '2026-09-22 01:48:16', 'hv_1124989349/perfil/foto.jpg'),
('1143954094', 'Yopal, Casanare', 'Jonnathan Alexander Daza Barrera', 'M', 'Secretario recaudador', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', NULL, NULL, 'activo', NULL, NULL, '1993-02-02', '2026-09-25 02:29:26', NULL),
('16672796', 'Yopal, Casanare', 'Juan Carlos Santacoloma Piedrahita', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Fijo', '2025-05-15', '2025-11-14', 'activo', NULL, NULL, '1962-08-27', '2026-09-23 03:35:08', NULL),
('33445352', 'Yopal, Casanare', 'Gladys Escobar De Hernandez', 'F', 'Revisor(a) fiscal', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', '2026-01-01', '2027-01-01', 'activo', NULL, NULL, '1951-07-13', '2026-10-07 19:41:51', NULL),
('4284762', 'Yopal, Casanare', 'Jose Manuel Gutierrez Teatin', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', '1999-08-20', NULL, 'activo', NULL, NULL, '1970-09-29', '2026-09-23 18:03:27', NULL),
('47428604', 'Nunchía, Casanare', 'Graciela Garcia Chinchilla', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', '2010-02-01', NULL, 'activo', NULL, NULL, '1968-07-26', '2026-09-23 17:57:55', NULL),
('47430097', 'Yopal, Casanare', 'Sthella Gutierrez', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Turnos', NULL, NULL, 'Indefinido', '2007-12-04', NULL, 'activo', NULL, NULL, '1972-05-12', '2026-09-23 18:01:10', NULL),
('47431008', 'Yopal, Casanare', 'Romelia Medina Martinez', 'F', 'Servicios Generales', 'Civil', NULL, NULL, NULL, NULL, 0, 'Administrativa', NULL, NULL, 'Fijo', '2019-02-18', '2020-02-18', 'activo', NULL, NULL, '1973-03-03', '2026-10-06 15:32:04', NULL),
('47441163', 'Yopal, Casanare', 'Sandra Milena Castaño Vargas', 'F', 'Administrativo', 'Civil', 'Sanitas', 'Porvenir', 'Positiva', 2071830.00, 0, 'Administrativa', NULL, NULL, 'Fijo', '2024-02-13', '2024-08-12', 'activo', NULL, NULL, '1983-06-13', '2026-09-23 00:56:25', NULL),
('47441979', 'Yopal, Casanare', 'Angela Maria Moreno', 'F', 'Subcomandante', 'Bombero', NULL, NULL, NULL, NULL, 1, 'Turnos', NULL, NULL, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, '1983-06-09', '2026-09-29 21:33:52', 'hv_47441979/perfil/foto.jpg'),
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
-- Estructura de tabla para la tabla `festivos_colombia`
--

CREATE TABLE `festivos_colombia` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `anio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `festivos_colombia`
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
(45, '2027-05-10', 'Ascensión de Jesús', 2027),
(46, '2027-05-31', 'Corpus Christi', 2027),
(47, '2027-06-07', 'Sagrado Corazón', 2027),
(48, '2027-07-05', 'San Pedro y San Pablo', 2027),
(49, '2027-07-12', 'Día de Nuestra Señora de Chiquinquirá', 2027),
(50, '2027-07-20', 'Día de la Independencia', 2027),
(51, '2027-08-07', 'Batalla de Boyacá', 2027),
(52, '2027-08-16', 'Asunción de la Virgen', 2027),
(53, '2027-10-18', 'Día de la Raza', 2027),
(54, '2027-11-01', 'Todos los Santos', 2027),
(55, '2027-11-15', 'Independencia de Cartagena', 2027),
(56, '2027-12-08', 'Inmaculada Concepción', 2027),
(57, '2027-12-25', 'Navidad', 2027),
(58, '2028-01-01', 'Año Nuevo', 2028),
(59, '2028-01-10', 'Reyes Magos', 2028),
(60, '2028-03-20', 'Día de San José', 2028),
(61, '2028-04-13', 'Jueves Santo', 2028),
(62, '2028-04-14', 'Viernes Santo', 2028),
(63, '2028-05-01', 'Día del Trabajo', 2028),
(64, '2028-05-29', 'Ascensión de Jesús', 2028),
(65, '2028-06-19', 'Corpus Christi', 2028),
(66, '2028-06-26', 'Sagrado Corazón', 2028),
(67, '2028-07-03', 'San Pedro y San Pablo', 2028),
(68, '2028-07-10', 'Día de Nuestra Señora de Chiquinquirá', 2028),
(69, '2028-07-20', 'Día de la Independencia', 2028),
(70, '2028-08-07', 'Batalla de Boyacá', 2028),
(71, '2028-08-21', 'Asunción de la Virgen', 2028),
(72, '2028-10-16', 'Día de la Raza', 2028),
(73, '2028-11-06', 'Todos los Santos', 2028),
(74, '2028-11-13', 'Independencia de Cartagena', 2028),
(75, '2028-12-08', 'Inmaculada Concepción', 2028),
(76, '2028-12-25', 'Navidad', 2028),
(77, '2029-01-01', 'Año Nuevo', 2029),
(78, '2029-01-08', 'Reyes Magos', 2029),
(79, '2029-03-19', 'Día de San José', 2029),
(80, '2029-03-29', 'Jueves Santo', 2029),
(81, '2029-03-30', 'Viernes Santo', 2029),
(82, '2029-05-01', 'Día del Trabajo', 2029),
(83, '2029-05-14', 'Ascensión de Jesús', 2029),
(84, '2029-06-04', 'Corpus Christi', 2029),
(85, '2029-06-11', 'Sagrado Corazón', 2029),
(86, '2029-07-02', 'San Pedro y San Pablo', 2029),
(87, '2029-07-09', 'Día de Nuestra Señora de Chiquinquirá', 2029),
(88, '2029-07-20', 'Día de la Independencia', 2029),
(89, '2029-08-07', 'Batalla de Boyacá', 2029),
(90, '2029-08-20', 'Asunción de la Virgen', 2029),
(91, '2029-10-15', 'Día de la Raza', 2029),
(92, '2029-11-05', 'Todos los Santos', 2029),
(93, '2029-11-12', 'Independencia de Cartagena', 2029),
(94, '2029-12-08', 'Inmaculada Concepción', 2029),
(95, '2029-12-25', 'Navidad', 2029),
(96, '2030-01-01', 'Año Nuevo', 2030),
(97, '2030-01-07', 'Reyes Magos', 2030),
(98, '2030-03-25', 'Día de San José', 2030),
(99, '2030-04-18', 'Jueves Santo', 2030),
(100, '2030-04-19', 'Viernes Santo', 2030),
(101, '2030-05-01', 'Día del Trabajo', 2030),
(102, '2030-06-03', 'Ascensión de Jesús', 2030),
(103, '2030-06-24', 'Corpus Christi', 2030),
(104, '2030-07-01', 'Sagrado Corazón', 2030),
(106, '2030-07-15', 'Día de Nuestra Señora de Chiquinquirá', 2030),
(107, '2030-07-20', 'Día de la Independencia', 2030),
(108, '2030-08-07', 'Batalla de Boyacá', 2030),
(109, '2030-08-19', 'Asunción de la Virgen', 2030),
(110, '2030-10-14', 'Día de la Raza', 2030),
(111, '2030-11-04', 'Todos los Santos', 2030),
(112, '2030-11-11', 'Independencia de Cartagena', 2030),
(113, '2030-12-08', 'Inmaculada Concepción', 2030),
(114, '2030-12-25', 'Navidad', 2030);

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
(1, 36, 'respuesta en incendios, rescates y atención de emergencias, y todas aquellas funciones de acuerdo a su cargo', 0, 1, '2026-10-07 21:12:38', '2026-10-07 21:13:07');

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `consecutivo` varchar(20) NOT NULL,
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_consecutivos`
--

CREATE TABLE `permisos_consecutivos` (
  `anio` int(11) NOT NULL,
  `ultimo_numero` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `renovaciones`
--

CREATE TABLE `renovaciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `cedula_empleado` varchar(10) NOT NULL,
  `numero` tinyint(3) UNSIGNED NOT NULL COMMENT '1 = RNV1, 2 = RNV2, ... (sin tope fijo)',
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `duracion_meses` smallint(5) UNSIGNED NOT NULL COMMENT 'Duración REAL registrada; coherente con las fechas',
  `es_historica` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = renovación anterior al sistema, registrada después',
  `duracion_menor_confirmada` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = era menor que la anterior y el usuario confirmó la advertencia',
  `observaciones` text DEFAULT NULL,
  `creado_por_id` int(11) DEFAULT NULL,
  `creado_por_nombre` varchar(50) NOT NULL,
  `creado_por_rol` varchar(40) NOT NULL,
  `created_at` datetime NOT NULL,
  `modificado_por_id` int(11) DEFAULT NULL,
  `modificado_por_nombre` varchar(50) DEFAULT NULL,
  `modificado_por_rol` varchar(40) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `renovaciones_auditoria`
--

CREATE TABLE `renovaciones_auditoria` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `usuario_nombre` varchar(50) NOT NULL,
  `usuario_rol` varchar(40) NOT NULL,
  `accion` varchar(50) NOT NULL,
  `cedula_empleado` varchar(10) DEFAULT NULL,
  `renovacion_id` int(10) UNSIGNED DEFAULT NULL,
  `detalle` longtext DEFAULT NULL COMMENT 'JSON: antes/después, resumen, etc.',
  `ip` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `renovaciones_avisos`
--

CREATE TABLE `renovaciones_avisos` (
  `id` int(10) UNSIGNED NOT NULL,
  `cedula` varchar(10) NOT NULL,
  `vigencia_fin` date NOT NULL COMMENT 'Fecha de vencimiento a la que corresponde el aviso',
  `tipo` enum('por_vencer','vencido') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `renovaciones_avisos`
--

INSERT INTO `renovaciones_avisos` (`id`, `cedula`, `vigencia_fin`, `tipo`, `created_at`) VALUES
(1, '1006556137', '2026-09-11', 'vencido', '2026-10-08 22:16:14'),
(2, '1005719736', '2026-10-06', 'vencido', '2026-10-08 22:16:14'),
(3, '1118534974', '2026-10-14', 'por_vencer', '2026-10-08 22:16:14');

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
(29, '1118544837', 15, 'eliminar', 'RNV1 eliminada: 01/12/2025 a 30/11/2026', 3, 'Talento', '2026-10-08 22:32:55');

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
  `bloqueado_hasta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password_hash`, `rol`, `created_at`, `intentos_fallidos`, `bloqueado_hasta`) VALUES
(2, 'Tatiana', '$2y$10$cQdMMnhoNeo3U58ygsXVCuLtBi2RKOF8D0kZWidtQHoeygqR6URNK', 'auxiliar_talento_humano', '2026-09-17 21:38:01', 0, NULL),
(3, 'Talento', '$2y$10$jTKCp2WcjVoE1cyGKmqqxekoeukZ6bKaJLyOdOzv67Tcw0a4C9Wmm', 'superadmin_talento_humano', '2026-09-29 21:24:40', 0, NULL),
(4, 'Omar', '$2y$10$Ub.R51IzfyUY0Rs4ROKP0OQsn9VjxxsmJfRZsaBo8oMouHdi49pZ2', 'teniente', '2026-09-29 22:43:28', 0, NULL);

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
-- Indices de la tabla `festivos_colombia`
--
ALTER TABLE `festivos_colombia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fecha` (`fecha`),
  ADD KEY `idx_anio` (`anio`);

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
-- Indices de la tabla `permisos_historial`
--
ALTER TABLE `permisos_historial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_permiso` (`permiso_id`);

--
-- Indices de la tabla `renovaciones`
--
ALTER TABLE `renovaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_renovacion_empleado_numero` (`cedula_empleado`,`numero`);

--
-- Indices de la tabla `renovaciones_auditoria`
--
ALTER TABLE `renovaciones_auditoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_aud_cedula_fecha` (`cedula_empleado`,`created_at`),
  ADD KEY `idx_aud_usuario` (`usuario_id`),
  ADD KEY `idx_aud_accion` (`accion`),
  ADD KEY `idx_aud_fecha` (`created_at`);

--
-- Indices de la tabla `renovaciones_avisos`
--
ALTER TABLE `renovaciones_avisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_renaviso` (`cedula`,`vigencia_fin`,`tipo`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cargos`
--
ALTER TABLE `cargos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1839;

--
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `festivos_colombia`
--
ALTER TABLE `festivos_colombia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT de la tabla `firmas_guardadas`
--
ALTER TABLE `firmas_guardadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `funciones_certificados`
--
ALTER TABLE `funciones_certificados`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `funciones_contratos`
--
ALTER TABLE `funciones_contratos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos_devoluciones`
--
ALTER TABLE `permisos_devoluciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos_dias`
--
ALTER TABLE `permisos_dias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos_historial`
--
ALTER TABLE `permisos_historial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `renovaciones`
--
ALTER TABLE `renovaciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `renovaciones_auditoria`
--
ALTER TABLE `renovaciones_auditoria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `renovaciones_avisos`
--
ALTER TABLE `renovaciones_avisos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT de la tabla `renovaciones_contrato`
--
ALTER TABLE `renovaciones_contrato`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `renovaciones_historial`
--
ALTER TABLE `renovaciones_historial`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios_empleados`
--
ALTER TABLE `usuarios_empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- Filtros para la tabla `renovaciones`
--
ALTER TABLE `renovaciones`
  ADD CONSTRAINT `fk_renovaciones_empleado` FOREIGN KEY (`cedula_empleado`) REFERENCES `empleados` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `renovaciones_avisos`
--
ALTER TABLE `renovaciones_avisos`
  ADD CONSTRAINT `fk_renaviso_empleado` FOREIGN KEY (`cedula`) REFERENCES `empleados` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE;

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
