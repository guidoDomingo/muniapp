@extends('muni')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">{{ $tramite->nombre }}</h1>
        </div>
        <div class="card-body">
            <p class="card-text">{{ $tramite->descripcion }}</p>
            {{-- <a href="{{ route('solicitudes.create', $tramite->id) }}" class="btn btn-primary">Solicitar este trámite</a> --}}
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h1 class="card-title">Solicitar Trámite: {{ $tramite->nombre }}</h1>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('solicitudes.store', $tramite->id) }}" enctype="multipart/form-data">
                @csrf

                @if($tramite->form_fields && count($tramite->form_fields) > 0)
                    {{-- Campos dinámicos del trámite --}}
                    <div class="form-section">
                        <div class="section-header mb-4">
                            <h4 class="section-title">📋 Información Requerida</h4>
                            <p class="section-subtitle">Complete los siguientes campos para procesar su solicitud</p>
                        </div>
                        
                        @foreach($tramite->form_fields as $field)
                            <div class="form-group mb-4 dynamic-field" data-field-type="{{ $field['type'] ?? 'text' }}">
                                <div class="field-header">
                                    <label for="field_{{ $loop->index }}" class="form-label">
                                        <span class="field-icon">
                                            @switch($field['type'] ?? 'text')
                                                @case('image')
                                                    📷
                                                    @break
                                                @case('file')
                                                    📎
                                                    @break
                                                @case('email')
                                                    ✉️
                                                    @break
                                                @case('date')
                                                    📅
                                                    @break
                                                @case('select')
                                                    📝
                                                    @break
                                                @case('textarea')
                                                    📄
                                                    @break
                                                @default
                                                    ✏️
                                            @endswitch
                                        </span>
                                        <span class="field-title">{{ $field['label'] ?? 'Campo' }}</span>
                                        @if($field['required'] ?? false)
                                            <span class="required-badge">Obligatorio</span>
                                        @endif
                                    </label>
                                    @if(isset($field['description']))
                                        <small class="field-description">{{ $field['description'] }}</small>
                                    @endif
                                </div>
                                
                                <div class="field-content">
                            
                            @switch($field['type'] ?? 'text')
                                @case('text')
                                @case('email')
                                @case('number')
                                    <input type="{{ $field['type'] }}" 
                                           name="form_data[{{ $loop->index }}]" 
                                           id="field_{{ $loop->index }}" 
                                           class="form-control @error('form_data.'.$loop->index) is-invalid @enderror" 
                                           value="{{ old('form_data.'.$loop->index) }}" 
                                           {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                    @break
                                
                                @case('textarea')
                                    <textarea name="form_data[{{ $loop->index }}]" 
                                              id="field_{{ $loop->index }}" 
                                              class="form-control @error('form_data.'.$loop->index) is-invalid @enderror" 
                                              rows="3" 
                                              {{ ($field['required'] ?? false) ? 'required' : '' }}>{{ old('form_data.'.$loop->index) }}</textarea>
                                    @break
                                
                                @case('select')
                                    <select name="form_data[{{ $loop->index }}]" 
                                            id="field_{{ $loop->index }}" 
                                            class="form-select @error('form_data.'.$loop->index) is-invalid @enderror" 
                                            {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                        <option value="" disabled selected>-- Seleccionar una opción --</option>
                                        @if(isset($field['options']) && is_array($field['options']))
                                            @foreach($field['options'] as $option)
                                                <option value="{{ $option }}" 
                                                        {{ old('form_data.'.$loop->parent->index) == $option ? 'selected' : '' }}>
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @break
                                
                                @case('radio')
                                    @if(isset($field['options']) && is_array($field['options']))
                                        <div class="radio-group">
                                            @foreach($field['options'] as $optionIndex => $option)
                                                <div class="form-check form-check-custom">
                                                    <input type="radio" 
                                                           name="form_data[{{ $loop->parent->index }}]" 
                                                           id="field_{{ $loop->parent->index }}_{{ $optionIndex }}" 
                                                           value="{{ $option }}" 
                                                           class="form-check-input @error('form_data.'.$loop->parent->index) is-invalid @enderror" 
                                                           {{ old('form_data.'.$loop->parent->index) == $option ? 'checked' : '' }}
                                                           {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                                    <label class="form-check-label" for="field_{{ $loop->parent->index }}_{{ $optionIndex }}">
                                                        <span class="check-indicator"></span>
                                                        {{ $option }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    @break
                                
                                @case('checkbox')
                                    @if(isset($field['options']) && is_array($field['options']))
                                        <div class="checkbox-group">
                                            @foreach($field['options'] as $optionIndex => $option)
                                                <div class="form-check form-check-custom">
                                                    <input type="checkbox" 
                                                           name="form_data[{{ $loop->parent->index }}][]" 
                                                           id="field_{{ $loop->parent->index }}_{{ $optionIndex }}" 
                                                           value="{{ $option }}" 
                                                           class="form-check-input @error('form_data.'.$loop->parent->index) is-invalid @enderror" 
                                                           {{ in_array($option, old('form_data.'.$loop->parent->index, [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="field_{{ $loop->parent->index }}_{{ $optionIndex }}">
                                                        <span class="check-indicator"></span>
                                                        {{ $option }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    @break
                                
                                    @case('file')
                                    @case('image')
                                        <div class="file-upload-container">
                                            <input type="file" 
                                                   name="form_data[{{ $loop->index }}]" 
                                                   id="field_{{ $loop->index }}" 
                                                   class="file-input" 
                                                   {{ ($field['required'] ?? false) ? 'required' : '' }}
                                                   @if(isset($field['file_types']) && $field['file_types'])
                                                       accept=".{{ str_replace(',', ',.', $field['file_types']) }}"
                                                   @endif
                                                   @if($field['type'] === 'image')
                                                       accept="image/*"
                                                   @endif
                                                   data-field-index="{{ $loop->index }}"
                                                   data-field-type="{{ $field['type'] }}">
                                            
                                            <div class="file-drop-zone" onclick="document.getElementById('field_{{ $loop->index }}').click()">
                                                <div class="drop-content">
                                                    <div class="drop-icon">
                                                        @if($field['type'] === 'image')
                                                            <i class="fas fa-images fa-3x"></i>
                                                        @else
                                                            <i class="fas fa-file-upload fa-3x"></i>
                                                        @endif
                                                    </div>
                                                    <h5 class="drop-title">
                                                        {{ $field['type'] === 'image' ? 'Subir Imagen' : 'Subir Archivo' }}
                                                    </h5>
                                                    <p class="drop-text">
                                                        Haz clic aquí o arrastra {{ $field['type'] === 'image' ? 'tu imagen' : 'tu archivo' }}
                                                    </p>
                                                    <div class="drop-formats">
                                                        @if($field['type'] === 'image')
                                                            <span class="format-tag">JPG</span>
                                                            <span class="format-tag">PNG</span>
                                                            <span class="format-tag">GIF</span>
                                                        @else
                                                            <span class="format-tag">PDF</span>
                                                            <span class="format-tag">DOC</span>
                                                            <span class="format-tag">DOCX</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div id="preview_{{ $loop->index }}" class="file-preview"></div>
                                            
                                            @if(isset($field['max_size']))
                                                <div class="file-info-footer">
                                                    <small class="text-muted">
                                                        <i class="fas fa-info-circle"></i> 
                                                        Tamaño máximo: <strong>{{ $field['max_size'] }}MB</strong>
                                                        @if(isset($field['file_types']))
                                                            • Formatos: <strong>{{ str_replace(',', ', ', strtoupper($field['file_types'])) }}</strong>
                                                        @endif
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                        @break
                                
                                @case('date')
                                    <input type="date" 
                                           name="form_data[{{ $loop->index }}]" 
                                           id="field_{{ $loop->index }}" 
                                           class="form-control @error('form_data.'.$loop->index) is-invalid @enderror" 
                                           value="{{ old('form_data.'.$loop->index) }}" 
                                           {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                    @break
                                
                                @default
                                    <input type="text" 
                                           name="form_data[{{ $loop->index }}]" 
                                           id="field_{{ $loop->index }}" 
                                           class="form-control @error('form_data.'.$loop->index) is-invalid @enderror" 
                                           value="{{ old('form_data.'.$loop->index) }}" 
                                           {{ ($field['required'] ?? false) ? 'required' : '' }}>
                            @endswitch
                            
                                @error('form_data.'.$loop->index)
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                                    </div>
                                @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Formulario por defecto si no hay campos configurados --}}
                    <div class="form-group">
                        <label for="nombre_usuario">Nombre de la comisión:</label>
                        <input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control" value="{{ old('nombre_usuario') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="documento_identidad">Documento de vigencia:</label>
                        <input type="file" name="documento_identidad" id="documento_identidad" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="imagen_usuario">Fotos del lugar:</label>
                        <input type="file" name="imagen_usuario[]" id="imagen_usuario" class="form-control" multiple required>
                    </div>

                    <div id="preview" class="gallery-preview"></div>

                    <div class="form-group">
                        <label for="ubicacion">Ubicación:</label>
                        <textarea name="detalles" id="detalles" class="form-control" rows="4" required></textarea>
                    </div>
                @endif

                {{-- Campos de ubicación (solo si el trámite incluye mapa) --}}
                @if($tramite->include_map)
                    <div class="form-group">
                        <label for="ubicacion-search">Buscar ubicación (opcional):</label>
                        <div class="location-search-container">
                            <div class="location-search-input">
                                <input type="text" id="ubicacion-search" class="form-control" placeholder="Ej: Centro de Asunción, Paraguay">
                                <button type="button" id="search-btn" class="btn btn-secondary">Buscar</button>
                            </div>
                            <div id="search-results" class="search-results"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="map">Seleccione su ubicación en el mapa:</label>
                        <div class="map-container">
                            <div id="map" style="width: 100%; height: 400px; border-radius: 8px; border: 2px solid #e5e7eb; position: relative; z-index: 1;"></div>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Arrastra el marcador o haz clic en el mapa para seleccionar la ubicación exacta
                        </small>
                    </div>

                    <input type="hidden" name="latitud" id="latitud">
                    <input type="hidden" name="longitud" id="longitud">
                @endif

                <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Reset y base */
    .card-body {
        padding: 2rem;
    }

    /* Mapa específico */
    .map-container {
        margin: 15px 0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    #map {
        width: 100% !important;
        height: 400px !important;
        min-height: 400px !important;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        position: relative;
        z-index: 1;
    }

    .leaflet-container {
        border-radius: 8px !important;
        border: none !important;
    }

    .location-search-container {
        margin-bottom: 20px;
        padding: 15px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 10px;
        border: 1px solid #e5e7eb;
    }

    .location-search-input {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .location-search-input input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
    }

    .location-search-input button {
        padding: 10px 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .location-search-input button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .location-search-input button:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        transform: none;
    }

    .search-results {
        margin-top: 10px;
        font-size: 13px;
        padding: 8px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.8);
    }

    /* Sección de formulario */
    .form-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .form-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 20px;
    }

    .section-header {
        position: relative;
        z-index: 2;
        text-align: center;
        margin-bottom: 2rem;
    }

    .section-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .section-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0;
    }

    /* Campos dinámicos mejorados */
    .dynamic-field {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        border: 2px solid transparent;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .dynamic-field::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .dynamic-field:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        border-color: #667eea;
    }

    .dynamic-field:hover::before {
        transform: scaleX(1);
    }

    /* Header del campo */
    .field-header {
        margin-bottom: 1rem;
    }

    .form-label {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
    }

    .field-icon {
        font-size: 1.4rem;
        margin-right: 0.75rem;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }

    .field-title {
        flex: 1;
    }

    .required-badge {
        background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
    }

    .field-description {
        color: #64748b;
        font-style: italic;
        margin-top: 0.25rem;
        display: block;
    }

    /* Contenido del campo */
    .field-content {
        position: relative;
    }

    /* Controles de formulario mejorados */
    .form-control, .form-select {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.875rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f8fafc;
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        background: white;
        transform: translateY(-1px);
    }

    .form-control::placeholder {
        color: #94a3b8;
        font-style: italic;
    }

    /* File upload mejorado */
    .file-upload-container {
        position: relative;
    }

    .file-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .file-drop-zone {
        border: 3px dashed #cbd5e0;
        border-radius: 16px;
        padding: 2rem;
        text-align: center;
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .file-drop-zone::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .file-drop-zone:hover {
        border-color: #667eea;
        transform: scale(1.02);
        box-shadow: 0 8px 32px rgba(102, 126, 234, 0.2);
    }

    .file-drop-zone:hover::before {
        opacity: 1;
    }

    .file-drop-zone.dragover {
        border-color: #48bb78;
        background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
        transform: scale(1.05);
    }

    .drop-content {
        position: relative;
        z-index: 2;
    }

    .drop-icon {
        color: #667eea;
        margin-bottom: 1rem;
        opacity: 0.8;
    }

    .drop-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .drop-text {
        color: #64748b;
        margin-bottom: 1rem;
        font-size: 0.95rem;
    }

    .drop-formats {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .format-tag {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 16px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }

    /* Preview de archivos */
    .file-preview {
        margin-top: 1rem;
        min-height: 60px;
    }

    .image-preview {
        position: relative;
        display: inline-block;
        margin: 0.5rem;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .image-preview:hover {
        transform: scale(1.05);
    }

    .image-preview img {
        max-width: 200px;
        max-height: 150px;
        object-fit: cover;
        border-radius: 12px;
    }

    .file-info {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        margin: 0.5rem 0;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    .file-info:hover {
        border-color: #667eea;
        background: linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(102, 126, 234, 0.2);
    }

    .file-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        color: white;
        font-size: 1.5rem;
        box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
    }

    .file-details h6 {
        margin: 0 0 0.25rem 0;
        color: #2d3748;
        font-weight: 600;
    }

    .file-details small {
        color: #64748b;
    }

    .remove-file {
        background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        border: none;
        color: white;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: auto;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
    }

    .remove-file:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 16px rgba(255, 107, 107, 0.4);
    }

    .file-info-footer {
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px solid #e2e8f0;
    }

    /* Radio y checkbox grupos */
    .radio-group, .checkbox-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin-top: 0.75rem;
    }

    .form-check-custom {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .form-check-custom::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .form-check-custom:hover {
        border-color: #667eea;
        background: linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(102, 126, 234, 0.2);
    }

    .form-check-custom:hover::before {
        transform: scaleX(1);
    }

    .form-check-custom input:checked + label {
        color: #667eea;
        font-weight: 600;
    }

    /* Mensajes de error */
    .invalid-feedback {
        background: linear-gradient(135deg, #fed7d7, #feb2b2);
        color: #c53030;
        padding: 0.75rem;
        border-radius: 8px;
        margin-top: 0.5rem;
        font-weight: 500;
        border-left: 4px solid #e53e3e;
    }

    /* Botón de envío */
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 12px;
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(102, 126, 234, 0.4);
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    }

    /* Animaciones */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dynamic-field {
        animation: fadeInUp 0.6s ease forwards;
    }

    .dynamic-field:nth-child(even) {
        animation-delay: 0.1s;
    }

    .dynamic-field:nth-child(odd) {
        animation-delay: 0.2s;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-section {
            padding: 1.5rem;
        }
        
        .section-title {
            font-size: 1.5rem;
        }
        
        .dynamic-field {
            padding: 1rem;
        }
        
        .radio-group, .checkbox-group {
            grid-template-columns: 1fr;
        }
        
        .file-drop-zone {
            padding: 1.5rem;
        }
        
        .drop-title {
            font-size: 1.1rem;
        }
    }

    /* Estados de carga */
    .loading {
        opacity: 0.7;
        pointer-events: none;
    }

    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid #667eea;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

@endsection

@section('scripts')
<!-- Leaflet CSS y JS (OpenStreetMap - Gratuito) - Siempre cargar para evitar problemas -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<!-- Nuestro Leaflet Map Manager -->
<script src="{{ asset('js/leaflet-map-manager.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, configurando formulario...');
    
    // Configurar todos los inputs de archivo
    document.querySelectorAll('input[type="file"][data-field-index]').forEach(function(input) {
        const fieldIndex = input.getAttribute('data-field-index');
        const fieldType = input.getAttribute('data-field-type');
        
        console.log('Configurando input:', fieldIndex, fieldType);
        
        input.addEventListener('change', function() {
            console.log('Archivo seleccionado en campo:', fieldIndex);
            handleFilePreview(this, fieldIndex, fieldType);
        });
    });
    
    // Configurar drag and drop
    document.querySelectorAll('.file-drop-zone').forEach(function(dropZone) {
        const container = dropZone.closest('.file-upload-container');
        const input = container ? container.querySelector('input[type="file"]') : null;
        
        if (!input) {
            console.log('No se encontró input para esta zona de drop');
            return;
        }
        
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });
        
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            if (input && e.dataTransfer.files.length > 0) {
                input.files = e.dataTransfer.files;
                const fieldIndex = input.getAttribute('data-field-index');
                const fieldType = input.getAttribute('data-field-type');
                if (fieldIndex && fieldType) {
                    handleFilePreview(input, fieldIndex, fieldType);
                }
            }
        });
    });
    
    // Manejo de previsualización para el formulario estático (compatibilidad)
    const fileInput = document.getElementById('imagen_usuario');
    const preview = document.getElementById('preview');

    if (fileInput && preview) {
        fileInput.addEventListener('change', function(event) {
            let files = event.target.files;
            let fileArray = Array.from(files);

            preview.innerHTML = '';

            fileArray.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    let reader = new FileReader();

                    reader.onload = function(e) {
                        let imgContainer = document.createElement('div');
                        imgContainer.classList.add('img-container');

                        let img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '200px';
                        img.style.margin = '10px';
                        img.style.borderRadius = '8px';

                        let removeBtn = document.createElement('button');
                        removeBtn.textContent = 'Eliminar';
                        removeBtn.classList.add('btn', 'btn-danger', 'remove-btn');
                        removeBtn.style.display = 'block';
                        removeBtn.style.marginTop = '5px';
                        removeBtn.type = 'button';

                        removeBtn.addEventListener('click', function() {
                            imgContainer.remove();
                            fileArray.splice(index, 1);
                            let dataTransfer = new DataTransfer();
                            fileArray.forEach(file => dataTransfer.items.add(file));
                            document.getElementById('imagen_usuario').files = dataTransfer.files;
                        });

                        imgContainer.appendChild(img);
                        imgContainer.appendChild(removeBtn);
                        preview.appendChild(imgContainer);
                    };

                    reader.readAsDataURL(file);
                }
            });
        });
    }
    
    // Inicializar mapa de Leaflet para selección de ubicación (solo si está presente)
    if (document.getElementById('map')) {
        console.log('Inicializando mapa de selección con Leaflet...');
        
        // Esperar a que LeafletMapManager esté disponible
        const initMap = () => {
            if (typeof window.LeafletMapManager !== 'undefined') {
                const mapData = window.LeafletMapManager.initSelectMap('map', {
                    center: [-25.2637, -57.5759], // Paraguay
                    zoom: 13,
                    latInputId: 'latitud',
                    lngInputId: 'longitud'
                });

                if (mapData) {
                    console.log('Mapa de selección inicializado correctamente');

                    // Búsqueda de ubicación
                    const searchBtn = document.getElementById('search-btn');
                    const searchInput = document.getElementById('ubicacion-search');
                    const searchResults = document.getElementById('search-results');

                    if (searchBtn && searchInput) {
                        searchBtn.addEventListener('click', async function() {
                            const query = searchInput.value.trim();
                            if (!query) {
                                alert('Por favor ingrese una ubicación para buscar');
                                return;
                            }

                            searchBtn.disabled = true;
                            searchBtn.textContent = 'Buscando...';
                            searchResults.textContent = '';

                            try {
                                const result = await window.LeafletMapManager.searchLocation(query, 'map');
                                searchResults.innerHTML = `<i class="fas fa-check text-success"></i> Ubicación encontrada: ${result.address}`;
                                searchResults.style.color = '#10b981';
                            } catch (error) {
                                searchResults.innerHTML = `<i class="fas fa-exclamation-triangle text-warning"></i> No se encontró la ubicación. Intenta con términos más específicos.`;
                                searchResults.style.color = '#f59e0b';
                            } finally {
                                searchBtn.disabled = false;
                                searchBtn.textContent = 'Buscar';
                            }
                        });

                        // Buscar al presionar Enter
                        searchInput.addEventListener('keypress', function(e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                searchBtn.click();
                            }
                        });
                    }
                } else {
                    console.error('Error inicializando el mapa de selección');
                }
            } else {
                // Si LeafletMapManager no está disponible, esperar un poco más
                console.log('Esperando a que se cargue LeafletMapManager...');
                setTimeout(initMap, 100);
            }
        };

        // Iniciar el proceso de inicialización
        initMap();
    } else {
        console.log('No se encontró elemento de mapa, omitiendo inicialización');
    }
});

