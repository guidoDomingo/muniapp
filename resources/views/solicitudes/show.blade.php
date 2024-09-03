@extends('muni')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Solicitud de Trámite: {{ $solicitud->tramite->nombre }}</h2>
        </div>
        <div class="card-body">
            <p><strong>Detalles:</strong> {{ $solicitud->detalles }}</p>
            <p><strong>Estado:</strong> {{ $solicitud->estado }}</p>

            <!-- Mostrar los campos del formulario -->
            <div class="formulario-campos">
                @foreach(json_decode($solicitud->formulario, true)['campos'] as $campo)
                    <div class="form-group">
                        <label><strong>{{ ucwords(str_replace('_', ' ', $campo['nombre'])) }}:</strong></label>
                        @if($campo['tipo'] === 'image')
                            <!-- Mostrar la imagen en miniatura -->
                            <div class="thumbnail-container">
                                <img src="{{ asset('storage/' . $campo['valor']) }}" class="thumbnail-img" alt="Imagen">
                            </div>
                        @elseif($campo['tipo'] === 'file' && pathinfo($campo['valor'], PATHINFO_EXTENSION) === 'pdf')
                            <!-- Mostrar un botón prominente para el PDF -->
                            <a href="{{ asset('storage/' . $campo['valor']) }}" class="btn btn-danger btn-pdf" target="_blank">Ver PDF</a>
                        @else
                            <p>{{ $campo['valor'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="qr-container">
                <img src="{{ $base64 }}" class="responsive-img" alt="Código QR">
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
