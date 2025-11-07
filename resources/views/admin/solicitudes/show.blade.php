@extends('layouts.admin')

@section('title', 'Solicitud #' . ($solicitud->tracking_code ?? $solicitud->id) . ' - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-8">
                <h1 class="m-0 d-flex align-items-center">
                    <span class="badge badge-{{ $solicitud->status_color ?? 'secondary' }} badge-lg mr-3">
                        {{ strtoupper(str_replace('_', ' ', $solicitud->estado)) }}
                    </span>
                    Solicitud #{{ $solicitud->tracking_code ?? $solicitud->id }}
                </h1>
                <p class="text-muted mb-0">
                    <i class="fas fa-file-alt mr-1"></i>
                    {{ $solicitud->tramite->nombre ?? 'Trámite' }}
                    <span class="mx-2">•</span>
                    <i class="fas fa-calendar mr-1"></i>
                    {{ $solicitud->created_at->format('d/m/Y H:i') }}
                    <span class="mx-2">•</span>
                    <i class="fas fa-clock mr-1"></i>
                    {{ $solicitud->created_at->diffForHumans() }}
                </p>
            </div>
            <div class="col-sm-4">
                <div class="dropdown float-right">
                    <button type="button" class="btn btn-primary dropdown-toggle" id="actionsDropdown" onclick="toggleActionsDropdown()">
                        <i class="fas fa-cogs"></i> Acciones
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" id="actionsDropdownMenu" style="display: none;">
                        <a class="dropdown-item" href="#" onclick="updateStatus('en_revision'); hideDropdown();">
                            <i class="fas fa-eye text-info mr-2"></i> En Revisión
                        </a>
                        <a class="dropdown-item" href="#" onclick="updateStatus('en_proceso'); hideDropdown();">
                            <i class="fas fa-cogs text-warning mr-2"></i> En Proceso
                        </a>
                        <a class="dropdown-item" href="#" onclick="updateStatus('completado'); hideDropdown();">
                            <i class="fas fa-check text-success mr-2"></i> Completado
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="#" onclick="updateStatus('rechazado'); hideDropdown();">
                            <i class="fas fa-times mr-2"></i> Rechazar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Contenido Principal -->
            <div class="col-lg-8">
                
                <!-- Información del Solicitante (Mejorada) -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-circle mr-2"></i>
                            Información del Solicitante
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="media">
                            <img src="{{ $solicitud->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($solicitud->user->name ?? 'Usuario') . '&background=007bff&color=ffffff&size=128' }}" 
                                 class="mr-3 rounded-circle" alt="Avatar" style="width: 64px; height: 64px;">
                            <div class="media-body">
                                <h5 class="mt-0 mb-1">{{ $solicitud->user->name ?? 'Usuario sin nombre' }}</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1">
                                            <i class="fas fa-envelope text-muted mr-2"></i>
                                            <a href="mailto:{{ $solicitud->user->email }}">{{ $solicitud->user->email }}</a>
                                        </p>
                                        @if($solicitud->telefono)
                                        <p class="mb-1">
                                            <i class="fas fa-phone text-muted mr-2"></i>
                                            {{ $solicitud->telefono }}
                                        </p>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        @if($solicitud->direccion)
                                        <p class="mb-1">
                                            <i class="fas fa-map-marker-alt text-muted mr-2"></i>
                                            {{ $solicitud->direccion }}
                                        </p>
                                        @endif
                                        <p class="mb-0 text-muted">
                                            <i class="fas fa-user-clock mr-2"></i>
                                            Cliente desde {{ $solicitud->user->created_at->format('M Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalles de la Solicitud (Simplificado) -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-text mr-2"></i>
                            Detalles de la Solicitud
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($solicitud->detalles)
                        <div class="alert alert-light border-left-primary">
                            <h6 class="alert-heading">
                                <i class="fas fa-comment-alt mr-2"></i>Descripción
                            </h6>
                            <p class="mb-0">{{ $solicitud->detalles }}</p>
                        </div>
                        @endif
                        
                        @if($solicitud->comentario)
                        <div class="alert alert-light border-left-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-sticky-note mr-2"></i>Comentarios Adicionales
                            </h6>
                            <p class="mb-0">{{ $solicitud->comentario }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Datos del Formulario (Mejorado) -->
                @php
                    $formData = json_decode($solicitud->formulario, true);
                    $campos = $formData['campos'] ?? [];
                @endphp
                @if(count($campos) > 0)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-wpforms mr-2"></i>
                            Información Proporcionada
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($campos as $index => $campo)
                            <div class="col-lg-6 mb-4">
                                <div class="form-group">
                                    <label class="text-primary font-weight-bold mb-2">
                                        <i class="fas fa-tag mr-1"></i>
                                        {{ $campo['nombre'] }}
                                    </label>
                                    
                                    @switch($campo['tipo'])
                                        @case('file')
                                            @if($campo['valor'])
                                            <div class="card border-0 bg-light">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="file-icon mr-3">
                                                            <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">{{ basename($campo['valor']) }}</h6>
                                                            <small class="text-muted">Documento adjunto</small>
                                                        </div>
                                                        <a href="{{ url('storage/' . $campo['valor']) }}" 
                                                           class="btn btn-sm btn-outline-primary" 
                                                           target="_blank">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            @else
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                No se adjuntó archivo
                                            </p>
                                            @endif
                                            @break
                                            
                                        @case('image')
                                            @if($campo['valor'])
                                            <div class="text-center">
                                                <img src="{{ url('storage/' . $campo['valor']) }}" 
                                                     class="img-thumbnail shadow-sm" 
                                                     style="max-width: 250px; max-height: 200px; cursor: pointer;"
                                                     alt="Imagen adjunta"
                                                     onclick="showImageModal('{{ url('storage/' . $campo['valor']) }}', '{{ $campo['nombre'] }}')">
                                                <div class="mt-2">
                                                    <small class="text-muted">Clic para ver en tamaño completo</small>
                                                </div>
                                            </div>
                                            @else
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                No se adjuntó imagen
                                            </p>
                                            @endif
                                            @break
                                            
                                        @case('textarea')
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-3">
                                                {!! nl2br(e($campo['valor'])) !!}
                                            </div>
                                        </div>
                                        @break
                                        
                                        @default
                                        <div class="form-control-plaintext bg-light rounded p-2">
                                            <strong>{{ $campo['valor'] ?: 'No especificado' }}</strong>
                                        </div>
                                    @endswitch
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Ubicación (Mejorada) -->
                @if($solicitud->latitud && $solicitud->longitud)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            Ubicación Reportada
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <div id="location-address" class="text-muted">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                    Obteniendo dirección...
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <button class="btn btn-sm btn-outline-primary" onclick="openInMaps()">
                                    <i class="fas fa-external-link-alt mr-1"></i>
                                    Abrir en Google Maps
                                </button>
                            </div>
                        </div>
                        <div id="mapa" style="width: 100%; height: 300px; border-radius: 8px; border: 2px solid #dee2e6;"></div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Barra Lateral -->
            <div class="col-lg-4">
                
                <!-- Resumen Rápido -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-2"></i>
                            Resumen
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-right">
                                    <h4 class="text-primary">{{ $solicitud->created_at->diffInDays(now()) }}</h4>
                                    <small class="text-muted">Días transcurridos</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-{{ $solicitud->priority === 'high' || $solicitud->priority === 'urgent' ? 'danger' : 'info' }}">
                                    {{ strtoupper($solicitud->priority ?? 'MEDIUM') }}
                                </h4>
                                <small class="text-muted">Prioridad</small>
                            </div>
                        </div>
                        
                        @if($solicitud->assignedUser)
                        <hr>
                        <div class="text-center">
                            <h6 class="text-muted">Asignado a:</h6>
                            <div class="d-flex align-items-center justify-content-center">
                                <img src="{{ $solicitud->assignedUser->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($solicitud->assignedUser->name) . '&background=28a745&color=ffffff&size=64' }}" 
                                     class="rounded-circle mr-2" style="width: 32px; height: 32px;">
                                <span class="font-weight-bold">{{ $solicitud->assignedUser->name }}</span>
                            </div>
                        </div>
                        @else
                        <hr>
                        <div class="text-center">
                            <button class="btn btn-warning btn-sm" onclick="showAssignModal()">
                                <i class="fas fa-user-plus mr-1"></i>
                                Asignar Funcionario
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt mr-2"></i>
                            Acciones Rápidas
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block" onclick="openChat()">
                                <i class="fas fa-comments mr-2"></i>
                                Chatear con Ciudadano
                            </button>
                            <button type="button" class="btn btn-outline-info btn-block" onclick="generateReport()">
                                <i class="fas fa-file-pdf mr-2"></i>
                                Generar Reporte
                            </button>
                            <a href="{{ route('admin.solicitudes.edit', $solicitud->id) }}" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-edit mr-2"></i>
                                Editar Solicitud
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Historial Simplificado -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history mr-2"></i>
                            Historial Reciente
                        </h3>
                    </div>
                    <div class="card-body">
                        @php
                            $historialReciente = $solicitud->history->sortByDesc('created_at')->take(3);
                        @endphp
                        @forelse($historialReciente as $historial)
                        <div class="d-flex mb-3">
                            <div class="mr-3">
                                <span class="badge badge-{{ $historial->status_color ?? 'primary' }} badge-circle">
                                    <i class="fas fa-{{ $historial->icon ?? 'circle' }}"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $historial->action ?? 'Actualización')) }}</h6>
                                <small class="text-muted">
                                    {{ $historial->created_at->format('d/m/Y H:i') }}
                                    @if($historial->user)
                                    • {{ $historial->user->name }}
                                    @endif
                                </small>
                                @if($historial->description)
                                <p class="text-muted small mb-0 mt-1">{{ $historial->description }}</p>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted">
                            <i class="fas fa-history fa-2x mb-2"></i>
                            <p class="mb-0">Sin historial</p>
                        </div>
                        @endforelse
                        
                        @if($solicitud->history->count() > 3)
                        <div class="text-center mt-3">
                            <a href="#" class="text-muted small" onclick="showFullHistory()">
                                Ver historial completo ({{ $solicitud->history->count() }} registros)
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Navegación -->
                <div class="text-center">
                    <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver a la Lista
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal para ver imagen en tamaño completo -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Vista de Imagen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Imagen">
            </div>
        </div>
    </div>
</div>

<!-- Modal para cambio de estado -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Actualizar Estado</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="status_comments">Comentarios del cambio</label>
                    <textarea class="form-control" id="status_comments" rows="3" 
                              placeholder="Describe el motivo del cambio de estado..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmStatusUpdate">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para asignación -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignModalLabel">Asignar Funcionario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="assign_user">Funcionario</label>
                    <select class="form-control" id="assign_user">
                        <option value="">Seleccionar funcionario...</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="assign_comments">Instrucciones</label>
                    <textarea class="form-control" id="assign_comments" rows="3" 
                              placeholder="Instrucciones para el funcionario asignado..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmAssign">Asignar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>
      
<style>
/* Mejoras estéticas generales */
.badge-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.border-left-primary {
    border-left: 4px solid #007bff !important;
}

.border-left-info {
    border-left: 4px solid #17a2b8 !important;
}

/* Efectos hover para cards */
.card {
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.125);
}

