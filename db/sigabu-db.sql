SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `sigabu` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
USE `sigabu` ;

-- -----------------------------------------------------
-- Table `sigabu`.`personas`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sigabu`.`personas` (
  `dni` BIGINT NOT NULL COMMENT 'Documento Nacional de Identidad (DNI).' ,
  `tipo_dni` VARCHAR(3) NOT NULL COMMENT 'Tipo de documento:\nCC -> Cédula de Ciudadanía.\nCE -> Cédula de Extranjería.\nTI -> Tarjeta de Identidad.\nRC -> Registro Civil.' ,
  `nombres` VARCHAR(45) NOT NULL ,
  `apellidos` VARCHAR(45) NOT NULL ,
  `telefono_fijo` VARCHAR(45) NULL COMMENT 'Número de teléfono fijo, o de residencia.' ,
  `telefono_movil` VARCHAR(45) NULL COMMENT 'Número de teléfono móvil (o celular).' ,
  `email` VARCHAR(60) NULL ,
  `fecha_nac` DATE NULL COMMENT 'Fecha de nacimiento.' ,
  `genero` CHAR(1) NOT NULL COMMENT 'Género sexual de la persona:\nH -> Hombre.\nM -> Mujer.' ,
  `direccion_residencia` VARCHAR(60) NULL COMMENT 'Dirección de residencia de la persona.' ,
  `monitor` TINYINT NOT NULL DEFAULT 0 COMMENT 'Los posibles valores del campo son:\n1 -> Activo.\n0 -> Inactivo.\nSi el valor que se guarda es activo (1), indica que la persona es monitor (o entrenador) y se le podrá asignar alguna actividad. ' ,
  `estado` TINYINT NOT NULL DEFAULT 1 COMMENT 'Los posibles valores del campo son:\n1 -> Activo.\n0 -> Inactivo.\nSi el Estado de una persona es activo, podrá entre otras acciones, inscribirse a actividades y ser beneficiaria de Bienestar Universitario.' ,
  `created_at` DATETIME NOT NULL ,
  `updated_at` DATETIME NULL ,
  PRIMARY KEY (`dni`) )
ENGINE = InnoDB
COMMENT = 'Son las personas las que conforman la comunidad universitari' /* comment truncated */ ;


-- -----------------------------------------------------
-- Table `sigabu`.`periodos`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sigabu`.`periodos` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `periodo` VARCHAR(8) NOT NULL COMMENT 'Periodo (académico) de un año. Por ej.:\n2011-1, 2011-2, etc.' ,
  `actual` TINYINT NOT NULL DEFAULT 0 COMMENT 'Sólo un periodo será definido como actual, para cuyo valor en este campo será 1. En caso contrario, deberá tener valor 0.' ,
  `fecha_inic` DATE NOT NULL COMMENT 'Fecha de inicio del periodo.' ,
  `fecha_fin` DATE NOT NULL COMMENT 'Fecha de finalización del periodo.' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `periodo_UNIQUE` (`periodo` ASC) )
ENGINE = InnoDB, 
COMMENT = 'Periodos académicos.' ;


-- -----------------------------------------------------
-- Table `sigabu`.`facultad`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sigabu`.`facultad` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(60) NOT NULL ,
  `abrev` VARCHAR(15) NULL COMMENT 'Abreviatura del nombre de la facultad.' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `nombre_UNIQUE` (`nombre` ASC) ,
  UNIQUE INDEX `abrev_UNIQUE` (`abrev` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigabu`.`programas`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sigabu`.`programas` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(60) NOT NULL ,
  `abrev` VARCHAR(20) NULL COMMENT 'Abreviatura del programa académico.' ,
  `facultad_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `nombre_UNIQUE` (`nombre` ASC) ,
  UNIQUE INDEX `abrev_UNIQUE` (`abrev` ASC) ,
  INDEX `fk_programas_facultad1` (`facultad_id` ASC) ,
  CONSTRAINT `fk_programas_facultad1`
    FOREIGN KEY (`facultad_id` )
    REFERENCES `sigabu`.`facultad` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigabu`.`multientidad`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sigabu`.`multientidad` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `entidad` VARCHAR(45) NOT NULL COMMENT 'Entidad a la cual pertenece el registro. El dato que se ingrese en esta columna, deberá estar en minúscula y (en lo posible) en plural.' ,
  `nombre` VARCHAR(45) NOT NULL COMMENT 'Nombre del registro de una determinada entidad.' ,
  `abrev` VARCHAR(15) NULL COMMENT 'Abreviación del nombre.' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB, 
