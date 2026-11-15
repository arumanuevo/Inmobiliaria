-- ============================================================
-- Rol "particular" (dueño que publica su propia vivienda, sin ser inmobiliaria)
-- Ejecutar en phpMyAdmin CON LA BASE SELECCIONADA (SELECT DATABASE();)
-- Idempotente, SIN foreign keys
-- ============================================================

SET @c := (SELECT COUNT(*) FROM `roles` WHERE `name` = 'particular' AND `guard_name` = 'web');
SET @ddl := IF(@c=0, 'INSERT INTO `roles` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES (''particular'', ''web'', NOW(), NOW())', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;
