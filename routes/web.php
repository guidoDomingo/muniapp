<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TramiteController;
use App\Http\Controllers\SolicitudController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('contenido.contenido');
});

//Route::middleware('auth')->group(function () {
    Route::get('tramites', [TramiteController::class, 'index'])->name('tramites.index');
    Route::get('tramites/{id}', [TramiteController::class, 'show'])->name('tramites.show');

    Route::get('solicitudes', [SolicitudController::class, 'index'])->name('solicitudes.index');
    Route::get('solicitudes/{id}', [SolicitudController::class, 'show'])->name('solicitudes.show');
    Route::get('tramites/{id}/solicitud', [SolicitudController::class, 'create'])->name('solicitudes.create');
    Route::post('tramites/{id}/solicitud', [SolicitudController::class, 'store'])->name('solicitudes.store');
//});

