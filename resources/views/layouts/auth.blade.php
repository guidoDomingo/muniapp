<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'MuniApp - Sistema Municipal')</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .auth-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
        }

        .logo-container {
            margin-bottom: 30px;
        }

        .logo-container img {
            max-height: 80px;
            max-width: 200px;
        }

        .auth-title {
            color: #333;
            font-weight: 600;
            margin-bottom: 30px;
            font-size: 1.8rem;
        }

        .form-control {
            border-radius: 12px;
            border: 2px solid #e3e6f0;
            padding: 12px 20px;
            font-size: 16px;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .form-label {
            font-weight: 500;
            color: #555;
            margin-bottom: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 12px;
            padding: 10px 25px;
            font-weight: 500;
            width: 100%;
            margin-top: 15px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }

        .alert {
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .copyright {
            margin-top: 30px;
            font-size: 14px;
            color: #666;
        }

        .copyright a {
            color: #667eea;
            text-decoration: none;
        }

        .copyright a:hover {
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .auth-container {
                padding: 15px;
            }
            
            .auth-card {
                padding: 30px 20px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="logo-container">
                <img id="system_logo" 
                     src="{{ asset('images/logo.png') }}" 
                     alt="Logo del Sistema" 
                     onerror="this.style.display='none'; document.querySelector('.logo-fallback').style.display='block';">
                <div class="logo-fallback" style="display: none;">
                    <i class="fas fa-city fa-3x" style="color: #667eea;"></i>
                    <h4 style="color: #667eea; margin-top: 10px;">MuniApp</h4>
                </div>
            </div>

            @yield('content')

            <div class="copyright">
                Desarrollado con <i class="fas fa-heart" style="color: #e74c3c;"></i> por el equipo de desarrollo
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        // Función para cargar el logo actual
        function loadCurrentLogo() {
            const logoImg = document.getElementById('system_logo');
            const logoFallback = document.querySelector('.logo-fallback');
            
            // Intentar cargar diferentes formatos de logo
            const logoFormats = ['png', 'jpg', 'jpeg', 'svg'];
            let logoLoaded = false;
            
            logoFormats.forEach(format => {
                if (!logoLoaded) {
                    const testImg = new Image();
                    testImg.onload = function() {
                        if (!logoLoaded) {
                            logoImg.src = `{{ asset('images/logo.${format}') }}?t=${new Date().getTime()}`;
                            logoImg.style.display = 'block';
                            logoFallback.style.display = 'none';
                            logoLoaded = true;
                        }
                    };
                    testImg.src = `{{ asset('images/logo.${format}') }}?t=${new Date().getTime()}`;
                }
            });
        }

        // Cargar logo al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            loadCurrentLogo();
        });
    </script>

    @stack('scripts')
</body>
</html>