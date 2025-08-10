# MUNIAPP - DOCUMENTACIÓN TÉCNICA DEL SISTEMA
## PARTE 2: BASE DE DATOS Y IMPLEMENTACIÓN DE APIS

---

## 3. DISEÑO E IMPLEMENTACIÓN DE BASE DE DATOS

### 3.1 Modelo Conceptual de la Base de Datos

#### 3.1.1 Entidades Principales

El sistema MuniApp maneja las siguientes entidades principales con sus respectivas características:

| Entidad | Descripción | Atributos Clave | Relaciones |
|---------|-------------|-----------------|------------|
| **User** | Usuarios del sistema (ciudadanos y funcionarios) | id, name, email, password, email_verified_at | 1:N con Solicitud, 1:N con Chat, N:N con Role |
| **Tramite** | Catálogo de trámites municipales disponibles | id, nombre, descripcion, created_at, updated_at | 1:N con Solicitud |
| **Solicitud** | Solicitudes de trámites realizadas por usuarios | id, user_id, tramite_id, detalles, estado, latitud, longitud, formulario | N:1 con User, N:1 con Tramite |
| **Chat** | Mensajes del sistema de comunicación | id, user_id, message, room, created_at | N:1 con User |
| **Faq** | Preguntas frecuentes del sistema | id, pregunta, respuesta, activo, orden, categoria | - |
| **Role** | Roles del sistema de permisos | id, name, guard_name, created_at | N:N con User, N:N con Permission |
| **Permission** | Permisos específicos del sistema | id, name, guard_name, created_at | N:N con Role |

#### 3.1.2 Modelo Físico - Estructura de Tablas

**Tabla: users**
```sql
CREATE TABLE `users` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email_verified_at` timestamp NULL DEFAULT NULL,
    `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Tabla: tramites**
```sql
CREATE TABLE `tramites` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `tramites_nombre_index` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Tabla: solicitudes**
```sql
CREATE TABLE `solicitudes` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) UNSIGNED NOT NULL,
    `tramite_id` bigint(20) UNSIGNED NOT NULL,
    `formulario` json DEFAULT NULL,
    `detalles` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `comentario` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `latitud` decimal(10,7) DEFAULT NULL,
    `longitud` decimal(10,7) DEFAULT NULL,
    `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `solicitudes_user_id_foreign` (`user_id`),
    KEY `solicitudes_tramite_id_foreign` (`tramite_id`),
    KEY `solicitudes_estado_index` (`estado`),
    KEY `solicitudes_created_at_index` (`created_at`),
    CONSTRAINT `solicitudes_tramite_id_foreign` FOREIGN KEY (`tramite_id`) REFERENCES `tramites` (`id`) ON DELETE CASCADE,
    CONSTRAINT `solicitudes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Tabla: chats**
```sql
CREATE TABLE `chats` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) UNSIGNED NOT NULL,
    `message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `room` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `chats_user_id_foreign` (`user_id`),
    KEY `chats_room_index` (`room`),
    KEY `chats_created_at_index` (`created_at`),
    CONSTRAINT `chats_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 3.1.3 Implementación con Laravel Migrations

**Migración de la tabla users:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            
            // Índices para optimización
            $table->index('email');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

