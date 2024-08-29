@extends('muni')

@section('content')
    <h1 class="text-center mb-4">Mis Solicitudes</h1>

    <div class="container">
        <table id="solicitudes-table" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Trámite</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($solicitudes as $solicitud)
                    <tr>
                        <td>{{ $solicitud->id }}</td>
                        <td>{{ $solicitud->tramite->nombre }}</td>
                        <td>{{ $solicitud->estado }}</td>
                        <td>
                            <a href="{{ route('solicitudes.show', $solicitud->id) }}" class="btn btn-primary">Ver</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- @push('scripts')
        <script>
            $(document).ready(function() {
                $('#solicitudes-table').DataTable({
                    responsive: true,
                    autoWidth: false
                });
            });
        </script>
    @endpush --}}
@endsection
