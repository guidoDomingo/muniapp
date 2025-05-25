@extends('muni')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Chatbot de Ayuda</div>

                <div class="card-body">
                    <div class="chatbot-container">
                        <div class="chatbot-messages" id="chatMessages">
                            <div class="message bot">
                                <div class="message-content">
                                    Hola, soy el asistente virtual de MuniApp. ¿En qué puedo ayudarte?
                                </div>
                            </div>
                        </div>
                        <div class="chatbot-input">
                            <form id="chatForm">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="userQuestion" placeholder="Escribe tu pregunta...">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-paper-plane"></i> Enviar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .chatbot-container {
        display: flex;
        flex-direction: column;
        height: 500px;
    }
    .chatbot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        background-color: #f8f9fa;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    .message {
        max-width: 80%;
        padding: 10px 15px;
        border-radius: 15px;
        margin-bottom: 5px;
    }
    .message.user {
        align-self: flex-end;
        background-color: #007bff;
        color: white;
    }
    .message.bot {
        align-self: flex-start;
        background-color: #e9ecef;
        color: #212529;
    }
    .chatbot-input {
        padding: 10px 0;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatForm = document.getElementById('chatForm');
        const userQuestion = document.getElementById('userQuestion');
        const chatMessages = document.getElementById('chatMessages');

        // Función para añadir un mensaje al chat
        function addMessage(content, isUser = false) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isUser ? 'user' : 'bot'}`;
            
            const contentDiv = document.createElement('div');
            contentDiv.className = 'message-content';
            contentDiv.textContent = content;
            
            messageDiv.appendChild(contentDiv);
            chatMessages.appendChild(messageDiv);
            
            // Scroll al final del chat
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Manejar el envío del formulario
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const question = userQuestion.value.trim();
            if (!question) return;
            
            // Añadir la pregunta del usuario al chat
            addMessage(question, true);
            
            // Limpiar el campo de entrada
            userQuestion.value = '';
            
            // Indicador de "escribiendo..."
            const typingDiv = document.createElement('div');
            typingDiv.className = 'message bot typing';
            typingDiv.innerHTML = '<div class="message-content">Escribiendo...</div>';
            chatMessages.appendChild(typingDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            
            // Enviar la pregunta al servidor
            fetch('{{ route("chatbot.procesar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ pregunta: question })
            })
            .then(response => response.json())
            .then(data => {
                // Eliminar el mensaje de "escribiendo..."
                chatMessages.removeChild(typingDiv);
                
                // Añadir la respuesta del chatbot
                addMessage(data.respuesta);
            })
            .catch(error => {
                console.error('Error:', error);
                chatMessages.removeChild(typingDiv);
                addMessage('Lo siento, ha ocurrido un error al procesar tu pregunta. Por favor, intenta de nuevo más tarde.');
            });
        });
    });
</script>
@endsection