**Migración de la tabla solicitudes:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tramite_id')->constrained('tramites')->onDelete('cascade');
            $table->json('formulario')->nullable();
            $table->string('detalles')->nullable();
            $table->text('comentario')->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->string('estado')->default('pendiente');
            $table->timestamps();
            
            // Índices compuestos para consultas optimizadas
            $table->index(['user_id', 'estado']);
            $table->index(['tramite_id', 'created_at']);
            $table->index(['estado', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
```

### 3.2 Optimización y Performance de Base de Datos

#### 3.2.1 Estrategias de Indexación

**Índices implementados por tabla:**

```sql
-- Tabla users
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_created_at ON users(created_at);

-- Tabla solicitudes
CREATE INDEX idx_solicitudes_user_estado ON solicitudes(user_id, estado);
CREATE INDEX idx_solicitudes_tramite_fecha ON solicitudes(tramite_id, created_at);
CREATE INDEX idx_solicitudes_estado_fecha ON solicitudes(estado, created_at);
CREATE INDEX idx_solicitudes_geolocation ON solicitudes(latitud, longitud);

-- Tabla chats
CREATE INDEX idx_chats_room_fecha ON chats(room, created_at);
CREATE INDEX idx_chats_user_fecha ON chats(user_id, created_at);
```

#### 3.2.2 Consultas Optimizadas con Eloquent

**Consultas con Eager Loading:**
```php
// Consulta optimizada para listado de solicitudes
$solicitudes = Solicitud::with(['user:id,name,email', 'tramite:id,nombre'])
                        ->where('estado', 'pendiente')
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);

// Consulta optimizada para dashboard administrativo
$estadisticas = DB::table('solicitudes')
                  ->select('estado', DB::raw('COUNT(*) as total'))
                  ->where('created_at', '>=', Carbon::now()->subMonth())
                  ->groupBy('estado')
                  ->get();

// Consulta geoespacial para solicitudes cercanas
$solicitudesCercanas = Solicitud::whereNotNull('latitud')
                                ->whereNotNull('longitud')
                                ->selectRaw('*, (6371 * ACOS(COS(RADIANS(?)) 
                                           * COS(RADIANS(latitud)) 
                                           * COS(RADIANS(longitud) - RADIANS(?)) 
                                           + SIN(RADIANS(?)) 
                                           * SIN(RADIANS(latitud)))) AS distance', 
                                           [$lat, $lon, $lat])
                                ->having('distance', '<', 5)
                                ->orderBy('distance')
                                ->limit(10)
                                ->get();
```

#### 3.2.3 Cache y Optimización de Consultas

**Implementación de Cache en controladores:**
```php
class DashboardController extends Controller
{
    public function index()
    {
        // Cache de estadísticas por 30 minutos
        $estadisticas = Cache::remember('dashboard_stats', 1800, function () {
            return [
                'total_solicitudes' => Solicitud::count(),
                'solicitudes_pendientes' => Solicitud::where('estado', 'pendiente')->count(),
                'solicitudes_aprobadas' => Solicitud::where('estado', 'aprobada')->count(),
                'usuarios_activos' => User::where('created_at', '>=', Carbon::now()->subMonth())->count(),
                'tramites_populares' => $this->getTramitesPopulares(),
            ];
        });
        
        return view('admin.dashboard', compact('estadisticas'));
    }
    
    private function getTramitesPopulares()
    {
        return Cache::remember('tramites_populares', 3600, function () {
            return Tramite::withCount(['solicitudes' => function ($query) {
                            $query->where('created_at', '>=', Carbon::now()->subMonth());
                        }])
                        ->orderBy('solicitudes_count', 'desc')
                        ->limit(5)
                        ->get();
        });
    }
}
```

---

## 4. IMPLEMENTACIÓN DE APIs RESTful

### 4.1 Arquitectura de API

#### 4.1.1 Estructura de Rutas API

```php
// routes/api.php
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Rutas de usuario autenticado
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles');
    });
    
    // API de Trámites
    Route::apiResource('tramites', TramiteController::class)
         ->only(['index', 'show']);
    
    // API de Solicitudes
    Route::apiResource('solicitudes', SolicitudController::class);
    Route::patch('/solicitudes/{solicitud}/estado', [SolicitudController::class, 'updateEstado'])
         ->middleware('role:admin');
    
    // API de Chat
    Route::get('/chat/messages/{room?}', [ChatController::class, 'getMessages']);
    Route::post('/chat', [ChatController::class, 'store']);
    
    // API de FAQs
    Route::get('/faqs', [FaqController::class, 'index']);
    Route::get('/faqs/categoria/{categoria}', [FaqController::class, 'byCategoria']);
    
    // API de Estadísticas (solo admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/estadisticas', [AdminController::class, 'estadisticas']);
        Route::get('/admin/solicitudes', [AdminController::class, 'solicitudes']);
        Route::get('/admin/usuarios', [AdminController::class, 'usuarios']);
    });
});
```

#### 4.1.2 Controladores API Especializados

**API Controller Base:**
```php
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaseApiController extends Controller
{
    protected function successResponse($data, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }
    
    protected function errorResponse(string $message, $errors = null, int $code = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message
        ];
        
        if ($errors) {
            $response['errors'] = $errors;
        }
        
        return response()->json($response, $code);
    }
    
    protected function validationErrorResponse($validator): JsonResponse
    {
        return $this->errorResponse(
            'Errores de validación',
            $validator->errors(),
            422
        );
    }
}
```

**Controlador API de Solicitudes:**
```php
<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreSolicitudRequest;
use App\Http\Requests\UpdateSolicitudRequest;
use App\Http\Resources\SolicitudResource;
use App\Models\Solicitud;
use Illuminate\Http\Request;

