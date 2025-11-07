@extends('layouts.admin')

@section('title', 'Crear Trámite - MuniApp Admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-tramites.css') }}">
@endpush

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
                </div>

                <!-- Configuración y Campos del Formulario -->
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

                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="include_map" name="include_map" value="1">
                                <label class="form-check-label" for="include_map">
                                    Incluir mapa de ubicación
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

                </div>
            </div>

            <!-- Campos del Formulario - Ancho completo -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-wpforms mr-1"></i>
                                Campos del Formulario
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="form-fields-container">
                                <!-- Los campos se agregan dinámicamente aquí -->
                            </div>
                            <button type="button" class="btn btn-outline-primary" id="add-form-field">
                                <i class="fas fa-plus"></i> Agregar Campo
                            </button>
                            <input type="hidden" name="form_fields" id="form_fields_json">
                            <input type="hidden" name="include_map" id="include_map_hidden">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="submit" class="btn btn-primary btn-lg mr-3">
                                <i class="fas fa-save"></i> Crear Trámite
                            </button>
                            <a href="{{ route('admin.tramites.index') }}" class="btn btn-secondary btn-lg">
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
    // Función para crear un nuevo campo del formulario
    function createFormField() {
        const fieldId = Date.now();
        return `
            <div class="form-field-item mb-4 p-4 border rounded" data-field-id="${fieldId}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nombre del Campo</label>
                            <input type="text" class="form-control form-field-name" 
                                   placeholder="ej: direccion">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Etiqueta</label>
                            <input type="text" class="form-control form-field-label" 
                                   placeholder="ej: Dirección">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tipo</label>
                            <select class="form-control form-field-type">
                                <option value="text">Texto</option>
                                <option value="textarea">Área de texto</option>
                                <option value="number">Número</option>
                                <option value="email">Email</option>
                                <option value="date">Fecha</option>
                                <option value="select">Lista desplegable</option>
                                <option value="radio">Botones de radio</option>
                                <option value="checkbox">Casillas de verificación</option>
                                <option value="file">Archivo/Documento</option>
                                <option value="image">Imagen</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Opciones para select, radio y checkbox -->
                <div class="options-container" style="display: none;">
                    <div class="form-group">
                        <label>Opciones (una por línea)</label>
                        <textarea class="form-control form-field-options" rows="3" 
                                  placeholder="Opción 1&#10;Opción 2&#10;Opción 3"></textarea>
                        <small class="form-text text-muted">Escriba cada opción en una línea separada</small>
                    </div>
                </div>
                
                <!-- Configuración de archivos -->
                <div class="file-config" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipos de archivo permitidos</label>
                                <select class="form-control form-field-file-types" multiple>
                                    <option value="pdf">PDF</option>
                                    <option value="doc,docx">Word</option>
                                    <option value="jpg,jpeg,png">Imágenes</option>
                                    <option value="txt">Texto</option>
                                    <option value="xls,xlsx">Excel</option>
                                </select>
                                <small class="form-text text-muted">Mantén Ctrl presionado para seleccionar múltiples</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tamaño máximo (MB)</label>
                                <input type="number" class="form-control form-field-max-size" 
                                       value="10" min="1" max="50">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input form-field-required">
                            <label class="form-check-label">Campo requerido</label>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-outline-danger remove-field">
                            <i class="fas fa-trash"></i> Eliminar Campo
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    // Agregar nuevo campo del formulario
    $('#add-form-field').click(function() {
        $('#form-fields-container').append(createFormField());
    });

    // Remover campo del formulario
    $(document).on('click', '.remove-field', function() {
        $(this).closest('.form-field-item').remove();
    });

    // Manejar cambio de tipo de campo
    $(document).on('change', '.form-field-type', function() {
        const type = $(this).val();
        const fieldItem = $(this).closest('.form-field-item');
        const optionsContainer = fieldItem.find('.options-container');
        const fileConfig = fieldItem.find('.file-config');
        
        // Mostrar/ocultar contenedores según el tipo
        if (type === 'select' || type === 'radio' || type === 'checkbox') {
            optionsContainer.show();
            fileConfig.hide();
        } else if (type === 'file' || type === 'image') {
            optionsContainer.hide();
            fileConfig.show();
            
            // Configurar tipos de archivo por defecto
            if (type === 'image') {
                fileConfig.find('.form-field-file-types').val(['jpg,jpeg,png']);
            }
        } else {
            optionsContainer.hide();
            fileConfig.hide();
        }
    });

    // Sincronizar checkbox de mapa con campo oculto
    $('#include_map').change(function() {
        $('#include_map_hidden').val($(this).is(':checked') ? '1' : '0');
    });

    // Recopilar datos de campos del formulario antes de enviar
    $('form').submit(function() {
        const formFields = [];
        
        $('.form-field-item').each(function() {
            const name = $(this).find('.form-field-name').val();
            const label = $(this).find('.form-field-label').val();
            const type = $(this).find('.form-field-type').val();
            const required = $(this).find('.form-field-required').is(':checked');
            
            if (name && label) {
                const field = {
                    name: name,
                    label: label,
                    type: type,
                    required: required
                };

                // Agregar opciones para select, radio, checkbox
                if (type === 'select' || type === 'radio' || type === 'checkbox') {
                    const optionsText = $(this).find('.form-field-options').val();
                    if (optionsText) {
                        field.options = optionsText.split('\n').filter(option => option.trim().length > 0);
                    }
                }

                // Agregar configuración de archivos
                if (type === 'file' || type === 'image') {
                    const fileTypes = $(this).find('.form-field-file-types').val();
                    const maxSize = $(this).find('.form-field-max-size').val();
                    
                    field.file_types = fileTypes ? fileTypes.join(',') : '';
                    field.max_size = maxSize || 10;
                }

                formFields.push(field);
            }
        });
        
        $('#form_fields_json').val(JSON.stringify(formFields));
        
        // Asegurar que el valor del mapa esté sincronizado
        $('#include_map_hidden').val($('#include_map').is(':checked') ? '1' : '0');
    });

    // Inicializar con un campo por defecto
    if ($('#form-fields-container').children().length === 0) {
        $('#form-fields-container').append(createFormField());
    }
});
</script>
@endpush