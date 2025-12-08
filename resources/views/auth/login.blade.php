@extends('layouts.auth')

@section('title', 'Iniciar Sesión - MuniApp')

@push('styles')
<style>
body {
    font-family: 'Roboto', sans-serif !important;
    background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('{{ asset('caacupe.avif') }}') !important;
    background-size: cover !important;
    background-position: center !important;
    background-attachment: fixed !important;
    min-height: 100vh !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 !important;
    padding: 20px !important;
}

.login-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 25px;
    padding: 3.5rem;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    width: 100%;
    max-width: 480px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    position: relative;
    overflow: hidden;
}

.login-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.logo-section {
    text-align: center;
    margin-bottom: 2.5rem;
}

.logo-section i {
    font-size: 3.5rem;
    color: #667eea;
    margin-bottom: 1rem;
}

.logo-section h2 {
    color: #333;
    font-weight: 700;
    font-size: 1.8rem;
    margin: 0;
}

.form-group {
    margin-bottom: 1.8rem;
}

.form-control {
    height: 58px !important;
    border-radius: 15px !important;
    border: 2px solid #e9ecef !important;
    font-size: 1.1rem !important;
    padding: 0 25px !important;
    transition: all 0.3s ease !important;
    background: rgba(255, 255, 255, 0.9) !important;
}

.form-control:focus {
    border-color: #667eea !important;
    box-shadow: 0 0 0 0.3rem rgba(102, 126, 234, 0.15) !important;
    background: white !important;
}

.form-label {
    font-weight: 600 !important;
    font-size: 1.1rem !important;
    color: #333 !important;
    margin-bottom: 12px !important;
}

.btn-login {
    height: 58px;
    border-radius: 15px;
    font-size: 1.2rem;
    font-weight: 600;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    width: 100%;
    margin-top: 25px;
    transition: all 0.3s ease;
    color: white;
}

.btn-login:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
    color: white;
}

.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.form-check-label {
    font-size: 1rem;
    color: #555;
    font-weight: 500;
}

.alert {
    border-radius: 15px !important;
    margin-bottom: 25px !important;
    border: none !important;
    font-weight: 500 !important;
}

.alert-danger {
    background: rgba(220, 53, 69, 0.1) !important;
    color: #dc3545 !important;
}

.alert-success {
    background: rgba(25, 135, 84, 0.1) !important;
    color: #198754 !important;
}

.forgot-password-link {
    color: #667eea;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.forgot-password-link:hover {
    color: #764ba2;
    text-decoration: underline !important;
    transform: translateY(-1px);
}

.remember-section {
    margin: 2rem 0;
}

.forgot-section {
    text-align: center;
    margin-top: 2rem;
}

@media (max-width: 576px) {
    body {
        padding: 10px !important;
    }
    
    .login-card {
        padding: 2.5rem 2rem;
        border-radius: 20px;
    }
    
    .logo-section i {
        font-size: 3rem;
    }
    
    .logo-section h2 {
        font-size: 1.6rem;
    }
    
    .form-control, .btn-login {
        height: 52px;
    }
}
</style>
@endpush

@section('content')
<div class="login-card">
    <div class="logo-section">
        <i class="fas fa-city"></i>
        <h2>MuniApp</h2>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Correo Electrónico</label>
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

        <div class="form-group">
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

        <div class="remember-section">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">
                    Recordarme
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-login">
            <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
        </button>

        @if (Route::has('password.request'))
            <div class="forgot-section">
                <a href="{{ route('password.request') }}" class="text-decoration-none forgot-password-link">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>
        @endif
    </form>
</div>
