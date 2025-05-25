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
        return view('chatbot.index');
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
        
        // Buscar en la base de datos preguntas similares
        $faqs = \App\Models\Faq::where('activo', true)
            ->get();
        
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
