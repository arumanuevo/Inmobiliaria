-- ============================================================
-- Solicitudes de carga asistida (el cliente pide que el equipo
-- del portal cargue su propiedad; el admin la atiende desde /admin)
-- Ejecutar en phpMyAdmin CON LA BASE SELECCIONADA (SELECT DATABASE();)
-- Idempotente, SIN foreign keys
-- ============================================================

CREATE TABLE IF NOT EXISTS `solicitudes_carga` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `inmobiliaria_id` BIGINT UNSIGNED NOT NULL,
  `titulo` VARCHAR(255) NULL DEFAULT NULL,
  `detalle` TEXT NULL,
  `estado` ENUM('pendiente','en_carga','cargada','cancelada') NOT NULL DEFAULT 'pendiente',
  `inmueble_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `atendida_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  KEY `idx_solicitudes_inmobiliaria` (`inmobiliaria_id`),
  KEY `idx_solicitudes_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='solicitudes_carga' AND INDEX_NAME='idx_solicitudes_estado');
SET @ddl := IF(@c=0, 'ALTER TABLE `solicitudes_carga` ADD INDEX `idx_solicitudes_estado` (`estado`)', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;
