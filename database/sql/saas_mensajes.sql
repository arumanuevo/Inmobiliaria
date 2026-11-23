-- ============================================================
-- saas_mensajes.sql
-- Mensajería interna: avisos del superadmin a los usuarios del SaaS
-- (ej: aviso de actualización/indexación de precios de planes).
-- Fan-out: al enviar se inserta UNA fila por destinatario, así cada
-- usuario tiene su propio estado de leído (badge de no leídos).
--  - usuario_id: destinatario (BIGINT, sin FK)
--  - titulo / cuerpo / tipo ('aviso','planes','mantenimiento','otro')
--  - leido_at: NULL = no leído
-- Idempotente, sin FKs.
-- ============================================================

SET @t := (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='mensajes');
SET @ddl := IF(@t=0, 'CREATE TABLE `mensajes` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `titulo` VARCHAR(150) NOT NULL,
  `cuerpo` TEXT NOT NULL,
  `tipo` VARCHAR(30) NOT NULL DEFAULT ''aviso'',
  `leido_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  KEY `idx_mensajes_usuario` (`usuario_id`),
  KEY `idx_mensajes_leido` (`leido_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- Por si la tabla ya existía sin alguna columna:
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='mensajes' AND COLUMN_NAME='usuario_id');
SET @ddl := IF(@c=0, 'ALTER TABLE `mensajes` ADD COLUMN `usuario_id` BIGINT UNSIGNED NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='mensajes' AND COLUMN_NAME='titulo');
SET @ddl := IF(@c=0, 'ALTER TABLE `mensajes` ADD COLUMN `titulo` VARCHAR(150) NOT NULL DEFAULT ''''', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='mensajes' AND COLUMN_NAME='cuerpo');
SET @ddl := IF(@c=0, 'ALTER TABLE `mensajes` ADD COLUMN `cuerpo` TEXT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='mensajes' AND COLUMN_NAME='tipo');
SET @ddl := IF(@c=0, 'ALTER TABLE `mensajes` ADD COLUMN `tipo` VARCHAR(30) NOT NULL DEFAULT ''aviso''', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='mensajes' AND COLUMN_NAME='leido_at');
SET @ddl := IF(@c=0, 'ALTER TABLE `mensajes` ADD COLUMN `leido_at` TIMESTAMP NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;
