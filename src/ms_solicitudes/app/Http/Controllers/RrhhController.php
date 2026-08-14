<?php

namespace App\Http\Controllers;

use App\Models\Sessions;
use App\Models\SolSaldoAnual;
use App\Services\RolService;
use App\Services\SaldoService;
use Exception;
use Illuminate\Http\Request;

class RrhhController extends Controller
{
    protected $roles;
    protected $saldos;

    public function __construct()
    {
        $this->roles = new RolService();
        $this->saldos = new SaldoService();
    }

    protected function userId(Request $request): int
    {
        $session = Sessions::where('id', $request->header('key'))->first();
        if (!$session || !$session->user_id) {
            throw new Exception('Sesión inválida.');
        }
        return (int) $session->user_id;
    }

    public function saldos(Request $request)
    {
        try {
            $uid = $this->userId($request);
            $this->roles->assertRoles($uid, ['rrhh', 'admin_solicitudes']);
            $anio = (int) ($request->get('anio') ?: date('Y'));
            $data = SolSaldoAnual::with('user')->where('anio', $anio)->orderBy('user_id')->get();
            return response()->json(['ok' => true, 'data' => $data, 'anio' => $anio]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function movimiento(Request $request)
    {
        try {
            $uid = $this->userId($request);
            $this->roles->assertRoles($uid, ['rrhh', 'admin_solicitudes']);
            $d = $request->json()->all();
            $saldo = $this->saldos->registrarMovimiento(
                (int) $d['user_id'],
                $uid,
                (int) ($d['anio'] ?? date('Y')),
                $d['tipo'],
                $d['permiso_tipo'] ?? 'dias_administrativos',
                (int) $d['dias'],
                $d['motivo'] ?? null
            );
            return response()->json(['ok' => true, 'data' => $saldo->load('user')]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
