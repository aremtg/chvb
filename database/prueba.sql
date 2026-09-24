-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-09-2026 a las 02:02:51
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `cedula` varchar(10) NOT NULL COMMENT 'Max 10 caracteres, permite extranjera alfanumérica',
  `nombre` varchar(150) NOT NULL,
  `sexo` enum('F','M') DEFAULT NULL,
  `cargo` enum('Auxiliar en Talento Humano','Director de talento humano','Auxiliar de Extintores','Enfermero/a','Practicante Sena','Practicante Fundetec','Practicante otra entidad','Servicios Generales','Maquinista','Guardia','Recepcionista','Administrativo','Auxiliar administrativo','Director Académico','Director de negocios','Tecnico en soporte sistemas','Tecnico archivista','Coordinador SST','Auxiliar SST','Jefe de prensa','Contador','Auxiliar de contaduría','Almacenista','Supervisor','Coordinador de banda','Conductor de ambulancia','Aspirante','Voluntario','Secretario recaudador','Auxiliar de enfermería','Docente de banda marcial','PAMEC','Revisor(a) fiscal','Comandante de estación','Directora administrativa y financiera','Bombero integral') NOT NULL,
  `tipo_de_personal` enum('Bombero','Civil') DEFAULT NULL,
  `eps` enum('Sanitas','Nueva EPS','Capresoca','Salud Total') DEFAULT NULL,
  `pension` enum('Colfondos','Porvenir','Colpensiones','Protección','NA') DEFAULT NULL,
  `arl` enum('Positiva','SURA','Colmena','AXA Colpatria','Seguros Bolívar') DEFAULT NULL,
  `salario_basico` decimal(12,2) DEFAULT NULL,
  `es_bombero_integral` tinyint(1) DEFAULT 0,
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

