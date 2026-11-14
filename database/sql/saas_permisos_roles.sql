-- ============================================================
-- Tablas de Spatie laravel-permission + roles base + superadmin
-- Ejecutar en phpMyAdmin CON LA BASE DEL PROYECTO SELECCIONADA
-- (verificar antes con: SELECT DATABASE();  -> debe ser arumasof_inmobiliariaxyz)
-- Idempotente y SIN foreign keys (hosting compartido)
-- ============================================================

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `guard_name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `roles` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `guard_name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` BIGINT UNSIGNED NOT NULL,
  `model_type` VARCHAR(255) NOT NULL,
  `model_id` BIGINT UNSIGNED NOT NULL,
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` BIGINT UNSIGNED NOT NULL,
  `model_type` VARCHAR(255) NOT NULL,
  `model_id` BIGINT UNSIGNED NOT NULL,
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  PRIMARY KEY (`role_id`,`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` BIGINT UNSIGNED NOT NULL,
  `role_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles base
INSERT INTO `roles` (`name`,`guard_name`,`created_at`,`updated_at`) VALUES
('admin','web',NOW(),NOW()),
('user','web',NOW(),NOW()),
('inmobiliaria','web',NOW(),NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();

-- Asignar rol admin al superadmin (scastellano10@gmail.com)
INSERT INTO `model_has_roles` (`role_id`,`model_type`,`model_id`)
SELECT r.`id`, 'App\\Models\\User', u.`id`
FROM `roles` r CROSS JOIN `users` u
WHERE r.`name` = 'admin' AND u.`email` = 'scastellano10@gmail.com'
AND NOT EXISTS (
  SELECT 1 FROM `model_has_roles` m
  WHERE m.`role_id` = r.`id` AND m.`model_type` = 'App\\Models\\User' AND m.`model_id` = u.`id`
);
