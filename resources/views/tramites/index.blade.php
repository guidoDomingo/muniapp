@extends('muni')

@section('content')
<div class="container-fluid">
    <h1 class="text-center mb-4">Trámites Disponibles</h1>
    <div class="table-responsive">
        <table id="tramites-table" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tramites as $tramite)
                <tr>
                    <td>{{ $tramite->id }}</td>
                    <td>{{ $tramite->nombre }}</td>
                    <td>{{ $tramite->descripcion }}</td>
                    <td>
                        <a href="{{ route('tramites.show', $tramite->id) }}" class="btn btn-primary">Ver</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
