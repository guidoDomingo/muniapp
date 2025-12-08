@extends('layouts.admin')

@section('title', 'Reportes - MuniApp Admin')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<style>
.filter-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    margin-bottom: 20px;
}
.export-buttons {
    margin: 20px 0;
}
.card-stats {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
}
.stat-item {
    text-align: center;
}
.stat-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 5px;
}
.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Reportes del Sistema</h1>
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
        
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="total-solicitudes">{{ \App\Models\Solicitud::count() }}</h3>
                        <p>Total Solicitudes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="total-tramites">{{ \App\Models\Tramite::count() }}</h3>
                        <p>Total Trámites</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="total-usuarios">{{ \App\Models\User::count() }}</h3>
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
                        <h3 id="total-departamentos">{{ \App\Models\Department::count() }}</h3>
                        <p>Total Departamentos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card filter-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>
                    Filtros de Búsqueda
                </h5>
            </div>
            <div class="card-body">
                <form id="reportFilters">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="report_type">Tipo de Reporte</label>
                                <select class="form-control" id="report_type" name="report_type">
                                    <option value="solicitudes">Solicitudes</option>
                                    <option value="tramites">Trámites</option>
                                    <option value="usuarios">Usuarios</option>
                                    <option value="general">Reporte General</option>
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
                                <label for="department_id">Departamento</label>
                                <select class="form-control" id="department_id" name="department_id">
                                    <option value="">Todos los departamentos</option>
                                    @foreach(\App\Models\Department::all() as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tramite_id">Tipo de Trámite</label>
                                <select class="form-control" id="tramite_id" name="tramite_id">
                                    <option value="">Todos los trámites</option>
                                    @foreach(\App\Models\Tramite::all() as $tramite)
                                        <option value="{{ $tramite->id }}">{{ $tramite->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Estado</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">Todos los estados</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="en_revision">En Revisión</option>
                                    <option value="aprobada">Aprobada</option>
                                    <option value="rechazada">Rechazada</option>
                                    <option value="finalizada">Finalizada</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="user_id">Usuario</label>
                                <select class="form-control" id="user_id" name="user_id">
                                    <option value="">Todos los usuarios</option>
                                    @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="d-block">
                                    <button type="button" class="btn btn-primary" onclick="loadReportData()">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                                        <i class="fas fa-times"></i> Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Export Buttons -->
        <div class="export-buttons" id="exportButtons" style="display: none;">
            <button type="button" class="btn btn-danger me-2" onclick="exportToPDF()">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </button>
            <button type="button" class="btn btn-success me-2" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </button>
            <button type="button" class="btn btn-info" onclick="printReport()">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>

        <!-- Results Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-table me-2"></i>
                    Resultados del Reporte
                </h5>
            </div>
            <div class="card-body">
                <div id="loadingSpinner" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p>Generando reporte...</p>
                </div>
                <table id="reportTable" class="table table-bordered table-striped" style="display: none;">
                    <thead>
                        <tr id="tableHeaders">
                            <!-- Headers will be dynamically generated -->
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <!-- Data will be dynamically loaded -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
let reportDataTable;

$(document).ready(function() {
    // Set default dates (last 30 days)
    const today = new Date();
    const lastMonth = new Date();
    lastMonth.setDate(today.getDate() - 30);
    
    $('#date_to').val(today.toISOString().split('T')[0]);
    $('#date_from').val(lastMonth.toISOString().split('T')[0]);
});

function loadReportData() {
    const reportType = $('#report_type').val();
    const filters = {
        report_type: reportType,
        date_from: $('#date_from').val(),
        date_to: $('#date_to').val(),
        department_id: $('#department_id').val(),
        tramite_id: $('#tramite_id').val(),
        status: $('#status').val(),
        user_id: $('#user_id').val()
    };
    
    $('#loadingSpinner').show();
    $('#reportTable').hide();
    $('#exportButtons').hide();
    
    // Destroy existing DataTable if it exists
    if (reportDataTable) {
        reportDataTable.destroy();
    }
    
    $.ajax({
        url: '{{ route("admin.reports.data") }}',
        method: 'GET',
        data: filters,
        success: function(response) {
            $('#loadingSpinner').hide();
            
            if (response.success) {
                buildTable(response.data, response.headers);
                $('#exportButtons').show();
            } else {
                toastr.error('Error al cargar los datos: ' + response.message);
            }
        },
        error: function(xhr) {
            $('#loadingSpinner').hide();
            toastr.error('Error al cargar el reporte');
            console.error(xhr.responseText);
        }
    });
}

function buildTable(data, headers) {
    // Build headers
    let headerHtml = '';
    headers.forEach(header => {
        headerHtml += `<th>${header}</th>`;
    });
    $('#tableHeaders').html(headerHtml);
    
    // Build body
    let bodyHtml = '';
    data.forEach(row => {
        bodyHtml += '<tr>';
        Object.values(row).forEach(cell => {
            bodyHtml += `<td>${cell || '-'}</td>`;
        });
        bodyHtml += '</tr>';
    });
    $('#tableBody').html(bodyHtml);
    
    $('#reportTable').show();
    
    // Initialize DataTable
    reportDataTable = $('#reportTable').DataTable({
        responsive: true,
        pageLength: 25,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copiar',
                className: 'btn btn-secondary btn-sm'
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                filename: 'reporte_' + new Date().toISOString().split('T')[0]
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                filename: 'reporte_' + new Date().toISOString().split('T')[0],
                orientation: 'landscape',
                pageSize: 'A4'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                className: 'btn btn-info btn-sm'
            }
        ]
    });
}

function clearFilters() {
    $('#reportFilters')[0].reset();
    
    // Set default dates again
    const today = new Date();
    const lastMonth = new Date();
    lastMonth.setDate(today.getDate() - 30);
    
    $('#date_to').val(today.toISOString().split('T')[0]);
    $('#date_from').val(lastMonth.toISOString().split('T')[0]);
    
    $('#reportTable').hide();
    $('#exportButtons').hide();
    
    if (reportDataTable) {
        reportDataTable.destroy();
    }
}

function exportToPDF() {
    if (reportDataTable) {
        reportDataTable.button('.buttons-pdf').trigger();
    }
}

function exportToExcel() {
    if (reportDataTable) {
        reportDataTable.button('.buttons-excel').trigger();
    }
}

function printReport() {
    if (reportDataTable) {
        reportDataTable.button('.buttons-print').trigger();
    }
}
</script>
@endpush