.card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

/* Mejoras para archivos adjuntos */
.file-icon {
    transition: transform 0.2s ease;
}

.file-icon:hover {
    transform: scale(1.1);
}

/* Estilo para imágenes */
.img-thumbnail {
    transition: all 0.3s ease;
    cursor: pointer;
}

.img-thumbnail:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Mapa mejorado */
.leaflet-container {
    border-radius: 8px !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Badge personalizado */
.badge-lg {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}

/* Efectos para botones */
.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

/* Estilo para el historial */
.timeline-item {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    border-left: 4px solid #007bff;
    transition: all 0.3s ease;
}

.timeline-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

/* Responsive improvements */
@media (max-width: 768px) {
    .card:hover {
        transform: none;
    }
    
    .img-thumbnail:hover {
        transform: none;
    }
}

/* Dropdown fixes y mejoras completas */
.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    z-index: 1050;
    min-width: 220px;
    padding: 0.5rem 0;
    margin: 0.125rem 0 0;
    color: #212529;
    text-align: left;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0,0,0,.15);
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.175);
    display: none;
}

.dropdown-menu.dropdown-menu-right {
    right: 0;
    left: auto;
}

.dropdown-item {
    display: block;
    width: 100%;
    padding: 0.5rem 1rem;
    clear: both;
    font-weight: 400;
    color: #212529;
    text-align: inherit;
    text-decoration: none;
    white-space: nowrap;
    background-color: transparent;
    border: 0;
    cursor: pointer;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
}

