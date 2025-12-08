<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use App\Models\Tramite;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function index()
    {
        return view('admin.reports');
    }

    public function getData(Request $request)
    {
        try {
            $reportType = $request->get('report_type', 'solicitudes');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            $departmentId = $request->get('department_id');
            $tramiteId = $request->get('tramite_id');
            $status = $request->get('status');
            $userId = $request->get('user_id');

            $data = [];
            $headers = [];

            switch ($reportType) {
                case 'solicitudes':
                    $data = $this->getSolicitudesData($dateFrom, $dateTo, $departmentId, $tramiteId, $status, $userId);
                    $headers = ['ID', 'Usuario', 'Trámite', 'Estado', 'Departamento', 'Fecha Creación', 'Última Actualización'];
                    break;

                case 'tramites':
                    $data = $this->getTramitesData($dateFrom, $dateTo, $departmentId);
                    $headers = ['ID', 'Nombre', 'Descripción', 'Departamento', 'Activo', 'Fecha Creación'];
                    break;

                case 'usuarios':
                    $data = $this->getUsuariosData($dateFrom, $dateTo, $departmentId);
                    $headers = ['ID', 'Nombre', 'Email', 'Rol', 'Departamento', 'Activo', 'Fecha Registro'];
                    break;

                case 'general':
                    $data = $this->getGeneralData($dateFrom, $dateTo);
                    $headers = ['Métrica', 'Valor', 'Período'];
                    break;

                default:
                    $data = [];
                    $headers = [];
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'headers' => $headers,
                'total' => count($data)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getSolicitudesData($dateFrom, $dateTo, $departmentId, $tramiteId, $status, $userId)
    {
        $query = Solicitud::with(['user', 'tramite.department']);

        if ($dateFrom) {
            $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        }

        if ($dateTo) {
            $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }

        if ($departmentId) {
            $query->whereHas('tramite.department', function($q) use ($departmentId) {
                $q->where('id', $departmentId);
            });
        }

        if ($tramiteId) {
            $query->where('tramite_id', $tramiteId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->get()->map(function($solicitud) {
            return [
                'id' => $solicitud->id,
                'usuario' => $solicitud->user->name ?? 'N/A',
                'tramite' => $solicitud->tramite->name ?? 'N/A',
                'estado' => ucfirst(str_replace('_', ' ', $solicitud->status)),
                'departamento' => $solicitud->tramite->department->name ?? 'N/A',
                'fecha_creacion' => $solicitud->created_at->format('d/m/Y H:i'),
                'ultima_actualizacion' => $solicitud->updated_at->format('d/m/Y H:i')
            ];
        })->toArray();
    }

    private function getTramitesData($dateFrom, $dateTo, $departmentId)
    {
        $query = Tramite::with('department');

        if ($dateFrom) {
            $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        }

        if ($dateTo) {
            $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        return $query->get()->map(function($tramite) {
            return [
                'id' => $tramite->id,
                'nombre' => $tramite->name,
                'descripcion' => substr($tramite->description, 0, 100) . '...',
                'departamento' => $tramite->department->name ?? 'N/A',
                'activo' => $tramite->is_active ? 'Sí' : 'No',
                'fecha_creacion' => $tramite->created_at->format('d/m/Y H:i')
            ];
        })->toArray();
    }

    private function getUsuariosData($dateFrom, $dateTo, $departmentId)
    {
        $query = User::with(['roles', 'department']);

        if ($dateFrom) {
            $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        }

        if ($dateTo) {
            $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        return $query->get()->map(function($user) {
            return [
                'id' => $user->id,
                'nombre' => $user->name,
                'email' => $user->email,
                'rol' => $user->roles->pluck('name')->join(', ') ?: 'Sin rol',
                'departamento' => $user->department->name ?? 'N/A',
                'activo' => $user->is_active ? 'Sí' : 'No',
                'fecha_registro' => $user->created_at->format('d/m/Y H:i')
            ];
        })->toArray();
    }

    private function getGeneralData($dateFrom, $dateTo)
    {
        $startDate = $dateFrom ? Carbon::parse($dateFrom) : Carbon::now()->subMonth();
        $endDate = $dateTo ? Carbon::parse($dateTo) : Carbon::now();
        $period = $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y');

        return [
            [
                'metrica' => 'Total de Solicitudes',
                'valor' => Solicitud::whereBetween('created_at', [$startDate, $endDate])->count(),
                'periodo' => $period
            ],
            [
                'metrica' => 'Solicitudes Aprobadas',
                'valor' => Solicitud::whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'aprobada')->count(),
                'periodo' => $period
            ],
            [
                'metrica' => 'Solicitudes Pendientes',
                'valor' => Solicitud::whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'pendiente')->count(),
                'periodo' => $period
            ],
            [
                'metrica' => 'Solicitudes Rechazadas',
                'valor' => Solicitud::whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'rechazada')->count(),
                'periodo' => $period
            ],
            [
                'metrica' => 'Usuarios Registrados',
                'valor' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
                'periodo' => $period
            ],
            [
                'metrica' => 'Trámites Activos',
                'valor' => Tramite::where('is_active', true)->count(),
                'periodo' => 'Actual'
            ],
            [
                'metrica' => 'Departamentos Activos',
                'valor' => Department::where('is_active', true)->count(),
                'periodo' => 'Actual'
            ]
        ];
    }
}