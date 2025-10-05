@extends('layouts.admin')

@section('title', 'Reportes - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Generación de Reportes</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Reportes</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Report Categories -->
        <div class="row">
            <!-- System Reports -->
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Reportes del Sistema
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action" onclick="generateReport('system_overview')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><i class="fas fa-tachometer-alt mr-2"></i>Resumen General</h6>
                                    <small>PDF/Excel</small>
                                </div>
                                <p class="mb-1">Estadísticas generales del sistema y rendimiento</p>
                            </a>
                            
                            <a href="#" class="list-group-item list-group-item-action" onclick="generateReport('user_activity')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><i class="fas fa-users mr-2"></i>Actividad de Usuarios</h6>
                                    <small>PDF/Excel</small>
                                </div>
                                <p class="mb-1">Registro de actividades y accesos de usuarios</p>
                            </a>
                            
                            <a href="#" class="list-group-item list-group-item-action" onclick="generateReport('system_health')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><i class="fas fa-heartbeat mr-2"></i>Salud del Sistema</h6>
                                    <small>PDF</small>
                                </div>
                                <p class="mb-1">Métricas de rendimiento y disponibilidad</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tramite Reports -->
            <div class="col-md-4">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-alt mr-1"></i>
                            Reportes de Trámites
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action" onclick="generateReport('tramites_summary')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><i class="fas fa-list mr-2"></i>Resumen de Trámites</h6>
                                    <small>PDF/Excel</small>
                                </div>
                                <p class="mb-1">Estadísticas por tipo de trámite y estado</p>
                            </a>
                            
                            <a href="#" class="list-group-item list-group-item-action" onclick="generateReport('processing_times')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><i class="fas fa-clock mr-2"></i>Tiempos de Procesamiento</h6>
                                    <small>PDF/Excel</small>
                                </div>
                                <p class="mb-1">Análisis de tiempos de resolución por trámite</p>
                            </a>
                            
                            <a href="#" class="list-group-item list-group-item-action" onclick="generateReport('pending_requests')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><i class="fas fa-hourglass-half mr-2"></i>Solicitudes Pendientes</h6>
                                    <small>PDF/Excel</small>
                                </div>
                                <p class="mb-1">Listado de solicitudes en proceso o pendientes</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Department Reports -->
            <div class="col-md-4">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-building mr-1"></i>
                            Reportes por Departamento
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action" onclick="generateReport('department_performance')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><i class="fas fa-chart-line mr-2"></i>Rendimiento</h6>
                                    <small>PDF/Excel</small>
                                </div>
                                <p class="mb-1">Métricas de rendimiento por departamento</p>
                            </a>
                            
                            <a href="#" class="list-group-item list-group-item-action" onclick="generateReport('workload_distribution')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><i class="fas fa-balance-scale mr-2"></i>Distribución de Carga</h6>
                                    <small>PDF/Excel</small>
                                </div>
                                <p class="mb-1">Análisis de distribución de trabajo</p>
                            </a>
                            
                            <a href="#" class="list-group-item list-group-item-action" onclick="generateReport('employee_productivity')">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><i class="fas fa-user-tie mr-2"></i>Productividad</h6>
                                    <small>PDF/Excel</small>
                                </div>
                                <p class="mb-1">Métricas de productividad del personal</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Report Builder -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tools mr-1"></i>
                            Constructor de Reportes Personalizados
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="customReportForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="report_type">Tipo de Reporte</label>
                                        <select class="form-control" id="report_type" name="report_type">
                                            <option value="">Seleccionar tipo...</option>
                                            <option value="solicitudes">Solicitudes</option>
                                            <option value="tramites">Trámites</option>
                                            <option value="usuarios">Usuarios</option>
                                            <option value="departamentos">Departamentos</option>
                                            <option value="analytics">Analytics</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_from">Fecha Desde</label>
                                        <input type="date" class="form-control" id="date_from" name="date_from">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_to">Fecha Hasta</label>
                                        <input type="date" class="form-control" id="date_to" name="date_to">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="format">Formato</label>
                                        <select class="form-control" id="format" name="format">
                                            <option value="pdf">PDF</option>
                                            <option value="excel">Excel</option>
                                            <option value="csv">CSV</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="filters">Filtros Adicionales</label>
                                        <div id="additional_filters">
                                            <!-- Dynamic filters will be loaded here -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="columns">Columnas a Incluir</label>
                                        <div id="column_selector">
                                            <!-- Dynamic column selection will be loaded here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-warning" onclick="generateCustomReport()">
                                        <i class="fas fa-download mr-1"></i>Generar Reporte Personalizado
                                    </button>
                                    <button type="button" class="btn btn-secondary ml-2" onclick="previewReport()">
                                        <i class="fas fa-eye mr-1"></i>Vista Previa
                                    </button>
                                    <button type="button" class="btn btn-info ml-2" onclick="saveReportTemplate()">
                                        <i class="fas fa-save mr-1"></i>Guardar Plantilla
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scheduled Reports -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            Reportes Programados
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" onclick="scheduleReport()">
                                <i class="fas fa-plus"></i> Programar Reporte
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Reporte</th>
                                    <th>Frecuencia</th>
                                    <th>Próxima Ejecución</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Resumen Semanal</td>
                                    <td><span class="badge badge-info">Semanal</span></td>
                                    <td>Lunes 07/10/2025</td>
                                    <td><span class="badge badge-success">Activo</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editSchedule(1)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteSchedule(1)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Informe Mensual</td>
                                    <td><span class="badge badge-warning">Mensual</span></td>
                                    <td>01/11/2025</td>
                                    <td><span class="badge badge-success">Activo</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editSchedule(2)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteSchedule(2)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Report History -->
            <div class="col-md-6">
                <div class="card card-dark">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history mr-1"></i>
                            Historial de Reportes
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" onclick="clearHistory()">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <table class="table table-head-fixed text-nowrap">
                            <thead>
                                <tr>
                                    <th>Reporte</th>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Resumen de Trámites</td>
                                    <td>04/10/2025 10:30</td>
                                    <td>Admin</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success" onclick="downloadReport('report_1')">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Actividad de Usuarios</td>
                                    <td>03/10/2025 15:45</td>
                                    <td>Admin</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success" onclick="downloadReport('report_2')">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Rendimiento Departamental</td>
                                    <td>02/10/2025 09:15</td>
                                    <td>Admin</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success" onclick="downloadReport('report_3')">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mt-4">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>24</h3>
                        <p>Reportes Generados (Mes)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>5</h3>
                        <p>Reportes Programados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>3</h3>
                        <p>Plantillas Guardadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-save"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>856 MB</h3>
                        <p>Almacenamiento Utilizado</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-hdd"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Report Generation Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalTitle">Generando Reporte</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Generando...</span>
                    </div>
                    <p class="mt-3">Por favor espere mientras se genera el reporte...</p>
                    <div class="progress mt-3">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: 0%" id="reportProgress"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Report Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Programar Reporte</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="scheduleForm">
                    <div class="form-group">
                        <label for="schedule_report_type">Tipo de Reporte</label>
                        <select class="form-control" id="schedule_report_type" required>
                            <option value="">Seleccionar...</option>
                            <option value="system_overview">Resumen del Sistema</option>
                            <option value="tramites_summary">Resumen de Trámites</option>
                            <option value="department_performance">Rendimiento Departamental</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="schedule_frequency">Frecuencia</label>
                        <select class="form-control" id="schedule_frequency" required>
                            <option value="daily">Diario</option>
                            <option value="weekly">Semanal</option>
                            <option value="monthly">Mensual</option>
                            <option value="quarterly">Trimestral</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="schedule_time">Hora de Ejecución</label>
                        <input type="time" class="form-control" id="schedule_time" required>
                    </div>
                    <div class="form-group">
                        <label for="schedule_emails">Enviar por Email a:</label>
                        <input type="email" class="form-control" id="schedule_emails" 
                               placeholder="email1@ejemplo.com, email2@ejemplo.com">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="confirmSchedule()">Programar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function generateReport(type) {
    $('#reportModal').modal('show');
    
    // Simulate report generation progress
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 30;
        if (progress >= 100) {
            progress = 100;
            clearInterval(interval);
            
            setTimeout(() => {
                $('#reportModal').modal('hide');
                // Trigger download
                window.open(`/admin/reports/download/${type}`, '_blank');
            }, 1000);
        }
        $('#reportProgress').css('width', progress + '%');
    }, 500);
}

