<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MuniApp - Sistema Municipal Digital</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('src/assets/img/favicon.ico') }}"/>
    
    <!-- Modern CSS Framework -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-bg: #f8fafc;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --text-color: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-color);
            line-height: 1.6;
        }

        /* Modern Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: var(--sidebar-bg);
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--sidebar-hover) transparent;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--sidebar-hover);
            border-radius: 3px;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid var(--sidebar-hover);
            text-align: center;
        }

        .sidebar-logo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: block;
        }

        .sidebar-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: white;
        }

        .sidebar-subtitle {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
        }

        /* Navigation */
        .nav-section {
            padding: 1.5rem 0;
        }

        .nav-section-title {
            padding: 0 1.5rem 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-muted);
        }

        .nav-item {
            margin: 0.25rem 1rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.25rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .nav-link:hover {
            background: var(--sidebar-hover);
            color: white;
            transform: translateX(4px);
        }

        .nav-link.active {
            background: var(--primary-color);
            color: white;
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            margin-right: 1rem;
            opacity: 0.8;
        }

        .nav-text {
            font-size: 0.925rem;
        }

        .badge {
            margin-left: auto;
            font-size: 0.75rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            background: var(--light-bg);
        }

        .top-navbar {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .content-area {
            padding: 2rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-toggle {
                display: block;
            }
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            padding: 0.5rem;
            margin-right: 1rem;
        }

        /* Modern Cards */
        .modern-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: var(--text-muted);
            margin-bottom: 2rem;
        }

        /* Buttons */
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        /* Dropdown improvements */
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border-radius: 0.75rem;
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
        }

        .dropdown-item i {
            width: 16px;
            height: 16px;
            margin-right: 0.75rem;
        }
    </style>
</head>

