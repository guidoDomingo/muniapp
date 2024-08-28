@extends('muni')

@section('content')
    <h1>Trámites Disponibles</h1>
    <ul>
        @foreach($tramites as $tramite)
            <li><a href="{{ route('tramites.show', $tramite->id) }}">{{ $tramite->nombre }}</a></li>
        @endforeach
    </ul>
@endsection
