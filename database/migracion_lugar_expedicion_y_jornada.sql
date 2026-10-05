-- =====================================================================
-- Migración: lugar de expedición de la cédula + tipo de jornada
-- Base de datos: chvb  (MySQL 8.x)
--
-- ES SEGURA PARA TUS ~54 EMPLEADOS ACTUALES:
--   * Solo AGREGA columnas (ninguna existente se borra ni se modifica).
--   * lugar_expedicion queda vacío (NULL) hasta que lo llenes en "Editar".
--   * tipo_jornada se llena automáticamente para que NADA cambie hoy:
--       Bombero -> 'Turnos'   (igual que funciona ahora)
--       Civil / sin tipo -> 'Administrativa' (igual que funciona ahora)
--
-- EJECUTAR UNA SOLA VEZ (en phpMyAdmin, pestaña SQL, con la BD chvb elegida).
-- Haz antes una copia: Exportar > chvb > Continuar.
-- =====================================================================

ALTER TABLE `empleados`
  ADD COLUMN `lugar_expedicion` VARCHAR(120) NULL DEFAULT NULL
      COMMENT 'Municipio, Departamento (lista en public/assets/data/municipios.json)'
      AFTER `cedula`,
  ADD COLUMN `tipo_jornada` ENUM('Turnos','Administrativa','Restringida') NOT NULL DEFAULT 'Administrativa'
      COMMENT 'Cómo se cuentan horas/días en permisos: Turnos (operativo), Administrativa (07:00-17:24) o Restringida (horario reducido)'
      AFTER `es_bombero_integral`,
  ADD COLUMN `jornada_hora_entrada` TIME NULL DEFAULT NULL
      COMMENT 'Solo si tipo_jornada = Restringida'
      AFTER `tipo_jornada`,
  ADD COLUMN `jornada_hora_salida` TIME NULL DEFAULT NULL
      COMMENT 'Solo si tipo_jornada = Restringida'
      AFTER `jornada_hora_entrada`;

-- Mantiene el comportamiento actual: hoy todo Bombero se cuenta por turnos.
UPDATE `empleados` SET `tipo_jornada` = 'Turnos' WHERE `tipo_de_personal` = 'Bombero';


-- ---------------------------------------------------------------------
-- DESPUÉS (opcional, para revisar): bomberos integrales que probablemente
-- NO son de turnos (maquinistas que salen a las 5:00 pm, auxiliares de
-- talento humano, etc.). Para cada uno entra a Editar y cambia
-- "Jornada" a "Administrativa" o "Horario reducido".
-- ---------------------------------------------------------------------
-- SELECT cedula, nombre, cargo, tipo_jornada
--   FROM empleados
--  WHERE tipo_de_personal = 'Bombero' AND es_bombero_integral = 1
--  ORDER BY nombre;

-- ---------------------------------------------------------------------
-- ROLLBACK (solo si quisieras deshacer todo):
-- ALTER TABLE empleados DROP COLUMN jornada_hora_salida,
--   DROP COLUMN jornada_hora_entrada, DROP COLUMN tipo_jornada,
--   DROP COLUMN lugar_expedicion;
-- ---------------------------------------------------------------------
