@extends('muni')

@section('content')
    <h1 class="text-center mb-4">Mis Solicitudes</h1>

    <div class="container">
        <div class="row">
            @foreach($solicitudes as $solicitud)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Trámite: {{ $solicitud->tramite->nombre }}</h5>
                        <p><strong>ID:</strong> {{ $solicitud->id }}</p>
                        <p><strong>Estado:</strong> {{ $solicitud->estado }}</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('solicitudes.show', $solicitud->id) }}" class="btn btn-primary w-100">Ver</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
@endsection
