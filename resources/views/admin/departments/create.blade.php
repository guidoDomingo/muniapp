@extends('layouts.admin')

@section('title', 'Crear Departamento - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Crear Nuevo Departamento</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.departments.index') }}">Departamentos</a></li>
                    <li class="breadcrumb-item active">Crear</li>
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
                            <i class="fas fa-building mr-1"></i>
                            Información del Departamento
                        </h3>
                    </div>
                    
                    <form action="{{ route('admin.departments.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Nombre del Departamento <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" placeholder="Ej: Oficina de Licencias" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="description">Descripción</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                          rows="4" placeholder="Descripción de las funciones y responsabilidades del departamento...">{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="head_name">Jefe/Responsable</label>
                                        <input type="text" name="head_name" id="head_name" class="form-control @error('head_name') is-invalid @enderror" 
                                               value="{{ old('head_name') }}" placeholder="Nombre del responsable">
                                        @error('head_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email del Departamento</label>
                                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                               value="{{ old('email') }}" placeholder="departamento@municipio.gov">
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
                                               value="{{ old('phone') }}" placeholder="(123) 456-7890">
                                        @error('phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="location">Ubicación</label>
                                        <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" 
                                               value="{{ old('location') }}" placeholder="Edificio, piso, oficina">
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
                                           value="{{ old('budget') }}" placeholder="0.00" step="0.01">
                                    @error('budget')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Departamento Activo</label>
                                </div>
                                <small class="form-text text-muted">Los departamentos inactivos no aparecerán en la selección de trámites</small>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Volver
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Crear Departamento
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help and Tips Sidebar -->
            <div class="col-md-4">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-lightbulb mr-1"></i>
                            Consejos
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="callout callout-info">
                            <h5><i class="fas fa-info"></i> Información:</h5>
                            <p>Los departamentos organizan los diferentes servicios municipales y facilitan la gestión de trámites.</p>
                        </div>

                        <h5>Recomendaciones:</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> Use nombres descriptivos y claros</li>
                            <li><i class="fas fa-check text-success"></i> Incluya información de contacto completa</li>
                            <li><i class="fas fa-check text-success"></i> Asigne un responsable identificable</li>
                            <li><i class="fas fa-check text-success"></i> Mantenga la descripción actualizada</li>
                        </ul>

                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle"></i> Importante:</h6>
                            <p class="mb-0">Una vez creado el departamento, podrá asignar usuarios y configurar los trámites específicos.</p>
                        </div>
                    </div>
                </div>

                <!-- Department Statistics -->
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Estadísticas del Sistema
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header">{{ $totalDepartments ?? 0 }}</h5>
                                    <span class="description-text">Departamentos Activos</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header">{{ $totalEmployees ?? 0 }}</h5>
                                    <span class="description-text">Empleados Total</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header">{{ $averageTramites ?? 0 }}</h5>
                                    <span class="description-text">Trámites por Depto.</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header">{{ $activeTramites ?? 0 }}</h5>
                                    <span class="description-text">Trámites Activos</span>
                                </div>
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
$(document).ready(function() {
    // Format budget input
    $('#budget').on('input', function() {
        let value = $(this).val();
        if (value) {
            // Add thousands separator logic if needed
        }
    });

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

    // Auto-generate email suggestion based on department name
    $('#name').on('blur', function() {
        const name = $(this).val().trim();
        const email = $('#email').val().trim();
        
        if (name && !email) {
            const suggestion = name.toLowerCase()
                .replace(/[^a-z0-9\s]/g, '')
                .replace(/\s+/g, '.')
                .substring(0, 20) + '@municipio.gov';
            $('#email').attr('placeholder', suggestion);
        }
    });
});
</script>
@endpush