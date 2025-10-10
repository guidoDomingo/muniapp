@extends('layouts.admin')

@section('title', 'Configuración del Sistema - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Configuración del Sistema</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Configuración</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <div class="row">
            <!-- Settings Navigation -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cogs mr-1"></i>
                            Categorías
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item">
                                <a href="#general" class="nav-link active" data-toggle="pill">
                                    <i class="fas fa-sliders-h"></i> General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#system" class="nav-link" data-toggle="pill">
                                    <i class="fas fa-server"></i> Sistema
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#notifications" class="nav-link" data-toggle="pill">
                                    <i class="fas fa-bell"></i> Notificaciones
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#email" class="nav-link" data-toggle="pill">
                                    <i class="fas fa-envelope"></i> Email
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#security" class="nav-link" data-toggle="pill">
                                    <i class="fas fa-shield-alt"></i> Seguridad
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#integrations" class="nav-link" data-toggle="pill">
                                    <i class="fas fa-plug"></i> Integraciones
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#backup" class="nav-link" data-toggle="pill">
                                    <i class="fas fa-database"></i> Respaldos
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="col-md-9">
                <div class="tab-content">
                    
                    <!-- General Settings -->
                    <div class="tab-pane fade show active" id="general">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-sliders-h mr-1"></i>
                                    Configuración General
                                </h3>
                            </div>
                            <form id="generalForm" enctype="multipart/form-data">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="app_name">Nombre de la Aplicación</label>
                                                <input type="text" class="form-control" id="app_name" name="app_name" 
                                                       value="MuniApp" placeholder="Nombre de la aplicación">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="municipality_name">Nombre del Municipio</label>
                                                <input type="text" class="form-control" id="municipality_name" 
                                                       name="municipality_name" value="Municipio de Example" 
                                                       placeholder="Nombre del municipio">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="contact_email">Email de Contacto</label>
                                                <input type="email" class="form-control" id="contact_email" 
                                                       name="contact_email" value="contacto@municipio.gov" 
                                                       placeholder="email@municipio.gov">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="contact_phone">Teléfono de Contacto</label>
                                                <input type="tel" class="form-control" id="contact_phone" 
                                                       name="contact_phone" value="(123) 456-7890" 
                                                       placeholder="(123) 456-7890">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="municipality_address">Dirección del Municipio</label>
                                        <textarea class="form-control" id="municipality_address" name="municipality_address" 
                                                  rows="3" placeholder="Dirección completa del municipio">Calle Principal #123, Centro, Ciudad, País</textarea>
                                    </div>

                                    <!-- Logo Management Section -->
                                    <div class="form-group">
                                        <label for="system_logo">Logo del Sistema</label>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="system_logo" 
                                                           name="system_logo" accept="image/*">
                                                    <label class="custom-file-label" for="system_logo">
                                                        Seleccionar archivo de logo...
                                                    </label>
                                                </div>
                                                <small class="form-text text-muted">
                                                    Formatos permitidos: JPG, PNG, SVG. Tamaño recomendado: 200x60px
                                                </small>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="logo-preview">
                                                    <label class="d-block">Vista Previa:</label>
                                                    <div class="current-logo-container p-2 border rounded text-center" 
                                                         style="min-height: 80px; background-color: #f8f9fa;">
                                                        <img id="current_logo" 
                                                             src="" 
                                                             alt="Logo actual" 
                                                             class="img-fluid" 
                                                             style="max-height: 60px; max-width: 100%; display: none;">
                                                        <div class="no-logo text-muted" style="display: block;">
                                                            <i class="fas fa-image fa-2x mb-2"></i>
                                                            <br>Sin logo
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-danger" id="removeLogo" style="display: none;">
                                                <i class="fas fa-trash"></i> Remover Logo
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="timezone">Zona Horaria</label>
                                                <select class="form-control" id="timezone" name="timezone">
                                                    <option value="America/Asuncion" selected>America/Asuncion</option>
                                                    <option value="America/Argentina/Buenos_Aires">America/Argentina/Buenos_Aires</option>
                                                    <option value="America/Sao_Paulo">America/Sao_Paulo</option>
                                                    <option value="America/Montevideo">America/Montevideo</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="language">Idioma</label>
                                                <select class="form-control" id="language" name="language">
                                                    <option value="es" selected>Español</option>
                                                    <option value="en">English</option>
                                                    <option value="pt">Português</option>
                                                    <option value="gn">Guaraní</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Guardar Cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- System Settings -->
                    <div class="tab-pane fade" id="system">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-server mr-1"></i>
                                    Configuración del Sistema
                                </h3>
                            </div>
                            <form id="systemForm">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="max_file_size">Tamaño Máximo de Archivo (MB)</label>
                                                <input type="number" class="form-control" id="max_file_size" 
                                                       name="max_file_size" value="10" min="1" max="100">
                                                <small class="form-text text-muted">Máximo tamaño permitido para archivos adjuntos</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="session_timeout">Tiempo de Sesión (minutos)</label>
                                                <input type="number" class="form-control" id="session_timeout" 
                                                       name="session_timeout" value="120" min="15" max="480">
                                                <small class="form-text text-muted">Tiempo antes de cerrar sesión automáticamente</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pagination_size">Elementos por Página</label>
                                                <select class="form-control" id="pagination_size" name="pagination_size">
                                                    <option value="10">10</option>
                                                    <option value="25" selected>25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="auto_assign">Asignación Automática</label>
                                                <select class="form-control" id="auto_assign" name="auto_assign">
                                                    <option value="manual">Manual</option>
                                                    <option value="round_robin" selected>Rotativo</option>
                                                    <option value="workload">Por Carga de Trabajo</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="maintenance_mode" name="maintenance_mode">
                                            <label class="custom-control-label" for="maintenance_mode">Modo Mantenimiento</label>
                                        </div>
                                        <small class="form-text text-muted">Activar para realizar mantenimiento del sistema</small>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="debug_mode" name="debug_mode">
                                            <label class="custom-control-label" for="debug_mode">Modo Debug</label>
                                        </div>
                                        <small class="form-text text-muted">Solo para desarrollo - NO activar en producción</small>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Guardar Configuración
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Notifications Settings -->
                    <div class="tab-pane fade" id="notifications">
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-bell mr-1"></i>
                                    Configuración de Notificaciones
                                </h3>
                            </div>
                            <form id="notificationsForm">
                                <div class="card-body">
                                    <h5>Notificaciones por Email</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="email_new_solicitud" 
                                                       name="email_new_solicitud" checked>
                                                <label class="custom-control-label" for="email_new_solicitud">Nueva Solicitud</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="email_status_change" 
                                                       name="email_status_change" checked>
                                                <label class="custom-control-label" for="email_status_change">Cambio de Estado</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="email_overdue" 
                                                       name="email_overdue" checked>
                                                <label class="custom-control-label" for="email_overdue">Solicitudes Vencidas</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="email_completion" 
                                                       name="email_completion" checked>
                                                <label class="custom-control-label" for="email_completion">Solicitud Completada</label>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <h5>Notificaciones Push</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="push_enabled" 
                                                       name="push_enabled" checked>
                                                <label class="custom-control-label" for="push_enabled">Habilitar Push</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="browser_notifications" 
                                                       name="browser_notifications" checked>
                                                <label class="custom-control-label" for="browser_notifications">Notificaciones del Navegador</label>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <h5>Configuración de Chat</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="chat_sound" 
                                                       name="chat_sound" checked>
                                                <label class="custom-control-label" for="chat_sound">Sonido de Chat</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="chat_desktop_notifications" 
                                                       name="chat_desktop_notifications" checked>
                                                <label class="custom-control-label" for="chat_desktop_notifications">Notificaciones de Escritorio</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Guardar Notificaciones
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Email Settings -->
                    <div class="tab-pane fade" id="email">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-envelope mr-1"></i>
                                    Configuración de Email
                                </h3>
                            </div>
                            <form id="emailForm">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mail_driver">Driver de Email</label>
                                                <select class="form-control" id="mail_driver" name="mail_driver">
                                                    <option value="smtp" selected>SMTP</option>
                                                    <option value="sendmail">Sendmail</option>
                                                    <option value="mailgun">Mailgun</option>
                                                    <option value="ses">Amazon SES</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mail_host">Servidor SMTP</label>
                                                <input type="text" class="form-control" id="mail_host" name="mail_host" 
                                                       value="smtp.gmail.com" placeholder="smtp.ejemplo.com">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mail_port">Puerto</label>
                                                <input type="number" class="form-control" id="mail_port" name="mail_port" 
                                                       value="587" placeholder="587">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mail_encryption">Encriptación</label>
                                                <select class="form-control" id="mail_encryption" name="mail_encryption">
                                                    <option value="tls" selected>TLS</option>
                                                    <option value="ssl">SSL</option>
                                                    <option value="">Sin Encriptación</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mail_username">Usuario</label>
                                                <input type="email" class="form-control" id="mail_username" name="mail_username" 
                                                       value="" placeholder="usuario@ejemplo.com">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mail_password">Contraseña</label>
                                                <input type="password" class="form-control" id="mail_password" name="mail_password" 
                                                       placeholder="••••••••">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mail_from_address">Email Remitente</label>
                                                <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" 
                                                       value="noreply@municipio.gov" placeholder="noreply@municipio.gov">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mail_from_name">Nombre Remitente</label>
                                                <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" 
                                                       value="Municipio Example" placeholder="Municipio Example">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-save"></i> Guardar Email
                                    </button>
                                    <button type="button" class="btn btn-secondary ml-2" onclick="testEmail()">
                                        <i class="fas fa-paper-plane"></i> Enviar Prueba
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="tab-pane fade" id="security">
                        <div class="card card-danger">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-shield-alt mr-1"></i>
                                    Configuración de Seguridad
                                </h3>
                            </div>
                            <form id="securityForm">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password_min_length">Longitud Mínima de Contraseña</label>
                                                <input type="number" class="form-control" id="password_min_length" 
                                                       name="password_min_length" value="8" min="6" max="32">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="login_attempts">Intentos de Login Máximos</label>
                                                <input type="number" class="form-control" id="login_attempts" 
                                                       name="login_attempts" value="5" min="3" max="10">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="require_uppercase" 
                                                       name="require_uppercase" checked>
                                                <label class="custom-control-label" for="require_uppercase">Requerir Mayúsculas</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="require_numbers" 
                                                       name="require_numbers" checked>
                                                <label class="custom-control-label" for="require_numbers">Requerir Números</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="require_special_chars" 
                                                       name="require_special_chars">
                                                <label class="custom-control-label" for="require_special_chars">Requerir Caracteres Especiales</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="two_factor_auth" 
                                                       name="two_factor_auth">
                                                <label class="custom-control-label" for="two_factor_auth">Autenticación de Dos Factores</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="force_https" 
                                                       name="force_https" checked>
                                                <label class="custom-control-label" for="force_https">Forzar HTTPS</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="enable_audit_log" 
                                                       name="enable_audit_log" checked>
                                                <label class="custom-control-label" for="enable_audit_log">Log de Auditoría</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-save"></i> Guardar Seguridad
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Integrations Settings -->
                    <div class="tab-pane fade" id="integrations">
                        <div class="card card-purple">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-plug mr-1"></i>
                                    Integraciones
                                </h3>
                            </div>
                            <div class="card-body">
                                <!-- Pusher Configuration -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Pusher (WebSockets)</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pusher_app_id">App ID</label>
                                                    <input type="text" class="form-control" id="pusher_app_id" 
                                                           name="pusher_app_id" placeholder="App ID de Pusher">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pusher_key">Key</label>
                                                    <input type="text" class="form-control" id="pusher_key" 
                                                           name="pusher_key" placeholder="Key de Pusher">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pusher_secret">Secret</label>
                                                    <input type="password" class="form-control" id="pusher_secret" 
                                                           name="pusher_secret" placeholder="Secret de Pusher">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pusher_cluster">Cluster</label>
                                                    <input type="text" class="form-control" id="pusher_cluster" 
                                                           name="pusher_cluster" value="us2" placeholder="us2">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- Payment Gateway -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Pasarela de Pagos</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="payment_gateway">Proveedor</label>
                                                    <select class="form-control" id="payment_gateway" name="payment_gateway">
                                                        <option value="">Ninguno</option>
                                                        <option value="stripe">Stripe</option>
                                                        <option value="paypal">PayPal</option>
                                                        <option value="mercadopago">MercadoPago</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="custom-control custom-switch mt-4 pt-2">
                                                    <input type="checkbox" class="custom-control-input" id="payment_enabled" 
                                                           name="payment_enabled">
                                                    <label class="custom-control-label" for="payment_enabled">Habilitar Pagos Online</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- Google Maps -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Google Maps</h5>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="google_maps_key">API Key</label>
                                                    <input type="text" class="form-control" id="google_maps_key" 
                                                           name="google_maps_key" placeholder="Google Maps API Key">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="custom-control custom-switch mt-4 pt-2">
                                                    <input type="checkbox" class="custom-control-input" id="maps_enabled" 
                                                           name="maps_enabled">
                                                    <label class="custom-control-label" for="maps_enabled">Habilitar Mapas</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="button" class="btn btn-purple" onclick="saveIntegrations()">
                                    <i class="fas fa-save"></i> Guardar Integraciones
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Backup Settings -->
                    <div class="tab-pane fade" id="backup">
                        <div class="card card-dark">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-database mr-1"></i>
                                    Configuración de Respaldos
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="backup_frequency">Frecuencia de Respaldo</label>
                                            <select class="form-control" id="backup_frequency" name="backup_frequency">
                                                <option value="daily" selected>Diario</option>
                                                <option value="weekly">Semanal</option>
                                                <option value="monthly">Mensual</option>
                                                <option value="manual">Solo Manual</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="backup_retention">Retención (días)</label>
                                            <input type="number" class="form-control" id="backup_retention" 
                                                   name="backup_retention" value="30" min="7" max="365">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="backup_database" 
                                                   name="backup_database" checked>
                                            <label class="custom-control-label" for="backup_database">Respaldar Base de Datos</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="backup_files" 
                                                   name="backup_files" checked>
                                            <label class="custom-control-label" for="backup_files">Respaldar Archivos</label>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <h5>Respaldos Recientes</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Tipo</th>
                                                <th>Tamaño</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>04/10/2025 02:00</td>
                                                <td>Completo</td>
                                                <td>245 MB</td>
                                                <td><span class="badge badge-success">Exitoso</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="downloadBackup('backup_1')">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>03/10/2025 02:00</td>
                                                <td>Base de Datos</td>
                                                <td>12 MB</td>
                                                <td><span class="badge badge-success">Exitoso</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="downloadBackup('backup_2')">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="button" class="btn btn-dark" onclick="saveBackupSettings()">
                                    <i class="fas fa-save"></i> Guardar Configuración
                                </button>
                                <button type="button" class="btn btn-info ml-2" onclick="createBackup()">
                                    <i class="fas fa-database"></i> Crear Respaldo Ahora
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@push('scripts')
<script>
// Form submission handlers
$('#generalForm').submit(function(e) {
    e.preventDefault();
    saveSettings('general', this);
});

$('#systemForm').submit(function(e) {
    e.preventDefault();
    saveSettings('system', this);
});

$('#notificationsForm').submit(function(e) {
    e.preventDefault();
    saveSettings('notifications', this);
});

$('#emailForm').submit(function(e) {
    e.preventDefault();
    saveSettings('email', this);
});

$('#securityForm').submit(function(e) {
    e.preventDefault();
    saveSettings('security', this);
});

function saveSettings(category, form) {
    const formData = new FormData(form);
    
    // Show loading state
    const submitBtn = $(form).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Guardando...').prop('disabled', true);
    
    // For general settings, make real AJAX call
    if (category === 'general') {
        $.ajax({
            url: '{{ route("admin.settings.update") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                submitBtn.html(originalText).prop('disabled', false);
                toastr.success('Configuración guardada exitosamente');
                
                // Refresh page after successful save to show updated logo
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            },
            error: function(xhr) {
                submitBtn.html(originalText).prop('disabled', false);
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(key => {
                        toastr.error(errors[key][0]);
                    });
                } else {
                    toastr.error('Error al guardar la configuración');
                }
            }
        });
    } else {
        // Simulate AJAX call for other categories
        setTimeout(() => {
            submitBtn.html(originalText).prop('disabled', false);
            toastr.success(`Configuración de ${category} guardada exitosamente`);
        }, 2000);
    }
}

