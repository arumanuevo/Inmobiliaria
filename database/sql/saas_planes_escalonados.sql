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
