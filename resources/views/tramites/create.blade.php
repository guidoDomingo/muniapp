@extends('muni')

@section('content')
<div class="container">
    <h1>Crear Trámite</h1>
    <form action="{{ route('tramites.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-success mt-2">Guardar</button>
    </form>
</div>
@endsection
