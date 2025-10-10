<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TramiteController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminTramiteController;
use App\Http\Controllers\Admin\AdminSolicitudController;
use App\Http\Controllers\Admin\AdminChatController;
use App\Http\Controllers\Admin\AdminDepartmentController;

// Route to serve images
Route::get('/images/{filename}', function ($filename) {
    $path = public_path('images/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    $file = file_get_contents($path);
    $type = mime_content_type($path);
    
    return response($file, 200)->header('Content-Type', $type);
})->where('filename', '.*');

// Route to serve storage files (documentos)
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    
    if (!file_exists($fullPath)) {
        abort(404);
    }
    
    $file = file_get_contents($fullPath);
    $type = mime_content_type($fullPath);
    
    // For downloads, add proper headers
    $filename = basename($fullPath);
    
    return response($file, 200)
        ->header('Content-Type', $type)
        ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
})->where('path', '.*');

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
use App\Http\Controllers\CommissionDashboardController;
use App\Http\Controllers\SSEController;

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


Route::get('/', [TramiteController::class, 'index'])->middleware(['auth']);

// Admin access route
Route::get('/admin', function () {
    return view('admin.login');
})->name('admin.login.form');

Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login');

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
    
    // Rutas SSE para tiempo real
    Route::get('chat/{room}/stream', [SSEController::class, 'chatStream'])->name('chat.stream');
    Route::get('sse/status', [SSEController::class, 'status'])->name('sse.status');
});

// Admin Routes - Protected by admin role
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');
    Route::get('/reports', [AdminDashboardController::class, 'reports'])->name('reports');
    
    // Users Management
    Route::resource('users', AdminUserController::class);
    
    // Tramites Management
    Route::resource('tramites', AdminTramiteController::class);
    
    // Solicitudes Management
    Route::get('solicitudes', [AdminSolicitudController::class, 'index'])->name('solicitudes.index');
    Route::get('solicitudes/create', [AdminSolicitudController::class, 'create'])->name('solicitudes.create');
    Route::post('solicitudes', [AdminSolicitudController::class, 'store'])->name('solicitudes.store');
    Route::get('solicitudes/{solicitud}', [AdminSolicitudController::class, 'show'])->name('solicitudes.show');
    Route::get('solicitudes/{solicitud}/edit', [AdminSolicitudController::class, 'edit'])->name('solicitudes.edit');
    Route::put('solicitudes/{solicitud}', [AdminSolicitudController::class, 'update'])->name('solicitudes.update');
    Route::delete('solicitudes/{solicitud}', [AdminSolicitudController::class, 'destroy'])->name('solicitudes.destroy');
    Route::patch('solicitudes/{solicitud}/status', [AdminSolicitudController::class, 'updateStatus'])->name('solicitudes.update-status');
    Route::post('solicitudes/{solicitud}/assign', [AdminSolicitudController::class, 'assignUser'])->name('solicitudes.assign');
    
    // Chat Management
    Route::get('chat', [AdminChatController::class, 'index'])->name('chat.index');
    Route::get('chat/live', [AdminChatController::class, 'chat'])->name('chat.live');
    Route::get('chat/rooms', [AdminChatController::class, 'rooms'])->name('chat.rooms');
    Route::post('chat/moderate', [AdminChatController::class, 'moderate'])->name('chat.moderate');
    
    // Departments Management
    Route::resource('departments', AdminDepartmentController::class);
    
    // Settings
    Route::get('settings', [AdminDashboardController::class, 'settings'])->name('settings');
    Route::post('settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');
});

// Commission Routes - Protected by commission role
Route::middleware(['auth', 'role:commission|admin'])->prefix('commission')->name('commission.')->group(function () {
    Route::get('/dashboard', [CommissionDashboardController::class, 'index'])->name('dashboard');
    Route::post('solicitudes/{id}/approve', [CommissionDashboardController::class, 'aproveSolicitud'])->name('solicitudes.approve');
    Route::post('solicitudes/{id}/reject', [CommissionDashboardController::class, 'rejectSolicitud'])->name('solicitudes.reject');
    
    Route::get('solicitudes', [SolicitudController::class, 'index'])->name('solicitudes.index');
    Route::get('solicitudes/{solicitud}', [SolicitudController::class, 'show'])->name('solicitudes.show');
    Route::patch('solicitudes/{solicitud}/estado', [SolicitudController::class, 'updateEstado'])->name('solicitudes.updateEstado');
    
    Route::get('chat', function() { 
        return view('commission.chat'); 
    })->name('chat.index');
});



