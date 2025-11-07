@extends('layouts.admin')

@section('title', 'Chat con Solicitante - Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-comments mr-2"></i>
                    Chat: {{ $solicitud->tracking_code }}
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.solicitudes.index') }}">Solicitudes</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.solicitudes.show', $solicitud->id) }}">{{ $solicitud->tracking_code }}</a></li>
                    <li class="breadcrumb-item active">Chat</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Información de la Solicitud -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user mr-1"></i>
                            Información del Solicitante
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img src="{{ $solicitud->user->avatar_url ?? asset('images/default-avatar.png') }}" 
                                 class="img-circle img-size-64" alt="Avatar">
                            <h5 class="mt-2">{{ $solicitud->user->name }}</h5>
                            <p class="text-muted">{{ $solicitud->user->email }}</p>
                        </div>
                        
                        <hr>
                        
                        <div class="info-box mb-2">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-file-alt"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Trámite</span>
                                <span class="info-box-number" style="font-size: 14px;">{{ $solicitud->tramite->nombre }}</span>
                            </div>
                        </div>
                        
                        <div class="info-box mb-2">
                            <span class="info-box-icon bg-warning">
                                <i class="fas fa-calendar"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Fecha de Solicitud</span>
                                <span class="info-box-number" style="font-size: 14px;">{{ $solicitud->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                        
                        <div class="info-box mb-2">
                            @php
                                $statusColors = [
                                    'pendiente' => 'secondary',
                                    'en_revision' => 'warning',
                                    'en_proceso' => 'info',
                                    'completado' => 'success',
                                    'rechazado' => 'danger'
                                ];
                            @endphp
                            <span class="info-box-icon bg-{{ $statusColors[$solicitud->estado] ?? 'secondary' }}">
                                <i class="fas fa-flag"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Estado</span>
                                <span class="info-box-number" style="font-size: 14px;">{{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}</span>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <a href="{{ route('admin.solicitudes.show', $solicitud->id) }}" class="btn btn-primary btn-block">
                                <i class="fas fa-eye"></i> Ver Solicitud Completa
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Chat Principal -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-comment-dots mr-1"></i>
                            Conversación
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" id="refreshChat">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <span class="badge badge-success" id="connectionStatus">
                                <i class="fas fa-circle"></i> Conectado
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Messages Container -->
                        <div id="messagesContainer" style="height: 400px; overflow-y: auto; border: 1px solid #dee2e6; padding: 15px; margin-bottom: 15px; background-color: #f8f9fa;">
                            <div id="messages">
                                <!-- Los mensajes se cargarán aquí dinámicamente -->
                                <div class="text-center text-muted">
                                    <i class="fas fa-comments fa-2x mb-2"></i>
                                    <p>Cargando conversación...</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Message Input -->
                        <div class="input-group">
                            <input type="text" id="messageInput" class="form-control" 
                                   placeholder="Escribe tu mensaje aquí..." maxlength="500">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="sendMessage">
                                    <i class="fas fa-paper-plane"></i> Enviar
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Presiona Enter para enviar</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.message {
    margin-bottom: 10px;
    padding: 8px 12px;
    border-radius: 10px;
    max-width: 80%;
    word-wrap: break-word;
}

.message.admin {
    background-color: #007bff;
    color: white;
    margin-left: auto;
    text-align: right;
}

.message.user {
    background-color: #e9ecef;
    color: #333;
}

.message-header {
    font-size: 0.8em;
    margin-bottom: 3px;
    opacity: 0.8;
}

.message-time {
    font-size: 0.7em;
    margin-top: 3px;
    opacity: 0.7;
}

#messagesContainer {
    background-image: 
        radial-gradient(circle at 20px 20px, #e3f2fd 2px, transparent 2px),
        radial-gradient(circle at 80px 80px, #f3e5f5 2px, transparent 2px);
    background-size: 100px 100px;
}

.typing-indicator {
    display: none;
    font-style: italic;
    color: #6c757d;
    margin: 10px 0;
}

.connection-status {
    position: fixed;
    top: 10px;
    right: 10px;
    z-index: 1000;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const room = '{{ $room }}';
    const solicitudId = {{ $solicitud->id }};
    const currentUserId = {{ auth()->id() }};
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendMessage');
    const messagesContainer = document.getElementById('messagesContainer');
    const messages = document.getElementById('messages');
    
    // Configuración del chat
    let eventSource;
    let lastMessageId = 0;
    
    // Inicializar chat
    initializeChat();
    
    function initializeChat() {
        loadMessages();
        setupEventSource();
        setupEventListeners();
    }
    
    function loadMessages() {
        fetch(`/chat/${room}/messages?type=solicitud`)
            .then(response => response.json())
            .then(data => {
                messages.innerHTML = '';
                if (data.success && data.messages && data.messages.length > 0) {
                    data.messages.forEach(message => {
                        displayMessage(message);
                        lastMessageId = Math.max(lastMessageId, message.id);
                    });
                } else {
                    messages.innerHTML = `
                        <div class="text-center text-muted">
                            <i class="fas fa-comments fa-2x mb-2"></i>
                            <p>No hay mensajes en esta conversación.</p>
                            <p>Inicia la conversación con el solicitante.</p>
                        </div>
                    `;
                }
                scrollToBottom();
            })
            .catch(error => {
                console.error('Error loading messages:', error);
                messages.innerHTML = `
                    <div class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p>Error al cargar los mensajes</p>
                    </div>
                `;
            });
    }
    
    function setupEventSource() {
        if (eventSource) {
            eventSource.close();
        }
        
        eventSource = new EventSource(`/chat/${room}/stream?type=solicitud`);
        
        eventSource.onmessage = function(event) {
            const message = JSON.parse(event.data);
            if (message.id > lastMessageId) {
                displayMessage(message);
                lastMessageId = message.id;
                scrollToBottom();
            }
        };
        
        eventSource.onerror = function(event) {
            console.error('EventSource error:', event);
            document.getElementById('connectionStatus').innerHTML = 
                '<i class="fas fa-circle text-danger"></i> Desconectado';
            
            // Intentar reconectar después de 5 segundos
            setTimeout(() => {
                setupEventSource();
            }, 5000);
        };
        
        eventSource.onopen = function(event) {
            document.getElementById('connectionStatus').innerHTML = 
                '<i class="fas fa-circle text-success"></i> Conectado';
        };
    }
    
    function setupEventListeners() {
        sendButton.addEventListener('click', sendMessage);
        
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
        
        document.getElementById('refreshChat').addEventListener('click', function() {
            loadMessages();
        });
    }
    
    function sendMessage() {
        const message = messageInput.value.trim();
        if (!message) return;
        
        const messageData = {
            message: message,
            room: room,
            chat_type: 'solicitud',
            solicitud_id: solicitudId
        };
        
        console.log('Enviando mensaje desde admin:', messageData);
        
        fetch('/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(messageData)
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                messageInput.value = '';
                // El mensaje aparecerá vía EventSource
            } else {
                console.error('Error en respuesta:', data);
                alert('Error al enviar mensaje: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            alert('Error al enviar el mensaje');
        });
    }
    
    function displayMessage(message) {
        const messageDiv = document.createElement('div');
        const isAdmin = message.user_id === currentUserId;
        
        messageDiv.className = `message ${isAdmin ? 'admin' : 'user'}`;
        messageDiv.innerHTML = `
            <div class="message-header">
                <strong>${message.user_name || 'Usuario'}</strong>
            </div>
            <div class="message-content">${escapeHtml(message.message)}</div>
            <div class="message-time">${formatTime(message.created_at)}</div>
        `;
        
        messages.appendChild(messageDiv);
    }
    
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function formatTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('es-ES', {
            hour: '2-digit',
            minute: '2-digit',
            day: '2-digit',
            month: '2-digit'
        });
    }
    
    // Limpiar al salir de la página
    window.addEventListener('beforeunload', function() {
        if (eventSource) {
            eventSource.close();
        }
    });
});
</script>
@endpush