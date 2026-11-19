-- ============================================================
-- SEEDER DEMO: inmobiliarias y propiedades de ejemplo
-- Ejecutar en phpMyAdmin CON LA BASE SELECCIONADA (SELECT DATABASE();)
-- Requiere haber corrido antes los saas_*.sql (tablas y columnas).
--
-- Idempotente: se puede re-ejecutar y no duplica nada.
-- Marca de todo lo que crea: users con email '%@seed.local' e
-- inmobiliarias con slug 'demo-%'. Para DESHACER todo:
-- ejecutar seed_demo_undo.sql
--
-- Login demo: demo1@seed.local / demo1234 (igual en demo2 y demo3)
-- SIN foreign keys
-- ============================================================

-- ============================================================
-- 0) PRERREQUISITOS MÍNIMOS (idempotente): asegura las columnas
--    que usa este seeder, por si todavía no corriste los saas_*.sql.
--    Si ya existen, no hace nada.
-- ============================================================
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='users' AND COLUMN_NAME='telefono');
SET @ddl := IF(@c=0, 'ALTER TABLE `users` ADD COLUMN `telefono` VARCHAR(30) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='inmobiliaria_id');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `inmobiliaria_id` BIGINT UNSIGNED NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='tipo_propiedad');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `tipo_propiedad` VARCHAR(30) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='moneda');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `moneda` VARCHAR(3) NULL DEFAULT 'ARS'', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='ambientes');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `ambientes` INT NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='cocheras');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `cocheras` INT NOT NULL DEFAULT 0', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='expensas');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `expensas` DECIMAL(10,2) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='orientacion');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `orientacion` VARCHAR(20) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='estado_propiedad');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `estado_propiedad` VARCHAR(30) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='publicado');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `publicado` TINYINT(1) NOT NULL DEFAULT 1', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='visitas');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `visitas` INT UNSIGNED NOT NULL DEFAULT 0', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmuebles' AND COLUMN_NAME='declaracion_propietario');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmuebles` ADD COLUMN `declaracion_propietario` TINYINT(1) NOT NULL DEFAULT 0', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='zonas_cobertura');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `zonas_cobertura` VARCHAR(255) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='lat');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `lat` DECIMAL(10,8) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='lng');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `lng` DECIMAL(10,8) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='imagen_portada');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `imagen_portada` VARCHAR(255) NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inmobiliarias' AND COLUMN_NAME='galeria');
SET @ddl := IF(@c=0, 'ALTER TABLE `inmobiliarias` ADD COLUMN `galeria` TEXT NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- ============================================================
-- 0b) TABLA busquedas_guardadas y columnas de contactos (si no
--     corriste saas_alertas.sql / saas_multi_inmobiliaria.sql)
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

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='contactos' AND COLUMN_NAME='inmobiliaria_id');
SET @ddl := IF(@c=0, 'ALTER TABLE `contactos` ADD COLUMN `inmobiliaria_id` BIGINT UNSIGNED NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='contactos' AND COLUMN_NAME='inmueble_id');
SET @ddl := IF(@c=0, 'ALTER TABLE `contactos` ADD COLUMN `inmueble_id` BIGINT UNSIGNED NULL DEFAULT NULL', 'SELECT ''ok'' AS msg');
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- ============================================================
-- 1) USUARIOS DEMO (password hasheada = demo1234)
-- ============================================================
INSERT INTO `users` (`name`,`email`,`email_verified_at`,`password`,`telefono`,`created_at`,`updated_at`)
SELECT 'Inmobiliaria Demo Centro','demo1@seed.local',NOW(),'$2b$12$liQMpSIDz1bYMiMp6VARj.gcTF3DB.nmTCu3I7x6hPIylZgPMr4rS','2364-111111',NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM `users` WHERE `email`='demo1@seed.local');

INSERT INTO `users` (`name`,`email`,`email_verified_at`,`password`,`telefono`,`created_at`,`updated_at`)
SELECT 'Demo Propiedades Norte','demo2@seed.local',NOW(),'$2b$12$liQMpSIDz1bYMiMp6VARj.gcTF3DB.nmTCu3I7x6hPIylZgPMr4rS','2364-222222',NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM `users` WHERE `email`='demo2@seed.local');

INSERT INTO `users` (`name`,`email`,`email_verified_at`,`password`,`telefono`,`created_at`,`updated_at`)
SELECT 'Juan Demo (particular)','demo3@seed.local',NOW(),'$2b$12$liQMpSIDz1bYMiMp6VARj.gcTF3DB.nmTCu3I7x6hPIylZgPMr4rS','2364-333333',NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM `users` WHERE `email`='demo3@seed.local');

-- ============================================================
-- 2) ROLES DE LOS DEMO (inmobiliaria / particular)
-- ============================================================
INSERT INTO `model_has_roles` (`role_id`,`model_type`,`model_id`)
SELECT r.`id`,'App\\Models\\User',u.`id`
FROM `roles` r JOIN `users` u ON u.`email` IN ('demo1@seed.local','demo2@seed.local')
WHERE r.`name`='inmobiliaria' AND r.`guard_name`='web'
AND NOT EXISTS (SELECT 1 FROM `model_has_roles` mr WHERE mr.`role_id`=r.`id` AND mr.`model_type`='App\\Models\\User' AND mr.`model_id`=u.`id`);

INSERT INTO `model_has_roles` (`role_id`,`model_type`,`model_id`)
SELECT r.`id`,'App\\Models\\User',u.`id`
FROM `roles` r JOIN `users` u ON u.`email`='demo3@seed.local'
WHERE r.`name`='particular' AND r.`guard_name`='web'
AND NOT EXISTS (SELECT 1 FROM `model_has_roles` mr WHERE mr.`role_id`=r.`id` AND mr.`model_type`='App\\Models\\User' AND mr.`model_id`=u.`id`);

-- ============================================================
-- 3) INMOBILIARIAS DEMO (slug demo-%, estado activa)
-- ============================================================
INSERT INTO `inmobiliarias`
  (`usuario_id`,`plan_id`,`nombre_comercial`,`slug`,`descripcion`,`direccion`,`ciudad`,`provincia`,`codigo_postal`,`telefono`,`whatsapp`,`email`,`color_tema`,`estado`,`zonas_cobertura`,`lat`,`lng`,`created_at`,`updated_at`)
SELECT u.`id`, p.`id`, 'Inmobiliaria Demo Centro','demo-central',
  'Inmobiliaria de ejemplo del seeder demo. Atención personalizada en Chivilcoy y alrededores.',
  'Av. Sarmiento 123','Chivilcoy','Buenos Aires','6700','2364-111111','5492364111111','demo1@seed.local','#6366f1','activa','Chivilcoy, Bragado, Alberti',-34.9716,-60.0164,NOW(),NOW()
FROM `users` u JOIN `planes` p ON p.`slug`='basico'
WHERE u.`email`='demo1@seed.local'
AND NOT EXISTS (SELECT 1 FROM `inmobiliarias` WHERE `slug`='demo-central');

INSERT INTO `inmobiliarias`
  (`usuario_id`,`plan_id`,`nombre_comercial`,`slug`,`descripcion`,`direccion`,`ciudad`,`provincia`,`codigo_postal`,`telefono`,`whatsapp`,`email`,`color_tema`,`estado`,`zonas_cobertura`,`lat`,`lng`,`created_at`,`updated_at`)
SELECT u.`id`, p.`id`, 'Demo Propiedades Norte','demo-norte',
  'Inmobiliaria demo de la zona norte (Bragado y alrededores).',
  'Rivadavia 456','Bragado','Buenos Aires','6740','2364-222222','5492364222222','demo2@seed.local','#0ea5e9','activa','Bragado, 25 de Mayo',-35.1189,-60.4914,NOW(),NOW()
FROM `users` u JOIN `planes` p ON p.`slug`='gratis'
WHERE u.`email`='demo2@seed.local'
AND NOT EXISTS (SELECT 1 FROM `inmobiliarias` WHERE `slug`='demo-norte');

INSERT INTO `inmobiliarias`
  (`usuario_id`,`plan_id`,`nombre_comercial`,`slug`,`descripcion`,`direccion`,`ciudad`,`provincia`,`codigo_postal`,`telefono`,`whatsapp`,`email`,`color_tema`,`estado`,`zonas_cobertura`,`lat`,`lng`,`created_at`,`updated_at`)
SELECT u.`id`, p.`id`, 'Juan Demo (particular)','demo-juan',
  'Perfil de particular demo: publica su propia vivienda.',
  'Calle 25 N° 340','Chivilcoy','Buenos Aires','6700','2364-333333','5492364333333','demo3@seed.local','#16a34a','activa','Chivilcoy',-34.9716,-60.0164,NOW(),NOW()
FROM `users` u JOIN `planes` p ON p.`slug`='particular'
WHERE u.`email`='demo3@seed.local'
AND NOT EXISTS (SELECT 1 FROM `inmobiliarias` WHERE `slug`='demo-juan');

-- ============================================================
-- 4) PROPIEDADES DEMO (fotos = imágenes demo ya existentes en public/images/fotosinmuebles)
-- ============================================================
INSERT INTO `inmuebles`
  (`usuario_id`,`inmobiliaria_id`,`tipo_operacion`,`tipo_propiedad`,`moneda`,`titulo`,`descripcion`,`precio`,`superficie_cubierta`,`habitaciones`,`ambientes`,`banos`,`cocheras`,`expensas`,`anio_construccion`,`orientacion`,`estado_propiedad`,`direccion`,`ciudad`,`estado`,`codigo_postal`,`coordenadas`,`piscina`,`jardin`,`garaje`,`ascensor`,`fotos`,`destacado`,`publicado`,`visitas`,`declaracion_propietario`,`created_at`,`updated_at`)
SELECT u.`id`, im.`id`, 'venta','departamento','ARS',
  'Depto 3 ambientes en el centro de Chivilcoy',
  'Living-comedor amplio, cocina separada, 2 dormitorios y baño. A metros de la plaza principal. Ideal para vivir o invertir.',
  85000000, 78, 3, 4, 1, 1, 52000, 2005, 'Norte', 'usado',
  'Av. Sarmiento 450','Chivilcoy','Buenos Aires','6700','-34.9710,-60.0150',
  0,0,0,1,
  '["1_pexels-expect-best-79873-323780.jpg","1_pexels-eziz-1475938.jpg"]',
  1,1,132,1,NOW(),NOW()
FROM `users` u, `inmobiliarias` im
WHERE u.`email`='demo1@seed.local' AND im.`slug`='demo-central'
AND NOT EXISTS (SELECT 1 FROM `inmuebles` i WHERE i.`titulo`='Depto 3 ambientes en el centro de Chivilcoy' AND i.`inmobiliaria_id`=im.`id`);

INSERT INTO `inmuebles`
  (`usuario_id`,`inmobiliaria_id`,`tipo_operacion`,`tipo_propiedad`,`moneda`,`titulo`,`descripcion`,`precio`,`superficie_cubierta`,`habitaciones`,`ambientes`,`banos`,`cocheras`,`expensas`,`anio_construccion`,`orientacion`,`estado_propiedad`,`direccion`,`ciudad`,`estado`,`codigo_postal`,`coordenadas`,`piscina`,`jardin`,`garaje`,`ascensor`,`fotos`,`destacado`,`publicado`,`visitas`,`declaracion_propietario`,`created_at`,`updated_at`)
SELECT u.`id`, im.`id`, 'venta','casa','ARS',
  'Casa con pileta en Barrio Los Pinos',
  'Casa de 4 ambientes con patio, pileta y quincho. Garage para 2 autos. Excelente estado.',
  120000000, 220, 4, 6, 2, 2, NULL, 1998, 'Noreste', 'usado',
  'Los Pinos 890','Chivilcoy','Buenos Aires','6700','-34.9850,-60.0300',
  1,1,1,0,
  '["1_pexels-dave-frisch-731685-1575939.jpg","1_pexels-dropshado-2251247.jpg"]',
  1,1,98,1,NOW(),NOW()
FROM `users` u, `inmobiliarias` im
WHERE u.`email`='demo1@seed.local' AND im.`slug`='demo-central'
AND NOT EXISTS (SELECT 1 FROM `inmuebles` i WHERE i.`titulo`='Casa con pileta en Barrio Los Pinos' AND i.`inmobiliaria_id`=im.`id`);

INSERT INTO `inmuebles`
  (`usuario_id`,`inmobiliaria_id`,`tipo_operacion`,`tipo_propiedad`,`moneda`,`titulo`,`descripcion`,`precio`,`superficie_cubierta`,`habitaciones`,`ambientes`,`banos`,`cocheras`,`expensas`,`anio_construccion`,`orientacion`,`estado_propiedad`,`direccion`,`ciudad`,`estado`,`codigo_postal`,`coordenadas`,`piscina`,`jardin`,`garaje`,`ascensor`,`fotos`,`destacado`,`publicado`,`visitas`,`declaracion_propietario`,`created_at`,`updated_at`)
SELECT u.`id`, im.`id`, 'alquiler','departamento','ARS',
  'Depto 2 ambientes céntrico (alquiler)',
  'Departamento luminoso de 2 ambientes con balcón. Edificio con ascensor y seguridad 24 hs.',
  350000, 55, 2, 3, 1, 0, 45000, 2012, 'Sur', 'a_estrenar',
  'Mitre 210','Chivilcoy','Buenos Aires','6700','-34.9722,-60.0170',
  0,0,0,1,
  '["1_pexels-felix-lauster-343083-987550.jpg"]',
  1,1,210,1,NOW(),NOW()
FROM `users` u, `inmobiliarias` im
WHERE u.`email`='demo1@seed.local' AND im.`slug`='demo-central'
AND NOT EXISTS (SELECT 1 FROM `inmuebles` i WHERE i.`titulo`='Depto 2 ambientes céntrico (alquiler)' AND i.`inmobiliaria_id`=im.`id`);

INSERT INTO `inmuebles`
  (`usuario_id`,`inmobiliaria_id`,`tipo_operacion`,`tipo_propiedad`,`moneda`,`titulo`,`descripcion`,`precio`,`superficie_cubierta`,`habitaciones`,`ambientes`,`banos`,`cocheras`,`expensas`,`anio_construccion`,`orientacion`,`estado_propiedad`,`direccion`,`ciudad`,`estado`,`codigo_postal`,`coordenadas`,`piscina`,`jardin`,`garaje`,`ascensor`,`fotos`,`destacado`,`publicado`,`visitas`,`declaracion_propietario`,`created_at`,`updated_at`)
SELECT u.`id`, im.`id`, 'venta','terreno','USD',
  'Terreno 450 m2 en zona norte de Chivilcoy',
  'Terreno plano con todos los servicios. Ideal para construir casa o local. Escriturado.',
  45000, 450, 0, 0, 0, 0, NULL, NULL, NULL, NULL,
  'Ruta 5 km 3','Chivilcoy','Buenos Aires','6700','-34.9600,-60.0100',
  0,0,0,0,
  '["2_pexels-heyho-7535019.jpg"]',
  0,1,41,1,NOW(),NOW()
FROM `users` u, `inmobiliarias` im
WHERE u.`email`='demo1@seed.local' AND im.`slug`='demo-central'
AND NOT EXISTS (SELECT 1 FROM `inmuebles` i WHERE i.`titulo`='Terreno 450 m2 en zona norte de Chivilcoy' AND i.`inmobiliaria_id`=im.`id`);

INSERT INTO `inmuebles`
  (`usuario_id`,`inmobiliaria_id`,`tipo_operacion`,`tipo_propiedad`,`moneda`,`titulo`,`descripcion`,`precio`,`superficie_cubierta`,`habitaciones`,`ambientes`,`banos`,`cocheras`,`expensas`,`anio_construccion`,`orientacion`,`estado_propiedad`,`direccion`,`ciudad`,`estado`,`codigo_postal`,`coordenadas`,`piscina`,`jardin`,`garaje`,`ascensor`,`fotos`,`destacado`,`publicado`,`visitas`,`declaracion_propietario`,`created_at`,`updated_at`)
SELECT u.`id`, im.`id`, 'venta','casa','ARS',
  'Casa en Bragado con patio trasero',
  'Casa de 3 ambientes a 4 cuadras del centro de Bragado. Patio con parrilla.',
  68000000, 110, 3, 4, 1, 0, NULL, 1990, 'Oeste', 'usado',
  'Rivadavia 456','Bragado','Buenos Aires','6740','-35.1180,-60.4900',
  0,1,0,0,
  '["2_pexels-grizzlybear-1101146.jpg","2_pexels-frans-van-heerden-201846-1438834.jpg"]',
  1,1,77,1,NOW(),NOW()
FROM `users` u, `inmobiliarias` im
WHERE u.`email`='demo2@seed.local' AND im.`slug`='demo-norte'
AND NOT EXISTS (SELECT 1 FROM `inmuebles` i WHERE i.`titulo`='Casa en Bragado con patio trasero' AND i.`inmobiliaria_id`=im.`id`);

INSERT INTO `inmuebles`
  (`usuario_id`,`inmobiliaria_id`,`tipo_operacion`,`tipo_propiedad`,`moneda`,`titulo`,`descripcion`,`precio`,`superficie_cubierta`,`habitaciones`,`ambientes`,`banos`,`cocheras`,`expensas`,`anio_construccion`,`orientacion`,`estado_propiedad`,`direccion`,`ciudad`,`estado`,`codigo_postal`,`coordenadas`,`piscina`,`jardin`,`garaje`,`ascensor`,`fotos`,`destacado`,`publicado`,`visitas`,`declaracion_propietario`,`created_at`,`updated_at`)
SELECT u.`id`, im.`id`, 'alquiler','ph','ARS',
  'PH 3 ambientes en el centro de Bragado',
  'PH reciclado de 3 ambientes, contrafrente, con patio propio. Ideal para familia chica.',
  280000, 80, 2, 3, 2, 0, 30000, 2008, 'Este', 'usado',
  'San Martín 780','Bragado','Buenos Aires','6740','-35.1200,-60.4920',
  0,0,0,0,
  '["3_pexels-fotoaibe-1571460.jpg"]',
  0,1,64,1,NOW(),NOW()
FROM `users` u, `inmobiliarias` im
WHERE u.`email`='demo2@seed.local' AND im.`slug`='demo-norte'
AND NOT EXISTS (SELECT 1 FROM `inmuebles` i WHERE i.`titulo`='PH 3 ambientes en el centro de Bragado' AND i.`inmobiliaria_id`=im.`id`);

INSERT INTO `inmuebles`
  (`usuario_id`,`inmobiliaria_id`,`tipo_operacion`,`tipo_propiedad`,`moneda`,`titulo`,`descripcion`,`precio`,`superficie_cubierta`,`habitaciones`,`ambientes`,`banos`,`cocheras`,`expensas`,`anio_construccion`,`orientacion`,`estado_propiedad`,`direccion`,`ciudad`,`estado`,`codigo_postal`,`coordenadas`,`piscina`,`jardin`,`garaje`,`ascensor`,`fotos`,`destacado`,`publicado`,`visitas`,`declaracion_propietario`,`created_at`,`updated_at`)
SELECT u.`id`, im.`id`, 'venta','departamento','USD',
  'Dueño vende depto 2 ambientes en USD',
  'Vendo mi departamento de 2 ambientes, 6to piso con ascensor. Acepto permuta por casa.',
  95000, 60, 2, 3, 1, 0, 60000, 2015, 'Norte', 'a_estrenar',
  'Calle 25 N° 340','Chivilcoy','Buenos Aires','6700','-34.9730,-60.0190',
  0,0,0,1,
  '["3_pexels-fotios-photos-2816323.jpg","2_pexels-heyho-7587474.jpg"]',
  0,1,156,1,NOW(),NOW()
FROM `users` u, `inmobiliarias` im
WHERE u.`email`='demo3@seed.local' AND im.`slug`='demo-juan'
AND NOT EXISTS (SELECT 1 FROM `inmuebles` i WHERE i.`titulo`='Dueño vende depto 2 ambientes en USD' AND i.`inmobiliaria_id`=im.`id`);

INSERT INTO `inmuebles`
  (`usuario_id`,`inmobiliaria_id`,`tipo_operacion`,`tipo_propiedad`,`moneda`,`titulo`,`descripcion`,`precio`,`superficie_cubierta`,`habitaciones`,`ambientes`,`banos`,`cocheras`,`expensas`,`anio_construccion`,`orientacion`,`estado_propiedad`,`direccion`,`ciudad`,`estado`,`codigo_postal`,`coordenadas`,`piscina`,`jardin`,`garaje`,`ascensor`,`fotos`,`destacado`,`publicado`,`visitas`,`declaracion_propietario`,`created_at`,`updated_at`)
SELECT u.`id`, im.`id`, 'alquiler','casa','ARS',
  'Alquiler casa familiar 4 ambientes',
  'Casa de 4 ambientes con garage, patio y lavadero. Contrato anual con garantía propietaria.',
  420000, 140, 3, 5, 2, 1, NULL, 1985, 'Suroeste', 'usado',
  'Belgrano 1020','Chivilcoy','Buenos Aires','6700','-34.9760,-60.0220',
  0,1,1,0,
  '["2_pexels-heyho-7587861.jpg"]',
  0,1,88,1,NOW(),NOW()
FROM `users` u, `inmobiliarias` im
WHERE u.`email`='demo3@seed.local' AND im.`slug`='demo-juan'
AND NOT EXISTS (SELECT 1 FROM `inmuebles` i WHERE i.`titulo`='Alquiler casa familiar 4 ambientes' AND i.`inmobiliaria_id`=im.`id`);

INSERT INTO `inmuebles`
  (`usuario_id`,`inmobiliaria_id`,`tipo_operacion`,`tipo_propiedad`,`moneda`,`titulo`,`descripcion`,`precio`,`superficie_cubierta`,`habitaciones`,`ambientes`,`banos`,`cocheras`,`expensas`,`anio_construccion`,`orientacion`,`estado_propiedad`,`direccion`,`ciudad`,`estado`,`codigo_postal`,`coordenadas`,`piscina`,`jardin`,`garaje`,`ascensor`,`fotos`,`destacado`,`publicado`,`visitas`,`declaracion_propietario`,`created_at`,`updated_at`)
SELECT u.`id`, im.`id`, 'venta','local','ARS',
  'Local comercial sobre Av. Sarmiento',
  'Local de 90 m2 con vidriera amplia y depósito. Ideal comercio o consultorio.',
  150000000, 90, 0, 2, 1, 0, 80000, 1975, 'Norte', 'usado',
  'Av. Sarmiento 610','Chivilcoy','Buenos Aires','6700','-34.9715,-60.0160',
  0,0,0,0,
  '["1_pexels-expect-best-79873-323780.jpg"]',
  0,1,33,1,NOW(),NOW()
FROM `users` u, `inmobiliarias` im
WHERE u.`email`='demo1@seed.local' AND im.`slug`='demo-central'
AND NOT EXISTS (SELECT 1 FROM `inmuebles` i WHERE i.`titulo`='Local comercial sobre Av. Sarmiento' AND i.`inmobiliaria_id`=im.`id`);

-- ============================================================
-- 5) DATOS DE ACTIVIDAD DEMO: consultas (contactos) y una búsqueda guardada
-- ============================================================
INSERT INTO `contactos` (`nombre`,`email`,`telefono`,`mensaje`,`inmobiliaria_id`,`inmueble_id`)
SELECT 'Cliente Demo Uno','cliente1@seed.local','2364-999001',
  'Hola, me interesa el depto del centro. ¿Sigue disponible? Gracias.',
  im.`id`, i.`id`
FROM `inmobiliarias` im JOIN `inmuebles` i ON i.`inmobiliaria_id`=im.`id`
WHERE im.`slug`='demo-central' AND i.`titulo`='Depto 3 ambientes en el centro de Chivilcoy'
AND NOT EXISTS (SELECT 1 FROM `contactos` c WHERE c.`email`='cliente1@seed.local' AND c.`inmueble_id`=i.`id`);

INSERT INTO `contactos` (`nombre`,`email`,`telefono`,`mensaje`,`inmobiliaria_id`,`inmueble_id`)
SELECT 'Cliente Demo Dos','cliente2@seed.local','2364-999002',
  'Consulta por la casa con pileta: ¿aceptan permuta?',
  im.`id`, i.`id`
FROM `inmobiliarias` im JOIN `inmuebles` i ON i.`inmobiliaria_id`=im.`id`
WHERE im.`slug`='demo-central' AND i.`titulo`='Casa con pileta en Barrio Los Pinos'
AND NOT EXISTS (SELECT 1 FROM `contactos` c WHERE c.`email`='cliente2@seed.local' AND c.`inmueble_id`=i.`id`);

INSERT INTO `busquedas_guardadas` (`user_id`,`nombre`,`email`,`filtros`,`activa`,`created_at`,`updated_at`)
SELECT NULL,'Visitante Demo','alerta1@seed.local','{"tipo_operacion":"venta","ciudad":"Chivilcoy","precio_max":"200000000"}',1,NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM `busquedas_guardadas` WHERE `email`='alerta1@seed.local');

SELECT 'Seeder demo aplicado (idempotente). Para deshacer: seed_demo_undo.sql' AS msg;
