@extends('layouts.auth')

@section('title', 'Registro - MuniApp')

@section('content')
<h2 class="auth-title">Crear Cuenta</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label">Nombre Completo</label>
        <input id="name" 
               type="text" 
               class="form-control @error('name') is-invalid @enderror" 
               name="name" 
               value="{{ old('name') }}" 
               required 
               autocomplete="name" 
               autofocus>
        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Correo Electrónico</label>
        <input id="email" 
               type="email" 
               class="form-control @error('email') is-invalid @enderror" 
               name="email" 
               value="{{ old('email') }}" 
               required 
               autocomplete="email">
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
               autocomplete="new-password">
        @error('password')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
        <input id="password_confirmation" 
               type="password" 
               class="form-control" 
               name="password_confirmation" 
               required 
               autocomplete="new-password">
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="fas fa-user-plus me-2"></i> Crear Cuenta
    </button>

    <a href="{{ route('login') }}" class="btn btn-secondary">
        <i class="fas fa-sign-in-alt me-2"></i> Ya tengo cuenta
    </a>
</form>
