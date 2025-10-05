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
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-cogs"></i> Acciones
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ route('admin.solicitudes.edit', $solicitud) }}">
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
                                    {{ $solicitud->detalle }}
                                </div>
                            </div>
                        </div>

                        @if($solicitud->observaciones)
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <h5>Observaciones:</h5>
                                    <div class="border p-3 bg-light rounded">
                                        {{ $solicitud->observaciones }}
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

                <!-- Attachments -->
                @if($solicitud->adjuntos && count($solicitud->adjuntos) > 0)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-paperclip mr-1"></i>
                                Archivos Adjuntos
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($solicitud->adjuntos as $archivo)
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <i class="fas fa-file fa-3x text-primary mb-2"></i>
                                                <h6 class="card-title">{{ $archivo->nombre ?? 'archivo.pdf' }}</h6>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        Tamaño: {{ $archivo->tamaño ?? '0' }} KB<br>
                                                        Subido: {{ $archivo->created_at->format('d/m/Y') ?? 'N/A' }}
                                                    </small>
                                                </p>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ $archivo->url ?? '#' }}" class="btn btn-sm btn-primary" target="_blank">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                    <a href="{{ $archivo->download_url ?? '#' }}" class="btn btn-sm btn-success">
                                                        <i class="fas fa-download"></i> Descargar
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
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
                            @forelse($solicitud->historial ?? [] as $historial)
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
                                        </span>
                                        <h3 class="timeline-header">
                                            {{ ucfirst(str_replace('_', ' ', $historial->status ?? 'Estado actualizado')) }}
                                        </h3>
                                        @if($historial->comments)
                                            <div class="timeline-body">
                                                {{ $historial->comments }}
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
                                    <h5 class="description-header">{{ $solicitud->days_since_created ?? 0 }}</h5>
                                    <span class="description-text">Días desde creación</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header">{{ $solicitud->days_since_updated ?? 0 }}</h5>
                                    <span class="description-text">Días sin actualizar</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header">{{ $solicitud->total_comments ?? 0 }}</h5>
                                    <span class="description-text">Comentarios</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header">{{ $solicitud->file_count ?? 0 }}</h5>
                                    <span class="description-text">Archivos</span>
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
                            <a href="{{ route('admin.solicitudes.edit', $solicitud) }}" class="btn btn-outline-primary btn-block">
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
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Actualizar Estado</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="status_comments">Comentarios</label>
                    <textarea class="form-control" id="status_comments" rows="3" 
                              placeholder="Comentarios sobre el cambio de estado..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmStatusUpdate">Actualizar</button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Asignar Solicitud</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="assign_user">Asignar a</label>
                    <select class="form-control" id="assign_user">
                        <option value="">Seleccionar usuario...</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="assign_comments">Comentarios</label>
                    <textarea class="form-control" id="assign_comments" rows="3" 
                              placeholder="Comentarios sobre la asignación..."></textarea>
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

@push('scripts')
<script>
let currentStatus = null;

function updateStatus(status) {
    currentStatus = status;
    $('#statusModal').modal('show');
}

function showAssignModal() {
    $('#assignModal').modal('show');
}

$('#confirmStatusUpdate').click(function() {
    const comments = $('#status_comments').val();
    
    $.ajax({
        url: `/admin/solicitudes/{{ $solicitud->id }}/status`,
        method: 'PATCH',
        data: {
            _token: '{{ csrf_token() }}',
            status: currentStatus,
            comments: comments
        },
        success: function(response) {
            $('#statusModal').modal('hide');
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error al actualizar el estado');
        }
    });
});

$('#confirmAssign').click(function() {
    const userId = $('#assign_user').val();
    const comments = $('#assign_comments').val();
    
    if (!userId) {
        alert('Selecciona un usuario');
        return;
    }
    
    $.ajax({
        url: `/admin/solicitudes/{{ $solicitud->id }}/assign`,
        method: 'PATCH',
        data: {
            _token: '{{ csrf_token() }}',
            user_id: userId,
            comments: comments
        },
        success: function(response) {
            $('#assignModal').modal('hide');
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error al asignar la solicitud');
        }
    });
});

function openChat() {
    const solicitudId = {{ $solicitud->id }};
    window.open(`/chat?type=solicitud&room=SOL-${solicitudId}`, '_blank');
}

function generateReport() {
    window.open(`/admin/solicitudes/{{ $solicitud->id }}/report`, '_blank');
}
</script>
@endpush