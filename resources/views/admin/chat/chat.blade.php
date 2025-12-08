@extends('layouts.admin')

@section('title', 'Chat en Tiempo Real - Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Chat en Tiempo Real</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Chat</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row h-100">
            <!-- Sidebar de conversaciones -->
            <div class="col-md-4 col-lg-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-comments mr-2"></i>
                            Conversaciones Activas
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" id="refreshRooms">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0" style="max-height: 500px; overflow-y: auto;">
                        @forelse($rooms as $room)
                        <div class="room-item p-3 border-bottom cursor-pointer" 
                             data-room="{{ $room['room'] }}" 
                             data-display="{{ $room['display_name'] }}"
                             data-type="{{ $room['chat_type'] }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $room['display_name'] }}</h6>
                                    <small class="text-muted">
                                        <i class="fas fa-comment mr-1"></i>
                                        {{ $room['message_count'] }} mensajes
                                    </small>
                                </div>
                                <div class="text-right">
                                    <small class="text-muted d-block">
                                        {{ \Carbon\Carbon::parse($room['last_message'])->diffForHumans() }}
                                    </small>
                                    <span class="badge badge-{{ $room['chat_type'] === 'solicitud' ? 'primary' : 'secondary' }} badge-sm">
                                        {{ ucfirst($room['chat_type']) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-comments fa-3x mb-3"></i>
                            <p class="mb-0">No hay conversaciones activas</p>
                        </div>
                        @endforelse
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.chat.index') }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-cog mr-1"></i>
                            Moderación de Chat
                        </a>
                    </div>
                </div>
            </div>

            <!-- Área de chat principal -->
            <div class="col-md-8 col-lg-9">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0" id="chatTitle">
                                <i class="fas fa-comment-dots mr-2"></i>
                                Selecciona una conversación
                            </h5>
                            <span id="onlineStatus" class="badge badge-secondary ml-2" style="display: none;">
                                <i class="fas fa-circle mr-1" style="font-size: 8px;"></i> Desconectado
                            </span>
                        </div>
                        <div class="chat-actions" style="display: none;">
                            <button class="btn btn-sm btn-outline-secondary" id="refreshChat">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        <!-- Placeholder inicial -->
                        <div id="chatPlaceholder" class="d-flex flex-column justify-content-center align-items-center text-muted" style="height: 100px;">
                            <i class="fas fa-comments fa-4x mb-3 text-muted"></i>
                            <h5>Selecciona una conversación para comenzar</h5>
                            <!-- <p class="text-center">Elige una conversación del panel izquierdo para ver los mensajes y responder a los ciudadanos.</p> -->
                        </div>
                        
                        <!-- Área de mensajes -->
                        <div id="chatMessages" class="chat-messages-container" style="display: none; height: 400px; overflow-y: auto; background-color: #f8f9fa;">
                            <!-- Los mensajes se cargarán aquí dinámicamente -->
                        </div>
                    </div>
                    
                    <!-- Formulario de envío de mensajes -->
                    <div class="card-footer" id="messageForm" style="display: none;">
                        <form id="sendMessageForm" class="d-flex">
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="messageInput" 
                                       placeholder="Escribe tu respuesta como administrador..."
                                       autocomplete="off">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-paper-plane"></i>
                                        Enviar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    /* Chat Container Styles */
    .chat-messages-container {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
        padding: 0.5rem 0;
    }
    
    /* Message Styles */
    .message {
        margin: 0.75rem 1rem;
        display: flex;
        flex-direction: column;
        animation: fadeInUp 0.3s ease;
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
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
    }
    
    .message-content:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .message.sent .message-content {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
    }
    
    .message.received .message-content {
        background-color: white;
        border: 1px solid #dee2e6;
        color: #495057;
    }
    
    .message-time {
        font-size: 0.75rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }
    
    .message-author {
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
        color: #495057;
    }
    
    /* Room Item Styles */
    .room-item {
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 8px;
        margin: 0.25rem;
    }
    
    .room-item:hover {
        background: linear-gradient(135deg, rgba(0, 123, 255, 0.1), rgba(0, 123, 255, 0.05));
        transform: translateX(4px);
    }
    
    .room-item.active {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }
    
    .room-item.active .text-muted {
        color: rgba(255, 255, 255, 0.8) !important;
    }
    
    /* Connection Status */
    .connection-status {
        position: relative;
    }
    
    .connection-status.connected .fas.fa-circle {
        color: #28a745;
        animation: pulse 2s infinite;
    }
    
    .connection-status.disconnected .fas.fa-circle {
        color: #dc3545;
    }
    
    /* Card Enhancements */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px 12px 0 0 !important;
        border: none;
    }
    
    .card-footer {
        border-radius: 0 0 12px 12px;
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }
    
    /* Form Styles */
    .form-control {
        border-radius: 25px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .btn {
        border-radius: 25px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border: none;
    }
    
    /* Badge Styles */
    .badge {
        border-radius: 10px;
        font-weight: 500;
    }
    
    /* Placeholder Styles */
    #chatPlaceholder {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 12px;
        margin: 1rem;
    }
    
    /* Animations */
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
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
    
    /* Scrollbar Styling */
    .chat-messages-container::-webkit-scrollbar {
        width: 6px;
    }
    
    .chat-messages-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .chat-messages-container::-webkit-scrollbar-thumb {
        background: #007bff;
        border-radius: 10px;
    }
    
    .chat-messages-container::-webkit-scrollbar-thumb:hover {
        background: #0056b3;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .message-content {
            max-width: 85%;
        }
        
        .room-item:hover {
            transform: none;
        }
        
        .room-item.active {
            transform: none;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Variables globales
    let currentRoom = null;
    let currentRoomType = 'public'; // Agregar esta variable
    let currentUser = @json(auth()->user());
    let eventSource = null;
    let lastMessageId = 0;
    
    document.addEventListener('DOMContentLoaded', function() {
        setupEventListeners();
        console.log('Admin Chat initialized - Debug Mode');
        console.log('Current user:', currentUser);
        
        // Detectar parámetros de URL para autoseleccionar sala
        const urlParams = new URLSearchParams(window.location.search);
        const urlRoom = urlParams.get('room');
        const urlType = urlParams.get('type');
        const urlSolicitudId = urlParams.get('solicitud_id');
        
        if (urlRoom && urlType === 'solicitud') {
            console.log('Auto-selecting room from URL:', urlRoom);
            currentRoomType = urlType; // Establecer el tipo correcto
            // Buscar y hacer clic en la sala correspondiente
            const roomElement = document.querySelector(`[data-room="${urlRoom}"]`);
            if (roomElement) {
                roomElement.click();
            } else {
                // Si no existe la sala en la lista, seleccionarla manualmente
                selectRoom(urlRoom, `Solicitud ${urlRoom}`, 'solicitud');
            }
        }
    });
    
    function setupEventListeners() {
        console.log('Setting up event listeners...');
        
        // Selección de sala
        document.querySelectorAll('.room-item').forEach(item => {
            console.log('Adding click listener to room:', item.dataset.room);
            item.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Room clicked:', this.dataset.room);
                const roomName = this.dataset.room;
                const displayName = this.dataset.display;
                const roomType = this.dataset.type;
                selectRoom(roomName, displayName, roomType);
            });
        });
        
        // Envío de mensajes
        const messageForm = document.getElementById('sendMessageForm');
        if (messageForm) {
            console.log('Message form found, adding submit listener');
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Form submitted');
                sendMessage();
            });
        } else {
            console.error('Message form not found!');
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
        
        // Refresh rooms
        const refreshRoomsBtn = document.getElementById('refreshRooms');
        if (refreshRoomsBtn) {
            refreshRoomsBtn.addEventListener('click', function() {
                location.reload();
            });
        }
    }
    
    function selectRoom(roomName, displayName, roomType) {
        console.log('Selecting room:', roomName, displayName, roomType);
        
        // Cerrar conexión SSE anterior
        if (eventSource) {
            eventSource.close();
            updateConnectionStatus('disconnected');
        }
        
        // Actualizar UI
        currentRoom = roomName;
        currentRoomType = roomType; // Agregar esta línea
        
        // Actualizar título
        const chatTitle = document.getElementById('chatTitle');
        if (chatTitle) {
            chatTitle.innerHTML = `<i class="fas fa-comment-dots mr-2"></i>${displayName}`;
            console.log('Title updated');
        }
        
        // Mostrar controles de chat
        const messageForm = document.getElementById('messageForm');
        const chatMessages = document.getElementById('chatMessages');
        const chatPlaceholder = document.getElementById('chatPlaceholder');
        const chatActions = document.querySelector('.chat-actions');
        
        if (messageForm) {
            messageForm.style.display = 'block';
            console.log('Message form shown');
        }
        if (chatMessages) {
            chatMessages.style.display = 'block';
            console.log('Chat messages shown');
        }
        if (chatPlaceholder) {
            chatPlaceholder.style.display = 'none';
            console.log('Placeholder hidden');
        }
        if (chatActions) {
            chatActions.style.display = 'block';
            console.log('Chat actions shown');
        }
        
        // Actualizar salas activas
        document.querySelectorAll('.room-item').forEach(item => {
            item.classList.remove('active');
        });
        const selectedRoom = document.querySelector(`[data-room="${roomName}"]`);
        if (selectedRoom) {
            selectedRoom.classList.add('active');
            console.log('Room marked as active');
        }
        
        // Cargar mensajes iniciales
        loadMessages(roomName);
        
        // Iniciar SSE para tiempo real
        startSSE(roomName);
    }
    
    function startSSE(roomName) {
        console.log('Starting SSE for room:', roomName, 'type:', currentRoomType);
        const url = `/chat/${roomName}/stream?type=${currentRoomType}&lastId=${lastMessageId}`;
        
        console.log('SSE URL:', url);
        
        eventSource = new EventSource(url);
        
        eventSource.onopen = function(event) {
            console.log('SSE Connection opened:', event);
            updateConnectionStatus('connected');
        };
        
        eventSource.addEventListener('connected', function(event) {
            const data = JSON.parse(event.data);
            console.log('SSE Connected event:', data);
            updateConnectionStatus('connected');
        });
        
        eventSource.addEventListener('new-message', function(event) {
            const message = JSON.parse(event.data);
            console.log('New message received:', message);
            
            // Solo agregar el mensaje si NO es del usuario actual (evitar duplicados)
            if (message.user_id != currentUser.id) {
                addMessageToChat(message);
                lastMessageId = Math.max(lastMessageId, message.id);
            } else {
                // Si es nuestro mensaje, solo actualizar el lastMessageId
                lastMessageId = Math.max(lastMessageId, message.id);
                console.log('Own message ignored to prevent duplication');
            }
        });
        
        eventSource.addEventListener('heartbeat', function(event) {
            console.log('SSE Heartbeat received');
        });
        
        eventSource.onerror = function(event) {
            console.error('SSE Error:', event);
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
        console.log('Connection status updated:', status);
        const statusElement = document.getElementById('onlineStatus');
        if (statusElement) {
            statusElement.style.display = 'inline-block';
            statusElement.className = `badge ml-2 connection-status ${status}`;
            if (status === 'connected') {
                statusElement.classList.add('badge-success');
                statusElement.innerHTML = '<i class="fas fa-circle mr-1" style="font-size: 8px;"></i> En línea';
            } else {
                statusElement.classList.add('badge-danger');
                statusElement.innerHTML = '<i class="fas fa-circle mr-1" style="font-size: 8px;"></i> Desconectado';
            }
        }
    }
    
    function loadMessages(roomName, silent = false) {
        console.log('Loading messages for room:', roomName, 'type:', currentRoomType);
        
        if (!silent) {
            showLoadingInChat();
        }
        
        const url = `/chat/${roomName}/messages?type=${currentRoomType}`;
        console.log('Messages URL:', url);
        
        fetch(url)
            .then(response => {
                console.log('Messages response:', response);
                return response.json();
            })
            .then(data => {
                console.log('Messages data:', data);
                if (data.success) {
                    displayMessages(data.messages);
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
                console.error('Fetch error:', error);
                if (!silent) {
                    showErrorInChat('Error de conexión');
                }
            });
    }
    
    function displayMessages(messages) {
        console.log('Displaying messages:', messages);
        const container = document.getElementById('chatMessages');
        if (!container) {
            console.error('Chat messages container not found!');
            return;
        }
        
        container.innerHTML = '';
        
        if (messages.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted p-5">
                    <i class="fas fa-comment-slash fa-3x mb-3"></i>
                    <p class="mb-0">No hay mensajes en esta conversación</p>
                </div>
            `;
            return;
        }
        
        messages.forEach(message => {
            const messageElement = createMessageElement(message);
            container.appendChild(messageElement);
        });
        
        // Scroll to bottom
        container.scrollTop = container.scrollHeight;
    }
    
    function addMessageToChat(message) {
        console.log('Adding message to chat:', message);
        const container = document.getElementById('chatMessages');
        if (!container) {
            console.error('Chat container not found!');
            return null;
        }
        
        // Remover placeholder si existe
        const placeholder = container.querySelector('.text-center');
        if (placeholder) {
            placeholder.remove();
        }
        
        const messageElement = createMessageElement(message);
        container.appendChild(messageElement);
        
        // Scroll to bottom
        container.scrollTop = container.scrollHeight;
        
        return messageElement;
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
        console.log('Sending message...');
        const input = document.getElementById('messageInput');
        if (!input) {
            console.error('Message input not found!');
            return;
        }
        
        const message = input.value.trim();
        console.log('Message content:', message);
        
        if (!message || !currentRoom) {
            console.log('No message or room');
            return;
        }
        
        // Deshabilitar input
        input.disabled = true;
        
        // Crear mensaje temporal para mostrar inmediatamente
        const tempMessage = {
            id: 'temp-' + Date.now(), // ID temporal único
            message: message,
            user_id: currentUser.id,
            user_name: currentUser.name + ' (Admin)',
            created_at: new Date().toISOString(),
            isTemporary: true
        };
        
        // Agregar mensaje inmediatamente
        const tempElement = addMessageToChat(tempMessage);
        
        // Enviar al servidor
        fetch('/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                message: message,
                room: currentRoom,
                chat_type: currentRoomType
            })
        })
        .then(response => {
            console.log('Send response:', response);
            return response.json();
        })
        .then(data => {
            console.log('Send data:', data);
            if (data.success) {
                input.value = '';
                lastMessageId = Math.max(lastMessageId, data.chat.id);
                
                // Remover mensaje temporal si existe
                if (tempElement && tempElement.parentNode) {
                    tempElement.remove();
                }
                
                // Agregar mensaje real del servidor
                addMessageToChat({
                    id: data.chat.id,
                    message: data.chat.message,
                    user_id: data.chat.user_id,
                    user_name: currentUser.name + ' (Admin)',
                    created_at: data.chat.created_at
                });
            } else {
                alert('Error al enviar el mensaje');
                // Remover mensaje temporal en caso de error
                if (tempElement && tempElement.parentNode) {
                    tempElement.remove();
                }
            }
        })
        .catch(error => {
            console.error('Send error:', error);
            alert('Error al enviar el mensaje');
            // Remover mensaje temporal en caso de error
            if (tempElement && tempElement.parentNode) {
                tempElement.remove();
            }
        })
        .finally(() => {
            input.disabled = false;
            input.focus();
        });
    }
    
    function showLoadingInChat() {
        const container = document.getElementById('chatMessages');
        if (container) {
            container.innerHTML = `
                <div class="text-center text-muted p-5">
                    <div class="spinner-border spinner-border-sm mr-2" role="status"></div>
                    Cargando mensajes...
                </div>
            `;
        }
    }
    
    function showErrorInChat(errorMessage) {
        const container = document.getElementById('chatMessages');
        if (container) {
            container.innerHTML = `
                <div class="text-center text-danger p-5">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <p class="mb-0">${errorMessage}</p>
                </div>
            `;
        }
    }
    
    function formatMessageTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString('es-ES', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
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
@endpush