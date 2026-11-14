-- ============================================================
-- Fix tabla users creada a mano: columnas que Laravel espera
-- Ejecutar en phpMyAdmin CON LA BASE DEL PROYECTO SELECCIONADA
-- (verificar antes: SELECT DATABASE(); -> arumasof_inmobiliariaxyz)
-- Idempotente
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'remember_token');
SET @ddl := IF(@c = 0,
  'ALTER TABLE `users` ADD COLUMN `remember_token` VARCHAR(100) NULL DEFAULT NULL AFTER `password`',
  'SELECT ''users.remember_token ya existe'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'email_verified_at');
SET @ddl := IF(@c = 0,
  'ALTER TABLE `users` ADD COLUMN `email_verified_at` TIMESTAMP NULL DEFAULT NULL AFTER `email`',
  'SELECT ''users.email_verified_at ya existe'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'telefono');
SET @ddl := IF(@c = 0,
  'ALTER TABLE `users` ADD COLUMN `telefono` VARCHAR(30) NULL DEFAULT NULL AFTER `email`',
  'SELECT ''users.telefono ya existe'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;
