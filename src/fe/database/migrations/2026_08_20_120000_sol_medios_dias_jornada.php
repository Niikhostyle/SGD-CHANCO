<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sol_solicitudes')) {
            return;
        }
        Schema::table('sol_solicitudes', function (Blueprint $table) {
            if (!Schema::hasColumn('sol_solicitudes', 'jornada_inicio')) {
                $table->string('jornada_inicio', 2)->nullable();
            }
            if (!Schema::hasColumn('sol_solicitudes', 'jornada_termino')) {
                $table->string('jornada_termino', 2)->nullable();
            }
        });
        try {
            DB::statement('ALTER TABLE sol_solicitudes ALTER COLUMN total_dias TYPE numeric(6,1) USING total_dias::numeric(6,1)');
        } catch (\Throwable $e) {
            // MySQL / ya decimal
            try {
                DB::statement('ALTER TABLE sol_solicitudes MODIFY total_dias DECIMAL(6,1) NOT NULL DEFAULT 1');
            } catch (\Throwable $e2) {
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('sol_solicitudes')) {
            return;
        }
        Schema::table('sol_solicitudes', function (Blueprint $table) {
            if (Schema::hasColumn('sol_solicitudes', 'jornada_inicio')) {
                $table->dropColumn('jornada_inicio');
            }
            if (Schema::hasColumn('sol_solicitudes', 'jornada_termino')) {
                $table->dropColumn('jornada_termino');
            }
        });
    }
};
