<?php

namespace App\Http\Controllers;

use App\Models\Sessions;
use App\Models\SolSolicitud;
use App\Models\SolUsuarioRol;
use App\Models\User;
use App\Services\PlantillaService;
use App\Services\PdfFirmaService;
use App\Services\RolService;
use App\Services\SaldoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SolicitudController extends Controller
{
    protected $saldos;
    protected $plantillas;
    protected $pdfs;
    protected $roles;

    public function __construct()
    {
        $this->saldos = new SaldoService();
        $this->plantillas = new PlantillaService();
        $this->pdfs = new PdfFirmaService();
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

    public function listar(Request $request)
    {
        try {
            $uid = $this->userId($request);
            $rol = $this->roles->ensureRol($uid);
            $q = SolSolicitud::with(['usuario', 'directivoAsignado', 'directivo', 'rrhh', 'alcalde'])->orderByDesc('id');

            if ($this->roles->isAdmin($uid)) {
                // ve todo
            } elseif ($rol->rol === 'directivo') {
                $q->where(function ($qq) use ($uid) {
                    $qq->where('user_id', $uid)
                        ->orWhere('directivo_asignado_id', $uid)
                        ->orWhere(function ($q2) use ($uid) {
                            $q2->where('estado', 'pendiente_directivo')
                                ->where(function ($inner) use ($uid) {
                                    $inner->where('directivo_asignado_id', $uid)->orWhereNull('directivo_asignado_id');
                                });
                        });
                });
            } elseif ($rol->rol === 'rrhh') {
                $q->where(function ($qq) use ($uid) {
                    $qq->where('user_id', $uid)->orWhere('estado', 'pendiente_rrhh')->orWhere('rrhh_id', $uid);
                });
            } elseif ($rol->rol === 'alcalde') {
                $q->where(function ($qq) use ($uid) {
                    $qq->where('user_id', $uid)->orWhere('estado', 'pendiente_alcalde')->orWhere('alcalde_id', $uid);
                });
            } else {
                $q->where('user_id', $uid);
            }

            if ($request->get('estado')) {
                $q->where('estado', $request->get('estado'));
            }
            if ($request->get('tipo')) {
                $q->where('tipo_solicitud', $request->get('tipo'));
            }

            return response()->json(['ok' => true, 'data' => $q->limit(200)->get()]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function ver(Request $request)
    {
        try {
            $s = SolSolicitud::with(['usuario', 'directivoAsignado', 'directivo', 'rrhh', 'alcalde'])
                ->findOrFail($request->get('id'));
            return response()->json(['ok' => true, 'data' => $s]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function plantilla(Request $request)
    {
        try {
            $uid = $this->userId($request);
            $rol = $this->roles->ensureRol($uid);
            $tipo = $request->get('tipo');
            $plantilla = $this->plantillas->resolver($tipo, $rol->regimen_laboral);
            if (!$plantilla) {
                return response()->json(['ok' => true, 'data' => [
                    'plantilla_cuerpo_html' => '<p>Solicito <strong>' . htmlspecialchars($tipo) . '</strong> desde {{fecha_inicio}} hasta {{fecha_termino}} ({{total_dias}} días).</p><p>Motivo: {{motivo}}</p>',
                ]]);
            }
            return response()->json(['ok' => true, 'data' => $plantilla]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function crear(Request $request)
    {
        try {
            $uid = $this->userId($request);
            $datos = $request->json()->all();
            $user = User::findOrFail($uid);
            $rol = $this->roles->ensureRol($uid);
            $rol->load('departamento');

            $tipo = $datos['tipo_solicitud'] ?? null;
            $inicio = $datos['fecha_inicio'] ?? null;
            $termino = $datos['fecha_termino'] ?? null;
            if (!$tipo || !$inicio || !$termino) {
                throw new Exception('tipo_solicitud, fecha_inicio y fecha_termino son obligatorios.');
            }

            $dias = $this->saldos->calcularDias($inicio, $termino);
            $this->saldos->validarDisponibilidad($uid, $tipo, $dias);

            $plantilla = $this->plantillas->resolver($tipo, $rol->regimen_laboral ?? ($datos['regimen_laboral'] ?? null));
            $cuerpo = $datos['documento_cuerpo_html'] ?? null;
            if (!$cuerpo && $plantilla) {
                $cuerpo = $this->plantillas->renderCuerpo($plantilla, $user, array_merge($datos, [
                    'tipo_solicitud' => $tipo,
                    'fecha_inicio' => $inicio,
                    'fecha_termino' => $termino,
                    'total_dias' => $dias,
                ]));
            }

            DB::beginTransaction();
            $sol = SolSolicitud::create([
                'user_id' => $uid,
                'directivo_asignado_id' => $datos['directivo_asignado_id'] ?? ($rol->departamento->directivo_id ?? null),
                'mensaje_para_directivo' => $datos['mensaje_para_directivo'] ?? null,
                'tipo_solicitud' => $tipo,
                'regimen_laboral' => $rol->regimen_laboral ?? ($datos['regimen_laboral'] ?? null),
                'fecha_inicio' => $inicio,
                'fecha_termino' => $termino,
                'total_dias' => $dias,
                'estado' => 'pendiente_directivo',
                'observaciones' => $datos['observaciones'] ?? null,
                'motivo' => $datos['motivo'] ?? null,
                'explicacion' => $datos['explicacion'] ?? null,
                'sobretiempo_referencia' => $datos['sobretiempo_referencia'] ?? null,
                'viaticos_destino' => $datos['viaticos_destino'] ?? null,
                'viaticos_hora_inicio' => $datos['viaticos_hora_inicio'] ?? null,
                'viaticos_hora_termino' => $datos['viaticos_hora_termino'] ?? null,
                'licencia_folio' => $datos['licencia_folio'] ?? null,
                'licencia_tipo' => $datos['licencia_tipo'] ?? null,
                'licencia_emisor' => $datos['licencia_emisor'] ?? null,
                'con_goce' => $datos['con_goce'] ?? true,
                'documento_cuerpo_html' => $cuerpo,
                'documento_distribucion_html' => $datos['documento_distribucion_html'] ?? ($plantilla->plantilla_distribucion_html ?? null),
                'solicitante_firmado_at' => date('Y-m-d H:i:s'),
            ]);

            $this->pdfs->generarPdf($sol);
            if (!empty($datos['usar_firmagob']) || $rol->firmagob_enabled) {
                $this->pdfs->firmarConFirmaGob($sol->fresh(), $uid, $request->header('key'));
            }

            DB::commit();
            $this->notificar($sol->fresh(), 'creada');

            return response()->json(['ok' => true, 'data' => $sol->fresh()->load(['usuario', 'directivoAsignado'])]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function actualizar(Request $request)
    {
        try {
            $uid = $this->userId($request);
            $datos = $request->json()->all();
            $sol = SolSolicitud::findOrFail($datos['id']);
            if ((int) $sol->user_id !== $uid && !$this->roles->isAdmin($uid)) {
                throw new Exception('No puede editar esta solicitud.');
            }
            if ($sol->estado !== 'pendiente_directivo') {
                throw new Exception('Solo se puede editar en estado pendiente_directivo.');
            }
            foreach (['motivo', 'explicacion', 'observaciones', 'documento_cuerpo_html', 'mensaje_para_directivo', 'viaticos_destino'] as $f) {
                if (array_key_exists($f, $datos)) {
                    $sol->{$f} = $datos[$f];
                }
            }
            if (!empty($datos['fecha_inicio']) && !empty($datos['fecha_termino'])) {
                $sol->fecha_inicio = $datos['fecha_inicio'];
                $sol->fecha_termino = $datos['fecha_termino'];
                $sol->total_dias = $this->saldos->calcularDias($datos['fecha_inicio'], $datos['fecha_termino']);
            }
            $sol->save();
            $this->pdfs->generarPdf($sol);
            return response()->json(['ok' => true, 'data' => $sol->fresh()]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function eliminar(Request $request)
    {
        try {
            $uid = $this->userId($request);
            $sol = SolSolicitud::findOrFail($request->get('id') ?? $request->json('id'));
            if ((int) $sol->user_id !== $uid && !$this->roles->isAdmin($uid)) {
                throw new Exception('No autorizado.');
            }
            if ($sol->estado !== 'pendiente_directivo' && !$this->roles->isAdmin($uid)) {
                throw new Exception('Solo se puede eliminar en pendiente_directivo.');
            }
            $sol->delete();
            return response()->json(['ok' => true]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function aprobarDirectivo(Request $request)
    {
        return $this->decidirEtapa($request, 'directivo', 'pendiente_directivo', 'pendiente_rrhh', true);
    }

    public function rechazarDirectivo(Request $request)
    {
        return $this->decidirEtapa($request, 'directivo', 'pendiente_directivo', 'rechazada', false);
    }

    public function firmarRrhh(Request $request)
    {
        return $this->decidirEtapa($request, 'rrhh', 'pendiente_rrhh', 'pendiente_alcalde', true);
    }

    public function rechazarRrhh(Request $request)
    {
        return $this->decidirEtapa($request, 'rrhh', 'pendiente_rrhh', 'rechazada', false);
    }

    public function firmarAlcalde(Request $request)
    {
        return $this->decidirEtapa($request, 'alcalde', 'pendiente_alcalde', 'completada', true);
    }

    public function rechazarAlcalde(Request $request)
    {
        return $this->decidirEtapa($request, 'alcalde', 'pendiente_alcalde', 'rechazada', false);
    }

    protected function decidirEtapa(Request $request, string $etapa, string $estadoActual, string $estadoNuevo, bool $aprobar)
    {
        try {
            $uid = $this->userId($request);
            $this->roles->assertRoles($uid, [$etapa, 'admin_solicitudes']);
            $datos = $request->json()->all();
            $sol = SolSolicitud::findOrFail($datos['id']);
            if ($sol->estado !== $estadoActual) {
                throw new Exception("La solicitud no está en estado {$estadoActual}.");
            }

            $rol = $this->roles->ensureRol($uid);
            if ($aprobar && !$rol->firmagob_enabled && !$this->roles->isAdmin($uid)) {
                // Admin puede omitir; roles de firma deberían tener firmagob
            }

            DB::beginTransaction();
            $obs = $datos['observaciones'] ?? null;
            if ($etapa === 'directivo') {
                $sol->directivo_id = $uid;
                $sol->directivo_decidido_at = date('Y-m-d H:i:s');
                $sol->directivo_observaciones = $obs;
            } elseif ($etapa === 'rrhh') {
                $sol->rrhh_id = $uid;
                $sol->rrhh_decidido_at = date('Y-m-d H:i:s');
                $sol->rrhh_observaciones = $obs;
            } else {
                $sol->alcalde_id = $uid;
                $sol->alcalde_decidido_at = date('Y-m-d H:i:s');
                $sol->alcalde_observaciones = $obs;
            }
            $sol->estado = $estadoNuevo;
            $sol->save();

            if ($aprobar) {
                if (!$sol->documento_pdf_path) {
                    $this->pdfs->generarPdf($sol);
                }
                try {
                    $this->pdfs->firmarConFirmaGob($sol->fresh(), $uid, $request->header('key'));
                } catch (Exception $fe) {
                    // En sandbox/local permitir continuar si falla firma externa, dejando PDF sin firma encadenada
                    if (env('PLCSGD_API_TOKEN_KEY') !== 'sandbox') {
                        throw $fe;
                    }
                }
            }

            DB::commit();
            $this->notificar($sol->fresh(), $aprobar ? 'aprobada_' . $etapa : 'rechazada_' . $etapa);
            return response()->json(['ok' => true, 'data' => $sol->fresh()->load(['usuario', 'directivo', 'rrhh', 'alcalde'])]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function pdf(Request $request)
    {
        try {
            $sol = SolSolicitud::findOrFail($request->get('id'));
            if (!$sol->documento_pdf_path) {
                $this->pdfs->generarPdf($sol);
                $sol->refresh();
            }
            $abs = storage_path('app/public/files/' . $sol->documento_pdf_path);
            if (!is_file($abs)) {
                throw new Exception('PDF no encontrado.');
            }
            return response()->json([
                'ok' => true,
                'path' => $sol->documento_pdf_path,
                'pdf_base64' => base64_encode(file_get_contents($abs)),
            ]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 404);
        }
    }

    protected function notificar(SolSolicitud $sol, string $evento): void
    {
        try {
            $to = optional($sol->usuario)->email;
            if (!$to) {
                return;
            }
            $asunto = '[SGD Solicitudes] #' . $sol->id . ' ' . $evento;
            $cuerpo = "Solicitud #{$sol->id} ({$sol->tipo_solicitud}) cambió a estado {$sol->estado} ({$evento}).";
            // Mailer del entorno; si no hay SMTP, se ignora
            @mail($to, $asunto, $cuerpo);
        } catch (Exception $e) {
            // no bloquear flujo por correo
        }
    }
}
