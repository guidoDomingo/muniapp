@extends('muni')

@section('content')
<div class="container-fluid">
    <h1 class="text-center mb-4">Trámites Disponibles</h1>
    <div class="row">
        @foreach($tramites as $tramite)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">{{ $tramite->nombre }}</h5>
                    <p class="card-text">{{ $tramite->descripcion }}</p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('tramites.show', $tramite->id) }}" class="btn btn-primary w-100">Ver</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
