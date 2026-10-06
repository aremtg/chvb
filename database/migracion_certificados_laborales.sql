-- =====================================================================
-- Migración: Certificado laboral (GH-FT-10) + funciones por cargo
-- MySQL 8.x / MariaDB 10.4+
--
-- * NO modifica ni borra nada existente (tabla `empleados` intacta).
-- * Solo CREA 6 tablas nuevas (IF NOT EXISTS: no falla si se repite).
-- * Antes: Exportar > chvb > Continuar (copia de seguridad).
-- * Ejecutar en phpMyAdmin > BD chvb > pestaña SQL.
-- * `cargos` se llena sola (se sincroniza desde el ENUM empleados.cargo
--   la primera vez que se abre el modal "Funciones").
-- =====================================================================

CREATE TABLE IF NOT EXISTS `cargos` (
  `id`     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `activo` TINYINT(1)   NOT NULL DEFAULT 1,
  UNIQUE KEY `uq_cargos_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Funciones cortas y ejecutivas (solo para certificados)
CREATE TABLE IF NOT EXISTS `funciones_certificados` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cargo_id`   INT UNSIGNED NOT NULL,
  `texto`      VARCHAR(160) NOT NULL,
  `orden`      TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `activo`     TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_fcert_cargo` FOREIGN KEY (`cargo_id`) REFERENCES `cargos`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `uq_fcert_cargo_texto` (`cargo_id`, `texto`),
  KEY `idx_fcert_cargo` (`cargo_id`, `activo`, `orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Funciones detalladas (para contratos)
CREATE TABLE IF NOT EXISTS `funciones_contratos` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cargo_id`   INT UNSIGNED NOT NULL,
  `texto`      TEXT NOT NULL,
  `orden`      TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `activo`     TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_fcont_cargo` FOREIGN KEY (`cargo_id`) REFERENCES `cargos`(`id`) ON DELETE CASCADE,
  KEY `idx_fcont_cargo` (`cargo_id`, `activo`, `orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Consecutivo global de certificados (0001, 0002, ...). Una sola fila.
CREATE TABLE IF NOT EXISTS `certificados_consecutivos` (
  `id`            TINYINT UNSIGNED NOT NULL PRIMARY KEY,
  `ultimo_numero` INT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT IGNORE INTO `certificados_consecutivos` (`id`, `ultimo_numero`) VALUES (1, 0);

-- Registro de certificados emitidos (sirve para "actual" y "retirado")
CREATE TABLE IF NOT EXISTS `certificados_laborales` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `numero`          INT UNSIGNED NOT NULL,
  `consecutivo`     VARCHAR(10)  NOT NULL,
  `tipo`            ENUM('actual','retirado') NOT NULL,
  `cedula`          VARCHAR(10)  NOT NULL,
  `nombre_snapshot` VARCHAR(150) NOT NULL,
  `cargo_snapshot`  VARCHAR(100) NOT NULL,
  `fecha_inicio`    DATE NOT NULL,
  `fecha_retiro`    DATE NULL,
  `archivo`         VARCHAR(255) NOT NULL,
  `creado_por`      INT NULL,
  `created_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_cert_numero` (`numero`),
  UNIQUE KEY `uq_cert_archivo` (`archivo`),
  KEY `idx_cert_cedula` (`cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1 o 2 funciones por certificado (el tope lo impone la BD)
CREATE TABLE IF NOT EXISTS `certificados_laborales_funciones` (
  `certificado_id` INT UNSIGNED NOT NULL,
  `posicion`       TINYINT UNSIGNED NOT NULL,
  `funcion_id`     INT UNSIGNED NULL,
  `texto_snapshot` VARCHAR(160) NOT NULL,
  PRIMARY KEY (`certificado_id`, `posicion`),
  CONSTRAINT `chk_cert_pos` CHECK (`posicion` IN (1,2)),
  CONSTRAINT `fk_clf_cert` FOREIGN KEY (`certificado_id`) REFERENCES `certificados_laborales`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_clf_func` FOREIGN KEY (`funcion_id`) REFERENCES `funciones_certificados`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
