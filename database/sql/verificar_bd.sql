-- ============================================================
-- verificar_bd.sql — chequea que la BD tenga TODO lo que la app espera
-- ============================================================
-- Ejecutar en phpMyAdmin CON LA BASE DEL PROYECTO SELECCIONADA:
--   SELECT DATABASE();   -> debe decir arumasof_inmobiliariaxyz
-- Resultados: devuelve filas SOLO con lo que FALTA.
-- Si los 4 resultados vienen VACÍOS => la BD está 100% al día.
-- Si aparece algo, corré instalar_todo.sql y volvé a correr este chequeo.
-- ============================================================

-- 1) Tablas esperadas que faltan
SELECT 'TABLA_FALTA' AS tipo, t.tabla AS nombre
FROM (
SELECT 'boletos' AS tabla UNION ALL
SELECT 'busquedas_guardadas' AS tabla UNION ALL
SELECT 'cache' AS tabla UNION ALL
SELECT 'cache_locks' AS tabla UNION ALL
SELECT 'contactos' AS tabla UNION ALL
SELECT 'failed_jobs' AS tabla UNION ALL
SELECT 'fotos' AS tabla UNION ALL
SELECT 'inmobiliarias' AS tabla UNION ALL
SELECT 'inmuebles' AS tabla UNION ALL
SELECT 'job_batches' AS tabla UNION ALL
SELECT 'jobs' AS tabla UNION ALL
SELECT 'migrations' AS tabla UNION ALL
SELECT 'model_has_permissions' AS tabla UNION ALL
SELECT 'model_has_roles' AS tabla UNION ALL
SELECT 'password_reset_tokens' AS tabla UNION ALL
SELECT 'permissions' AS tabla UNION ALL
SELECT 'planes' AS tabla UNION ALL
SELECT 'role_has_permissions' AS tabla UNION ALL
SELECT 'roles' AS tabla UNION ALL
SELECT 'sessions' AS tabla UNION ALL
SELECT 'solicitudes_carga' AS tabla UNION ALL
SELECT 'users' AS tabla
) t
LEFT JOIN information_schema.TABLES i ON i.TABLE_SCHEMA = DATABASE() AND i.TABLE_NAME = t.tabla
WHERE i.TABLE_NAME IS NULL;

