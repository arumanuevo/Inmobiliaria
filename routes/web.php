<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InmuebleController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\FotoController;
use App\Http\Controllers\PropertySearchController;
use App\Http\Controllers\MessageController;

Route::get('/', [InmuebleController::class, 'destacados'])->name('welcome');
Route::get('/inicio', [InmuebleController::class, 'destacados'])->name('inicio');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Rutas para el CRUD de Inmuebles
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

Route::get('/ruta-admin', [App\Http\Controllers\Admin\AdminController::class, 'adminView']);

Route::post('/fotos/{inmuebleId}', [FotoController::class, 'store'])->name('fotos.store');
Route::delete('/fotos/{id}', [FotoController::class, 'destroy'])->name('fotos.destroy');



Route::get('/send-whatsapp', [MessageController::class, 'showForm'])->name('send-whatsapp.form');
Route::post('/sendpost', [MessageController::class, 'sendMessage'])->name('sendpost');





