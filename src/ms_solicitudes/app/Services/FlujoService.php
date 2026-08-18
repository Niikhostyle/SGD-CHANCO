<?php

namespace App\Services;

use App\Models\Buzon;
use App\Models\BuzonUsuario;
use App\Models\SolSolicitud;
use App\Models\SolSolicitudBitacora;
use App\Models\SolSolicitudBuzon;
use App\Models\SolTipoDocumento;
use Exception;
use Illuminate\Support\Facades\DB;

class FlujoService
{
    public function snapshot(SolTipoDocumento $tipo): array
    {
        $tipo->load('buzonesFlujo');
        return [
            'id' => $tipo->id,
            'nombre' => $tipo->nombre,
            'tipo_solicitud' => $tipo->tipo_solicitud,
            'categoria' => $tipo->categoria,
            'consume_saldo' => (bool) $tipo->consume_saldo,
            'requiere_fe' => (bool) $tipo->requiere_fe,
            'numero_firmas' => (int) $tipo->numero_firmas,
            'primer_buzon_editable' => (bool) $tipo->primer_buzon_editable,
            'plantilla_cuerpo_html' => $tipo->plantilla_cuerpo_html,
            'plantilla_encabezado_html' => $tipo->plantilla_encabezado_html,
            'plantilla_distribucion_html' => $tipo->plantilla_distribucion_html,
            'buzones_flujo' => $tipo->buzonesFlujo->map(function ($p) {
                return [
                    'id_buzon' => (int) $p->id_buzon,
                    'nombre_buzon' => $p->nombre_buzon,
                    'orden' => (int) $p->orden,
                    'acciones' => $p->acciones ?: ['firmar'],
                ];
            })->values()->toArray(),
        ];
    }

    public function instanciarPasos(SolSolicitud $sol, array $snapshot, ?int $idBuzonDestino = null): void
    {
        $editable = !array_key_exists('primer_buzon_editable', $snapshot) || !empty($snapshot['primer_buzon_editable']);
        $flujo = $this->asegurarCadena($snapshot['buzones_flujo'] ?? [], $idBuzonDestino, $editable);

        if (!$flujo) {
            throw new Exception('Configure el flujo de buzones del tipo o seleccione un buzón destino.');
        }

        foreach ($flujo as $i => $paso) {
            SolSolicitudBuzon::create([
                'solicitud_id' => $sol->id,
                'id_buzon' => $paso['id_buzon'],
                'nombre_buzon' => $paso['nombre_buzon'] ?? '',
                'orden' => $paso['orden'] ?? ($i + 1),
                'estado' => $i === 0 ? 'pendiente' : 'por_recibir',
                'acciones' => $paso['acciones'] ?? ['firmar'],
            ]);
        }

        $sol->id_buzon_destino = $flujo[0]['id_buzon'];
        $sol->paso_actual = 1;
        $sol->json_tipo = array_merge($snapshot, ['buzones_flujo' => $flujo]);
        $sol->estado = 'pendiente';
        $sol->directivo_asignado_id = $this->titularBuzon((int) $flujo[0]['id_buzon']) ?: $sol->directivo_asignado_id;
        $sol->save();

        $this->registrar($sol, 'enviar', (int) $flujo[0]['id_buzon'], $sol->user_id, 'Enviada al buzón ' . ($flujo[0]['nombre_buzon'] ?? ''));
    }

