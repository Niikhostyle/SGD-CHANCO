<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sol_tipo_documentos')) {
            return;
        }

        $ids = DB::table('sol_tipo_documentos')
            ->where(function ($q) {
                $q->whereIn('categoria', ['dias', 'compensatorios'])
                    ->orWhereIn('tipo_solicitud', ['dias_administrativos', 'dias_compensatorios']);
            })
            ->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        if (Schema::hasTable('sol_tipo_documento_buzon')) {
            DB::table('sol_tipo_documento_buzon')->whereIn('sol_tipo_documento_id', $ids)->delete();
        }
        if (Schema::hasTable('sol_solicitudes') && Schema::hasColumn('sol_solicitudes', 'sol_tipo_documento_id')) {
            DB::table('sol_solicitudes')->whereIn('sol_tipo_documento_id', $ids)->update(['sol_tipo_documento_id' => null]);
        }
        DB::table('sol_tipo_documentos')->whereIn('id', $ids)->delete();
    }

    public function down(): void
    {
        // Las plantillas se recrean a mano (administrativos y compensatorios por separado).
    }
};
