-- ============================================================
-- saas_cierre_aprobacion.sql
-- ============================================================
-- Aprobación del cierre de operación por el superadmin:
-- cuando el usuario registra el cierre, queda cierre_estado='pendiente'
-- y NO se considera operación concretada hasta que el admin lo aprueba
-- (cierre_estado='aprobado') tras verificar el pago de la comisión.
-- Ejecutar en phpMyAdmin CON LA BASE DEL PROYECTO SELECCIONADA
-- (verificar antes con: SELECT DATABASE();  -> debe ser arumasof_inmobiliariaxyz)
-- Idempotente y SIN foreign keys (hosting compartido)
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='cierre_estado');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `cierre_estado` VARCHAR(20) NULL DEFAULT NULL COMMENT ''pendiente|aprobado'' AFTER `cerrada_at`', 'SELECT ''ok'' AS msg');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='cierre_aprobado_at');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `cierre_aprobado_at` TIMESTAMP NULL DEFAULT NULL AFTER `cierre_estado`', 'SELECT ''ok'' AS msg');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='cierre_aprobado_por');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `cierre_aprobado_por` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `cierre_aprobado_at`', 'SELECT ''ok'' AS msg');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;
