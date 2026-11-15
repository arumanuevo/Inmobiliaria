-- ============================================================
-- Declaración de propietario/autorización en cada publicación
-- (protección legal: el anunciante declara ser dueño o estar autorizado)
-- Ejecutar en phpMyAdmin CON LA BASE SELECCIONADA (SELECT DATABASE();)
-- Idempotente, SIN foreign keys
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='declaracion_propietario');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `declaracion_propietario` TINYINT(1) NOT NULL DEFAULT 0', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;
