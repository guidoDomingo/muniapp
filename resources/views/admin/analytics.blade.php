@extends('layouts.admin')

@section('title', 'Analytics - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Analytics y Estadísticas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Analytics</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Performance Summary Cards -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($averageResolutionTime->avg('avg_days'), 1) ?? 0 }} días</h3>
                        <p>Tiempo Promedio de Resolución</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ number_format($departmentSatisfaction->avg('completion_rate'), 1) ?? 0 }}%</h3>
                        <p>Tasa de Finalización Promedio</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $departmentSatisfaction->sum('total_solicitudes') ?? 0 }}</h3>
                        <p>Total de Solicitudes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $departmentSatisfaction->count() ?? 0 }}</h3>
                        <p>Departamentos Activos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Average Resolution Time Chart -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-stopwatch mr-1"></i>
                            Tiempo Promedio de Resolución por Trámite
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="resolutionTimeChart" height="400"></canvas>
                    </div>
                </div>
            </div>

            <!-- Department Performance Chart -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Rendimiento por Departamento
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="departmentPerformanceChart" height="400"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Tables -->
        <div class="row">
            <!-- Resolution Time Table -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-table mr-1"></i>
                            Detalle de Tiempos de Resolución
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" onclick="exportTable('resolutionTable')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap" id="resolutionTable">
                            <thead>
                                <tr>
                                    <th>Trámite</th>
                                    <th>Promedio (días)</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($averageResolutionTime as $resolution)
                                <tr>
                                    <td>{{ $resolution->nombre }}</td>
                                    <td>
                                        <span class="badge badge-{{ $resolution->avg_days <= 5 ? 'success' : ($resolution->avg_days <= 10 ? 'warning' : 'danger') }}">
                                            {{ number_format($resolution->avg_days, 1) }} días
                                        </span>
                                    </td>
                                    <td>
                                        @if($resolution->avg_days <= 5)
                                            <span class="badge badge-success">Excelente</span>
                                        @elseif($resolution->avg_days <= 10)
                                            <span class="badge badge-warning">Bueno</span>
                                        @elseif($resolution->avg_days <= 15)
                                            <span class="badge badge-info">Regular</span>
                                        @else
                                            <span class="badge badge-danger">Necesita Mejora</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No hay datos de resolución</h5>
                                            <p class="text-muted">Los datos aparecerán cuando se completen trámites</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Department Performance Table -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-building mr-1"></i>
                            Rendimiento por Departamento
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" onclick="exportTable('departmentTable')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap" id="departmentTable">
                            <thead>
                                <tr>
                                    <th>Departamento</th>
                                    <th>Total Solicitudes</th>
                                    <th>Tasa de Finalización</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departmentSatisfaction as $dept)
                                <tr>
                                    <td>{{ $dept['department'] }}</td>
                                    <td>{{ $dept['total_solicitudes'] }}</td>
                                    <td>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-{{ $dept['completion_rate'] >= 80 ? 'success' : ($dept['completion_rate'] >= 60 ? 'warning' : 'danger') }}" 
                                                 style="width: {{ $dept['completion_rate'] }}%"></div>
                                        </div>
                                        <small>{{ number_format($dept['completion_rate'], 1) }}%</small>
                                    </td>
                                    <td>
                                        @if($dept['completion_rate'] >= 80)
                                            <span class="badge badge-success">Excelente</span>
                                        @elseif($dept['completion_rate'] >= 60)
                                            <span class="badge badge-warning">Bueno</span>
                                        @elseif($dept['completion_rate'] >= 40)
                                            <span class="badge badge-info">Regular</span>
                                        @else
                                            <span class="badge badge-danger">Necesita Mejora</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No hay datos de departamentos</h5>
                                            <p class="text-muted">Los datos aparecerán cuando haya actividad en los departamentos</p>
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

        <!-- Additional Analytics Row -->
        <div class="row">
            <!-- Trends Analysis -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Análisis de Tendencias
                        </h3>
                        <div class="card-tools">
                            <div class="btn-group">
                                <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-filter"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#" onclick="updateTrends('7d')">Últimos 7 días</a>
                                    <a class="dropdown-item" href="#" onclick="updateTrends('30d')">Últimos 30 días</a>
                                    <a class="dropdown-item" href="#" onclick="updateTrends('90d')">Últimos 90 días</a>
                                    <a class="dropdown-item" href="#" onclick="updateTrends('1y')">Último año</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="trendsChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Key Insights -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-lightbulb mr-1"></i>
                            Insights Clave
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="callout callout-info">
                            <h5><i class="fas fa-info"></i> Rendimiento General:</h5>
                            @if($averageResolutionTime->count() > 0)
                                <p>
                                    @php
                                        $fastestTramite = $averageResolutionTime->sortBy('avg_days')->first();
                                        $slowestTramite = $averageResolutionTime->sortByDesc('avg_days')->first();
                                    @endphp
                                    • Trámite más rápido: <strong>{{ $fastestTramite->nombre ?? 'N/A' }}</strong> ({{ number_format($fastestTramite->avg_days ?? 0, 1) }} días)<br>
                                    • Trámite más lento: <strong>{{ $slowestTramite->nombre ?? 'N/A' }}</strong> ({{ number_format($slowestTramite->avg_days ?? 0, 1) }} días)
                                </p>
                            @else
                                <p>No hay suficientes datos para generar insights.</p>
                            @endif
                        </div>

                        <div class="callout callout-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> Recomendaciones:</h5>
                            <ul class="mb-0">
                                @if($averageResolutionTime->where('avg_days', '>', 10)->count() > 0)
                                    <li>{{ $averageResolutionTime->where('avg_days', '>', 10)->count() }} trámite(s) requieren optimización</li>
                                @endif
                                @if($departmentSatisfaction->where('completion_rate', '<', 60)->count() > 0)
                                    <li>{{ $departmentSatisfaction->where('completion_rate', '<', 60)->count() }} departamento(s) necesitan apoyo</li>
                                @endif
                                @if($averageResolutionTime->count() == 0)
                                    <li>Implementar seguimiento de tiempos de resolución</li>
                                @endif
                            </ul>
                        </div>

                        <div class="text-center">
                            <button type="button" class="btn btn-primary btn-sm" onclick="generateDetailedReport()">
                                <i class="fas fa-file-pdf"></i> Generar Reporte Detallado
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Prepare data for charts
    const resolutionData = @json($averageResolutionTime);
    const departmentData = @json($departmentSatisfaction);

    // Resolution Time Chart
    if (resolutionData.length > 0) {
        const ctx1 = document.getElementById('resolutionTimeChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: resolutionData.map(item => item.nombre),
                datasets: [{
                    label: 'Días Promedio',
                    data: resolutionData.map(item => parseFloat(item.avg_days)),
                    backgroundColor: resolutionData.map(item => 
                        item.avg_days <= 5 ? '#28a745' : 
                        item.avg_days <= 10 ? '#ffc107' : '#dc3545'
                    ),
                    borderColor: '#dee2e6',
                    borderWidth: 1
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
                        title: {
                            display: true,
                            text: 'Días'
                        }
                    }
                }
            }
        });
    }

    // Department Performance Chart
    if (departmentData.length > 0) {
        const ctx2 = document.getElementById('departmentPerformanceChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: departmentData.map(item => item.department),
                datasets: [{
                    data: departmentData.map(item => item.total_solicitudes),
                    backgroundColor: [
                        '#007bff', '#28a745', '#ffc107', '#dc3545', 
                        '#6f42c1', '#fd7e14', '#20c997', '#6c757d'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
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
    }

    // Trends Chart (placeholder)
    const ctx3 = document.getElementById('trendsChart').getContext('2d');
    new Chart(ctx3, {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            datasets: [{
                label: 'Solicitudes Mensuales',
                data: [12, 19, 3, 5, 2, 3],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
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
            }
        }
    });
});

function exportTable(tableId) {
    // Implementation for table export
    const table = document.getElementById(tableId);
    const rows = Array.from(table.rows);
    
    let csvContent = '';
    rows.forEach(row => {
        const cols = Array.from(row.cells);
        const rowData = cols.map(col => col.textContent.trim()).join(',');
        csvContent += rowData + '\n';
    });
    
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = tableId + '_analytics.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

function updateTrends(period) {
    // AJAX call to update trends data
    console.log('Updating trends for period:', period);
    // Implementation would go here
}

function generateDetailedReport() {
    // Generate comprehensive PDF report
    window.open('/admin/analytics/report', '_blank');
}
</script>
@endpush