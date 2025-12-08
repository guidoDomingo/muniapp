@extends('layouts.citizen')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-users-cog me-2"></i>
                        Panel Comisión Municipal
                    </h1>
                    <p class="page-subtitle">Panel de gestión para miembros de la comisión</p>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">Panel de Comisión</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
<div class="container-fluid">
    <!-- Cards de estadísticas principales -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="modern-card text-center bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $pendingSolicitudes ?? 0 }}</h3>
                            <p class="mb-0">Solicitudes Pendientes</p>
                        </div>
                        <div class="card-icon">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-light btn-sm mt-3">
                        Ver todas <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="modern-card text-center bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $approvedToday ?? 0 }}</h3>
                            <p class="mb-0">Aprobadas Hoy</p>
                        </div>
                        <div class="card-icon">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-light btn-sm mt-3">
                        Ver detalles <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="modern-card text-center bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $inReviewSolicitudes ?? 0 }}</h3>
                            <p class="mb-0">En Revisión</p>
                        </div>
                        <div class="card-icon">
                            <i class="fas fa-eye fa-2x opacity-75"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-light btn-sm mt-3">
                        Revisar <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="modern-card text-center bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $totalTramites ?? 0 }}</h3>
                            <p class="mb-0">Tipos de Trámites</p>
                        </div>
                        <div class="card-icon">
                            <i class="fas fa-file-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.tramites.index') }}" class="btn btn-light btn-sm mt-3">
                        Gestionar <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila principal con contenido -->
    <div class="row g-4">
        <!-- Solicitudes recientes -->
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        Solicitudes Recientes por Revisar
                    </h3>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Ciudadano</th>
                                    <th>Trámite</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSolicitudes ?? [] as $solicitud)
                                <tr>
                                    <td><strong>#{{ $solicitud->id }}</strong></td>
                                    <td>{{ $solicitud->user->name ?? 'N/A' }}</td>
                                    <td>{{ $solicitud->tramite->nombre ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($solicitud->estado == 'pendiente') bg-warning
                                            @elseif($solicitud->estado == 'en_revision') bg-info
                                            @elseif($solicitud->estado == 'aprobado') bg-success
                                            @else bg-danger
                                            @endif">
                                            {{ ucfirst($solicitud->estado) }}
                                        </span>
                                    </td>
                                    <td>{{ $solicitud->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-info" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($solicitud->estado == 'pendiente' || $solicitud->estado == 'en_revision')
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    title="Aprobar"
                                                    onclick="aprobarSolicitud({{ $solicitud->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    title="Rechazar"
                                                    onclick="rechazarSolicitud({{ $solicitud->id }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        No hay solicitudes pendientes por revisar
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel lateral con acciones rápidas -->
        <div class="col-lg-4">
            <!-- Acciones rápidas -->
            <div class="modern-card mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Acciones Rápidas
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-primary">
                            <i class="fas fa-list me-2"></i>
                            Ver Todas las Solicitudes
                        </a>
                        <a href="{{ route('admin.tramites.index') }}" class="btn btn-info">
                            <i class="fas fa-file-alt me-2"></i>
                            Gestionar Trámites
                        </a>
                        <a href="{{ route('admin.chat.index') }}" class="btn btn-success">
                            <i class="fas fa-comments me-2"></i>
                            Chat de Soporte
                        </a>
                        <a href="{{ route('admin.reports') }}" class="btn btn-warning">
                            <i class="fas fa-chart-bar me-2"></i>
                            Generar Reportes
                        </a>
                    </div>
                </div>
            </div>

            <!-- Estado del sistema -->
            <div class="modern-card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Estado del Sistema
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-calendar-alt me-2 text-primary"></i>Solicitudes del Mes:</span>
                                <span class="badge bg-primary">{{ $monthSolicitudes ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-clock me-2 text-info"></i>Promedio de Revisión:</span>
                                <span class="badge bg-info">{{ $avgReviewTime ?? '0' }} días</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-check-circle me-2 text-success"></i>Tasa de Aprobación:</span>
                                <span class="badge bg-success">{{ $approvalRate ?? '0' }}%</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-users me-2 text-warning"></i>Usuarios Activos:</span>
                                <span class="badge bg-warning">{{ $activeUsers ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de tendencias -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="modern-card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Tendencias de Solicitudes (Últimos 30 días)
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="solicitudesChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .card-icon {
        opacity: 0.7;
    }
    
    .modern-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }
    
    .btn-group .btn {
        margin-right: 2px;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }
    
    .badge {
        font-size: 0.75em;
    }
    
    .card-header {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-bottom: 1px solid #dee2e6;
    }
</style>

<script>
    // CSRF Token
    window.Laravel = {
        csrfToken: '{{ csrf_token() }}'
    };
    
    // Gráfico de tendencias
    const ctx = document.getElementById('solicitudesChart').getContext('2d');
    const solicitudesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels ?? []) !!},
            datasets: [{
                label: 'Solicitudes Recibidas',
                data: {!! json_encode($chartData ?? []) !!},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1,
                fill: true
            }, {
                label: 'Solicitudes Aprobadas',
                data: {!! json_encode($chartApproved ?? []) !!},
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            }
        }
    });

    // Funciones para aprobar/rechazar solicitudes
    function aprobarSolicitud(id) {
        if (confirm('¿Está seguro de que desea aprobar esta solicitud?')) {
            fetch(`/commission/solicitudes/${id}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.Laravel.csrfToken
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Solicitud aprobada exitosamente');
                    location.reload();
                } else {
                    alert('Error al aprobar la solicitud');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            });
        }
    }

    function rechazarSolicitud(id) {
        const motivo = prompt('Ingrese el motivo del rechazo:');
        if (motivo && motivo.trim()) {
            fetch(`/commission/solicitudes/${id}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.Laravel.csrfToken
                },
                body: JSON.stringify({ motivo: motivo })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Solicitud rechazada exitosamente');
                    location.reload();
                } else {
                    alert('Error al rechazar la solicitud');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            });
        }
    }

    // Auto-refresh cada 5 minutos
    setInterval(function() {
        console.log('Auto-refresh activado');
        // Aquí podrías actualizar solo partes específicas del dashboard
    }, 300000);
</script>
@endsection