.dropdown-item:hover,
.dropdown-item:focus {
    color: #16181b;
    background-color: #f8f9fa;
    text-decoration: none;
}

.dropdown-item:active {
    background-color: #007bff;
    color: #fff;
}

.dropdown-divider {
    height: 0;
    margin: 0.5rem 0;
    overflow: hidden;
    border-top: 1px solid #dee2e6;
}

.dropdown-toggle::after {
    display: inline-block;
    margin-left: 0.255em;
    vertical-align: 0.255em;
    content: "";
    border-top: 0.3em solid;
    border-right: 0.3em solid transparent;
    border-bottom: 0;
    border-left: 0.3em solid transparent;
}

.dropdown-item:hover,
.dropdown-item:focus {
    color: #16181b;
    background-color: #f8f9fa;
    text-decoration: none;
}

.dropdown-divider {
    height: 0;
    margin: 0.5rem 0;
    overflow: hidden;
    border-top: 1px solid #dee2e6;
}

/* Alert mejorado */
.alert {
    border-radius: 10px;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Media object mejorado */
.media {
    padding: 1rem;
    border-radius: 8px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}
</style>
@endpush

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<!-- Leaflet Map Manager -->
<script src="{{ asset('js/leaflet-map-manager.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($solicitud->latitud && $solicitud->longitud)
        // Inicializar mapa de visualización
        const initDisplayMap = () => {
            if (window.LeafletMapManager) {
                const lat = {{ $solicitud->latitud }};
                const lng = {{ $solicitud->longitud }};
                
                const map = window.LeafletMapManager.initDisplayMap('mapa', lat, lng, {
                    zoom: 16
                });
                
                if (map) {
                    console.log('✅ Mapa de visualización inicializado correctamente');
                    
                    // Obtener dirección
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.display_name) {
                                document.getElementById('location-address').innerHTML = 
                                    `<i class="fas fa-map-marker-alt text-primary mr-2"></i>${data.display_name}`;
                            } else {
                                document.getElementById('location-address').innerHTML = 
                                    `<i class="fas fa-map-marker-alt text-primary mr-2"></i>Lat: ${lat}, Lng: ${lng}`;
                            }
                        })
                        .catch(error => {
                            console.error('Error obteniendo dirección:', error);
                            document.getElementById('location-address').innerHTML = 
                                `<i class="fas fa-map-marker-alt text-primary mr-2"></i>Lat: ${lat}, Lng: ${lng}`;
                        });
                } else {
                    console.error('❌ Error inicializando el mapa');
                }
            } else {
                setTimeout(initDisplayMap, 100);
            }
        };
        
        initDisplayMap();
    @endif
});

