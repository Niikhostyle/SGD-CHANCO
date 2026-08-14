<?php

namespace App\Http\Controllers;

use App\Models\Sessions;
use App\Models\SolCargo;
use App\Models\SolDepartamento;
use App\Models\SolTipoDocumento;
use App\Models\SolUsuarioRol;
use App\Models\User;
use App\Services\RolService;
use Exception;
use Illuminate\Http\Request;

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
            $nombre = $request->json('nombre');
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
            $d = $request->json()->all();
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
            $d = $request->json()->all();
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
            $d = $request->json()->all();
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
            return response()->json(['ok' => true, 'data' => SolTipoDocumento::orderBy('tipo_solicitud')->get()]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function crearTipoDocumento(Request $request)
    {
        try {
            $this->assertAdmin($request);
            $d = $request->json()->all();
            $t = SolTipoDocumento::create([
                'tipo_solicitud' => $d['tipo_solicitud'],
                'regimen_laboral' => $d['regimen_laboral'] ?? null,
                'nombre' => $d['nombre'],
                'activo' => $d['activo'] ?? true,
                'plantilla_encabezado_html' => $d['plantilla_encabezado_html'] ?? null,
                'plantilla_cuerpo_html' => $d['plantilla_cuerpo_html'] ?? '<p>Solicitud</p>',
                'plantilla_distribucion_html' => $d['plantilla_distribucion_html'] ?? null,
                'texto_documento' => $d['texto_documento'] ?? null,
            ]);
            return response()->json(['ok' => true, 'data' => $t]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function actualizarTipoDocumento(Request $request)
    {
        try {
            $this->assertAdmin($request);
            $d = $request->json()->all();
            $t = SolTipoDocumento::findOrFail($d['id']);
            $t->fill(collect($d)->only([
                'tipo_solicitud', 'regimen_laboral', 'nombre', 'activo',
                'plantilla_encabezado_html', 'plantilla_cuerpo_html',
                'plantilla_distribucion_html', 'texto_documento',
            ])->toArray());
            $t->save();
            return response()->json(['ok' => true, 'data' => $t]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
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
