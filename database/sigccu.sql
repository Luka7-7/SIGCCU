-- MySQL / MariaDB Script generated for SIGCCU
-- Compatible con MySQL Workbench, MySQL 5.7 / 8.x y MariaDB 10.x

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema sigccu
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `sigccu` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sigccu`;

-- -----------------------------------------------------
-- Table `Carrera`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Carrera` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre_carrera` VARCHAR(100) NOT NULL,
  `unidad_academica` VARCHAR(100) NOT NULL,
  `area` ENUM('artes', 'ciencias basicas e ingenierias', 'ciencias economico y administrativas', 'ciencias biologicas y agropecuarias', 'ciencias de la salud', 'ciencias sociales y humanidades') NOT NULL,
  `institucion` VARCHAR(90) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- -----------------------------------------------------
-- Table `usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `correo_electronico` VARCHAR(100) NOT NULL,
  `contrasena` VARCHAR(255) NOT NULL,
  `nombres` VARCHAR(70) NOT NULL,
  `apellidos` VARCHAR(80) NOT NULL,
  `rol` ENUM('administrador', 'alumno', 'docente', 'externo') NOT NULL,
  `matricula_numtrabajador` VARCHAR(8) NULL DEFAULT NULL,
  `Carrera_id` INT UNSIGNED NOT NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `correo_electronico_UNIQUE` (`correo_electronico` ASC),
  INDEX `fk_usuarios_Carrera1_idx` (`Carrera_id` ASC),
  CONSTRAINT `fk_usuarios_Carrera1`
    FOREIGN KEY (`Carrera_id`)
    REFERENCES `Carrera` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- -----------------------------------------------------
-- Table `congresos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `congresos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(120) NOT NULL,
  `siglas` VARCHAR(12) NOT NULL,
  `edicion` INT NOT NULL,
  `fecha_inicio` DATE NOT NULL,
  `fecha_fin` DATE NOT NULL,
  `registro_abre` DATETIME NOT NULL,
  `registro_cierre` DATETIME NOT NULL,
  `descripcion` TEXT NOT NULL,
  `logo_url` VARCHAR(255) NULL DEFAULT NULL,
  `minimo_talleres` INT NOT NULL DEFAULT 0,
  `minimo_conferencias` INT NOT NULL DEFAULT 0,
  `eje_tematico` VARCHAR(100) NOT NULL,
  `dictamen` ENUM('aceptada', 'con observaciones', 'rechazada') NULL DEFAULT NULL,
  `solicitante_id` INT UNSIGNED NULL DEFAULT NULL,
  `solicitud_pdf` VARCHAR(255) NULL DEFAULT NULL,
  `solicitada_en` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_congresos_solicitante_idx` (`solicitante_id` ASC),
  CONSTRAINT `fk_congresos_solicitante`
    FOREIGN KEY (`solicitante_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- -----------------------------------------------------