// Variables globales
let currentStatus = null;

// Funciones del dropdown de acciones
function toggleActionsDropdown() {
    const dropdown = document.getElementById('actionsDropdownMenu');
    const isVisible = dropdown.style.display === 'block';
    
    // Cerrar todos los dropdowns primero
    hideAllDropdowns();
    
    // Toggle del dropdown actual
    if (!isVisible) {
        dropdown.style.display = 'block';
        dropdown.style.position = 'absolute';
        dropdown.style.top = '100%';
        dropdown.style.right = '0';
        dropdown.style.zIndex = '1050';
    }
}

function hideDropdown() {
    const dropdown = document.getElementById('actionsDropdownMenu');
    dropdown.style.display = 'none';
}

function hideAllDropdowns() {
    const dropdowns = document.querySelectorAll('.dropdown-menu');
    dropdowns.forEach(dropdown => {
        dropdown.style.display = 'none';
    });
}

// Cerrar dropdown cuando se hace click fuera
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('actionsDropdown');
    const dropdownMenu = document.getElementById('actionsDropdownMenu');
    
    if (!dropdown.contains(event.target) && !dropdownMenu.contains(event.target)) {
        hideDropdown();
    }
});

// Funciones de modal
function showImageModal(src, title) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModalLabel').textContent = title;
    $('#imageModal').modal('show');
}

