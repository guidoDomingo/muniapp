<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TramiteController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ChatController;

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
    Route::put('solicitudes/{id}/comentario', [SolicitudController::class, 'updateComentario'])->name('solicitudes.updateComentario');
    Route::get('tramites/{id}/solicitud', [SolicitudController::class, 'create'])->name('solicitudes.create');
    Route::post('tramites/{id}/solicitud', [SolicitudController::class, 'store'])->name('solicitudes.store');
    
    // Rutas del chatbot
    Route::get('chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
    Route::post('chatbot/procesar', [ChatbotController::class, 'procesarPregunta'])->name('chatbot.procesar');
    
    // Rutas de administración del chatbot (solo para administradores)
    Route::get('chatbot/admin', [ChatbotController::class, 'admin'])->name('chatbot.admin');
    Route::post('chatbot', [ChatbotController::class, 'store'])->name('chatbot.store');
    Route::put('chatbot/{id}', [ChatbotController::class, 'update'])->name('chatbot.update');
    Route::delete('chatbot/{id}', [ChatbotController::class, 'destroy'])->name('chatbot.destroy');
    
    // Rutas del chat en tiempo real
    Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('chat/{room}/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
});



