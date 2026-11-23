-- ============================================================
-- saas_verificacion.sql
-- Datos de habilitación/identidad de la inmobiliaria para verificación:
--  - cuit: CUIT del responsable (AFIP)
--  - matricula_registro: matrícula en el Registro/Colegio provincial de
--    corredores inmobiliarios (CABA, PBA, etc.)
--  - verificada: la marca el superadmin tras chequear esos datos
-- Idempotente, sin FKs.
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='cuit');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `cuit` VARCHAR(20) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='matricula_registro');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `matricula_registro` VARCHAR(80) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='verificada');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `verificada` TINYINT(1) NOT NULL DEFAULT 0', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;