function updateStatus(status) {
    currentStatus = status;
    hideDropdown(); // Cerrar el dropdown primero
    $('#statusModal').modal('show');
}

function showAssignModal() {
    $('#assignModal').modal('show');
}

function openInMaps() {
    const lat = {{ $solicitud->latitud ?? 0 }};
    const lng = {{ $solicitud->longitud ?? 0 }};
    window.open(`https://www.google.com/maps?q=${lat},${lng}`, '_blank');
}

// Event listeners
document.getElementById('confirmStatusUpdate')?.addEventListener('click', function() {
    const comments = document.getElementById('status_comments').value;
    
    // Mostrar loading
    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Actualizando...';
    this.disabled = true;
    
    fetch(`/admin/solicitudes/{{ $solicitud->id }}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            status: currentStatus,
            comments: comments
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Error desconocido'));
            this.innerHTML = 'Confirmar';
            this.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el estado');
        this.innerHTML = 'Confirmar';
        this.disabled = false;
    });
});

document.getElementById('confirmAssign')?.addEventListener('click', function() {
    const userId = document.getElementById('assign_user').value;
    const comments = document.getElementById('assign_comments').value;
    
    if (!userId) {
        alert('Por favor selecciona un funcionario');
        return;
    }
    
    // Mostrar loading
    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Asignando...';
    this.disabled = true;
    
    fetch(`/admin/solicitudes/{{ $solicitud->id }}/assign`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            user_id: userId,
            comments: comments
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Error desconocido'));
            this.innerHTML = 'Asignar';
            this.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al asignar la solicitud');
        this.innerHTML = 'Asignar';
        this.disabled = false;
    });
});

// Funciones auxiliares
function openChat() {
    const solicitudId = {{ $solicitud->id }};
    const url = `/admin/chat/solicitud?room=SOL-${solicitudId}&solicitud_id=${solicitudId}`;
    window.location.href = url; // Cambio: abrir en la misma página
}

function generateReport() {
    window.open(`/admin/solicitudes/{{ $solicitud->id }}/report`, '_blank');
}

function showFullHistory() {
    // Implementar modal o página para historial completo
    alert('Funcionalidad de historial completo en desarrollo');
}

// Tooltips y inicialización
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips si están disponibles
    if (typeof $ !== 'undefined' && $.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }
    
    console.log('✅ Página de solicitud cargada correctamente');
});
</script>
@endpush