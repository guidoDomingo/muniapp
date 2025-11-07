@extends('muni')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="card-title mb-1">{{ $solicitud->tramite->nombre }}</h2>
                <p class="text-muted mb-0">Solicitud #{{ $solicitud->id }} - {{ \Carbon\Carbon::parse($solicitud->created_at)->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                @switch($solicitud->estado)
                    @case('pendiente')
                        <span class="badge bg-warning text-dark fs-6">{{ ucfirst($solicitud->estado) }}</span>
                        @break
                    @case('aprobado')
                        <span class="badge bg-success fs-6">{{ ucfirst($solicitud->estado) }}</span>
                        @break
                    @case('rechazado')
                        <span class="badge bg-danger fs-6">{{ ucfirst($solicitud->estado) }}</span>
                        @break
                    @case('en_revision')
                        <span class="badge bg-info fs-6">En Revisión</span>
                        @break
                    @case('en_proceso')
                        <span class="badge bg-primary fs-6">En Proceso</span>
                        @break
                    @default
                        <span class="badge bg-secondary fs-6">{{ ucfirst($solicitud->estado) }}</span>
                @endswitch
            </div>
        </div>
        <div class="card-body">
            <!-- Información básica -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h5 class="text-primary mb-3">Descripción del Trámite</h5>
                    <p class="text-muted">{{ $solicitud->tramite->descripcion }}</p>
                    
                    @if($solicitud->detalles)
                        <h6 class="mt-3">Detalles Adicionales</h6>
                        <p>{{ $solicitud->detalles }}</p>
                    @endif
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="card-title">Información del Trámite</h6>
                            <p class="mb-1"><strong>Tiempo estimado:</strong> {{ $solicitud->tramite->tiempo_estimado ?? 'No especificado' }}</p>
                            <p class="mb-1"><strong>Costo:</strong> {{ $solicitud->tramite->costo ?? 'Gratuito' }}</p>
                            <p class="mb-3"><strong>Estado actual:</strong> {{ ucfirst($solicitud->estado) }}</p>
                            
                            <!-- Botón de Chat -->
                            <a href="/chat?type=solicitud&room={{ $solicitud->tracking_code ?: 'SOL-' . $solicitud->id }}" 
                               class="btn btn-primary btn-sm w-100 mb-2" target="_blank">
                                <i class="fas fa-comments me-2"></i>Chat con Soporte
                            </a>
                            
                            <!-- Código de seguimiento -->
                            <div class="mt-2 p-2 bg-white rounded">
                                <small class="text-muted">Código de seguimiento:</small><br>
                                <strong class="text-primary">{{ $solicitud->tracking_code ?: 'SOL-' . $solicitud->id }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campos dinámicos del formulario -->
            @if($solicitud->tramite->form_fields && count($solicitud->tramite->form_fields) > 0)
                <div class="row">
                    <div class="col-12">
                        <h5 class="text-primary mb-3">Datos de la Solicitud</h5>
                        <div class="form-data-container">
                            @php
                                $formData = json_decode($solicitud->formulario, true);
                                $campos = $formData['campos'] ?? [];
                            @endphp
                            
                            @foreach($solicitud->tramite->form_fields as $index => $field)
                                @php
                                    $fieldLabel = $field['label'] ?? "Campo {$index}";
                                    $fieldType = $field['type'] ?? 'text';
                                    $fieldValue = '';
                                    
                                    // Buscar el valor correspondiente en los campos guardados
                                    foreach($campos as $campo) {
                                        if($campo['nombre'] === $fieldLabel) {
                                            $fieldValue = $campo['valor'];
                                            break;
                                        }
                                    }
                                @endphp
                                
                                <div class="form-group mb-4">
                                    <label class="form-label fw-bold">{{ $fieldLabel }}:</label>
                                    
                                    @switch($fieldType)
                                        @case('file')
                                            @if($fieldValue)
                                                <div class="file-preview">
                                                    <a href="{{ url('storage/' . $fieldValue) }}" 
                                                       class="btn btn-outline-primary" 
                                                       target="_blank">
                                                        <i class="fas fa-file-download me-2"></i>Ver Documento
                                                    </a>
                                                    <small class="text-muted d-block mt-1">{{ basename($fieldValue) }}</small>
                                                </div>
                                            @else
                                                <p class="text-muted">No se adjuntó archivo</p>
                                            @endif
                                            @break
                                            
                                        @case('image')
                                            @if($fieldValue)
                                                <div class="image-preview">
                                                    <img src="{{ url('storage/' . $fieldValue) }}" 
                                                         class="img-thumbnail" 
                                                         style="max-width: 200px; max-height: 200px;"
                                                         alt="Imagen adjunta">
                                                    <div class="mt-2">
                                                        <a href="{{ url('storage/' . $fieldValue) }}" 
                                                           class="btn btn-sm btn-outline-primary" 
                                                           target="_blank">
                                                            <i class="fas fa-eye me-1"></i>Ver en tamaño completo
                                                        </a>
                                                    </div>
                                                </div>
                                            @else
                                                <p class="text-muted">No se adjuntó imagen</p>
                                            @endif
                                            @break
                                            
                                        @case('textarea')
                                            <div class="border rounded p-3 bg-light">
                                                {!! nl2br(e($fieldValue)) !!}
                                            </div>
                                            @break
                                            
                                        @case('select')
                                        @case('radio')
                                            <span class="badge bg-secondary fs-6">{{ $fieldValue ?: 'No seleccionado' }}</span>
                                            @break
                                            
                                        @case('checkbox')
                                            @if($fieldValue)
                                                @php $options = explode(', ', $fieldValue); @endphp
                                                @foreach($options as $option)
                                                    <span class="badge bg-info me-1">{{ $option }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">Ninguna opción seleccionada</span>
                                            @endif
                                            @break
                                            
                                        @default
                                            <p class="mb-0">{{ $fieldValue ?: 'No especificado' }}</p>
                                    @endswitch
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Ubicación si existe -->
            @if($solicitud->latitud && $solicitud->longitud)
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-map-marker-alt me-2"></i>Ubicación de la Solicitud
                        </h5>
                        <div class="location-info mb-3">
                            <p class="mb-1"><strong>Coordenadas:</strong> {{ $solicitud->latitud }}, {{ $solicitud->longitud }}</p>
                            <p class="text-muted mb-0" id="location-address">Cargando dirección...</p>
                        </div>
                        <div id="mapa" style="width: 100%; height: 400px; border-radius: 8px;"></div>
                    </div>
                </div>
            @endif

            <!-- Panel de Administración -->
            @role('admin')
                <div class="row">
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-shield me-2"></i>Panel de Administración
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Cambiar Estado -->
                                    <div class="col-md-6">
                                        <form action="{{ route('solicitudes.updateEstado', $solicitud->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-group mb-3">
                                                <label for="estado" class="form-label fw-bold">Cambiar Estado:</label>
                                                <select name="estado" id="estado" class="form-select">
                                                    <option value="pendiente" {{ $solicitud->estado == 'pendiente' ? 'selected' : '' }}>
                                                        📝 Pendiente
                                                    </option>
                                                    <option value="en_revision" {{ $solicitud->estado == 'en_revision' ? 'selected' : '' }}>
                                                        👀 En Revisión
                                                    </option>
                                                    <option value="en_proceso" {{ $solicitud->estado == 'en_proceso' ? 'selected' : '' }}>
                                                        ⚙️ En Proceso
                                                    </option>
                                                    <option value="aprobado" {{ $solicitud->estado == 'aprobado' ? 'selected' : '' }}>
                                                        ✅ Aprobado
                                                    </option>
                                                    <option value="rechazado" {{ $solicitud->estado == 'rechazado' ? 'selected' : '' }}>
                                                        ❌ Rechazado
                                                    </option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-save me-2"></i>Actualizar Estado
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <!-- QR y Herramientas -->
                                    <div class="col-md-6">
                                        <div class="d-flex flex-column gap-2">
                                            <form action="{{ route('solicitudes.qr') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="url" value="{{ url('solicitudes/' . $solicitud->id) }}">
                                                <button type="submit" class="btn btn-outline-primary">
                                                    <i class="fas fa-qrcode me-2"></i>Generar QR
                                                </button>
                                            </form>
                                            
                                            @if(isset($base64))
                                                <div class="qr-display text-center p-3 border rounded bg-light">
                                                    <img src="{{ $base64 }}" 
                                                         style="max-width: 150px; height: auto;" 
                                                         alt="Código QR">
                                                    <p class="small text-muted mt-2">Código QR de la solicitud</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Comentarios Administrativos -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <form action="{{ route('solicitudes.updateComentario', $solicitud->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-group">
                                                <label for="comentarios" class="form-label fw-bold">
                                                    Comentarios/Respuesta Administrativa:
                                                </label>
                                                <textarea name="comentarios" 
                                                          id="comentarios" 
                                                          class="form-control" 
                                                          rows="6"
                                                          placeholder="Escriba aquí los comentarios o respuesta para el ciudadano...">{{ old('comentarios', $solicitud->comentario) }}</textarea>
                                            </div>
                                            <button type="submit" class="btn btn-success mt-2">
                                                <i class="fas fa-save me-2"></i>Guardar Comentarios
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Vista del Ciudadano -->
                @if($solicitud->comentario)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-comment-dots me-2"></i>Respuesta del Municipio
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="border rounded p-3 bg-light">
                                        {!! nl2br(e($solicitud->comentario)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- QR para el ciudadano si existe -->
                @if(isset($base64))
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <h6>Código QR de tu solicitud</h6>
                            <div class="qr-display d-inline-block p-3 border rounded bg-light">
                                <img src="{{ $base64 }}" 
                                     style="max-width: 200px; height: auto;" 
                                     alt="Código QR">
                            </div>
                            <p class="small text-muted mt-2">Comparte este código para acceso rápido a tu solicitud</p>
                        </div>
                    </div>
                @endif
            @endrole
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .form-data-container .form-group {
        border-left: 4px solid #e3f2fd;
        padding-left: 1rem;
        margin-bottom: 1.5rem;
        background-color: #fafafa;
        padding: 1rem;
        border-radius: 0 8px 8px 0;
    }
    
    .form-data-container .form-label {
        color: #1976d2;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .image-preview img {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        transition: transform 0.2s ease;
    }
    
    .image-preview img:hover {
        transform: scale(1.05);
    }
    
    .file-preview {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .qr-display {
        background: linear-gradient(45deg, #f8f9fa, #ffffff);
    }
    
    .location-info {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        border-left: 4px solid #2196f3;
    }
    
    .card.border-warning .card-header {
        border-bottom: 2px solid #ffc107;
    }
    
    .card.border-info .card-header {
        border-bottom: 2px solid #17a2b8;
    }
    
    .badge.fs-6 {
        font-size: 0.875rem !important;
        padding: 0.5rem 0.75rem;
    }
    
    .form-select, .form-control {
        border-radius: 8px;
        border: 2px solid #e0e0e0;
        transition: border-color 0.3s ease;
    }
    
    .form-select:focus, .form-control:focus {
        border-color: #2196f3;
        box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
    }
    
    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .leaflet-container {
        border: 2px solid #e0e0e0;
        border-radius: 8px !important;
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
document.addEventListener('DOMContentLoaded', function() {
    @if($solicitud->latitud && $solicitud->longitud)
        // Inicializar mapa de visualización si hay coordenadas
        console.log('Inicializando mapa de visualización...');
        
        // Función para intentar inicializar el mapa
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
                    window.LeafletMapManager.reverseGeocode(lat, lng)
                        .then(address => {
                            document.getElementById('location-address').textContent = address;
                        })
                        .catch(error => {
                            console.error('Error obteniendo dirección:', error);
                            document.getElementById('location-address').textContent = `Ubicación: ${lat}, ${lng}`;
                        });
                } else {
                    console.error('Error inicializando el mapa de visualización');
                }
            } else {
                // Si LeafletMapManager no está disponible, esperar un poco más
                console.log('Esperando a que se cargue LeafletMapManager...');
                setTimeout(initDisplayMap, 100);
            }
        };
        
        // Iniciar el proceso de inicialización
        initDisplayMap();
    @endif
});
</script>
    }
    
    /* Mejorar la apariencia del editor en el contexto del formulario */
    .simple-editor-container {
        margin-top: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
</style>
@endsection

@section('scripts')
    @if($solicitud->latitud && $solicitud->longitud)
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
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar mapa de visualización con Leaflet
            const lat = {{ $solicitud->latitud }};
            const lng = {{ $solicitud->longitud }};
            
            console.log('Inicializando mapa de visualización con coordenadas:', lat, lng);
            
            const map = window.LeafletMapManager.initDisplayMap('mapa', lat, lng, {
                zoom: 15,
                scrollWheelZoom: true,
                dragging: true
            });

            if (map) {
                console.log('Mapa de visualización inicializado correctamente');
                
                // Obtener dirección de las coordenadas
                window.LeafletMapManager.reverseGeocode(lat, lng)
                    .then(address => {
                        const addressDiv = document.createElement('div');
                        addressDiv.className = 'mt-2 text-muted';
                        addressDiv.innerHTML = `
                            <small>
                                <i class="fas fa-map-marker-alt"></i> 
                                <strong>Dirección aproximada:</strong> ${address}
                            </small>
                        `;
                        document.getElementById('mapa').parentNode.appendChild(addressDiv);
                    })
                    .catch(error => {
                        console.log('No se pudo obtener la dirección:', error);
                    });
            }
        });
    </script>
    @else
    <script>
        // Si no hay coordenadas, mostrar mensaje
        document.addEventListener('DOMContentLoaded', function() {
            const mapElement = document.getElementById('mapa');
            if (mapElement) {
                mapElement.innerHTML = '<div class="alert alert-info text-center p-4"><i class="fas fa-info-circle"></i><h6>Ubicación no disponible</h6><p class="mb-0">No hay ubicación guardada para esta solicitud</p></div>';
            }
        });
    </script>
    @endif

    <!-- Simple Rich Text Editor (alternativa a TinyMCE sin alertas) -->
    <script src="{{ asset('js/simple-rich-text-editor.js') }}"></script>

    <!-- Inicializar Editor Simple -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const comentariosElement = document.getElementById('comentarios');
            
            if (comentariosElement) {
                @role('admin')
                    // Editor completo para administradores
                    const editor = new SimpleRichTextEditor('comentarios', {
                        height: '400px',
                        placeholder: 'Escriba su comentario aquí...',
                        readonly: false,
                        toolbar: true
                    });
                    console.log('Editor simple inicializado para admin');
                @else
                    // Editor de solo lectura para usuarios
                    const editor = new SimpleRichTextEditor('comentarios', {
                        height: '300px',
                        readonly: true,
                        toolbar: false
                    });
                    console.log('Editor simple inicializado para usuario (solo lectura)');
                @endrole
            } else {
                console.warn('Elemento #comentarios no encontrado');
            }
        });
    </script>

@endsection
