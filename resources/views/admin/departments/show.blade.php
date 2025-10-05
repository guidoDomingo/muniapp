@extends('layouts.admin')

@section('title', 'Ver Departamento - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $department->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.departments.index') }}">Departamentos</a></li>
                    <li class="breadcrumb-item active">Ver</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Department Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-building mr-1"></i>
                            Información del Departamento
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-{{ $department->is_active ? 'success' : 'secondary' }} badge-lg">
                                {{ $department->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                            <div class="btn-group ml-2">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-cogs"></i> Acciones
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ route('admin.departments.edit', $department) }}">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('admin.users.index', ['department' => $department->id]) }}">
                                        <i class="fas fa-users"></i> Ver Empleados
                                    </a>
                                    <a class="dropdown-item" href="{{ route('admin.tramites.index', ['department' => $department->id]) }}">
                                        <i class="fas fa-file-alt"></i> Ver Trámites
                                    </a>
                                    <a class="dropdown-item" href="{{ route('admin.solicitudes.index', ['department' => $department->id]) }}">
                                        <i class="fas fa-list"></i> Ver Solicitudes
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" onclick="generateReport()">
                                        <i class="fas fa-file-pdf"></i> Generar Reporte
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <dl class="row">
                                    <dt class="col-sm-3">Descripción:</dt>
                                    <dd class="col-sm-9">{{ $department->description ?? 'Sin descripción' }}</dd>
                                    
                                    <dt class="col-sm-3">Responsable:</dt>
                                    <dd class="col-sm-9">{{ $department->head_name ?? 'No asignado' }}</dd>
                                    
                                    <dt class="col-sm-3">Email:</dt>
                                    <dd class="col-sm-9">
                                        @if($department->email)
                                            <a href="mailto:{{ $department->email }}">{{ $department->email }}</a>
                                        @else
                                            No especificado
                                        @endif
                                    </dd>
                                    
                                    <dt class="col-sm-3">Teléfono:</dt>
                                    <dd class="col-sm-9">
                                        @if($department->phone)
                                            <a href="tel:{{ $department->phone }}">{{ $department->phone }}</a>
                                        @else
                                            No especificado
                                        @endif
                                    </dd>
                                    
                                    <dt class="col-sm-3">Ubicación:</dt>
                                    <dd class="col-sm-9">{{ $department->location ?? 'No especificada' }}</dd>
                                    
                                    <dt class="col-sm-3">Presupuesto:</dt>
                                    <dd class="col-sm-9">
                                        @if($department->budget)
                                            ${{ number_format($department->budget, 2) }}
                                        @else
                                            No especificado
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Creado</span>
                                        <span class="info-box-number">{{ $department->created_at->format('d/m/Y') }}</span>
                                        <small>{{ $department->created_at->format('H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $department->employees_count ?? 0 }}</h3>
                        <p>Empleados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="{{ route('admin.users.index', ['department' => $department->id]) }}" class="small-box-footer">
                        Ver empleados <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $department->tramites_count ?? 0 }}</h3>
                        <p>Trámites</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <a href="{{ route('admin.tramites.index', ['department' => $department->id]) }}" class="small-box-footer">
                        Ver trámites <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $department->active_solicitudes_count ?? 0 }}</h3>
                        <p>Solicitudes Activas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <a href="{{ route('admin.solicitudes.index', ['department' => $department->id, 'status' => 'en_proceso']) }}" class="small-box-footer">
                        Ver solicitudes <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $department->completed_solicitudes_count ?? 0 }}</h3>
                        <p>Completadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <a href="{{ route('admin.solicitudes.index', ['department' => $department->id, 'status' => 'completado']) }}" class="small-box-footer">
                        Ver completadas <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Employee List -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-users mr-1"></i>
                            Empleados del Departamento
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.users.create', ['department' => $department->id]) }}" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Agregar Empleado
                            </a>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 400px;">
                        <table class="table table-head-fixed text-nowrap">
                            <thead>
                                <tr>
                                    <th>Empleado</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($department->employees ?? [] as $employee)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $employee->avatar_url ?? asset('images/default-avatar.png') }}" 
                                                 class="img-circle img-size-32 mr-2" alt="Avatar">
                                            <div>
                                                <strong>{{ $employee->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $employee->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @foreach($employee->roles as $role)
                                            <span class="badge badge-info">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $employee->is_active ? 'success' : 'secondary' }}">
                                            {{ $employee->is_active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.users.show', $employee) }}" 
                                               class="btn btn-sm btn-outline-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $employee) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No hay empleados asignados</h5>
                                            <p class="text-muted">Agregue empleados a este departamento</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tramites List -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-alt mr-1"></i>
                            Trámites del Departamento
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.tramites.create', ['department' => $department->id]) }}" 
                               class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Nuevo Trámite
                            </a>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 400px;">
                        <table class="table table-head-fixed text-nowrap">
                            <thead>
                                <tr>
                                    <th>Trámite</th>
                                    <th>Costo</th>
                                    <th>Tiempo</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($department->tramites ?? [] as $tramite)
                                <tr>
                                    <td>
                                        <strong>{{ $tramite->nombre }}</strong>
                                        @if($tramite->descripcion)
                                            <br>
                                            <small class="text-muted">{{ Str::limit($tramite->descripcion, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tramite->costo)
                                            ${{ number_format($tramite->costo, 2) }}
                                        @else
                                            <span class="text-muted">Gratis</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tramite->tiempo_estimado)
                                            {{ $tramite->tiempo_estimado }} días
                                        @else
                                            <span class="text-muted">No esp.</span>
                                        @endif
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
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No hay trámites configurados</h5>
                                            <p class="text-muted">Configure los trámites para este departamento</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history mr-1"></i>
                            Actividad Reciente
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @forelse($department->recent_activity ?? [] as $activity)
                                <div class="time-label">
                                    <span class="bg-primary">
                                        {{ $activity->created_at->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div>
                                    <i class="fas fa-{{ $activity->icon ?? 'circle' }} bg-{{ $activity->color ?? 'primary' }}"></i>
                                    <div class="timeline-item">
                                        <span class="time">
                                            <i class="fas fa-clock"></i> {{ $activity->created_at->format('H:i') }}
                                        </span>
                                        <h3 class="timeline-header">
                                            {{ $activity->title ?? 'Actividad' }}
                                        </h3>
                                        @if($activity->description)
                                            <div class="timeline-body">
                                                {{ $activity->description }}
                                            </div>
                                        @endif
                                        @if($activity->user)
                                            <div class="timeline-footer">
                                                <small class="text-muted">Por: {{ $activity->user->name }}</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted">
                                    <i class="fas fa-history fa-3x mb-3"></i>
                                    <h5 class="text-muted">No hay actividad reciente</h5>
                                    <p class="text-muted">La actividad del departamento aparecerá aquí</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@push('scripts')
<script>
function generateReport() {
    window.open(`/admin/departments/{{ $department->id }}/report`, '_blank');
}

// Chart for department statistics
$(document).ready(function() {
    // You could add charts here for department statistics
    // Example: Monthly solicitudes chart, employee performance, etc.
});
</script>
@endpush