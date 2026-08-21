<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Saldos y movimientos deben aceptar medios días (0.5), igual que sol_solicitudes.total_dias.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->toDecimal('sol_saldos_anuales', [
            'dias_administrativos',
            'feriados_legales',
            'dias_compensatorios',
        ], true);

        $this->toDecimal('sol_dia_administrativo_movimientos', ['dias'], false);
    }

    public function down(): void
    {
        // no-op: no volver a enteros (perdería medios días)
    }

    protected function toDecimal(string $table, array $cols, bool $withDefaultZero): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }
        foreach ($cols as $col) {
            if (!Schema::hasColumn($table, $col)) {
                continue;
            }
            try {
                DB::statement("ALTER TABLE {$table} ALTER COLUMN {$col} TYPE numeric(6,1) USING {$col}::numeric(6,1)");
                if ($withDefaultZero) {
                    DB::statement("ALTER TABLE {$table} ALTER COLUMN {$col} SET DEFAULT 0");
                }
            } catch (\Throwable $e) {
                try {
                    $null = $withDefaultZero ? 'NOT NULL DEFAULT 0' : 'NOT NULL';
                    DB::statement("ALTER TABLE {$table} MODIFY {$col} DECIMAL(6,1) {$null}");
                } catch (\Throwable $e2) {
                }
            }
        }
    }
};
