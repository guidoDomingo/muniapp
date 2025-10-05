<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MuniApp Admin')</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
        }
        
        .main-sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .nav-sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 8px;
            margin: 2px 8px;
            transition: all 0.3s ease;
        }
        
        .nav-sidebar .nav-link:hover,
        .nav-sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .content-wrapper {
            background-color: #f1f5f9;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.5rem;
        }
        
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            transition: all 0.2s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .small-box {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
        }
        
        .small-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .small-box .inner h3 {
            font-weight: 700;
            font-size: 2.5rem;
        }
        
        .small-box .icon i {
            font-size: 70px;
        }
        
        .navbar-nav .nav-link {
            color: #64748b;
            font-weight: 500;
        }
        
        .sidebar-mini.sidebar-collapse .main-sidebar:hover {
            width: 250px;
        }
        
        .content-header h1 {
            font-weight: 600;
            color: #1e293b;
        }
        
        .breadcrumb-item a {
            color: #64748b;
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: #2563eb;
        }
        
        .table {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table th {
            background-color: #f8fafc;
            border: none;
            font-weight: 600;
            color: #374151;
        }
        
        .badge {
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-weight: 500;
        }
        
        .progress {
            height: 8px;
            border-radius: 4px;
        }
        
        .modal-content {
            border-radius: 12px;
            border: none;
        }
        
        .modal-header {
            border-bottom: 1px solid #e2e8f0;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #d1d5db;
            transition: all 0.2s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.5s ease;
        }
    </style>
    
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Notifications Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell"></i>
                        <span class="badge badge-warning navbar-badge">3</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-item dropdown-header">3 Notificaciones</span>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-file mr-2"></i> Nueva solicitud
                            <span class="float-right text-muted text-sm">hace 2 min</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item dropdown-footer">Ver todas las notificaciones</a>
                    </div>
                </li>
                
                <!-- User Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <img src="{{ auth()->user()->avatar_url }}" alt="User Image" class="img-circle" style="width: 30px; height: 30px;">
                        <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <div class="dropdown-header">
                            <strong>{{ auth()->user()->name }}</strong>
                            <br>
                            <small class="text-muted">{{ auth()->user()->email }}</small>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-user mr-2"></i> Perfil
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-cogs mr-2"></i> Configuración
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>
        
        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('admin.dashboard') }}" class="brand-link">
                <img src="{{ asset('images/logo.png') }}" alt="MuniApp Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">MuniApp Admin</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        
                        <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Usuarios
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.users.index') }}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Lista de Usuarios</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.users.create') }}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Crear Usuario</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="nav-item {{ request()->routeIs('admin.tramites.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>
                                    Trámites
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.tramites.index') }}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Lista de Trámites</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.tramites.create') }}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Crear Trámite</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('admin.solicitudes.index') }}" class="nav-link {{ request()->routeIs('admin.solicitudes.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Solicitudes</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('admin.chat.index') }}" class="nav-link {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-comments"></i>
                                <p>Chat & Mensajería</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('admin.departments.index') }}" class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-building"></i>
                                <p>Departamentos</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('admin.analytics') }}" class="nav-link {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Analíticas</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('admin.reports') }}" class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file-export"></i>
                                <p>Reportes</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>Configuración</p>
                            </a>
                        </li>
                        
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; {{ date('Y') }} <a href="#">MuniApp</a>.</strong>
            Todos los derechos reservados.
            <div class="float-right d-none d-sm-inline-block">
                <b>Versión</b> 2.0.0
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    
    <!-- Real-time notifications -->
    <script>
        // Echo setup for real-time notifications
        @if(auth()->check())
        Echo.private('App.Models.User.{{ auth()->id() }}')
            .notification((notification) => {
                // Handle real-time notifications
                console.log('Nueva notificación:', notification);
                showNotification(notification);
            });
        @endif
        
        function showNotification(notification) {
            // Create toast notification
            const toast = $(`
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <i class="fas fa-bell text-primary"></i>
                        <strong class="mr-auto ml-2">MuniApp</strong>
                        <small>ahora</small>
                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="toast-body">
                        ${notification.message}
                    </div>
                </div>
            `);
            
            $('.toast-container').append(toast);
            toast.toast('show');
        }
    </script>
    
    @stack('scripts')
</body>
</html>