@extends('layouts.citizen')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="page-title">Portal Ciudadano</h1>
        <p class="page-subtitle">Accede a todos los servicios municipales de forma rápida y sencilla</p>
    </div>
</div>

<!-- Cards de Servicios Principales -->
<div class="row g-4 mb-5">
    <div class="col-md-6 col-lg-4">
        <div class="modern-card text-center h-100">
            <div class="card-icon mb-3">
                <i data-feather="file-text" class="text-primary" style="width: 48px; height: 48px;"></i>
            </div>
            <h3 class="h5 mb-3">Trámites Disponibles</h3>
            <p class="text-muted mb-4">Consulta todos los trámites municipales disponibles y sus requisitos</p>
            <a href="{{ route('tramites.index') }}" class="btn btn-primary">Ver Trámites</a>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="modern-card text-center h-100">
            <div class="card-icon mb-3">
                <i data-feather="clock" class="text-warning" style="width: 48px; height: 48px;"></i>
            </div>
            <h3 class="h5 mb-3">Mis Solicitudes</h3>
            <p class="text-muted mb-4">Revisa el estado de tus solicitudes en tiempo real</p>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">En proceso:</span>
                <span class="badge bg-warning">{{ auth()->user()->solicitudes()->whereIn('estado', ['recibido', 'en_revision', 'en_proceso'])->count() }}</span>
            </div>
            <a href="{{ route('solicitudes.index') }}" class="btn btn-warning">Ver Solicitudes</a>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="modern-card text-center h-100">
            <div class="card-icon mb-3">
                <i data-feather="message-circle" class="text-success" style="width: 48px; height: 48px;"></i>
            </div>
            <h3 class="h5 mb-3">Soporte en Vivo</h3>
            <p class="text-muted mb-4">Comunícate directamente con nuestro equipo de atención ciudadana</p>
            <a href="{{ route('chat.index') }}" class="btn btn-success">Iniciar Chat</a>
        </div>
    </div>
</div>

<!-- Información Adicional -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="modern-card">
            <h3 class="h5 mb-3">Información Municipal</h3>
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="d-flex align-items-center">
                        <i data-feather="map-pin" class="text-primary me-3"></i>
                        <div>
                            <h6 class="mb-0">Ubicación</h6>
                            <small class="text-muted">Centro Municipal</small>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="d-flex align-items-center">
                        <i data-feather="phone" class="text-primary me-3"></i>
                        <div>
                            <h6 class="mb-0">Teléfono</h6>
                            <small class="text-muted">(123) 456-7890</small>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="d-flex align-items-center">
                        <i data-feather="clock" class="text-primary me-3"></i>
                        <div>
                            <h6 class="mb-0">Horarios</h6>
                            <small class="text-muted">Lun - Vie: 8:00 - 16:00</small>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="d-flex align-items-center">
                        <i data-feather="mail" class="text-primary me-3"></i>
                        <div>
                            <h6 class="mb-0">Email</h6>
                            <small class="text-muted">info@municipio.gov</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="modern-card">
            <h3 class="h5 mb-3">Notificaciones Recientes</h3>
            @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notification)
                <div class="d-flex align-items-start mb-3">
                    <div class="me-3">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i data-feather="bell" style="width: 16px; height: 16px;" class="text-white"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="mb-1 small">{{ $notification->data['message'] ?? 'Nueva notificación' }}</p>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0">No hay notificaciones recientes</p>
            @endforelse
        </div>
    </div>
</div>

<style>
.card-icon {
    padding: 1rem 0;
}

.modern-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

.btn {
    border-radius: 0.75rem;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}
</style>
@endsection

@section('scripts')
<script>
    // Initialize icons after page load
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
        
        // Add smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    });
</script>
@endsection