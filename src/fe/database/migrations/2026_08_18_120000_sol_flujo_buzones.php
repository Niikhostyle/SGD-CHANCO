<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('sol_tipo_documentos', 'categoria')) {
            Schema::table('sol_tipo_documentos', function (Blueprint $table) {
                $table->string('categoria', 32)->default('dias');
                $table->boolean('consume_saldo')->default(false);
                $table->boolean('requiere_fe')->default(true);
                $table->unsignedTinyInteger('numero_firmas')->default(1);
                $table->boolean('primer_buzon_editable')->default(true);
            });
        }

        DB::table('sol_tipo_documentos')->whereIn('tipo_solicitud', [
            'dias_administrativos', 'dias_compensatorios',
        ])->update(['categoria' => 'dias', 'consume_saldo' => true]);

        DB::table('sol_tipo_documentos')->where('tipo_solicitud', 'feriados_legales')
            ->update(['categoria' => 'vacaciones', 'consume_saldo' => true]);

        DB::table('sol_tipo_documentos')->where('tipo_solicitud', 'viaticos')
            ->update(['categoria' => 'viaticos', 'consume_saldo' => false]);

        DB::table('sol_tipo_documentos')->where('tipo_solicitud', 'licencia_medica')
            ->update(['categoria' => 'licencias', 'consume_saldo' => false]);

        if (!Schema::hasTable('sol_tipo_documento_buzon')) {
            Schema::create('sol_tipo_documento_buzon', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sol_tipo_documento_id');
                $table->unsignedBigInteger('id_buzon');
                $table->string('nombre_buzon', 200)->nullable();
                $table->unsignedSmallInteger('orden')->default(1);
                $table->json('acciones')->nullable();
                $table->timestamps();
                $table->foreign('sol_tipo_documento_id', 'fk_sol_tipo_doc_buzon_tipo')
                    ->references('id')->on('sol_tipo_documentos')->cascadeOnDelete();
                $table->index(['sol_tipo_documento_id', 'orden']);
            });
        }

        if (!Schema::hasColumn('sol_solicitudes', 'sol_tipo_documento_id')) {
            Schema::table('sol_solicitudes', function (Blueprint $table) {
                $table->unsignedBigInteger('sol_tipo_documento_id')->nullable();
                $table->unsignedBigInteger('id_buzon_destino')->nullable();
                $table->unsignedSmallInteger('paso_actual')->default(0);
                $table->json('json_tipo')->nullable();
                $table->foreign('sol_tipo_documento_id', 'fk_sol_solicitud_tipo')
                    ->references('id')->on('sol_tipo_documentos')->nullOnDelete();
                $table->index('id_buzon_destino');
            });
        }

        if (!Schema::hasTable('sol_solicitud_buzon')) {
            Schema::create('sol_solicitud_buzon', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('solicitud_id');
                $table->unsignedBigInteger('id_buzon');
                $table->string('nombre_buzon', 200)->nullable();
                $table->unsignedSmallInteger('orden')->default(1);
                $table->string('estado', 32)->default('por_recibir');
                $table->json('acciones')->nullable();
                $table->unsignedBigInteger('id_usuario_accion')->nullable();
                $table->text('observaciones')->nullable();
                $table->timestamp('decidido_at')->nullable();
                $table->timestamps();
                $table->foreign('solicitud_id', 'fk_sol_sol_buzon_sol')
                    ->references('id')->on('sol_solicitudes')->cascadeOnDelete();
                $table->foreign('id_usuario_accion', 'fk_sol_sol_buzon_user')
                    ->references('id')->on('users')->nullOnDelete();
                $table->index(['id_buzon', 'estado']);
            });
        }

        if (!Schema::hasTable('sol_solicitud_bitacora')) {
            Schema::create('sol_solicitud_bitacora', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('solicitud_id');
                $table->unsignedBigInteger('id_buzon')->nullable();
                $table->unsignedBigInteger('id_usuario')->nullable();
                $table->string('accion', 64);
                $table->text('comentario')->nullable();
                $table->timestamps();
                $table->foreign('solicitud_id', 'fk_sol_bitacora_sol')
                    ->references('id')->on('sol_solicitudes')->cascadeOnDelete();
                $table->foreign('id_usuario', 'fk_sol_bitacora_user')
                    ->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sol_solicitud_bitacora');
        Schema::dropIfExists('sol_solicitud_buzon');
        Schema::table('sol_solicitudes', function (Blueprint $table) {
            $table->dropForeign('fk_sol_solicitud_tipo');
            $table->dropColumn(['sol_tipo_documento_id', 'id_buzon_destino', 'paso_actual', 'json_tipo']);
        });
        Schema::dropIfExists('sol_tipo_documento_buzon');
        Schema::table('sol_tipo_documentos', function (Blueprint $table) {
            $table->dropColumn([
                'categoria', 'consume_saldo', 'requiere_fe',
                'numero_firmas', 'primer_buzon_editable',
            ]);
        });
    }
};
