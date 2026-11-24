-- ============================================================
-- instalar_todo.sql — UN SOLO archivo para dejar la BD al día
-- ============================================================
-- ¿Qué es? La concatenación, en orden de dependencias, de todos los
-- scripts saas_*.sql del SaaS multi-inmobiliaria.
--
-- ¿Cómo se usa? En phpMyAdmin, entrá a la base `arumasof_inmobiliariaxyz`
-- (verificá arriba que diga esa base, o corré: SELECT DATABASE();),
-- pestaña "SQL" y pegá TODO este archivo, o importalo como archivo.
--
-- Es 100% idempotente: cada bloque chequea information_schema antes de
-- crear/altera, así que se puede correr entero aunque ya tengas parte
-- de los cambios. NO usa foreign keys (compatibilidad hosting compartido).
-- No toca datos de usuarios ni propiedades existentes (solo agrega
-- columnas/tablas y backfills de defaults seguros).
--
-- Opcional aparte: seed_demo.sql (datos de prueba demo-*) y
-- seed_demo_undo.sql (los borra). NO están incluidos acá.

-- ============================================================
-- saas_permisos_roles.sql
-- ============================================================
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

-- ============================================================
-- saas_multi_inmobiliaria.sql
-- ============================================================
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

-- ============================================================
-- saas_fix_users.sql
-- ============================================================
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