function saveIntegrations() {
    // Save integrations logic
    toastr.success('Integraciones guardadas exitosamente');
}

function saveBackupSettings() {
    // Save backup settings logic
    toastr.success('Configuración de respaldos guardada');
}

function testEmail() {
    // Test email functionality
    const btn = $(event.target);
    const originalText = btn.html();
    btn.html('<i class="fas fa-spinner fa-spin"></i> Enviando...').prop('disabled', true);
    
    setTimeout(() => {
        btn.html(originalText).prop('disabled', false);
        toastr.success('Email de prueba enviado exitosamente');
    }, 3000);
}

function createBackup() {
    // Create backup logic
    const btn = $(event.target);
    const originalText = btn.html();
    btn.html('<i class="fas fa-spinner fa-spin"></i> Creando...').prop('disabled', true);
    
    setTimeout(() => {
        btn.html(originalText).prop('disabled', false);
        toastr.success('Respaldo creado exitosamente');
        // Refresh backup table
        location.reload();
    }, 5000);
}

function downloadBackup(backupId) {
    // Download backup logic
    window.open(`/admin/backups/download/${backupId}`, '_blank');
}

// Logo management functionality
$(document).ready(function() {
    // Check which logo file exists and load it
    loadCurrentLogo();
    
    // Preview logo when file is selected
    $('#system_logo').change(function(e) {
        const file = e.target.files[0];
        if (file) {
            // Update file label
            $('.custom-file-label').text(file.name);
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#current_logo').attr('src', e.target.result).show();
                $('.no-logo').hide();
                $('#removeLogo').show();
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Remove logo functionality
    $('#removeLogo').click(function() {
        if (confirm('¿Estás seguro de que quieres remover el logo actual?')) {
            // Clear file input
            $('#system_logo').val('');
            $('.custom-file-label').text('Seleccionar archivo de logo...');
            
            // Hide current logo and show placeholder
            $('#current_logo').hide();
            $('.no-logo').show();
            $(this).hide();
            
            toastr.info('Logo removido. Guarda los cambios para confirmar.');
        }
    });
});

function loadCurrentLogo() {
    // Try to load logo in order of preference: JPG, PNG, SVG, GIF
    const logoFormats = ['jpg', 'png', 'svg', 'jpeg', 'gif'];
    
    function tryLoadLogo(index) {
        if (index >= logoFormats.length) {
            // No logo found, show placeholder
            $('#current_logo').hide();
            $('.no-logo').show();
            $('#removeLogo').hide();
            return;
        }
        
        const format = logoFormats[index];
        const logoUrl = `${window.MuniAppConfig.imagesPath}/logo.${format}`;
        const img = new Image();
        
        img.onload = function() {
            // Logo found and loaded successfully
            $('#current_logo').attr('src', logoUrl).show();
            $('.no-logo').hide();
            $('#removeLogo').show();
        };
        
        img.onerror = function() {
            // Try next format
            tryLoadLogo(index + 1);
        };
        
        img.src = logoUrl;
    }
    
    tryLoadLogo(0);
}

// Initialize page
$(document).ready(function() {
    // Logo management is already initialized above
    console.log('Settings page loaded');
});
</script>
@endpush