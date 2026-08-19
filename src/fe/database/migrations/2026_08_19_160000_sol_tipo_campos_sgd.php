<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sol_tipo_documentos')) {
            return;
        }
        Schema::table('sol_tipo_documentos', function (Blueprint $table) {
            if (!Schema::hasColumn('sol_tipo_documentos', 'descripcion')) {
                $table->string('descripcion', 512)->nullable();
            }
            if (!Schema::hasColumn('sol_tipo_documentos', 'nombre_corto')) {
                $table->string('nombre_corto', 64)->nullable();
            }
            if (!Schema::hasColumn('sol_tipo_documentos', 'nombre_corto_firma')) {
                $table->string('nombre_corto_firma', 64)->nullable();
            }
            if (!Schema::hasColumn('sol_tipo_documentos', 'id_tipo_origen')) {
                $table->unsignedInteger('id_tipo_origen')->nullable();
            }
            if (!Schema::hasColumn('sol_tipo_documentos', 'id_tipo_flujo')) {
                $table->unsignedInteger('id_tipo_flujo')->nullable();
            }
            if (!Schema::hasColumn('sol_tipo_documentos', 'id_tipo_avance')) {
                $table->unsignedInteger('id_tipo_avance')->nullable();
            }
            if (!Schema::hasColumn('sol_tipo_documentos', 'id_tipo_folio')) {
                $table->unsignedInteger('id_tipo_folio')->nullable();
            }
            if (!Schema::hasColumn('sol_tipo_documentos', 'id_tipo_asignacion_folio')) {
                $table->unsignedInteger('id_tipo_asignacion_folio')->nullable();
            }
            if (!Schema::hasColumn('sol_tipo_documentos', 'derivar_primera_firma')) {
                $table->unsignedTinyInteger('derivar_primera_firma')->default(0)->nullable();
            }
            if (!Schema::hasColumn('sol_tipo_documentos', 'derivar_ultima_firma')) {
                $table->unsignedTinyInteger('derivar_ultima_firma')->default(0)->nullable();
            }
            if (!Schema::hasColumn('sol_tipo_documentos', 'buzon_primera_firma')) {
                $table->unsignedInteger('buzon_primera_firma')->nullable();
            }
            if (!Schema::hasColumn('sol_tipo_documentos', 'buzon_ultima_firma')) {
                $table->unsignedInteger('buzon_ultima_firma')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('sol_tipo_documentos')) {
            return;
        }
        Schema::table('sol_tipo_documentos', function (Blueprint $table) {
            foreach ([
                'descripcion', 'nombre_corto', 'nombre_corto_firma',
                'id_tipo_origen', 'id_tipo_flujo', 'id_tipo_avance', 'id_tipo_folio',
                'id_tipo_asignacion_folio', 'derivar_primera_firma', 'derivar_ultima_firma',
                'buzon_primera_firma', 'buzon_ultima_firma',
            ] as $col) {
                if (Schema::hasColumn('sol_tipo_documentos', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