function generateCustomReport() {
    const formData = new FormData($('#customReportForm')[0]);
    
    if (!formData.get('report_type')) {
        alert('Por favor seleccione un tipo de reporte');
        return;
    }
    
    $('#reportModal').modal('show');
    
    // Here you would make an AJAX call to generate the custom report
    setTimeout(() => {
        $('#reportModal').modal('hide');
        alert('Reporte personalizado generado exitosamente');
    }, 3000);
}

function previewReport() {
    const reportType = $('#report_type').val();
    if (!reportType) {
        alert('Por favor seleccione un tipo de reporte');
        return;
    }
    
    window.open(`/admin/reports/preview/${reportType}`, '_blank');
}

function saveReportTemplate() {
    const formData = new FormData($('#customReportForm')[0]);
    
    if (!formData.get('report_type')) {
        alert('Por favor seleccione un tipo de reporte');
        return;
    }
    
    const templateName = prompt('Nombre para la plantilla:');
    if (templateName) {
        // Save template logic here
        alert(`Plantilla "${templateName}" guardada exitosamente`);
    }
}

function scheduleReport() {
    $('#scheduleModal').modal('show');
}

function confirmSchedule() {
    const reportType = $('#schedule_report_type').val();
    const frequency = $('#schedule_frequency').val();
    const time = $('#schedule_time').val();
    
    if (!reportType || !frequency || !time) {
        alert('Por favor complete todos los campos requeridos');
        return;
    }
    
    // Schedule report logic here
    $('#scheduleModal').modal('hide');
    alert('Reporte programado exitosamente');
    location.reload();
}

