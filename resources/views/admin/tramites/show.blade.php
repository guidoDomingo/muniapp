@extends('layouts.admin')

@section('title', 'Detalles del Trámite - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Detalles del Trámite</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tramites.index') }}">Trámites</a></li>
                    <li class="breadcrumb-item active">{{ $tramite->nombre }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Header Card -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-file-alt mr-2"></i>
                                {{ $tramite->nombre }}
                            </h3>
                            <div>
                                <span class="badge badge-{{ $tramite->is_active ? 'success' : 'danger' }} mr-2">
                                    {{ $tramite->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                                <a href="{{ route('admin.tramites.edit', $tramite) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="{{ route('admin.tramites.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5>Descripción</h5>
                                <p class="text-muted">{{ $tramite->descripcion ?: 'Sin descripción' }}</p>
                                
                                @if($tramite->form_fields)
                                <h6 class="mt-4">Campos del Formulario</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Campo</th>
                                                <th>Tipo</th>
                                                <th>Requerido</th>
                                                <th>Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tramite->form_fields as $field)
                                            <tr>
                                                <td>{{ $field['label'] ?? 'Sin etiqueta' }}</td>
                                                <td>
                                                    <span class="badge badge-info">{{ $field['type'] ?? 'text' }}</span>
                                                </td>
                                                <td>
                                                    @if($field['required'] ?? false)
                                                        <i class="fas fa-check text-success"></i> Sí
                                                    @else
                                                        <i class="fas fa-times text-danger"></i> No
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(isset($field['options']) && is_array($field['options']))
                                                        {{ implode(', ', $field['options']) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Departamento</span>
                                        <span class="info-box-number">{{ $tramite->department->name ?? 'Sin asignar' }}</span>
                                    </div>
                                </div>
                                
                                <div class="info-box">
                                    <span class="info-box-icon bg-success">
                                        <i class="fas fa-clock"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Tiempo Estimado</span>
                                        <span class="info-box-number">{{ $tramite->estimated_days ?? 'N/A' }} días</span>
                                    </div>
                                </div>
                                
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning">
                                        <i class="fas fa-dollar-sign"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Costo</span>
                                        <span class="info-box-number">${{ number_format($tramite->cost ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Documents Required -->
        @if($tramite->required_documents && count($tramite->required_documents) > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-paperclip mr-2"></i>
                            Documentos Requeridos
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($tramite->required_documents as $document)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-file mr-1"></i>
                                            {{ $document }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Workflow Steps -->
        @if($tramite->workflow_steps && count($tramite->workflow_steps) > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-project-diagram mr-2"></i>
                            Flujo de Trabajo
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($tramite->workflow_steps as $index => $step)
                            <div class="time-label">
                                <span class="bg-blue">Paso {{ $index + 1 }}</span>
                            </div>
                            <div>
                                <i class="fas fa-circle bg-blue"></i>
                                <div class="timeline-item">
                                    <h3 class="timeline-header">{{ $step['name'] ?? 'Paso ' . ($index + 1) }}</h3>
                                    <div class="timeline-body">
                                        {{ $step['description'] ?? 'Sin descripción' }}
                                        @if(isset($step['estimated_days']) && $step['estimated_days'])
                                        <br><small class="text-muted">
                                            <i class="fas fa-clock mr-1"></i>
                                            Duración estimada: {{ $step['estimated_days'] }} días
                                        </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Recent Requests -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-2"></i>
                            Solicitudes Recientes
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($tramite->solicitudes && count($tramite->solicitudes) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Solicitante</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Código de Seguimiento</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tramite->solicitudes->take(10) as $solicitud)
                                    <tr>
                                        <td>{{ $solicitud->id }}</td>
                                        <td>
                                            <strong>{{ $solicitud->user->name ?? 'Usuario eliminado' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $solicitud->user->email ?? '' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ 
                                                $solicitud->estado === 'aprobado' ? 'success' : 
                                                ($solicitud->estado === 'rechazado' ? 'danger' : 
                                                ($solicitud->estado === 'en_proceso' ? 'warning' : 'secondary')) 
                                            }}">
                                                {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                                            </span>
                                        </td>
                                        <td>{{ $solicitud->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($solicitud->tracking_code)
                                            <code>{{ $solicitud->tracking_code }}</code>
                                            @else
                                            <span class="text-muted">Sin código</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.solicitudes.show', $solicitud) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($tramite->solicitudes->count() > 10)
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.solicitudes.index') }}?tramite_id={{ $tramite->id }}" 
                               class="btn btn-outline-primary">
                                Ver todas las solicitudes ({{ $tramite->solicitudes->count() }})
                            </a>
                        </div>
                        @endif
                        
                        @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>No hay solicitudes para este trámite aún.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $tramite->solicitudes->count() }}</h3>
                        <p>Total Solicitudes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $tramite->solicitudes->where('estado', 'aprobado')->count() }}</h3>
                        <p>Aprobadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $tramite->solicitudes->where('estado', 'en_proceso')->count() }}</h3>
                        <p>En Proceso</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $tramite->solicitudes->where('estado', 'rechazado')->count() }}</h3>
                        <p>Rechazadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-times"></i>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</section>
@endsection

@push('styles')
<style>
.info-box {
    margin-bottom: 15px;
}

.timeline {
    position: relative;
    margin: 0 0 30px 0;
    padding: 0;
    list-style: none;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 31px;
    width: 4px;
    background: #ddd;
}

.timeline > div {
    margin-bottom: 15px;
    position: relative;
}

.timeline > div > .timeline-item {
    -webkit-box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 3px;
    margin-top: 0;
    background: #fff;
    color: #444;
    margin-left: 60px;
    margin-right: 15px;
    padding: 0;
    position: relative;
}

.timeline > div > .timeline-item > .timeline-header {
    margin: 0;
    color: #555;
    border-bottom: 1px solid #f4f4f4;
    padding: 10px 15px;
    font-size: 16px;
    line-height: 1.1;
}

.timeline > div > .timeline-item > .timeline-body {
    padding: 15px;
    font-size: 14px;
}

.timeline > div > .fas {
    width: 30px;
    height: 30px;
    font-size: 15px;
    line-height: 30px;
    position: absolute;
    color: #666;
    background: #d2d6de;
    border-radius: 50%;
    text-align: center;
    left: 18px;
    top: 0;
}

.timeline > .time-label > span {
    font-weight: 600;
    color: #fff;
    border-radius: 4px;
    display: inline-block;
    padding: 5px 10px;
}
</style>
@endpush