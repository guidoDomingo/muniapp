@extends('layouts.admin')

@section('title', 'Moderación de Chat - MuniApp Admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Moderación de Chat</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Moderación Chat</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Statistics Cards -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $stats['total_messages'] ?? 0 }}</h3>
                        <p>Total Mensajes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-comments"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $stats['active_rooms'] ?? 0 }}</h3>
                        <p>Salas Activas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-door-open"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $stats['users_chatting'] ?? 0 }}</h3>
                        <p>Usuarios Activos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $stats['messages_today'] ?? 0 }}</h3>
                        <p>Mensajes Hoy</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.chat.index') }}">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="search">Buscar</label>
                                        <input type="text" name="search" id="search" class="form-control" 
                                               placeholder="Contenido del mensaje..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="room">Sala</label>
                                        <select name="room" id="room" class="form-control">
                                            <option value="">Todas</option>
                                            @foreach($rooms ?? [] as $room)
                                                <option value="{{ $room }}" {{ request('room') == $room ? 'selected' : '' }}>
                                                    {{ ucfirst($room) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="type">Tipo</label>
                                        <select name="type" id="type" class="form-control">
                                            <option value="">Todos</option>
                                            <option value="public" {{ request('type') == 'public' ? 'selected' : '' }}>Público</option>
                                            <option value="private" {{ request('type') == 'private' ? 'selected' : '' }}>Privado</option>
                                            <option value="commission" {{ request('type') == 'commission' ? 'selected' : '' }}>Comisión</option>
                                            <option value="solicitud" {{ request('type') == 'solicitud' ? 'selected' : '' }}>Solicitud</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="user">Usuario</label>
                                        <select name="user" id="user" class="form-control">
                                            <option value="">Todos</option>
                                            @foreach($users ?? [] as $user)
                                                <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-search"></i> Buscar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Messages -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-comments mr-1"></i>
                            Mensajes de Chat
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-warning" onclick="moderateSelected('warn')">
                                <i class="fas fa-exclamation-triangle"></i> Advertir
                            </button>
                            <button type="button" class="btn btn-danger" onclick="moderateSelected('delete')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chat-messages" style="max-height: 600px; overflow-y: auto;">
                            @forelse($chats ?? [] as $chat)
                            <div class="chat-message mb-3 p-3 border rounded" data-chat-id="{{ $chat->id }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex align-items-start">
                                        <input type="checkbox" class="form-check-input chat-checkbox mr-2" 
                                               value="{{ $chat->id }}" style="margin-top: 8px;">
                                        <img src="{{ $chat->user->avatar_url ?? asset('images/default-avatar.png') }}" 
                                             class="img-circle img-size-32 mr-2" alt="Avatar">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <strong>{{ $chat->user->name }}</strong>
                                                <small class="text-muted ml-2">{{ $chat->created_at->format('d/m/Y H:i') }}</small>
                                                <span class="badge badge-{{ $chat->chat_type == 'public' ? 'primary' : ($chat->chat_type == 'commission' ? 'warning' : 'info') }} ml-2">
                                                    {{ ucfirst($chat->chat_type) }}
                                                </span>
                                                <span class="badge badge-secondary ml-1">{{ $chat->room }}</span>
                                            </div>
                                            <div class="message-content">
                                                {{ $chat->message }}
                                            </div>
                                            @if($chat->attachments && count($chat->attachments) > 0)
                                            <div class="attachments mt-2">
                                                @foreach($chat->attachments as $attachment)
                                                <span class="badge badge-info">
                                                    <i class="fas fa-paperclip"></i> {{ $attachment['name'] }}
                                                </span>
                                                @endforeach
                                            </div>
                                            @endif
                                            @if($chat->solicitud)
                                            <div class="mt-2">
                                                <small class="text-info">
                                                    <i class="fas fa-link"></i> Relacionado con: {{ $chat->solicitud->tramite->nombre }}
                                                </small>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#" onclick="moderateMessage({{ $chat->id }}, 'warn')">
                                                <i class="fas fa-exclamation-triangle text-warning"></i> Advertir usuario
                                            </a>
                                            <a class="dropdown-item" href="#" onclick="moderateMessage({{ $chat->id }}, 'hide')">
                                                <i class="fas fa-eye-slash text-info"></i> Ocultar mensaje
                                            </a>
                                            <a class="dropdown-item" href="#" onclick="moderateMessage({{ $chat->id }}, 'delete')">
                                                <i class="fas fa-trash text-danger"></i> Eliminar mensaje
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4">
                                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No se encontraron mensajes</h5>
                                <p class="text-muted">No hay mensajes que coincidan con los filtros aplicados</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    @if(isset($chats) && $chats->hasPages())
                    <div class="card-footer">
                        {{ $chats->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Moderation Modal -->
<div class="modal fade" id="moderationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Moderar Mensaje(s)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="reason">Razón (opcional)</label>
                    <textarea class="form-control" id="reason" rows="3" 
                              placeholder="Describe la razón de la moderación..."></textarea>
                </div>
                <p class="text-muted">
                    <small>Esta acción se aplicará a los mensajes seleccionados.</small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmModeration">Confirmar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentAction = '';
let selectedChatIds = [];

function moderateSelected(action) {
    selectedChatIds = [];
    $('.chat-checkbox:checked').each(function() {
        selectedChatIds.push($(this).val());
    });
    
    if (selectedChatIds.length === 0) {
        alert('Selecciona al menos un mensaje para moderar');
        return;
    }
    
    currentAction = action;
    const modal = new bootstrap.Modal(document.getElementById('moderationModal'));
    modal.show();
}

function moderateMessage(chatId, action) {
    selectedChatIds = [chatId];
    currentAction = action;
    const modal = new bootstrap.Modal(document.getElementById('moderationModal'));
    modal.show();
}

$('#confirmModeration').click(function() {
    const reason = $('#reason').val();
    
    $.ajax({
        url: '{{ route("admin.chat.moderate") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            action: currentAction,
            chat_ids: selectedChatIds,
            reason: reason
        },
        success: function(response) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('moderationModal'));
            modal.hide();
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error al procesar la moderación');
        }
    });
});

// Select all checkbox
$('#selectAll').change(function() {
    $('.chat-checkbox').prop('checked', $(this).is(':checked'));
});

// Auto refresh every 30 seconds
setInterval(function() {
    if (!$('.modal').hasClass('show')) {
        location.reload();
    }
}, 30000);
</script>
@endpush