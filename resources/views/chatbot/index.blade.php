@extends('muni')

@section('content')
<div class="container">
    <div class="row">
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
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Preguntas Frecuentes</div>
                <div class="card-body">
                    @if(isset($preguntasFrecuentes) && count($preguntasFrecuentes) > 0)
                        <div class="accordion" id="accordionFaqs">
                            @foreach($preguntasFrecuentes as $index => $faq)
                                <div class="accordion-item faq-item" data-pregunta="{{ $faq->pregunta }}">
                                    <h2 class="accordion-header" id="heading{{ $index }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#collapse{{ $index }}" aria-expanded="false" 
                                                aria-controls="collapse{{ $index }}">
                                            {{ $faq->pregunta }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $index }}" class="accordion-collapse collapse" 
                                         aria-labelledby="heading{{ $index }}" data-bs-parent="#accordionFaqs">
                                        <div class="accordion-body">
                                            {{ $faq->respuesta }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p>No hay preguntas frecuentes disponibles.</p>
                    @endif
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
    
    /* Estilos para las preguntas frecuentes */
    .accordion-item {
        margin-bottom: 8px;
        border: 1px solid rgba(0,0,0,.125);
        border-radius: 0.25rem;
    }
    .accordion-button {
        padding: 10px 15px;
        font-size: 14px;
        color: #333;
        background-color: #f8f9fa;
        border: none;
        text-align: left;
        transition: all 0.3s ease;
    }
    .accordion-button:not(.collapsed) {
        color: #007bff;
        background-color: #e7f1ff;
        box-shadow: none;
    }
    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0,123,255,.25);
    }
    .accordion-body {
        padding: 15px;
        font-size: 14px;
        color: #555;
    }
    .faq-item {
        cursor: pointer;
    }
    .faq-item:hover .accordion-button {
        background-color: #e7f1ff;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatForm = document.getElementById('chatForm');
        const userQuestion = document.getElementById('userQuestion');
        const chatMessages = document.getElementById('chatMessages');
        const faqItems = document.querySelectorAll('.faq-item');

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

        // Función para procesar una pregunta
        function procesarPregunta(question) {
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
        }

        // Manejar el envío del formulario
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const question = userQuestion.value.trim();
            procesarPregunta(question);
        });
        
        // Manejar clics en las preguntas frecuentes
        faqItems.forEach(function(item) {
            const pregunta = item.getAttribute('data-pregunta');
            const boton = item.querySelector('.accordion-button');
            
            boton.addEventListener('click', function() {
                // También enviar la pregunta al chat cuando se hace clic en ella
                setTimeout(() => {
                    if (!boton.classList.contains('collapsed')) {
                        procesarPregunta(pregunta);
                    }
                }, 100);
            });
        });
    });
</script>
@endsection
