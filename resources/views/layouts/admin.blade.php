<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MuniApp Admin')</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }
        
        .main-sidebar .brand-link {
            display: flex;
            align-items: center;
            padding: 0.8125rem 1rem;
            transition: width .3s ease-in-out;
            color: rgba(255,255,255,.8);
            white-space: nowrap;
            border-bottom: 1px solid #4b545c;
        }
        
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active {
            background-color: #007bff;
            color: #fff;
        }
        
        .content-wrapper, .right-side, .main-footer {
            transition: -webkit-transform .3s ease-in-out;
            transition: transform .3s ease-in-out;
            transition: transform .3s ease-in-out,-webkit-transform .3s ease-in-out;
            margin-left: 250px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .navbar-nav .nav-link {
            color: rgba(255,255,255,.8);
            border-radius: 0.375rem;
            margin: 0 0.125rem;
        }
        
        .navbar-nav .nav-link:hover {
            color: #fff;
            background-color: rgba(255,255,255,.1);
        }
        
        .content-header h1 {
            font-size: 1.8rem;
            margin: 0;
        }
        
        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            margin-bottom: 1rem;
        }
        
        .card-header .card-title {
            font-weight: 500;
            margin: 0;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        
        .badge {
            font-weight: 500;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: none;
        }
        
        .modal-header .close {
            color: white;
            opacity: 0.8;
        }
        
        .modal-header .close:hover {
            opacity: 1;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .page-link {
            color: #667eea;
        }
        
        .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
        }
    </style>
    
    @stack('styles')
    
    <!-- Fix FontAwesome loading -->
    <script>
        // Ensure FontAwesome loads
        if (!document.querySelector('link[href*="font-awesome"]')) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
            document.head.appendChild(link);
        }
    </script>
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
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">Panel de Control</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ms-auto">
                <!-- Notifications Dropdown Menu -->
                <li class="nav-item dropdown">
                    <!-- <a class="nav-link" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                        <i class="far fa-bell"></i>
                        <span class="badge bg-warning">3</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                        <span class="dropdown-item-text dropdown-header">3 Notificaciones</span>
                        <li><hr class="dropdown-divider"></li>
                        <li><a href="#" class="dropdown-item">
                            <i class="fas fa-file me-2"></i> Nueva solicitud
                            <span class="float-end text-muted small">hace 2 min</span>
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a href="#" class="dropdown-item dropdown-footer">Ver todas las notificaciones</a></li>
                    </ul> -->
                </li>
                
                <!-- User Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                        <!-- <img src="{{ auth()->user()->avatar_url }}" alt="User Image" class="rounded-circle" style="width: 30px; height: 30px;"> -->
                        <i class="fas fa-user me-2"></i>
                        <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                        <li class="dropdown-header">
                            <strong>{{ auth()->user()->name }}</strong>
                            <br>
                            <small class="text-muted">{{ auth()->user()->email }}</small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a href="#" class="dropdown-item">
                            <i class="fas fa-user me-2"></i> Perfil
                        </a></li>
                        <li><a href="#" class="dropdown-item">
                            <i class="fas fa-cogs me-2"></i> Configuración
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="mb-0">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        
        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('admin.dashboard') }}" class="brand-link">
                <img src="{{ url('images/logo.jpg') }}" alt="MuniApp Logo" class="brand-image system-logo elevation-3" style="opacity: .8; max-height: 33px; width: auto;" onerror="this.style.display='none'">
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
                                <p>Panel de Control</p>
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
                            <a href="{{ route('admin.chat.live') }}" class="nav-link {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}">
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
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <!-- Real-time notifications -->
    <script>
        // Global configuration from Laravel
        window.MuniAppConfig = {
            baseUrl: '{{ url('/') }}',
            assetUrl: '{{ asset('') }}',
            imagesPath: '{{ url('images') }}'
        };

        // Initialize toastr
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "3000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Echo setup for real-time notifications (only if Echo is available)
        @if(auth()->check())
        if (typeof Echo !== 'undefined') {
            Echo.private('App.Models.User.{{ auth()->id() }}')
                .notification((notification) => {
                    // Handle real-time notifications
                    console.log('Nueva notificación:', notification);
                    showNotification(notification);
                });
        }
        @endif
        
        function showNotification(notification) {
            // Use toastr for notifications
            if (typeof toastr !== 'undefined') {
                toastr.info(notification.message, 'MuniApp');
            } else {
                console.log('Notification:', notification.message);
            }
        }

        // Global function to update logo throughout the system
        window.updateSystemLogo = function() {
            const logoFormats = ['jpg', 'png', 'svg', 'jpeg', 'gif'];
            
            function tryLoadLogo(index) {
                if (index >= logoFormats.length) {
                    return;
                }
                
                const format = logoFormats[index];
                const logoUrl = `${window.MuniAppConfig.imagesPath}/logo.${format}?v=${Date.now()}`;
                const img = new Image();
                
                img.onload = function() {
                    // Update all logo elements in the page
                    const logoElements = document.querySelectorAll('.system-logo, #system-logo, .app-logo');
                    logoElements.forEach(element => {
                        if (element.tagName === 'IMG') {
                            element.src = logoUrl;
                        } else {
                            element.style.backgroundImage = `url(${logoUrl})`;
                        }
                    });
                    
                    // Update sidebar logo if exists
                    const sidebarLogo = document.querySelector('.brand-link img');
                    if (sidebarLogo) {
                        sidebarLogo.src = logoUrl;
                    }
                };
                
                img.onerror = function() {
                    tryLoadLogo(index + 1);
                };
                
                img.src = logoUrl;
            }
            
            tryLoadLogo(0);
        };
    </script>
    
    @stack('scripts')
</body>
</html>