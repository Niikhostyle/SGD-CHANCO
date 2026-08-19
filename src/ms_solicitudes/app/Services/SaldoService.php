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
        $campo = self::TIPOS_CON_SALDO[$tipo] ?? null;
        if (!$campo && $categoria === 'vacaciones') {
            $campo = 'feriados_legales';
        } elseif (!$campo && $categoria === 'compensatorios') {
            $campo = 'dias_compensatorios';
        } elseif (!$campo && in_array($categoria, ['dias'], true)) {
            $campo = 'dias_administrativos';
        } elseif (!$campo) {
            $campo = 'dias_administrativos';
        }
        $saldo = $this->obtenerOCrear($userId, $anio);
        $disponible = (int) $saldo->{$campo};
        $usados = SolSolicitud::where('user_id', $userId)
            ->where('tipo_solicitud', $tipo)
            ->whereYear('fecha_inicio', $anio ?: date('Y'))
            ->whereNotIn('estado', ['rechazada'])
            ->sum('total_dias');
        $restante = $disponible - (int) $usados;
        if ($dias > $restante) {
            throw new Exception("Saldo insuficiente para {$tipo}. Disponible: {$restante}, solicitado: {$dias}.");
        }
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