COMMENT = 'Tabla para múltiple uso. (Tipos de) Contratos, jornadas, (ti' /* comment truncated */ ;


-- -----------------------------------------------------
-- Table `sigabu`.`perfiles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sigabu`.`perfiles` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `persona_dni` BIGINT NOT NULL ,
  `periodo_id` INT NOT NULL ,
  `perfil_multientidad` INT NOT NULL COMMENT 'Perfil según clasificación de la comunidad universitaria.' ,
  `jornada_multientidad` INT NULL COMMENT 'Jornada académica.\nAplica para estudiantes.' ,
  `programa_id` INT NULL COMMENT 'Programa académico.\nAplica para:\nEstudiantes,\nEgresados,\nDocentes y\nFuncionarios (sino se indica un programa para éste, se infiere que es adminstrativo).' ,
  `semestre` TINYINT NULL COMMENT 'Semestre que cursa un estudiante en un determinado programa académico.\nAplica para estudiantes.' ,
  `contrato_multientidad` INT NULL COMMENT 'Tipo de contrato asignado a un docente.\nAplica para docentes.' ,
  `parentesco_multientidad` INT NULL COMMENT 'Tipo de parentesco.\nAplica para familiares.' ,
  `apoderado_dni` BIGINT NULL COMMENT 'DNI de la persona por la cual es beneficiaria.\nAplica para familiares.' ,
  `created_at` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_perfil_personas` (`persona_dni` ASC) ,
  INDEX `fk_perfil_periodos1` (`periodo_id` ASC) ,
  INDEX `fk_perfil_programas1` (`programa_id` ASC) ,
  INDEX `fk_perfil_personas1` (`apoderado_dni` ASC) ,
  INDEX `fk_perfil_multientidad1` (`jornada_multientidad` ASC) ,
  INDEX `fk_perfil_multientidad2` (`contrato_multientidad` ASC) ,
  INDEX `fk_perfil_multientidad3` (`parentesco_multientidad` ASC) ,
  INDEX `fk_perfil_multientidad4` (`perfil_multientidad` ASC) ,
  CONSTRAINT `fk_perfil_personas`
    FOREIGN KEY (`persona_dni` )
    REFERENCES `sigabu`.`personas` (`dni` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_perfil_periodos1`
    FOREIGN KEY (`periodo_id` )
    REFERENCES `sigabu`.`periodos` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_perfil_programas1`
    FOREIGN KEY (`programa_id` )
    REFERENCES `sigabu`.`programas` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_perfil_personas1`
    FOREIGN KEY (`apoderado_dni` )
    REFERENCES `sigabu`.`personas` (`dni` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_perfil_multientidad1`
    FOREIGN KEY (`jornada_multientidad` )
    REFERENCES `sigabu`.`multientidad` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_perfil_multientidad2`
    FOREIGN KEY (`contrato_multientidad` )
    REFERENCES `sigabu`.`multientidad` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_perfil_multientidad3`
    FOREIGN KEY (`parentesco_multientidad` )
    REFERENCES `sigabu`.`multientidad` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_perfil_multientidad4`
    FOREIGN KEY (`perfil_multientidad` )
    REFERENCES `sigabu`.`multientidad` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB, 
COMMENT = 'Los perfiles son las diferentes clasificaciones que puede te' /* comment truncated */ ;


-- -----------------------------------------------------
-- Table `sigabu`.`roles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sigabu`.`roles` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(45) NOT NULL ,
  `permiso` TINYINT NOT NULL COMMENT 'Nivel de permiso (s) que tiene un rol. ' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `nombre_UNIQUE` (`nombre` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigabu`.`usuarios`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sigabu`.`usuarios` (
  `persona_dni` BIGINT NOT NULL ,
  `username` VARCHAR(45) NOT NULL ,
  `password` VARCHAR(100) NOT NULL ,
  `email` VARCHAR(60) NOT NULL ,
  `rol_id` INT NOT NULL COMMENT 'Rol que se asigna al usuario.' ,
  `estado` TINYINT NOT NULL DEFAULT 1 COMMENT 'Los posibles valores del campo son:\n1 -> Activo.\n0 -> Inactivo.' ,
  `fecha_activacion` DATETIME NULL COMMENT 'Ingresar en esta campo, si el usuario está inactivo, la fecha (y hora) en la cual éste volverá a estar activo.' ,
  `ultima_visita` DATETIME NULL ,
  `created_at` DATETIME NOT NULL ,
  INDEX `fk_usuarios_personas1` (`persona_dni` ASC) ,
  PRIMARY KEY (`persona_dni`) ,
  UNIQUE INDEX `username_UNIQUE` (`username` ASC) ,
  INDEX `fk_usuarios_roles1` (`rol_id` ASC) ,
  CONSTRAINT `fk_usuarios_personas1`
    FOREIGN KEY (`persona_dni` )
    REFERENCES `sigabu`.`personas` (`dni` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_usuarios_roles1`
    FOREIGN KEY (`rol_id` )
    REFERENCES `sigabu`.`roles` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigabu`.`areas`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sigabu`.`areas` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(45) NOT NULL ,
  `coordinador_dni` BIGINT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  UNIQUE INDEX `nombre_UNIQUE` (`nombre` ASC) ,
  INDEX `fk_areas_personas1` (`coordinador_dni` ASC) ,
  CONSTRAINT `fk_areas_personas1`
    FOREIGN KEY (`coordinador_dni` )
    REFERENCES `sigabu`.`personas` (`dni` )
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB, 
COMMENT = 'Áreas de Bienestar Universitario.' ;


-- -----------------------------------------------------
-- Table `sigabu`.`actividades`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sigabu`.`actividades` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(60) NOT NULL ,
  `area_id` INT NOT NULL ,
  `comentario` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_actividades_areas1` (`area_id` ASC) ,
  CONSTRAINT `fk_actividades_areas1`
    FOREIGN KEY (`area_id` )
    REFERENCES `sigabu`.`areas` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigabu`.`cursos`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sigabu`.`cursos` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `actividad_id` INT NOT NULL ,
  `periodo_id` INT NOT NULL ,
  `monitor_dni` BIGINT NULL ,
  `fecha_inic` DATE NULL ,
  `fecha_fin` DATE NULL ,
  `abierto` TINYINT NOT NULL DEFAULT 1 ,
  `comentario` TEXT NULL ,
  `created_at` DATETIME NOT NULL ,
  `updated_at` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_cursos_actividades1` (`actividad_id` ASC) ,
  INDEX `fk_cursos_periodos1` (`periodo_id` ASC) ,
  INDEX `fk_cursos_personas1` (`monitor_dni` ASC) ,
  CONSTRAINT `fk_cursos_actividades1`
    FOREIGN KEY (`actividad_id` )
    REFERENCES `sigabu`.`actividades` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cursos_periodos1`
    FOREIGN KEY (`periodo_id` )
    REFERENCES `sigabu`.`periodos` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cursos_personas1`
    FOREIGN KEY (`monitor_dni` )
    REFERENCES `sigabu`.`personas` (`dni` )
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB, 
COMMENT = 'Un curso es la programación de una actividad en un determina' /* comment truncated */ ;


-- -----------------------------------------------------
-- Table `sigabu`.`lugares`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sigabu`.`lugares` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(60) NOT NULL ,
  `administrador` VARCHAR(80) NULL COMMENT 'Nombre (y apellidos) del administrador del lugar.' ,
  `direccion` VARCHAR(60) NOT NULL COMMENT 'Dirección del lugar.' ,
  `telefono_fijo` VARCHAR(45) NULL ,
  `telefono_movil` VARCHAR(45) NULL ,
  `email` VARCHAR(60) NULL ,
  `comentario` TEXT NULL ,
  `created_at` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `nombre_UNIQUE` (`nombre` ASC) )
ENGINE = InnoDB, 
COMMENT = 'Espacios deportivos o culturales donde se desarrollan activi' /* comment truncated */ ;


-- -----------------------------------------------------
-- Table `sigabu`.`horarios`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sigabu`.`horarios` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `curso_id` INT NOT NULL ,
  `dia` TINYINT NOT NULL COMMENT 'Día (en número) de la semana del horario:\nLunes -> 1,\nMartes -> 2,\n...,\nDomingo -> 7.' ,
  `hora_inic` TIME NOT NULL COMMENT 'Hora de inicio.' ,
  `hora_fin` TIME NOT NULL COMMENT 'Hora de finalización.' ,
  `lugar_id` INT NOT NULL ,
  `comentario` TEXT NULL ,
  `created_at` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_horarios_cursos1` (`curso_id` ASC) ,
  INDEX `fk_horarios_lugares1` (`lugar_id` ASC) ,
  CONSTRAINT `fk_horarios_cursos1`
    FOREIGN KEY (`curso_id` )
    REFERENCES `sigabu`.`cursos` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_horarios_lugares1`
    FOREIGN KEY (`lugar_id` )
    REFERENCES `sigabu`.`lugares` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigabu`.`inscripciones`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sigabu`.`inscripciones` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `curso_id` INT NOT NULL ,
  `persona_dni` BIGINT NOT NULL ,
  `fecha_inscripcion` DATETIME NOT NULL ,
  `created_at` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_inscripciones_cursos1` (`curso_id` ASC) ,
  INDEX `fk_inscripciones_personas1` (`persona_dni` ASC) ,
  CONSTRAINT `fk_inscripciones_cursos1`
    FOREIGN KEY (`curso_id` )
    REFERENCES `sigabu`.`cursos` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_inscripciones_personas1`
    FOREIGN KEY (`persona_dni` )
    REFERENCES `sigabu`.`personas` (`dni` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigabu`.`asistencias`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sigabu`.`asistencias` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `inscripcion_id` BIGINT NOT NULL ,
  `fecha_asistencia` DATE NOT NULL ,
  `horario_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_asistencias_inscripciones1` (`inscripcion_id` ASC) ,
  INDEX `fk_asistencias_horarios1` (`horario_id` ASC) ,
  CONSTRAINT `fk_asistencias_inscripciones1`
    FOREIGN KEY (`inscripcion_id` )
    REFERENCES `sigabu`.`inscripciones` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_asistencias_horarios1`
    FOREIGN KEY (`horario_id` )
    REFERENCES `sigabu`.`horarios` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `sigabu`.`facultad`
-- -----------------------------------------------------
START TRANSACTION;
USE `sigabu`;
INSERT INTO `sigabu`.`facultad` (`id`, `nombre`, `abrev`) VALUES (1, 'Ingeniería', 'Ing.');
INSERT INTO `sigabu`.`facultad` (`id`, `nombre`, `abrev`) VALUES (2, 'Derecho', 'Der.');
INSERT INTO `sigabu`.`facultad` (`id`, `nombre`, `abrev`) VALUES (3, 'Psicología', 'Psicol.');
INSERT INTO `sigabu`.`facultad` (`id`, `nombre`, `abrev`) VALUES (4, 'Ciencias Económicas Administrativas y Contables', 'C.A.E.C.');
INSERT INTO `sigabu`.`facultad` (`id`, `nombre`, `abrev`) VALUES (5, 'Fisioterapia', 'Fisio.');

COMMIT;

-- -----------------------------------------------------
-- Data for table `sigabu`.`programas`
-- -----------------------------------------------------
START TRANSACTION;
USE `sigabu`;
INSERT INTO `sigabu`.`programas` (`id`, `nombre`, `abrev`, `facultad_id`) VALUES (1, 'Ingeniería de Sistemas', 'Ing. de Sist.', 1);
INSERT INTO `sigabu`.`programas` (`id`, `nombre`, `abrev`, `facultad_id`) VALUES (2, 'Ingeniería Industrial', 'Ing. Indust.', 1);
INSERT INTO `sigabu`.`programas` (`id`, `nombre`, `abrev`, `facultad_id`) VALUES (3, 'Derecho', 'Der.', 2);
INSERT INTO `sigabu`.`programas` (`id`, `nombre`, `abrev`, `facultad_id`) VALUES (4, 'Psicología', 'Psicol.', 3);
INSERT INTO `sigabu`.`programas` (`id`, `nombre`, `abrev`, `facultad_id`) VALUES (5, 'Administración de Empresas', 'Admin. de Emp.', 4);
INSERT INTO `sigabu`.`programas` (`id`, `nombre`, `abrev`, `facultad_id`) VALUES (6, 'Contaduría Pública', 'Cont. Públic.', 4);
INSERT INTO `sigabu`.`programas` (`id`, `nombre`, `abrev`, `facultad_id`) VALUES (7, 'Mercadeo', 'Merc.', 4);
INSERT INTO `sigabu`.`programas` (`id`, `nombre`, `abrev`, `facultad_id`) VALUES (8, 'Fisioterapia', 'Fisio.', 5);

COMMIT;

-- -----------------------------------------------------
-- Data for table `sigabu`.`multientidad`
-- -----------------------------------------------------
START TRANSACTION;
USE `sigabu`;
INSERT INTO `sigabu`.`multientidad` (`id`, `entidad`, `nombre`, `abrev`) VALUES (1, 'jornadas', 'Diurno', 'D');
INSERT INTO `sigabu`.`multientidad` (`id`, `entidad`, `nombre`, `abrev`) VALUES (2, 'jornadas', 'Nocturno', 'N');
INSERT INTO `sigabu`.`multientidad` (`id`, `entidad`, `nombre`, `abrev`) VALUES (3, 'contratos', 'Tiempo completo', 'TC');
INSERT INTO `sigabu`.`multientidad` (`id`, `entidad`, `nombre`, `abrev`) VALUES (4, 'contratos', 'Medio tiempo', 'MT');
INSERT INTO `sigabu`.`multientidad` (`id`, `entidad`, `nombre`, `abrev`) VALUES (5, 'contratos', 'Catedrático', 'CAT');
INSERT INTO `sigabu`.`multientidad` (`id`, `entidad`, `nombre`, `abrev`) VALUES (6, 'parentescos', 'Madre', NULL);
INSERT INTO `sigabu`.`multientidad` (`id`, `entidad`, `nombre`, `abrev`) VALUES (7, 'parentescos', 'Padre', NULL);
INSERT INTO `sigabu`.`multientidad` (`id`, `entidad`, `nombre`, `abrev`) VALUES (8, 'parentescos', 'Hermano', NULL);
INSERT INTO `sigabu`.`multientidad` (`id`, `entidad`, `nombre`, `abrev`) VALUES (9, 'parentescos', 'Hijo', NULL);
INSERT INTO `sigabu`.`multientidad` (`id`, `entidad`, `nombre`, `abrev`) VALUES (10, 'parentescos', 'Cónyuge', NULL);
INSERT INTO `sigabu`.`multientidad` (`id`, `entidad`, `nombre`, `abrev`) VALUES (11, 'comunidad_universitaria', 'Estudiante', 'Est.');
INSERT INTO `sigabu`.`multientidad` (`id`, `entidad`, `nombre`, `abrev`) VALUES (12, 'comunidad_universitaria', 'Docente', 'Doc.');
INSERT INTO `sigabu`.`multientidad` (`id`, `entidad`, `nombre`, `abrev`) VALUES (13, 'comunidad_universitaria', 'Egresado', 'Egres.');
INSERT INTO `sigabu`.`multientidad` (`id`, `entidad`, `nombre`, `abrev`) VALUES (14, 'comunidad_universitaria', 'Funcionario', 'Func.');
INSERT INTO `sigabu`.`multientidad` (`id`, `entidad`, `nombre`, `abrev`) VALUES (15, 'comunidad_universitaria', 'Familiar', 'Fliar.');

COMMIT;

-- -----------------------------------------------------
-- Data for table `sigabu`.`roles`
-- -----------------------------------------------------
START TRANSACTION;
USE `sigabu`;
INSERT INTO `sigabu`.`roles` (`id`, `nombre`, `permiso`) VALUES (1, 'Jefe de Bienestar Universitario', 5);
INSERT INTO `sigabu`.`roles` (`id`, `nombre`, `permiso`) VALUES (2, 'Coordinador de Cultura y Deporte', 4);
INSERT INTO `sigabu`.`roles` (`id`, `nombre`, `permiso`) VALUES (3, 'Secretaria', 3);
INSERT INTO `sigabu`.`roles` (`id`, `nombre`, `permiso`) VALUES (4, 'Monitor', 2);

COMMIT;

-- -----------------------------------------------------
-- Data for table `sigabu`.`areas`
-- -----------------------------------------------------
START TRANSACTION;
USE `sigabu`;
INSERT INTO `sigabu`.`areas` (`id`, `nombre`, `coordinador_dni`) VALUES (1, 'Artística y Cultural', NULL);
INSERT INTO `sigabu`.`areas` (`id`, `nombre`, `coordinador_dni`) VALUES (2, 'Recreación y Deportes', NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `sigabu`.`actividades`
-- -----------------------------------------------------
START TRANSACTION;
USE `sigabu`;
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (1, 'Ajedrez', 2, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (2, 'Baloncesto', 2, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (3, 'Fútbol', 2, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (4, 'Fútbol Sala', 2, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (5, 'Yoga', 2, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (6, 'Kung Fu', 2, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (7, 'Natación', 2, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (8, 'Tenis de Mesa', 2, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (9, 'Voleibol', 2, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (10, 'Danzas', 1, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (11, 'Coro', 1, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (12, 'Balada', 1, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (13, 'Vals', 1, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (14, 'Boleros', 1, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (15, 'Grupo de Rock', 1, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (16, 'Artesanías', 1, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (17, 'Bailes Modernos', 1, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (18, 'Cine Foro', 1, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (19, 'Guitarra', 1, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (20, 'Teatro', 1, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (21, 'Técnica Vocal', 1, NULL);
INSERT INTO `sigabu`.`actividades` (`id`, `nombre`, `area_id`, `comentario`) VALUES (22, 'Explorando las Artes', 1, NULL);

COMMIT;