-- Table `sedes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sedes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `direccion` VARCHAR(150) NOT NULL,
  `ciudad` VARCHAR(60) NOT NULL,
  `estado` VARCHAR(60) NOT NULL,
  `url_ubicacion` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- -----------------------------------------------------
-- Table `congresos_sedes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `congresos_sedes` (
  `congreso_id` INT UNSIGNED NOT NULL,
  `sede_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`congreso_id`, `sede_id`),
  INDEX `fk_congresos_sedes_sedes` (`sede_id` ASC),
  CONSTRAINT `fk_congresos_sedes_congresos`
    FOREIGN KEY (`congreso_id`)
    REFERENCES `congresos` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_congresos_sedes_sedes`
    FOREIGN KEY (`sede_id`)
    REFERENCES `sedes` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- -----------------------------------------------------
-- Table `congresos_programas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `congresos_programas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `congreso_id` INT UNSIGNED NOT NULL,
  `programa` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `congresos_programas_unique` (`congreso_id` ASC, `programa` ASC),
  CONSTRAINT `fk_congresos_programas_congreso`
    FOREIGN KEY (`congreso_id`)
    REFERENCES `congresos` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- -----------------------------------------------------
-- Table `conferencias_talleres`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `conferencias_talleres` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `descripcion` TEXT NOT NULL,
  `tipo` ENUM('conferencia', 'taller') NOT NULL,
  `cupo` INT NOT NULL,
  `ubicacion` VARCHAR(100) NOT NULL,
  `fecha` DATE NOT NULL,
  `hora_inicio` TIME NOT NULL,
  `hora_fin` TIME NOT NULL,
  `congreso_id` INT UNSIGNED NOT NULL,
  `qr_link` VARCHAR(50) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_conferencias_talleres_congresos` (`congreso_id` ASC),
  CONSTRAINT `fk_conferencias_talleres_congresos`
    FOREIGN KEY (`congreso_id`)
    REFERENCES `congresos` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- -----------------------------------------------------
-- Table `inscripciones_congresos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `inscripciones_congresos` (
  `usuario_id` INT UNSIGNED NOT NULL,
  `congreso_id` INT UNSIGNED NOT NULL,
  `fecha_hora_inscripcion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `aceptado` TINYINT(1) NOT NULL DEFAULT 0,
  `fecha_hora_respuesta` DATETIME NULL DEFAULT NULL,
  `rol_congreso` ENUM('organizador', 'comite', 'asistente y/o ponente') NULL DEFAULT NULL,
  PRIMARY KEY (`usuario_id`, `congreso_id`),
  INDEX `fk_inscripciones_congresos_congresos` (`congreso_id` ASC),
  CONSTRAINT `fk_inscripciones_congresos_usuarios`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_inscripciones_congresos_congresos`
    FOREIGN KEY (`congreso_id`)
    REFERENCES `congresos` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- -----------------------------------------------------
-- Table `inscripciones_conferencias_talleres`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `inscripciones_conferencias_talleres` (
  `usuario_id` INT UNSIGNED NOT NULL,
  `conferencia_taller_id` INT UNSIGNED NOT NULL,
  `fecha_hora_inscripcion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `aceptado` TINYINT(1) NOT NULL DEFAULT 0,
  `fecha_hora_respuesta` DATETIME NULL DEFAULT NULL,
  `tipo_participacion` ENUM('asistente', 'ponente', 'tallerista') NULL DEFAULT NULL,
  PRIMARY KEY (`usuario_id`, `conferencia_taller_id`),
  INDEX `fk_inscripciones_ct_conferencias_talleres` (`conferencia_taller_id` ASC),
  CONSTRAINT `fk_inscripciones_ct_usuarios`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_inscripciones_ct_conferencias_talleres`
    FOREIGN KEY (`conferencia_taller_id`)
    REFERENCES `conferencias_talleres` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- -----------------------------------------------------
-- Table `asistencias`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `asistencias` (
  `usuario_id` INT UNSIGNED NOT NULL,
  `conferencia_taller_id` INT UNSIGNED NOT NULL,
  `fecha_hora` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` VARCHAR(45) NOT NULL,
  `Activo` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`usuario_id`, `conferencia_taller_id`),
  CONSTRAINT `fk_asistencias_inscripciones`
    FOREIGN KEY (`usuario_id`, `conferencia_taller_id`)
    REFERENCES `inscripciones_conferencias_talleres` (`usuario_id`, `conferencia_taller_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- -----------------------------------------------------
-- Table `constancias`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `constancias` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` INT UNSIGNED NOT NULL,
  `congreso_id` INT UNSIGNED NULL DEFAULT NULL,
  `conferencia_taller_id` INT UNSIGNED NULL DEFAULT NULL,
  `url_constancia` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_constancias_congresos` (`congreso_id` ASC),
  INDEX `fk_constancias_conferencias_talleres` (`conferencia_taller_id` ASC),
  INDEX `fk_constancias_usuarios` (`usuario_id` ASC),
  CONSTRAINT `fk_constancias_congresos`
    FOREIGN KEY (`congreso_id`)
    REFERENCES `congresos` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_constancias_conferencias_talleres`
    FOREIGN KEY (`conferencia_taller_id`)
    REFERENCES `conferencias_talleres` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_constancias_usuarios`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- -----------------------------------------------------
-- Table `evaluadores`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `evaluadores` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` INT UNSIGNED NOT NULL,
  `licenciatura` VARCHAR(100) NOT NULL,
  `maestria` VARCHAR(100) NULL DEFAULT NULL,
  `doctorado` VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `usuario_id_UNIQUE` (`usuario_id` ASC),
  CONSTRAINT `fk_evaluadores_usuarios`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- -----------------------------------------------------
-- Table `propuestas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `propuestas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `resumen_url` VARCHAR(255) NOT NULL,
  `extenso_url` VARCHAR(255) NULL DEFAULT NULL,
  `resumen_estado` ENUM('Enviado', 'En revision', 'A corregir', 'Aceptado', 'Rechazado') NOT NULL DEFAULT 'Enviado',
  `usuario_id` INT UNSIGNED NOT NULL,
  `congreso_id` INT UNSIGNED NOT NULL,
  `evaluador_id` INT UNSIGNED NULL DEFAULT NULL,
  `propuestascol` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_propuestas_usuarios` (`usuario_id` ASC),
  INDEX `fk_propuestas_congresos` (`congreso_id` ASC),
  INDEX `fk_propuestas_evaluadores` (`evaluador_id` ASC),
  CONSTRAINT `fk_propuestas_usuarios`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_propuestas_congresos`
    FOREIGN KEY (`congreso_id`)
    REFERENCES `congresos` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_propuestas_evaluadores`
    FOREIGN KEY (`evaluador_id`)
    REFERENCES `evaluadores` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- -----------------------------------------------------
-- Table `Comentarios_propuestas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Comentarios_propuestas` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Comentario` VARCHAR(256) NOT NULL,
  `propuestas_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE INDEX `Id_UNIQUE` (`Id` ASC),
  INDEX `fk_Comentarios_propuestas_propuestas1_idx` (`propuestas_id` ASC),
  CONSTRAINT `fk_Comentarios_propuestas_propuestas1`
    FOREIGN KEY (`propuestas_id`)
    REFERENCES `propuestas` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
