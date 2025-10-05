@extends('layouts.admin')

@section('title', 'Crear Solicitud - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Crear Nueva Solicitud</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.solicitudes.index') }}">Solicitudes</a></li>
                    <li class="breadcrumb-item active">Crear</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-plus mr-1"></i>
                            Información de la Solicitud
                        </h3>
                    </div>
                    
                    <form action="{{ route('admin.solicitudes.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <!-- Información del Usuario -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Información del Usuario</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="user_id">Usuario <span class="text-danger">*</span></label>
                                                <select name="user_id" id="user_id" class="form-control select2" required>
                                                    <option value="">Seleccionar usuario...</option>
                                                    @foreach($users ?? [] as $user)
                                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                            {{ $user->name }} - {{ $user->email }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('user_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="nombre_completo">Nombre Completo <span class="text-danger">*</span></label>
                                                <input type="text" name="nombre_completo" id="nombre_completo" 
                                                       class="form-control" value="{{ old('nombre_completo') }}" required>
                                                @error('nombre_completo')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="telefono">Teléfono</label>
                                                <input type="tel" name="telefono" id="telefono" 
                                                       class="form-control" value="{{ old('telefono') }}">
                                                @error('telefono')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="direccion">Dirección</label>
                                                <textarea name="direccion" id="direccion" class="form-control" 
                                                          rows="3">{{ old('direccion') }}</textarea>
                                                @error('direccion')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información del Trámite -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Información del Trámite</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="tramite_id">Tipo de Trámite <span class="text-danger">*</span></label>
                                                <select name="tramite_id" id="tramite_id" class="form-control select2" required>
                                                    <option value="">Seleccionar trámite...</option>
                                                    @foreach($tramites ?? [] as $tramite)
                                                        <option value="{{ $tramite->id }}" 
                                                                data-costo="{{ $tramite->costo }}"
                                                                data-tiempo="{{ $tramite->tiempo_estimado }}"
                                                                {{ old('tramite_id') == $tramite->id ? 'selected' : '' }}>
                                                            {{ $tramite->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('tramite_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="priority">Prioridad</label>
                                                <select name="priority" id="priority" class="form-control">
                                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Media</option>
                                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Baja</option>
                                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Alta</option>
                                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgente</option>
                                                </select>
                                                @error('priority')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="assigned_to">Asignar a</label>
                                                <select name="assigned_to" id="assigned_to" class="form-control select2">
                                                    <option value="">Sin asignar</option>
                                                    @foreach($functionaries ?? [] as $functionary)
                                                        <option value="{{ $functionary->id }}" {{ old('assigned_to') == $functionary->id ? 'selected' : '' }}>
                                                            {{ $functionary->name }} - {{ $functionary->department->name ?? 'Sin departamento' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('assigned_to')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Costo Estimado</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">$</span>
                                                            </div>
                                                            <input type="text" id="costo_display" class="form-control" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Tiempo Estimado</label>
                                                        <input type="text" id="tiempo_display" class="form-control" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detalles de la Solicitud -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Detalles de la Solicitud</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="detalle">Descripción/Detalle <span class="text-danger">*</span></label>
                                                <textarea name="detalle" id="detalle" class="form-control" 
                                                          rows="4" placeholder="Describa los detalles de la solicitud..." required>{{ old('detalle') }}</textarea>
                                                @error('detalle')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="observaciones">Observaciones</label>
                                                <textarea name="observaciones" id="observaciones" class="form-control" 
                                                          rows="3" placeholder="Observaciones adicionales...">{{ old('observaciones') }}</textarea>
                                                @error('observaciones')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="adjuntos">Archivos Adjuntos</label>
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
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estado Inicial -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Estado Inicial</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="estado">Estado Inicial</label>
                                                <select name="estado" id="estado" class="form-control">
                                                    <option value="recibido" {{ old('estado') == 'recibido' ? 'selected' : '' }}>Recibido</option>
                                                    <option value="en_revision" {{ old('estado') == 'en_revision' ? 'selected' : '' }}>En Revisión</option>
                                                    <option value="en_proceso" {{ old('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                                </select>
                                                @error('estado')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="comments">Comentarios Iniciales</label>
                                                <textarea name="comments" id="comments" class="form-control" 
                                                          rows="3" placeholder="Comentarios sobre la creación de la solicitud...">{{ old('comments') }}</textarea>
                                                @error('comments')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
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
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Crear Solicitud
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
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

    // Auto-fill user data when user is selected
    $('#user_id').change(function() {
        const userId = $(this).val();
        if (userId) {
            // Aquí podrías hacer una llamada AJAX para obtener los datos del usuario
            // y rellenar automáticamente los campos
        }
    });

    // Update cost and time when tramite is selected
    $('#tramite_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const costo = selectedOption.data('costo');
        const tiempo = selectedOption.data('tiempo');
        
        $('#costo_display').val(costo ? `${costo}` : '-');
        $('#tiempo_display').val(tiempo ? `${tiempo} días` : '-');
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

    // Form validation
    $('form').submit(function(e) {
        const requiredFields = ['user_id', 'nombre_completo', 'tramite_id', 'detalle'];
        let isValid = true;
        
        requiredFields.forEach(function(field) {
            const value = $(`#${field}`).val();
            if (!value || value.trim() === '') {
                isValid = false;
                $(`#${field}`).addClass('is-invalid');
            } else {
                $(`#${field}`).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Por favor complete todos los campos requeridos');
        }
    });
});
</script>
@endpush