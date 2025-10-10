@extends('layouts.auth')

@section('title', 'Panel de Administración - MuniApp')

@section('content')
<h2 class="auth-title">Panel de Administración</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.login') }}">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label">Email de Administrador</label>
        <input id="email" 
               type="email" 
               class="form-control @error('email') is-invalid @enderror" 
               name="email" 
               value="{{ old('email') }}" 
               required 
               autocomplete="email" 
               autofocus>
        @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input id="password" 
               type="password" 
               class="form-control @error('password') is-invalid @enderror" 
               name="password" 
               required 
               autocomplete="current-password">
        @error('password')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="fas fa-shield-alt me-2"></i> Acceder al Panel Admin
    </button>

    <a href="{{ route('login') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i> Login de Usuario
    </a>
</form>

@push('styles')
<style>
    .auth-title {
        color: #dc3545;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
    }
</style>
@endpush
                                </a>
                                <a href="{{ route('admin.tramites.index') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-file-alt"></i> Gestión de Trámites
                                </a>
                                <a href="{{ route('admin.solicitudes.index') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-clipboard-list"></i> Solicitudes
                                </a>
                                <a href="{{ route('admin.chat.index') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-comments"></i> Moderación de Chat
                                </a>
                            </div>
                            
                            <hr>
                            
                            <h5>Credenciales de Prueba:</h5>
                            <div class="alert alert-info">
                                <strong>Admin:</strong><br>
                                Email: admin@muniapp.com<br>
                                Contraseña: admin123
                            </div>
                            <div class="alert alert-warning">
                                <strong>Comisión:</strong><br>
                                Email: comision@muniapp.com<br>
                                Contraseña: comision123
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection