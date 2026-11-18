-- ============================================================
-- DESHACER EL SEEDER DEMO (seed_demo.sql)
-- Ejecutar en phpMyAdmin CON LA BASE SELECCIONADA (SELECT DATABASE();)
-- Borra SOLO lo creado por el seeder, identificado por la marca:
--   users con email '%@seed.local' e inmobiliarias con slug 'demo-%'
-- (y todo lo que cuelga de ellos: roles, inmuebles, contactos,
--  boletos y búsquedas guardadas).
-- NO toca datos reales del portal.
-- SIN foreign keys
-- ============================================================

-- Roles asignados a usuarios demo
DELETE mr FROM `model_has_roles` mr
JOIN `users` u ON u.`id` = mr.`model_id` AND mr.`model_type` = 'App\\Models\\User'
WHERE u.`email` LIKE '%@seed.local';

-- Consultas (contactos) de demo o sobre propiedades/inmobiliarias demo
DELETE FROM `contactos`
WHERE `email` LIKE '%@seed.local'
   OR `inmobiliaria_id` IN (SELECT `id` FROM `inmobiliarias` WHERE `slug` LIKE 'demo-%')
   OR `inmueble_id` IN (SELECT `id` FROM `inmuebles` WHERE `inmobiliaria_id` IN (SELECT `id` FROM `inmobiliarias` WHERE `slug` LIKE 'demo-%'));

-- Búsquedas guardadas de demo
DELETE FROM `busquedas_guardadas`
WHERE `email` LIKE '%@seed.local'
   OR `user_id` IN (SELECT `id` FROM `users` WHERE `email` LIKE '%@seed.local');

-- Boletos de inmobiliarias demo
DELETE FROM `boletos`
WHERE `inmobiliaria_id` IN (SELECT `id` FROM `inmobiliarias` WHERE `slug` LIKE 'demo-%');

-- Propiedades demo (por inmobiliaria demo o por usuario demo)
DELETE FROM `inmuebles`
WHERE `inmobiliaria_id` IN (SELECT `id` FROM `inmobiliarias` WHERE `slug` LIKE 'demo-%')
   OR `usuario_id` IN (SELECT `id` FROM `users` WHERE `email` LIKE '%@seed.local');

-- Inmobiliarias demo
DELETE FROM `inmobiliarias` WHERE `slug` LIKE 'demo-%';

-- Usuarios demo
DELETE FROM `users` WHERE `email` LIKE '%@seed.local';

SELECT 'Seeder demo deshecho. Quedaron solo los datos reales del portal.' AS msg;
