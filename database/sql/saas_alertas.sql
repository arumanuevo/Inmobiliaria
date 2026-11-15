-- ============================================================
-- Alertas de nuevas propiedades (búsquedas guardadas)
-- Ejecutar en phpMyAdmin CON LA BASE SELECCIONADA (SELECT DATABASE();)
-- Idempotente, SIN foreign keys
-- ============================================================

CREATE TABLE IF NOT EXISTS `busquedas_guardadas` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `nombre` VARCHAR(150) NULL DEFAULT NULL,
  `email` VARCHAR(150) NOT NULL,
  `filtros` TEXT NULL,
  `activa` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='busquedas_guardadas' AND INDEX_NAME='busquedas_guardadas_email_index');
SET @ddl := IF(@c=0, 'ALTER TABLE `busquedas_guardadas` ADD INDEX `busquedas_guardadas_email_index` (`email`)', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='busquedas_guardadas' AND INDEX_NAME='busquedas_guardadas_user_id_index');
SET @ddl := IF(@c=0, 'ALTER TABLE `busquedas_guardadas` ADD INDEX `busquedas_guardadas_user_id_index` (`user_id`)', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;