INSERT INTO `empleados` (`cedula`, `nombre`, `sexo`, `cargo`, `tipo_de_personal`, `eps`, `pension`, `arl`, `salario_basico`, `es_bombero_integral`, `tipo_de_contrato`, `fecha_inicio_contrato`, `fecha_fin_contrato`, `estado`, `celular`, `correo`, `fecha_nacimiento`, `created_at`, `foto`) VALUES
('1006555838', 'Lourdes Ester Guarin Garcia', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 03:32:54', NULL),
('1006556137', 'Javier David Moreno', 'M', 'Auxiliar de Extintores', 'Civil', 'Nueva EPS', NULL, NULL, NULL, 0, 'Fijo', '2026-03-12', '2026-09-11', 'activo', '3224045766', NULL, NULL, '2026-09-23 22:46:04', NULL),
('1006556671', 'Jhon Marco Rincon Castaño', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 03:41:28', NULL),
('1006636306', 'Karen Lizeth Diaz Pineda', 'F', 'Auxiliar de Extintores', 'Bombero', 'Sanitas', 'Porvenir', NULL, NULL, 0, 'Fijo', '2026-04-09', '2026-10-08', 'activo', NULL, NULL, NULL, '2026-09-23 23:07:53', NULL),
('1019024577', 'Nohora Rocio Duran Torres', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 18:07:51', NULL),
('1029643799', 'Samuel Santiago Fonseca Patarroyo', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 18:09:04', NULL),
('1029661794', 'Darwin Camilo Bedoya Gutierrez', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, '2007-09-22', '2026-09-23 18:08:27', NULL),
('1115911058', 'Edwar Santiago Alfonso Ducon', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 18:06:47', NULL),
('1115913555', 'Wilder Andrey Chaparro Chaparro', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 03:43:10', NULL),
('1116552720', 'Juan Fernando Dominguez Ibarguen', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 03:38:48', NULL),
('1118529611', 'Jimmy Alejandro Garcia Chinchilla', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 02:08:05', NULL),
('1118534974', 'Soraida Sepulveda Gordillo', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 03:25:05', NULL),
('1118543385', 'Lewis Arfrey Ardila Achagua', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 03:30:44', NULL),
('1118544837', 'José Ferney Rodriguez Barrera', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 02:05:57', NULL),
('1118547243', 'Tito Enrique Camargo Pezca', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 01:11:57', NULL),
('1118550799', 'Deyna Yurany Torres Cuervo', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 03:27:52', NULL),
('1118555586', 'Angel Gabriel Camargo Pezca', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 02:06:39', NULL),
('111856453', 'Astrid Mariana Aquite Gómez', 'F', 'Auxiliar en Talento Humano', 'Civil', 'Nueva EPS', 'Colfondos', 'Positiva', 1964430.00, 1, 'Fijo', '2024-02-15', '2024-08-15', 'activo', '3209308877', NULL, '2001-04-13', '2026-09-23 00:46:12', NULL),
('1118564997', 'Kewin Alexis Adan Jeronimo', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'no activo', NULL, NULL, NULL, '2026-09-23 03:18:30', NULL),
('1118565906', 'Jeidi Carolina Acevedo Lopez', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 03:44:42', NULL),
('1118567328', 'Nelson Fabian Chaparro Rincon', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 18:11:40', NULL),
('1118573216', 'Camilo Andres Corredor Garcia', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 03:22:10', NULL),
('1118575006', 'Angela Brithey Maldonado', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 03:37:51', NULL),
('1118775342', 'Daniel Fernando Gutierrez Riaño', 'M', 'Bombero integral', 'Bombero', 'Sanitas', 'Colpensiones', 'Positiva', 0.00, 0, 'Fijo', '2024-01-17', '2025-01-17', 'activo', NULL, NULL, '1993-03-12', '2026-09-23 01:04:54', NULL),
('11206377', 'Juan Fernando Guzman Guzman', 'M', 'Bombero integral', 'Bombero', 'Sanitas', 'Colpensiones', 'Positiva', NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 01:34:23', NULL),
('1121898640', 'Arlyn Johanna Sanchez Gutierrez', 'F', 'Auxiliar administrativo', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 22:47:28', NULL),
('1124989349', 'Tatiana Andrea Guzman Galindo', 'F', 'Practicante Fundetec', 'Civil', 'Capresoca', 'NA', 'Positiva', NULL, 0, 'No aplica', '2026-05-04', NULL, 'activo', '3229496595', 'tgz57031@gmail.com', '2003-04-13', '2026-09-22 01:48:16', 'hv_1124989349/perfil/foto.jpg'),
('16672796', 'Juan Carlos Santacoloma Piedrahita', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 03:35:08', NULL),
('4284762', 'Jose Manuel Gutierrez Teatin', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 18:03:27', NULL),
('47428604', 'Graciela Garcia Chinchilla', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 17:57:55', NULL),
('47430097', 'Sthella Gutierrez', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 18:01:10', NULL),
('47441163', 'Sandra Milena Castaño Vargas', 'F', 'Administrativo', 'Civil', 'Sanitas', 'Porvenir', 'Positiva', 2071830.00, 0, 'Fijo', '2024-02-13', '2024-08-12', 'activo', NULL, NULL, '1983-06-13', '2026-09-23 00:56:25', NULL),
('52308103', 'Fanny Paola Mercado Delgado', 'F', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Fijo', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 18:10:43', NULL),
('7180789', 'Hector Favian Auzaque Parra', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 03:45:50', NULL),
('74770870', 'Ariosto Castelblanco Zorro', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 17:56:03', NULL),
('74814305', 'Nelson Morales Cubides', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 18:04:03', NULL),
('74859815', 'Waldo Ramirez Avila', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 18:04:43', NULL),
('74861711', 'Wilmar Vargas Teatin', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 18:05:33', NULL),
('9656509', 'Jose Orlando Gonzalez Gonzales', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 17:59:42', NULL),
('9658799', 'Javier Fernando Fuquen Calderon', 'M', 'Bombero integral', 'Bombero', NULL, NULL, NULL, NULL, 0, 'Indefinido', NULL, NULL, 'activo', NULL, NULL, NULL, '2026-09-23 17:57:02', NULL);

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
(19, '2026-12-25', 'Navidad', 2026);

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
-- Estructura de tabla para la tabla `presencia_empleados`
--

CREATE TABLE `presencia_empleados` (
  `cedula` varchar(10) NOT NULL,
  `ultima_actividad` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'Talento', '$2y$10$NGyVALKNFTFSp2RCTHsb8OkWNq5687eAzdD1fTtTEuSngyyk9lzQC', 'superadmin_talento_humano', '2026-09-17 19:26:28', 0, NULL),
(2, 'Tatiana', '$2y$10$cQdMMnhoNeo3U58ygsXVCuLtBi2RKOF8D0kZWidtQHoeygqR6URNK', 'auxiliar_talento_humano', '2026-09-17 21:38:01', 0, NULL);

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
-- Indices de la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bolsillo_id` (`bolsillo_id`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`cedula`);

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
-- Indices de la tabla `presencia_empleados`
--
ALTER TABLE `presencia_empleados`
  ADD PRIMARY KEY (`cedula`);

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
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `festivos_colombia`
--
ALTER TABLE `festivos_colombia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `firmas_guardadas`
--
ALTER TABLE `firmas_guardadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- Filtros para la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`bolsillo_id`) REFERENCES `bolsillos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `firmas_guardadas`
--
ALTER TABLE `firmas_guardadas`
  ADD CONSTRAINT `firmas_guardadas_ibfk_1` FOREIGN KEY (`cedula`) REFERENCES `empleados` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD CONSTRAINT `permisos_ibfk_1` FOREIGN KEY (`cedula_empleado`) REFERENCES `empleados` (`cedula`) ON UPDATE CASCADE,
  ADD CONSTRAINT `permisos_ibfk_2` FOREIGN KEY (`cedula_reemplazo`) REFERENCES `empleados` (`cedula`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `permisos_ibfk_3` FOREIGN KEY (`cedula_jefe`) REFERENCES `empleados` (`cedula`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `permisos_devoluciones`
--
ALTER TABLE `permisos_devoluciones`
  ADD CONSTRAINT `permisos_devoluciones_ibfk_1` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `permisos_dias`
--
ALTER TABLE `permisos_dias`
  ADD CONSTRAINT `permisos_dias_ibfk_1` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `permisos_historial`
--
ALTER TABLE `permisos_historial`
  ADD CONSTRAINT `permisos_historial_ibfk_1` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `presencia_empleados`
--
ALTER TABLE `presencia_empleados`
  ADD CONSTRAINT `presencia_empleados_ibfk_1` FOREIGN KEY (`cedula`) REFERENCES `empleados` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios_empleados`
--
ALTER TABLE `usuarios_empleados`
  ADD CONSTRAINT `usuarios_empleados_ibfk_1` FOREIGN KEY (`cedula`) REFERENCES `empleados` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
