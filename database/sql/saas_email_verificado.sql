-- ============================================================
-- saas_email_verificado.sql
-- Verificación de email al registrarse (MustVerifyEmail):
--  - la columna users.email_verified_at ya existe (migración base);
--  - este script marca como verificados a los usuarios que ya existían
--    antes de activar la verificación (para que no les aparezca el aviso).
-- Idempotente: si se corre de nuevo no cambia nada (solo toca los NULL).
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='users' AND COLUMN_NAME='email_verified_at');
SET @ddl := IF(@c=0, 'ALTER TABLE `users` ADD COLUMN `email_verified_at` TIMESTAMP NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- Backfill: usuarios preexistentes quedan verificados
UPDATE `users` SET `email_verified_at` = NOW() WHERE `email_verified_at` IS NULL;
