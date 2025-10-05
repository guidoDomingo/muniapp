@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Panel de Administración - MuniApp</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Login Form -->
                        <div class="col-md-6">
                            <h4>Acceso de Administrador</h4>
                            <form method="POST" action="{{ route('admin.login') }}">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="password">Contraseña</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" required>
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-block">
                                    Acceder al Panel Admin
                                </button>
                            </form>
                        </div>
                        
                        <!-- Quick Access -->
                        <div class="col-md-6">
                            <h4>Acceso Rápido</h4>
                            <div class="list-group">
                                <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard Principal
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-users"></i> Gestión de Usuarios
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