ALTER TABLE permisos
  MODIFY estado ENUM('en_proceso','por_firmar_reemplazo','por_firmar_jefe','por_firmar_jefe_final','firmado','devuelto','devuelto_regreso','rechazado','aprobado_pendiente_regreso','anulado') NOT NULL DEFAULT 'en_proceso',
  ADD COLUMN foto_jefe_prefirmado VARCHAR(255) NULL AFTER firma_jefe,
  ADD COLUMN firma_jefe_prefirmado VARCHAR(255) NULL AFTER foto_jefe_prefirmado,
  ADD COLUMN motivo_anulacion TEXT NULL AFTER motivo_rechazo,
  ADD COLUMN anulado_por VARCHAR(50) NULL AFTER motivo_anulacion,
  ADD COLUMN fecha_anulacion TIMESTAMP NULL AFTER anulado_por;
