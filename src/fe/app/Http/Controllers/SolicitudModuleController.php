<?php

namespace App\Http\Controllers;

use App\Providers\AppServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

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
            'Content-Type' => 'application/json',
        ])->timeout(60);
    }

    public function index(Request $request)
    {
        $dash = $this->client()->get($this->api() . '/api/sgd-solicitudes/dashboard');
        $list = $this->client()->get($this->api() . '/api/sgd-solicitudes/listar', [
            'estado' => $request->get('estado'),
            'tipo' => $request->get('tipo'),
        ]);
        $dashboard = $dash->ok() ? ($dash->json()['data'] ?? []) : [];
        $solicitudes = $list->ok() ? ($list->json()['data'] ?? []) : [];
        if ($dash->failed() || $list->failed()) {
            toast(($list->json()['message'] ?? $dash->json()['message'] ?? 'Error al cargar solicitudes'), 'error');
        }
        return view('solicitudes.index', compact('dashboard', 'solicitudes'));
    }

    public function create()
    {
        $deps = $this->client()->get($this->api() . '/api/sgd-solicitudes/departamentos');
        $users = $this->client()->get($this->api() . '/api/sgd-solicitudes/usuarios-catalogo');
        $departamentos = $deps->ok() ? ($deps->json()['data'] ?? []) : [];
        $usuarios = $users->ok() ? ($users->json()['data'] ?? []) : [];
        $tipos = [
            'dias_administrativos' => 'Días administrativos',
            'feriados_legales' => 'Feriados legales',
            'dias_compensatorios' => 'Días compensatorios',
            'licencia_medica' => 'Licencia médica',
            'viaticos' => 'Viáticos',
        ];
        return view('solicitudes.create', compact('departamentos', 'usuarios', 'tipos'));
    }

    public function store(Request $request)
    {
        $payload = $request->validate([
            'tipo_solicitud' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_termino' => 'required|date|after_or_equal:fecha_inicio',
            'motivo' => 'nullable|string',
            'explicacion' => 'nullable|string',
            'directivo_asignado_id' => 'nullable|integer',
            'mensaje_para_directivo' => 'nullable|string',
            'viaticos_destino' => 'nullable|string',
            'viaticos_hora_inicio' => 'nullable|string',
            'viaticos_hora_termino' => 'nullable|string',
            'licencia_folio' => 'nullable|string',
            'licencia_tipo' => 'nullable|string',
            'licencia_emisor' => 'nullable|string',
            'usar_firmagob' => 'nullable',
        ]);
        $payload['usar_firmagob'] = $request->boolean('usar_firmagob');

        $res = $this->client()->post($this->api() . '/api/sgd-solicitudes/crear', $payload);
        if ($res->failed() || !($res->json()['ok'] ?? false)) {
            toast($res->json()['message'] ?? 'No se pudo crear la solicitud', 'error');
            return back()->withInput();
        }
        toast('Solicitud creada correctamente', 'success');
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
        $res = $this->client()->delete($this->api() . '/api/sgd-solicitudes/eliminar', ['id' => $id]);
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
        $payload = $request->all();
        if (!empty($payload['id'])) {
            $res = $this->client()->put($this->api() . '/api/sgd-solicitudes/tipo-documentos', $payload);
        } else {
            $res = $this->client()->post($this->api() . '/api/sgd-solicitudes/tipo-documentos', $payload);
        }
        toast(($res->json()['ok'] ?? false) ? 'Plantilla guardada' : ($res->json()['message'] ?? 'Error'), ($res->json()['ok'] ?? false) ? 'success' : 'error');
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
