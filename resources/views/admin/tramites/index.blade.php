@extends('layouts.admin')

@section('title', 'Gestión de Trámites - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Trámites</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Trámites</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Filters and Search -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.tramites.index') }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="search">Buscar</label>
                                        <input type="text" name="search" id="search" class="form-control" 
                                               placeholder="Nombre o descripción..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="department">Departamento</label>
                                        <select name="department" id="department" class="form-control">
                                            <option value="">Todos los departamentos</option>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}" 
                                                    {{ request('department') == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status">Estado</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="">Todos</option>
                                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Activos</option>
                                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactivos</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search"></i> Buscar
                                            </button>
                                            <a href="{{ route('admin.tramites.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tramites Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard mr-1"></i>
                            Lista de Trámites
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.tramites.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Nuevo Trámite
                            </a>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Departamento</th>
                                    <th>Costo</th>
                                    <th>Días Estimados</th>
                                    <th>Solicitudes</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tramites as $tramite)
                                <tr>
                                    <td>{{ $tramite->id }}</td>
                                    <td>
                                        <strong>{{ $tramite->nombre }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($tramite->descripcion, 50) }}</small>
                                    </td>
                                    <td>
                                        @if($tramite->department)
                                            <span class="badge badge-info">{{ $tramite->department->name }}</span>
                                        @else
                                            <span class="text-muted">Sin departamento</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tramite->cost > 0)
                                            <span class="badge badge-warning">${{ number_format($tramite->cost, 2) }}</span>
                                        @else
                                            <span class="badge badge-success">Gratuito</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tramite->estimated_days)
                                            {{ $tramite->estimated_days }} días
                                        @else
                                            <span class="text-muted">No especificado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $tramite->solicitudes_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $tramite->is_active ? 'success' : 'secondary' }}">
                                            {{ $tramite->is_active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.tramites.show', $tramite) }}" 
                                               class="btn btn-sm btn-outline-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.tramites.edit', $tramite) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteTramite({{ $tramite->id }})" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-clipboard fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No se encontraron trámites</h5>
                                            <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                                            <a href="{{ route('admin.tramites.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Crear Primer Trámite
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($tramites->hasPages())
                    <div class="card-footer">
                        {{ $tramites->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $tramites->total() }}</h3>
                        <p>Total Trámites</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clipboard"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $tramites->where('is_active', true)->count() }}</h3>
                        <p>Activos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $tramites->where('cost', '>', 0)->count() }}</h3>
                        <p>Con Costo</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $tramites->sum('solicitudes_count') ?? 0 }}</h3>
                        <p>Total Solicitudes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este trámite?</p>
                <p class="text-muted"><small>Esta acción no se puede deshacer y eliminará todas las solicitudes asociadas.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteTramite(tramiteId) {
    $('#deleteForm').attr('action', '/admin/tramites/' + tramiteId);
    $('#deleteModal').modal('show');
}

// Auto-submit search form on department/status change
$('#department, #status').change(function() {
    $(this).closest('form').submit();
});
</script>
@endpush