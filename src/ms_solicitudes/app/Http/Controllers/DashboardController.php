<?php

namespace App\Http\Controllers;

use App\Models\Sessions;
use App\Models\SolSolicitud;
use App\Services\RolService;
use App\Services\SaldoService;
use Exception;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $session = Sessions::where('id', $request->header('key'))->first();
            if (!$session || !$session->user_id) {
                throw new Exception('Sesión inválida.');
            }
            $uid = (int) $session->user_id;
            $roles = new RolService();
            $saldos = new SaldoService();
            $rol = $roles->ensureRol($uid);
            $saldo = $saldos->obtenerOCrear($uid);

            $mias = SolSolicitud::where('user_id', $uid)->count();
            $pendientes = [
                'directivo' => SolSolicitud::where('estado', 'pendiente_directivo')->count(),
                'rrhh' => SolSolicitud::where('estado', 'pendiente_rrhh')->count(),
                'alcalde' => SolSolicitud::where('estado', 'pendiente_alcalde')->count(),
            ];

            return response()->json([
                'ok' => true,
                'data' => [
                    'rol' => $rol,
                    'saldo' => $saldo,
                    'mis_solicitudes' => $mias,
                    'pendientes' => $pendientes,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
