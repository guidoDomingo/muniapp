@extends('layouts.admin')

@section('title', 'Editar Trámite - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Editar Trámite</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tramites.index') }}">Trámites</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tramites.show', $tramite) }}">{{ $tramite->nombre }}</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <form action="{{ route('admin.tramites.update', $tramite) }}" method="POST" id="tramiteForm">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-2"></i>
                                Información Básica
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre">Nombre del Trámite <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('nombre') is-invalid @enderror" 
                                               id="nombre" 
                                               name="nombre" 
                                               value="{{ old('nombre', $tramite->nombre) }}" 
                                               required>
                                        @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="department_id">Departamento Responsable</label>
                                        <select class="form-control @error('department_id') is-invalid @enderror" 
                                                id="department_id" 
                                                name="department_id">
                                            <option value="">Seleccionar departamento</option>
                                            @foreach($departments as $department)
                                            <option value="{{ $department->id }}" 
                                                    {{ old('department_id', $tramite->department_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                          id="descripcion" 
                                          name="descripcion" 
                                          rows="3">{{ old('descripcion', $tramite->descripcion) }}</textarea>
                                @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="estimated_days">Días Estimados</label>
                                        <input type="number" 
                                               class="form-control @error('estimated_days') is-invalid @enderror" 
                                               id="estimated_days" 
                                               name="estimated_days" 
                                               value="{{ old('estimated_days', $tramite->estimated_days) }}" 
                                               min="1">
                                        @error('estimated_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cost">Costo (en $)</label>
                                        <input type="number" 
                                               class="form-control @error('cost') is-invalid @enderror" 
                                               id="cost" 
                                               name="cost" 
                                               value="{{ old('cost', $tramite->cost) }}" 
                                               min="0" 
                                               step="0.01">
                                        @error('cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" 
                                                   class="custom-control-input" 
                                                   id="is_active" 
                                                   name="is_active" 
                                                   {{ old('is_active', $tramite->is_active) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="is_active">Trámite Activo</label>
                                        </div>
                                        <div class="custom-control custom-switch mt-2">
                                            <input type="checkbox" 
                                                   class="custom-control-input" 
                                                   id="include_map" 
                                                   name="include_map" 
                                                   {{ old('include_map', $tramite->include_map) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="include_map">
                                                <i class="fas fa-map-marker-alt mr-1"></i>Incluir Mapa
                                            </label>
                                        </div>
                                        <small class="form-text text-muted">
                                            Al activar esta opción, el formulario incluirá un mapa interactivo para que los ciudadanos puedan seleccionar una ubicación específica
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Required Documents -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-paperclip mr-2"></i>
                                Documentos Requeridos
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="documentsContainer">
                                @if($tramite->required_documents && count($tramite->required_documents) > 0)
                                    @foreach($tramite->required_documents as $index => $document)
                                    <div class="input-group mb-2 document-item">
                                        <input type="text" 
                                               class="form-control" 
                                               name="required_documents[]" 
                                               value="{{ $document }}" 
                                               placeholder="Nombre del documento">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-danger remove-document">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                <div class="input-group mb-2 document-item">
                                    <input type="text" 
                                           class="form-control" 
                                           name="required_documents[]" 
                                           placeholder="Nombre del documento">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger remove-document">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="addDocument">
                                <i class="fas fa-plus mr-1"></i> Agregar Documento
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Fields -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-2"></i>
                                Campos del Formulario
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="formFieldsContainer">
                                @if($tramite->form_fields && count($tramite->form_fields) > 0)
                                    @foreach($tramite->form_fields as $index => $field)
                                    <div class="card bg-light mb-3 form-field-item">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Etiqueta</label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="form_fields[{{ $index }}][label]" 
                                                               value="{{ $field['label'] ?? '' }}" 
                                                               placeholder="Etiqueta del campo">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>Tipo</label>
                                                        <select class="form-control field-type" name="form_fields[{{ $index }}][type]">
                                                            <option value="text" {{ ($field['type'] ?? '') == 'text' ? 'selected' : '' }}>Texto</option>
                                                            <option value="textarea" {{ ($field['type'] ?? '') == 'textarea' ? 'selected' : '' }}>Área de texto</option>
                                                            <option value="select" {{ ($field['type'] ?? '') == 'select' ? 'selected' : '' }}>Selección</option>
                                                            <option value="checkbox" {{ ($field['type'] ?? '') == 'checkbox' ? 'selected' : '' }}>Casilla</option>
                                                            <option value="radio" {{ ($field['type'] ?? '') == 'radio' ? 'selected' : '' }}>Radio</option>
                                                            <option value="file" {{ ($field['type'] ?? '') == 'file' ? 'selected' : '' }}>Archivo</option>
                                                            <option value="date" {{ ($field['type'] ?? '') == 'date' ? 'selected' : '' }}>Fecha</option>
                                                            <option value="email" {{ ($field['type'] ?? '') == 'email' ? 'selected' : '' }}>Email</option>
                                                            <option value="number" {{ ($field['type'] ?? '') == 'number' ? 'selected' : '' }}>Número</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Opciones (separadas por coma)</label>
                                                        <input type="text" 
                                                               class="form-control options-field" 
                                                               name="form_fields[{{ $index }}][options_text]" 
                                                               value="{{ isset($field['options']) && is_array($field['options']) ? implode(', ', $field['options']) : '' }}" 
                                                               placeholder="Opción 1, Opción 2, Opción 3"
                                                               {{ in_array($field['type'] ?? '', ['select', 'radio', 'checkbox']) ? '' : 'style=display:none' }}>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>&nbsp;</label>
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" 
                                                                   class="custom-control-input" 
                                                                   id="required_{{ $index }}" 
                                                                   name="form_fields[{{ $index }}][required]" 
                                                                   {{ ($field['required'] ?? false) ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="required_{{ $index }}">Requerido</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <label>&nbsp;</label>
                                                        <button type="button" class="btn btn-danger btn-sm remove-field d-block">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="addFormField">
                                <i class="fas fa-plus mr-1"></i> Agregar Campo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Workflow Steps -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-project-diagram mr-2"></i>
                                Flujo de Trabajo
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="workflowContainer">
                                @if($tramite->workflow_steps && count($tramite->workflow_steps) > 0)
                                    @foreach($tramite->workflow_steps as $index => $step)
                                    <div class="card bg-info mb-3 workflow-step">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Nombre del Paso</label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="workflow_steps[{{ $index }}][name]" 
                                                               value="{{ $step['name'] ?? '' }}" 
                                                               placeholder="Nombre del paso">
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label>Descripción</label>
                                                        <textarea class="form-control" 
                                                                  name="workflow_steps[{{ $index }}][description]" 
                                                                  rows="2" 
                                                                  placeholder="Descripción del paso">{{ $step['description'] ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>Días Estimados</label>
                                                        <input type="number" 
                                                               class="form-control" 
                                                               name="workflow_steps[{{ $index }}][estimated_days]" 
                                                               value="{{ $step['estimated_days'] ?? '' }}" 
                                                               min="1">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <label>&nbsp;</label>
                                                        <button type="button" class="btn btn-danger btn-sm remove-step d-block">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="addWorkflowStep">
                                <i class="fas fa-plus mr-1"></i> Agregar Paso
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.tramites.show', $tramite) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Actualizar Trámite
                                </button>
                            </div>
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
let fieldIndex = {{ $tramite->form_fields ? count($tramite->form_fields) : 0 }};
let stepIndex = {{ $tramite->workflow_steps ? count($tramite->workflow_steps) : 0 }};

// Add document
$('#addDocument').click(function() {
    const html = `
        <div class="input-group mb-2 document-item">
            <input type="text" class="form-control" name="required_documents[]" placeholder="Nombre del documento">
            <div class="input-group-append">
                <button type="button" class="btn btn-danger remove-document">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    $('#documentsContainer').append(html);
});

// Remove document
$(document).on('click', '.remove-document', function() {
    if ($('.document-item').length > 1) {
        $(this).closest('.document-item').remove();
    }
});

// Add form field
$('#addFormField').click(function() {
    const html = `
        <div class="card bg-light mb-3 form-field-item">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Etiqueta</label>
                            <input type="text" class="form-control" name="form_fields[${fieldIndex}][label]" placeholder="Etiqueta del campo">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Tipo</label>
                            <select class="form-control field-type" name="form_fields[${fieldIndex}][type]">
                                <option value="text">Texto</option>
                                <option value="textarea">Área de texto</option>
                                <option value="select">Selección</option>
                                <option value="checkbox">Casilla</option>
                                <option value="radio">Radio</option>
                                <option value="file">Archivo</option>
                                <option value="date">Fecha</option>
                                <option value="email">Email</option>
                                <option value="number">Número</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Opciones (separadas por coma)</label>
                            <input type="text" class="form-control options-field" name="form_fields[${fieldIndex}][options_text]" placeholder="Opción 1, Opción 2, Opción 3" style="display:none">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="required_${fieldIndex}" name="form_fields[${fieldIndex}][required]">
                                <label class="custom-control-label" for="required_${fieldIndex}">Requerido</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm remove-field d-block">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('#formFieldsContainer').append(html);
    fieldIndex++;
});

// Remove form field
$(document).on('click', '.remove-field', function() {
    $(this).closest('.form-field-item').remove();
});

// Show/hide options field based on type
$(document).on('change', '.field-type', function() {
    const optionsField = $(this).closest('.card-body').find('.options-field');
    if (['select', 'radio', 'checkbox'].includes($(this).val())) {
        optionsField.show();
    } else {
        optionsField.hide();
    }
});

// Add workflow step
$('#addWorkflowStep').click(function() {
    const html = `
        <div class="card bg-info mb-3 workflow-step">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nombre del Paso</label>
                            <input type="text" class="form-control" name="workflow_steps[${stepIndex}][name]" placeholder="Nombre del paso">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea class="form-control" name="workflow_steps[${stepIndex}][description]" rows="2" placeholder="Descripción del paso"></textarea>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Días Estimados</label>
                            <input type="number" class="form-control" name="workflow_steps[${stepIndex}][estimated_days]" min="1">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm remove-step d-block">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('#workflowContainer').append(html);
    stepIndex++;
});

// Remove workflow step
$(document).on('click', '.remove-step', function() {
    $(this).closest('.workflow-step').remove();
});

// Form submission processing
$('#tramiteForm').submit(function(e) {
    // Process form fields to create proper JSON structure
    const formFields = [];
    $('.form-field-item').each(function(index) {
        const label = $(this).find('input[name*="[label]"]').val();
        const type = $(this).find('select[name*="[type]"]').val();
        const required = $(this).find('input[name*="[required]"]').is(':checked');
        const optionsText = $(this).find('input[name*="[options_text]"]').val();
        
        if (label) {
            const field = {
                label: label,
                type: type,
                required: required
            };
            
            if (optionsText && ['select', 'radio', 'checkbox'].includes(type)) {
                field.options = optionsText.split(',').map(opt => opt.trim()).filter(opt => opt);
            }
            
            formFields.push(field);
        }
    });
    
    // Add hidden input with form fields JSON
    $('<input>').attr({
        type: 'hidden',
        name: 'form_fields',
        value: JSON.stringify(formFields)
    }).appendTo(this);
    
    // Process workflow steps
    const workflowSteps = [];
    $('.workflow-step').each(function() {
        const name = $(this).find('input[name*="[name]"]').val();
        const description = $(this).find('textarea[name*="[description]"]').val();
        const estimatedDays = $(this).find('input[name*="[estimated_days]"]').val();
        
        if (name) {
            workflowSteps.push({
                name: name,
                description: description,
                estimated_days: estimatedDays ? parseInt(estimatedDays) : null
            });
        }
    });
    
    // Add hidden input with workflow steps JSON
    $('<input>').attr({
        type: 'hidden',
        name: 'workflow_steps',
        value: JSON.stringify(workflowSteps)
    }).appendTo(this);
});
</script>
@endpush