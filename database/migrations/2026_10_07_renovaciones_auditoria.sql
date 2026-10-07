-- database/migrations/2026_10_07_renovaciones_auditoria.sql
-- Módulo "Control de Renovaciones": base mínima + trazabilidad por usuario.
-- Probado en sintaxis para MySQL 8.4 / MariaDB 10.4+. Idempotente (IF NOT EXISTS).
--
-- Cómo aplicarla:  phpMyAdmin → base `chvb` → pestaña SQL → pegar y ejecutar.
-- Requiere que ya exista la tabla `empleados` (PK `cedula`, utf8mb4_unicode_ci).
-- NO modifica ni borra ninguna tabla existente.

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------------
-- 1) Renovaciones (RNV1, RNV2, ...). Cada fila guarda la REALIDAD contractual
--    tal como Talento Humano la registró: ninguna regla la reescribe.
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `renovaciones` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `cedula_empleado` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` tinyint UNSIGNED NOT NULL COMMENT '1 = RNV1, 2 = RNV2, ... (sin tope fijo)',
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `duracion_meses` smallint UNSIGNED NOT NULL COMMENT 'Duración REAL registrada; coherente con las fechas',
  `es_historica` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = renovación anterior al sistema, registrada después',
  `duracion_menor_confirmada` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = era menor que la anterior y el usuario confirmó la advertencia',
  `observaciones` text COLLATE utf8mb4_unicode_ci,

  -- Quién la registró (snapshot: sobrevive a borrado de usuario, cambio de rol o reciclaje de id)
  `creado_por_id` int DEFAULT NULL,
  `creado_por_nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `creado_por_rol` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,

  -- Quién la modificó por última vez (NULL si nunca se modificó)
  `modificado_por_id` int DEFAULT NULL,
  `modificado_por_nombre` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modificado_por_rol` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_renovacion_empleado_numero` (`cedula_empleado`, `numero`),
  CONSTRAINT `fk_renovaciones_empleado` FOREIGN KEY (`cedula_empleado`)
    REFERENCES `empleados` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_renovaciones_fechas` CHECK (`fecha_fin` >= `fecha_inicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- 2) Bitácora de auditoría (solo se INSERTA; el código no expone UPDATE/DELETE).
--    Sin FK a `usuarios` ni a `empleados` a propósito: la evidencia no debe
--    desaparecer si se elimina al usuario o al empleado.
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `renovaciones_auditoria` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `usuario_nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuario_rol` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `accion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cedula_empleado` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `renovacion_id` int UNSIGNED DEFAULT NULL,
  `detalle` longtext COLLATE utf8mb4_unicode_ci COMMENT 'JSON: antes/después, resumen, etc.',
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_aud_cedula_fecha` (`cedula_empleado`, `created_at`),
  KEY `idx_aud_usuario` (`usuario_id`),
  KEY `idx_aud_accion` (`accion`),
  KEY `idx_aud_fecha` (`created_at`),
  CONSTRAINT `chk_aud_detalle_json` CHECK (`detalle` IS NULL OR JSON_VALID(`detalle`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
