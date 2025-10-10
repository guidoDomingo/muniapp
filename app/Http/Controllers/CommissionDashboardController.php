<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\Tramite;
use App\Models\User;
use Carbon\Carbon;

class CommissionDashboardController extends Controller
{
    public function index()
    {
        // Estadísticas principales
        $pendingSolicitudes = Solicitud::whereIn('estado', ['pendiente', 'recibido'])->count();
        $approvedToday = Solicitud::where('estado', 'aprobado')
            ->whereDate('updated_at', today())
            ->count();
        $inReviewSolicitudes = Solicitud::where('estado', 'en_revision')->count();
        $totalTramites = Tramite::where('is_active', true)->count();

        // Solicitudes recientes para revisar
        $recentSolicitudes = Solicitud::with(['user', 'tramite'])
            ->whereIn('estado', ['pendiente', 'recibido', 'en_revision'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Estadísticas del mes
        $monthSolicitudes = Solicitud::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Promedio de tiempo de revisión (en días) - Simplificado usando updated_at
        $avgReviewTime = Solicitud::where('estado', 'aprobado')
            ->get()
            ->map(function ($solicitud) {
                return $solicitud->created_at->diffInDays($solicitud->updated_at);
            })
            ->avg();

        $avgReviewTime = round($avgReviewTime ?? 0, 1);

        // Tasa de aprobación
        $totalSolicitudes = Solicitud::count();
        $approvedSolicitudes = Solicitud::where('estado', 'aprobado')->count();
        $approvalRate = $totalSolicitudes > 0 ? round(($approvedSolicitudes / $totalSolicitudes) * 100, 1) : 0;

        // Usuarios activos (último mes)
        $activeUsers = User::where('updated_at', '>=', now()->subMonth())->count();

        // Datos para el gráfico (últimos 30 días)
        $chartLabels = [];
        $chartData = [];
        $chartApproved = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('d/m');
            
            $dailySolicitudes = Solicitud::whereDate('created_at', $date)->count();
            $chartData[] = $dailySolicitudes;
            
            $dailyApproved = Solicitud::where('estado', 'aprobado')
                ->whereDate('updated_at', $date)
                ->count();
            $chartApproved[] = $dailyApproved;
        }

        return view('commission.dashboard', compact(
            'pendingSolicitudes',
            'approvedToday',
            'inReviewSolicitudes',
            'totalTramites',
            'recentSolicitudes',
            'monthSolicitudes',
            'avgReviewTime',
            'approvalRate',
            'activeUsers',
            'chartLabels',
            'chartData',
            'chartApproved'
        ));
    }

    /**
     * Aprobar solicitud
     */
    public function aproveSolicitud(Request $request, $id)
    {
        $solicitud = Solicitud::findOrFail($id);
        
        $solicitud->update([
            'estado' => 'aprobado',
            'assigned_to' => auth()->id(), // Usar la columna available assigned_to
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Solicitud aprobada exitosamente'
        ]);
    }

    /**
     * Rechazar solicitud
     */
    public function rejectSolicitud(Request $request, $id)
    {
        $request->validate([
            'motivo' => 'required|string|max:500'
        ]);

        $solicitud = Solicitud::findOrFail($id);
        
        $solicitud->update([
            'estado' => 'rechazado',
            'assigned_to' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Solicitud rechazada'
        ]);
    }
}