class SolicitudController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = Solicitud::with(['user:id,name,email', 'tramite:id,nombre']);
        
        // Filtros opcionales
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->has('tramite_id')) {
            $query->where('tramite_id', $request->tramite_id);
        }
        
        if ($request->has('usuario_id')) {
            $query->where('user_id', $request->usuario_id);
        }
        
        // Búsqueda por texto
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('detalles', 'like', "%{$search}%")
                  ->orWhere('comentario', 'like', "%{$search}%")
                  ->orWhereHas('tramite', function($tramiteQuery) use ($search) {
                      $tramiteQuery->where('nombre', 'like', "%{$search}%");
                  });
            });
        }
        
        // Ordenamiento
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);
        
        // Paginación
        $perPage = min($request->get('per_page', 15), 50); // Máximo 50 por página
        $solicitudes = $query->paginate($perPage);
        
        return $this->successResponse([
            'solicitudes' => SolicitudResource::collection($solicitudes->items()),
            'pagination' => [
                'current_page' => $solicitudes->currentPage(),
                'last_page' => $solicitudes->lastPage(),
                'per_page' => $solicitudes->perPage(),
                'total' => $solicitudes->total(),
                'has_more_pages' => $solicitudes->hasMorePages()
            ]
        ]);
    }
    
    public function store(StoreSolicitudRequest $request)
    {
        try {
            $solicitud = Solicitud::create([
                'user_id' => auth()->id(),
                ...$request->validated()
            ]);
            
            $solicitud->load(['user', 'tramite']);
            
            // Disparar evento para notificaciones
            event(new \App\Events\SolicitudCreated($solicitud));
            
            return $this->successResponse(
                new SolicitudResource($solicitud),
                'Solicitud creada exitosamente',
                201
            );
            
        } catch (\Exception $e) {
            \Log::error('Error creating solicitud: ' . $e->getMessage());
            
            return $this->errorResponse(
                'Error interno del servidor',
                null,
                500
            );
        }
    }
    
    public function show(Solicitud $solicitud)
    {
        // Verificar autorización
        if (!auth()->user()->can('view', $solicitud)) {
            return $this->errorResponse('No autorizado', null, 403);
        }
        
        $solicitud->load(['user', 'tramite']);
        
        return $this->successResponse(new SolicitudResource($solicitud));
    }
    
    public function update(UpdateSolicitudRequest $request, Solicitud $solicitud)
    {
        try {
            $solicitud->update($request->validated());
            $solicitud->load(['user', 'tramite']);
            
            return $this->successResponse(
                new SolicitudResource($solicitud),
                'Solicitud actualizada exitosamente'
            );
            
        } catch (\Exception $e) {
            \Log::error('Error updating solicitud: ' . $e->getMessage());
            
            return $this->errorResponse(
                'Error interno del servidor',
                null,
                500
            );
        }
    }
    
    public function updateEstado(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_revision,aprobada,rechazada,finalizada',
            'comentario_admin' => 'nullable|string|max:1000'
        ]);
        
        $estadoAnterior = $solicitud->estado;
        
        $solicitud->update([
            'estado' => $request->estado,
            'comentario_admin' => $request->comentario_admin
        ]);
        
        // Disparar evento de cambio de estado
        event(new \App\Events\SolicitudStatusChanged($solicitud, $estadoAnterior));
        
        return $this->successResponse(
            new SolicitudResource($solicitud->load(['user', 'tramite'])),
            'Estado actualizado exitosamente'
        );
    }
    
    public function destroy(Solicitud $solicitud)
    {
        try {
            $solicitud->delete();
            
            return $this->successResponse(
                null,
                'Solicitud eliminada exitosamente'
            );
            
        } catch (\Exception $e) {
            \Log::error('Error deleting solicitud: ' . $e->getMessage());
            
            return $this->errorResponse(
                'Error interno del servidor',
                null,
                500
            );
        }
    }
}
```

### 4.2 Resources (Transformadores de Datos)

#### 4.2.1 API Resources para Formateo Consistente

**Resource de Usuario:**
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Relaciones condicionales
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'permissions' => $this->when(
                $this->relationLoaded('roles'),
                function () {
                    return $this->getAllPermissions()->pluck('name');
                }
            ),
            
            // Contadores condicionales
            'solicitudes_count' => $this->when(
                $this->solicitudes_count !== null,
                $this->solicitudes_count
            ),
            
            'chats_count' => $this->when(
                $this->chats_count !== null,
                $this->chats_count
            ),
        ];
    }
}
```

