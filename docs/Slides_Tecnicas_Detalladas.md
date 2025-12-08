# SLIDES TÉCNICAS DETALLADAS - MUNIAPP

## SLIDE TÉCNICA 1: ARQUITECTURA MVC IMPLEMENTADA

```php
// Ejemplo de Controller - AdminSolicitudController.php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Http\Request;

class AdminSolicitudController extends Controller
{
    public function show(Solicitud $solicitud)
    {
        // Autorización usando Policies
        $this->authorize('view', $solicitud);
        
        // Carga eager loading para optimización
        $solicitud->load(['user', 'tramite', 'assignedUser', 'history.user']);
        
        // Filtrado de usuarios por roles
        $users = User::role(['functionary', 'admin'])
            ->where('email', 'not like', '%@muniapp.com')
            ->orderBy('name')
            ->get();
            
        return view('admin.solicitudes.show', compact('solicitud', 'users'));
    }
}
```

**Ventajas del Patrón MVC:**
- Separación clara de responsabilidades
- Mantenibilidad del código
- Reutilización de componentes
- Testing más efectivo

---

## SLIDE TÉCNICA 2: ELOQUENT ORM Y RELACIONES

```php
// Modelo Solicitud.php
class Solicitud extends Model
{
    protected $fillable = [
        'user_id', 'tramite_id', 'estado', 'formulario',
        'attachments', 'assigned_to', 'tracking_code',
        'latitud', 'longitud'
    ];

    protected $casts = [
        'formulario' => 'array',    // JSON casting automático
        'attachments' => 'array',
    ];

    // Relaciones Eloquent
    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function history()
    {
        return $this->hasMany(SolicitudHistory::class);
    }

    // Boot Events para auditoría automática
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($solicitud) {
            SolicitudHistory::createEntry(
                $solicitud->id,
                $solicitud->user_id,
                'created',
                null,
                $solicitud->estado,
                'Solicitud creada por el ciudadano'
            );
        });
    }
}
```

---

## SLIDE TÉCNICA 3: SISTEMA DE ROLES Y PERMISOS

```php
// RoleAndPermissionSeeder.php
class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // Definición de permisos granulares
        $permissions = [
            'view_all_solicitudes',
            'create_solicitudes', 
            'edit_solicitudes',
            'approve_solicitudes',
            'access_admin_chat',
            'manage_system_settings'
        ];

        // Creación de roles con permisos específicos
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $functionalRole = Role::firstOrCreate(['name' => 'functionary']);
        $functionalRole->givePermissionTo([
            'view_all_solicitudes',
            'edit_solicitudes',
            'access_commission_chat'
        ]);

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->givePermissionTo([
            'view_own_solicitudes',
            'create_solicitudes'
        ]);
    }
}
```

**Middleware de Autorización:**
```php
// En routes/web.php
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});
```

---

## SLIDE TÉCNICA 4: FORMULARIOS DINÁMICOS

```json
// Configuración JSON en tabla tramites.configuracion_formulario
{
  "fields": [
    {
      "name": "nombre_completo",
      "type": "text",
      "required": true,
      "label": "Nombre completo del solicitante",
      "validation": "required|string|max:255"
    },
    {
      "name": "cedula",
      "type": "number", 
      "required": true,
      "label": "Número de cédula",
      "validation": "required|numeric|digits_between:6,8"
    },
    {
      "name": "motivo",
      "type": "textarea",
      "required": true,
      "label": "Motivo de la solicitud",
      "rows": 4,
      "validation": "required|string|min:10"
    },
    {
      "name": "documentos",
      "type": "file",
      "required": false,
      "label": "Documentos de respaldo",
      "accept": ".pdf,.jpg,.png,.doc,.docx",
      "multiple": true
    }
  ],
  "include_map": true,
  "map_required": true
}
```

**Renderizado Dinámico en Blade:**
```php
@foreach($tramite->configuracion_formulario['fields'] as $field)
    @if($field['type'] === 'text')
        <div class="form-group">
            <label>{{ $field['label'] }}</label>
            <input type="text" name="{{ $field['name'] }}" 
                   class="form-control" 
                   {{ $field['required'] ? 'required' : '' }}>
        </div>
    @elseif($field['type'] === 'file')
        <div class="form-group">
            <label>{{ $field['label'] }}</label>
            <input type="file" name="{{ $field['name'] }}[]" 
                   class="form-control-file" 
                   {{ $field['multiple'] ? 'multiple' : '' }}>
        </div>
    @endif
@endforeach
```

---

## SLIDE TÉCNICA 5: CHAT EN TIEMPO REAL CON EVENTSOURCE

