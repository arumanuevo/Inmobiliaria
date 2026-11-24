<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InmuebleController;
use App\Http\Controllers\InmobiliariaController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\FotoController;
use App\Http\Controllers\PropertySearchController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\BusquedaGuardadaController;
use App\Http\Controllers\FeedController;

Route::get('/', [InmuebleController::class, 'destacados'])->name('welcome');
Route::get('/inicio', [InmuebleController::class, 'destacados'])->name('inicio');

Auth::routes(['verify' => true]); // incluye /email/verify, /email/verify/{id}/{hash} y /email/resend

// Login con Google (OAuth2)
Route::get('/auth/google', [App\Http\Controllers\Auth\GoogleAuthController::class, 'redirect'])->name('login.google');
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\GoogleAuthController::class, 'callback'])->name('login.google.callback');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Mercado Pago: pagar plan (logueado), retorno y webhook (públicos)
Route::middleware('auth')->group(function () {
    Route::post('/mp/pagar', [App\Http\Controllers\MercadoPagoController::class, 'pagar'])->name('mp.pagar');
});
Route::get('/mp/retorno/{estado}', [App\Http\Controllers\MercadoPagoController::class, 'retorno'])->name('mp.retorno');
Route::post('/mp/webhook', [App\Http\Controllers\MercadoPagoController::class, 'webhook'])->name('mp.webhook');

// Directorio público de inmobiliarias y perfil público de cada una
Route::get('/inmobiliarias', [InmobiliariaController::class, 'index'])->name('inmobiliarias.index');
Route::get('/inmobiliaria/{slug}', [InmobiliariaController::class, 'show'])->name('inmobiliarias.show');
Route::get('/inmobiliaria/{slug}/propiedad/{inmueble}', [InmobiliariaController::class, 'propiedad'])->name('propiedad.show');
Route::get('/propiedades-en-{operacion}-en-{ciudad}', [PropertySearchController::class, 'porZona'])->name('propiedades.zona');
Route::get('/sitemap.xml', [PropertySearchController::class, 'sitemap'])->name('sitemap');
Route::get('/sumate', [InmobiliariaController::class, 'sumate'])->name('sumate');
Route::get('/aviso-legal', function () {
    return view('legal');
})->name('aviso-legal');
Route::post('/inmuebles/{inmueble}/cerrar', [InmuebleController::class, 'cerrar'])->name('inmuebles.cerrar');

// Perfil editable por la propia inmobiliaria (logueada)
Route::middleware('auth')->group(function () {
    Route::get('/mi-inmobiliaria', [InmobiliariaController::class, 'perfil'])->name('inmobiliaria.perfil');
    Route::get('/mi-inmobiliaria/preview', [InmobiliariaController::class, 'preview'])->name('inmobiliaria.preview');
    Route::put('/mi-inmobiliaria', [InmobiliariaController::class, 'actualizarPerfil'])->name('inmobiliaria.update');
    Route::get('/mis-boletos', [App\Http\Controllers\BoletoController::class, 'mios'])->name('boletos.mios');
    Route::post('/boletos/{boleto}/pagar', [App\Http\Controllers\BoletoController::class, 'pagar'])->name('boletos.pagar');
    Route::get('/mis-busquedas', [BusquedaGuardadaController::class, 'misBusquedas'])->name('busquedas.mias');
    Route::delete('/mis-busquedas/{busqueda}', [BusquedaGuardadaController::class, 'destroy'])->name('busquedas.destroy');
    Route::get('/mis-solicitudes', [App\Http\Controllers\SolicitudCargaController::class, 'mis'])->name('solicitudes.mias');
    Route::post('/mis-solicitudes', [App\Http\Controllers\SolicitudCargaController::class, 'store'])->name('solicitudes.store');
    // Bandeja de mensajes internos (avisos del administrador)
    Route::get('/mensajes', [App\Http\Controllers\MensajeController::class, 'misMensajes'])->name('mensajes.mias');
    Route::post('/mensajes/leer-todos', [App\Http\Controllers\MensajeController::class, 'marcarTodosLeidos'])->name('mensajes.leerTodos');
    Route::post('/mensajes/{mensaje}/leer', [App\Http\Controllers\MensajeController::class, 'marcarLeido'])->name('mensajes.leer');
    // Centro de ayuda del sistema (usuarios; el superadmin administra desde /admin)
    Route::get('/ayuda', [App\Http\Controllers\AyudaController::class, 'index'])->name('ayuda');
    // Valuación de mercado (SOLO planes de pago; Premium = reporte detallado)
    Route::get('/valuar', [App\Http\Controllers\ValuacionController::class, 'form'])->name('valuar');
    Route::post('/valuar', [App\Http\Controllers\ValuacionController::class, 'estimar'])->name('valuar.estimar')->middleware('throttle:30,60');
    Route::get('/valuar/reporte/{valuacion}', [App\Http\Controllers\ValuacionController::class, 'reporte'])->name('valuar.reporte');
});

