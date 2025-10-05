@extends('layouts.admin')

@section('title', 'Gestión de Departamentos - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Departamentos</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Departamentos</li>
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
                        <form method="GET" action="{{ route('admin.departments.index') }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="search">Buscar</label>
                                        <input type="text" name="search" id="search" class="form-control" 
                                               placeholder="Nombre o descripción..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
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
                                            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
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

        <!-- Departments Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-building mr-1"></i>
                            Lista de Departamentos
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Nuevo Departamento
                            </a>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Responsable</th>
                                    <th>Usuarios</th>
                                    <th>Trámites</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departments as $department)
                                <tr>
                                    <td>{{ $department->id }}</td>
                                    <td>
                                        <strong>{{ $department->name }}</strong>
                                        @if($department->description)
                                            <br>
                                            <small class="text-muted">{{ Str::limit($department->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($department->responsibleUser)
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $department->responsibleUser->avatar_url }}" 
                                                     class="img-circle img-size-32 mr-2" alt="Avatar">
                                                <div>
                                                    <strong>{{ $department->responsibleUser->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $department->responsibleUser->email }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Sin responsable</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $department->users_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $department->tramites_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $department->is_active ? 'success' : 'secondary' }}">
                                            {{ $department->is_active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.departments.show', $department) }}" 
                                               class="btn btn-sm btn-outline-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.departments.edit', $department) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteDepartment({{ $department->id }})" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No se encontraron departamentos</h5>
                                            <p class="text-muted">Crea el primer departamento para organizar el trabajo</p>
                                            <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Crear Primer Departamento
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($departments->hasPages())
                    <div class="card-footer">
                        {{ $departments->appends(request()->query())->links() }}
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
                        <h3>{{ $departments->count() }}</h3>
                        <p>Total Departamentos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $departments->where('is_active', true)->count() }}</h3>
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
                        <h3>{{ $departments->where('responsible_user_id', '!=', null)->count() }}</h3>
                        <p>Con Responsable</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $departments->sum('users_count') ?? 0 }}</h3>
                        <p>Total Usuarios</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
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
                <p>¿Estás seguro de que deseas eliminar este departamento?</p>
                <p class="text-muted"><small>Esta acción no se puede deshacer.</small></p>
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
function deleteDepartment(departmentId) {
    $('#deleteForm').attr('action', '/admin/departments/' + departmentId);
    $('#deleteModal').modal('show');
}

// Auto-submit search form on status change
$('#status').change(function() {
    $(this).closest('form').submit();
});
</script>
@endpush