@extends('muni')

@section('content')
    <h1>Mis Solicitudes</h1>
    <ul>
        @foreach($solicitudes as $solicitud)
            <li>
                <a href="{{ route('solicitudes.show', $solicitud->id) }}">
                    {{ $solicitud->tramite->nombre }} - {{ $solicitud->estado }}
                </a>
            </li>
        @endforeach
    </ul>
@endsection
