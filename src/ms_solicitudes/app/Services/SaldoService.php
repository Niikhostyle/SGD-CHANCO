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

    public function validarDisponibilidad(int $userId, string $tipo, int $dias, ?int $anio = null, ?bool $consumeSaldo = null, ?string $categoria = null): void
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
        if ($restante <= 0) {
            throw new Exception('No tiene días disponibles de este tipo. No puede crear la solicitud.');
        }
        if ($dias > $restante) {
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
            $asig = (int) $saldo->{$campo};
            $usado = $this->usadosEnAnio($userId, $anio, $campo);
            $out[$campo] = max(0, $asig - $usado);
            $out['asignados'][$campo] = $asig;
            $out['usados'][$campo] = $usado;
        }
        return $out;
    }

    public function restante(int $userId, string $campo, ?int $anio = null): int
    {
        $r = $this->resumen($userId, $anio);
        return (int) ($r[$campo] ?? 0);
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

    protected function usadosEnAnio(int $userId, int $anio, string $campo): int
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
        return (int) $q->sum('total_dias');
    }

    public function registrarMovimiento(int $userId, int $registradoPor, int $anio, string $tipo, string $permisoTipo, int $dias, ?string $motivo): SolSaldoAnual
    {
        $saldo = $this->obtenerOCrear($userId, $anio);
        $campo = self::TIPOS_CON_SALDO[$permisoTipo] ?? null;
        if (!$campo) {
            throw new Exception('Tipo de permiso no válido para saldo.');
        }
        if ($tipo === 'carga') {
            $saldo->{$campo} = (int) $saldo->{$campo} + $dias;
        } elseif ($tipo === 'descuento') {
            $saldo->{$campo} = max(0, (int) $saldo->{$campo} - $dias);
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

    public function calcularDias(string $inicio, string $termino, ?string $categoria = null): int
    {
        $a = Carbon::parse($inicio)->startOfDay();
        $b = Carbon::parse($termino)->startOfDay();
        if ($b->lt($a)) {
            throw new Exception('La fecha de término debe ser mayor o igual a la de inicio.');
        }
        if (in_array($categoria, ['licencias'], true)) {
            return $a->diffInDays($b) + 1;
        }
        $n = FeriadosChile::contarHabiles($a, $b);
        if ($n < 1) {
            throw new Exception('El período no incluye días hábiles (lunes a viernes, sin feriados de Chile).');
        }
        return $n;
    }
}
