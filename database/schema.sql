CREATE DATABASE IF NOT EXISTS chvb CHARACTER
SET
  utf8mb4 COLLATE utf8mb4_unicode_ci;

USE chvb;

CREATE TABLE
  empleados (
    cedula VARCHAR(10) PRIMARY KEY COMMENT 'Max 10 caracteres, permite extranjera alfanumérica',
    nombre VARCHAR(150) NOT NULL,
    cargo ENUM (
      'Auxiliar en Talento Humano',
      'Director de talento humano',
      'Auxiliar de Extintores',
      'Enfermero/a',
      'Teniente',
      'Practicante Sena',
      'Practicante Fundetec',
      'Practicante otra entidad',
      'Servicios Generales',
      'Maquinista',
      'Guardia',
      'Recepcionista',
      'Auxiliar administrativo',
      'Director Académico',
      'Director de negocios',
      'Tecnico en soporte sistemas',
      'Tecnico archivista',
      'Coordinador SST',
      'Auxiliar SST',
      'Jefe de prensa',
      'Contador',
      'Auxiliar de contaduría',
      'Almacenista',
      'Supervisor',
      'Coordinador de banda',
      'Conductor de ambulancia',
      'Aspirante',
      'Voluntario'
    ) NOT NULL,
    es_bombero_integral TINYINT (1) DEFAULT 0,
    tipo_de_contrato ENUM ('fijo', 'indefinido', 'ops') NOT NULL,
    estado ENUM ('activo', 'no activo') DEFAULT 'activo',
    celular VARCHAR(15) NULL COMMENT 'Numero de celular, validar 10 digitos',
    correo VARCHAR(150) NULL,
    fecha_nacimiento DATE NULL COMMENT 'Para notificacion de cumpleaños',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE = InnoDB;

CREATE TABLE
  bolsillos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula_empleado VARCHAR(10) NOT NULL,
    seccion ENUM ('hoja_de_vida', 'documentos_contractuales') NOT NULL,
    nombre VARCHAR(100) NOT NULL COMMENT 'slug interno, ej: certificados',
    nombre_completo VARCHAR(150) NOT NULL COMMENT 'nombre visible, ej: Certificados',
    orden INT NOT NULL DEFAULT 0,
    alarma_tipo ENUM ('2m', '3m', '6m', '1a', 'custom') NULL,
    alarma_fecha DATE NULL,
    alarma_activa TINYINT (1) DEFAULT 0,
    FOREIGN KEY (cedula_empleado) REFERENCES empleados (cedula) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_cedula_seccion (cedula_empleado, seccion)
  ) ENGINE = InnoDB;

ALTER TABLE bolsillos MODIFY COLUMN alarma_tipo ENUM ('1m', '2m', '6m', '1a', 'custom') NULL,
ADD COLUMN alarma_valor INT NULL COMMENT 'Cantidad numérica para alarma personalizada',
ADD COLUMN alarma_unidad ENUM ('dias', 'meses', 'anios') NULL COMMENT 'Unidad para alarma personalizada',
ADD COLUMN alarma_fecha_inicio DATE NULL COMMENT 'Fecha desde la cual se cuenta el plazo',
ADD COLUMN alarma_dias_aviso INT NULL DEFAULT 35 COMMENT 'Días de anticipación para la alerta amarilla';

CREATE TABLE
  documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bolsillo_id INT NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    ruta VARCHAR(500) NOT NULL COMMENT 'ruta relativa dentro de uploads/',
    orden INT NOT NULL DEFAULT 1,
    pendiente_revision TINYINT (1) DEFAULT 0,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bolsillo_id) REFERENCES bolsillos (id) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE = InnoDB;

CREATE TABLE
  usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM ('superadmin_talento_humano') NOT NULL DEFAULT 'superadmin_talento_humano',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE = InnoDB;

CREATE TABLE
  usuarios_empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(10) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL COMMENT 'hash del PIN de 4 digitos',
    activo TINYINT (1) DEFAULT 1,
    intentos_fallidos INT DEFAULT 0,
    bloqueado_hasta DATETIME NULL,
    FOREIGN KEY (cedula) REFERENCES empleados (cedula) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE = InnoDB;


  ALTER TABLE usuarios 
  MODIFY COLUMN rol ENUM('superadmin_talento_humano','auxiliar_talento_humano') NOT NULL;

CREATE TABLE notificaciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL COMMENT 'quien hizo el cambio (el auxiliar)',
  usuario_nombre VARCHAR(50) NOT NULL,
  cedula_empleado VARCHAR(10) NOT NULL,
  campo VARCHAR(50) NOT NULL,
  mensaje VARCHAR(500) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

ALTER TABLE usuarios_empleados 
  ADD COLUMN pin_encriptado VARCHAR(255) NULL COMMENT 'PIN cifrado reversible, solo visible para superadmin/auxiliar';