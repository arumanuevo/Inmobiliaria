-- ============================================================
-- Enriquecimiento: campos descriptivos de inmueble (estilo portales AR)
-- + cobertura/zona y branding (portada/galería) de la inmobiliaria
-- Ejecutar en phpMyAdmin CON LA BASE SELECCIONADA (SELECT DATABASE();)
-- Idempotente, SIN foreign keys
-- ============================================================

-- Inmuebles: campos descriptivos tipo Zonaprop/Argenprop
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='tipo_propiedad');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `tipo_propiedad` VARCHAR(30) NULL DEFAULT NULL AFTER `tipo_operacion`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='moneda');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `moneda` VARCHAR(3) NULL DEFAULT ''ARS'' AFTER `precio`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='ambientes');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `ambientes` INT NULL DEFAULT NULL AFTER `habitaciones`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='cocheras');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `cocheras` INT NOT NULL DEFAULT 0 AFTER `banos`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='expensas');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `expensas` DECIMAL(10,2) NULL DEFAULT NULL AFTER `cocheras`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='orientacion');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `orientacion` VARCHAR(20) NULL DEFAULT NULL AFTER `expensas`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='estado_propiedad');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `estado_propiedad` VARCHAR(30) NULL DEFAULT NULL AFTER `orientacion`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- Inmobiliarias: cobertura geográfica + ubicación de la oficina + branding
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='zonas_cobertura');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `zonas_cobertura` VARCHAR(255) NULL DEFAULT NULL AFTER `provincia`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='lat');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `lat` DECIMAL(10,8) NULL DEFAULT NULL AFTER `zonas_cobertura`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='lng');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `lng` DECIMAL(10,8) NULL DEFAULT NULL AFTER `lat`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='imagen_portada');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `imagen_portada` VARCHAR(255) NULL DEFAULT NULL AFTER `logo`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='galeria');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `galeria` TEXT NULL DEFAULT NULL AFTER `imagen_portada`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;
