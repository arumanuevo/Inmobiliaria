-- ============================================================
-- Comisión opcional por cierre de operación (vendida/alquilada)
-- Ejecutar en phpMyAdmin CON LA BASE SELECCIONADA (SELECT DATABASE();)
-- Idempotente, SIN foreign keys
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='comision_porcentaje');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `comision_porcentaje` DECIMAL(5,2) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='comision_monto');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `comision_monto` DECIMAL(14,2) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='comision_estado');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `comision_estado` VARCHAR(20) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='comision_cobrada_at');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `comision_cobrada_at` TIMESTAMP NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;
