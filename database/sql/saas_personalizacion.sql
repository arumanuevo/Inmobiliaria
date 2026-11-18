-- ============================================================
-- Personalización avanzada (optativa) del perfil público de la inmobiliaria
-- Ejecutar en phpMyAdmin CON LA BASE SELECCIONADA (SELECT DATABASE();)
-- Idempotente, SIN foreign keys
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='tema');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `tema` VARCHAR(20) NULL DEFAULT ''moderno''', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='mensaje_destacado');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `mensaje_destacado` VARCHAR(255) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='mostrar_operaciones');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `mostrar_operaciones` TINYINT(1) NOT NULL DEFAULT 1', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='mostrar_mapa');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `mostrar_mapa` TINYINT(1) NOT NULL DEFAULT 1', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='mostrar_redes');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `mostrar_redes` TINYINT(1) NOT NULL DEFAULT 1', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;
