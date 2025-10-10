@extends('layouts.citizen')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="page-title">
                <i data-feather="message-circle" class="me-2"></i>
                Chat de Soporte Municipal
            </h1>
            <p class="page-subtitle">Comunícate en tiempo real con nuestro equipo de atención ciudadana</p>
        </div>
    </div>

    <div class="row">
        <!-- Lista de salas de chat -->
        <div class="col-lg-4 col-md-5">
            <div class="modern-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i data-feather="users" class="me-2"></i>
                        Salas de Chat
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($availableRooms ?? [] as $room)
                        <a href="#" class="list-group-item list-group-item-action room-item" 
                           data-room="{{ $room['name'] ?? 'general' }}"
                           data-display="{{ $room['display_name'] ?? 'General' }}">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $room['display_name'] ?? 'General' }}</h6>
                                @if(isset($room['unread_count']) && $room['unread_count'] > 0)
                                <span class="badge bg-primary rounded-pill">{{ $room['unread_count'] }}</span>
                                @endif
                            </div>
                            <small class="text-muted">
                                @if($room['type'] === 'support')
                                    Soporte general
                                @elseif($room['type'] === 'solicitud')
                                    Consulta sobre trámite
                                @else
                                    Chat privado
                                @endif
                            </small>
                        </a>
                        @empty
                        <div class="list-group-item text-center text-muted">
                            <i data-feather="message-square" class="mb-2"></i>
                            <p class="mb-0">No hay salas disponibles</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-primary btn-sm w-100" id="newChatBtn">
                        <i data-feather="plus" class="me-1"></i>
                        Nueva Consulta
                    </button>
                </div>
            </div>
        </div>

        <!-- Área de chat principal -->
        <div class="col-lg-8 col-md-7">
            <div class="modern-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0" id="chatTitle">
                            <i data-feather="message-circle" class="me-2"></i>
                            Selecciona una sala de chat
                        </h5>
                        <span id="onlineStatus" class="badge bg-secondary ms-2" style="display: none;">
                            <i data-feather="circle" style="width: 12px; height: 12px;"></i> Desconectado
                        </span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-2" id="onlineStatus">
                            <i data-feather="circle" style="width: 12px; height: 12px;"></i>
                            En línea
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshChat">
                            <i data-feather="refresh-cw"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Área de mensajes -->
                    <div id="chatMessages" class="chat-messages-container">
                        <div class="text-center text-muted p-5">
                            <i data-feather="message-square" style="width: 48px; height: 48px;" class="mb-3"></i>
                            <p class="mb-0">Selecciona una sala para comenzar a chatear</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <!-- Formulario de envío de mensajes -->
                    <form id="messageForm" class="d-none">
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   id="messageInput" 
                                   placeholder="Escribe tu mensaje aquí..." 
                                   maxlength="500">
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="send"></i>
                                <span class="d-none d-md-inline ms-1">Enviar</span>
                            </button>
                        </div>
                    </form>
                    <div id="chatPlaceholder" class="text-center text-muted">
                        <small>Selecciona una sala para comenzar a chatear</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .chat-messages-container {
        height: 400px;
        overflow-y: auto;
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
    }
    
    .message {
        margin: 0.75rem 1rem;
        display: flex;
        flex-direction: column;
    }
    
    .message.sent {
        align-items: flex-end;
    }
    
    .message.received {
        align-items: flex-start;
    }
    
    .message-content {
        max-width: 70%;
        padding: 0.75rem 1rem;
        border-radius: 1.25rem;
        position: relative;
        word-wrap: break-word;
    }
    
    .message.sent .message-content {
        background-color: var(--primary-color);
        color: white;
    }
    
    .message.received .message-content {
        background-color: white;
        border: 1px solid #dee2e6;
        color: var(--text-color);
    }
    
    .message-time {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
    }
    
    .message-author {
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
        color: var(--text-color);
    }
    
    .room-item:hover {
        background-color: rgba(37, 99, 235, 0.1);
    }
    
    .room-item.active {
        background-color: var(--primary-color);
        color: white;
    }
    
    .connection-status {
        position: relative;
    }
    
    .connection-status.connected::before {
        content: '';
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
        animation: pulse 2s infinite;
    }
    
    .connection-status.disconnected::before {
        content: '';
        width: 8px;
        height: 8px;
        background-color: #ef4444;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
</style>

<script>
    // Variables globales
    let currentRoom = null;
    let currentUser = @json(auth()->user());
    let eventSource = null;
    let lastMessageId = 0;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Feather Icons
        feather.replace();
        
        // Event listeners
        setupEventListeners();
        
        console.log('Chat initialized with SSE (Server-Sent Events) - Real Time Mode');
    });
    
    function setupEventListeners() {
        // Selección de sala
        document.querySelectorAll('.room-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const roomName = this.dataset.room;
                const displayName = this.dataset.display;
                selectRoom(roomName, displayName);
            });
        });
        
        // Envío de mensajes
        const messageForm = document.getElementById('messageForm');
        if (messageForm) {
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                sendMessage();
            });
        }
        
        // Refresh chat
        const refreshBtn = document.getElementById('refreshChat');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                if (currentRoom) {
                    loadMessages(currentRoom);
                }
            });
        }
        
        // Nueva consulta
        const newChatBtn = document.getElementById('newChatBtn');
        if (newChatBtn) {
            newChatBtn.addEventListener('click', function() {
                alert('Funcionalidad de nueva consulta próximamente disponible');
            });
        }
    }
    
    function selectRoom(roomName, displayName) {
        // Cerrar conexión SSE anterior
        if (eventSource) {
            eventSource.close();
            updateConnectionStatus('disconnected');
        }
        
        // Actualizar UI
        currentRoom = roomName;
        
        // Actualizar título
        document.getElementById('chatTitle').innerHTML = 
            `<i data-feather="message-circle" class="me-2"></i>${displayName}`;
        feather.replace();
        
        // Mostrar formulario
        document.getElementById('messageForm').classList.remove('d-none');
        document.getElementById('chatPlaceholder').classList.add('d-none');
        
        // Actualizar salas activas
        document.querySelectorAll('.room-item').forEach(item => {
            item.classList.remove('active');
        });
        document.querySelector(`[data-room="${roomName}"]`)?.classList.add('active');
        
        // Cargar mensajes iniciales
        loadMessages(roomName);
        
        // Iniciar SSE para tiempo real
        startSSE(roomName);
    }
    
    function startSSE(roomName) {
        const chatType = 'public'; // Por ahora usar público
        const url = `/chat/${roomName}/stream?type=${chatType}&lastId=${lastMessageId}`;
        
        eventSource = new EventSource(url);
        
        eventSource.onopen = function(event) {
            console.log('SSE Connected to room:', roomName);
            updateConnectionStatus('connected');
        };
        
        eventSource.addEventListener('connected', function(event) {
            const data = JSON.parse(event.data);
            console.log('SSE Connected to room:', data.room);
            updateConnectionStatus('connected');
        });
        
        eventSource.addEventListener('new-message', function(event) {
            const message = JSON.parse(event.data);
            console.log('New message received:', message);
            
            // Solo agregar el mensaje si no es del usuario actual (evitar duplicados)
            if (message.user_id != currentUser.id) {
                addMessageToChat(message);
                lastMessageId = Math.max(lastMessageId, message.id);
            }
        });
        
        eventSource.addEventListener('heartbeat', function(event) {
            console.log('SSE Heartbeat received');
        });
        
        eventSource.addEventListener('error', function(event) {
            console.error('SSE Error:', event);
            updateConnectionStatus('disconnected');
        });
        
        eventSource.onerror = function(event) {
            console.error('SSE Connection error:', event);
            updateConnectionStatus('disconnected');
            
            // Reconexión automática después de 5 segundos
            setTimeout(() => {
                if (currentRoom && eventSource.readyState === EventSource.CLOSED) {
                    console.log('Attempting to reconnect SSE...');
                    startSSE(currentRoom);
                }
            }, 5000);
        };
    }
    
    function updateConnectionStatus(status) {
        const statusElement = document.getElementById('onlineStatus');
        if (statusElement) {
            statusElement.className = `badge me-2 connection-status ${status}`;
            if (status === 'connected') {
                statusElement.classList.add('bg-success');
                statusElement.innerHTML = '<i data-feather="circle" style="width: 12px; height: 12px;"></i> En línea';
            } else {
                statusElement.classList.add('bg-danger');
                statusElement.innerHTML = '<i data-feather="circle" style="width: 12px; height: 12px;"></i> Desconectado';
            }
            feather.replace();
        }
    }
    
    function loadMessages(roomName, silent = false) {
        if (!silent) {
            showLoadingInChat();
        }
        
        fetch(`/chat/${roomName}/messages`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayMessages(data.messages);
                    // Actualizar último ID de mensaje
                    if (data.messages.length > 0) {
                        lastMessageId = Math.max(...data.messages.map(m => m.id));
                    }
                } else {
                    console.error('Error loading messages:', data.error);
                    if (!silent) {
                        showErrorInChat('Error al cargar los mensajes');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (!silent) {
                    showErrorInChat('Error de conexión');
                }
            });
    }
    
    function displayMessages(messages) {
        const container = document.getElementById('chatMessages');
        container.innerHTML = '';
        
        if (messages.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted p-5">
                    <i data-feather="message-square" style="width: 48px; height: 48px;" class="mb-3"></i>
                    <p class="mb-0">No hay mensajes aún. ¡Sé el primero en escribir!</p>
                </div>
            `;
            feather.replace();
            return;
        }
        
        messages.forEach(message => {
            const messageElement = createMessageElement(message);
            container.appendChild(messageElement);
        });
        
        // Scroll to bottom
        container.scrollTop = container.scrollHeight;
        feather.replace();
    }
    
    function addMessageToChat(message) {
        const container = document.getElementById('chatMessages');
        
        // Remover placeholder si existe
        const placeholder = container.querySelector('.text-center');
        if (placeholder) {
            placeholder.remove();
        }
        
        const messageElement = createMessageElement(message);
        container.appendChild(messageElement);
        
        // Scroll to bottom
        container.scrollTop = container.scrollHeight;
        feather.replace();
    }
    
    function createMessageElement(message) {
        const messageDiv = document.createElement('div');
        const isOwn = message.user_id == currentUser.id;
        messageDiv.className = `message ${isOwn ? 'sent' : 'received'}`;
        
        const contentHtml = `
            ${!isOwn ? `<div class="message-author">${escapeHtml(message.user_name || 'Usuario')}</div>` : ''}
            <div class="message-content">
                ${escapeHtml(message.message)}
            </div>
            <div class="message-time">
                ${formatMessageTime(message.created_at)}
            </div>
        `;
        
        messageDiv.innerHTML = contentHtml;
        return messageDiv;
    }
    
    function sendMessage() {
        const input = document.getElementById('messageInput');
        const message = input.value.trim();
        
        if (!message || !currentRoom) return;
        
        // Deshabilitar input
        input.disabled = true;
        
        // Crear mensaje temporal para mostrar inmediatamente
        const tempMessage = {
            id: Date.now(),
            message: message,
            user_id: currentUser.id,
            user_name: currentUser.name,
            created_at: new Date().toISOString()
        };
        
        // Agregar mensaje inmediatamente (feedback visual)
        addMessageToChat(tempMessage);
        
        // Enviar mensaje al servidor
        fetch('/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                message: message,
                room: currentRoom,
                chat_type: 'public'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                lastMessageId = Math.max(lastMessageId, data.chat.id);
            } else {
                alert('Error al enviar el mensaje');
                // Remover mensaje temporal si falló
                const tempElements = document.querySelectorAll(`[data-temp-id="${tempMessage.id}"]`);
                tempElements.forEach(el => el.remove());
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            alert('Error al enviar el mensaje');
            // Remover mensaje temporal si falló
            const tempElements = document.querySelectorAll(`[data-temp-id="${tempMessage.id}"]`);
            tempElements.forEach(el => el.remove());
        })
        .finally(() => {
            input.disabled = false;
            input.focus();
        });
    }
    
    function showLoadingInChat() {
        document.getElementById('chatMessages').innerHTML = `
            <div class="text-center text-muted p-5">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Cargando mensajes...
            </div>
        `;
    }
    
    function showErrorInChat(errorMessage) {
        document.getElementById('chatMessages').innerHTML = `
            <div class="text-center text-danger p-5">
                <i data-feather="alert-circle" style="width: 48px; height: 48px;" class="mb-3"></i>
                <p class="mb-0">${errorMessage}</p>
            </div>
        `;
        feather.replace();
    }
    
    function formatMessageTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays === 1) {
            return 'Hoy ' + date.toLocaleTimeString('es-ES', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        } else if (diffDays === 2) {
            return 'Ayer ' + date.toLocaleTimeString('es-ES', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        } else {
            return date.toLocaleDateString('es-ES') + ' ' + 
                   date.toLocaleTimeString('es-ES', { 
                       hour: '2-digit', 
                       minute: '2-digit' 
                   });
        }
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (eventSource) {
            eventSource.close();
        }
    });
</script>
@endsection