function editSchedule(id) {
    // Edit schedule logic
    alert(`Editando programación ${id}`);
}

function deleteSchedule(id) {
    if (confirm('¿Está seguro de eliminar esta programación?')) {
        // Delete schedule logic
        alert(`Programación ${id} eliminada`);
        location.reload();
    }
}

function downloadReport(reportId) {
    window.open(`/admin/reports/download/history/${reportId}`, '_blank');
}

function clearHistory() {
    if (confirm('¿Está seguro de limpiar el historial de reportes?')) {
        // Clear history logic
        alert('Historial limpiado');
        location.reload();
    }
}

// Dynamic filter loading based on report type
$('#report_type').change(function() {
    const reportType = $(this).val();
    const filtersContainer = $('#additional_filters');
    const columnsContainer = $('#column_selector');
    
    // Clear previous content
    filtersContainer.empty();
    columnsContainer.empty();
    
    if (reportType) {
        // Load dynamic filters based on report type
        loadDynamicFilters(reportType, filtersContainer);
        loadColumnSelector(reportType, columnsContainer);
    }
});

function loadDynamicFilters(reportType, container) {
    let filters = '';
    
    switch(reportType) {
        case 'solicitudes':
            filters = `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="estado" id="filter_estado">
                    <label class="form-check-label" for="filter_estado">Filtrar por Estado</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="departamento" id="filter_departamento">
                    <label class="form-check-label" for="filter_departamento">Filtrar por Departamento</label>
                </div>
            `;
            break;
        case 'usuarios':
            filters = `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="rol" id="filter_rol">
                    <label class="form-check-label" for="filter_rol">Filtrar por Rol</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="activo" id="filter_activo">
                    <label class="form-check-label" for="filter_activo">Solo Usuarios Activos</label>
                </div>
            `;
            break;
    }
    
    container.html(filters);
}

function loadColumnSelector(reportType, container) {
    let columns = '';
    
    switch(reportType) {
        case 'solicitudes':
            columns = `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="codigo" id="col_codigo" checked>
                    <label class="form-check-label" for="col_codigo">Código</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="usuario" id="col_usuario" checked>
                    <label class="form-check-label" for="col_usuario">Usuario</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="tramite" id="col_tramite" checked>
                    <label class="form-check-label" for="col_tramite">Trámite</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="estado" id="col_estado" checked>
                    <label class="form-check-label" for="col_estado">Estado</label>
                </div>
            `;
            break;
    }
    
    container.html(columns);
}

// Set default dates
$(document).ready(function() {
    const today = new Date();
    const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());
    
    $('#date_from').val(lastMonth.toISOString().split('T')[0]);
    $('#date_to').val(today.toISOString().split('T')[0]);
});
</script>
@endpush