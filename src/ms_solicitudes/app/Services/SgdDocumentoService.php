<?php

namespace App\Services;

use App\Models\Buzon;
use App\Models\BuzonUsuario;
use App\Models\SolSolicitud;
use App\Models\SolTipoDocumento;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SgdDocumentoService
{
    protected $flujo;

    public function __construct()
    {
        $this->flujo = new FlujoService();
    }

    public function apiDocumentos(): string
    {
        return rtrim(env('API_SGD_DOCUMENTO', 'http://sgd_ms_documentos:3333'), '/');
    }

    public function buzonOrigenUsuario(int $uid): int
    {
        $row = BuzonUsuario::query()
            ->join('buzon', 'buzon.id_buzon', '=', 'buzon_usuario.id_buzon')
            ->where('buzon_usuario.id_usuario', $uid)
            ->whereNull('buzon.deleted_at')
            ->orderByRaw('CASE WHEN buzon.id_tipo_buzon = 1 THEN 0 ELSE 1 END')
            ->orderByRaw('CASE WHEN buzon_usuario.id_tipo_firma = 1 THEN 0 ELSE 1 END')
            ->orderBy('buzon.id_buzon')
            ->select('buzon.id_buzon')
            ->first();
        if (!$row) {
            throw new Exception('No tiene un buzón SGD asignado. Pida a un administrador que lo asigne a un buzón para poder enviar solicitudes.');
        }
        return (int) $row->id_buzon;
    }

    public function asegurarTipoSgd(?SolTipoDocumento $plantilla): int
    {
        $slug = $plantilla ? $plantilla->tipo_solicitud : 'solicitud';
        $nombre = $plantilla ? $plantilla->nombre : 'Solicitud municipal';
        $corto = $plantilla && $plantilla->nombre_corto
            ? $plantilla->nombre_corto
            : ('SOL-' . strtoupper(substr(preg_replace('/[^a-z0-9]+/i', '', $slug), 0, 12)));

        $existente = null;
        if ($plantilla && $plantilla->id_tipo_documento) {
            $existente = DB::table('tipo_documento')->where('id_tipo_documento', (int) $plantilla->id_tipo_documento)->first();
        }
        if (!$existente) {
            $existente = DB::table('tipo_documento')->where('nombre_corto', $corto)->first();
        }
        $now = date('Y-m-d H:i:s');

        $nFirmas = $plantilla ? (int) $plantilla->numero_firmas : 0;
        $requiereFe = $plantilla ? (bool) $plantilla->requiere_fe : true;
        if ($requiereFe) {
            // Solicitante + director + alcalde (la visación de Personal no es FE).
            $nFirmas = $nFirmas >= 3 ? $nFirmas : 3;
        } else {
            $nFirmas = $nFirmas > 0 ? $nFirmas : 0;
        }

        $rrhh = $this->flujo->resolverBuzonConfig('buzon_rrhh_id', ['departamento de personal', 'recursos humanos', 'rrhh']);
        $idPersonal = $rrhh ? (int) $rrhh->id_buzon : null;
        $buzonUltima = $plantilla && $plantilla->buzon_ultima_firma
            ? (int) $plantilla->buzon_ultima_firma
            : $idPersonal;

        $payload = [
            'nombre' => $nombre,
            'nombre_corto' => $corto,
            'nombre_corto_firma' => ($plantilla && $plantilla->nombre_corto_firma) ? $plantilla->nombre_corto_firma : $corto,
            'descripcion' => ($plantilla && $plantilla->descripcion) ? $plantilla->descripcion : ('Solicitud: ' . $nombre),
            'id_tipo_origen' => (int) ($plantilla && $plantilla->id_tipo_origen ? $plantilla->id_tipo_origen : 1),
            'id_tipo_flujo' => (int) ($plantilla && $plantilla->id_tipo_flujo ? $plantilla->id_tipo_flujo : 1),
            'id_tipo_avance' => (int) ($plantilla && $plantilla->id_tipo_avance ? $plantilla->id_tipo_avance : 1),
            'id_tipo_folio' => (int) ($plantilla && $plantilla->id_tipo_folio ? $plantilla->id_tipo_folio : 3),
            'id_tipo_asignacion_folio' => (int) ($plantilla && $plantilla->id_tipo_asignacion_folio ? $plantilla->id_tipo_asignacion_folio : 3),
            'requiere_fe' => $requiereFe,
            'numero_firmas' => $nFirmas ?: null,
            'derivar_primera_firma' => (int) ($plantilla && $plantilla->derivar_primera_firma ? 1 : 0),
            'derivar_ultima_firma' => 1,
            'buzon_primera_firma' => $plantilla ? ($plantilla->buzon_primera_firma ?: null) : null,
            'buzon_ultima_firma' => $buzonUltima,
            'plantilla_encabezado' => $plantilla ? ($plantilla->plantilla_encabezado_html ?? null) : null,
            'plantilla_cuerpo' => $plantilla ? ($plantilla->plantilla_cuerpo_html ?? null) : null,
            'plantilla_distribucion' => $plantilla ? ($plantilla->plantilla_distribucion_html ?? null) : null,
            'updated_at' => $now,
        ];

        if ($existente) {
            $idTipo = (int) $existente->id_tipo_documento;
            DB::table('tipo_documento')->where('id_tipo_documento', $idTipo)->update($payload);
        } else {
            $payload['created_at'] = $now;
            $idTipo = (int) DB::table('tipo_documento')->insertGetId($payload, 'id_tipo_documento');
        }

        if ($plantilla) {
            $dirty = false;
            if ((int) $plantilla->id_tipo_documento !== $idTipo) {
                $plantilla->id_tipo_documento = $idTipo;
                $dirty = true;
            }
            if ($requiereFe && (int) $plantilla->numero_firmas !== $nFirmas) {
                $plantilla->numero_firmas = $nFirmas;
                $dirty = true;
            }
            if ($dirty) {
                $plantilla->save();
            }
        }

        $this->sincronizarCadenaTipo($idTipo, $plantilla);
        return $idTipo;
    }

    public function sincronizarCadenaTipo(int $idTipo, ?SolTipoDocumento $plantilla = null): void
    {
        $pasos = [];
        if ($plantilla) {
            $plantilla->loadMissing('buzonesFlujo');
            foreach ($plantilla->buzonesFlujo as $p) {
                $pasos[] = [
                    'id_buzon' => (int) $p->id_buzon,
                    'acciones' => $this->accionesSgd($p->acciones),
                ];
            }
        }
        if (!$pasos) {
            $rrhh = $this->flujo->resolverBuzonConfig('buzon_rrhh_id', ['departamento de personal', 'recursos humanos', 'rrhh']);
            $alcalde = $this->flujo->resolverBuzonConfig('buzon_alcalde_id', ['alcalde', 'alcaldía', 'alcaldia']);
            // Flujo libre: el destino inicial lo elige el solicitante (director).
            // Cadena de referencia del tipo: Personal (visar) → Alcalde (firmar) → Personal (cierre).
            if ($rrhh) {
                $pasos[] = ['id_buzon' => (int) $rrhh->id_buzon, 'acciones' => [6, 11]];
            }
            if ($alcalde) {
                $pasos[] = ['id_buzon' => (int) $alcalde->id_buzon, 'acciones' => [7, 11]];
            }
            if ($rrhh) {
                $pasos[] = ['id_buzon' => (int) $rrhh->id_buzon, 'acciones' => [10]];
            }
        }
        if (!$pasos) {
            return;
        }

        $viejos = DB::table('tipo_documento_buzon')->where('id_tipo_documento', $idTipo)->pluck('id_tipo_documento_buzon');
        if ($viejos->count()) {
            DB::table('tipo_documento_buzon_accion')->whereIn('id_tipo_documento_buzon', $viejos)->delete();
            DB::table('tipo_documento_buzon')->where('id_tipo_documento', $idTipo)->delete();
        }

        $now = date('Y-m-d H:i:s');
        $orden = 1;
        foreach ($pasos as $paso) {
            if (empty($paso['id_buzon'])) {
                continue;
            }
            $idTdb = (int) DB::table('tipo_documento_buzon')->insertGetId([
                'id_tipo_documento' => $idTipo,
                'id_buzon' => (int) $paso['id_buzon'],
                'orden' => $orden,
                'created_at' => $now,
                'updated_at' => $now,
            ], 'id_tipo_documento_buzon');
            foreach ($paso['acciones'] as $idAccion) {
                DB::table('tipo_documento_buzon_accion')->insert([
                    'id_tipo_documento_buzon' => $idTdb,
                    'id_accion' => $idAccion,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
            $orden++;
        }
    }

    protected function accionesSgd($acciones): array
    {
        if (is_string($acciones)) {
            $acciones = array_filter(explode(',', $acciones));
        }
        $acciones = is_array($acciones) ? $acciones : [];
        $ids = [];
        foreach ($acciones as $a) {
            $a = is_numeric($a) ? (int) $a : strtolower(trim((string) $a));
            if ($a === 6 || $a === 'visar') {
                $ids[] = 6;
            } elseif ($a === 7 || $a === 'firmar') {
                $ids[] = 7;
            } elseif ($a === 11 || $a === 'derivar') {
                $ids[] = 11;
            } elseif ($a === 10 || $a === 'finalizar') {
                $ids[] = 10;
            }
        }
        $ids = array_values(array_unique($ids));
        if (!$ids) {
            return [6, 7, 11];
        }
        if (in_array(7, $ids, true) && !in_array(10, $ids, true) && !in_array(11, $ids, true)) {
            $ids[] = 11;
        }
        if (in_array(7, $ids, true) && !in_array(6, $ids, true) && !in_array(10, $ids, true)) {
            array_unshift($ids, 6);
        }
        return array_values(array_unique($ids));
    }

    public function publicar(SolSolicitud $sol, int $uid, string $sessionKey, int $idBuzonDestino, ?SolTipoDocumento $plantilla, string $cuerpo, ?string $comentario): array
    {
        $idOrigen = $this->buzonOrigenUsuario($uid);
        $idTipo = $this->asegurarTipoSgd($plantilla);
        $user = $sol->usuario;
        $datosPlantilla = [
            'tipo_solicitud' => $plantilla ? $plantilla->tipo_solicitud : $sol->tipo_solicitud,
            'nombre_tipo' => $plantilla ? $plantilla->nombre : $sol->tipo_solicitud,
            'categoria' => $plantilla ? $plantilla->categoria : null,
            'fecha_inicio' => $sol->fecha_inicio,
            'fecha_termino' => $sol->fecha_termino,
            'jornada_inicio' => $sol->jornada_inicio ?? null,
            'jornada_termino' => $sol->jornada_termino ?? null,
            'total_dias' => $sol->total_dias,
            'motivo' => $sol->motivo,
            'explicacion' => $sol->explicacion ?: $sol->motivo,
            'viaticos_destino' => $sol->viaticos_destino,
            'alcalde_decision' => $sol->alcalde_decision ?? null,
            'alcalde_observaciones' => $sol->alcalde_observaciones ?? null,
        ];
        $plantillas = new PlantillaService();
        $encabezado = '';
        $distribucion = '';
        if ($plantilla) {
            $encabezado = $user
                ? $plantillas->renderHtml($plantilla->plantilla_encabezado_html, $user, $datosPlantilla)
                : ($plantilla->plantilla_encabezado_html ?? '');
            $distribucion = $user
                ? $plantillas->renderHtml($plantilla->plantilla_distribucion_html, $user, $datosPlantilla)
                : ($plantilla->plantilla_distribucion_html ?? '');
        }
        $fi = $sol->fecha_inicio instanceof \DateTimeInterface ? $sol->fecha_inicio->format('d-m-Y') : (string) $sol->fecha_inicio;
        $ft = $sol->fecha_termino instanceof \DateTimeInterface ? $sol->fecha_termino->format('d-m-Y') : (string) $sol->fecha_termino;
        $materia = ($plantilla ? $plantilla->nombre : $sol->tipo_solicitud) . ' — ' . ($user ? $user->nombreCompleto() : '') . ' (' . $fi . ' a ' . $ft . ')';

        // Flujo libre: primer destino = director elegido por el solicitante.
        $idEnviar = (int) $idBuzonDestino;
        if ($idEnviar < 1) {
            throw new Exception('Debe seleccionar el buzón del director de área.');
        }
        $acciones = [7, 11]; // firmar + derivar
        $buzonDir = Buzon::find($idEnviar);
        $saldo = (new SaldoService())->resumen((int) $sol->user_id);
        $txtDias = rtrim(rtrim(number_format((float) $sol->total_dias, 1, '.', ''), '0'), '.');
        $jIni = strtoupper((string) ($sol->jornada_inicio ?? ''));
        $jFin = strtoupper((string) ($sol->jornada_termino ?? ''));
        $txtJornada = '';
        if (in_array($jIni, ['AM', 'PM'], true) || in_array($jFin, ['AM', 'PM'], true)) {
            $txtJornada = ' Jornada: ' . ($jIni ?: '—') . ' a ' . ($jFin ?: '—') . '.';
        }
        $txtSaldo = 'Saldo ' . $saldo['anio'] . ': administrativos ' . $saldo['dias_administrativos']
            . ', feriados ' . $saldo['feriados_legales']
            . ', compensatorios ' . $saldo['dias_compensatorios']
            . '. Solicita ' . $txtDias . ' día(s).' . $txtJornada;
        $txtFlujo = 'Flujo libre: 1) firma solicitante al enviar · 2) firma director'
            . ($buzonDir ? ' (' . $buzonDir->nombre . ')' : '')
            . ' · 3) visación Departamento de Personal · 4) firma alcalde · 5) vuelve a Personal.';
        $comentario = trim((string) $comentario . "\n" . $txtSaldo . "\n" . $txtFlujo);
        $descripcion = trim((string) ($sol->motivo ?: $sol->explicacion ?: '') . ' — ' . $txtSaldo);

        $crear = $this->http($sessionKey)->post($this->apiDocumentos() . '/api/sgd-documentos/crear', [
            'id_tipo_documento' => $idTipo,
            'id_nivel_acceso' => 1,
            'efectos_terceros' => false,
            'referencias' => ['respuesta_a' => null],
            'materia' => mb_substr($materia, 0, 240),
            'anterior' => null,
            'descripcion' => $descripcion,
            'encabezado' => $encabezado,
            'cuerpo' => $cuerpo ?: '<p>Solicitud</p>',
            'distribucion' => $distribucion,
            'id_buzon' => $idOrigen,
            'contestar_hasta' => null,
            'id_usuario' => $uid,
        ]);
        if ($crear->failed() || empty($crear->json()['data']['id_documento'])) {
            throw new Exception('No se pudo crear el documento SGD: ' . $this->errorHttp($crear));
        }

        $idDocumento = (int) $crear->json()['data']['id_documento'];
        $origenHop = DB::table('documento_buzon')
            ->where('id_documento', $idDocumento)
            ->whereNull('id_documento_buzon_padre')
            ->orderBy('id_documento_buzon')
            ->first();
        if (!$origenHop) {
            throw new Exception('El documento SGD se creó pero no tiene buzón de origen.');
        }
        $idDocBuzon = (int) $origenHop->id_documento_buzon;

        $actualizar = $this->http($sessionKey)->put($this->apiDocumentos() . '/api/sgd-documentos/actualizar', [
            'id_tipo_documento' => $idTipo,
            'id_nivel_acceso' => 1,
            'id_documento' => $idDocumento,
            'id_documento_buzon' => $idDocBuzon,
            'efectos_terceros' => false,
            'respuesta_a' => null,
            'referenciaAnexos' => null,
            'materia' => mb_substr($materia, 0, 240),
            'anterior' => null,
            'descripcion' => $descripcion,
            'encabezado' => $encabezado,
            'cuerpo' => $cuerpo ?: '<p>Solicitud</p>',
            'distribucion' => $distribucion,
            'id_buzon' => $idOrigen,
            'contestar_hasta' => null,
            'id_usuario' => $uid,
            'destinatarioPrincipal' => $idEnviar,
            'destinatarioPrincipal2' => null,
            'destinatarioOtros' => '',
            'acciones_solicitadas' => $acciones,
            'comentarioPrincipal' => $comentario,
            'comentarioOtros' => null,
            'carpeta' => 3,
            'opcionGuardar' => null,
            'aParaFirma' => null,
            'fileDelete' => null,
        ]);
        if ($actualizar->failed()) {
            throw new Exception('No se pudo asignar el buzón destino SGD: ' . $this->errorHttp($actualizar));
        }

        $destHop = DB::table('documento_buzon')
            ->where('id_documento', $idDocumento)
            ->where('id_documento_buzon_padre', $idDocBuzon)
            ->where('id_tipo_destino', 1)
            ->where('id_buzon', $idEnviar)
            ->orderByDesc('id_documento_buzon')
            ->first();
        if (!$destHop) {
            throw new Exception('No se creó el buzón de destino (director).');
        }

        $sol->id_documento = $idDocumento;
        $sol->id_documento_buzon = $idDocBuzon;
        $sol->id_tipo_documento = $idTipo;
        $sol->save();

        // Firma del solicitante al enviar (antes de derivar al director).
        $this->generarPdfSgd($sessionKey, $idDocumento, $idDocBuzon, $idOrigen, $uid);
        $requiereFe = $plantilla ? (bool) $plantilla->requiere_fe : true;
        if ($requiereFe) {
            $this->firmarComoSolicitante($sessionKey, $idDocumento, $idDocBuzon, $idOrigen, $uid);
        }

        $enviar = $this->http($sessionKey)->timeout(120)->put($this->apiDocumentos() . '/api/sgd-documentos/enviar', [
            'id_documento' => $idDocumento,
            'id_documento_buzon' => $idDocBuzon,
            'id_buzon' => $idOrigen,
            'id_usuario' => $uid,
            'destinatarioPrincipal' => $idEnviar,
            'acciones_solicitadas' => $acciones,
            'destinatarioOtros' => '',
            'responder' => null,
            'id_tipo_destino' => 1,
            'carpeta' => 3,
        ]);
        if ($enviar->failed()) {
            throw new Exception('El documento SGD se creó pero no se pudo enviar al buzón del director: ' . $this->errorHttp($enviar));
        }

        $this->flujo->registrar(
            $sol,
            'enviar_sgd',
            $idEnviar,
            $uid,
            'Solicitud #' . $sol->id . ' firmada por el solicitante y enviada'
                . ($buzonDir ? ' a ' . $buzonDir->nombre : '.')
        );

        return [
            'id_documento' => $idDocumento,
            'id_documento_buzon' => $idDocBuzon,
            'id_tipo_documento' => $idTipo,
            'id_buzon_origen' => $idOrigen,
        ];
    }

    /**
     * Tras visar (acción 6): regenera el PDF con iniciales (igual que un oficio)
     * y reaplica las firmas FE ya hechas (solicitante/director) en el mismo orden.
     */
    public function trasVisar(int $idDocumento, string $sessionKey): array
    {
        $sol = \App\Models\SolSolicitud::where('id_documento', $idDocumento)->first();
        if (!$sol) {
            return ['aplicado' => false, 'motivo' => 'no_es_solicitud'];
        }

        $nVisas = (int) DB::table('documento_buzon_bitacora as bb')
            ->join('documento_buzon as db', 'db.id_documento_buzon', '=', 'bb.id_documento_buzon')
            ->where('db.id_documento', $idDocumento)
            ->where('bb.id_accion', 6)
            ->count();
        if ($nVisas < 1) {
            return ['aplicado' => false, 'motivo' => 'sin_visacion'];
        }

        $yaVisadoPdf = DB::table('sol_solicitud_bitacora')
            ->where('solicitud_id', $sol->id)
            ->where('accion', 'pdf_tras_visar')
            ->exists();
        if ($yaVisadoPdf) {
            return ['aplicado' => false, 'motivo' => 'ya_aplicado'];
        }

        $origenHop = DB::table('documento_buzon')
            ->where('id_documento', $idDocumento)
            ->whereNull('id_documento_buzon_padre')
            ->orderBy('id_documento_buzon')
            ->first();
        $idDocBuzon = $origenHop
            ? (int) $origenHop->id_documento_buzon
            : (int) ($sol->id_documento_buzon ?: 0);
        if (!$idDocBuzon) {
            throw new Exception('No se encontró el buzón de origen de la solicitud para generar el PDF.');
        }
        $idOrigen = (int) DB::table('documento_buzon')->where('id_documento_buzon', $idDocBuzon)->value('id_buzon');
        $uid = (int) $sol->user_id;
        if (!$idOrigen || !$uid) {
            throw new Exception('Faltan datos del solicitante para firmar tras la visación.');
        }

        // Firmas FE previas (solicitante, director, …) para reaplicarlas sobre el PDF con visadores.
        $firmasPrevias = DB::table('documento_buzon_bitacora as bb')
            ->join('documento_buzon as db', 'db.id_documento_buzon', '=', 'bb.id_documento_buzon')
            ->where('db.id_documento', $idDocumento)
            ->where('bb.id_accion', 7)
            ->orderBy('bb.id_documento_buzon_bitacora')
            ->select([
                'bb.id_documento_buzon_bitacora',
                'bb.id_usuario',
                'bb.id_documento_buzon',
                'db.id_buzon',
            ])
            ->get();

        // Regenera el PDF ya con la bitácora de visación (acción 6), igual que un oficio.
        $this->generarPdfSgd($sessionKey, $idDocumento, $idDocBuzon, $idOrigen, $uid, true);

        // Quitar bitácora de FE para que ms_firma recoloque los sellos desde cero en el PDF nuevo.
        $idsBitacoraFirma = $firmasPrevias->pluck('id_documento_buzon_bitacora')->filter()->values()->all();
        if ($idsBitacoraFirma) {
            DB::table('documento_buzon_bitacora')->whereIn('id_documento_buzon_bitacora', $idsBitacoraFirma)->delete();
        }

        $plantilla = $sol->tipoDocumento;
        $requiereFe = $plantilla ? (bool) $plantilla->requiere_fe : true;
        if ($requiereFe) {
            if ($firmasPrevias->count()) {
                foreach ($firmasPrevias as $f) {
                    $this->firmarComoSolicitante(
                        $sessionKey,
                        $idDocumento,
                        (int) $f->id_documento_buzon,
                        (int) $f->id_buzon,
                        (int) $f->id_usuario
                    );
                }
            } else {
                $this->firmarComoSolicitante($sessionKey, $idDocumento, $idDocBuzon, $idOrigen, $uid);
            }
        }

        $this->flujo->registrar(
            $sol,
            'pdf_tras_visar',
            $idOrigen,
            $uid,
            'PDF regenerado con visación (iniciales) e igual que oficios. Documento #' . $idDocumento . '.'
        );

        return [
            'aplicado' => true,
            'id_documento' => $idDocumento,
            'firmas_reaplicadas' => $firmasPrevias->count(),
            'visadores' => true,
        ];
    }

    protected function firmarComoSolicitante(string $sessionKey, int $idDocumento, int $idDocBuzon, int $idOrigen, int $uid): void
    {
        $apiFirma = rtrim(env('API_SGD_FIRMA', 'http://sgd_ms_firma:3333'), '/');
        $res = $this->http($sessionKey)->timeout(120)->put($apiFirma . '/api/sgd-firma/firmar_archivo', [
            'id_documento_buzon' => $idDocBuzon,
            'id_documento' => $idDocumento,
            'id_usuario' => $uid,
            'id_buzon' => $idOrigen,
        ]);
        if ($res->failed()) {
            throw new Exception('No se pudo firmar la solicitud del funcionario: ' . $this->errorHttp($res));
        }
    }

    protected function generarPdfSgd(string $sessionKey, int $idDocumento, int $idDocBuzon, int $idOrigen, int $uid, bool $regenerar = false): void
    {
        $api = rtrim(env('API_SGD_ARCHIVOS', 'http://sgd_ms_archivos:3333'), '/');
        $payload = [
            'id_documento' => $idDocumento,
            'id_documento_buzon' => $idDocBuzon,
            'id_usuario' => $uid,
            'id_buzon' => $idOrigen,
            'generaFolio' => 0,
            'forzar' => 1,
        ];
        if ($regenerar) {
            $payload['regenerar'] = 1;
        }
        $res = $this->http($sessionKey)->timeout(120)->put($api . '/api/sgd-archivos/generar_archivo_pdf', $payload);
        if ($res->failed()) {
            $msg = $this->errorHttp($res);
            if (stripos($msg, 'ya fue generado') === false) {
                throw new Exception('No se pudo generar el PDF de la solicitud: ' . $msg);
            }
        }
        $existe = DB::table('documento_buzon_archivo as a')
            ->join('documento_buzon as db', 'db.id_documento_buzon', '=', 'a.id_documento_buzon')
            ->where('db.id_documento', $idDocumento)
            ->where('a.id_tipo_archivo', 1)
            ->where('a.version', 1)
            ->exists();
        if (!$existe) {
            throw new Exception('El PDF de la solicitud no quedó adjunto al documento.');
        }
    }

    public function hopActual(?int $idDocumento): ?object
    {
        if (!$idDocumento) {
            return null;
        }
        return DB::table('documento_buzon as db')
            ->join('buzon as b', 'b.id_buzon', '=', 'db.id_buzon')
            ->leftJoin('estado_documento as e', 'e.id_estado_documento', '=', 'db.id_estado_documento')
            ->leftJoin('carpeta as c', 'c.id_carpeta', '=', 'db.id_carpeta')
            ->where('db.id_documento', $idDocumento)
            ->whereIn('db.id_carpeta', [1, 2])
            ->whereIn('db.id_estado_documento', [3, 4, 8, 9, 11])
            ->orderByDesc('db.id_documento_buzon')
            ->select([
                'db.id_documento_buzon',
                'db.id_buzon',
                'db.id_carpeta',
                'db.id_estado_documento',
                'b.nombre as nombre_buzon',
                'e.nombre as estado_nombre',
                'c.nombre as carpeta_nombre',
            ])
            ->first();
    }

    public function sincronizar(SolSolicitud $sol): SolSolicitud
    {
        if (!$sol->id_documento) {
            return $sol;
        }
        $hop = $this->hopActual((int) $sol->id_documento);
        $doc = DB::table('documento')->where('id_documento', $sol->id_documento)->first();

        if ($hop) {
            $sol->id_buzon_destino = (int) $hop->id_buzon;
            if (in_array((int) $hop->id_estado_documento, [9, 10], true)) {
                // sigue pendiente de derivar o de siguiente buzón
            }
        }

        $final = DB::table('documento_buzon')
            ->where('id_documento', $sol->id_documento)
            ->whereIn('id_estado_documento', [13, 6])
            ->exists();
        $jsonTipo = [];
        if (!empty($doc->json_tipo_documento)) {
            $jsonTipo = is_string($doc->json_tipo_documento)
                ? (json_decode($doc->json_tipo_documento, true) ?: [])
                : (array) $doc->json_tipo_documento;
        }
        $nReq = max(1, (int) ($jsonTipo['numero_firmas'] ?? ($sol->tipoDocumento->numero_firmas ?? 4)));
        $nHechas = (int) DB::table('documento_buzon_bitacora as bb')
            ->join('documento_buzon as db', 'db.id_documento_buzon', '=', 'bb.id_documento_buzon')
            ->where('db.id_documento', $sol->id_documento)
            ->where('bb.id_accion', 7)
            ->count();

        $alc = $this->flujo->resolverBuzonConfig('buzon_alcalde_id', ['alcalde', 'alcaldía', 'alcaldia']);
        $firmaAlc = null;
        if ($alc) {
            $firmaAlc = DB::table('documento_buzon_bitacora as bb')
                ->join('documento_buzon as db', 'db.id_documento_buzon', '=', 'bb.id_documento_buzon')
                ->where('db.id_documento', $sol->id_documento)
                ->where('db.id_buzon', $alc->id_buzon)
                ->where('bb.id_accion', 7)
                ->orderByDesc('bb.id_documento_buzon_bitacora')
                ->select('bb.id_usuario', 'bb.comentario', 'bb.mensaje_respuesta')
                ->first();
        }

        if (!empty($doc->finalizado) || $final || $firmaAlc || ($nHechas >= $nReq && !$hop)) {
            $sol->estado = 'completada';
        } elseif ($sol->estado !== 'rechazada') {
            $sol->estado = 'pendiente';
        }

        if ($alc && empty($sol->alcalde_decision) && $firmaAlc) {
            $sol->alcalde_decision = 'autorizado';
            $sol->alcalde_id = $firmaAlc->id_usuario ?: $sol->alcalde_id;
            $sol->alcalde_decidido_at = $sol->alcalde_decidido_at ?: date('Y-m-d H:i:s');
            $obs = $firmaAlc->mensaje_respuesta ?: $firmaAlc->comentario;
            if ($obs) {
                $sol->alcalde_observaciones = $obs;
            }
        }
        if ($sol->estado === 'rechazada' && $alc && empty($sol->alcalde_decision)) {
            $sol->alcalde_decision = 'denegado';
        }

        $sol->save();
        return $sol;
    }

    public function detalleSgd(SolSolicitud $sol): ?array
    {
        if (!$sol->id_documento) {
            return null;
        }
        $hop = $this->hopActual((int) $sol->id_documento);
        $buzon = $hop ? Buzon::find($hop->id_buzon) : $sol->buzonDestino;
        return [
            'id_documento' => (int) $sol->id_documento,
            'id_documento_buzon' => $hop ? (int) $hop->id_documento_buzon : (int) $sol->id_documento_buzon,
            'id_buzon' => $hop ? (int) $hop->id_buzon : (int) $sol->id_buzon_destino,
            'nombre_buzon' => $hop->nombre_buzon ?? ($buzon->nombre ?? null),
            'id_carpeta' => $hop->id_carpeta ?? null,
            'carpeta' => $hop->carpeta_nombre ?? null,
            'estado_documento' => $hop->estado_nombre ?? null,
            'id_estado_documento' => $hop->id_estado_documento ?? null,
        ];
    }

    protected function http(string $key)
    {
        return Http::withHeaders([
            'key' => $key,
        ])->asJson()->timeout(120);
    }

    protected function errorHttp($res): string
    {
        $json = $res->json();
        if (is_array($json)) {
            return $json['data']['comentario'] ?? $json['message'] ?? $res->body();
        }
        return $res->body() ?: ('HTTP ' . $res->status());
    }
}
