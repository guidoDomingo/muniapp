<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Solicitud;
use App\Models\Tramite;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_admin_dashboard');
    }

    public function index()
    {
        // Estadísticas generales
        $stats = [
            'total_users' => User::count(),
            'total_solicitudes' => Solicitud::count(),
            'total_tramites' => Tramite::count(),
            'pending_solicitudes' => Solicitud::where('estado', 'pendiente')->count(),
            'in_progress_solicitudes' => Solicitud::where('estado', 'en_proceso')->count(),
            'completed_solicitudes' => Solicitud::where('estado', 'completado')->count(),
            'rejected_solicitudes' => Solicitud::where('estado', 'rechazado')->count(),
        ];

        // Solicitudes por mes (últimos 12 meses)
        $solicitudesByMonth = Solicitud::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', Carbon::now()->subYear())
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Trámites más solicitados
        $popularTramites = Tramite::withCount('solicitudes')
            ->orderBy('solicitudes_count', 'desc')
            ->limit(5)
            ->get();

        // Solicitudes recientes
        $recentSolicitudes = Solicitud::with(['user', 'tramite'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Usuarios por rol
        $usersByRole = User::select('roles.name as role_name', DB::raw('COUNT(*) as count'))
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->groupBy('roles.name')
            ->get();

        // Rendimiento por departamento
        $departmentPerformance = Department::withCount(['tramites', 'users'])
            ->with(['tramites' => function($query) {
                $query->withCount('solicitudes');
            }])
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'solicitudesByMonth',
            'popularTramites',
            'recentSolicitudes',
            'usersByRole',
            'departmentPerformance'
        ));
    }

    public function analytics()
    {
        // Already protected by admin role middleware in routes

        // Tiempo promedio de resolución por trámite
        $averageResolutionTime = Solicitud::select(
            'tramites.nombre',
            DB::raw('AVG(DATEDIFF(actual_completion_date, solicitudes.created_at)) as avg_days')
        )
        ->join('tramites', 'solicitudes.tramite_id', '=', 'tramites.id')
        ->whereNotNull('actual_completion_date')
        ->groupBy('tramites.id', 'tramites.nombre')
        ->get();

        // Satisfacción por departamento (si tienes un sistema de ratings)
        $departmentSatisfaction = Department::with(['tramites.solicitudes'])
            ->get()
            ->map(function ($dept) {
                $totalSolicitudes = $dept->tramites->sum(function ($tramite) {
                    return $tramite->solicitudes->count();
                });
                $completedSolicitudes = $dept->tramites->sum(function ($tramite) {
                    return $tramite->solicitudes->where('estado', 'completado')->count();
                });
                
                return [
                    'department' => $dept->name,
                    'completion_rate' => $totalSolicitudes > 0 ? ($completedSolicitudes / $totalSolicitudes) * 100 : 0,
                    'total_solicitudes' => $totalSolicitudes,
                ];
            });

        return view('admin.analytics', compact('averageResolutionTime', 'departmentSatisfaction'));
    }

    public function reports()
    {
        // Already protected by admin role middleware in routes
        
        return view('admin.reports');
    }

    public function settings()
    {
        // Already protected by admin role middleware in routes
        return view('admin.settings');
    }

    public function updateSettings(Request $request)
    {
        // Already protected by admin role middleware in routes
        
        $request->validate([
            'app_name' => 'nullable|string|max:255',
            'municipality_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'municipality_address' => 'nullable|string|max:500',
            'timezone' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:10',
            'system_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $settings = [];

        // Process regular settings
        if ($request->filled('app_name')) {
            $settings['app_name'] = $request->app_name;
        }
        if ($request->filled('municipality_name')) {
            $settings['municipality_name'] = $request->municipality_name;
        }
        if ($request->filled('contact_email')) {
            $settings['contact_email'] = $request->contact_email;
        }
        if ($request->filled('contact_phone')) {
            $settings['contact_phone'] = $request->contact_phone;
        }
        if ($request->filled('municipality_address')) {
            $settings['municipality_address'] = $request->municipality_address;
        }
        if ($request->filled('timezone')) {
            $settings['timezone'] = $request->timezone;
        }
        if ($request->filled('language')) {
            $settings['language'] = $request->language;
        }

        // Handle logo upload
        if ($request->hasFile('system_logo')) {
            $logo = $request->file('system_logo');
            
            // Delete old logo files if they exist
            $logoExtensions = ['png', 'jpg', 'jpeg', 'gif', 'svg'];
            foreach ($logoExtensions as $ext) {
                $oldLogoPath = public_path("images/logo.{$ext}");
                if (file_exists($oldLogoPath)) {
                    unlink($oldLogoPath);
                }
            }
            
            // Store new logo with original extension
            $logoExtension = $logo->getClientOriginalExtension();
            $logoName = 'logo.' . $logoExtension;
            $logo->move(public_path('images'), $logoName);
            
            $settings['system_logo'] = 'images/' . $logoName;
        }

        // Here you could save settings to database or config files
        // For now, we'll simulate success
        
        return redirect()->route('admin.settings')->with('success', 'Configuración actualizada exitosamente');
    }
}