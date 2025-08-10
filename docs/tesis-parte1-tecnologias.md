# MUNIAPP - DOCUMENTACIÓN TÉCNICA DEL SISTEMA
## PARTE 1: TECNOLOGÍAS Y ARQUITECTURA DEL SISTEMA

---

## 1. STACK TECNOLÓGICO IMPLEMENTADO

### 1.1 Tecnologías Backend

#### 1.1.1 Framework Principal: Laravel 10+

Laravel es un framework de desarrollo web de código abierto basado en PHP que sigue el patrón arquitectónico Modelo-Vista-Controlador (MVC). Para el proyecto MuniApp se utilizó Laravel 10+ por las siguientes ventajas técnicas:

**Características principales implementadas:**
- **Eloquent ORM**: Sistema de mapeo objeto-relacional que facilita la interacción con la base de datos
- **Artisan CLI**: Herramienta de línea de comandos para tareas de desarrollo y mantenimiento
- **Blade Template Engine**: Sistema de plantillas para la generación de vistas dinámicas
- **Laravel Sanctum**: Sistema de autenticación ligero para APIs y SPAs
- **Laravel Reverb**: Servidor WebSocket nativo para comunicación en tiempo real
- **Sistema de Migraciones**: Control de versiones para la estructura de base de datos

**Dependencias principales del proyecto:**
```json
{
    "php": "^8.1",
    "laravel/framework": "^10.0",
    "laravel/reverb": "^1.5",
    "laravel/sanctum": "^3.2",
    "spatie/laravel-permission": "^6.9",
    "barryvdh/laravel-dompdf": "^3.0",
    "mpdf/mpdf": "^8.2",
    "simplesoftwareio/simple-qrcode": "^4.2",
    "silviolleite/laravelpwa": "^2.0"
}
```

#### 1.1.2 Lenguaje de Programación: PHP 8.1+

PHP 8.1 ofrece mejoras significativas en rendimiento y funcionalidades modernas:

**Características utilizadas:**
- **Tipos de Unión**: Para definición precisa de tipos de parámetros
- **Atributos**: Metadatos estructurados para clases y métodos
- **Match Expression**: Alternativa mejorada a switch/case
- **Propiedades ReadOnly**: Para inmutabilidad de datos
- **JIT Compiler**: Compilación Just-In-Time para mejor rendimiento