```javascript
// Frontend JavaScript para chat en tiempo real
class ChatManager {
    constructor(solicitudId) {
        this.solicitudId = solicitudId;
        this.eventSource = null;
        this.init();
    }

    init() {
        // Establecer conexión EventSource
        this.eventSource = new EventSource(`/admin/chat/stream?room=SOL-${this.solicitudId}`);
        
        // Manejar mensajes entrantes
        this.eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data);
            this.displayMessage(data);
            this.updateUnreadCount();
        };

        // Manejar errores de conexión
        this.eventSource.onerror = (error) => {
            console.error('EventSource failed:', error);
            this.reconnect();
        };
    }

    sendMessage(message) {
        fetch(`/admin/chat/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                room: `SOL-${this.solicitudId}`,
                message: message,
                user_id: currentUserId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('messageInput').value = '';
            }
        });
    }
}
```

**Backend PHP para EventSource:**
```php
// ChatController.php - Método stream
public function stream(Request $request)
{
    $room = $request->get('room');
    
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');

    // Enviar datos en formato SSE
    while (true) {
        $messages = Chat::where('room', $room)
            ->where('created_at', '>', now()->subSeconds(30))
            ->with('user')
            ->get();

        foreach ($messages as $message) {
            echo "data: " . json_encode([
                'message' => $message->message,
                'user' => $message->user->name,
                'timestamp' => $message->created_at->format('H:i')
            ]) . "\n\n";
        }

        sleep(1); // Polling cada segundo
        
        if (connection_aborted()) break;
    }
}
```

---

## SLIDE TÉCNICA 6: GEOLOCALIZACIÓN CON LEAFLET

```javascript
// LeafletMapManager.js
class LeafletMapManager {
    constructor(containerId, options = {}) {
        this.map = null;
        this.marker = null;
        this.containerId = containerId;
        this.options = {
            defaultLat: -25.2637,  // Asunción, Paraguay
            defaultLng: -57.5759,
            zoom: 13,
            ...options
        };
        this.init();
    }

    init() {
        // Inicializar mapa Leaflet
        this.map = L.map(this.containerId).setView(
            [this.options.defaultLat, this.options.defaultLng], 
            this.options.zoom
        );

        // Agregar tiles de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(this.map);

        // Event listener para clicks en el mapa
        this.map.on('click', (e) => {
            this.setLocation(e.latlng.lat, e.latlng.lng);
        });
    }

    setLocation(lat, lng) {
        // Remover marker anterior si existe
        if (this.marker) {
            this.map.removeLayer(this.marker);
        }

        // Crear nuevo marker
        this.marker = L.marker([lat, lng], {
            icon: L.icon({
                iconUrl: '/images/marker-icon.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41]
            })
        }).addTo(this.map);

        // Actualizar inputs hidden del formulario
        document.getElementById('latitud').value = lat.toFixed(6);
        document.getElementById('longitud').value = lng.toFixed(6);

        // Trigger custom event
        document.dispatchEvent(new CustomEvent('locationSelected', {
            detail: { lat, lng }
        }));
    }
}

// Uso en formularios de trámites
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('map')) {
        const mapManager = new LeafletMapManager('map');
        
        // Auto-detectar ubicación del usuario si está disponible
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                mapManager.map.setView([lat, lng], 15);
            });
        }
    }
});
```

---

## SLIDE TÉCNICA 7: UPLOAD DE ARCHIVOS CON PREVIEW

```javascript
// FileUploadManager.js
class FileUploadManager {
    constructor(inputElement, previewContainer) {
        this.input = inputElement;
        this.preview = previewContainer;
        this.files = [];
        this.maxSize = 10 * 1024 * 1024; // 10MB
        this.allowedTypes = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword'];
        
        this.init();
    }

    init() {
        // Drag and drop events
        this.preview.addEventListener('dragover', this.handleDragOver.bind(this));
        this.preview.addEventListener('drop', this.handleDrop.bind(this));
        
        // Input change event
        this.input.addEventListener('change', this.handleFileSelect.bind(this));
    }

    handleFileSelect(e) {
        const files = Array.from(e.target.files);
        this.processFiles(files);
    }

    handleDrop(e) {
        e.preventDefault();
        const files = Array.from(e.dataTransfer.files);
        this.processFiles(files);
    }

    processFiles(files) {
        files.forEach(file => {
            if (this.validateFile(file)) {
                this.files.push(file);
                this.createPreview(file);
            }
        });
    }

    validateFile(file) {
        // Validar tipo
        if (!this.allowedTypes.includes(file.type)) {
            alert(`Tipo de archivo no permitido: ${file.type}`);
            return false;
        }

        // Validar tamaño
        if (file.size > this.maxSize) {
            alert(`Archivo demasiado grande: ${file.name}`);
            return false;
        }

        return true;
    }

