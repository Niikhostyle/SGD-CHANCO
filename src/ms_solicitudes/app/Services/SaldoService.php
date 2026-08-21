<?php

namespace App\Services;

use App\Models\SolSaldoAnual;
use App\Models\SolSolicitud;
use App\Models\SolDiaAdministrativoMovimiento;
use Carbon\Carbon;
use Exception;

class SaldoService
{
    public const TIPOS_CON_SALDO = [
        'dias_administrativos' => 'dias_administrativos',
        'feriados_legales' => 'feriados_legales',
        'dias_compensatorios' => 'dias_compensatorios',
    ];

    public function obtenerOCrear(int $userId, ?int $anio = null): SolSaldoAnual
    {
        $anio = $anio ?: (int) date('Y');
        return SolSaldoAnual::firstOrCreate(
            ['user_id' => $userId, 'anio' => $anio],
            ['dias_administrativos' => 0, 'feriados_legales' => 0, 'dias_compensatorios' => 0]
        );
    }

    public function validarDisponibilidad(int $userId, string $tipo, $dias, ?int $anio = null, ?bool $consumeSaldo = null, ?string $categoria = null): void
    {
        $debeValidar = $consumeSaldo === null
            ? isset(self::TIPOS_CON_SALDO[$tipo])
            : (bool) $consumeSaldo;
        if (!$debeValidar) {
            return;
        }
        $campo = $this->campoPorPlantilla($tipo, $categoria) ?: 'dias_administrativos';
        $anio = $anio ?: (int) date('Y');
        $restante = $this->restante($userId, $campo, $anio);
        $dias = round((float) $dias, 1);
        if ($restante <= 0) {
            throw new Exception('No tiene días disponibles de este tipo. No puede crear la solicitud.');
        }
        if ($dias > $restante + 0.0001) {
            throw new Exception("Saldo insuficiente. Le quedan {$restante} día(s) y está pidiendo {$dias}.");
        }
    }

    public function resumen(int $userId, ?int $anio = null): array
    {
        $anio = $anio ?: (int) date('Y');
        $saldo = $this->obtenerOCrear($userId, $anio);
        $campos = ['dias_administrativos', 'feriados_legales', 'dias_compensatorios'];
        $out = ['anio' => $anio, 'user_id' => $userId];
        foreach ($campos as $campo) {
            $asig = (float) $saldo->{$campo};
            $usado = $this->usadosEnAnio($userId, $anio, $campo);
            $out[$campo] = max(0, round($asig - $usado, 1));
            $out['asignados'][$campo] = round($asig, 1);
            $out['usados'][$campo] = round($usado, 1);
        }
        return $out;
    }

    public function restante(int $userId, string $campo, ?int $anio = null): float
    {
        $r = $this->resumen($userId, $anio);
        return (float) ($r[$campo] ?? 0);
    }

    public function campoPorPlantilla(?string $tipo, ?string $categoria): ?string
    {
        if ($tipo && isset(self::TIPOS_CON_SALDO[$tipo])) {
            return self::TIPOS_CON_SALDO[$tipo];
        }
        if ($categoria === 'vacaciones') {
            return 'feriados_legales';
        }
        if ($categoria === 'compensatorios') {
            return 'dias_compensatorios';
        }
        if ($categoria === 'dias') {
            return 'dias_administrativos';
        }
        return null;
    }

    protected function usadosEnAnio(int $userId, int $anio, string $campo): float
    {
        $q = SolSolicitud::query()
            ->where('sol_solicitudes.user_id', $userId)
            ->whereYear('sol_solicitudes.fecha_inicio', $anio)
            ->whereNotIn('sol_solicitudes.estado', ['rechazada']);
        if ($campo === 'dias_administrativos') {
            $q->where(function ($w) {
                $w->whereIn('tipo_solicitud', ['dias_administrativos', 'dias'])
                    ->orWhereHas('tipoDocumento', function ($t) {
                        $t->where('categoria', 'dias');
                    });
            });
        } elseif ($campo === 'dias_compensatorios') {
            $q->where(function ($w) {
                $w->where('tipo_solicitud', 'dias_compensatorios')
                    ->orWhereHas('tipoDocumento', function ($t) {
                        $t->where('categoria', 'compensatorios');
                    });
            });
        } elseif ($campo === 'feriados_legales') {
            $q->where(function ($w) {
                $w->whereIn('tipo_solicitud', ['feriados_legales', 'vacaciones'])
                    ->orWhereHas('tipoDocumento', function ($t) {
                        $t->where('categoria', 'vacaciones');
                    });
            });
        }
        return (float) $q->sum('total_dias');
    }

