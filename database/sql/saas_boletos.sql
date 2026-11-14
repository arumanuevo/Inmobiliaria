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
