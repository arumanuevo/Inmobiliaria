<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InmuebleController;
use App\Http\Controllers\InmobiliariaController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\FotoController;
use App\Http\Controllers\PropertySearchController;
use App\Http\Controllers\MessageController;

Route::get('/', [InmuebleController::class, 'destacados'])->name('welcome');
Route::get('/inicio', [InmuebleController::class, 'destacados'])->name('inicio');

Auth::routes();

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

// Perfil editable por la propia inmobiliaria (logueada)
Route::middleware('auth')->group(function () {
    Route::get('/mi-inmobiliaria', [InmobiliariaController::class, 'perfil'])->name('inmobiliaria.perfil');
    Route::put('/mi-inmobiliaria', [InmobiliariaController::class, 'actualizarPerfil'])->name('inmobiliaria.update');
});

// Panel de administración del SaaS (solo admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/inmobiliarias', [App\Http\Controllers\Admin\AdminController::class, 'inmobiliarias'])->name('inmobiliarias');
    Route::post('/inmobiliarias/{id}/estado', [App\Http\Controllers\Admin\AdminController::class, 'cambiarEstado'])->name('inmobiliarias.estado');
    Route::post('/inmobiliarias/{id}/plan', [App\Http\Controllers\Admin\AdminController::class, 'cambiarPlan'])->name('inmobiliarias.plan');
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
