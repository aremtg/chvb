-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20260915.9e4dc5b5f4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 17, 2026 at 05:02 PM
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
-- Table structure for table `documentos`
--
CREATE TABLE `documentos` (
  `id` int NOT NULL,
  `bolsillo_id` int NOT NULL,
  `nombre_archivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruta` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ruta relativa dentro de uploads/',
  `orden` int NOT NULL DEFAULT '1',
  `pendiente_revision` tinyint(1) DEFAULT '0',
  `fecha_subida` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
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
  `grupo` enum('Operativo','Administrativo','Administrativo (Negocios y Académica)') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eps` enum('Sanitas','Nueva EPS','Capresoca','Salud Total') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pension` enum('Colfondos','Porvenir','Colpensiones','Protección','NA') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salario_basico` decimal(12,2) DEFAULT NULL,
  `es_bombero_integral` tinyint(1) DEFAULT '0',
  `tipo_de_contrato` enum('fijo','indefinido','ops','Contrato SENA','OPS SEMY') COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('activo','no activo') COLLATE utf8mb4_unicode_ci DEFAULT 'activo',
  `celular` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numero de celular, validar 10 digitos',
  `correo` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL COMMENT 'Para notificacion de cumpleaños',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ruta relativa dentro de uploads/'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `evidencia_archivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('en_proceso','por_firmar_reemplazo','por_firmar_jefe','firmado','devuelto','rechazado','aprobado_pendiente_regreso') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_proceso',
  `motivo_devolucion` text COLLATE utf8mb4_unicode_ci,
  `motivo_rechazo` text COLLATE utf8mb4_unicode_ci,
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

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
