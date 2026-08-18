<?php

namespace App\Http\Controllers;

use App\Models\Buzon;
use App\Models\Sessions;
use App\Models\SolCargo;
use App\Models\SolDepartamento;
use App\Models\SolTipoDocumento;
use App\Models\SolUsuarioRol;
use App\Models\User;
use App\Services\FlujoService;
use App\Services\SgdDocumentoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    protected $roles;

    public function __construct()
    {
        $this->roles = new RolService();
    }

    protected function userId(Request $request): int
    {
        $session = Sessions::where('id', $request->header('key'))->first();
        if (!$session || !$session->user_id) {
            throw new Exception('Sesión inválida.');
        }
        return (int) $session->user_id;
    }

    protected function assertAdmin(Request $request): int
    {
        $uid = $this->userId($request);
        if (!$this->roles->isAdmin($uid)) {
            throw new Exception('Requiere rol administrador de solicitudes.');
        }
        return $uid;
    }

    public function cargos(Request $request)
    {
        try {
            $this->userId($request);
            return response()->json(['ok' => true, 'data' => SolCargo::orderBy('nombre')->get()]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function crearCargo(Request $request)
    {
        try {
            $this->assertAdmin($request);
            $nombre = $this->body($request)['nombre'] ?? $request->json('nombre') ?? $request->get('nombre');
            if (!$nombre) {
                throw new Exception('nombre requerido');
            }
            $c = SolCargo::firstOrCreate(['nombre' => $nombre]);
            return response()->json(['ok' => true, 'data' => $c]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function departamentos(Request $request)
    {
        try {
            $this->userId($request);
            return response()->json([
                'ok' => true,
                'data' => SolDepartamento::with(['directivo', 'subrogantes'])->orderBy('nombre')->get(),
            ]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function crearDepartamento(Request $request)
    {
        try {
            $this->assertAdmin($request);
            $d = $this->body($request);
            $dep = SolDepartamento::create([
                'nombre' => $d['nombre'],
                'directivo_id' => $d['directivo_id'] ?? null,
            ]);
            if (!empty($d['subrogantes']) && is_array($d['subrogantes'])) {
                $dep->subrogantes()->sync($d['subrogantes']);
            }
            return response()->json(['ok' => true, 'data' => $dep->load(['directivo', 'subrogantes'])]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function actualizarDepartamento(Request $request)
    {
        try {
            $this->assertAdmin($request);
            $d = $this->body($request);
            $dep = SolDepartamento::findOrFail($d['id']);
            $dep->update([
                'nombre' => $d['nombre'] ?? $dep->nombre,
                'directivo_id' => array_key_exists('directivo_id', $d) ? $d['directivo_id'] : $dep->directivo_id,
            ]);
            if (isset($d['subrogantes']) && is_array($d['subrogantes'])) {
                $dep->subrogantes()->sync($d['subrogantes']);
            }
            return response()->json(['ok' => true, 'data' => $dep->fresh()->load(['directivo', 'subrogantes'])]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function roles(Request $request)
    {
        try {
            $this->assertAdmin($request);
            $roles = SolUsuarioRol::with(['user', 'cargo', 'departamento'])->orderBy('id')->get();
            return response()->json(['ok' => true, 'data' => $roles]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function actualizarRol(Request $request)
    {
        try {
            $this->assertAdmin($request);
            $d = $this->body($request);
            $rol = SolUsuarioRol::firstOrNew(['user_id' => $d['user_id']]);
            $rol->rol = $d['rol'] ?? 'usuario';
            $rol->cargo_id = $d['cargo_id'] ?? null;
            $rol->departamento_id = $d['departamento_id'] ?? null;
            $rol->regimen_laboral = $d['regimen_laboral'] ?? null;
            $rol->firmagob_enabled = !empty($d['firmagob_enabled']);
            $rol->save();
            return response()->json(['ok' => true, 'data' => $rol->fresh()->load(['user', 'cargo', 'departamento'])]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function tipoDocumentos(Request $request)
    {
        try {
            $this->userId($request);
            $q = SolTipoDocumento::with('buzonesFlujo')->orderBy('categoria')->orderBy('nombre');
            if ($request->get('solo_activos')) {
                $q->where('activo', true);
            }
            return response()->json(['ok' => true, 'data' => $q->get()]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function verTipoDocumento(Request $request)
    {
        try {
            $this->userId($request);
            $t = SolTipoDocumento::with('buzonesFlujo')->findOrFail($request->get('id'));
            return response()->json(['ok' => true, 'data' => $t]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function crearTipoDocumento(Request $request)
    {
        try {
            $this->assertAdmin($request);
            $d = $this->body($request);
            if (empty($d['nombre']) || empty($d['tipo_solicitud'])) {
                throw new Exception('nombre y tipo_solicitud son obligatorios.');
            }
            DB::beginTransaction();
            $t = SolTipoDocumento::create($this->payloadTipo($d));
            $this->syncBuzonesFlujo($t, $d['buzones_flujo'] ?? []);
            (new SgdDocumentoService())->asegurarTipoSgd($t->fresh());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $t->fresh()->load('buzonesFlujo')]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function actualizarTipoDocumento(Request $request)
    {
        try {
            $this->assertAdmin($request);
            $d = $this->body($request);
            $t = SolTipoDocumento::findOrFail($d['id']);
            DB::beginTransaction();
            $t->fill($this->payloadTipo($d));
            $t->save();
            if (array_key_exists('buzones_flujo', $d)) {
                $this->syncBuzonesFlujo($t, $d['buzones_flujo']);
            }
            (new SgdDocumentoService())->asegurarTipoSgd($t->fresh());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $t->fresh()->load('buzonesFlujo')]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function eliminarTipoDocumento(Request $request)
    {
        try {
            $this->assertAdmin($request);
            $t = SolTipoDocumento::findOrFail($request->get('id') ?? $request->json('id'));
            $t->activo = false;
            $t->save();
            return response()->json(['ok' => true]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function buzones(Request $request)
    {
        try {
            $this->userId($request);
            $flujo = new FlujoService();
            return response()->json(['ok' => true, 'data' => $flujo->catalogoBuzones($request->get('texto_busqueda'))]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function configuraciones(Request $request)
    {
        try {
            $this->userId($request);
            $flujo = new FlujoService();
            $rrhh = $flujo->resolverBuzonConfig('buzon_rrhh_id', ['departamento de personal', 'recursos humanos', 'rrhh']);
            $alc = $flujo->resolverBuzonConfig('buzon_alcalde_id', ['alcalde', 'alcaldía', 'alcaldia']);
            return response()->json([
                'ok' => true,
                'data' => [
                    'buzon_rrhh_id' => $rrhh ? $rrhh->id_buzon : null,
                    'buzon_rrhh_nombre' => $rrhh ? $rrhh->nombre : null,
                    'buzon_alcalde_id' => $alc ? $alc->id_buzon : null,
                    'buzon_alcalde_nombre' => $alc ? $alc->nombre : null,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function guardarConfiguraciones(Request $request)
    {
        try {
            $this->assertAdmin($request);
            $d = $this->body($request);
            $flujo = new FlujoService();
            if (array_key_exists('buzon_rrhh_id', $d)) {
                $flujo->setConfig('buzon_rrhh_id', $d['buzon_rrhh_id'] ?: null);
            }
            if (array_key_exists('buzon_alcalde_id', $d)) {
                $flujo->setConfig('buzon_alcalde_id', $d['buzon_alcalde_id'] ?: null);
            }
            $sgd = new SgdDocumentoService();
            foreach (SolTipoDocumento::where('activo', true)->get() as $tipo) {
                $sgd->asegurarTipoSgd($tipo);
            }
            return $this->configuraciones($request);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    protected function payloadTipo(array $d): array
    {
        $slug = $d['tipo_solicitud'] ?? null;
        if (!$slug && !empty($d['nombre'])) {
            $slug = preg_replace('/[^a-z0-9_]+/', '_', strtolower($d['nombre']));
        }
        return [
            'tipo_solicitud' => $slug,
            'regimen_laboral' => !empty($d['regimen_laboral']) ? $d['regimen_laboral'] : null,
            'nombre' => $d['nombre'],
            'activo' => $this->asBool($d['activo'] ?? null, true),
            'categoria' => $d['categoria'] ?? 'dias',
            'consume_saldo' => $this->asBool($d['consume_saldo'] ?? null, false),
            'requiere_fe' => $this->asBool($d['requiere_fe'] ?? null, true),
            'numero_firmas' => (int) ($d['numero_firmas'] ?? 1),
            'primer_buzon_editable' => $this->asBool($d['primer_buzon_editable'] ?? null, true),
            'id_tipo_documento' => !empty($d['id_tipo_documento']) ? (int) $d['id_tipo_documento'] : null,
            'plantilla_encabezado_html' => $d['plantilla_encabezado_html'] ?? null,
            'plantilla_cuerpo_html' => $d['plantilla_cuerpo_html'] ?? '<p>Solicitud</p>',
            'plantilla_distribucion_html' => $d['plantilla_distribucion_html'] ?? null,
            'texto_documento' => $d['texto_documento'] ?? null,
        ];
    }

    protected function syncBuzonesFlujo(SolTipoDocumento $t, $pasos): void
    {
        $t->buzonesFlujo()->delete();
        if (!is_array($pasos)) {
            return;
        }
        $orden = 1;
        foreach ($pasos as $p) {
            if (empty($p['id_buzon'])) {
                continue;
            }
            $buzon = Buzon::find($p['id_buzon']);
            $acciones = $p['acciones'] ?? ['firmar'];
            if (is_string($acciones)) {
                $acciones = array_filter(explode(',', $acciones));
            }
            $t->buzonesFlujo()->create([
                'id_buzon' => (int) $p['id_buzon'],
                'nombre_buzon' => $p['nombre_buzon'] ?? ($buzon->nombre ?? ''),
                'orden' => (int) ($p['orden'] ?? $orden),
                'acciones' => array_values($acciones),
            ]);
            $orden++;
        }
    }

    protected function asBool($v, bool $default = false): bool
    {
        if ($v === null || $v === '') {
            return $default;
        }
        if (is_bool($v)) {
            return $v;
        }
        return in_array($v, [1, '1', 'true', 'on', 'yes'], true);
    }

    public function usuariosCatalogo(Request $request)
    {
        try {
            $this->userId($request);
            $users = User::orderBy('nombres')->limit(500)->get([
                'id', 'email', 'nombres', 'primer_apellido', 'segundo_apellido', 'run', 'cargo', 'id_perfil',
            ]);
            return response()->json(['ok' => true, 'data' => $users]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
