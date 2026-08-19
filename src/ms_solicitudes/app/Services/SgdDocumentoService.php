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
        if ($requiereFe && $nFirmas < 1) {
            $nFirmas = 1;
        }

        $payload = [
            'nombre' => $nombre,
            'nombre_corto' => $corto,
            'nombre_corto_firma' => ($plantilla && $plantilla->nombre_corto_firma) ? $plantilla->nombre_corto_firma : $corto,
            'descripcion' => ($plantilla && $plantilla->descripcion) ? $plantilla->descripcion : ('Solicitud: ' . $nombre),
            'id_tipo_origen' => (int) ($plantilla && $plantilla->id_tipo_origen ? $plantilla->id_tipo_origen : 1),
            'id_tipo_flujo' => (int) ($plantilla && $plantilla->id_tipo_flujo ? $plantilla->id_tipo_flujo : 2),
            'id_tipo_avance' => (int) ($plantilla && $plantilla->id_tipo_avance ? $plantilla->id_tipo_avance : 1),
            'id_tipo_folio' => (int) ($plantilla && $plantilla->id_tipo_folio ? $plantilla->id_tipo_folio : 3),
            'id_tipo_asignacion_folio' => (int) ($plantilla && $plantilla->id_tipo_asignacion_folio ? $plantilla->id_tipo_asignacion_folio : 3),
            'requiere_fe' => $requiereFe,
            'numero_firmas' => $nFirmas ?: null,
            'derivar_primera_firma' => (int) ($plantilla && $plantilla->derivar_primera_firma ? 1 : 0),
            'derivar_ultima_firma' => (int) ($plantilla && $plantilla->derivar_ultima_firma ? 1 : 0),
            'buzon_primera_firma' => $plantilla ? ($plantilla->buzon_primera_firma ?: null) : null,
            'buzon_ultima_firma' => $plantilla ? ($plantilla->buzon_ultima_firma ?: null) : null,
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

        if ($plantilla && (int) $plantilla->id_tipo_documento !== $idTipo) {
            $plantilla->id_tipo_documento = $idTipo;
            $plantilla->save();
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
            if ($rrhh) {
                $pasos[] = ['id_buzon' => (int) $rrhh->id_buzon, 'acciones' => [6, 7, 11]];
            }
            if ($alcalde) {
                $pasos[] = ['id_buzon' => (int) $alcalde->id_buzon, 'acciones' => [7, 10]];
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
        $idTipo = ($plantilla && $plantilla->id_tipo_documento)
            ? (int) $plantilla->id_tipo_documento
            : $this->asegurarTipoSgd($plantilla);
        $user = $sol->usuario;
        $datosPlantilla = [
            'tipo_solicitud' => $plantilla ? $plantilla->nombre : $sol->tipo_solicitud,
            'fecha_inicio' => $sol->fecha_inicio,
            'fecha_termino' => $sol->fecha_termino,
            'total_dias' => $sol->total_dias,
            'motivo' => $sol->motivo,
            'explicacion' => $sol->explicacion ?: $sol->motivo,
            'viaticos_destino' => $sol->viaticos_destino,
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

        $crear = $this->http($sessionKey)->post($this->apiDocumentos() . '/api/sgd-documentos/crear', [
            'id_tipo_documento' => $idTipo,
            'id_nivel_acceso' => 1,
            'efectos_terceros' => false,
            'referencias' => ['respuesta_a' => null],
            'materia' => mb_substr($materia, 0, 240),
            'anterior' => null,
            'descripcion' => $sol->motivo ?: $sol->explicacion,
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

        $acciones = [6, 7, 11];
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
            'descripcion' => $sol->motivo ?: $sol->explicacion,
            'encabezado' => $encabezado,
            'cuerpo' => $cuerpo ?: '<p>Solicitud</p>',
            'distribucion' => $distribucion,
            'id_buzon' => $idOrigen,
            'contestar_hasta' => null,
            'id_usuario' => $uid,
            'destinatarioPrincipal' => $idBuzonDestino,
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

        $requiereFe = $plantilla ? (bool) $plantilla->requiere_fe : true;
        if ($requiereFe) {
            $this->firmarComoSolicitante($sessionKey, $idDocumento, $idDocBuzon, $idOrigen, $uid);
        }

        $enviar = $this->http($sessionKey)->put($this->apiDocumentos() . '/api/sgd-documentos/enviar', [
            'id_documento' => $idDocumento,
            'id_documento_buzon' => $idDocBuzon,
            'id_buzon' => $idOrigen,
            'id_usuario' => $uid,
            'destinatarioPrincipal' => $idBuzonDestino,
            'acciones_solicitadas' => $acciones,
            'destinatarioOtros' => '',
            'responder' => null,
            'id_tipo_destino' => 1,
            'carpeta' => 3,
        ]);
        if ($enviar->failed()) {
            throw new Exception('El documento SGD se creó pero no se pudo enviar al buzón: ' . $this->errorHttp($enviar));
        }

        $sol->id_documento = $idDocumento;
        $sol->id_documento_buzon = $idDocBuzon;
        $sol->id_tipo_documento = $idTipo;
        $sol->save();

        $this->flujo->registrar($sol, 'enviar_sgd', $idBuzonDestino, $uid, 'Documento SGD #' . $idDocumento . ' enviado al buzón destino (Por Recibir), firmado por el solicitante.');

        return [
            'id_documento' => $idDocumento,
            'id_documento_buzon' => $idDocBuzon,
            'id_tipo_documento' => $idTipo,
            'id_buzon_origen' => $idOrigen,
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
        $nReq = max(1, (int) ($jsonTipo['numero_firmas'] ?? ($sol->tipoDocumento->numero_firmas ?? 1)));
        $nHechas = (int) DB::table('documento_buzon_bitacora as bb')
            ->join('documento_buzon as db', 'db.id_documento_buzon', '=', 'bb.id_documento_buzon')
            ->where('db.id_documento', $sol->id_documento)
            ->where('bb.id_accion', 7)
            ->count();

        if (!empty($doc->finalizado) || $final || ($nHechas >= $nReq && !$hop)) {
            $sol->estado = 'completada';
        } elseif ($sol->estado !== 'rechazada') {
            $sol->estado = 'pendiente';
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
        ])->asJson()->timeout(60);
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
