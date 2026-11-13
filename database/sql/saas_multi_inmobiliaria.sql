-- ============================================================
-- SaaS multi-inmobiliaria - Ejecutar en phpMyAdmin (cPanel)
-- Idempotente: se puede re-ejecutar sin romper nada.
-- SIN foreign keys a propósito: en hosting compartido dan
-- error 150 si el engine/charset/tipo de users.id no coincide.
-- La integridad la maneja la aplicación (Laravel).
-- ============================================================

-- ============================================================
-- 1) PLANES (primero, porque inmobiliarias referencia plan_id)
-- ============================================================
CREATE TABLE IF NOT EXISTS `planes` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `precio_mensual` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `limite_inmuebles` INT NOT NULL DEFAULT 5,
  `limite_destacados` INT NOT NULL DEFAULT 0,
  `limite_fotos` INT NOT NULL DEFAULT 10,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `planes` (`nombre`,`slug`,`precio_mensual`,`limite_inmuebles`,`limite_destacados`,`limite_fotos`,`activo`,`created_at`,`updated_at`) VALUES
('Gratis','gratis',0.00,5,1,5,1,NOW(),NOW()),
('Basico','basico',9900.00,50,3,20,1,NOW(),NOW()),
('Premium','premium',19900.00,999,10,50,1,NOW(),NOW())
ON DUPLICATE KEY UPDATE `nombre`=VALUES(`nombre`), `precio_mensual`=VALUES(`precio_mensual`),
  `limite_inmuebles`=VALUES(`limite_inmuebles`), `limite_destacados`=VALUES(`limite_destacados`),
  `limite_fotos`=VALUES(`limite_fotos`), `updated_at`=NOW();

-- ============================================================
-- 2) INMOBILIARIAS - perfil de empresa, 1:1 con users
-- ============================================================
CREATE TABLE IF NOT EXISTS `inmobiliarias` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` BIGINT UNSIGNED NOT NULL,
  `plan_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `nombre_comercial` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(160) NOT NULL UNIQUE,
  `logo` VARCHAR(255) NULL DEFAULT NULL,
  `descripcion` TEXT NULL,
  `direccion` VARCHAR(255) NULL DEFAULT NULL,
  `ciudad` VARCHAR(100) NULL DEFAULT NULL,
  `provincia` VARCHAR(100) NULL DEFAULT NULL,
  `codigo_postal` VARCHAR(20) NULL DEFAULT NULL,
  `telefono` VARCHAR(30) NULL DEFAULT NULL,
  `whatsapp` VARCHAR(30) NULL DEFAULT NULL,
  `email` VARCHAR(150) NULL DEFAULT NULL,
  `sitio_web` VARCHAR(255) NULL DEFAULT NULL,
  `facebook` VARCHAR(255) NULL DEFAULT NULL,
  `instagram` VARCHAR(255) NULL DEFAULT NULL,
  `color_tema` VARCHAR(7) NULL DEFAULT '#0d6efd',
  `estado` ENUM('pendiente','activa','suspendida') NOT NULL DEFAULT 'pendiente',
  `plan_vence_at` DATE NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  KEY `idx_inmobiliarias_usuario` (`usuario_id`),
  KEY `idx_inmobiliarias_plan` (`plan_id`),
  KEY `idx_inmobiliarias_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3) users: agregar telefono (idempotente)
-- ============================================================
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='users' AND COLUMN_NAME='telefono');
SET @ddl := IF(@c=0, 'ALTER TABLE `users` ADD COLUMN `telefono` VARCHAR(30) NULL DEFAULT NULL AFTER `email`',
              'SELECT ''users.telefono ya existe'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- ============================================================
-- 4) inmuebles: inmobiliaria_id, publicado, visitas + índices
-- ============================================================
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='inmobiliaria_id');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `inmobiliaria_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `usuario_id`',
              'SELECT ''inmuebles.inmobiliaria_id ya existe'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='publicado');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `publicado` TINYINT(1) NOT NULL DEFAULT 1 AFTER `destacado`',
              'SELECT ''inmuebles.publicado ya existe'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='visitas');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `visitas` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `publicado`',
              'SELECT ''inmuebles.visitas ya existe'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS
           WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND INDEX_NAME='idx_inmuebles_inmobiliaria');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD INDEX `idx_inmuebles_inmobiliaria` (`inmobiliaria_id`)',
              'SELECT ''indice ya existe'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS
           WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND INDEX_NAME='idx_inmuebles_busqueda');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD INDEX `idx_inmuebles_busqueda` (`tipo_operacion`,`ciudad`,`precio`,`publicado`)',
              'SELECT ''indice ya existe'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- ============================================================
-- 5) contactos: a qué inmobiliaria/propiedad va el mensaje
-- ============================================================
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='contactos' AND COLUMN_NAME='inmobiliaria_id');
SET @ddl := IF(@c=0, 'ALTER TABLE `contactos` ADD COLUMN `inmobiliaria_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `mensaje`',
              'SELECT ''contactos.inmobiliaria_id ya existe'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='contactos' AND COLUMN_NAME='inmueble_id');
SET @ddl := IF(@c=0, 'ALTER TABLE `contactos` ADD COLUMN `inmueble_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `inmobiliaria_id`',
              'SELECT ''contactos.inmueble_id ya existe'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- ============================================================
-- 6) Rol 'inmobiliaria' en Spatie (si no existe)
-- ============================================================
INSERT INTO `roles` (`name`,`guard_name`,`created_at`,`updated_at`)
SELECT 'inmobiliaria','web',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `roles` WHERE `name`='inmobiliaria' AND `guard_name`='web');

-- ============================================================
-- 7) Backfill: perfil demo para TU cuenta admin
--    (scastellano10@gmail.com) y vincular los inmuebles
--    existentes a esa inmobiliaria
-- ============================================================
INSERT INTO `inmobiliarias`
  (`usuario_id`,`plan_id`,`nombre_comercial`,`slug`,`descripcion`,`telefono`,`email`,`estado`,`plan_vence_at`,`created_at`,`updated_at`)
SELECT u.`id`,
       (SELECT `id` FROM `planes` WHERE `slug`='premium' LIMIT 1),
       'Inmobiliaria XYZ','inmobiliaria-xyz','Propiedades seleccionadas en Chivilcoy y zona.','+123 456 7890',
       u.`email`,'activa',DATE_ADD(CURDATE(), INTERVAL 1 YEAR),NOW(),NOW()
FROM `users` u
WHERE u.`email`='scastellano10@gmail.com'
  AND NOT EXISTS (SELECT 1 FROM `inmobiliarias` i WHERE i.`usuario_id`=u.`id`);

UPDATE `inmuebles` i
JOIN `inmobiliarias` m ON m.`usuario_id` = i.`usuario_id`
SET i.`inmobiliaria_id` = m.`id`
WHERE i.`inmobiliaria_id` IS NULL;

-- (opcional) después del backfill, hacer la columna obligatoria:
-- ALTER TABLE `inmuebles` MODIFY `inmobiliaria_id` BIGINT UNSIGNED NOT NULL;
