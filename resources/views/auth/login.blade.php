@extends('layouts.auth')

@section('title', 'Iniciar Sesión - MuniApp')

@section('content')
<h2 class="auth-title">Iniciar Sesión</h2>

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

    <div class="mb-3">
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

    <div class="mb-3 form-check">
        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label" for="remember">
            Recordarme
        </label>
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
    </button>

    @if (Route::has('registercreate'))
        <a href="{{ route('registercreate') }}" class="btn btn-secondary">
            <i class="fas fa-user-plus me-2"></i> Crear Cuenta
        </a>
    @endif

    @if (Route::has('password.request'))
        <div class="mt-3">
            <a href="{{ route('password.request') }}" class="text-decoration-none" style="color: #667eea;">
                ¿Olvidaste tu contraseña?
            </a>
        </div>
    @endif
</form>
