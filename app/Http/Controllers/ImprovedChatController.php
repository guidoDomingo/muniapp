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

class ImprovedChatController extends Controller
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
        
        return view('chat.improved', compact('messages', 'roomName', 'chatType', 'availableRooms', 'solicitud'));
    }
    
    /**
     * Store a newly created message.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'message' => 'required_without:attachment|string|max:1000',
                'room' => 'required|string|max:100',
                'chat_type' => 'required|string|in:public,private,solicitud,commission',
                'parent_id' => 'nullable|exists:chats,id',
                'attachment' => 'nullable|file|max:10240', // 10MB max
            ]);

            $roomName = $request->room;
            $chatType = $request->chat_type;
            
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

            // Enviar evento en tiempo real
            broadcast(new NewChatMessage($chat))->toOthers();

            return response()->json([
                'success' => true,
                'message' => 'Mensaje enviado correctamente',
                'chat' => $chat,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error sending chat message: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el mensaje'
            ], 500);
        }
    }

    /**
     * Get messages for a specific room
     */
    public function getMessages(Request $request, $room): JsonResponse
    {
        $chatType = $request->get('type', 'public');
        $this->authorizeRoom($room, $chatType);
        
        $messages = Chat::with('user')
            ->where('room', $room)
            ->where('chat_type', $chatType)
            ->when($request->has('since'), function($query) use ($request) {
                return $query->where('id', '>', $request->since);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    private function authorizeRoom($roomName, $chatType)
    {
        $user = auth()->user();
        
        switch ($chatType) {
            case 'commission':
                if (!$user->hasAnyRole(['admin', 'commission', 'functionary'])) {
                    abort(403, 'No tienes permisos para acceder a este chat');
                }
                break;
                
            case 'solicitud':
                $solicitud = Solicitud::where('tracking_code', $roomName)->first();
                if (!$solicitud || (!$user->isAdmin() && !$user->isCommission() && $solicitud->user_id !== $user->id)) {
                    abort(403, 'No tienes permisos para acceder a este chat');
                }
                break;
                
            case 'private':
                $room = ChatRoom::where('name', $roomName)->first();
                if (!$room || !$room->isParticipant($user->id)) {
                    abort(403, 'No tienes permisos para acceder a este chat');
                }
                break;
                
            case 'public':
                // Todos los usuarios autenticados pueden acceder
                break;
        }
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

        // Salas de comisión (si tiene permisos)
        if ($user->hasAnyRole(['admin', 'commission', 'functionary'])) {
            $rooms[] = [
                'name' => 'commission',
                'display_name' => 'Chat de Comisión',
                'type' => 'commission',
                'unread_count' => 0,
            ];
        }

        // Chats de solicitudes del usuario
        if ($user->isUser()) {
            $solicitudes = $user->solicitudes()->whereNotNull('tracking_code')->get();
            foreach ($solicitudes as $solicitud) {
                $rooms[] = [
                    'name' => $solicitud->tracking_code,
                    'display_name' => 'Solicitud: ' . $solicitud->tramite->nombre,
                    'type' => 'solicitud',
                    'unread_count' => 0,
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
}