    public function registrarMovimiento(int $userId, int $registradoPor, int $anio, string $tipo, string $permisoTipo, $dias, ?string $motivo): SolSaldoAnual
    {
        $saldo = $this->obtenerOCrear($userId, $anio);
        $campo = self::TIPOS_CON_SALDO[$permisoTipo] ?? null;
        if (!$campo) {
            throw new Exception('Tipo de permiso no válido para saldo.');
        }
        $dias = round((float) $dias, 1);
        if ($dias < 0.5) {
            throw new Exception('Debe indicar al menos 0,5 día.');
        }
        if (abs(($dias * 2) - round($dias * 2)) > 0.001) {
            throw new Exception('Los días deben ser enteros o medios días (ej. 1 o 0,5).');
        }
        if ($tipo === 'carga') {
            $saldo->{$campo} = round((float) $saldo->{$campo} + $dias, 1);
        } elseif ($tipo === 'descuento') {
            $saldo->{$campo} = max(0, round((float) $saldo->{$campo} - $dias, 1));
        } else {
            throw new Exception('Tipo de movimiento inválido.');
        }
        $saldo->save();

        SolDiaAdministrativoMovimiento::create([
            'user_id' => $userId,
            'registrado_por' => $registradoPor,
            'anio' => $anio,
            'tipo' => $tipo,
            'permiso_tipo' => $permisoTipo,
            'dias' => $dias,
            'motivo' => $motivo,
        ]);

        return $saldo->fresh();
    }

    /**
     * Calcula días (puede ser 0.5). Para administrativos/compensatorios admite jornada AM/PM.
     */
    public function calcularDias(string $inicio, string $termino, ?string $categoria = null, ?string $jornadaInicio = null, ?string $jornadaTermino = null): float
    {
        $a = Carbon::parse($inicio)->startOfDay();
        $b = Carbon::parse($termino)->startOfDay();
        if ($b->lt($a)) {
            throw new Exception('La fecha de término debe ser mayor o igual a la de inicio.');
        }
        if (in_array($categoria, ['licencias'], true)) {
            return (float) ($a->diffInDays($b) + 1);
        }

        $permiteMedio = in_array($categoria, ['dias', 'compensatorios'], true);
        $ji = $this->normalizarJornada($jornadaInicio);
        $jt = $this->normalizarJornada($jornadaTermino);
        if ($permiteMedio) {
            $ji = $ji ?: 'am';
            $jt = $jt ?: 'pm';
            if ($a->equalTo($b) && $ji === 'pm' && $jt === 'am') {
                throw new Exception('En el mismo día no puede iniciar en PM y terminar en AM.');
            }
        }

        $habiles = FeriadosChile::listarHabiles($a, $b);
        $n = count($habiles);
        if ($n < 1) {
            throw new Exception('El período no incluye días hábiles (lunes a viernes, sin feriados de Chile).');
        }
        if (!$permiteMedio) {
            return (float) $n;
        }

        if ($n === 1) {
            if ($ji === $jt) {
                return 0.5;
            }
            return 1.0;
        }

        $total = 0.0;
        foreach ($habiles as $i => $dia) {
            if ($i === 0) {
                $total += ($ji === 'pm') ? 0.5 : 1.0;
            } elseif ($i === $n - 1) {
                $total += ($jt === 'am') ? 0.5 : 1.0;
            } else {
                $total += 1.0;
            }
        }
        return max(0.5, round($total, 1));
    }

    public function normalizarJornada(?string $j): ?string
    {
        $j = strtolower(trim((string) $j));
        if ($j === 'am' || $j === 'pm') {
            return $j;
        }
        return null;
    }
}