// Búsquedas guardadas (público: cualquiera puede recibir alertas por email de nuevas propiedades)
Route::post('/busquedas-guardadas', [BusquedaGuardadaController::class, 'guardar'])->name('busquedas.guardar');

// Feeds XML de propiedades publicadas (para sindicar en portales inmobiliarios)
Route::get('/feed.xml', [FeedController::class, 'generico'])->name('feed.generico');
Route::get('/feed/zonaprop.xml', [FeedController::class, 'zonaprop'])->name('feed.zonaprop');
Route::get('/feed/argenprop.xml', [FeedController::class, 'argenprop'])->name('feed.argenprop');

// Panel de administración del SaaS (solo admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/inmobiliarias', [App\Http\Controllers\Admin\AdminController::class, 'inmobiliarias'])->name('inmobiliarias');
    Route::post('/inmobiliarias/{id}/estado', [App\Http\Controllers\Admin\AdminController::class, 'cambiarEstado'])->name('inmobiliarias.estado');
    Route::post('/inmobiliarias/{id}/plan', [App\Http\Controllers\Admin\AdminController::class, 'cambiarPlan'])->name('inmobiliarias.plan');
    Route::post('/inmobiliarias/{id}/verificada', [App\Http\Controllers\Admin\AdminController::class, 'cambiarVerificada'])->name('inmobiliarias.verificada');
    Route::get('/boletos', [App\Http\Controllers\Admin\AdminController::class, 'boletos'])->name('boletos');
    Route::post('/boletos', [App\Http\Controllers\Admin\AdminController::class, 'crearBoleto'])->name('boletos.store');
    Route::post('/boletos/{id}/estado', [App\Http\Controllers\Admin\AdminController::class, 'cambiarEstadoBoleto'])->name('boletos.estado');
    Route::get('/planes', [App\Http\Controllers\Admin\AdminController::class, 'planes'])->name('planes');
    Route::post('/planes', [App\Http\Controllers\Admin\AdminController::class, 'crearPlan'])->name('planes.store');
    Route::put('/planes/{id}', [App\Http\Controllers\Admin\AdminController::class, 'actualizarPlan'])->name('planes.update');
    Route::post('/planes/{id}/toggle', [App\Http\Controllers\Admin\AdminController::class, 'togglePlan'])->name('planes.toggle');
    Route::get('/operaciones', [App\Http\Controllers\Admin\AdminController::class, 'operaciones'])->name('operaciones');
    Route::post('/operaciones/{id}/comision', [App\Http\Controllers\Admin\AdminController::class, 'cobrarOperacion'])->name('operaciones.comision');
    Route::post('/operaciones/{id}/aprobar', [App\Http\Controllers\Admin\AdminController::class, 'aprobarOperacion'])->name('operaciones.aprobar');
    Route::get('/inmobiliarias/{id}/cargar-inmueble', [App\Http\Controllers\Admin\AdminController::class, 'formCargaAsistida'])->name('carga.form');
    Route::post('/inmobiliarias/{id}/cargar-inmueble', [App\Http\Controllers\Admin\AdminController::class, 'cargarInmueble'])->name('carga.store');
    Route::get('/solicitudes', [App\Http\Controllers\Admin\AdminController::class, 'solicitudes'])->name('solicitudes');
    Route::post('/solicitudes/{id}/estado', [App\Http\Controllers\Admin\AdminController::class, 'cambiarEstadoSolicitud'])->name('solicitudes.estado');
    // Avisos internos a usuarios (solo superadmin, el controller hace abort 403 si no lo es)
    Route::get('/mensajes', [App\Http\Controllers\MensajeController::class, 'adminIndex'])->name('mensajes');
    Route::post('/mensajes', [App\Http\Controllers\MensajeController::class, 'adminStore'])->name('mensajes.store');
    // Diagnóstico de envío de email (solo superadmin): envía un mail de prueba y muestra la config efectiva
    Route::get('/mail-test', [App\Http\Controllers\Admin\AdminController::class, 'mailTest'])->name('mail-test');

    // Estadísticas del valuador de mercado (AVM-lite)
    Route::get('/valuaciones', [App\Http\Controllers\Admin\AdminController::class, 'valuaciones'])->name('valuaciones');

    // Configuración global del portal (solo superadmin): comisión por operación cerrada, etc.
    Route::get('/configuracion', [App\Http\Controllers\Admin\AdminController::class, 'configuracion'])->name('configuracion');
    Route::post('/configuracion', [App\Http\Controllers\Admin\AdminController::class, 'guardarConfiguracion'])->name('configuracion.store');
});

