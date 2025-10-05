@extends('layouts.admin')

@section('title', 'Editar Solicitud - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Editar Solicitud #{{ $solicitud->tracking_code ?? $solicitud->id }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.solicitudes.index') }}">Solicitudes</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Main Form -->
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-edit mr-1"></i>
                            Editar Información de la Solicitud
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-{{ $solicitud->status_color ?? 'secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                            </span>
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.solicitudes.update', $solicitud) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <!-- Información del Usuario -->
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Información del Usuario</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="user_id">Usuario <span class="text-danger">*</span></label>
                                                <select name="user_id" id="user_id" class="form-control select2" required>
                                                    <option value="">Seleccionar usuario...</option>
                                                    @foreach($users ?? [] as $user)
                                                        <option value="{{ $user->id }}" 
                                                                {{ (old('user_id') ?? $solicitud->user_id) == $user->id ? 'selected' : '' }}>
                                                            {{ $user->name }} - {{ $user->email }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('user_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nombre_completo">Nombre Completo <span class="text-danger">*</span></label>
                                                <input type="text" name="nombre_completo" id="nombre_completo" 
                                                       class="form-control" value="{{ old('nombre_completo') ?? $solicitud->nombre_completo }}" required>
                                                @error('nombre_completo')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="telefono">Teléfono</label>
                                                <input type="tel" name="telefono" id="telefono" 
                                                       class="form-control" value="{{ old('telefono') ?? $solicitud->telefono }}">
                                                @error('telefono')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="direccion">Dirección</label>
                                                <input type="text" name="direccion" id="direccion" 
                                                       class="form-control" value="{{ old('direccion') ?? $solicitud->direccion }}">
                                                @error('direccion')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información del Trámite -->
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Información del Trámite</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tramite_id">Tipo de Trámite <span class="text-danger">*</span></label>
                                                <select name="tramite_id" id="tramite_id" class="form-control select2" required>
                                                    <option value="">Seleccionar trámite...</option>
                                                    @foreach($tramites ?? [] as $tramite)
                                                        <option value="{{ $tramite->id }}" 
                                                                data-costo="{{ $tramite->costo }}"
                                                                data-tiempo="{{ $tramite->tiempo_estimado }}"
                                                                {{ (old('tramite_id') ?? $solicitud->tramite_id) == $tramite->id ? 'selected' : '' }}>
                                                            {{ $tramite->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('tramite_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="priority">Prioridad</label>
                                                <select name="priority" id="priority" class="form-control">
                                                    <option value="low" {{ (old('priority') ?? $solicitud->priority) == 'low' ? 'selected' : '' }}>Baja</option>
                                                    <option value="medium" {{ (old('priority') ?? $solicitud->priority) == 'medium' ? 'selected' : '' }}>Media</option>
                                                    <option value="high" {{ (old('priority') ?? $solicitud->priority) == 'high' ? 'selected' : '' }}>Alta</option>
                                                    <option value="urgent" {{ (old('priority') ?? $solicitud->priority) == 'urgent' ? 'selected' : '' }}>Urgente</option>
                                                </select>
                                                @error('priority')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="assigned_to">Asignar a</label>
                                                <select name="assigned_to" id="assigned_to" class="form-control select2">
                                                    <option value="">Sin asignar</option>
                                                    @foreach($functionaries ?? [] as $functionary)
                                                        <option value="{{ $functionary->id }}" 
                                                                {{ (old('assigned_to') ?? $solicitud->assigned_to) == $functionary->id ? 'selected' : '' }}>
                                                            {{ $functionary->name }} - {{ $functionary->department->name ?? 'Sin departamento' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('assigned_to')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="estado">Estado</label>
                                                <select name="estado" id="estado" class="form-control">
                                                    <option value="recibido" {{ (old('estado') ?? $solicitud->estado) == 'recibido' ? 'selected' : '' }}>Recibido</option>
                                                    <option value="en_revision" {{ (old('estado') ?? $solicitud->estado) == 'en_revision' ? 'selected' : '' }}>En Revisión</option>
                                                    <option value="en_proceso" {{ (old('estado') ?? $solicitud->estado) == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                                    <option value="completado" {{ (old('estado') ?? $solicitud->estado) == 'completado' ? 'selected' : '' }}>Completado</option>
                                                    <option value="rechazado" {{ (old('estado') ?? $solicitud->estado) == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                                                </select>
                                                @error('estado')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detalles de la Solicitud -->
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Detalles de la Solicitud</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="detalle">Descripción/Detalle <span class="text-danger">*</span></label>
                                        <textarea name="detalle" id="detalle" class="form-control" 
                                                  rows="4" placeholder="Describa los detalles de la solicitud..." required>{{ old('detalle') ?? $solicitud->detalle }}</textarea>
                                        @error('detalle')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="observaciones">Observaciones</label>
                                        <textarea name="observaciones" id="observaciones" class="form-control" 
                                                  rows="3" placeholder="Observaciones adicionales...">{{ old('observaciones') ?? $solicitud->observaciones }}</textarea>
                                        @error('observaciones')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="adjuntos">Archivos Adjuntos Adicionales</label>
                                        <div class="custom-file">
                                            <input type="file" name="adjuntos[]" id="adjuntos" 
                                                   class="custom-file-input" multiple 
                                                   accept=".pdf,.doc,.docx,.jpg,.png,.jpeg">
                                            <label class="custom-file-label" for="adjuntos">Seleccionar archivos...</label>
                                        </div>
                                        <small class="form-text text-muted">
                                            Formatos permitidos: PDF, DOC, DOCX, JPG, PNG. Máximo 10MB por archivo.
                                        </small>
                                        @error('adjuntos')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Existing Files -->
                                    @if($solicitud->adjuntos ?? false)
                                        <div class="form-group">
                                            <label>Archivos Existentes</label>
                                            <div class="row">
                                                @foreach($solicitud->adjuntos as $archivo)
                                                    <div class="col-md-4 mb-2">
                                                        <div class="card">
                                                            <div class="card-body p-2">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <i class="fas fa-file"></i>
                                                                        <small>{{ $archivo->nombre ?? 'archivo.pdf' }}</small>
                                                                    </div>
                                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                            onclick="removeFile({{ $archivo->id ?? 0 }})">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Comments for Update -->
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Comentarios de Actualización</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="update_comments">Comentarios sobre los cambios</label>
                                        <textarea name="update_comments" id="update_comments" class="form-control" 
                                                  rows="3" placeholder="Describa los cambios realizados...">{{ old('update_comments') }}</textarea>
                                        @error('update_comments')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Volver
                                    </a>
                                    <a href="{{ route('admin.solicitudes.show', $solicitud) }}" class="btn btn-info">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Actualizar Solicitud
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar with Additional Info -->
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

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt mr-1"></i>
                            Acciones Rápidas
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block" onclick="openChat()">
                                <i class="fas fa-comments"></i> Abrir Chat
                            </button>
                            <button type="button" class="btn btn-outline-info btn-block" onclick="sendNotification()">
                                <i class="fas fa-bell"></i> Notificar Usuario
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-block" onclick="generateReport()">
                                <i class="fas fa-file-pdf"></i> Generar Reporte
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Summary Info -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-1"></i>
                            Información Adicional
                        </h3>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-5">Creado:</dt>
                            <dd class="col-sm-7">{{ $solicitud->created_at->format('d/m/Y H:i') }}</dd>
                            
                            <dt class="col-sm-5">Actualizado:</dt>
                            <dd class="col-sm-7">{{ $solicitud->updated_at->format('d/m/Y H:i') }}</dd>
                            
                            <dt class="col-sm-5">Código:</dt>
                            <dd class="col-sm-7">
                                <span class="badge badge-secondary">{{ $solicitud->tracking_code ?? 'SOL-' . $solicitud->id }}</span>
                            </dd>
                            
                            @if($solicitud->tramite && $solicitud->tramite->costo)
                                <dt class="col-sm-5">Costo:</dt>
                                <dd class="col-sm-7">${{ number_format($solicitud->tramite->costo, 2) }}</dd>
                            @endif
                            
                            @if($solicitud->tramite && $solicitud->tramite->tiempo_estimado)
                                <dt class="col-sm-5">Tiempo Est.:</dt>
                                <dd class="col-sm-7">{{ $solicitud->tramite->tiempo_estimado }} días</dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Seleccionar...',
        allowClear: true
    });

    // File input label update
    $('#adjuntos').change(function() {
        const files = this.files;
        let label = 'Seleccionar archivos...';
        
        if (files.length > 0) {
            if (files.length === 1) {
                label = files[0].name;
            } else {
                label = `${files.length} archivos seleccionados`;
            }
        }
        
        $(this).next('.custom-file-label').text(label);
    });
});

function removeFile(fileId) {
    if (confirm('¿Está seguro de eliminar este archivo?')) {
        // Aquí harías la llamada AJAX para eliminar el archivo
        console.log('Eliminar archivo ID:', fileId);
    }
}

function openChat() {
    const solicitudId = {{ $solicitud->id }};
    window.open(`/chat?type=solicitud&room=SOL-${solicitudId}`, '_blank');
}

function sendNotification() {
    // Implementar notificación al usuario
    alert('Funcionalidad de notificación en desarrollo');
}

function generateReport() {
    // Generar reporte PDF
    window.open(`/admin/solicitudes/{{ $solicitud->id }}/report`, '_blank');
}
</script>
@endpush