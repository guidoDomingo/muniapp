@extends('layouts.admin')

@section('title', 'Editar Departamento - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Editar Departamento: {{ $department->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.departments.index') }}">Departamentos</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-edit mr-1"></i>
                            Editar Información del Departamento
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-{{ $department->is_active ? 'success' : 'secondary' }}">
                                {{ $department->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.departments.update', $department) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Nombre del Departamento <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $department->name) }}" placeholder="Ej: Oficina de Licencias" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="description">Descripción</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                          rows="4" placeholder="Descripción de las funciones y responsabilidades del departamento...">{{ old('description', $department->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="head_name">Jefe/Responsable</label>
                                        <input type="text" name="head_name" id="head_name" class="form-control @error('head_name') is-invalid @enderror" 
                                               value="{{ old('head_name', $department->head_name) }}" placeholder="Nombre del responsable">
                                        @error('head_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email del Departamento</label>
                                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                               value="{{ old('email', $department->email) }}" placeholder="departamento@municipio.gov">
                                        @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Teléfono</label>
                                        <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" 
                                               value="{{ old('phone', $department->phone) }}" placeholder="(123) 456-7890">
                                        @error('phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="location">Ubicación</label>
                                        <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" 
                                               value="{{ old('location', $department->location) }}" placeholder="Edificio, piso, oficina">
                                        @error('location')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="budget">Presupuesto Anual</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" name="budget" id="budget" class="form-control @error('budget') is-invalid @enderror" 
                                           value="{{ old('budget', $department->budget) }}" placeholder="0.00" step="0.01">
                                    @error('budget')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Departamento Activo</label>
                                </div>
                                <small class="form-text text-muted">Los departamentos inactivos no aparecerán en la selección de trámites</small>
                            </div>

                            <!-- Update Comments -->
                            <div class="form-group">
                                <label for="update_notes">Notas de Actualización</label>
                                <textarea name="update_notes" id="update_notes" class="form-control" 
                                          rows="3" placeholder="Describa los cambios realizados...">{{ old('update_notes') }}</textarea>
                                <small class="form-text text-muted">Estas notas se guardarán en el historial del departamento</small>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Volver
                                    </a>
                                    <a href="{{ route('admin.departments.show', $department) }}" class="btn btn-info">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Actualizar Departamento
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar with Department Info -->
            <div class="col-md-4">
                <!-- Department Stats -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Estadísticas del Departamento
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header">{{ $department->employees_count ?? 0 }}</h5>
                                    <span class="description-text">Empleados</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header">{{ $department->tramites_count ?? 0 }}</h5>
                                    <span class="description-text">Trámites</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header">{{ $department->active_solicitudes_count ?? 0 }}</h5>
                                    <span class="description-text">Solicitudes Activas</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header">{{ $department->monthly_avg ?? 0 }}</h5>
                                    <span class="description-text">Promedio Mensual</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Department Information -->
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-1"></i>
                            Información del Departamento
                        </h3>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-5">Creado:</dt>
                            <dd class="col-sm-7">{{ $department->created_at->format('d/m/Y') }}</dd>
                            
                            <dt class="col-sm-5">Actualizado:</dt>
                            <dd class="col-sm-7">{{ $department->updated_at->format('d/m/Y') }}</dd>
                            
                            <dt class="col-sm-5">Estado:</dt>
                            <dd class="col-sm-7">
                                <span class="badge badge-{{ $department->is_active ? 'success' : 'secondary' }}">
                                    {{ $department->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </dd>
                            
                            @if($department->budget)
                                <dt class="col-sm-5">Presupuesto:</dt>
                                <dd class="col-sm-7">${{ number_format($department->budget, 2) }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt mr-1"></i>
                            Acciones Rápidas
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.users.index', ['department' => $department->id]) }}" 
                               class="btn btn-outline-primary btn-block">
                                <i class="fas fa-users"></i> Ver Empleados
                            </a>
                            <a href="{{ route('admin.tramites.index', ['department' => $department->id]) }}" 
                               class="btn btn-outline-info btn-block">
                                <i class="fas fa-file-alt"></i> Ver Trámites
                            </a>
                            <a href="{{ route('admin.solicitudes.index', ['department' => $department->id]) }}" 
                               class="btn btn-outline-success btn-block">
                                <i class="fas fa-list"></i> Ver Solicitudes
                            </a>
                            <button type="button" class="btn btn-outline-warning btn-block" onclick="generateReport()">
                                <i class="fas fa-file-pdf"></i> Generar Reporte
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                @if(auth()->user()->hasRole('admin'))
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Zona de Peligro
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Las siguientes acciones son irreversibles:</p>
                        <button type="button" class="btn btn-outline-danger btn-block" onclick="archiveDepartment()">
                            <i class="fas fa-archive"></i> Archivar Departamento
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-block" onclick="deleteDepartment()">
                            <i class="fas fa-trash"></i> Eliminar Departamento
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Phone number formatting
    $('#phone').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length >= 6) {
            if (value.length <= 10) {
                value = value.replace(/(\d{3})(\d{3})(\d{0,4})/, '($1) $2-$3');
            }
        } else if (value.length >= 3) {
            value = value.replace(/(\d{3})(\d{0,3})/, '($1) $2');
        }
        $(this).val(value);
    });

    // Form validation
    $('form').on('submit', function(e) {
        let isValid = true;
        
        // Validate required fields
        const name = $('#name').val().trim();
        if (!name) {
            $('#name').addClass('is-invalid');
            isValid = false;
        } else {
            $('#name').removeClass('is-invalid');
        }

        // Validate email format if provided
        const email = $('#email').val().trim();
        if (email && !isValidEmail(email)) {
            $('#email').addClass('is-invalid');
            if (!$('#email').next('.invalid-feedback').length) {
                $('#email').after('<span class="invalid-feedback">Formato de email inválido</span>');
            }
            isValid = false;
        } else {
            $('#email').removeClass('is-invalid');
        }

        if (!isValid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
        }
    });

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
});

function generateReport() {
    window.open(`/admin/departments/{{ $department->id }}/report`, '_blank');
}

function archiveDepartment() {
    if (confirm('¿Está seguro de archivar este departamento? Esta acción marcará el departamento como inactivo.')) {
        $.ajax({
            url: `/admin/departments/{{ $department->id }}/archive`,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error al archivar el departamento');
            }
        });
    }
}

function deleteDepartment() {
    if (confirm('¿Está seguro de eliminar este departamento? Esta acción no se puede deshacer.')) {
        if (confirm('Esta acción eliminará permanentemente el departamento y afectará todos los trámites asociados. ¿Desea continuar?')) {
            $.ajax({
                url: `/admin/departments/{{ $department->id }}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        window.location.href = '{{ route("admin.departments.index") }}';
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error al eliminar el departamento');
                }
            });
        }
    }
}
</script>
@endpush