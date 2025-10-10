<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;

class SSEController extends Controller
{
    /**
     * Stream de eventos del servidor para chat en tiempo real
     */
    public function chatStream(Request $request, $room)
    {
        $response = new StreamedResponse();
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no'); // Nginx
        
        $response->setCallback(function () use ($room, $request) {
            $lastId = $request->get('lastId', 0);
            $chatType = $request->get('type', 'public');
            $sentIds = [];
            
            // Función para enviar datos SSE
            $sendSSE = function($data, $event = 'message') {
                echo "event: {$event}\n";
                echo "data: " . json_encode($data) . "\n\n";
                
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            };
            
            // Enviar evento de conexión
            $sendSSE(['status' => 'connected', 'room' => $room], 'connected');
            
            // Loop infinito para mantener la conexión
            while (true) {
                // Verificar si la conexión sigue activa
                if (connection_aborted()) {
                    break;
                }
                
                try {
                    // Buscar nuevos mensajes
                    $newMessages = Chat::with('user')
                        ->where('room', $room)
                        ->where('chat_type', $chatType)
                        ->where('id', '>', $lastId)
                        ->whereNotIn('id', $sentIds)
                        ->orderBy('created_at', 'asc')
                        ->get();
                    
                    foreach ($newMessages as $message) {
                        if (!in_array($message->id, $sentIds)) {
                            $sendSSE([
                                'id' => $message->id,
                                'message' => $message->message,
                                'user_id' => $message->user_id,
                                'user_name' => $message->user->name ?? 'Usuario',
                                'created_at' => $message->created_at->toISOString(),
                                'room' => $room
                            ], 'new-message');
                            
                            $sentIds[] = $message->id;
                            $lastId = $message->id;
                        }
                    }
                    
                    // Limpiar array de IDs enviados cada 100 mensajes para evitar memory leak
                    if (count($sentIds) > 100) {
                        $sentIds = array_slice($sentIds, -50);
                    }
                    
                    // Enviar heartbeat cada 30 segundos
                    static $lastHeartbeat = 0;
                    if (time() - $lastHeartbeat > 30) {
                        $sendSSE(['timestamp' => time()], 'heartbeat');
                        $lastHeartbeat = time();
                    }
                    
                } catch (\Exception $e) {
                    Log::error('SSE Error: ' . $e->getMessage());
                    $sendSSE(['error' => 'Error en el servidor'], 'error');
                }
                
                // Esperar 1 segundo antes de la siguiente verificación
                sleep(1);
            }
        });
        
        return $response;
    }
    
    /**
     * Endpoint para verificar el estado del SSE
     */
    public function status()
    {
        return response()->json([
            'status' => 'active',
            'timestamp' => now(),
            'server' => 'SSE Chat Server'
        ]);
    }
}
