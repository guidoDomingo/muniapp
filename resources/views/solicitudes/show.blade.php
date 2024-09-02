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
            <div class="qr-container">
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
</style>
@endsection
