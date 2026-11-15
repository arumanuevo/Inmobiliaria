-- ============================================================
-- Cierre de operaciones (vendida/alquilada) en inmuebles
-- Ejecutar en phpMyAdmin CON LA BASE SELECCIONADA (SELECT DATABASE();)
-- Idempotente, SIN foreign keys
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='cerrada');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `cerrada` TINYINT(1) NOT NULL DEFAULT 0 AFTER `publicado`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='tipo_cierre');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `tipo_cierre` VARCHAR(20) NULL DEFAULT NULL AFTER `cerrada`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='cerrada_at');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `cerrada_at` TIMESTAMP NULL DEFAULT NULL AFTER `tipo_cierre`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;
