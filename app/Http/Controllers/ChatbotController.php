<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    /**
     * Muestra la interfaz del chatbot
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Intentar obtener las preguntas frecuentes de la base de datos
            $preguntasFrecuentes = \App\Models\Faq::where('activo', true)
                ->orderBy('orden', 'asc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            // Si hay algún error, proporcionar preguntas frecuentes predeterminadas
            $preguntasFrecuentes = $this->getDefaultFaqs();
        }
        
        // Si no hay preguntas en la base de datos, usar las predeterminadas
        if ($preguntasFrecuentes->isEmpty()) {
            $preguntasFrecuentes = $this->getDefaultFaqs();
        }
            
        return view('chatbot.index', compact('preguntasFrecuentes'));
    }
    
    /**
     * Proporciona preguntas frecuentes por defecto cuando la base de datos no está disponible
     *
     * @return \Illuminate\Support\Collection
     */
    private function getDefaultFaqs()
    {
        $faqs = [
            [
                'id' => 1,
                'pregunta' => '¿Cómo puedo iniciar un nuevo trámite?',
                'respuesta' => 'Para iniciar un nuevo trámite, debes ir a la sección "Trámites" en el menú lateral, seleccionar el tipo de trámite que necesitas y hacer clic en "Iniciar Trámite". Sigue las instrucciones para completar el formulario correspondiente.',
                'categoria' => 'Trámites',
                'orden' => 1,
                'activo' => true
            ],
            [
                'id' => 2,
                'pregunta' => '¿Cómo puedo verificar el estado de mi solicitud?',
                'respuesta' => 'Para verificar el estado de tu solicitud, ve a la sección "Solicitudes" en el menú lateral. Allí encontrarás una lista de todas tus solicitudes con su estado actual (pendiente, aprobado o rechazado).',
                'categoria' => 'Solicitudes',
                'orden' => 2,
                'activo' => true
            ],
            [
                'id' => 3,
                'pregunta' => '¿Qué documentos necesito para hacer un trámite?',
                'respuesta' => 'Los documentos requeridos varían según el tipo de trámite. Al seleccionar un trámite específico, se te informará qué documentos necesitas adjuntar. Generalmente, se requiere tu documento de identidad vigente y, dependiendo del trámite, fotografías o documentación adicional.',
                'categoria' => 'Documentación',
                'orden' => 3,
                'activo' => true
            ],
            [
                'id' => 4,
                'pregunta' => '¿Cuánto tiempo tarda en procesarse mi solicitud?',
                'respuesta' => 'El tiempo de procesamiento varía según el tipo de trámite y la carga actual de trabajo. Por lo general, las solicitudes se procesan en un plazo de 3 a 5 días hábiles. Puedes verificar el estado de tu solicitud en cualquier momento en la sección "Solicitudes".',
                'categoria' => 'Proceso',
                'orden' => 5,
                'activo' => true
            ],
            [
                'id' => 5,
                'pregunta' => '¿Qué hago si mi solicitud fue rechazada?',
                'respuesta' => 'Si tu solicitud fue rechazada, podrás ver el motivo del rechazo en los comentarios de la solicitud. Puedes corregir los errores señalados y volver a presentar una nueva solicitud.',
                'categoria' => 'Solicitudes',
                'orden' => 6,
                'activo' => true
            ]
        ];
        
        return collect($faqs)->map(function($faq) {
            return (object) $faq;
        });
    }

    /**
     * Procesa una pregunta y devuelve una respuesta
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function procesarPregunta(Request $request)
    {
        $request->validate([
            'pregunta' => 'required|string'
        ]);

        $pregunta = strtolower($request->input('pregunta'));
        
        try {
            // Intentar buscar en la base de datos preguntas similares
            $faqs = \App\Models\Faq::where('activo', true)->get();
        } catch (\Exception $e) {
            // Si hay algún error, usar preguntas predeterminadas
            $faqs = $this->getDefaultFaqs();
        }
        
        // Si no hay preguntas en la base de datos, usar las predeterminadas
        if ($faqs->isEmpty()) {
            $faqs = $this->getDefaultFaqs();
        }
        
        $mejorCoincidencia = null;
        $mejorPuntuacion = 0;
        
        foreach ($faqs as $faq) {
            $puntuacion = $this->calcularSimilitud($pregunta, strtolower($faq->pregunta));
            if ($puntuacion > $mejorPuntuacion && $puntuacion > 0.6) { // Umbral de similitud del 60%
                $mejorPuntuacion = $puntuacion;
                $mejorCoincidencia = $faq;
            }
        }
        
        if ($mejorCoincidencia) {
            return response()->json([
                'respuesta' => $mejorCoincidencia->respuesta,
                'confianza' => $mejorPuntuacion
            ]);
        }
        
        // Respuestas predefinidas para preguntas comunes
        $respuestasComunes = $this->getRespuestasComunes();
        foreach ($respuestasComunes as $palabrasClave => $respuesta) {
            // Comprobar si alguna palabra clave está en la pregunta
            $palabras = explode(',', $palabrasClave);
            foreach ($palabras as $palabra) {
                if (stripos($pregunta, trim($palabra)) !== false) {
                    return response()->json([
                        'respuesta' => $respuesta,
                        'confianza' => 0.7
                    ]);
                }
            }
        }
        
        // Respuesta por defecto si no se encontró coincidencia
        return response()->json([
            'respuesta' => 'Lo siento, no puedo responder a esa pregunta. Por favor, intenta reformular tu consulta o contacta con un administrador.',
            'confianza' => 0
        ]);
    }
    
    /**
     * Calcula la similitud entre dos strings usando la distancia de Levenshtein
     *
     * @param string $str1
     * @param string $str2
     * @return float
     */
    private function calcularSimilitud($str1, $str2)
    {
        $levenshtein = levenshtein($str1, $str2);
        $maxLen = max(strlen($str1), strlen($str2));
        
        if ($maxLen === 0) return 1.0; // Ambas cadenas están vacías
        
        return 1.0 - ($levenshtein / $maxLen);
    }
    
    /**
     * Proporciona respuestas comunes para palabras clave específicas
     *
     * @return array
     */
    private function getRespuestasComunes()
    {
        return [
            'hola,buenos días,saludos,buen día,hi,hello' => 'Hola, soy el asistente virtual de MuniApp. ¿En qué puedo ayudarte hoy?',
            'gracias,thanks,thank you,agradecido' => 'De nada, estoy aquí para ayudarte. ¿Hay algo más en lo que pueda asistirte?',
            'adiós,chao,bye,hasta luego,nos vemos' => 'Hasta luego, que tengas un buen día. Si necesitas ayuda nuevamente, estaré aquí.',
            'horario,atención,oficina,atienden' => 'El horario de atención de las oficinas municipales es de lunes a viernes de 8:00 AM a 4:00 PM.',
            'dirección,ubicación,donde queda,donde está' => 'La oficina municipal está ubicada en Calle Principal #123, Centro. Puedes encontrar más información en la sección de Contacto.',
            'teléfono,llamar,número,contacto' => 'Puedes comunicarte con la oficina municipal al teléfono 0123-456-789 en horario de atención.',
            'quién eres,que eres,tu nombre' => 'Soy el asistente virtual de MuniApp, diseñado para ayudarte con información sobre trámites municipales y responder tus consultas.',
            'ayuda,help,necesito ayuda' => 'Estoy aquí para ayudarte. Puedes preguntarme sobre cómo iniciar un trámite, verificar el estado de una solicitud, documentos necesarios, y más.',
        ];
    }
    
    /**
     * Muestra la interfaz de administración de preguntas frecuentes
     * 
     * @return \Illuminate\Http\Response
     */
    public function admin()
    {
        // Verificar si el usuario es administrador
        if (!\Auth::user()->hasRole('admin')) {
            return redirect()->route('home')->withErrors(['mensaje' => 'No tienes permiso para acceder a esta sección.']);
        }
        
        $faqs = \App\Models\Faq::orderBy('orden', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();
                
        return view('chatbot.admin', compact('faqs'));
    }
    
    /**
     * Guarda una nueva pregunta frecuente
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Verificar si el usuario es administrador
        if (!\Auth::user()->hasRole('admin')) {
            return redirect()->route('home')->withErrors(['mensaje' => 'No tienes permiso para acceder a esta sección.']);
        }
        
        $request->validate([
            'pregunta' => 'required|string|max:255',
            'respuesta' => 'required|string',
            'categoria' => 'nullable|string|max:100',
            'orden' => 'nullable|integer'
        ]);
        
        \App\Models\Faq::create($request->all());
        
        return redirect()->route('chatbot.admin')->with('success', 'Pregunta frecuente creada correctamente.');
    }
    
    /**
     * Actualiza una pregunta frecuente
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Verificar si el usuario es administrador
        if (!\Auth::user()->hasRole('admin')) {
            return redirect()->route('home')->withErrors(['mensaje' => 'No tienes permiso para acceder a esta sección.']);
        }
        
        $request->validate([
            'pregunta' => 'required|string|max:255',
            'respuesta' => 'required|string',
            'categoria' => 'nullable|string|max:100',
            'orden' => 'nullable|integer',
            'activo' => 'nullable|boolean'
        ]);
        
        $faq = \App\Models\Faq::findOrFail($id);
        $faq->update($request->all());
        
        return redirect()->route('chatbot.admin')->with('success', 'Pregunta frecuente actualizada correctamente.');
    }
    
    /**
     * Elimina una pregunta frecuente
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Verificar si el usuario es administrador
        if (!\Auth::user()->hasRole('admin')) {
            return redirect()->route('home')->withErrors(['mensaje' => 'No tienes permiso para acceder a esta sección.']);
        }
        
        $faq = \App\Models\Faq::findOrFail($id);
        $faq->delete();
        
        return redirect()->route('chatbot.admin')->with('success', 'Pregunta frecuente eliminada correctamente.');
    }
}
