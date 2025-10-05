@extends('layouts.admin')

@section('title', 'Crear Trámite - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Crear Trámite</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tramites.index') }}">Trámites</a></li>
                    <li class="breadcrumb-item active">Crear</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <form action="{{ route('admin.tramites.store') }}" method="POST">
            @csrf
            <div class="row">
                <!-- Información Básica -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-1"></i>
                                Información Básica
                            </h3>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="nombre">Nombre del Trámite <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="descripcion">Descripción <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                          id="descripcion" name="descripcion" rows="4" required>{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="department_id">Departamento</label>
                                        <select class="form-control @error('department_id') is-invalid @enderror" 
                                                id="department_id" name="department_id">
                                            <option value="">Seleccionar departamento</option>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('department_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="estimated_days">Días Estimados</label>
                                        <input type="number" class="form-control @error('estimated_days') is-invalid @enderror" 
                                               id="estimated_days" name="estimated_days" value="{{ old('estimated_days') }}" min="1">
                                        @error('estimated_days')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="cost">Costo ($)</label>
                                <input type="number" class="form-control @error('cost') is-invalid @enderror" 
                                       id="cost" name="cost" value="{{ old('cost', 0) }}" min="0" step="0.01">
                                @error('cost')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Dejar en 0 para trámites gratuitos</small>
                            </div>
                        </div>
                    </div>

                    <!-- Documentos Requeridos -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-alt mr-1"></i>
                                Documentos Requeridos
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="documents-container">
                                <div class="document-item mb-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="required_documents[]" 
                                               placeholder="Nombre del documento requerido">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-danger remove-document" type="button">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-document">
                                <i class="fas fa-plus"></i> Agregar Documento
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Configuración Adicional -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cogs mr-1"></i>
                                Configuración
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">
                                    Trámite activo
                                </label>
                            </div>

                            <div class="form-group">
                                <label>Estado</label>
                                <div class="text-sm">
                                    <i class="fas fa-info-circle text-info"></i>
                                    Los trámites activos aparecen en la lista pública para que los ciudadanos puedan solicitarlos.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campos del Formulario -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-wpforms mr-1"></i>
                                Campos del Formulario
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="form-fields-container">
                                <div class="form-field-item mb-3 p-3 border rounded">
                                    <div class="form-group">
                                        <label>Nombre del Campo</label>
                                        <input type="text" class="form-control form-field-name" 
                                               placeholder="ej: direccion">
                                    </div>
                                    <div class="form-group">
                                        <label>Etiqueta</label>
                                        <input type="text" class="form-control form-field-label" 
                                               placeholder="ej: Dirección">
                                    </div>
                                    <div class="form-group">
                                        <label>Tipo</label>
                                        <select class="form-control form-field-type">
                                            <option value="text">Texto</option>
                                            <option value="textarea">Área de texto</option>
                                            <option value="number">Número</option>
                                            <option value="email">Email</option>
                                            <option value="date">Fecha</option>
                                            <option value="select">Lista desplegable</option>
                                        </select>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input form-field-required">
                                        <label class="form-check-label">Campo requerido</label>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-field mt-2">
                                        <i class="fas fa-trash"></i> Eliminar Campo
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-form-field">
                                <i class="fas fa-plus"></i> Agregar Campo
                            </button>
                            <input type="hidden" name="form_fields" id="form_fields_json">
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="card">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-save"></i> Crear Trámite
                            </button>
                            <a href="{{ route('admin.tramites.index') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Add document
    $('#add-document').click(function() {
        const documentItem = `
            <div class="document-item mb-2">
                <div class="input-group">
                    <input type="text" class="form-control" name="required_documents[]" 
                           placeholder="Nombre del documento requerido">
                    <div class="input-group-append">
                        <button class="btn btn-outline-danger remove-document" type="button">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#documents-container').append(documentItem);
    });

    // Remove document
    $(document).on('click', '.remove-document', function() {
        $(this).closest('.document-item').remove();
    });

    // Add form field
    $('#add-form-field').click(function() {
        const fieldItem = `
            <div class="form-field-item mb-3 p-3 border rounded">
                <div class="form-group">
                    <label>Nombre del Campo</label>
                    <input type="text" class="form-control form-field-name" 
                           placeholder="ej: direccion">
                </div>
                <div class="form-group">
                    <label>Etiqueta</label>
                    <input type="text" class="form-control form-field-label" 
                           placeholder="ej: Dirección">
                </div>
                <div class="form-group">
                    <label>Tipo</label>
                    <select class="form-control form-field-type">
                        <option value="text">Texto</option>
                        <option value="textarea">Área de texto</option>
                        <option value="number">Número</option>
                        <option value="email">Email</option>
                        <option value="date">Fecha</option>
                        <option value="select">Lista desplegable</option>
                    </select>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input form-field-required">
                    <label class="form-check-label">Campo requerido</label>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-field mt-2">
                    <i class="fas fa-trash"></i> Eliminar Campo
                </button>
            </div>
        `;
        $('#form-fields-container').append(fieldItem);
    });

    // Remove form field
    $(document).on('click', '.remove-field', function() {
        $(this).closest('.form-field-item').remove();
    });

    // Collect form fields data before submit
    $('form').submit(function() {
        const formFields = [];
        $('.form-field-item').each(function() {
            const name = $(this).find('.form-field-name').val();
            const label = $(this).find('.form-field-label').val();
            const type = $(this).find('.form-field-type').val();
            const required = $(this).find('.form-field-required').is(':checked');
            
            if (name && label) {
                formFields.push({
                    name: name,
                    label: label,
                    type: type,
                    required: required
                });
            }
        });
        $('#form_fields_json').val(JSON.stringify(formFields));
    });
});
</script>
@endpush