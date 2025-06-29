<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ChatController extends Controller
{
    /**
     * Show the chat interface.
     */
    public function index(): View
    {
        $messages = Chat::with('user')
            ->where('room', 'general')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse();
        
        return view('chat.index', compact('messages'));
    }
    
    /**
     * Store a newly created message.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'message' => 'required|string|max:1000',
                'room' => 'sometimes|string|max:50',
            ]);
            
            $chat = Chat::create([
                'user_id' => auth()->id(),
                'message' => $validated['message'],
                'room' => $validated['room'] ?? 'general',
            ]);
            
            $chat->load('user');
            
            // Broadcast the event
            try {
                broadcast(new NewChatMessage($chat))->toOthers();
            } catch (\Exception $e) {
                // Log broadcasting error but continue to return the chat message
                Log::error("Broadcasting error: " . $e->getMessage());
            }
            
            return response()->json($chat, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Get messages for a specific room.
     */
    public function getMessages(Request $request, string $room = 'general'): JsonResponse
    {
        $messages = Chat::with('user')
            ->where('room', $room)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();
        
        return response()->json($messages);
    }
}