// Rutas para el CRUD de Inmuebles (admin e inmobiliarias; cada una solo gestiona lo suyo)
Route::resource('inmuebles', InmuebleController::class);
Route::get('inmuebles/{inmueble}/add-photos', [InmuebleController::class, 'addPhotos'])->name('inmuebles.addPhotos');
Route::post('inmuebles/{id}/store-photos', [InmuebleController::class, 'storePhotos'])->name('inmuebles.storePhotos');
Route::get('inmuebles/{inmueble}/delete-photos', [InmuebleController::class, 'deletePhotos'])->name('inmuebles.deletePhotos');
Route::delete('inmuebles/{inmueble}/delete-photo', [InmuebleController::class, 'destroyPhoto'])->name('inmuebles.destroyPhoto');
Route::post('/guardar_imagenes', [InmuebleController::class, 'guardarImagenes'])->name('guardar_imagenes');
Route::post('/inmuebles/toggle-destacado/{id}', [InmuebleController::class, 'toggleDestacado'])->name('inmuebles.toggleDestacado');
Route::get('/buscar-propiedades', [PropertySearchController::class, 'search'])->name('buscar-propiedades');
Route::get('/propiedades', [PropertySearchController::class, 'index'])->name('propiedades.index');
Route::get('/mapa', function () {
    return redirect()->route('propiedades.index', ['vista' => 'mapa']);
})->name('propiedades.mapa');
Route::get('/api/propiedades', [PropertySearchController::class, 'apiMapa'])->name('propiedades.api');

// Ruta para la página de contacto
Route::get('/contacto', function () {
    return view('contacto');
})->name('contacto');
Route::post('/contacto', [ContactoController::class, 'store'])->name('contacto.store');

Route::get('/portadaimages', [ImageController::class, 'list'])->name('images.list');

Route::post('/fotos/{inmuebleId}', [FotoController::class, 'store'])->name('fotos.store');
Route::delete('/fotos/{id}', [FotoController::class, 'destroy'])->name('fotos.destroy');

Route::get('/send-whatsapp', [MessageController::class, 'showForm'])->name('send-whatsapp.form');
Route::post('/sendpost', [MessageController::class, 'sendMessage'])->name('sendpost');
