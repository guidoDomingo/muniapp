@extends('layouts.app')

@section('title', 'Chat en tiempo real')

@push('styles')
<style>
    .chat-container {
        display: flex;
        flex-direction: column;
        height: 70vh;
        background-color: #f5f5f5;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .chat-header {
        background-color: #007bff;
        color: white;
        padding: 10px 15px;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
    }
    
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 15px;
    }
    
    .message {
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
    }
    
    .message-content {
        max-width: 80%;
        padding: 10px 15px;
        border-radius: 18px;
        position: relative;
    }
    
    .message.sent {
        align-items: flex-end;
    }
    
    .message.received {
        align-items: flex-start;
    }
    
    .message.sent .message-content {
        background-color: #dcf8c6;
    }
    
    .message.received .message-content {
        background-color: white;
    }
    
    .message-info {
        font-size: 0.75rem;
        color: #888;
        margin-top: 3px;
    }
    
    .chat-input {
        padding: 15px;
        background-color: white;
        border-top: 1px solid #e0e0e0;
        display: flex;
    }
    
    .chat-input input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 30px;
        margin-right: 10px;
    }
    
    .chat-input button {
        border: none;
        background-color: #007bff;
        color: white;
        border-radius: 30px;
        padding: 10px 20px;
        cursor: pointer;
    }
    
    .chat-input button:hover {
        background-color: #0069d9;
    }
    
    .online-users {
        color: #d4edda;
        font-size: 0.85rem;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Chat en tiempo real</div>

                <div class="card-body p-0">
                    <div class="chat-container">
                        <div class="chat-header">
                            <span>Sala General</span>
                            <span class="online-users" id="online-count">Conectados: 0</span>
                        </div>
                        
                        <div class="chat-messages" id="chat-messages">
                            @foreach($messages as $message)
                                <div class="message {{ $message->user_id == auth()->id() ? 'sent' : 'received' }}">
                                    <div class="message-content">
                                        <strong>{{ $message->user->name }}</strong>
                                        <p>{{ $message->message }}</p>
                                    </div>
                                    <div class="message-info">
                                        {{ $message->created_at->format('H:i') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="chat-input">
                            <input type="text" id="message-input" placeholder="Escribe un mensaje..." autocomplete="off">
                            <button type="button" id="send-button">Enviar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
    // Backup Laravel Echo implementation in case the Vite bundled version doesn't load
    if (typeof window.Echo === 'undefined') {
        console.log('Initializing Echo from view');
        
        // Initialize Pusher
        window.Pusher = Pusher;
        
        // Initialize Echo
        window.Echo = new (function() {
            this.channel = function(channelName) {
                const pusher = new Pusher('{{ env('REVERB_APP_KEY', 'muniapp_key') }}', {
                    wsHost: '{{ env('REVERB_HOST', '127.0.0.1') }}',
                    wsPort: {{ env('REVERB_PORT', 8080) }},
                    forceTLS: false,
                    disableStats: true,
                    enabledTransports: ['ws', 'wss'],
                    cluster: 'mt1'
                });
                
                const channel = pusher.subscribe(channelName);
                
                return {
                    listen: function(eventName, callback) {
                        channel.bind(eventName, callback);
                        return this;
                    }
                };
            };
        })();
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const messagesContainer = document.getElementById('chat-messages');
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('send-button');
        const currentUserId = {{ auth()->id() }};
        
        // Scroll to bottom of messages
        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        // Initial scroll
        scrollToBottom();
        
        // Add event listeners
        sendButton.addEventListener('click', sendMessage);
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
        
        function sendMessage() {
            const message = messageInput.value.trim();
            
            if (!message) return;
            
            // Clear input
            messageInput.value = '';
            
            // Send message to server
            fetch('{{ route('chat.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    message: message,
                    room: 'general'
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Message sent successfully:', data);
                // Append the sent message (this one will also come back through Echo)
                addMessageToChat({
                    id: data.id,
                    user: {
                        id: data.user_id || {{ auth()->id() }},
                        name: data.user ? data.user.name : '{{ auth()->user()->name }}'
                    },
                    message: data.message,
                    created_at: 'ahora'
                }, true);
            })
            .catch(error => {
                console.error('Error sending message:', error);
                alert('Error sending message: ' + error.message);
            });
        }
        
        function addMessageToChat(message, isSent) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
            
            const messageContent = document.createElement('div');
            messageContent.className = 'message-content';
            
            const nameElement = document.createElement('strong');
            nameElement.textContent = message.user.name;
            
            const textElement = document.createElement('p');
            textElement.textContent = message.message;
            
            const infoElement = document.createElement('div');
            infoElement.className = 'message-info';
            infoElement.textContent = message.created_at;
            
            messageContent.appendChild(nameElement);
            messageContent.appendChild(textElement);
            
            messageDiv.appendChild(messageContent);
            messageDiv.appendChild(infoElement);
            
            messagesContainer.appendChild(messageDiv);
            
            scrollToBottom();
        }
        
        // Set up Laravel Echo
        window.addEventListener('load', function() {
            console.log('Initializing Echo listeners...');
            
            function setupEchoListeners() {
                try {
                    if (typeof window.Echo !== 'undefined') {
                        console.log('Echo found, setting up listeners');
                        
                        window.Echo.channel('chat.general')
                            .listen('.message.sent', (data) => {
                                console.log('Received message via Echo:', data);
                                // Only add messages from other users (our own messages are added manually)
                                if (data.user && data.user.id !== currentUserId) {
                                    addMessageToChat(data, false);
                                }
                            });
                        
                        console.log('Echo is listening on chat.general channel');
                        return true;
                    } else {
                        console.error('Laravel Echo is not available');
                        return false;
                    }
                } catch (error) {
                    console.error('Error setting up Echo listeners:', error);
                    return false;
                }
            }
            
            // Try to set up listeners right away
            let success = setupEchoListeners();
            
            // If that fails, try again after a short delay
            if (!success) {
                setTimeout(() => {
                    console.log('Retrying Echo setup...');
                    setupEchoListeners();
                }, 2000);
            }
        });
    });
</script>
@endpush
