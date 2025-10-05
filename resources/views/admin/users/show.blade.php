@extends('layouts.admin')

@section('title', 'Detalles del Usuario - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $user->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuarios</a></li>
                    <li class="breadcrumb-item active">{{ $user->name }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- User Profile -->
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle"
                                 src="{{ $user->avatar_url }}"
                                 alt="User profile picture"
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        </div>

                        <h3 class="profile-username text-center">{{ $user->name }}</h3>

                        <p class="text-muted text-center">
                            @foreach($user->roles as $role)
                                <span class="badge badge-{{ $role->name == 'admin' ? 'danger' : ($role->name == 'commission' ? 'warning' : 'info') }}">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Email</b> <span class="float-right">{{ $user->email }}</span>
                            </li>
                            @if($user->dni)
                            <li class="list-group-item">
                                <b>DNI</b> <span class="float-right">{{ $user->dni }}</span>
                            </li>
                            @endif
                            @if($user->phone)
                            <li class="list-group-item">
                                <b>Teléfono</b> <span class="float-right">{{ $user->phone }}</span>
                            </li>
                            @endif
                            @if($user->department)
                            <li class="list-group-item">
                                <b>Departamento</b> <span class="float-right">{{ $user->department->name }}</span>
                            </li>
                            @endif
                            <li class="list-group-item">
                                <b>Estado</b> 
                                <span class="float-right">
                                    <span class="badge badge-{{ $user->is_active ? 'success' : 'secondary' }}">
                                        {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </span>
                            </li>
                            <li class="list-group-item">
                                <b>Registro</b> <span class="float-right">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                            </li>
                        </ul>

                        <div class="row">
                            <div class="col-6">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </div>
                            <div class="col-6">
                                @if($user->id !== auth()->id())
                                <button type="button" class="btn btn-danger btn-block" onclick="deleteUser({{ $user->id }})">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($user->address)
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Dirección</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">{{ $user->address }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- User Activity -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link active" href="#solicitudes" data-toggle="tab">
                                    Solicitudes ({{ $user->solicitudes->count() }})
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#activity" data-toggle="tab">
                                    Actividad Reciente
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#permissions" data-toggle="tab">
                                    Permisos
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Solicitudes Tab -->
                            <div class="active tab-pane" id="solicitudes">
                                @if($user->solicitudes->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Código</th>
                                                <th>Trámite</th>
                                                <th>Estado</th>
                                                <th>Fecha</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($user->solicitudes->take(10) as $solicitud)
                                            <tr>
                                                <td>
                                                    <span class="badge badge-secondary">{{ $solicitud->tracking_code ?? 'N/A' }}</span>
                                                </td>
                                                <td>{{ $solicitud->tramite->nombre }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $solicitud->status_color ?? 'secondary' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                                                    </span>
                                                </td>
                                                <td>{{ $solicitud->created_at->format('d/m/Y') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.solicitudes.show', $solicitud) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($user->solicitudes->count() > 10)
                                <div class="text-center">
                                    <a href="{{ route('admin.solicitudes.index', ['user' => $user->id]) }}" 
                                       class="btn btn-primary">Ver todas las solicitudes</a>
                                </div>
                                @endif
                                @else
                                <div class="text-center py-4">
                                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Sin solicitudes</h5>
                                    <p class="text-muted">Este usuario no ha creado solicitudes aún.</p>
                                </div>
                                @endif
                            </div>

                            <!-- Activity Tab -->
                            <div class="tab-pane" id="activity">
                                <div class="timeline timeline-inverse">
                                    <!-- User Registration -->
                                    <div class="time-label">
                                        <span class="bg-success">
                                            {{ $user->created_at->format('d M Y') }}
                                        </span>
                                    </div>
                                    <div>
                                        <i class="fas fa-user bg-primary"></i>
                                        <div class="timeline-item">
                                            <h3 class="timeline-header">Usuario registrado</h3>
                                            <div class="timeline-body">
                                                Se registró en el sistema como {{ $user->roles->first()->name ?? 'usuario' }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Recent Solicitudes -->
                                    @foreach($user->solicitudes->take(5) as $solicitud)
                                    <div>
                                        <i class="fas fa-file-alt bg-yellow"></i>
                                        <div class="timeline-item">
                                            <span class="time">
                                                <i class="far fa-clock"></i> {{ $solicitud->created_at->diffForHumans() }}
                                            </span>
                                            <h3 class="timeline-header">Nueva solicitud</h3>
                                            <div class="timeline-body">
                                                Creó solicitud para: <strong>{{ $solicitud->tramite->nombre }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach

                                    <div>
                                        <i class="far fa-clock bg-gray"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Permissions Tab -->
                            <div class="tab-pane" id="permissions">
                                <h4>Roles Asignados</h4>
                                @foreach($user->roles as $role)
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <span class="badge badge-{{ $role->name == 'admin' ? 'danger' : ($role->name == 'commission' ? 'warning' : 'info') }}">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <h6>Permisos incluidos:</h6>
                                        <div class="row">
                                            @foreach($role->permissions as $permission)
                                            <div class="col-md-6">
                                                <span class="badge badge-light">{{ $permission->name }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
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
                <p>¿Estás seguro de que deseas eliminar a <strong>{{ $user->name }}</strong>?</p>
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
</script>
@endpush