    createPreview(file) {
        const previewElement = document.createElement('div');
        previewElement.className = 'file-preview-item';

        if (file.type.startsWith('image/')) {
            // Preview para imágenes
            const reader = new FileReader();
            reader.onload = (e) => {
                previewElement.innerHTML = `
                    <img src="${e.target.result}" alt="${file.name}">
                    <span class="file-name">${file.name}</span>
                    <button type="button" class="remove-file" data-filename="${file.name}">×</button>
                `;
            };
            reader.readAsDataURL(file);
        } else {
            // Preview para otros archivos
            previewElement.innerHTML = `
                <div class="file-icon">📄</div>
                <span class="file-name">${file.name}</span>
                <span class="file-size">${this.formatFileSize(file.size)}</span>
                <button type="button" class="remove-file" data-filename="${file.name}">×</button>
            `;
        }

        // Event listener para remover archivo
        previewElement.querySelector('.remove-file').addEventListener('click', () => {
            this.removeFile(file.name);
            previewElement.remove();
        });

        this.preview.appendChild(previewElement);
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}
```

---

## SLIDE TÉCNICA 8: RESPONSIVE DESIGN CON BOOTSTRAP 5

```css
/* CSS personalizado para diseño responsivo */
.solicitud-card {
    background: linear-gradient(145deg, #ffffff, #f8f9fa);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.solicitud-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

/* Breakpoints específicos */
@media (max-width: 576px) {
    .content-wrapper {
        margin-left: 0 !important;
        padding: 10px;
    }
    
    .solicitud-header h1 {
        font-size: 1.5rem;
    }
    
    .btn-group-mobile {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group-mobile .btn {
        margin-bottom: 5px;
        border-radius: 8px !important;
    }
}

@media (max-width: 768px) {
    .dashboard-stats {
        grid-template-columns: 1fr 1fr;
    }
    
    .chat-container {
        height: 60vh;
    }
    
    .dropdown-menu {
        position: fixed !important;
        top: auto !important;
        transform: none !important;
        width: 90vw;
        left: 5vw;
    }
}

@media (max-width: 992px) {
    .sidebar-collapse .main-sidebar {
        transform: translateX(-100%);
    }
    
    .form-row {
        flex-direction: column;
    }
    
    .col-form {
        width: 100%;
        margin-bottom: 1rem;
    }
}

/* Grid personalizado para formularios */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.form-grid-full {
    grid-column: 1 / -1;
}

/* Componentes interactivos */
.interactive-element {
    position: relative;
    overflow: hidden;
}

.interactive-element::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.interactive-element:hover::before {
    left: 100%;
}
```

---

## SLIDE TÉCNICA 9: OPTIMIZACIÓN Y PERFORMANCE

```php
// Optimizaciones en Controladores
class AdminSolicitudController extends Controller
{
    public function index(Request $request)
    {
        // Query optimization con eager loading
        $solicitudes = Solicitud::with(['user:id,name,email', 'tramite:id,nombre'])
            ->when($request->status, function ($query, $status) {
                return $query->where('estado', $status);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where('tracking_code', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15); // Paginación para mejor performance

        return view('admin.solicitudes.index', compact('solicitudes'));
    }

    // Caching para datos frecuentemente accedidos
    public function dashboard()
    {
        $stats = Cache::remember('dashboard_stats', 300, function () {
            return [
                'total_solicitudes' => Solicitud::count(),
                'pendientes' => Solicitud::where('estado', 'pendiente')->count(),
                'en_proceso' => Solicitud::where('estado', 'en_proceso')->count(),
                'completadas' => Solicitud::where('estado', 'completado')->count(),
                'solicitudes_hoy' => Solicitud::whereDate('created_at', today())->count(),
            ];
        });

        return view('admin.dashboard', compact('stats'));
    }
}
```

**Optimizaciones de Base de Datos:**
```sql
-- Índices para mejor performance
CREATE INDEX idx_solicitudes_estado ON solicitudes(estado);
CREATE INDEX idx_solicitudes_tracking ON solicitudes(tracking_code);
CREATE INDEX idx_solicitudes_user ON solicitudes(user_id);
CREATE INDEX idx_solicitudes_created ON solicitudes(created_at);
CREATE INDEX idx_chat_room ON chats(room);

-- Índice compuesto para consultas complejas
CREATE INDEX idx_solicitudes_estado_created ON solicitudes(estado, created_at);
```

---

## SLIDE TÉCNICA 10: SEGURIDAD Y VALIDACIÓN

```php
// Middleware personalizado para auditoría
class AuditMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Log de actividad para auditoría
        if (auth()->check()) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'route' => $request->route()->getName(),
                'method' => $request->method(),
                'created_at' => now()
            ]);
        }

        return $response;
    }
}

// Validación con Form Requests
class StoreSolicitudRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'tramite_id' => 'required|exists:tramites,id',
            'formulario' => 'required|array',
            'formulario.nombre_completo' => 'required|string|max:255',
            'formulario.cedula' => 'required|numeric|digits_between:6,8',
            'attachments.*' => 'file|mimes:pdf,jpg,png,doc,docx|max:10240',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
        ];
    }

    public function messages()
    {
        return [
            'tramite_id.required' => 'Debe seleccionar un tipo de trámite',
            'formulario.nombre_completo.required' => 'El nombre completo es obligatorio',
            'attachments.*.mimes' => 'Solo se permiten archivos PDF, JPG, PNG, DOC, DOCX',
            'attachments.*.max' => 'El archivo no debe superar los 10MB',
        ];
    }
}

// Sanitización de inputs
class DataSanitizer
{
    public static function sanitizeInput($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }

        return trim(strip_tags(htmlspecialchars($data, ENT_QUOTES, 'UTF-8')));
    }
}
```

---

Esta documentación técnica detallada muestra la implementación específica de cada componente del sistema MuniApp, destacando las mejores prácticas de desarrollo web con Laravel y las tecnologías complementarias utilizadas.