<body>
    <!-- Modern Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <img src="{{ url('images/logo.jpg') }}" alt="Logo" class="sidebar-logo system-logo" onerror="this.style.display='none'">
            <h1 class="sidebar-title">MuniApp</h1>
            <p class="sidebar-subtitle">Sistema Municipal Digital</p>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <!-- Sección Principal para Ciudadanos -->
            <div class="nav-section">
                <div class="nav-section-title">Servicios Ciudadanos</div>
                
                <div class="nav-item">
                    <a href="{{ route('tramites.index') }}" class="nav-link {{ request()->routeIs('tramites.*') ? 'active' : '' }}">
                        <i data-feather="file-text" class="nav-icon"></i>
                        <span class="nav-text">Trámites Disponibles</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('solicitudes.index') }}" class="nav-link {{ request()->routeIs('solicitudes.*') ? 'active' : '' }}">
                        <i data-feather="clock" class="nav-icon"></i>
                        <span class="nav-text">Mis Solicitudes</span>
                        @if(auth()->user()->solicitudes()->whereIn('estado', ['recibido', 'en_revision', 'en_proceso'])->count() > 0)
                            <span class="badge bg-warning">{{ auth()->user()->solicitudes()->whereIn('estado', ['recibido', 'en_revision', 'en_proceso'])->count() }}</span>
                        @endif
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('chat.index') }}" class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                        <i data-feather="message-circle" class="nav-icon"></i>
                        <span class="nav-text">Soporte en Vivo</span>
                    </a>
                </div>
            </div>

            <!-- Sección Administrativa (Solo para Admin/Comisión) -->
            @if(Auth::user()->hasAnyRole(['admin', 'commission']))
            <div class="nav-section">
                <div class="nav-section-title">Panel Administrativo</div>
                
                @if(Auth::user()->hasRole('admin'))
                <div class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i data-feather="activity" class="nav-icon"></i>
                        <span class="nav-text">Panel de Control</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i data-feather="users" class="nav-icon"></i>
                        <span class="nav-text">Gestión de Usuarios</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.tramites.index') }}" class="nav-link {{ request()->routeIs('admin.tramites.*') ? 'active' : '' }}">
                        <i data-feather="clipboard" class="nav-icon"></i>
                        <span class="nav-text">Gestión de Trámites</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.solicitudes.index') }}" class="nav-link {{ request()->routeIs('admin.solicitudes.*') ? 'active' : '' }}">
                        <i data-feather="list" class="nav-icon"></i>
                        <span class="nav-text">Gestión Solicitudes</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.departments.index') }}" class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                        <i data-feather="home" class="nav-icon"></i>
                        <span class="nav-text">Departamentos</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.analytics') }}" class="nav-link {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                        <i data-feather="bar-chart-2" class="nav-icon"></i>
                        <span class="nav-text">Analytics</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.reports') }}" class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                        <i data-feather="file-minus" class="nav-icon"></i>
                        <span class="nav-text">Reportes</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <i data-feather="settings" class="nav-icon"></i>
                        <span class="nav-text">Configuraciones</span>
                    </a>
                </div>
                @endif

                @if(Auth::user()->hasRole('commission'))
                <div class="nav-item">
                    <a href="{{ route('commission.dashboard') }}" class="nav-link {{ request()->routeIs('commission.*') ? 'active' : '' }}">
                        <i data-feather="briefcase" class="nav-icon"></i>
                        <span class="nav-text">Panel Comisión</span>
                    </a>
                </div>
                @endif
            </div>
            @endif

            <!-- Sección de Usuario -->
            <div class="nav-section">
                <div class="nav-section-title">Mi Cuenta</div>
                
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i data-feather="user" class="nav-icon"></i>
                        <span class="nav-text">Mi Perfil</span>
                    </a>
                </div>

                <div class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" class="nav-link p-0" style="background: none; border: none;">
                        @csrf
                        <button type="submit" class="nav-link" style="background: none; border: none; width: 100%; text-align: left;">
                            <i data-feather="log-out" class="nav-icon"></i>
                            <span class="nav-text">Cerrar Sesión</span>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Top Navigation Bar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="mobile-toggle" onclick="toggleSidebar()">
                    <i data-feather="menu"></i>
                </button>
                <img src="{{ url('images/logo.jpg') }}" alt="Logo" class="system-logo me-3" style="height: 30px; width: auto;" onerror="this.style.display='none'">
                <h2 class="mb-0 text-muted">Bienvenido, {{ Auth::user()->name }}</h2>
            </div>
            
            <div class="user-menu">
                <!-- Notifications -->
                <div class="dropdown">
                    <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                        <i data-feather="bell"></i>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="badge bg-danger rounded-pill">{{ auth()->user()->unreadNotifications->count() }}</span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Notificaciones</h6></li>
                        @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                            <li><a class="dropdown-item" href="#">{{ $notification->data['message'] ?? 'Nueva notificación' }}</a></li>
                        @empty
                            <li><span class="dropdown-item text-muted">No hay notificaciones nuevas</span></li>
                        @endforelse
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                    </ul>
                </div>

                <!-- User Profile -->
                <div class="dropdown">
                    <button class="btn btn-link d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="ms-2 d-none d-md-inline">{{ Auth::user()->name }}</span>
                        <i data-feather="chevron-down" class="ms-1"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i data-feather="user" class="me-2"></i>Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i data-feather="settings" class="me-2"></i>Configuración</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="mb-0">
                                @csrf
                                <button type="submit" class="dropdown-item" style="border: none; background: none; width: 100%; text-align: left;">
                                    <i data-feather="log-out" class="me-2"></i>Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="content-area">
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Global configuration from Laravel
        window.MuniAppConfig = {
            baseUrl: '{{ url('/') }}',
            assetUrl: '{{ asset('') }}',
            imagesPath: '{{ url('images') }}'
        };
        console.log('MuniApp Config:', window.MuniAppConfig);

        // Initialize Feather Icons
        feather.replace();

        // Mobile sidebar toggle
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-toggle');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Active link highlighting
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
        });
        
        // Global logo update function
        window.updateSystemLogo = function() {
            const timestamp = new Date().getTime();
            const logoElements = document.querySelectorAll('.system-logo, #system-logo, .app-logo');
            
            // Lista de formatos de logo a verificar en orden de prioridad
            const logoFormats = ['jpg', 'png', 'svg'];
            
            logoFormats.forEach(format => {
                const logoUrl = `${window.MuniAppConfig.imagesPath}/logo.${format}?v=${timestamp}`;
                
                // Verificar si el archivo existe haciendo una petición HEAD
                fetch(logoUrl, { method: 'HEAD' })
                    .then(response => {
                        if (response.ok) {
                            logoElements.forEach(element => {
                                if (element.tagName === 'IMG') {
                                    element.src = logoUrl;
                                }
                            });
                            
                            // También actualizar elementos con estilos background-image
                            document.querySelectorAll('.navbar-brand, .brand-link').forEach(element => {
                                if (element.style.backgroundImage) {
                                    element.style.backgroundImage = `url('${logoUrl}')`;
                                }
                            });
                            
                            // Update all navbar and top-bar logos
                            const navbarLogos = document.querySelectorAll('.navbar img, .top-navbar img');
                            navbarLogos.forEach(img => {
                                if (img.classList.contains('system-logo')) {
                                    img.src = logoUrl;
                                }
                            });
                            
                            // Force update ALL img elements that might contain logos
                            const allImages = document.querySelectorAll('img');
                            allImages.forEach(img => {
                                if (img.src && (img.src.includes('/images/logo.') || img.alt.toLowerCase().includes('logo'))) {
                                    const oldSrc = img.src;
                                    img.src = logoUrl;
                                    console.log('Force updated img:', oldSrc, '→', logoUrl);
                                }
                            });
                            
                            console.log('Citizen logos updated with:', logoUrl);
                        }
                    })
                    .catch(error => {
                        console.log('Logo format not found:', format);
                    });
            });
        };
    </script>

    @yield('scripts')
</body>
</html>