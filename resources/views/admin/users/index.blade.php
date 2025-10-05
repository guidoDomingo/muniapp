@extends('layouts.admin')

@section('title', 'Gestión de Usuarios - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Usuarios</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Usuarios</li>
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
                        <form method="GET" action="{{ route('admin.users.index') }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="search">Buscar</label>
                                        <input type="text" name="search" id="search" class="form-control" 
                                               placeholder="Nombre o email..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="role">Rol</label>
                                        <select name="role" id="role" class="form-control">
                                            <option value="">Todos los roles</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}" 
                                                    {{ request('role') == $role->name ? 'selected' : '' }}>
                                                    {{ ucfirst($role->name) }}
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
                                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
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

        <!-- Users Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-users mr-1"></i>
                            Lista de Usuarios
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Nuevo Usuario
                            </a>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Avatar</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Departamento</th>
                                    <th>Estado</th>
                                    <th>Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>
                                        <img src="{{ $user->avatar_url }}" alt="Avatar" 
                                             class="img-circle img-size-32">
                                    </td>
                                    <td>
                                        <strong>{{ $user->name }}</strong>
                                        @if($user->dni)
                                            <br><small class="text-muted">DNI: {{ $user->dni }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge badge-{{ $role->name == 'admin' ? 'danger' : ($role->name == 'commission' ? 'warning' : 'info') }}">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        {{ $user->department ? $user->department->name : '-' }}
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $user->is_active ? 'success' : 'secondary' }}">
                                            {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.users.show', $user) }}" 
                                               class="btn btn-sm btn-outline-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($user->id !== auth()->id())
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteUser({{ $user->id }})" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No se encontraron usuarios</h5>
                                            <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($users->hasPages())
                    <div class="card-footer">
                        {{ $users->appends(request()->query())->links() }}
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
                        <h3>{{ $users->total() }}</h3>
                        <p>Total Usuarios</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $users->where('roles.0.name', 'admin')->count() }}</h3>
                        <p>Administradores</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $users->where('roles.0.name', 'commission')->count() }}</h3>
                        <p>Comisión</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $users->where('roles.0.name', 'user')->count() }}</h3>
                        <p>Ciudadanos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user"></i>
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
                <p>¿Estás seguro de que deseas eliminar este usuario?</p>
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
function deleteUser(userId) {
    $('#deleteForm').attr('action', '/admin/users/' + userId);
    $('#deleteModal').modal('show');
}

// Auto-submit search form on role/status change
$('#role, #status').change(function() {
    $(this).closest('form').submit();
});
</script>
@endpush