**Ejemplo de implementación en el modelo User:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class);
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }
}
```

#### 1.1.3 Base de Datos: MySQL 8.0+

MySQL fue seleccionado como sistema de gestión de base de datos relacional por su robustez y compatibilidad con Laravel.

**Características implementadas:**
- **InnoDB Storage Engine**: Para transacciones ACID y integridad referencial
- **UTF8MB4 Character Set**: Soporte completo para Unicode y emojis
- **JSON Data Type**: Para almacenamiento de formularios dinámicos
- **Spatial Data Types**: Para coordenadas de geolocalización
- **Indexación optimizada**: Para consultas rápidas en tablas principales

**Estructura de base de datos:**

| Tabla | Descripción | Registros Estimados |
|-------|-------------|---------------------|
| users | Usuarios del sistema | 1,000 - 10,000 |
| tramites | Catálogo de trámites municipales | 50 - 200 |
| solicitudes | Solicitudes de trámites | 5,000 - 50,000 |
| chats | Mensajes del sistema de chat | 10,000 - 100,000 |
| faqs | Preguntas frecuentes | 20 - 100 |
| roles | Roles del sistema | 5 - 10 |
| permissions | Permisos específicos | 20 - 50 |
| model_has_roles | Relación usuario-rol | 1,000 - 10,000 |
| role_has_permissions | Relación rol-permiso | 50 - 200 |

### 1.2 Tecnologías Frontend

#### 1.2.1 Sistema de Plantillas: Blade

Blade es el motor de plantillas nativo de Laravel que permite crear interfaces dinámicas con sintaxis elegante.

**Características implementadas:**
```blade
{{-- Ejemplo de plantilla principal --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MuniApp - Chat')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    @include('partials.navbar')
    
    <div class="container py-4">
        @yield('content')
    </div>
    
    @stack('scripts')
</body>
</html>
```

#### 1.2.2 Framework CSS: Bootstrap 5

Bootstrap 5 proporciona un sistema de diseño responsivo y componentes pre-construidos.

**Componentes utilizados:**
- **Grid System**: Sistema de 12 columnas para layouts responsivos
- **Navigation**: Barras de navegación adaptativas
- **Forms**: Controles de formulario con validación visual
- **Cards**: Contenedores de contenido estructurado
- **Modals**: Ventanas emergentes para confirmaciones
- **Alerts**: Mensajes de notificación al usuario
- **Badges**: Indicadores de estado y contadores

#### 1.2.3 JavaScript Moderno: ES6+ con Vite

Vite es una herramienta de construcción moderna que proporciona desarrollo rápido y optimización de producción.

**Configuración implementada:**
```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

**Funcionalidades JavaScript implementadas:**
- **Comunicación en tiempo real** con Laravel Echo
- **Gestión de eventos** del DOM
- **AJAX requests** para operaciones asíncronas
- **Geolocalización** con HTML5 Geolocation API
- **WebSocket connections** para chat en tiempo real

### 1.3 Tecnologías de Comunicación en Tiempo Real

#### 1.3.1 Laravel Reverb

Laravel Reverb es el servidor WebSocket nativo de Laravel para aplicaciones en tiempo real.

**Configuración del servidor:**
```php
// config/broadcasting.php
'reverb' => [
    'driver' => 'reverb',
    'app_id' => env('REVERB_APP_ID', '620483'),
    'key' => env('REVERB_APP_KEY', 'bfaoq7zrkktceypj1l09'),
    'secret' => env('REVERB_APP_SECRET', 'qimbnvqlo8lnrgqeaq2h'),
    'host' => env('REVERB_HOST', 'localhost'),
    'port' => env('REVERB_PORT', 8080),
    'scheme' => env('REVERB_SCHEME', 'http'),
    'options' => [
        'cluster' => 'mt1',
        'useTLS' => false,
    ],
],
```

#### 1.3.2 Laravel Echo

Laravel Echo es la librería JavaScript para suscribirse a canales y escuchar eventos.

**Implementación del cliente:**
```javascript
// resources/js/bootstrap.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY || 'bfaoq7zrkktceypj1l09',
    cluster: 'mt1',
    wsHost: import.meta.env.VITE_PUSHER_HOST || window.location.hostname,
    wsPort: import.meta.env.VITE_PUSHER_PORT || 8080,
    wssPort: import.meta.env.VITE_PUSHER_PORT || 8080,
    forceTLS: false,
    encrypted: false,
    disableStats: true,
    enabledTransports: ['ws', 'wss']
});
```

### 1.4 Paquetes y Librerías Especializadas

#### 1.4.1 Sistema de Permisos: Spatie Laravel Permission

Esta librería proporciona un sistema robusto de roles y permisos.

**Características implementadas:**
- **Roles dinámicos**: Administrador, Usuario, Moderador
- **Permisos granulares**: Crear, leer, actualizar, eliminar por recurso
- **Middleware de autorización**: Protección automática de rutas
- **Herencia de permisos**: Los roles pueden heredar permisos

**Ejemplo de uso:**
```php
// Asignar rol a usuario
$user->assignRole('admin');

// Verificar permisos
if ($user->can('edit solicitudes')) {
    // Usuario puede editar solicitudes
}

// Middleware en rutas
Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index']);
});
```

#### 1.4.2 Generación de PDF: DOMPDF y MPDF

Para la generación de documentos PDF del sistema.

**DOMPDF - Reportes básicos:**
```php
use Barryvdh\DomPDF\Facade\Pdf;

public function generateReport()
{
    $solicitudes = Solicitud::with('user', 'tramite')->get();
    
    $pdf = Pdf::loadView('reports.solicitudes', compact('solicitudes'));
    
    return $pdf->download('reporte-solicitudes.pdf');
}
```

**MPDF - Documentos complejos:**
```php
use Mpdf\Mpdf;

public function generateCertificate($solicitudId)
{
    $solicitud = Solicitud::findOrFail($solicitudId);
    
    $mpdf = new Mpdf();
    $html = view('certificates.tramite', compact('solicitud'))->render();
    $mpdf->WriteHTML($html);
    
    return $mpdf->Output('certificado.pdf', 'D');
}
```

#### 1.4.3 Códigos QR: Simple QRCode

Para generación de códigos QR de seguimiento de solicitudes.

**Implementación:**
```php
use SimpleSoftwareIO\QrCode\Facades\QrCode;

public function generateTrackingQR($solicitudId)
{
    $url = route('solicitud.seguimiento', $solicitudId);
    
    return QrCode::size(300)
                 ->format('png')
                 ->generate($url);
}
```

#### 1.4.4 PWA: Laravel PWA

Para convertir la aplicación web en una Progressive Web App.

**Características habilitadas:**
- **Service Worker**: Cache offline y actualizaciones automáticas
- **Web App Manifest**: Instalación en dispositivos móviles
- **Push Notifications**: Notificaciones push (futuro)
- **Offline Support**: Funcionalidad básica sin conexión

---

## 2. ARQUITECTURA DEL SISTEMA

### 2.1 Patrón Arquitectónico: MVC (Modelo-Vista-Controlador)

El sistema MuniApp implementa el patrón MVC de Laravel con las siguientes capas:

#### 2.1.1 Capa de Modelo (Model)

**Responsabilidades:**
- Gestión de datos y lógica de negocio
- Interacción con la base de datos
- Validación de datos
- Relaciones entre entidades

**Modelos principales:**
```php
// Modelo User con relaciones
class User extends Authenticatable
{
    use HasRoles;
    
    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }
    
    public function chats()
    {
        return $this->hasMany(Chat::class);
    }
}

// Modelo Solicitud con validaciones
class Solicitud extends Model
{
    protected $fillable = [
        'user_id', 'tramite_id', 'detalles', 
        'estado', 'latitud', 'longitud', 'formulario'
    ];
    
    protected $casts = [
        'formulario' => 'array',
        'latitud' => 'decimal:7',
        'longitud' => 'decimal:7'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }
}
```

#### 2.1.2 Capa de Vista (View)

**Estructura de vistas:**
```
resources/views/
├── layouts/
│   ├── app.blade.php          # Plantilla principal
│   └── admin.blade.php        # Plantilla administrativa
├── auth/
│   ├── login.blade.php        # Formulario de login
│   └── register.blade.php     # Formulario de registro
├── tramites/
│   ├── index.blade.php        # Lista de trámites
│   └── show.blade.php         # Detalle de trámite
├── solicitudes/
│   ├── index.blade.php        # Mis solicitudes
│   ├── create.blade.php       # Nueva solicitud
│   └── show.blade.php         # Detalle de solicitud
├── chat/
│   └── index.blade.php        # Interfaz de chat
└── admin/
    ├── dashboard.blade.php    # Panel administrativo
    └── solicitudes.blade.php  # Gestión de solicitudes
```

#### 2.1.3 Capa de Controlador (Controller)

**Controladores principales:**

```php
// ChatController - Gestión del sistema de chat
class ChatController extends Controller
{
    public function index(): View
    {
        $messages = Chat::with('user')
                       ->where('room', 'general')
                       ->orderBy('created_at', 'desc')
                       ->limit(50)
                       ->get();
        
        return view('chat.index', compact('messages'));
    }
    
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'room' => 'sometimes|string|max:50',
        ]);
        
        $chat = Chat::create([
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'room' => $validated['room'] ?? 'general',
        ]);
        
        // Broadcasting del evento
        event(new NewChatMessage($chat));
        
        return response()->json($chat->load('user'), 201);
    }
}

// SolicitudController - Gestión de trámites
class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = auth()->user()
                            ->solicitudes()
                            ->with('tramite')
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
        
        return view('solicitudes.index', compact('solicitudes'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tramite_id' => 'required|exists:tramites,id',
            'detalles' => 'required|string|max:1000',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'formulario' => 'nullable|array'
        ]);
        
        $solicitud = Solicitud::create([
            'user_id' => auth()->id(),
            ...$validated
        ]);
        
        return redirect()
            ->route('solicitudes.show', $solicitud)
            ->with('success', 'Solicitud creada exitosamente');
    }
}
```

### 2.2 Arquitectura de Capas

El sistema implementa una arquitectura de capas bien definida:

#### 2.2.1 Capa de Presentación
- **Componentes**: Vistas Blade, JavaScript, CSS
- **Responsabilidades**: Interfaz de usuario, validación del lado cliente
- **Tecnologías**: Bootstrap 5, Blade Templates, JavaScript ES6+

#### 2.2.2 Capa de Aplicación
- **Componentes**: Controladores, Middleware, Form Requests
- **Responsabilidades**: Lógica de aplicación, flujo de control
- **Patrones**: Command Pattern, Strategy Pattern

#### 2.2.3 Capa de Dominio
- **Componentes**: Modelos, Servicios, Eventos, Listeners
- **Responsabilidades**: Lógica de negocio, reglas de dominio
- **Patrones**: Repository Pattern, Observer Pattern

#### 2.2.4 Capa de Infraestructura
- **Componentes**: Base de datos, Sistema de archivos, APIs externas
- **Responsabilidades**: Persistencia, comunicaciones externas
- **Tecnologías**: MySQL, Redis, WebSockets

### 2.3 Patrones de Diseño Implementados

#### 2.3.1 Repository Pattern
```php
interface SolicitudRepositoryInterface
{
    public function findByUser(User $user);
    public function findByStatus(string $status);
    public function create(array $data);
    public function update(Solicitud $solicitud, array $data);
}

class EloquentSolicitudRepository implements SolicitudRepositoryInterface
{
    public function findByUser(User $user)
    {
        return Solicitud::where('user_id', $user->id)
                       ->with('tramite')
                       ->orderBy('created_at', 'desc')
                       ->get();
    }
    
    public function findByStatus(string $status)
    {
        return Solicitud::where('estado', $status)
                       ->with(['user', 'tramite'])
                       ->get();
    }
}
```

#### 2.3.2 Observer Pattern
```php
// Event
class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $chat;
    
    public function __construct(Chat $chat)
    {
        $this->chat = $chat->load('user');
    }
    
    public function broadcastOn()
    {
        return new Channel('chat.' . $this->chat->room);
    }
    
    public function broadcastAs()
    {
        return 'message.sent';
    }
}

// Listener
class SendChatNotification
{
    public function handle(NewChatMessage $event)
    {
        // Enviar notificación por email si es necesario
        if ($event->chat->room === 'support') {
            Mail::to('admin@muniapp.com')
                ->send(new NewSupportMessage($event->chat));
        }
    }
}
```

#### 2.3.3 Factory Pattern
```php
class TramiteFormFactory
{
    public static function createForm(string $type): FormInterface
    {
        return match($type) {
            'licencia_comercial' => new LicenciaComercialForm(),
            'permiso_construccion' => new PermisoConstruccionForm(),
            'registro_vehicular' => new RegistroVehicularForm(),
            default => new GenericForm()
        };
    }
}
```

---

Esta es la primera parte de la documentación técnica. ¿Te parece bien el nivel de detalle y estructura? Continúo con la siguiente parte que incluirá la implementación de la base de datos, APIs y funcionalidades específicas.
