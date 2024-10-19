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
</style>
@endsection

@section('scripts')

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCmCOQkQoH7KDvifqpLNcrcLDl4lbhAT1Q&callback=initMap" async defer></script>

    <script>
        function initMap() {
            // Latitud y longitud guardadas en la base de datos
            const latitud = {{ $solicitud->latitud }};
            const longitud = {{ $solicitud->longitud }};

            // Crea el mapa centrado en la ubicación guardada
            const location = { lat: latitud, lng: longitud };
            const map = new google.maps.Map(document.getElementById('mapa'), {
                zoom: 13,
                center: location
            });

            // Añadir un marcador en la ubicación guardada
            const marker = new google.maps.Marker({
                position: location,
                map: map
            });
        }
    </script>

    <!-- Cargar TinyMCE desde el CDN -->
    <script src="https://cdn.tiny.cloud/1/h7y0e8vsfrtfu3bejiv58jkgykpsem3y4be37m3p2gjtq85b/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

    <!-- Inicializar TinyMCE -->
    <script>
        @role('admin')
            tinymce.init({
            selector: '#comentarios',
            plugins: [
                // Core editing features
                'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
                // Your account includes a free trial of TinyMCE premium features
                // Try the most popular premium features until Nov 1, 2024:
                'checklist', 'mediaembed', 'casechange', 'export', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown',
            ],
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            language: 'es',
            mergetags_list: [
                { value: 'First.Name', title: 'First Name' },
                { value: 'Email', title: 'Email' },
            ],
            ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
            });
        @else
            tinymce.init({
                selector: '#comentarios',
                plugins: 'advlist autolink link image lists charmap print preview hr anchor pagebreak',
                toolbar: false, // Deshabilitar la barra de herramientas
                menubar: false, // Deshabilitar el menú
                height: 300,
                readonly: true // Establecer el modo de solo lectura
            });
        @endrole
    </script>

@endsection