-- ============================================================
-- saas_boletos.sql
-- ============================================================
-- ============================================================
-- Boletos de suscripción (Mercado Pago) - Ejecutar en phpMyAdmin
-- Idempotente y SIN foreign keys (hosting compartido)
-- ============================================================
CREATE TABLE IF NOT EXISTS `boletos` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `inmobiliaria_id` BIGINT UNSIGNED NOT NULL,
  `plan_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `concepto` VARCHAR(255) NOT NULL,
  `monto` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `estado` ENUM('pendiente','pagado','vencido','cancelado') NOT NULL DEFAULT 'pendiente',
  `vence_at` DATE NULL DEFAULT NULL,
  `mp_preference_id` VARCHAR(100) NULL DEFAULT NULL,
  `mp_payment_id` VARCHAR(50) NULL DEFAULT NULL,
  `pagado_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  KEY `idx_boletos_inmobiliaria` (`inmobiliaria_id`),
  KEY `idx_boletos_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- saas_enriquecimiento.sql
-- ============================================================
-- ============================================================
-- Enriquecimiento: campos descriptivos de inmueble (estilo portales AR)
-- + cobertura/zona y branding (portada/galería) de la inmobiliaria
-- Ejecutar en phpMyAdmin CON LA BASE SELECCIONADA (SELECT DATABASE();)
-- Idempotente, SIN foreign keys
-- ============================================================

-- Inmuebles: campos descriptivos tipo Zonaprop/Argenprop
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='tipo_propiedad');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `tipo_propiedad` VARCHAR(30) NULL DEFAULT NULL AFTER `tipo_operacion`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='moneda');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `moneda` VARCHAR(3) NULL DEFAULT ''ARS'' AFTER `precio`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='ambientes');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `ambientes` INT NULL DEFAULT NULL AFTER `habitaciones`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='cocheras');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `cocheras` INT NOT NULL DEFAULT 0 AFTER `banos`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='expensas');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `expensas` DECIMAL(10,2) NULL DEFAULT NULL AFTER `cocheras`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='orientacion');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `orientacion` VARCHAR(20) NULL DEFAULT NULL AFTER `expensas`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='estado_propiedad');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `estado_propiedad` VARCHAR(30) NULL DEFAULT NULL AFTER `orientacion`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- Inmobiliarias: cobertura geográfica + ubicación de la oficina + branding
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='zonas_cobertura');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `zonas_cobertura` VARCHAR(255) NULL DEFAULT NULL AFTER `provincia`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='lat');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `lat` DECIMAL(10,8) NULL DEFAULT NULL AFTER `zonas_cobertura`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='lng');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `lng` DECIMAL(10,8) NULL DEFAULT NULL AFTER `lat`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='imagen_portada');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `imagen_portada` VARCHAR(255) NULL DEFAULT NULL AFTER `logo`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='galeria');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `galeria` TEXT NULL DEFAULT NULL AFTER `imagen_portada`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- ============================================================
-- saas_cierres.sql
-- ============================================================
-- ============================================================
-- Cierre de operaciones (vendida/alquilada) en inmuebles
-- Ejecutar en phpMyAdmin CON LA BASE SELECCIONADA (SELECT DATABASE();)
-- Idempotente, SIN foreign keys
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='cerrada');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `cerrada` TINYINT(1) NOT NULL DEFAULT 0 AFTER `publicado`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='tipo_cierre');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `tipo_cierre` VARCHAR(20) NULL DEFAULT NULL AFTER `cerrada`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='cerrada_at');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `cerrada_at` TIMESTAMP NULL DEFAULT NULL AFTER `tipo_cierre`', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- ============================================================
-- saas_alertas.sql
-- ============================================================
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

-- ============================================================
-- saas_comisiones.sql
-- ============================================================
-- ============================================================
-- Comisión opcional por cierre de operación (vendida/alquilada)
-- Ejecutar en phpMyAdmin CON LA BASE SELECCIONADA (SELECT DATABASE();)
-- Idempotente, SIN foreign keys
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='comision_porcentaje');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `comision_porcentaje` DECIMAL(5,2) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='comision_monto');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `comision_monto` DECIMAL(14,2) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='comision_estado');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `comision_estado` VARCHAR(20) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='comision_cobrada_at');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `comision_cobrada_at` TIMESTAMP NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- ============================================================
-- saas_asistida.sql
-- ============================================================
-- ============================================================
-- Carga asistida: marca las propiedades cargadas por el admin
-- a nombre de una inmobiliaria (servicio de arranque del portal)
-- Ejecutar en phpMyAdmin CON LA BASE SELECCIONADA (SELECT DATABASE();)
-- Idempotente, SIN foreign keys
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='carga_asistida');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `carga_asistida` TINYINT(1) NOT NULL DEFAULT 0', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- ============================================================
-- saas_particulares.sql
-- ============================================================
-- ============================================================
-- Rol "particular" (dueño que publica su propia vivienda, sin ser inmobiliaria)
-- Ejecutar en phpMyAdmin CON LA BASE SELECCIONADA (SELECT DATABASE();)
-- Idempotente, SIN foreign keys
-- ============================================================

SET @c := (SELECT COUNT(*) FROM `roles` WHERE `name` = 'particular' AND `guard_name` = 'web');
SET @ddl := IF(@c=0, 'INSERT INTO `roles` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES (''particular'', ''web'', NOW(), NOW())', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- ============================================================
-- saas_plan_particular.sql
-- ============================================================
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

-- ============================================================
-- saas_declaracion.sql
-- ============================================================
-- ============================================================
-- Declaración de propietario/autorización en cada publicación
-- (protección legal: el anunciante declara ser dueño o estar autorizado)
-- Ejecutar en phpMyAdmin CON LA BASE SELECCIONADA (SELECT DATABASE();)
-- Idempotente, SIN foreign keys
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='declaracion_propietario');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `declaracion_propietario` TINYINT(1) NOT NULL DEFAULT 0', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- ============================================================
-- saas_personalizacion.sql
-- ============================================================
-- ============================================================
-- Personalización avanzada (optativa) del perfil público de la inmobiliaria
-- Ejecutar en phpMyAdmin CON LA BASE SELECCIONADA (SELECT DATABASE();)
-- Idempotente, SIN foreign keys
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='tema');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `tema` VARCHAR(20) NULL DEFAULT ''moderno''', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='mensaje_destacado');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `mensaje_destacado` VARCHAR(255) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='mostrar_operaciones');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `mostrar_operaciones` TINYINT(1) NOT NULL DEFAULT 1', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='mostrar_mapa');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `mostrar_mapa` TINYINT(1) NOT NULL DEFAULT 1', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='mostrar_redes');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `mostrar_redes` TINYINT(1) NOT NULL DEFAULT 1', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- ============================================================
-- saas_solicitudes_carga.sql
-- ============================================================
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

-- ============================================================
-- saas_proximidades.sql
-- ============================================================
-- ============================================================
-- saas_proximidades.sql
-- Proximidades / ventajas estructurales del entorno del inmueble
-- (JSON: [{"nombre": "Estación de tren", "metros": 200}, ...])
-- Idempotente: se puede correr varias veces. Sin FKs (hosting compartido).
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='proximidades');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `proximidades` TEXT NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- Ejemplos demo opcionales (solo si querés poblar el seeder existente):
-- UPDATE `inmuebles` SET `proximidades` = JSON_ARRAY(JSON_OBJECT('nombre','Estación de tren','metros',250), JSON_OBJECT('nombre','Plaza','metros',120)) WHERE `id` = 1;

-- ============================================================
-- saas_entorno.sql
-- ============================================================
-- ============================================================
-- saas_entorno.sql
-- 1) uso_detalle: texto libre de uso/habilitación para comerciales
--    (kiosco, tienda de ropa, gastronomía, oficina, depósito...)
-- 2) puntaje_entorno: score 0-100 calculado con Overpass (OSM)
--    según lo que rodea al inmueble (arteria, transporte, comercio,
--    servicios) + detalle JSON + fecha de cálculo.
-- Idempotente, sin FKs (hosting compartido).
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='uso_detalle');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `uso_detalle` VARCHAR(120) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='puntaje_entorno');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `puntaje_entorno` TINYINT UNSIGNED NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='puntaje_entorno_detalle');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `puntaje_entorno_detalle` TEXT NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='puntaje_entorno_at');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `puntaje_entorno_at` TIMESTAMP NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- ============================================================
-- saas_verificacion.sql
-- ============================================================
-- ============================================================
-- saas_verificacion.sql
-- Datos de habilitación/identidad de la inmobiliaria para verificación:
--  - cuit: CUIT del responsable (AFIP)
--  - matricula_registro: matrícula en el Registro/Colegio provincial de
--    corredores inmobiliarios (CABA, PBA, etc.)
--  - verificada: la marca el superadmin tras chequear esos datos
-- Idempotente, sin FKs.
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='cuit');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `cuit` VARCHAR(20) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='matricula_registro');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `matricula_registro` VARCHAR(80) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='verificada');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `verificada` TINYINT(1) NOT NULL DEFAULT 0', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- ============================================================
-- saas_seo_inmobiliarias.sql
-- ============================================================
-- ============================================================
-- saas_seo_inmobiliarias.sql
-- SEO personalizado por inmobiliaria (feature Premium):
--  - meta_titulo: título de la página /inmobiliaria/{slug} (máx 70 chars recomendado)
--  - meta_descripcion: meta description de esa página (máx 160-200 chars)
-- Si están vacíos (o el plan no es Premium), el sistema genera el título y
-- la descripción automáticamente con los datos del perfil.
-- Idempotente, sin FKs.
-- ============================================================

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='meta_titulo');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `meta_titulo` VARCHAR(70) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='meta_descripcion');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `meta_descripcion` VARCHAR(200) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- ============================================================
-- saas_mensajes.sql
-- ============================================================
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

-- ============================================================
-- saas_email_verificado.sql
-- ============================================================
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

-- ============================================================
-- saas_planes_escalonados.sql
-- ============================================================
-- ============================================================
-- saas_planes_escalonados.sql
-- Escalera de planes: el monto mensual escala con la cantidad de
-- publicaciones (5 → 15 → 50 → 150 → ilimitado), para que no sea
-- agresivo con inmobiliarias chicas.
-- Idempotente: upsert por slug (planes.slug es UNIQUE), sin FKs.
-- No toca inmobiliarias existentes (conservan su plan_id).
-- ============================================================

INSERT INTO `planes` (`nombre`,`slug`,`precio_mensual`,`limite_inmuebles`,`limite_destacados`,`limite_fotos`,`activo`,`created_at`,`updated_at`) VALUES
('Gratis','gratis',0.00,5,1,5,1,NOW(),NOW()),
('Inicial','inicial',4900.00,15,2,10,1,NOW(),NOW()),
('Basico','basico',9900.00,50,3,20,1,NOW(),NOW()),
('Profesional','profesional',14900.00,150,10,30,1,NOW(),NOW()),
('Premium','premium',19900.00,999,10,50,1,NOW(),NOW())
ON DUPLICATE KEY UPDATE `nombre`=VALUES(`nombre`), `precio_mensual`=VALUES(`precio_mensual`),
  `limite_inmuebles`=VALUES(`limite_inmuebles`), `limite_destacados`=VALUES(`limite_destacados`),
  `limite_fotos`=VALUES(`limite_fotos`), `activo`=VALUES(`activo`), `updated_at`=NOW();

-- ============================================================
-- saas_configuracion.sql
-- ============================================================
-- ============================================================
-- saas_configuracion.sql
-- ============================================================
-- Tabla clave/valor para la configuración global del portal SaaS
-- (hoy: comisión por operación cerrada). La administra el superadmin
-- desde /admin/configuracion.
-- Ejecutar en phpMyAdmin CON LA BASE DEL PROYECTO SELECCIONADA
-- (verificar antes con: SELECT DATABASE();  -> debe ser arumasof_inmobiliariaxyz)
-- Idempotente y SIN foreign keys (hosting compartido)
-- ============================================================

CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `clave` VARCHAR(100) NOT NULL,
  `valor` TEXT NULL,
  `descripcion` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `configuracion_clave_unique` (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seeds: upsert por clave UNIQUE. OJO: solo se actualiza `descripcion` para no
-- pisar el valor que el superadmin haya cambiado desde el panel.
INSERT INTO `configuracion` (`clave`, `valor`, `descripcion`, `created_at`, `updated_at`) VALUES
('comision_habilitada', '1',
 'Si vale 1, al cerrar una operacion se registra la comision del portal (porcentaje por defecto o monto fijo) y queda pendiente de cobro hasta que el admin la marca cobrada en /admin/operaciones',
 NOW(), NOW()),
('comision_pct_default', '3.00',
 'Porcentaje de comision por defecto del portal sobre el precio declarado al cerrar una operacion (0-100). Se precarga en el modal de cierre.',
 NOW(), NOW()),
('comision_nota', 'En locaciones residenciales la comision corresponde al locador, no al inquilino (Ley 27.551). El porcentaje se calcula sobre el precio declarado de la operacion.',
 'Aclaracion legal que se muestra al cerrar una operacion, en la pagina legal y en la ayuda',
 NOW(), NOW())
ON DUPLICATE KEY UPDATE `descripcion` = VALUES(`descripcion`), `updated_at` = NOW();
