-- database/migraciones/2026_10_15_usuarios_panel.sql
-- Fase 1 · Panel de usuarios del sistema (Configuración -> Usuarios).
--
-- Cómo aplicarla (HAZ BACKUP ANTES):
--   C:\xampp\mysql\bin\mysql.exe -u root -p chvb < database\migraciones\2026_10_15_usuarios_panel.sql
--
-- Es idempotente: se puede ejecutar más de una vez sin romper nada (MariaDB 10.0.2+ / XAMPP).
-- Los usuarios que ya existen quedan ACTIVOS y sin obligación de cambiar contraseña.

ALTER TABLE `usuarios`
  ADD COLUMN IF NOT EXISTS `nombre_completo` varchar(100) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `activo` tinyint(1) NOT NULL DEFAULT 1,
  ADD COLUMN IF NOT EXISTS `debe_cambiar_password` tinyint(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `password_cambiada_at` datetime DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `ultimo_acceso` datetime DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `creado_por` int(11) DEFAULT NULL;

-- Auditoría: quién hizo qué sobre las cuentas. Sin llaves foráneas A PROPÓSITO:
-- el registro debe sobrevivir aunque algún día se borre una cuenta (por eso guarda el username).
CREATE TABLE IF NOT EXISTS `usuarios_auditoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `actor_id` int(11) DEFAULT NULL,
  `actor_username` varchar(50) NOT NULL,
  `objetivo_id` int(11) DEFAULT NULL,
  `objetivo_username` varchar(50) DEFAULT NULL,
  `accion` varchar(40) NOT NULL,
  `detalle` varchar(255) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_objetivo` (`objetivo_id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
