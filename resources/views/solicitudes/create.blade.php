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
                    @foreach($tramite->form_fields as $field)
                        <div class="form-group">
                            <label for="field_{{ $loop->index }}">
                                {{ $field['label'] ?? 'Campo' }}
                                @if($field['required'] ?? false)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            
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
                                            class="form-control @error('form_data.'.$loop->index) is-invalid @enderror" 
                                            {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                        <option value="">Seleccionar...</option>
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
                                        @foreach($field['options'] as $optionIndex => $option)
                                            <div class="form-check">
                                                <input type="radio" 
                                                       name="form_data[{{ $loop->parent->index }}]" 
                                                       id="field_{{ $loop->parent->index }}_{{ $optionIndex }}" 
                                                       value="{{ $option }}" 
                                                       class="form-check-input @error('form_data.'.$loop->parent->index) is-invalid @enderror" 
                                                       {{ old('form_data.'.$loop->parent->index) == $option ? 'checked' : '' }}
                                                       {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                                <label class="form-check-label" for="field_{{ $loop->parent->index }}_{{ $optionIndex }}">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                    @endif
                                    @break
                                
                                @case('checkbox')
                                    @if(isset($field['options']) && is_array($field['options']))
                                        @foreach($field['options'] as $optionIndex => $option)
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       name="form_data[{{ $loop->parent->index }}][]" 
                                                       id="field_{{ $loop->parent->index }}_{{ $optionIndex }}" 
                                                       value="{{ $option }}" 
                                                       class="form-check-input @error('form_data.'.$loop->parent->index) is-invalid @enderror" 
                                                       {{ in_array($option, old('form_data.'.$loop->parent->index, [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="field_{{ $loop->parent->index }}_{{ $optionIndex }}">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                    @endif
                                    @break
                                
                                @case('file')
                                    <input type="file" 
                                           name="form_data[{{ $loop->index }}]" 
                                           id="field_{{ $loop->index }}" 
                                           class="form-control @error('form_data.'.$loop->index) is-invalid @enderror" 
                                           {{ ($field['required'] ?? false) ? 'required' : '' }}>
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
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach
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

                {{-- Campos de ubicación (siempre presentes) --}}
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
                    <div id="map" style="width: 100%; height: 400px;"></div>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Arrastra el marcador o haz clic en el mapa para seleccionar la ubicación exacta
                    </small>
                </div>

                <input type="hidden" name="latitud" id="latitud">
                <input type="hidden" name="longitud" id="longitud">

                <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card {
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    textarea {
        resize: vertical;
    }
</style>
<style>
    .gallery-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .gallery-preview img {
        border: 2px solid #ddd;
        border-radius: 5px;
        padding: 5px;
    }
</style>

@endsection

@section('scripts')
<!-- Leaflet CSS y JS (OpenStreetMap - Gratuito) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<!-- Nuestro Leaflet Map Manager -->
<script src="{{ asset('js/leaflet-map-manager.js') }}"></script>

<script>
    // Script para previsualización de imágenes
    document.addEventListener('DOMContentLoaded', function() {
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

        // Inicializar mapa de Leaflet para selección de ubicación
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
    }
});
</script>
@endsection


