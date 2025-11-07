@extends('layouts.admin')

@section('title', 'Ver Solicitud - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Solicitud #{{ $solicitud->tracking_code ?? $solicitud->id }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.solicitudes.index') }}">Solicitudes</a></li>
                    <li class="breadcrumb-item active">Ver</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <div class="col-md-8">
                <!-- Status and Actions -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-alt mr-1"></i>
                            {{ $solicitud->tramite->nombre ?? 'Trámite' }}
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-{{ $solicitud->status_color ?? 'secondary' }} badge-lg">
                                {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                            </span>
                            <div class="btn-group ml-2">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-cogs"></i> Acciones
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="{{ route('admin.solicitudes.edit', $solicitud->id) }}">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" onclick="updateStatus('en_revision')">
                                        <i class="fas fa-eye"></i> Marcar en Revisión
                                    </a>
                                    <a class="dropdown-item" href="#" onclick="updateStatus('en_proceso')">
                                        <i class="fas fa-cogs"></i> Marcar en Proceso
                                    </a>
                                    <a class="dropdown-item" href="#" onclick="updateStatus('completado')">
                                        <i class="fas fa-check"></i> Marcar Completado
                                    </a>
                                    <a class="dropdown-item text-danger" href="#" onclick="updateStatus('rechazado')">
                                        <i class="fas fa-times"></i> Rechazar
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" onclick="openChat()">
                                        <i class="fas fa-comments"></i> Abrir Chat
                                    </a>
                                    <a class="dropdown-item" href="#" onclick="generateReport()">
                                        <i class="fas fa-file-pdf"></i> Generar Reporte
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Fecha de Solicitud</span>
                                        <span class="info-box-number">{{ $solicitud->created_at->format('d/m/Y') }}</span>
                                        <small>{{ $solicitud->created_at->format('H:i') }}</small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> {{ $solicitud->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @php
                                    $priorityColors = [
                                        'low' => 'secondary',
                                        'medium' => 'primary',
                                        'high' => 'warning',
                                        'urgent' => 'danger'
                                    ];
                                @endphp
                                <div class="info-box">
                                    <span class="info-box-icon bg-{{ $priorityColors[$solicitud->priority ?? 'medium'] }}">
                                        <i class="fas fa-exclamation"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Prioridad</span>
                                        <span class="info-box-number">{{ ucfirst($solicitud->priority ?? 'medium') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Information -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user mr-1"></i>
                            Información del Solicitante
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <img src="{{ $solicitud->user->avatar_url ?? asset('images/default-avatar.png') }}" 
                                     class="img-circle img-fluid" alt="Avatar" style="width: 100px; height: 100px;">
                            </div>
                            <div class="col-md-9">
                                <dl class="row">
                                    <dt class="col-sm-3">Nombre:</dt>
                                    <dd class="col-sm-9">{{ $solicitud->nombre_completo ?? $solicitud->user->name }}</dd>
                                    
                                    <dt class="col-sm-3">Email:</dt>
                                    <dd class="col-sm-9">{{ $solicitud->user->email }}</dd>
                                    
                                    <dt class="col-sm-3">Teléfono:</dt>
                                    <dd class="col-sm-9">{{ $solicitud->telefono ?? 'No especificado' }}</dd>
                                    
                                    <dt class="col-sm-3">Dirección:</dt>
                                    <dd class="col-sm-9">{{ $solicitud->direccion ?? 'No especificada' }}</dd>
                                    
                                    <dt class="col-sm-3">Usuario desde:</dt>
                                    <dd class="col-sm-9">{{ $solicitud->user->created_at->format('d/m/Y') }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Request Details -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-text mr-1"></i>
                            Detalles de la Solicitud
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h5>Descripción:</h5>
                                <div class="border p-3 bg-light rounded">
                                    {{ $solicitud->detalles ?: 'No se proporcionó descripción' }}
                                </div>
                            </div>
                        </div>

                        @if($solicitud->comentario)
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <h5>Comentarios:</h5>
                                    <div class="border p-3 bg-light rounded">
                                        {{ $solicitud->comentario }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <h5>Información del Trámite:</h5>
                                <ul class="list-unstyled">
                                    <li><strong>Tipo:</strong> {{ $solicitud->tramite->nombre }}</li>
                                    @if($solicitud->tramite->department)
                                        <li><strong>Departamento:</strong> {{ $solicitud->tramite->department->name }}</li>
                                    @endif
                                    @if($solicitud->tramite->descripcion)
                                        <li><strong>Descripción:</strong> {{ $solicitud->tramite->descripcion }}</li>
                                    @endif
                                    @if($solicitud->tramite->costo)
                                        <li><strong>Costo:</strong> ${{ number_format($solicitud->tramite->costo, 2) }}</li>
                                    @endif
                                    @if($solicitud->tramite->tiempo_estimado)
                                        <li><strong>Tiempo Estimado:</strong> {{ $solicitud->tramite->tiempo_estimado }} días</li>
                                    @endif
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5>Asignación:</h5>
                                @if($solicitud->assignedUser)
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="{{ $solicitud->assignedUser->avatar_url }}" 
                                             class="img-circle img-size-32 mr-2" alt="Avatar">
                                        <div>
                                            <strong>{{ $solicitud->assignedUser->name }}</strong><br>
                                            <small class="text-muted">{{ $solicitud->assignedUser->email }}</small>
                                        </div>
                                    </div>
                                    @if($solicitud->assignedUser->department)
                                        <p><strong>Departamento:</strong> {{ $solicitud->assignedUser->department->name }}</p>
                                    @endif
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Esta solicitud no está asignada a ningún funcionario.
                                        <br>
                                        <button class="btn btn-sm btn-primary mt-2" onclick="showAssignModal()">
                                            <i class="fas fa-user-plus"></i> Asignar Ahora
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Datos del Formulario Dinámico -->
                @php
                    $formData = json_decode($solicitud->formulario, true);
                    $campos = $formData['campos'] ?? [];
                @endphp
                @if(count($campos) > 0)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-wpforms mr-1"></i>
                                Datos Enviados por el Ciudadano
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($campos as $index => $campo)
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">
                                                {{ $campo['nombre'] }}:
                                            </label>
                                            <div class="mt-2">
                                                @switch($campo['tipo'])
                                                    @case('file')
                                                        @if($campo['valor'])
                                                            <div class="file-preview border rounded p-3 bg-light">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-file-pdf fa-2x text-danger mr-3"></i>
                                                                    <div class="flex-grow-1">
                                                                        <strong>{{ basename($campo['valor']) }}</strong>
                                                                        <br>
                                                                        <small class="text-muted">Documento adjunto</small>
                                                                    </div>
                                                                    <a href="{{ url('storage/' . $campo['valor']) }}" 
                                                                       class="btn btn-sm btn-outline-primary" 
                                                                       target="_blank">
                                                                        <i class="fas fa-download me-1"></i> Descargar
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">No se adjuntó archivo</span>
                                                        @endif
                                                        @break
                                                        
                                                    @case('image')
                                                        @if($campo['valor'])
                                                            <div class="image-preview">
                                                                <img src="{{ url('storage/' . $campo['valor']) }}" 
                                                                     class="img-thumbnail mb-2" 
                                                                     style="max-width: 200px; max-height: 200px;"
                                                                     alt="Imagen adjunta">
                                                                <br>
                                                                <a href="{{ url('storage/' . $campo['valor']) }}" 
                                                                   class="btn btn-sm btn-outline-primary" 
                                                                   target="_blank">
                                                                    <i class="fas fa-eye me-1"></i> Ver tamaño completo
                                                                </a>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">No se adjuntó imagen</span>
                                                        @endif
                                                        @break
                                                        
                                                    @case('textarea')
                                                        <div class="border rounded p-3 bg-light">
                                                            {!! nl2br(e($campo['valor'])) !!}
                                                        </div>
                                                        @break
                                                        
                                                    @default
                                                        <div class="border rounded p-2 bg-light">
                                                            <strong>{{ $campo['valor'] ?: 'No especificado' }}</strong>
                                                        </div>
                                                @endswitch
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Ubicación de la Solicitud -->
                @if($solicitud->latitud && $solicitud->longitud)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                Ubicación de la Solicitud
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Coordenadas:</strong><br>
                                    <span class="text-muted">
                                        Latitud: {{ $solicitud->latitud }}<br>
                                        Longitud: {{ $solicitud->longitud }}
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <div id="location-address">
                                        <strong>Dirección:</strong><br>
                                        <span class="text-muted">Obteniendo dirección...</span>
                                    </div>
                                </div>
                            </div>
                            <div id="mapa" style="width: 100%; height: 400px; border-radius: 8px;"></div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Status Timeline -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history mr-1"></i>
                            Historial de Estados
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @php
                                $historialUnico = $solicitud->history->unique('id')->sortBy('created_at');
                            @endphp
                            @forelse($historialUnico as $historial)
                                <div class="time-label">
                                    <span class="bg-{{ $historial->status_color ?? 'primary' }}">
                                        {{ $historial->created_at->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div>
                                    <i class="fas fa-{{ $historial->icon ?? 'circle' }} bg-{{ $historial->status_color ?? 'primary' }}"></i>
                                    <div class="timeline-item">
                                        <span class="time">
                                            <i class="fas fa-clock"></i> {{ $historial->created_at->format('H:i') }}
                                            <small class="text-muted ml-2">
                                                ({{ $historial->created_at->diffForHumans() }})
                                            </small>
                                        </span>
                                        <h3 class="timeline-header">
                                            {{ ucfirst(str_replace('_', ' ', $historial->action ?? 'Estado actualizado')) }}
                                        </h3>
                                        @if($historial->description)
                                            <div class="timeline-body">
                                                {{ $historial->description }}
                                            </div>
                                        @endif
                                        @if($historial->user)
                                            <div class="timeline-footer">
                                                <small class="text-muted">Por: {{ $historial->user->name }}</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted">
                                    <i class="fas fa-history fa-2x mb-3"></i>
                                    <p>No hay historial disponible</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Estadísticas Rápidas
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header">{{ $solicitud->created_at->diffInDays(now()) }}</h5>
                                    <span class="description-text">Días desde creación</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header">{{ $solicitud->updated_at->diffInDays(now()) }}</h5>
                                    <span class="description-text">Días sin actualizar</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header">{{ $solicitud->history->count() }}</h5>
                                    <span class="description-text">Cambios de estado</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header">{{ count(json_decode($solicitud->formulario, true)['campos'] ?? []) }}</h5>
                                    <span class="description-text">Campos del formulario</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-navigation mr-1"></i>
                            Navegación
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-list"></i> Volver a la Lista
                            </a>
                            <a href="{{ route('admin.solicitudes.edit', $solicitud->id) }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-edit"></i> Editar Solicitud
                            </a>
                            <button type="button" class="btn btn-outline-success btn-block" onclick="openChat()">
                                <i class="fas fa-comments"></i> Abrir Chat
                            </button>
                            <button type="button" class="btn btn-outline-info btn-block" onclick="generateReport()">
                                <i class="fas fa-file-pdf"></i> Generar Reporte
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Actualizar Estado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="status_comments">Comentarios</label>
                    <textarea class="form-control" id="status_comments" rows="3" 
                              placeholder="Comentarios sobre el cambio de estado..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmStatusUpdate">Actualizar</button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignModalLabel">Asignar Solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="assign_user">Asignar a</label>
                    <select class="form-control" id="assign_user">
                        <option value="">Seleccionar usuario...</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="assign_comments">Comentarios</label>
                    <textarea class="form-control" id="assign_comments" rows="3" 
                              placeholder="Comentarios sobre la asignación..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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
.file-preview {
    transition: all 0.3s ease;
}

.file-preview:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.image-preview img {
    transition: transform 0.2s ease;
}

.image-preview img:hover {
    transform: scale(1.05);
}

.leaflet-container {
    border: 2px solid #dee2e6;
    border-radius: 8px !important;
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
        // Inicializar mapa de visualización si hay coordenadas
        console.log('Inicializando mapa de visualización...');
        
        const initDisplayMap = () => {
            if (window.LeafletMapManager) {
                const lat = {{ $solicitud->latitud }};
                const lng = {{ $solicitud->longitud }};
                
                const map = window.LeafletMapManager.initDisplayMap('mapa', lat, lng, {
                    zoom: 15
                });
                
                if (map) {
                    console.log('Mapa de visualización inicializado correctamente');
                    
                    // Obtener dirección usando geocodificación inversa
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.display_name) {
                                document.getElementById('location-address').innerHTML = 
                                    `<strong>Dirección:</strong><br><span class="text-muted">${data.display_name}</span>`;
                            } else {
                                document.getElementById('location-address').innerHTML = 
                                    `<strong>Ubicación:</strong><br><span class="text-muted">Lat: ${lat}, Lng: ${lng}</span>`;
                            }
                        })
                        .catch(error => {
                            console.error('Error obteniendo dirección:', error);
                            document.getElementById('location-address').innerHTML = 
                                `<strong>Ubicación:</strong><br><span class="text-muted">Lat: ${lat}, Lng: ${lng}</span>`;
                        });
                } else {
                    console.error('Error inicializando el mapa de visualización');
                }
            } else {
                console.log('Esperando a que se cargue LeafletMapManager...');
                setTimeout(initDisplayMap, 100);
            }
        };
        
        initDisplayMap();
    @endif
});

// Funciones para gestión de solicitudes
// Funciones para gestión de solicitudes
let currentStatus = null;

function updateStatus(status) {
    currentStatus = status;
    const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
    statusModal.show();
}

function showAssignModal() {
    const assignModal = new bootstrap.Modal(document.getElementById('assignModal'));
    assignModal.show();
}

document.getElementById('confirmStatusUpdate').addEventListener('click', function() {
    const comments = document.getElementById('status_comments').value;
    
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
        const statusModal = bootstrap.Modal.getInstance(document.getElementById('statusModal'));
        statusModal.hide();
        
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el estado');
    });
});

document.getElementById('confirmAssign').addEventListener('click', function() {
    const userId = document.getElementById('assign_user').value;
    const comments = document.getElementById('assign_comments').value;
    
    if (!userId) {
        alert('Selecciona un usuario');
        return;
    }
    
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
        const assignModal = bootstrap.Modal.getInstance(document.getElementById('assignModal'));
        assignModal.hide();
        
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al asignar la solicitud');
    });
});

function openChat() {
    const solicitudId = {{ $solicitud->id }};
    window.open(`/admin/chat/solicitud?room=SOL-${solicitudId}&solicitud_id=${solicitudId}`, '_blank');
}

function generateReport() {
    window.open(`/admin/solicitudes/{{ $solicitud->id }}/report`, '_blank');
}
</script>
@endpush