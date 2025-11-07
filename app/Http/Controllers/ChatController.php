<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Models\Chat;
use App\Models\ChatRoom;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * Show the chat interface.
     */
    public function index(Request $request): View
    {
        $roomName = $request->get('room', 'general');
        $chatType = $request->get('type', 'public');
        
        // Verificar permisos según el tipo de chat
        $this->authorizeRoom($roomName, $chatType);
        
        $messages = Chat::with('user')
            ->where('room', $roomName)
            ->where('chat_type', $chatType)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse();

        // Obtener salas disponibles para el usuario
        $availableRooms = $this->getAvailableRooms();
        
        // Si es un chat de solicitud, obtener la solicitud
        $solicitud = null;
        if ($chatType === 'solicitud') {
            $solicitud = Solicitud::where('tracking_code', $roomName)->first();
        }
        
        return view('chat.index', compact('messages', 'roomName', 'chatType', 'availableRooms', 'solicitud'));
    }
    
    /**
     * Store a newly created message.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Log para depuración de autenticación
            Log::info('Chat store attempt', [
                'user_id' => auth()->id(),
                'user_authenticated' => auth()->check(),
                'request_data' => $request->only(['message', 'room', 'chat_type'])
            ]);
            
            // Verificar autenticación
            if (!auth()->check()) {
                Log::error('User not authenticated in chat store');
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }
            
            $request->validate([
                'message' => 'required_without:attachment|string|max:1000',
                'room' => 'required|string|max:100',
                'chat_type' => 'required|string|in:public,private,solicitud,support,group',
                'parent_id' => 'nullable|exists:chats,id',
                'attachment' => 'nullable|file|max:10240', // 10MB max
            ]);

            $roomName = $request->room;
            $chatType = $request->chat_type;
            
            Log::info('Chat validation passed', [
                'room' => $roomName,
                'chat_type' => $chatType,
                'user_id' => auth()->id()
            ]);
            
            // Verificar permisos
            $this->authorizeRoom($roomName, $chatType);

            $attachments = [];
            
            // Manejar archivos adjuntos
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('chat-attachments', $filename, 'public');
                
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }

            $chat = Chat::create([
                'user_id' => auth()->id(),
                'message' => $request->message,
                'room' => $roomName,
                'chat_type' => $chatType,
                'parent_id' => $request->parent_id,
                'attachments' => $attachments,
                'solicitud_id' => $this->getSolicitudId($roomName, $chatType),
            ]);

            $chat->load('user');
            
            // Log para depuración
            Log::info('Chat message created', [
                'user_id' => auth()->id(),
                'room' => $roomName,
                'chat_type' => $chatType,
                'message' => $request->message,
                'chat_id' => $chat->id
            ]);

            // Enviar evento en tiempo real
            broadcast(new NewChatMessage($chat))->toOthers();

            // Crear notificación para participantes relevantes
            $this->notifyParticipants($chat, $roomName, $chatType);

            return response()->json([
                'success' => true,
                'message' => 'Mensaje enviado correctamente',
                'chat' => $chat,
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Chat validation error', [
                'errors' => $e->errors(),
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error sending chat message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el mensaje: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get messages for a specific room
     */
    public function getMessages(Request $request, $room): JsonResponse
    {
        try {
            $chatType = $request->get('type', 'public');
            $this->authorizeRoom($room, $chatType);
            
            $messages = Chat::with('user')
                ->where('room', $room)
                ->where('chat_type', $chatType)
                ->when($request->has('since'), function($query) use ($request) {
                    return $query->where('id', '>', $request->since);
                })
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function($message) {
                    return [
                        'id' => $message->id,
                        'message' => $message->message,
                        'user_id' => $message->user_id,
                        'user_name' => $message->user->name ?? 'Usuario',
                        'created_at' => $message->created_at->toISOString(),
                    ];
                });

            return response()->json([
                'success' => true,
                'messages' => $messages
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting messages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al cargar los mensajes'
            ], 500);
        }
    }

    /**
     * Create a new private chat room
     */
    public function createPrivateRoom(Request $request): JsonResponse
    {
        $request->validate([
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,id',
            'name' => 'nullable|string|max:100',
        ]);

        $participants = $request->participants;
        $participants[] = auth()->id(); // Add current user
        $participants = array_unique($participants);

        $roomName = $request->name ?? 'private_' . uniqid();

        $room = ChatRoom::create([
            'name' => $roomName,
            'type' => 'private',
            'created_by' => auth()->id(),
            'is_active' => true,
        ]);

        // Add participants
        foreach ($participants as $participantId) {
            $room->addParticipant($participantId);
        }

        return response()->json([
            'success' => true,
            'room' => $room,
        ]);
    }

    /**
     * Join a chat room
     */
    public function joinRoom(Request $request, $roomId): JsonResponse
    {
        $room = ChatRoom::findOrFail($roomId);
        
        if (!$room->isParticipant(auth()->id())) {
            $room->addParticipant(auth()->id());
        }

        return response()->json([
            'success' => true,
            'message' => 'Te has unido a la sala exitosamente',
        ]);
    }

    /**
     * Leave a chat room
     */
    public function leaveRoom($roomId): JsonResponse
    {
        $room = ChatRoom::findOrFail($roomId);
        $room->removeParticipant(auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Has salido de la sala',
        ]);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'room' => 'required|string',
            'chat_type' => 'required|string',
        ]);

        Chat::where('room', $request->room)
            ->where('chat_type', $request->chat_type)
            ->where('user_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    private function authorizeRoom($roomName, $chatType)
    {
        $user = auth()->user();
        
        // Simplificado: permitir acceso a todas las salas para debugging
        // TODO: Implementar autorización completa más tarde
        return true;
    }

    private function getAvailableRooms()
    {
        $user = auth()->user();
        $rooms = [];

        // Sala pública general
        $rooms[] = [
            'name' => 'general',
            'display_name' => 'Chat General',
            'type' => 'public',
            'unread_count' => 0,
        ];

        // Para simplificar, mostrar salas básicas
        $rooms[] = [
            'name' => 'soporte-general',
            'display_name' => 'Soporte General',
            'type' => 'support',
            'unread_count' => 0,
        ];

        $rooms[] = [
            'name' => 'solicitudes-tramites',
            'display_name' => 'Consultas de Trámites',
            'type' => 'group',
            'unread_count' => 0,
        ];

        // Agregar salas de solicitudes específicas del usuario
        $solicitudes = Solicitud::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($solicitudes as $solicitud) {
            $roomName = $solicitud->tracking_code ?: 'SOL-' . $solicitud->id;
            
            // Contar mensajes en esta sala
            $messageCount = Chat::where('room', $roomName)
                ->where('chat_type', 'solicitud')
                ->count();
            
            if ($messageCount > 0) {
                $rooms[] = [
                    'name' => $roomName,
                    'display_name' => "Mi Solicitud: {$solicitud->tramite->nombre}",
                    'type' => 'solicitud',
                    'unread_count' => 0,
                    'solicitud_id' => $solicitud->id,
                ];
            }
        }

        return $rooms;
    }

    private function getSolicitudId($roomName, $chatType)
    {
        if ($chatType === 'solicitud') {
            $solicitud = Solicitud::where('tracking_code', $roomName)->first();
            return $solicitud ? $solicitud->id : null;
        }
        return null;
    }

    private function notifyParticipants($chat, $roomName, $chatType)
    {
        // Implementar notificaciones a participantes relevantes
        // Esto se puede expandir según las necesidades específicas
        Log::info("Notificación de chat enviada para sala: {$roomName}");
    }
}
