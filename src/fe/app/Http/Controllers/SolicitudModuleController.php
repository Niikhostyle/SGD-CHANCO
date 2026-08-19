<?php

namespace App\Http\Controllers;

use App\Providers\AppServiceProvider;
use App\Models\Buzon;
use App\Models\TipoAvance;
use App\Models\TipoAsignacionFolio;
use App\Models\TipoFlujo;
use App\Models\TipoFolio;
use App\Models\TipoOrigen;
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
        $departamento = '';
        try {
            $departamento = (string) (DB::table('sol_usuario_rol as r')
                ->leftJoin('sol_departamentos as d', 'd.id', '=', 'r.departamento_id')
                ->where('r.user_id', $yo->id)
                ->value('d.nombre') ?? '');
        } catch (\Throwable $e) {
            $departamento = '';
        }
        $yoDatos = [
            'nombre' => trim(($yo->nombres ?? '') . ' ' . ($yo->primer_apellido ?? '') . ' ' . ($yo->segundo_apellido ?? '')),
            'run' => $yo->run ?? '',
            'cargo' => $yo->cargo ?? '',
            'departamento' => $departamento,
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
        toast('Solicitud firmada por usted y enviada al buzón. Sigue el trámite como un documento SGD.', 'success');
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
        $lista = $tipos->ok() ? ($tipos->json()['data'] ?? []) : [];
        $error = $tipos->failed() ? ($tipos->json()['message'] ?? ('Error al cargar tipos (HTTP ' . $tipos->status() . ')')) : null;
        if (!$lista) {
            try {
                $flujos = DB::table('sol_tipo_documento_buzon')->orderBy('orden')->get()->groupBy('sol_tipo_documento_id');
                $lista = DB::table('sol_tipo_documentos')->orderBy('nombre')->get()->map(function ($t) use ($flujos) {
                    $row = (array) $t;
                    $row['buzones_flujo'] = array_values(($flujos[$t->id] ?? collect())->map(function ($p) {
                        $a = (array) $p;
                        if (!empty($a['acciones']) && is_string($a['acciones'])) {
                            $a['acciones'] = json_decode($a['acciones'], true) ?: [];
                        }
                        return $a;
                    })->all());
                    return $row;
                })->all();
                if ($lista) {
                    $error = null;
                }
            } catch (\Throwable $e) {
                // se mantiene el error de la API
            }
        }
        $datosFlujo = collect();
        $datosOrigen = collect();
        $datosAvance = collect();
        $datosFolio = collect();
        $datosAsignacionFolio = [];
        try {
            $datosFlujo = TipoFlujo::all('id_tipo_flujo', 'nombre');
            $datosOrigen = TipoOrigen::all('id_tipo_origen', 'nombre');
            $datosAvance = TipoAvance::all('id_tipo_avance', 'nombre');
            $datosFolio = TipoFolio::all('id_tipo_folio', 'nombre');
            $datosAsignacionFolio = TipoAsignacionFolio::all('id_tipo_asignacion_folio', 'nombre')->toArray();
            $ordenIds = [1, 2, 5, 3, 4];
            $idsOrdenados = array_flip($ordenIds);
            $ids = array_column($datosAsignacionFolio, 'id_tipo_asignacion_folio');
            if ($ids) {
                array_multisort(array_map(function ($id) use ($idsOrdenados) {
                    return $idsOrdenados[$id] ?? 99;
                }, $ids), $datosAsignacionFolio);
            }
        } catch (\Throwable $e) {
            // catálogos SGD no disponibles
        }
        return view('solicitudes.tipos', [
            'tipos' => $lista,
            'buzones' => $buzones->ok() ? ($buzones->json()['data'] ?? []) : [],
            'config' => $cfg->ok() ? ($cfg->json()['data'] ?? []) : [],
            'error' => $error,
            'datosFlujo' => $datosFlujo,
            'datosOrigen' => $datosOrigen,
            'datosAvance' => $datosAvance,
            'datosFolio' => $datosFolio,
            'datosAsignacionFolio' => $datosAsignacionFolio,
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
            'descripcion' => $request->input('descripcion'),
            'nombre_corto' => $request->input('nombre_corto'),
            'nombre_corto_firma' => $request->input('nombre_corto_firma'),
            'tipo_solicitud' => $request->input('tipo_solicitud'),
            'categoria' => $request->input('categoria', 'dias'),
            'regimen_laboral' => $request->input('regimen_laboral'),
            'tipo_origen' => $request->input('tipo_origen'),
            'tipo_flujo' => $request->input('tipo_flujo'),
            'tipo_avance' => $request->input('tipo_avance'),
            'tipo_folio' => $request->input('tipo_folio'),
            'tipo_asignacion_folio' => $request->input('tipo_asignacion_folio'),
            'consume_saldo' => $request->boolean('consume_saldo'),
            'requiere_fe' => $request->boolean('requiere_fe'),
            'numero_firmas' => (int) $request->input('numero_firmas', 0),
            'derivar_primera_firma' => $request->boolean('derivar_primera_firma'),
            'derivar_ultima_firma' => $request->boolean('derivar_ultima_firma'),
            'buzon_primera_firma' => $request->input('buzon_primera_firma') ?: null,
            'buzon_ultima_firma' => $request->input('buzon_ultima_firma') ?: null,
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
        toast(($res->json()['ok'] ?? false) ? 'Plantilla borrada' : ($res->json()['message'] ?? 'Error'), ($res->json()['ok'] ?? false) ? 'success' : 'error');
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