**Resource de Solicitud:**
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolicitudResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'detalles' => $this->detalles,
            'comentario' => $this->comentario,
            'estado' => $this->estado,
            'estado_label' => $this->getEstadoLabel(),
            'formulario' => $this->formulario,
            
            // Geolocalización
            'ubicacion' => $this->when(
                $this->latitud && $this->longitud,
                [
                    'latitud' => (float) $this->latitud,
                    'longitud' => (float) $this->longitud,
                    'maps_url' => "https://maps.google.com/maps?q={$this->latitud},{$this->longitud}"
                ]
            ),
            
            // Fechas
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'created_at_human' => $this->created_at->diffForHumans(),
            
            // Relaciones
            'usuario' => new UserResource($this->whenLoaded('user')),
            'tramite' => new TramiteResource($this->whenLoaded('tramite')),
            
            // URLs útiles
            'links' => [
                'self' => route('api.solicitudes.show', $this->id),
                'user' => route('api.users.show', $this->user_id),
                'tramite' => route('api.tramites.show', $this->tramite_id),
            ],
        ];
    }
    
    private function getEstadoLabel(): string
    {
        return match($this->estado) {
            'pendiente' => 'Pendiente de Revisión',
            'en_revision' => 'En Revisión',
            'documentos_pendientes' => 'Documentos Pendientes',
            'aprobada' => 'Aprobada',
            'rechazada' => 'Rechazada',
            'finalizada' => 'Finalizada',
            default => ucfirst($this->estado)
        };
    }
}
```

### 4.3 Validación y Form Requests

#### 4.3.1 Form Requests Personalizados

**Validación para crear solicitud:**
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }
    
    public function rules(): array
    {
        return [
            'tramite_id' => 'required|exists:tramites,id',
            'detalles' => 'required|string|min:10|max:1000',
            'comentario' => 'nullable|string|max:2000',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'formulario' => 'nullable|array',
            'formulario.*.campo' => 'required|string',
            'formulario.*.valor' => 'required',
            'formulario.*.requerido' => 'boolean',
        ];
    }
    
    public function messages(): array
    {
        return [
            'tramite_id.required' => 'Debe seleccionar un trámite válido',
            'tramite_id.exists' => 'El trámite seleccionado no existe',
            'detalles.required' => 'Los detalles de la solicitud son obligatorios',
            'detalles.min' => 'Los detalles deben tener al menos 10 caracteres',
            'detalles.max' => 'Los detalles no pueden exceder 1000 caracteres',
            'latitud.between' => 'La latitud debe estar entre -90 y 90 grados',
            'longitud.between' => 'La longitud debe estar entre -180 y 180 grados',
        ];
    }
    
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validación personalizada: si hay coordenadas, ambas deben estar presentes
            if (($this->latitud && !$this->longitud) || (!$this->latitud && $this->longitud)) {
                $validator->errors()->add('ubicacion', 'Tanto la latitud como la longitud son requeridas para la ubicación');
            }
            
            // Validar formulario dinámico según el tipo de trámite
            if ($this->tramite_id && $this->formulario) {
                $this->validateDynamicForm($validator);
            }
        });
    }
    
    private function validateDynamicForm($validator)
    {
        $tramite = \App\Models\Tramite::find($this->tramite_id);
        
        if ($tramite && $tramite->formulario_config) {
            $config = json_decode($tramite->formulario_config, true);
            
            foreach ($config['campos'] as $campo) {
                if ($campo['requerido']) {
                    $found = collect($this->formulario)->firstWhere('campo', $campo['nombre']);
                    
                    if (!$found || empty($found['valor'])) {
                        $validator->errors()->add(
                            "formulario.{$campo['nombre']}", 
                            "El campo {$campo['etiqueta']} es requerido"
                        );
                    }
                }
            }
        }
    }
}
```

