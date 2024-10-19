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

                <!-- Input para nombre_usuario -->
                <div class="form-group">
                    <label for="nombre_usuario">Nombre de la comision:</label>
                    <input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control" value="{{ old('nombre_usuario') }}" required>
                </div>

                <!-- Input para documento_identidad -->
                <div class="form-group">
                    <label for="documento_identidad">Documento de vigencia:</label>
                    <input type="file" name="documento_identidad" id="documento_identidad" class="form-control" required>
                </div>

                <!-- Input para múltiples imágenes con previsualización -->
                <div class="form-group">
                    <label for="imagen_usuario">Fotos del lugar:</label>
                    <input type="file" name="imagen_usuario[]" id="imagen_usuario" class="form-control" multiple required>
                </div>

                <!-- Contenedor donde se mostrarán las imágenes seleccionadas -->
                <div id="preview" class="gallery-preview"></div>

                <div class="form-group">
                    <label for="ubicacion">Ubicación:</label>
                    <textarea name="detalles" id="detalles" class="form-control" rows="4" required></textarea>
                </div>

                <!-- Mapa de Google para seleccionar ubicación -->
                <div class="form-group">
                    <label for="map">Seleccione su ubicación en el mapa:</label>
                    <div id="map" style="width: 100%; height: 400px;"></div>
                </div>

                <!-- Campos ocultos para guardar latitud y longitud -->
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
<script>
    document.getElementById('imagen_usuario').addEventListener('change', function(event) {
        let files = event.target.files;
        let preview = document.getElementById('preview');
        let fileArray = Array.from(files); // Convierte la lista de archivos en un array para poder manipularlo

        // Limpiar el contenedor de la previsualización antes de agregar nuevas imágenes
        preview.innerHTML = '';

        // Iterar sobre los archivos seleccionados
        fileArray.forEach((file, index) => {
            // Asegurarse de que el archivo es una imagen
            if (file.type.startsWith('image/')) {
                let reader = new FileReader();

                // Evento para cuando la imagen es cargada
                reader.onload = function(e) {
                    // Crear un contenedor para la imagen y el botón de eliminar
                    let imgContainer = document.createElement('div');
                    imgContainer.classList.add('img-container');

                    // Crear el elemento de la imagen
                    let img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '200px'; // Ajustar el tamaño de la previsualización
                    img.style.margin = '10px';

                    // Crear el botón de eliminar
                    let removeBtn = document.createElement('button');
                    removeBtn.textContent = 'Eliminar';
                    removeBtn.classList.add('btn', 'btn-danger', 'remove-btn');
                    removeBtn.style.display = 'block';
                    removeBtn.style.marginTop = '5px';

                    // Añadir evento de eliminar
                    removeBtn.addEventListener('click', function() {
                        // Eliminar la imagen del contenedor de previsualización
                        imgContainer.remove();

                        // Eliminar el archivo del array de archivos seleccionados
                        fileArray.splice(index, 1);

                        // Actualizar el input de archivos para reflejar el cambio
                        let dataTransfer = new DataTransfer(); // Objeto para actualizar la lista de archivos
                        fileArray.forEach(file => dataTransfer.items.add(file));
                        document.getElementById('imagen_usuario').files = dataTransfer.files;
                    });

                    // Agregar la imagen y el botón al contenedor
                    imgContainer.appendChild(img);
                    imgContainer.appendChild(removeBtn);

                    // Agregar el contenedor de la imagen a la previsualización
                    preview.appendChild(imgContainer);
                };

                // Leer el archivo como una URL de datos
                reader.readAsDataURL(file);
            }
        });
    });
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCmCOQkQoH7KDvifqpLNcrcLDl4lbhAT1Q&callback=initMap" async defer></script>

<script>
    let map;
    let marker;

    function initMap() {
        // Establece una ubicación predeterminada (puedes cambiarla según tus necesidades)
        const defaultLocation = { lat: -25.2637, lng: -57.5759 }; // Ejemplo para Paraguay

        // Inicializa el mapa centrado en la ubicación predeterminada
        map = new google.maps.Map(document.getElementById('map'), {
            center: defaultLocation,
            zoom: 13
        });

        // Añadir marcador en la ubicación predeterminada
        marker = new google.maps.Marker({
            position: defaultLocation,
            map: map,
            draggable: true // El marcador puede ser arrastrado
        });

        // Cuando el usuario mueve el marcador, actualizamos los campos latitud y longitud
        marker.addListener('dragend', function() {
            const position = marker.getPosition();
            document.getElementById('latitud').value = position.lat();
            document.getElementById('longitud').value = position.lng();
        });

        // Permitir clics en el mapa para cambiar la posición del marcador
        map.addListener('click', function(event) {
            marker.setPosition(event.latLng);
            document.getElementById('latitud').value = event.latLng.lat();
            document.getElementById('longitud').value = event.latLng.lng();
        });
    }
</script>



@endsection


