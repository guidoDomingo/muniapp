@extends('muni')

@section('content')
    <h1>Solicitud de Trámite: {{ $solicitud->tramite->nombre }}</h1>
    <p>Detalles: {{ $solicitud->detalles }}</p>
    <p>Estado: {{ $solicitud->estado }}</p>
@endsection
