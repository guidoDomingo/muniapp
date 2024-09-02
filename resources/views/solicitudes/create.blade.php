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
                    <label for="nombre_usuario">Nombre de Usuario:</label>
                    <input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control" value="{{ old('nombre_usuario') }}" required>
                </div>

                <!-- Input para documento_identidad -->
                <div class="form-group">
                    <label for="documento_identidad">Documento de Identidad:</label>
                    <input type="file" name="documento_identidad" id="documento_identidad" class="form-control" required>
                </div>

                <!-- Input para imagen_usuario -->
                <div class="form-group">
                    <label for="imagen_usuario">Imagen de Usuario:</label>
                    <input type="file" name="imagen_usuario" id="imagen_usuario" class="form-control" required>
                </div>

                <!-- Input para detalles -->
                <div class="form-group">
                    <label for="detalles">Detalles:</label>
                    <textarea name="detalles" id="detalles" class="form-control" rows="4" required></textarea>
                </div>

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
@endsection


