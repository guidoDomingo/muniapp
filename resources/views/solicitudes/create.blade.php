@extends('muni')

@section('content')
    <h1>Solicitar Trámite: {{ $tramite->nombre }}</h1>
    <form method="POST" action="{{ route('solicitudes.store', $tramite->id) }}">
        @csrf
        <label for="detalles">Detalles:</label>
        <textarea name="detalles" id="detalles" required></textarea>
        <button type="submit">Enviar Solicitud</button>
    </form>
@endsection
