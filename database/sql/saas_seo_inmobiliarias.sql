-- ============================================================
-- saas_seo_inmobiliarias.sql
-- SEO personalizado por inmobiliaria (feature Premium):
--  - meta_titulo: título de la página /inmobiliaria/{slug} (máx 70 chars recomendado)
--  - meta_descripcion: meta description de esa página (máx 160-200 chars)
-- Si están vacíos (o el plan no es Premium), el sistema genera el título y
-- la descripción automáticamente con los datos del perfil.
-- Idempotente, sin FKs.
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='meta_titulo');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `meta_titulo` VARCHAR(70) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='meta_descripcion');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `meta_descripcion` VARCHAR(200) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;