-- 2) Columnas esperadas que faltan
SELECT 'COLUMNA_FALTA' AS tipo, c.tabla AS tabla, c.columna AS nombre
FROM (
SELECT 'boletos' AS tabla, 'concepto' AS columna UNION ALL
SELECT 'boletos' AS tabla, 'created_at' AS columna UNION ALL
SELECT 'boletos' AS tabla, 'estado' AS columna UNION ALL
SELECT 'boletos' AS tabla, 'id' AS columna UNION ALL
SELECT 'boletos' AS tabla, 'inmobiliaria_id' AS columna UNION ALL
SELECT 'boletos' AS tabla, 'monto' AS columna UNION ALL
SELECT 'boletos' AS tabla, 'mp_payment_id' AS columna UNION ALL
SELECT 'boletos' AS tabla, 'mp_preference_id' AS columna UNION ALL
SELECT 'boletos' AS tabla, 'pagado_at' AS columna UNION ALL
SELECT 'boletos' AS tabla, 'plan_id' AS columna UNION ALL
SELECT 'boletos' AS tabla, 'updated_at' AS columna UNION ALL
SELECT 'boletos' AS tabla, 'vence_at' AS columna UNION ALL
SELECT 'busquedas_guardadas' AS tabla, 'activa' AS columna UNION ALL
SELECT 'busquedas_guardadas' AS tabla, 'created_at' AS columna UNION ALL
SELECT 'busquedas_guardadas' AS tabla, 'email' AS columna UNION ALL
SELECT 'busquedas_guardadas' AS tabla, 'filtros' AS columna UNION ALL
SELECT 'busquedas_guardadas' AS tabla, 'id' AS columna UNION ALL
SELECT 'busquedas_guardadas' AS tabla, 'nombre' AS columna UNION ALL
SELECT 'busquedas_guardadas' AS tabla, 'updated_at' AS columna UNION ALL
SELECT 'busquedas_guardadas' AS tabla, 'user_id' AS columna UNION ALL
SELECT 'contactos' AS tabla, 'created_at' AS columna UNION ALL
SELECT 'contactos' AS tabla, 'email' AS columna UNION ALL
SELECT 'contactos' AS tabla, 'email_verified_at' AS columna UNION ALL
SELECT 'contactos' AS tabla, 'id' AS columna UNION ALL
SELECT 'contactos' AS tabla, 'inmobiliaria_id' AS columna UNION ALL
SELECT 'contactos' AS tabla, 'inmueble_id' AS columna UNION ALL
SELECT 'contactos' AS tabla, 'mensaje' AS columna UNION ALL
SELECT 'contactos' AS tabla, 'nombre' AS columna UNION ALL
SELECT 'contactos' AS tabla, 'remember_token' AS columna UNION ALL
SELECT 'contactos' AS tabla, 'telefono' AS columna UNION ALL
SELECT 'contactos' AS tabla, 'updated_at' AS columna UNION ALL
SELECT 'fotos' AS tabla, 'created_at' AS columna UNION ALL
SELECT 'fotos' AS tabla, 'descripcion' AS columna UNION ALL
SELECT 'fotos' AS tabla, 'id' AS columna UNION ALL
SELECT 'fotos' AS tabla, 'inmueble_id' AS columna UNION ALL
SELECT 'fotos' AS tabla, 'ruta' AS columna UNION ALL
SELECT 'fotos' AS tabla, 'tipo' AS columna UNION ALL
SELECT 'fotos' AS tabla, 'updated_at' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'ciudad' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'codigo_postal' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'color_tema' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'created_at' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'descripcion' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'direccion' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'email' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'estado' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'facebook' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'galeria' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'id' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'imagen_portada' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'instagram' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'lat' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'lng' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'logo' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'mensaje_destacado' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'mostrar_mapa' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'mostrar_operaciones' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'mostrar_redes' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'nombre_comercial' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'plan_id' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'plan_vence_at' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'provincia' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'sitio_web' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'slug' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'telefono' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'tema' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'updated_at' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'usuario_id' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'whatsapp' AS columna UNION ALL
SELECT 'inmobiliarias' AS tabla, 'zonas_cobertura' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'ambientes' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'anio_construccion' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'ascensor' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'banos' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'carga_asistida' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'cerrada' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'cerrada_at' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'ciudad' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'cocheras' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'codigo_postal' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'comision_cobrada_at' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'comision_estado' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'comision_monto' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'comision_porcentaje' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'coordenadas' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'created_at' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'declaracion_propietario' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'descripcion' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'destacado' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'direccion' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'estado' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'estado_propiedad' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'expensas' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'fotos' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'garaje' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'habitaciones' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'id' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'inmobiliaria_id' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'jardin' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'moneda' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'orientacion' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'piscina' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'precio' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'propdni' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'propdom' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'propemail' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'propnombre' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'proptel' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'proximidades' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'publicado' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'puntaje_entorno' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'puntaje_entorno_at' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'puntaje_entorno_detalle' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'superficie_cubierta' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'tipo_cierre' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'tipo_operacion' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'tipo_propiedad' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'titulo' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'updated_at' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'uso_detalle' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'usuario_id' AS columna UNION ALL
SELECT 'inmuebles' AS tabla, 'visitas' AS columna UNION ALL
SELECT 'model_has_permissions' AS tabla, 'model_id' AS columna UNION ALL
SELECT 'model_has_permissions' AS tabla, 'model_type' AS columna UNION ALL
SELECT 'model_has_permissions' AS tabla, 'permission_id' AS columna UNION ALL
SELECT 'model_has_roles' AS tabla, 'model_id' AS columna UNION ALL
SELECT 'model_has_roles' AS tabla, 'model_type' AS columna UNION ALL
SELECT 'model_has_roles' AS tabla, 'role_id' AS columna UNION ALL
SELECT 'permissions' AS tabla, 'created_at' AS columna UNION ALL
SELECT 'permissions' AS tabla, 'guard_name' AS columna UNION ALL
SELECT 'permissions' AS tabla, 'id' AS columna UNION ALL
SELECT 'permissions' AS tabla, 'name' AS columna UNION ALL
SELECT 'permissions' AS tabla, 'updated_at' AS columna UNION ALL
SELECT 'planes' AS tabla, 'activo' AS columna UNION ALL
SELECT 'planes' AS tabla, 'created_at' AS columna UNION ALL
SELECT 'planes' AS tabla, 'id' AS columna UNION ALL
SELECT 'planes' AS tabla, 'limite_destacados' AS columna UNION ALL
SELECT 'planes' AS tabla, 'limite_fotos' AS columna UNION ALL
SELECT 'planes' AS tabla, 'limite_inmuebles' AS columna UNION ALL
SELECT 'planes' AS tabla, 'nombre' AS columna UNION ALL
SELECT 'planes' AS tabla, 'precio_mensual' AS columna UNION ALL
SELECT 'planes' AS tabla, 'slug' AS columna UNION ALL
SELECT 'planes' AS tabla, 'updated_at' AS columna UNION ALL
SELECT 'role_has_permissions' AS tabla, 'permission_id' AS columna UNION ALL
SELECT 'role_has_permissions' AS tabla, 'role_id' AS columna UNION ALL
SELECT 'roles' AS tabla, 'created_at' AS columna UNION ALL
SELECT 'roles' AS tabla, 'guard_name' AS columna UNION ALL
SELECT 'roles' AS tabla, 'id' AS columna UNION ALL
SELECT 'roles' AS tabla, 'name' AS columna UNION ALL
SELECT 'roles' AS tabla, 'updated_at' AS columna UNION ALL
SELECT 'solicitudes_carga' AS tabla, 'atendida_at' AS columna UNION ALL
SELECT 'solicitudes_carga' AS tabla, 'created_at' AS columna UNION ALL
SELECT 'solicitudes_carga' AS tabla, 'detalle' AS columna UNION ALL
SELECT 'solicitudes_carga' AS tabla, 'estado' AS columna UNION ALL
SELECT 'solicitudes_carga' AS tabla, 'id' AS columna UNION ALL
SELECT 'solicitudes_carga' AS tabla, 'inmobiliaria_id' AS columna UNION ALL
SELECT 'solicitudes_carga' AS tabla, 'inmueble_id' AS columna UNION ALL
SELECT 'solicitudes_carga' AS tabla, 'titulo' AS columna UNION ALL
SELECT 'solicitudes_carga' AS tabla, 'updated_at' AS columna UNION ALL
SELECT 'users' AS tabla, 'created_at' AS columna UNION ALL
SELECT 'users' AS tabla, 'email' AS columna UNION ALL
SELECT 'users' AS tabla, 'id' AS columna UNION ALL
SELECT 'users' AS tabla, 'name' AS columna UNION ALL
SELECT 'users' AS tabla, 'password' AS columna UNION ALL
SELECT 'users' AS tabla, 'telefono' AS columna UNION ALL
SELECT 'users' AS tabla, 'updated_at' AS columna
) c
LEFT JOIN information_schema.COLUMNS i ON i.TABLE_SCHEMA = DATABASE() AND i.TABLE_NAME = c.tabla AND i.COLUMN_NAME = c.columna
WHERE i.COLUMN_NAME IS NULL
ORDER BY c.tabla, c.columna;

-- 3) Roles mínimos que faltan (guard web)
SELECT 'ROL_FALTA' AS tipo, v.nombre AS nombre
FROM (
  SELECT 'admin' AS nombre UNION ALL SELECT 'user' UNION ALL SELECT 'inmobiliaria' UNION ALL SELECT 'particular'
) v
LEFT JOIN `roles` r ON r.`name` = v.nombre AND r.`guard_name` = 'web'
WHERE r.`id` IS NULL;

-- 4) Planes mínimos que faltan (slug)
SELECT 'PLAN_FALTA' AS tipo, v.slug AS nombre
FROM (
  SELECT 'gratis' AS slug UNION ALL SELECT 'basico' UNION ALL SELECT 'premium' UNION ALL SELECT 'particular'
) v
LEFT JOIN `planes` p ON p.`slug` = v.slug
WHERE p.`id` IS NULL;
