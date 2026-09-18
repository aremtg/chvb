-- Ejecutar SOLO sobre una base de datos EXISTENTE que todavía tenga pendiente_revision.
-- Si importaste database/schema.sql desde cero, NO ejecutes este archivo.

ALTER TABLE documentos DROP COLUMN pendiente_revision;
ALTER TABLE documentos ADD COLUMN subido_por_cedula varchar(10) DEFAULT NULL AFTER fecha_subida;
ALTER TABLE documentos ADD COLUMN subido_por_tipo enum('empleado','panel') NOT NULL DEFAULT 'panel' AFTER subido_por_cedula;
