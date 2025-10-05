<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Http\Request;

class AdminChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:moderate_chat');
    }

    public function index(Request $request)
    {
        $query = Chat::with(['user', 'solicitud']);

        if ($request->filled('room')) {
            $query->where('room', $request->room);
        }

        if ($request->filled('type')) {
            $query->where('chat_type', $request->type);
        }

        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        if ($request->filled('search')) {
            $query->where('message', 'like', '%' . $request->search . '%');
        }

        $chats = $query->orderBy('created_at', 'desc')->paginate(50);
        
        // Get available rooms
        $rooms = Chat::select('room')->distinct()->get()->pluck('room');
        $users = User::all();

        // Chat statistics
        $stats = [
            'total_messages' => Chat::count(),
            'active_rooms' => Chat::select('room')->distinct()->count(),
            'users_chatting' => Chat::select('user_id')->distinct()->count(),
            'messages_today' => Chat::whereDate('created_at', today())->count(),
        ];

        return view('admin.chat.index', compact('chats', 'rooms', 'users', 'stats'));
    }

    public function rooms()
    {
        $rooms = ChatRoom::with(['creator', 'participants', 'latestMessage.user'])
            ->withCount('participants')
            ->get();

        return view('admin.chat.rooms', compact('rooms'));
    }

    public function moderate(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,hide,warn',
            'chat_ids' => 'required|array',
            'chat_ids.*' => 'exists:chats,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $chats = Chat::whereIn('id', $request->chat_ids)->get();

        foreach ($chats as $chat) {
            switch ($request->action) {
                case 'delete':
                    $chat->delete();
                    break;
                case 'hide':
                    $chat->update(['is_hidden' => true]);
                    break;
                case 'warn':
                    // TODO: Implement warning notification
                    // $chat->user->notify(new \App\Notifications\ChatWarning($request->reason ?? 'Mensaje inapropiado'));
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Acción de moderación aplicada exitosamente'
        ]);
    }

    public function statistics()
    {
        $stats = [
            'messages_by_day' => Chat::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            
            'messages_by_room' => Chat::selectRaw('room, COUNT(*) as count')
                ->groupBy('room')
                ->orderBy('count', 'desc')
                ->get(),
            
            'most_active_users' => Chat::with('user')
                ->selectRaw('user_id, COUNT(*) as count')
                ->groupBy('user_id')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
        ];

        return view('admin.chat.statistics', compact('stats'));
    }

    public function createRoom(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:chat_rooms',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:public,private,commission',
            'max_participants' => 'nullable|integer|min:2',
        ]);

        $room = ChatRoom::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'created_by' => auth()->id(),
            'is_active' => true,
            'max_participants' => $request->max_participants,
        ]);

        // Add creator as admin
        $room->addParticipant(auth()->id(), 'admin');

        return redirect()->route('admin.chat.rooms')
            ->with('success', 'Sala de chat creada exitosamente.');
    }
}