-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20260910.b4a124aefc
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 14, 2026 at 03:53 PM
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
  `usuario_id` int NOT NULL COMMENT 'quien hizo el cambio (el auxiliar)',
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
  `fecha_fin` date NOT NULL,
  `hora_fin` time NOT NULL,
  `total_horas` decimal(6,2) NOT NULL,
  `incluye_festivo` tinyint(1) DEFAULT '0',
  `festivo_confirmado` tinyint(1) DEFAULT '0',
  `remunerado` tinyint(1) NOT NULL DEFAULT '0',
  `es_compensatorio` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_horas_extra` date DEFAULT NULL,
  `es_devolucion` tinyint(1) NOT NULL DEFAULT '0',
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
  `estado` enum('en_proceso','por_firmar_reemplazo','por_firmar_jefe','firmado','devuelto','rechazado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_proceso',
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
-- Table structure for table `usuarios`
--
CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('superadmin_talento_humano','auxiliar_talento_humano','teniente') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
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
-- Indexes for table `permisos_historial`
--
ALTER TABLE `permisos_historial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_permiso` (`permiso_id`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

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
-- Constraints for table `permisos_historial`
--
ALTER TABLE `permisos_historial`
  ADD CONSTRAINT `permisos_historial_ibfk_1` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `usuarios_empleados`
--
ALTER TABLE `usuarios_empleados`
  ADD CONSTRAINT `usuarios_empleados_ibfk_1` FOREIGN KEY (`cedula`) REFERENCES `empleados` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
