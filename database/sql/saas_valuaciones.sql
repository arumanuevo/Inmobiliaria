-- ============================================================
-- saas_valuaciones.sql
-- ============================================================
-- Valuación de mercado (AVM-lite) para usuarios con plan de pago:
-- tabla `valuaciones` (historial de estimaciones hechas desde /valuar)
-- + columnas `valuacion_*` en inmuebles (última valuación guardada por
-- inmueble, usada por el botón "Valor de mercado" del panel Premium).
-- Ejecutar en phpMyAdmin CON LA BASE DEL PROYECTO SELECCIONADA
-- (verificar antes con: SELECT DATABASE();  -> debe ser arumasof_inmobiliariaxyz)
-- Idempotente y SIN foreign keys (hosting compartido)
-- ============================================================

CREATE TABLE IF NOT EXISTS `valuaciones` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` BIGINT UNSIGNED NULL,
  `inmobiliaria_id` BIGINT UNSIGNED NULL,
  `inmueble_id` BIGINT UNSIGNED NULL,
  `direccion` VARCHAR(255) NULL,
  `lat` DECIMAL(10,6) NULL,
  `lng` DECIMAL(10,6) NULL,
  `ciudad` VARCHAR(120) NULL,
  `tipo_operacion` VARCHAR(20) NULL DEFAULT 'venta',
  `tipo_propiedad` VARCHAR(40) NULL,
  `ambientes` INT NULL,
  `sup_cubierta` DECIMAL(10,2) NULL,
  `sup_total` DECIMAL(10,2) NULL,
  `valor_medio` DECIMAL(14,2) NULL,
  `valor_min` DECIMAL(14,2) NULL,
  `valor_max` DECIMAL(14,2) NULL,
  `pm2_mediana` DECIMAL(14,2) NULL,
  `comparables` TEXT NULL COMMENT 'JSON con ids y datos de los comparables usados',
  `detallada` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 si es premium (reporte/comparables elegibles)',
  `ip` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `valuaciones_ciudad_idx` (`ciudad`),
  KEY `valuaciones_created_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Última valuación guardada por inmueble (botón "Valor de mercado" del panel)
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='valuacion_valor');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `valuacion_valor` DECIMAL(14,2) NULL DEFAULT NULL AFTER `cierre_aprobado_por`', 'SELECT ''ok'' AS msg');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='valuacion_min');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `valuacion_min` DECIMAL(14,2) NULL DEFAULT NULL AFTER `valuacion_valor`', 'SELECT ''ok'' AS msg');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='valuacion_max');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `valuacion_max` DECIMAL(14,2) NULL DEFAULT NULL AFTER `valuacion_min`', 'SELECT ''ok'' AS msg');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='valuacion_at');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `valuacion_at` TIMESTAMP NULL DEFAULT NULL AFTER `valuacion_max`', 'SELECT ''ok'' AS msg');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;