### 4.4 Middleware de API

#### 4.4.1 Rate Limiting para APIs

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ApiRateLimit
{
    public function handle(Request $request, Closure $next, string $key = 'api')
    {
        $user = $request->user();
        $identifier = $user ? "user:{$user->id}" : "ip:{$request->ip()}";
        
        // Límites diferentes según el tipo de usuario
        $maxAttempts = $user && $user->hasRole('admin') ? 120 : 60; // Por minuto
        
        if (RateLimiter::tooManyAttempts($key . ':' . $identifier, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key . ':' . $identifier);
            
            return response()->json([
                'success' => false,
                'message' => 'Demasiadas solicitudes. Intente nuevamente en ' . $seconds . ' segundos.',
                'retry_after' => $seconds
            ], 429);
        }
        
        RateLimiter::hit($key . ':' . $identifier, 60); // TTL de 1 minuto
        
        $response = $next($request);
        
        // Agregar headers de rate limiting
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', 
            RateLimiter::retriesLeft($key . ':' . $identifier, $maxAttempts));
        
        return $response;
    }
}
```

#### 4.4.2 Middleware de Logging para API

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiLogger
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        
        // Log de request
        Log::channel('api')->info('API Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'parameters' => $this->getLogSafeParameters($request),
            'timestamp' => now()->toISOString()
        ]);
        
        $response = $next($request);
        
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        // Log de response
        Log::channel('api')->info('API Response', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString()
        ]);
        
        // Log de errores detallado
        if ($response->getStatusCode() >= 400) {
            Log::channel('api')->error('API Error Response', [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'status' => $response->getStatusCode(),
                'response_body' => $response->getContent(),
                'user_id' => auth()->id(),
                'timestamp' => now()->toISOString()
            ]);
        }
        
        return $response;
    }
    
    private function getLogSafeParameters(Request $request): array
    {
        $parameters = $request->all();
        
        // Ocultar campos sensibles
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'api_key'];
        
        foreach ($sensitiveFields as $field) {
            if (isset($parameters[$field])) {
                $parameters[$field] = '[HIDDEN]';
            }
        }
        
        return $parameters;
    }
}
```

---

Esta es la segunda parte de la documentación técnica. Continúo con la tercera parte que incluirá la implementación de funcionalidades específicas, sistema de comunicación en tiempo real y testing.
