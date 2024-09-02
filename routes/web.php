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

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QrCodeController;

// Mostrar el formulario de registro
Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('registercreate');

// Registrar un nuevo usuario
Route::post('register', [AuthController::class, 'register'])->name('register');

// Mostrar el formulario de inicio de sesión
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');

// Iniciar sesión
Route::post('login', [AuthController::class, 'login']);

// Cerrar sesión
Route::get('logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/', function () {
    return view('tramites.create');
})->middleware(['auth']);

Route::middleware('auth')->group(function () {
    Route::resource('tramites', TramiteController::class);

    Route::get('solicitudes', [SolicitudController::class, 'index'])->name('solicitudes.index');
    Route::get('solicitudes/{id}', [SolicitudController::class, 'show'])->name('solicitudes.show');
    Route::post('/generate-qr', [QrCodeController::class, 'generateQrCode'])->name('solicitudes.qr');
    Route::put('solicitudes/{id}/estado', [SolicitudController::class, 'updateEstado'])->name('solicitudes.updateEstado');
    Route::get('tramites/{id}/solicitud', [SolicitudController::class, 'create'])->name('solicitudes.create');
    Route::post('tramites/{id}/solicitud', [SolicitudController::class, 'store'])->name('solicitudes.store');
});