    public function asegurarCadena(array $flujo, ?int $idBuzonDestino, bool $editable): array
    {
        if ($idBuzonDestino) {
            $buzon = Buzon::find($idBuzonDestino);
            $primero = [
                'id_buzon' => $idBuzonDestino,
                'nombre_buzon' => $buzon ? $buzon->nombre : 'Buzón',
                'orden' => 1,
                'acciones' => ['visar', 'firmar'],
            ];
            if (!$flujo) {
                $flujo = [$primero];
            } elseif ($editable) {
                $flujo[0]['id_buzon'] = $primero['id_buzon'];
                $flujo[0]['nombre_buzon'] = $primero['nombre_buzon'];
                if (empty($flujo[0]['acciones'])) {
                    $flujo[0]['acciones'] = ['visar', 'firmar'];
                }
            }
        }

        $ids = array_map('intval', array_column($flujo, 'id_buzon'));
        // Solo se completa Directivo → RRHH → Alcalde si el tipo no trae una cadena propia.
        if (count($flujo) <= 1) {
            $rrhh = $this->resolverBuzonConfig('buzon_rrhh_id', ['departamento de personal', 'recursos humanos', 'rrhh']);
            $alcalde = $this->resolverBuzonConfig('buzon_alcalde_id', ['alcalde', 'alcaldía', 'alcaldia']);

            if ($rrhh && !in_array((int) $rrhh->id_buzon, $ids, true)) {
                $flujo[] = [
                    'id_buzon' => (int) $rrhh->id_buzon,
                    'nombre_buzon' => $rrhh->nombre,
                    'orden' => count($flujo) + 1,
                    'acciones' => ['firmar'],
                ];
                $ids[] = (int) $rrhh->id_buzon;
            }
            if ($alcalde && !in_array((int) $alcalde->id_buzon, $ids, true)) {
                $flujo[] = [
                    'id_buzon' => (int) $alcalde->id_buzon,
                    'nombre_buzon' => $alcalde->nombre,
                    'orden' => count($flujo) + 1,
                    'acciones' => ['firmar', 'finalizar'],
                ];
            }
        }

        foreach ($flujo as $i => &$p) {
            $p['orden'] = $i + 1;
            if (empty($p['acciones'])) {
                $p['acciones'] = ['firmar'];
            }
        }
        unset($p);

        return array_values($flujo);
    }

    public function resolverBuzonConfig(string $clave, array $nombres): ?Buzon
    {
        $id = $this->config($clave);
        if ($id) {
            $b = Buzon::find((int) $id);
            if ($b) {
                return $b;
            }
        }
        foreach ($nombres as $n) {
            $b = Buzon::whereRaw('lower(nombre) = ?', [mb_strtolower($n)])
                ->orderBy('id_buzon')
                ->first();
            if ($b) {
                return $b;
            }
        }
        foreach ($nombres as $n) {
            $b = Buzon::where(function ($q) use ($n) {
                $q->where('nombre', 'ilike', '%' . $n . '%')
                    ->orWhere('nombre_corto', 'ilike', '%' . $n . '%');
            })->where('id_tipo_buzon', 2)
                ->orderBy('id_buzon')
                ->first();
            if ($b) {
                return $b;
            }
        }
        foreach ($nombres as $n) {
            $b = Buzon::where(function ($q) use ($n) {
                $q->where('nombre', 'ilike', '%' . $n . '%')
                    ->orWhere('nombre_corto', 'ilike', '%' . $n . '%');
            })->orderBy('id_buzon')->first();
            if ($b) {
                return $b;
            }
        }
        return null;
    }

    public function config(string $clave, $default = null)
    {
        try {
            $row = DB::table('sol_configuraciones')->where('clave', $clave)->first();
            return $row && $row->valor !== null && $row->valor !== '' ? $row->valor : $default;
        } catch (Exception $e) {
            return $default;
        }
    }

