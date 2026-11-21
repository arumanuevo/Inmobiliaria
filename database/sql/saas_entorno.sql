-- ============================================================
-- saas_entorno.sql
-- 1) uso_detalle: texto libre de uso/habilitación para comerciales
--    (kiosco, tienda de ropa, gastronomía, oficina, depósito...)
-- 2) puntaje_entorno: score 0-100 calculado con Overpass (OSM)
--    según lo que rodea al inmueble (arteria, transporte, comercio,
--    servicios) + detalle JSON + fecha de cálculo.
-- Idempotente, sin FKs (hosting compartido).
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='uso_detalle');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `uso_detalle` VARCHAR(120) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='puntaje_entorno');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `puntaje_entorno` TINYINT UNSIGNED NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='puntaje_entorno_detalle');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `puntaje_entorno_detalle` TEXT NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='puntaje_entorno_at');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `puntaje_entorno_at` TIMESTAMP NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;