// Función para obtener el icono según el tipo de archivo
function getFileIcon(mimeType, fileName) {
    const extension = fileName.split('.').pop().toLowerCase();
    
    if (mimeType.startsWith('image/')) return 'image';
    if (mimeType.includes('pdf') || extension === 'pdf') return 'file-pdf';
    if (mimeType.includes('word') || ['doc', 'docx'].includes(extension)) return 'file-word';
    if (mimeType.includes('excel') || ['xls', 'xlsx'].includes(extension)) return 'file-excel';
    if (mimeType.includes('powerpoint') || ['ppt', 'pptx'].includes(extension)) return 'file-powerpoint';
    if (mimeType.startsWith('text/') || ['txt', 'csv'].includes(extension)) return 'file-alt';
    if (mimeType.startsWith('video/')) return 'file-video';
    if (mimeType.startsWith('audio/')) return 'file-audio';
    return 'file';
}

// Función para formatear el tamaño del archivo
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Función para remover archivos
function removeFile(fieldIndex) {
    const input = document.getElementById('field_' + fieldIndex);
    const previewContainer = document.getElementById('preview_' + fieldIndex);
    
    if (input) {
        input.value = '';
    }
    
    if (previewContainer) {
        previewContainer.innerHTML = `
            <div class="alert alert-info" style="margin: 1rem 0; padding: 1rem; background: linear-gradient(135deg, #e3f2fd, #bbdefb); border: none; border-radius: 8px; color: #1565c0;">
                <i class="fas fa-info-circle"></i> Archivo eliminado. Seleccione otro archivo si lo desea.
            </div>
        `;
        
        // Remover el mensaje después de 3 segundos
        setTimeout(function() {
            if (previewContainer) {
                previewContainer.innerHTML = '';
            }
        }, 3000);
    }
}
</script>
@endsection