    public function setConfig(string $clave, $valor): void
    {
        $now = date('Y-m-d H:i:s');
        $exists = DB::table('sol_configuraciones')->where('clave', $clave)->exists();
        if ($exists) {
            DB::table('sol_configuraciones')->where('clave', $clave)->update(['valor' => $valor, 'updated_at' => $now]);
        } else {
            DB::table('sol_configuraciones')->insert([
                'clave' => $clave,
                'valor' => $valor,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function pasoActual(SolSolicitud $sol): ?SolSolicitudBuzon
    {
        return $sol->pasos()->where('estado', 'pendiente')->orderBy('orden')->first();
    }

    public function usuarioEnBuzon(int $uid, int $idBuzon): bool
    {
        return BuzonUsuario::where('id_usuario', $uid)->where('id_buzon', $idBuzon)->exists();
    }

    public function idsBuzonesUsuario(int $uid): array
    {
        return BuzonUsuario::where('id_usuario', $uid)->pluck('id_buzon')->all();
    }

    public function titularBuzon(int $idBuzon): ?int
    {
        $t = BuzonUsuario::where('id_buzon', $idBuzon)->where('id_tipo_firma', 1)->first();
        return $t ? (int) $t->id_usuario : null;
    }

    public function puedeActuar(int $uid, SolSolicitud $sol, bool $esAdmin): bool
    {
        if ($esAdmin) {
            return true;
        }
        $paso = $this->pasoActual($sol);
        if (!$paso) {
            return false;
        }
        return $this->usuarioEnBuzon($uid, (int) $paso->id_buzon);
    }

    public function actuar(SolSolicitud $sol, int $uid, string $accion, ?string $obs, bool $esAdmin): SolSolicitud
    {
        $paso = $this->pasoActual($sol);
        if (!$paso) {
            throw new Exception('La solicitud no tiene un paso pendiente de buzón.');
        }
        if (!$esAdmin && !$this->usuarioEnBuzon($uid, (int) $paso->id_buzon)) {
            throw new Exception('No pertenece al buzón actual de esta solicitud.');
        }

        $permitidas = $paso->acciones ?: [];
        if ($accion === 'rechazar') {
            $paso->estado = 'rechazado';
            $paso->id_usuario_accion = $uid;
            $paso->observaciones = $obs;
            $paso->decidido_at = date('Y-m-d H:i:s');
            $paso->save();
            $sol->estado = 'rechazada';
            $sol->observaciones = $obs;
            $sol->save();
            $this->registrar($sol, 'rechazar', (int) $paso->id_buzon, $uid, $obs);
            return $sol->fresh();
        }

        if ($accion === 'visar' && !in_array('visar', $permitidas, true) && !in_array('firmar', $permitidas, true) && !empty($permitidas)) {
            throw new Exception('Este paso no permite visar.');
        }
        if ($accion === 'firmar' && !in_array('firmar', $permitidas, true) && !empty($permitidas) && !in_array('visar', $permitidas, true)) {
            throw new Exception('Este paso no permite firmar.');
        }

        $paso->estado = $accion === 'visar' ? 'visado' : 'firmado';
        $paso->id_usuario_accion = $uid;
        $paso->observaciones = $obs;
        $paso->decidido_at = date('Y-m-d H:i:s');
        $paso->save();
        $this->marcarActor($sol, $uid, $paso, $obs);
        $this->registrar($sol, $accion, (int) $paso->id_buzon, $uid, $obs);

        $siguiente = $sol->pasos()->where('orden', '>', $paso->orden)->orderBy('orden')->first();
        $esFinal = in_array('finalizar', $permitidas, true) || !$siguiente;

        if ($esFinal) {
            $sol->estado = 'completada';
            $sol->save();
            $this->registrar($sol, 'finalizar', (int) $paso->id_buzon, $uid, 'Trámite completado');
        } else {
            $siguiente->estado = 'pendiente';
            $siguiente->save();
            $sol->id_buzon_destino = $siguiente->id_buzon;
            $sol->paso_actual = $siguiente->orden;
            $sol->estado = 'pendiente';
            $sol->save();
            $this->registrar($sol, 'derivar', (int) $siguiente->id_buzon, $uid, 'Derivada a ' . $siguiente->nombre_buzon);
        }

        return $sol->fresh();
    }

    public function marcarActor(SolSolicitud $sol, int $uid, SolSolicitudBuzon $paso, ?string $obs = null): void
    {
        $nombre = mb_strtolower((string) ($paso->nombre_buzon ?? ''));
        $now = date('Y-m-d H:i:s');
        if (mb_strpos($nombre, 'alcalde') !== false || mb_strpos($nombre, 'alcald') !== false) {
            $sol->alcalde_id = $uid;
            $sol->alcalde_decidido_at = $now;
            $sol->alcalde_observaciones = $obs;
        } elseif (preg_match('/rrhh|recursos humanos|personal/', $nombre)) {
            $sol->rrhh_id = $uid;
            $sol->rrhh_decidido_at = $now;
            $sol->rrhh_observaciones = $obs;
        } elseif ((int) $paso->orden === 1) {
            $sol->directivo_id = $uid;
            $sol->directivo_decidido_at = $now;
            $sol->directivo_observaciones = $obs;
        }
        $sol->save();
    }

    public function registrar(SolSolicitud $sol, string $accion, ?int $idBuzon, ?int $uid, ?string $comentario = null): void
    {
        SolSolicitudBitacora::create([
            'solicitud_id' => $sol->id,
            'id_buzon' => $idBuzon,
            'id_usuario' => $uid,
            'accion' => $accion,
            'comentario' => $comentario,
        ]);
    }

    public function catalogoBuzones(?string $texto = null)
    {
        $q = Buzon::query()->orderBy('nombre');
        if ($texto) {
            $q->where(function ($qq) use ($texto) {
                $qq->where('nombre', 'ilike', '%' . $texto . '%')
                    ->orWhere('nombre_corto', 'ilike', '%' . $texto . '%');
            });
        }
        return $q->get(['id_buzon', 'id_tipo_buzon', 'nombre', 'nombre_corto', 'cargo_firma']);
    }
}
