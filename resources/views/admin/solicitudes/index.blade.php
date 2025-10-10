@extends('layouts.admin')

@section('title', 'Gestión de Solicitudes - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Solicitudes</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Solicitudes</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Filters and Search -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.solicitudes.index') }}">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="search">Buscar</label>
                                        <input type="text" name="search" id="search" class="form-control" 
                                               placeholder="Código o nombre..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="status">Estado</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="">Todos</option>
                                            <option value="recibido" {{ request('status') == 'recibido' ? 'selected' : '' }}>Recibido</option>
                                            <option value="en_revision" {{ request('status') == 'en_revision' ? 'selected' : '' }}>En Revisión</option>
                                            <option value="en_proceso" {{ request('status') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                            <option value="completado" {{ request('status') == 'completado' ? 'selected' : '' }}>Completado</option>
                                            <option value="rechazado" {{ request('status') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="tramite">Trámite</label>
                                        <select name="tramite" id="tramite" class="form-control">
                                            <option value="">Todos</option>
                                            @foreach($tramites ?? [] as $tramite)
                                                <option value="{{ $tramite->id }}" {{ request('tramite') == $tramite->id ? 'selected' : '' }}>
                                                    {{ $tramite->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="priority">Prioridad</label>
                                        <select name="priority" id="priority" class="form-control">
                                            <option value="">Todas</option>
                                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Baja</option>
                                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Media</option>
                                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Alta</option>
                                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgente</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search"></i> Buscar
                                            </button>
                                            <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Solicitudes Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-1"></i>
                            Lista de Solicitudes
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-info" onclick="assignSelected()">
                                <i class="fas fa-user-plus"></i> Asignar Seleccionadas
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>Código</th>
                                    <th>Usuario</th>
                                    <th>Trámite</th>
                                    <th>Datos del Formulario</th>
                                    <th>Estado</th>
                                    <th>Prioridad</th>
                                    <th>Asignado a</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($solicitudes ?? [] as $solicitud)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="solicitud-checkbox" value="{{ $solicitud->id }}">
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $solicitud->tracking_code ?? 'SOL-' . $solicitud->id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $solicitud->user->avatar_url ?? asset('images/default-avatar.png') }}" 
                                                 class="img-circle img-size-32 mr-2" alt="Avatar">
                                            <div>
                                                <strong>{{ $solicitud->user->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $solicitud->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $solicitud->tramite->nombre }}</strong>
                                        @if($solicitud->tramite->department)
                                            <br>
                                            <small class="text-muted">{{ $solicitud->tramite->department->name }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $formData = json_decode($solicitud->formulario, true);
                                            $campos = $formData['campos'] ?? [];
                                        @endphp
                                        @if(count($campos) > 0)
                                            <div class="form-data-summary">
                                                @foreach(array_slice($campos, 0, 3) as $campo)
                                                    <div class="mb-1">
                                                        <small class="text-muted">{{ $campo['nombre'] }}:</small>
                                                        <br>
                                                        <span style="font-size: 0.85em;">
                                                            @if($campo['tipo'] === 'file' || $campo['tipo'] === 'image')
                                                                <i class="fas fa-paperclip text-primary"></i> 
                                                                {{ basename($campo['valor']) }}
                                                            @else
                                                                {{ Str::limit($campo['valor'], 30) }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endforeach
                                                @if(count($campos) > 3)
                                                    <small class="text-muted">
                                                        <i class="fas fa-plus"></i> 
                                                        {{ count($campos) - 3 }} más...
                                                    </small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Sin datos adicionales</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <span class="badge badge-{{ $solicitud->status_color }} dropdown-toggle" 
                                                  data-toggle="dropdown" style="cursor: pointer;">
                                                {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                                            </span>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#" 
                                                   onclick="updateStatus({{ $solicitud->id }}, 'recibido')">
                                                    <span class="badge badge-secondary">Recibido</span>
                                                </a>
                                                <a class="dropdown-item" href="#" 
                                                   onclick="updateStatus({{ $solicitud->id }}, 'en_revision')">
                                                    <span class="badge badge-warning">En Revisión</span>
                                                </a>
                                                <a class="dropdown-item" href="#" 
                                                   onclick="updateStatus({{ $solicitud->id }}, 'en_proceso')">
                                                    <span class="badge badge-info">En Proceso</span>
                                                </a>
                                                <a class="dropdown-item" href="#" 
                                                   onclick="updateStatus({{ $solicitud->id }}, 'completado')">
                                                    <span class="badge badge-success">Completado</span>
                                                </a>
                                                <a class="dropdown-item" href="#" 
                                                   onclick="updateStatus({{ $solicitud->id }}, 'rechazado')">
                                                    <span class="badge badge-danger">Rechazado</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $priorityColors = [
                                                'low' => 'secondary',
                                                'medium' => 'primary',
                                                'high' => 'warning',
                                                'urgent' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $priorityColors[$solicitud->priority ?? 'medium'] }}">
                                            {{ ucfirst($solicitud->priority ?? 'medium') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($solicitud->assignedUser)
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $solicitud->assignedUser->avatar_url }}" 
                                                     class="img-circle img-size-24 mr-1" alt="Avatar">
                                                <small>{{ $solicitud->assignedUser->name }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $solicitud->created_at->format('d/m/Y') }}
                                        <br>
                                        <small class="text-muted">{{ $solicitud->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.solicitudes.show', $solicitud) }}" 
                                               class="btn btn-sm btn-outline-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.solicitudes.edit', $solicitud) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="openChat({{ $solicitud->id }})" title="Chat">
                                                <i class="fas fa-comments"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-list fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No se encontraron solicitudes</h5>
                                            <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(isset($solicitudes) && $solicitudes->hasPages())
                    <div class="card-footer">
                        {{ $solicitudes->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-2 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $solicitudes->total() ?? 0 }}</h3>
                        <p>Total</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-list"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $solicitudes->where('estado', 'recibido')->count() ?? 0 }}</h3>
                        <p>Recibidas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $solicitudes->where('estado', 'en_proceso')->count() ?? 0 }}</h3>
                        <p>En Proceso</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $solicitudes->where('estado', 'completado')->count() ?? 0 }}</h3>
                        <p>Completadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $solicitudes->where('estado', 'rechazado')->count() ?? 0 }}</h3>
                        <p>Rechazadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-times"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $solicitudes->whereNull('assigned_to')->count() ?? 0 }}</h3>
                        <p>Sin Asignar</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-plus"></i>
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
                <h5 class="modal-title">Asignar Solicitudes</h5>
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
let currentSolicitudId = null;
let currentStatus = null;

function updateStatus(solicitudId, status) {
    currentSolicitudId = solicitudId;
    currentStatus = status;
    $('#statusModal').modal('show');
}

$('#confirmStatusUpdate').click(function() {
    const comments = $('#status_comments').val();
    
    $.ajax({
        url: `/admin/solicitudes/${currentSolicitudId}/status`,
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        data: {
            _token: '{{ csrf_token() }}',
            status: currentStatus,
            comments: comments
        },
        success: function(response) {
            $('#statusModal').modal('hide');
            if (response.success) {
                // Mostrar mensaje de éxito
                toastr.success('Estado actualizado exitosamente');
                location.reload();
            } else {
                toastr.error('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating status:', xhr.responseText);
            toastr.error('Error al actualizar el estado: ' + error);
        }
        error: function() {
            alert('Error al actualizar el estado');
        }
    });
});

function assignSelected() {
    const selectedIds = [];
    $('.solicitud-checkbox:checked').each(function() {
        selectedIds.push($(this).val());
    });
    
    if (selectedIds.length === 0) {
        alert('Selecciona al menos una solicitud');
        return;
    }
    
    window.selectedSolicitudIds = selectedIds;
    $('#assignModal').modal('show');
}

$('#confirmAssign').click(function() {
    const userId = $('#assign_user').val();
    const comments = $('#assign_comments').val();
    
    if (!userId) {
        alert('Selecciona un usuario');
        return;
    }
    
    // Aquí harías las peticiones AJAX para asignar cada solicitud
    // Por simplicidad, recargaremos la página
    location.reload();
});

function openChat(solicitudId) {
    // Abrir ventana de chat para la solicitud
    window.open(`/chat?type=solicitud&room=SOL-${solicitudId}`, '_blank');
}

// Select all functionality
$('#selectAll').change(function() {
    $('.solicitud-checkbox').prop('checked', $(this).is(':checked'));
});

// Auto-submit on filter change
$('#status, #tramite, #priority').change(function() {
    $(this).closest('form').submit();
});
</script>
@endpush

@push('styles')
<style>
.form-data-summary {
    max-width: 200px;
    font-size: 0.85em;
}

.form-data-summary .mb-1 {
    margin-bottom: 0.3rem !important;
    padding: 0.2rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.form-data-summary .mb-1:last-child {
    border-bottom: none;
}

.table td {
    vertical-align: middle;
}

.form-data-summary small.text-muted {
    font-weight: 600;
    color: #495057 !important;
}

/* Mejorar responsive de la tabla */
@media (max-width: 768px) {
    .form-data-summary {
        max-width: 150px;
        font-size: 0.8em;
    }
}
</style>
@endpush