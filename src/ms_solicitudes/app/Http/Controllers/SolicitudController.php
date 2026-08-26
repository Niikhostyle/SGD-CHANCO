<?php

namespace App\Http\Controllers;

use App\Models\Sessions;
use App\Models\SolSolicitud;
use App\Models\User;
use App\Services\FlujoService;
use App\Services\PlantillaService;
use App\Services\PdfFirmaService;
use App\Services\RolService;
use App\Services\SaldoService;
use App\Services\SgdDocumentoService;
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
    protected $flujo;
    protected $sgd;

    public function __construct()
    {
        $this->saldos = new SaldoService();
        $this->plantillas = new PlantillaService();
        $this->pdfs = new PdfFirmaService();
        $this->roles = new RolService();
        $this->flujo = new FlujoService();
        $this->sgd = new SgdDocumentoService();
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
            $q = SolSolicitud::with([
                'usuario', 'directivoAsignado', 'directivo', 'rrhh', 'alcalde',
                'buzonDestino', 'pasos', 'tipoDocumento',
            ])->orderByDesc('id');

            $misBuzones = $this->flujo->idsBuzonesUsuario($uid);

            if ($this->roles->isAdmin($uid)) {
                // ve todo
            } else {
                $q->where(function ($qq) use ($uid, $misBuzones, $rol) {
                    $qq->where('user_id', $uid);
                    if ($misBuzones) {
                        $qq->orWhereIn('id_buzon_destino', $misBuzones)
                            ->orWhereHas('pasos', function ($p) use ($misBuzones) {
                                $p->whereIn('id_buzon', $misBuzones);
                            });
                    }
                    if ($rol->rol === 'directivo') {
                        $qq->orWhere('directivo_asignado_id', $uid)
                            ->orWhere(function ($q2) use ($uid) {
                                $q2->where('estado', 'pendiente_directivo')
                                    ->where(function ($inner) use ($uid) {
                                        $inner->where('directivo_asignado_id', $uid)->orWhereNull('directivo_asignado_id');
                                    });
                            });
                    } elseif ($rol->rol === 'rrhh') {
                        $qq->orWhere('estado', 'pendiente_rrhh')->orWhere('rrhh_id', $uid);
                    } elseif ($rol->rol === 'alcalde') {
                        $qq->orWhere('estado', 'pendiente_alcalde')->orWhere('alcalde_id', $uid);
                    }
                });
            }

            if ($request->get('estado')) {
                $q->where('estado', $request->get('estado'));
            }
            if ($request->get('tipo')) {
                $q->where('tipo_solicitud', $request->get('tipo'));
            }
            if ($request->get('bandeja')) {
                if ($misBuzones) {
                    $q->where('estado', 'pendiente')->whereIn('id_buzon_destino', $misBuzones);
                } else {
                    $q->whereRaw('1=0');
                }
            }

            return response()->json(['ok' => true, 'data' => $q->limit(200)->get()]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function ver(Request $request)
    {
        try {
            $uid = $this->userId($request);
            $s = SolSolicitud::with([
                'usuario', 'directivoAsignado', 'directivo', 'rrhh', 'alcalde',
                'buzonDestino', 'pasos.usuarioAccion', 'bitacora.usuario', 'tipoDocumento',
            ])->findOrFail($request->get('id') ?: $this->body($request)['id'] ?? null);
            $this->assertPuedeVer($uid, $s);
            $this->sgd->sincronizar($s);
            $s->refresh()->load([
                'usuario', 'directivoAsignado', 'directivo', 'rrhh', 'alcalde',
                'buzonDestino', 'pasos.usuarioAccion', 'bitacora.usuario', 'tipoDocumento',
            ]);
            $paso = $this->flujo->pasoActual($s);
            $puede = $this->roles->isAdmin($uid) || ($paso && $this->flujo->usuarioEnBuzon($uid, (int) $paso->id_buzon));
            $data = $s->toArray();
            $data['paso_actual_detalle'] = $paso ? $paso->toArray() : null;
            $data['puede_actuar'] = $puede && $s->estado === 'pendiente' && empty($s->id_documento);
            $data['es_solicitante'] = (int) $s->user_id === $uid;
            $data['usa_flujo_buzones'] = $s->pasos->count() > 0;
            $data['sgd'] = $this->sgd->detalleSgd($s);
            $idBuzonSgd = (int) ($data['sgd']['id_buzon'] ?? 0);
            // El solicitante ve su trámite aquí; el buzón SGD es para quien debe recibir/visar/firmar.
            $data['puede_abrir_buzon'] = $idBuzonSgd > 0
                && !$data['es_solicitante']
                && ($this->roles->isAdmin($uid) || $this->flujo->usuarioEnBuzon($uid, $idBuzonSgd));
            $data['saldo'] = $this->saldos->resumen((int) $s->user_id);
            $data['puede_ver_saldos'] = $this->roles->puedeGestionarSaldos($uid) || (int) $s->user_id === $uid;
            return response()->json(['ok' => true, 'data' => $data]);
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
            $plantilla = $this->plantillas->resolver($tipo, $rol->regimen_laboral, $request->get('id') ? (int) $request->get('id') : null);
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
            $datos = $this->body($request);
            $user = User::findOrFail($uid);
            $rol = $this->roles->ensureRol($uid);
            $rol->load('departamento');

            $tipoId = $datos['sol_tipo_documento_id'] ?? $datos['id_tipo'] ?? null;
            $tipoSlug = $datos['tipo_solicitud'] ?? null;
            $plantilla = $this->plantillas->resolver($tipoSlug, $rol->regimen_laboral ?? ($datos['regimen_laboral'] ?? null), $tipoId ? (int) $tipoId : null);
            if ($plantilla) {
                $tipoSlug = $plantilla->tipo_solicitud;
            }
            $inicio = $datos['fecha_inicio'] ?? null;
            $termino = $datos['fecha_termino'] ?? null;
            if (!$tipoSlug || !$inicio || !$termino) {
                throw new Exception('tipo_solicitud, fecha_inicio y fecha_termino son obligatorios.');
            }

            $jIni = $this->saldos->normalizarJornada($datos['jornada_inicio'] ?? null);
            $jFin = $this->saldos->normalizarJornada($datos['jornada_termino'] ?? null);
            $cat = $plantilla ? ($plantilla->categoria ?? null) : null;
            $mediaJornada = !empty($datos['media_jornada']) && in_array((string) $datos['media_jornada'], ['1', 'true', 'on'], true);
            $franja = $this->saldos->normalizarJornada($datos['media_franja'] ?? null);
            if (in_array($cat, ['dias', 'compensatorios'], true)) {
                if ($mediaJornada) {
                    $franja = $franja ?: ($jIni ?: 'am');
                    $jIni = $franja;
                    $jFin = $franja;
                    $termino = $inicio;
                } else {
                    $jIni = $jIni ?: 'am';
                    $jFin = $jFin ?: 'pm';
                }
            } else {
                $jIni = null;
                $jFin = null;
            }
            $dias = $this->saldos->calcularDias($inicio, $termino, $cat, $jIni, $jFin);
            $this->saldos->validarDisponibilidad(
                $uid,
                $tipoSlug,
                $dias,
                null,
                $plantilla ? (bool) $plantilla->consume_saldo : null,
                $cat
            );

            if ($plantilla) {
                $this->sgd->asegurarTipoSgd($plantilla);
                $plantilla->refresh();
            } else {
                $this->sgd->asegurarTipoSgd(null);
            }

            $cuerpo = $datos['documento_cuerpo_html'] ?? null;
            if (!$cuerpo && $plantilla) {
                $cuerpo = $this->plantillas->renderCuerpo($plantilla, $user, array_merge($datos, [
                    'tipo_solicitud' => $tipoSlug,
                    'fecha_inicio' => $inicio,
                    'fecha_termino' => $termino,
                    'jornada_inicio' => $jIni,
                    'jornada_termino' => $jFin,
                    'total_dias' => $dias,
                    'categoria' => $plantilla->categoria ?? null,
                ]));
            }

            $idBuzon = !empty($datos['id_buzon_destino']) ? (int) $datos['id_buzon_destino'] : null;

            DB::beginTransaction();
            $sol = SolSolicitud::create([
                'user_id' => $uid,
                'sol_tipo_documento_id' => $plantilla ? $plantilla->id : null,
                'directivo_asignado_id' => $datos['directivo_asignado_id'] ?? ($rol->departamento->directivo_id ?? null),
                'id_buzon_destino' => $idBuzon,
                'mensaje_para_directivo' => $datos['mensaje_para_directivo'] ?? null,
                'tipo_solicitud' => $tipoSlug,
                'regimen_laboral' => $rol->regimen_laboral ?? ($datos['regimen_laboral'] ?? null),
                'fecha_inicio' => $inicio,
                'fecha_termino' => $termino,
                'jornada_inicio' => $jIni,
                'jornada_termino' => $jFin,
                'total_dias' => $dias,
                'estado' => 'pendiente',
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
                'documento_distribucion_html' => $datos['documento_distribucion_html'] ?? ($plantilla ? $plantilla->plantilla_distribucion_html : null),
                'solicitante_firmado_at' => date('Y-m-d H:i:s'),
            ]);

            if ($plantilla) {
                $snap = $this->flujo->snapshot($plantilla);
                $this->flujo->instanciarPasos($sol, $snap, $idBuzon);
            } elseif ($idBuzon) {
                $this->flujo->instanciarPasos($sol, [
                    'primer_buzon_editable' => true,
                    'buzones_flujo' => [],
                    'requiere_fe' => true,
                ], $idBuzon);
            } else {
                $sol->estado = 'pendiente_directivo';
                $sol->save();
            }

            $this->flujo->registrar($sol, 'crear', $idBuzon, $uid, 'Solicitud creada');

            if (!$idBuzon) {
                throw new Exception('Debe seleccionar un buzón SGD de destino.');
            }

            DB::commit();

            try {
                $this->sgd->publicar(
                    $sol->load('usuario'),
                    $uid,
                    (string) $request->header('key'),
                    $idBuzon,
                    $plantilla,
                    (string) ($cuerpo ?: ''),
                    $datos['mensaje_para_directivo'] ?? ($datos['explicacion'] ?? null)
                );
            } catch (Exception $pub) {
                $this->flujo->registrar($sol->fresh(), 'error_sgd', $idBuzon, $uid, $pub->getMessage());
                return response()->json(['ok' => false, 'message' => $pub->getMessage()], 400);
            }

            try {
                $this->pdfs->generarPdf($sol->fresh());
            } catch (Exception $fe) {
                $this->flujo->registrar($sol->fresh(), 'pdf_pendiente', $idBuzon, $uid, 'PDF: ' . $fe->getMessage());
            }

            $this->notificar($sol->fresh(), 'creada');

            return response()->json(['ok' => true, 'data' => $sol->fresh()->load(['usuario', 'directivoAsignado', 'buzonDestino', 'pasos'])]);
        } catch (Exception $e) {
            try {
                DB::rollBack();
            } catch (Exception $ignored) {
            }
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function actualizar(Request $request)
    {
        try {
            $uid = $this->userId($request);
            $datos = $this->body($request);
            $sol = SolSolicitud::findOrFail($datos['id'] ?? $request->get('id'));
            if ((int) $sol->user_id !== $uid && !$this->roles->isAdmin($uid)) {
                throw new Exception('No puede editar esta solicitud.');
            }
            if (!in_array($sol->estado, ['pendiente_directivo', 'pendiente'], true)) {
                throw new Exception('Solo se puede editar en estado pendiente.');
            }
            foreach (['motivo', 'explicacion', 'observaciones', 'documento_cuerpo_html', 'mensaje_para_directivo', 'viaticos_destino'] as $f) {
                if (array_key_exists($f, $datos)) {
                    $sol->{$f} = $datos[$f];
                }
            }
            if (!empty($datos['fecha_inicio']) && !empty($datos['fecha_termino'])) {
                $sol->fecha_inicio = $datos['fecha_inicio'];
                $sol->fecha_termino = $datos['fecha_termino'];
                $cat = null;
                if ($sol->sol_tipo_documento_id) {
                    $p = $this->plantillas->resolver(null, null, (int) $sol->sol_tipo_documento_id);
                    $cat = $p ? ($p->categoria ?? null) : null;
                }
                $sol->total_dias = $this->saldos->calcularDias($datos['fecha_inicio'], $datos['fecha_termino'], $cat);
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
            if (!in_array($sol->estado, ['pendiente_directivo', 'pendiente'], true) && !$this->roles->isAdmin($uid)) {
                throw new Exception('Solo se puede eliminar en pendiente.');
            }
            $sol->delete();
            return response()->json(['ok' => true]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function actuarFlujo(Request $request)
    {
        try {
            $uid = $this->userId($request);
            $datos = $this->body($request);
            $sol = SolSolicitud::with(['pasos', 'tipoDocumento'])->findOrFail($datos['id'] ?? $request->get('id'));
            $accion = $datos['accion'] ?? '';
            if (!in_array($accion, ['visar', 'firmar', 'rechazar'], true)) {
                throw new Exception('Acción inválida.');
            }
            if ($sol->pasos->count() === 0) {
                throw new Exception('Esta solicitud no usa flujo por buzones.');
            }

            $esAdmin = $this->roles->isAdmin($uid);
            if (!$this->flujo->puedeActuar($uid, $sol, $esAdmin)) {
                throw new Exception('No pertenece al buzón actual de esta solicitud.');
            }

            $requiereFe = true;
            if (is_array($sol->json_tipo) && array_key_exists('requiere_fe', $sol->json_tipo)) {
                $requiereFe = (bool) $sol->json_tipo['requiere_fe'];
            } elseif ($sol->tipoDocumento) {
                $requiereFe = (bool) $sol->tipoDocumento->requiere_fe;
            }

            if (!$sol->documento_pdf_path) {
                $this->pdfs->generarPdf($sol);
                $sol->refresh();
            }

            if ($accion === 'firmar' && $requiereFe) {
                try {
                    $this->pdfs->firmarConFirmaGob($sol->fresh(), $uid, $request->header('key'));
                } catch (Exception $fe) {
                    if (env('PLCSGD_API_TOKEN_KEY') === 'sandbox') {
                        $this->flujo->registrar($sol->fresh(), 'firma_sandbox', $sol->id_buzon_destino, $uid, $fe->getMessage());
                    } else {
                        throw new Exception('No se pudo firmar con FirmaGob: ' . $fe->getMessage());
                    }
                }
            }

            DB::beginTransaction();
            $sol = $this->flujo->actuar($sol->fresh()->load('pasos'), $uid, $accion, $datos['observaciones'] ?? null, $esAdmin);

            DB::commit();
            $this->notificar($sol->fresh(), $accion . '_buzon');
            return response()->json([
                'ok' => true,
                'data' => $sol->fresh()->load(['usuario', 'buzonDestino', 'pasos.usuarioAccion', 'bitacora.usuario']),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
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
            $datos = $this->body($request);
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

    public function trasVisar(Request $request)
    {
        try {
            $body = $this->body($request);
            $idDocumento = (int) ($body['id_documento'] ?? $request->get('id_documento') ?? 0);
            if ($idDocumento < 1) {
                throw new Exception('id_documento es obligatorio.');
            }
            $key = (string) $request->header('key');
            $out = $this->sgd->trasVisar($idDocumento, $key);
            return response()->json(['ok' => true, 'data' => $out]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function pdf(Request $request)
    {
        try {
            $uid = $this->userId($request);
            $sol = SolSolicitud::with('pasos')->findOrFail($request->get('id') ?: ($this->body($request)['id'] ?? null));
            $this->assertPuedeVer($uid, $sol);
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

    protected function assertPuedeVer(int $uid, SolSolicitud $s): void
    {
        if ($this->roles->isAdmin($uid) || (int) $s->user_id === $uid || $this->roles->puedeGestionarSaldos($uid)) {
            return;
        }
        $mis = $this->flujo->idsBuzonesUsuario($uid);
        if ($mis) {
            if (in_array((int) $s->id_buzon_destino, $mis, true)) {
                return;
            }
            if ($s->pasos->contains(function ($p) use ($mis) {
                return in_array((int) $p->id_buzon, $mis, true);
            })) {
                return;
            }
        }
        $rol = $this->roles->ensureRol($uid);
        if (in_array($rol->rol, ['directivo', 'rrhh', 'alcalde'], true)) {
            return;
        }
        throw new Exception('No autorizado para ver esta solicitud.');
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
