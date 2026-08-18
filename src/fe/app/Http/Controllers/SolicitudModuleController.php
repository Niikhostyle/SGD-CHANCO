<?php

namespace App\Http\Controllers;

use App\Providers\AppServiceProvider;
use App\Models\Buzon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class SolicitudModuleController extends Controller
{
    protected function api()
    {
        return rtrim(config('sgd.api_solicitudes'), '/');
    }

    protected function key(): string
    {
        return AppServiceProvider::session_key_general() ?: session()->getId();
    }

    protected function client()
    {
        return Http::withHeaders([
            'key' => $this->key(),
        ])->asJson()->timeout(120);
    }

    public function index(Request $request)
    {
        $dash = $this->client()->get($this->api() . '/api/sgd-solicitudes/dashboard');
        $list = $this->client()->get($this->api() . '/api/sgd-solicitudes/listar', [
            'estado' => $request->get('estado'),
            'tipo' => $request->get('tipo'),
            'bandeja' => $request->get('bandeja'),
        ]);
        $tiposRes = $this->client()->get($this->api() . '/api/sgd-solicitudes/tipo-documentos', ['solo_activos' => 1]);
        $dashboard = $dash->ok() ? ($dash->json()['data'] ?? []) : [];
        $solicitudes = $list->ok() ? ($list->json()['data'] ?? []) : [];
        $tiposFiltro = $tiposRes->ok() ? ($tiposRes->json()['data'] ?? []) : [];
        if ($dash->failed() || $list->failed()) {
            toast(($list->json()['message'] ?? $dash->json()['message'] ?? 'Error al cargar solicitudes'), 'error');
        }
        return view('solicitudes.index', compact('dashboard', 'solicitudes', 'tiposFiltro'));
    }

    public function create()
    {
        $tiposRes = $this->client()->get($this->api() . '/api/sgd-solicitudes/tipo-documentos', ['solo_activos' => 1]);
        $buzonesRes = $this->client()->get($this->api() . '/api/sgd-solicitudes/buzones');
        $tipos = $tiposRes->ok() ? ($tiposRes->json()['data'] ?? []) : [];
        $buzones = $buzonesRes->ok() ? ($buzonesRes->json()['data'] ?? []) : [];
        if (!$tipos) {
            try {
                $tipos = DB::table('sol_tipo_documentos')->where('activo', true)->orderBy('nombre')->get()->map(function ($t) {
                    return (array) $t + ['buzones_flujo' => []];
                })->all();
            } catch (\Throwable $e) {
                $tipos = [];
            }
        }
        if (!$buzones) {
            try {
                $buzones = Buzon::orderBy('nombre')->get(['id_buzon', 'id_tipo_buzon', 'nombre', 'nombre_corto'])->toArray();
            } catch (\Throwable $e) {
                $buzones = [];
            }
        }
        $yo = auth()->user();
        $yoDatos = [
            'nombre' => trim(($yo->nombres ?? '') . ' ' . ($yo->primer_apellido ?? '') . ' ' . ($yo->segundo_apellido ?? '')),
            'run' => $yo->run ?? '',
            'cargo' => $yo->cargo ?? '',
        ];
        return view('solicitudes.create', compact('tipos', 'buzones', 'yoDatos'));
    }

    public function store(Request $request)
    {
        $payload = $request->validate([
            'sol_tipo_documento_id' => 'required|integer',
            'tipo_solicitud' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_termino' => 'required|date|after_or_equal:fecha_inicio',
            'motivo' => 'nullable|string',
            'explicacion' => 'nullable|string',
            'id_buzon_destino' => 'required|integer',
            'mensaje_para_directivo' => 'nullable|string',
            'viaticos_destino' => 'nullable|string',
            'viaticos_hora_inicio' => 'nullable|string',
            'viaticos_hora_termino' => 'nullable|string',
            'licencia_folio' => 'nullable|string',
            'licencia_tipo' => 'nullable|string',
            'licencia_emisor' => 'nullable|string',
            'documento_cuerpo_html' => 'nullable|string',
            'usar_firmagob' => 'nullable',
        ]);
        $payload['usar_firmagob'] = $request->boolean('usar_firmagob');
        if (empty($payload['explicacion'])) {
            $payload['explicacion'] = $payload['motivo'] ?? null;
        }
        if (empty($payload['mensaje_para_directivo'])) {
            $payload['mensaje_para_directivo'] = $payload['motivo'] ?? null;
        }

        $res = $this->client()->post($this->api() . '/api/sgd-solicitudes/crear', $payload);
        if ($res->failed() || !($res->json()['ok'] ?? false)) {
            toast($res->json()['message'] ?? 'No se pudo crear la solicitud', 'error');
            return back()->withInput();
        }
        toast('Solicitud enviada al buzón. Ya puede tramitarse como un documento SGD.', 'success');
        return redirect()->route('solicitudes.show', ['id' => $res->json()['data']['id']]);
    }

    public function show($id)
    {
        $res = $this->client()->get($this->api() . '/api/sgd-solicitudes/ver', ['id' => $id]);
        if ($res->failed() || !($res->json()['ok'] ?? false)) {
            toast($res->json()['message'] ?? 'Solicitud no encontrada', 'error');
            return redirect()->route('solicitudes.index');
        }
        $solicitud = $res->json()['data'];
        return view('solicitudes.show', compact('solicitud'));
    }

    public function accion(Request $request, $id, $accion)
    {
        $flujo = ['visar', 'firmar', 'rechazar'];
        if (in_array($accion, $flujo, true)) {
            $res = $this->client()->put($this->api() . '/api/sgd-solicitudes/actuar', [
                'id' => (int) $id,
                'accion' => $accion,
                'observaciones' => $request->input('observaciones'),
            ]);
            if ($res->failed() || !($res->json()['ok'] ?? false)) {
                toast($res->json()['message'] ?? 'No se pudo completar la acción', 'error');
                return back();
            }
            toast('Acción realizada', 'success');
            return redirect()->route('solicitudes.show', ['id' => $id]);
        }

        $map = [
            'aprobar-directivo' => 'aprobar-directivo',
            'rechazar-directivo' => 'rechazar-directivo',
            'firmar-rrhh' => 'firmar-rrhh',
            'rechazar-rrhh' => 'rechazar-rrhh',
            'firmar-alcalde' => 'firmar-alcalde',
            'rechazar-alcalde' => 'rechazar-alcalde',
        ];
        if (!isset($map[$accion])) {
            toast('Acción inválida', 'error');
            return back();
        }
        $res = $this->client()->put($this->api() . '/api/sgd-solicitudes/' . $map[$accion], [
            'id' => (int) $id,
            'observaciones' => $request->input('observaciones'),
        ]);
        if ($res->failed() || !($res->json()['ok'] ?? false)) {
            toast($res->json()['message'] ?? 'No se pudo completar la acción', 'error');
            return back();
        }
        toast('Acción realizada', 'success');
        return redirect()->route('solicitudes.show', ['id' => $id]);
    }

    public function destroy($id)
    {
        $res = $this->client()->delete($this->api() . '/api/sgd-solicitudes/eliminar?id=' . (int) $id);
        if ($res->failed() || !($res->json()['ok'] ?? false)) {
            toast($res->json()['message'] ?? 'No se pudo eliminar', 'error');
            return back();
        }
        toast('Solicitud eliminada', 'success');
        return redirect()->route('solicitudes.index');
    }

    public function pdf($id)
    {
        $res = $this->client()->get($this->api() . '/api/sgd-solicitudes/pdf', ['id' => $id]);
        if ($res->failed() || empty($res->json()['pdf_base64'])) {
            toast($res->json()['message'] ?? 'PDF no disponible', 'error');
            return back();
        }
        $bin = base64_decode($res->json()['pdf_base64']);
        return response($bin, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="solicitud-' . $id . '.pdf"',
        ]);
    }

    public function admin()
    {
        $roles = $this->client()->get($this->api() . '/api/sgd-solicitudes/roles');
        $deps = $this->client()->get($this->api() . '/api/sgd-solicitudes/departamentos');
        $cargos = $this->client()->get($this->api() . '/api/sgd-solicitudes/cargos');
        $tipos = $this->client()->get($this->api() . '/api/sgd-solicitudes/tipo-documentos');
        $users = $this->client()->get($this->api() . '/api/sgd-solicitudes/usuarios-catalogo');
        return view('solicitudes.admin', [
            'roles' => $roles->ok() ? ($roles->json()['data'] ?? []) : [],
            'departamentos' => $deps->ok() ? ($deps->json()['data'] ?? []) : [],
            'cargos' => $cargos->ok() ? ($cargos->json()['data'] ?? []) : [],
            'tipos' => $tipos->ok() ? ($tipos->json()['data'] ?? []) : [],
            'usuarios' => $users->ok() ? ($users->json()['data'] ?? []) : [],
            'error' => (!$roles->ok() ? ($roles->json()['message'] ?? null) : null),
        ]);
    }

    public function adminSaveRol(Request $request)
    {
        $res = $this->client()->put($this->api() . '/api/sgd-solicitudes/roles', $request->all());
        toast(($res->json()['ok'] ?? false) ? 'Rol actualizado' : ($res->json()['message'] ?? 'Error'), ($res->json()['ok'] ?? false) ? 'success' : 'error');
        return back();
    }

    public function adminSaveDepartamento(Request $request)
    {
        $payload = $request->all();
        if (!empty($payload['id'])) {
            $res = $this->client()->put($this->api() . '/api/sgd-solicitudes/departamentos', $payload);
        } else {
            $res = $this->client()->post($this->api() . '/api/sgd-solicitudes/departamentos', $payload);
        }
        toast(($res->json()['ok'] ?? false) ? 'Departamento guardado' : ($res->json()['message'] ?? 'Error'), ($res->json()['ok'] ?? false) ? 'success' : 'error');
        return back();
    }

    public function adminSaveCargo(Request $request)
    {
        $res = $this->client()->post($this->api() . '/api/sgd-solicitudes/cargos', ['nombre' => $request->input('nombre')]);
        toast(($res->json()['ok'] ?? false) ? 'Cargo creado' : ($res->json()['message'] ?? 'Error'), ($res->json()['ok'] ?? false) ? 'success' : 'error');
        return back();
    }

    public function adminSaveTipo(Request $request)
    {
        return $this->tiposSave($request);
    }

    public function tipos()
    {
        $tipos = $this->client()->get($this->api() . '/api/sgd-solicitudes/tipo-documentos');
        $buzones = $this->client()->get($this->api() . '/api/sgd-solicitudes/buzones');
        $cfg = $this->client()->get($this->api() . '/api/sgd-solicitudes/configuraciones');
        return view('solicitudes.tipos', [
            'tipos' => $tipos->ok() ? ($tipos->json()['data'] ?? []) : [],
            'buzones' => $buzones->ok() ? ($buzones->json()['data'] ?? []) : [],
            'config' => $cfg->ok() ? ($cfg->json()['data'] ?? []) : [],
            'error' => $tipos->failed() ? ($tipos->json()['message'] ?? 'Error al cargar tipos') : null,
        ]);
    }

    public function tiposConfig(Request $request)
    {
        $res = $this->client()->put($this->api() . '/api/sgd-solicitudes/configuraciones', [
            'buzon_rrhh_id' => $request->input('buzon_rrhh_id') ?: null,
            'buzon_alcalde_id' => $request->input('buzon_alcalde_id') ?: null,
        ]);
        toast(($res->json()['ok'] ?? false) ? 'Flujo por defecto guardado' : ($res->json()['message'] ?? 'Error'), ($res->json()['ok'] ?? false) ? 'success' : 'error');
        return back();
    }

    public function tiposSave(Request $request)
    {
        $acciones = $request->input('flujo_acciones', []);
        $flujo = [];
        foreach ($request->input('flujo_id_buzon', []) as $i => $idBuzon) {
            if (!$idBuzon) {
                continue;
            }
            $flujo[] = [
                'id_buzon' => (int) $idBuzon,
                'nombre_buzon' => $request->input('flujo_nombre_buzon.' . $i),
                'orden' => $i + 1,
                'acciones' => $acciones[$i] ?? ['firmar'],
            ];
        }

        $payload = [
            'id' => $request->input('id') ?: null,
            'nombre' => $request->input('nombre'),
            'tipo_solicitud' => $request->input('tipo_solicitud'),
            'categoria' => $request->input('categoria', 'dias'),
            'regimen_laboral' => $request->input('regimen_laboral'),
            'consume_saldo' => $request->boolean('consume_saldo'),
            'requiere_fe' => $request->boolean('requiere_fe'),
            'numero_firmas' => (int) $request->input('numero_firmas', 1),
            'primer_buzon_editable' => $request->boolean('primer_buzon_editable'),
            'activo' => $request->has('activo') ? $request->boolean('activo') : true,
            'plantilla_encabezado_html' => $request->input('plantilla_encabezado_html'),
            'plantilla_cuerpo_html' => $request->input('plantilla_cuerpo_html'),
            'plantilla_distribucion_html' => $request->input('plantilla_distribucion_html'),
            'buzones_flujo' => $flujo,
        ];

        if (!empty($payload['id'])) {
            $res = $this->client()->put($this->api() . '/api/sgd-solicitudes/tipo-documentos', $payload);
        } else {
            unset($payload['id']);
            $res = $this->client()->post($this->api() . '/api/sgd-solicitudes/tipo-documentos', $payload);
        }
        toast(($res->json()['ok'] ?? false) ? 'Plantilla guardada' : ($res->json()['message'] ?? 'Error'), ($res->json()['ok'] ?? false) ? 'success' : 'error');
        return back();
    }

    public function tiposDelete($id)
    {
        $res = $this->client()->delete($this->api() . '/api/sgd-solicitudes/tipo-documentos?id=' . (int) $id);
        toast(($res->json()['ok'] ?? false) ? 'Tipo desactivado' : ($res->json()['message'] ?? 'Error'), ($res->json()['ok'] ?? false) ? 'success' : 'error');
        return back();
    }

    public function rrhh(Request $request)
    {
        $anio = $request->get('anio', date('Y'));
        $res = $this->client()->get($this->api() . '/api/sgd-solicitudes/saldos', ['anio' => $anio]);
        $users = $this->client()->get($this->api() . '/api/sgd-solicitudes/usuarios-catalogo');
        return view('solicitudes.rrhh', [
            'saldos' => $res->ok() ? ($res->json()['data'] ?? []) : [],
            'anio' => $anio,
            'usuarios' => $users->ok() ? ($users->json()['data'] ?? []) : [],
            'error' => $res->failed() ? ($res->json()['message'] ?? 'Error') : null,
        ]);
    }

    public function rrhhMovimiento(Request $request)
    {
        $res = $this->client()->post($this->api() . '/api/sgd-solicitudes/saldos/movimiento', $request->all());
        toast(($res->json()['ok'] ?? false) ? 'Movimiento registrado' : ($res->json()['message'] ?? 'Error'), ($res->json()['ok'] ?? false) ? 'success' : 'error');
        return back();
    }
}
