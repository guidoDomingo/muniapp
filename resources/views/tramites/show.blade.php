@extends('muni')

@section('content')
    <h1>{{ $tramite->nombre }}</h1>
    <p>{{ $tramite->descripcion }}</p>
    <a href="{{ route('solicitudes.create', $tramite->id) }}">Solicitar este trámite</a>
@endsection
