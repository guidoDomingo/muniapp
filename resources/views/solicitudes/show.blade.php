@extends('muni')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Solicitud de Trámite: {{ $solicitud->tramite->nombre }} - {{ $solicitud->estado }}</h2>
        </div>
        <div class="card-body">
            <p><strong>Detalles:</strong> {{ $solicitud->detalles }}</p>
            <p><strong>Estado:</strong> <span class="badge badge-primary">{{ $solicitud->estado }}</span></p>


            <!-- Mostrar los campos del formulario -->
            <div class="formulario-campos">
                @foreach(json_decode($solicitud->formulario, true)['campos'] as $campo)
                    <div class="form-group">
                        <label><strong>{{ ucwords(str_replace('_', ' ', $campo['nombre'])) }}:</strong></label>
                        @if($campo['tipo'] === 'image')
                            <!-- Mostrar la imagen en miniatura -->
                            <div class="thumbnail-container">
                                <img src="{{ asset('storage/' . $campo['valor']) }}" class="thumbnail-img" alt="Imagen">
                                <a href="{{ asset('storage/' . $campo['valor']) }}" class="btn btn-danger btn-pdf" target="_blank">Descargar imagen</a>
                            </div>
                        @elseif($campo['tipo'] === 'file')
                            <!-- Mostrar un botón prominente para el PDF -->
                            <a href="{{ asset('storage/' . $campo['valor']) }}" class="btn btn-danger btn-pdf" target="_blank">Ver Documento</a>
                        @else
                            <p>{{ $campo['valor'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="form-group mb-4">
                <label for="mapa">Ubicación guardada:</label>
                <div id="mapa" style="width: 100%; height: 400px;"></div>
            </div>

            @role('admin')
                <!-- Mostrar formulario para cambiar el estado de la solicitud -->
                <form action="{{ route('solicitudes.updateEstado', $solicitud->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="estado"><strong>Estado:</strong></label>
                        <select name="estado" id="estado" class="form-control">
                            <option value="pendiente" {{ $solicitud->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="aprobado" {{ $solicitud->estado == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                            <option value="rechazado" {{ $solicitud->estado == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success mt-2">Actualizar Estado</button>
                </form>
            @endrole

            @role('admin')
                <form action="{{ route('solicitudes.updateComentario', $solicitud->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <!-- Mostrar formulario para agregar un comentario -->
                    <div class="form-group mt-4">
                        <label for="comentarios">Comentario:</label>
                        <textarea name="comentarios" id="comentarios" class="form-control" rows="10">
                            {{ old('comentarios', $solicitud->comentario) }}
                        </textarea>
                    </div>
                    <button type="submit" class="btn btn-success mt-2">Guardar</button>
                </form>

            @else
                <!-- Si no es admin, mostrar el comentario en el editor pero deshabilitado -->
                <label for="comentarios">Respuesta:</label>
                <textarea id="comentarios" class="form-control" rows="10" readonly>
                    {!! old('comentarios', $solicitud->comentario) !!}
                </textarea>
            @endrole

            <div class="qr-container mt-4">
                <img src="{{ $base64 }}" class="responsive-img" alt="Código QR">
            </div>

            <form class="mt-4" action="{{ route('solicitudes.qr') }}" method="POST">
                @csrf
                <input type="hidden" name="url" value="{{ url('solicitudes/' . $solicitud->id) }}">
                <button type="submit" class="btn btn-primary">Generar QR</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* CSS para que la imagen sea responsiva y centrada a la izquierda */
    .responsive-img {
        max-width: 100%;
        height: auto;
        display: block;
        float: left;
        margin-right: 15px;
    }
    .qr-container {
        overflow: hidden; /* Asegura que el contenido flotante no se desborde */
        margin-bottom: 20px; /* Espacio debajo del QR */
    }
    .formulario-campos {
        margin-bottom: 20px;
    }
    .thumbnail-container {
        margin-top: 10px;
    }
    .thumbnail-img {
        max-width: 150px; /* Tamaño de miniatura */
        max-height: 150px;
        object-fit: cover; /* Mantener la proporción de la imagen */
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 5px;
        margin-bottom: 10px;
    }
    .btn-pdf {
        display: inline-block;
        margin-top: 10px;
        padding: 10px 20px;
        background-color: #d9534f;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }
    .btn-pdf:hover {
        background-color: #c9302c;
        color: white;
    }
    
    /* Estilos adicionales para el editor simple */
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #374151;
    }
    
    /* Ocultar el textarea original cuando se usa el editor */
    .simple-editor-container + textarea {
        display: none !important;
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
    <script>
        // Variable global para evitar conflictos
        window.initMap = function() {
            // Verificar que el elemento del mapa existe
            const mapElement = document.getElementById('mapa');
            if (!mapElement) {
                console.error('Elemento mapa no encontrado');
                return;
            }

            // Latitud y longitud guardadas en la base de datos
            const latitud = {{ $solicitud->latitud ?? 'null' }};
            const longitud = {{ $solicitud->longitud ?? 'null' }};

            // Verificar que tenemos coordenadas válidas
            if (latitud === null || longitud === null) {
                mapElement.innerHTML = '<div class="alert alert-warning">No hay ubicación guardada para esta solicitud</div>';
                return;
            }

            try {
                // Crea el mapa centrado en la ubicación guardada
                const location = { lat: parseFloat(latitud), lng: parseFloat(longitud) };
                const map = new google.maps.Map(mapElement, {
                    zoom: 13,
                    center: location,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });

                // Añadir un marcador en la ubicación guardada
                const marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    title: 'Ubicación de la solicitud'
                });

                // Añadir info window
                const infoWindow = new google.maps.InfoWindow({
                    content: '<div><strong>Ubicación de la solicitud</strong><br>Lat: ' + latitud + '<br>Lng: ' + longitud + '</div>'
                });

                marker.addListener('click', function() {
                    infoWindow.open(map, marker);
                });

            } catch (error) {
                console.error('Error inicializando Google Maps:', error);
                mapElement.innerHTML = '<div class="alert alert-danger">Error cargando el mapa</div>';
            }
        };

        // Función de callback para errores de Google Maps
        window.gm_authFailure = function() {
            console.error('Error de autenticación de Google Maps');
            const mapElement = document.getElementById('mapa');
            if (mapElement) {
                mapElement.innerHTML = '<div class="alert alert-danger">Error de autenticación de Google Maps</div>';
            }
        };
    </script>
    
    <!-- Cargar Google Maps API de forma asíncrona -->
    <script async defer 
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCmCOQkQoH7KDvifqpLNcrcLDl4lbhAT1Q&callback=initMap&loading=async">
    </script>
    @else
    <script>
        // Si no hay coordenadas, mostrar mensaje
        document.addEventListener('DOMContentLoaded', function() {
            const mapElement = document.getElementById('mapa');
            if (mapElement) {
                mapElement.innerHTML = '<div class="alert alert-info">No hay ubicación guardada para esta solicitud</div>';
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
