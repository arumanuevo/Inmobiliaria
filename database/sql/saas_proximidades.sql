-- ============================================================
-- saas_proximidades.sql
-- Proximidades / ventajas estructurales del entorno del inmueble
-- (JSON: [{"nombre": "Estación de tren", "metros": 200}, ...])
-- Idempotente: se puede correr varias veces. Sin FKs (hosting compartido).
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='proximidades');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `proximidades` TEXT NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- Ejemplos demo opcionales (solo si querés poblar el seeder existente):
-- UPDATE `inmuebles` SET `proximidades` = JSON_ARRAY(JSON_OBJECT('nombre','Estación de tren','metros',250), JSON_OBJECT('nombre','Plaza','metros',120)) WHERE `id` = 1;
