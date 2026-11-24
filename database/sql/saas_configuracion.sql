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
