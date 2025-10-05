@extends('layouts.admin')

@section('title', 'Dashboard - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info fade-in-up">
                    <div class="inner">
                        <h3>{{ $stats['total_solicitudes'] }}</h3>
                        <p>Total Solicitudes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <a href="{{ route('admin.solicitudes.index') }}" class="small-box-footer">
                        Ver más <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success fade-in-up" style="animation-delay: 0.1s">
                    <div class="inner">
                        <h3>{{ $stats['completed_solicitudes'] }}</h3>
                        <p>Completadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <a href="{{ route('admin.solicitudes.index', ['status' => 'completado']) }}" class="small-box-footer">
                        Ver más <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning fade-in-up" style="animation-delay: 0.2s">
                    <div class="inner">
                        <h3>{{ $stats['pending_solicitudes'] }}</h3>
                        <p>Pendientes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="{{ route('admin.solicitudes.index', ['status' => 'pendiente']) }}" class="small-box-footer">
                        Ver más <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary fade-in-up" style="animation-delay: 0.3s">
                    <div class="inner">
                        <h3>{{ $stats['total_users'] }}</h3>
                        <p>Usuarios Registrados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                        Ver más <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Solicitudes Chart -->
            <div class="col-md-6">
                <div class="card fade-in-up" style="animation-delay: 0.4s">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Solicitudes por Mes
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="solicitudesChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Status Distribution -->
            <div class="col-md-6">
                <div class="card fade-in-up" style="animation-delay: 0.5s">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Estado de Solicitudes
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Solicitudes -->
            <div class="col-md-8">
                <div class="card fade-in-up" style="animation-delay: 0.6s">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-1"></i>
                            Solicitudes Recientes
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-sm btn-primary">
                                Ver todas
                            </a>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Usuario</th>
                                    <th>Trámite</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSolicitudes as $solicitud)
                                <tr>
                                    <td>
                                        <span class="badge badge-secondary">{{ $solicitud->tracking_code ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $solicitud->user->name }}</td>
                                    <td>{{ $solicitud->tramite->nombre }}</td>
                                    <td>
                                        <span class="badge badge-{{ $solicitud->status_color }}">
                                            {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                                        </span>
                                    </td>
                                    <td>{{ $solicitud->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.solicitudes.show', $solicitud) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Popular Tramites -->
            <div class="col-md-4">
                <div class="card fade-in-up" style="animation-delay: 0.7s">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-star mr-1"></i>
                            Trámites Más Solicitados
                        </h3>
                    </div>
                    <div class="card-body">
                        @foreach($popularTramites as $tramite)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <strong>{{ $tramite->nombre }}</strong>
                                <br>
                                <small class="text-muted">{{ $tramite->solicitudes_count }} solicitudes</small>
                            </div>
                            <span class="badge badge-primary badge-pill">{{ $tramite->solicitudes_count }}</span>
                        </div>
                        @if(!$loop->last)
                        <hr>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Users by Role -->
        <div class="row">
            <div class="col-md-6">
                <div class="card fade-in-up" style="animation-delay: 0.8s">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-users-cog mr-1"></i>
                            Usuarios por Rol
                        </h3>
                    </div>
                    <div class="card-body">
                        @foreach($usersByRole as $role)
                        <div class="progress-group">
                            <span class="progress-text">{{ ucfirst($role->role_name) }}</span>
                            <span class="float-right"><b>{{ $role->count }}</b>/{{ $stats['total_users'] }}</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" style="width: {{ ($role->count / $stats['total_users']) * 100 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-md-6">
                <div class="card fade-in-up" style="animation-delay: 0.9s">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt mr-1"></i>
                            Acciones Rápidas
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <a href="{{ route('admin.tramites.create') }}" class="btn btn-outline-primary btn-block">
                                    <i class="fas fa-plus mr-2"></i>
                                    Nuevo Trámite
                                </a>
                            </div>
                            <div class="col-6 mb-3">
                                <a href="{{ route('admin.users.create') }}" class="btn btn-outline-success btn-block">
                                    <i class="fas fa-user-plus mr-2"></i>
                                    Nuevo Usuario
                                </a>
                            </div>
                            <div class="col-6 mb-3">
                                <a href="{{ route('admin.departments.create') }}" class="btn btn-outline-info btn-block">
                                    <i class="fas fa-building mr-2"></i>
                                    Nuevo Departamento
                                </a>
                            </div>
                            <div class="col-6 mb-3">
                                <a href="{{ route('admin.reports') }}" class="btn btn-outline-warning btn-block">
                                    <i class="fas fa-file-export mr-2"></i>
                                    Exportar Reporte
                                </a>
                            </div>
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
document.addEventListener('DOMContentLoaded', function() {
    // Solicitudes Chart
    const solicitudesCtx = document.getElementById('solicitudesChart').getContext('2d');
    new Chart(solicitudesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($solicitudesByMonth->pluck('month')) !!},
            datasets: [{
                label: 'Solicitudes',
                data: {!! json_encode($solicitudesByMonth->pluck('count')) !!},
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    }
                },
                x: {
                    grid: {
                        color: '#f1f5f9'
                    }
                }
            }
        }
    });

    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pendientes', 'En Proceso', 'Completadas', 'Rechazadas'],
            datasets: [{
                data: [
                    {{ $stats['pending_solicitudes'] }},
                    {{ $stats['in_progress_solicitudes'] }},
                    {{ $stats['completed_solicitudes'] }},
                    {{ $stats['rejected_solicitudes'] }}
                ],
                backgroundColor: [
                    '#f59e0b',
                    '#06b6d4',
                    '#10b981',
                    '#ef4444'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush