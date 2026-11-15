-- ============================================================
-- Plan "Particular" (gratis, restrictivo) para dueños que publican
-- su propia vivienda: 1 publicación, 0 destacadas, 5 fotos.
-- Ejecutar en phpMyAdmin CON LA BASE SELECCIONADA (SELECT DATABASE();)
-- Idempotente, SIN foreign keys
-- ============================================================

SET @c := (SELECT COUNT(*) FROM `planes` WHERE `slug` = 'particular');
SET @ddl := IF(@c=0, 'INSERT INTO `planes` (`nombre`, `slug`, `precio_mensual`, `limite_inmuebles`, `limite_destacados`, `limite_fotos`, `activo`, `created_at`, `updated_at`) VALUES (''Particular'', ''particular'', 0.00, 1, 0, 5, 1, NOW(), NOW())', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- Backfill: los usuarios con rol "particular" pasan al plan Particular
SET @planId := (SELECT `id` FROM `planes` WHERE `slug` = 'particular' LIMIT 1);
UPDATE `inmobiliarias` i
JOIN `model_has_roles` mr ON mr.`model_id` = i.`usuario_id` AND mr.`model_type` = 'App\\Models\\User'
JOIN `roles` r ON r.`id` = mr.`role_id` AND r.`name` = 'particular'
SET i.`plan_id` = @planId
WHERE @planId IS NOT NULL;
