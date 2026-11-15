-- ============================================================
-- Carga asistida: marca las propiedades cargadas por el admin
-- a nombre de una inmobiliaria (servicio de arranque del portal)
-- Ejecutar en phpMyAdmin CON LA BASE SELECCIONADA (SELECT DATABASE();)
-- Idempotente, SIN foreign keys
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='carga_asistida');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `carga_asistida` TINYINT(1) NOT NULL DEFAULT 0', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;
