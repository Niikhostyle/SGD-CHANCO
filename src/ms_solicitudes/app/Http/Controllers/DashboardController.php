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
            $saldo = $saldos->resumen($uid);

            $mias = SolSolicitud::where('user_id', $uid)->count();
            $flujo = new \App\Services\FlujoService();
            $misBuzones = $flujo->idsBuzonesUsuario($uid);
            $pendientesBuzon = 0;
            if ($misBuzones) {
                $pendientesBuzon = SolSolicitud::where('estado', 'pendiente')
                    ->whereIn('id_buzon_destino', $misBuzones)
                    ->count();
            }
            $buzonRrhh = $flujo->resolverBuzonConfig('buzon_rrhh_id', ['departamento de personal', 'recursos humanos', 'rrhh']);
            $buzonAlcalde = $flujo->resolverBuzonConfig('buzon_alcalde_id', ['alcalde', 'alcaldía', 'alcaldia']);
            $pendientes = [
                'directivo' => SolSolicitud::where('estado', 'pendiente_directivo')->count(),
                'rrhh' => SolSolicitud::where('estado', 'pendiente_rrhh')->count()
                    + ($buzonRrhh
                        ? SolSolicitud::where('estado', 'pendiente')->where('id_buzon_destino', $buzonRrhh->id_buzon)->count()
                        : 0),
                'alcalde' => SolSolicitud::where('estado', 'pendiente_alcalde')->count()
                    + ($buzonAlcalde
                        ? SolSolicitud::where('estado', 'pendiente')->where('id_buzon_destino', $buzonAlcalde->id_buzon)->count()
                        : 0),
                'buzon' => $pendientesBuzon,
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
