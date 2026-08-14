<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sol_cargos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 160)->unique();
            $table->timestamps();
        });

        Schema::create('sol_departamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 160)->unique();
            $table->unsignedBigInteger('directivo_id')->nullable();
            $table->timestamps();
            $table->foreign('directivo_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('sol_departamento_subrogante', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('departamento_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->foreign('departamento_id')->references('id')->on('sol_departamentos')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['departamento_id', 'user_id']);
        });

        Schema::create('sol_usuario_rol', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('rol', 32)->default('usuario'); // usuario|directivo|rrhh|alcalde|admin_solicitudes
            $table->unsignedBigInteger('cargo_id')->nullable();
            $table->unsignedBigInteger('departamento_id')->nullable();
            $table->string('regimen_laboral', 32)->nullable(); // administrativo|honorarios|codigo_trabajo
            $table->boolean('firmagob_enabled')->default(false);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('cargo_id')->references('id')->on('sol_cargos')->nullOnDelete();
            $table->foreign('departamento_id')->references('id')->on('sol_departamentos')->nullOnDelete();
            $table->index('rol');
        });

        Schema::create('sol_saldos_anuales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedSmallInteger('anio');
            $table->unsignedSmallInteger('dias_administrativos')->default(0);
            $table->unsignedSmallInteger('feriados_legales')->default(0);
            $table->unsignedSmallInteger('dias_compensatorios')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['user_id', 'anio']);
        });

        Schema::create('sol_dia_administrativo_movimientos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('registrado_por')->nullable();
            $table->unsignedSmallInteger('anio');
            $table->string('tipo', 32); // carga|descuento
            $table->string('permiso_tipo', 64)->default('dias_administrativos');
            $table->unsignedSmallInteger('dias');
            $table->text('motivo')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('registrado_por')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('sol_tipo_documentos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_solicitud', 64);
            $table->string('regimen_laboral', 32)->nullable();
            $table->string('nombre', 160);
            $table->boolean('activo')->default(true);
            $table->longText('plantilla_encabezado_html')->nullable();
            $table->longText('plantilla_cuerpo_html');
            $table->longText('plantilla_distribucion_html')->nullable();
            $table->longText('texto_documento')->nullable();
            $table->timestamps();
            $table->unique(['tipo_solicitud', 'regimen_laboral']);
        });

        Schema::create('sol_configuraciones', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 120)->unique();
            $table->text('valor')->nullable();
            $table->timestamps();
        });

        Schema::create('sol_solicitudes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('directivo_asignado_id')->nullable();
            $table->text('mensaje_para_directivo')->nullable();
            $table->text('otros_destinatarios')->nullable();
            $table->text('mensaje_otros_destinatarios')->nullable();
            $table->string('tipo_solicitud', 64);
            $table->string('regimen_laboral', 32)->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_termino');
            $table->unsignedSmallInteger('total_dias')->default(0);
            $table->string('estado', 64)->default('pendiente_directivo');
            $table->text('observaciones')->nullable();
            $table->text('motivo')->nullable();
            $table->text('explicacion')->nullable();
            $table->string('sobretiempo_referencia')->nullable();
            $table->string('viaticos_destino')->nullable();
            $table->string('viaticos_hora_inicio', 16)->nullable();
            $table->string('viaticos_hora_termino', 16)->nullable();
            $table->string('licencia_folio')->nullable();
            $table->string('licencia_tipo')->nullable();
            $table->string('licencia_emisor')->nullable();
            $table->string('licencia_documento_path')->nullable();
            $table->boolean('con_goce')->default(true);
            $table->longText('documento_cuerpo_html')->nullable();
            $table->longText('documento_distribucion_html')->nullable();
            $table->string('solicitante_firma_path')->nullable();
            $table->timestamp('solicitante_firmado_at')->nullable();
            $table->unsignedBigInteger('directivo_id')->nullable();
            $table->timestamp('directivo_decidido_at')->nullable();
            $table->text('directivo_observaciones')->nullable();
            $table->string('directivo_firma_path')->nullable();
            $table->unsignedBigInteger('rrhh_id')->nullable();
            $table->timestamp('rrhh_decidido_at')->nullable();
            $table->text('rrhh_observaciones')->nullable();
            $table->string('rrhh_firma_path')->nullable();
            $table->unsignedBigInteger('alcalde_id')->nullable();
            $table->timestamp('alcalde_decidido_at')->nullable();
            $table->text('alcalde_observaciones')->nullable();
            $table->string('alcalde_firma_path')->nullable();
            $table->string('documento_pdf_path')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('directivo_asignado_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('directivo_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('rrhh_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('alcalde_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['estado', 'tipo_solicitud']);
            $table->index(['user_id', 'fecha_inicio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sol_solicitudes');
        Schema::dropIfExists('sol_configuraciones');
        Schema::dropIfExists('sol_tipo_documentos');
        Schema::dropIfExists('sol_dia_administrativo_movimientos');
        Schema::dropIfExists('sol_saldos_anuales');
        Schema::dropIfExists('sol_usuario_rol');
        Schema::dropIfExists('sol_departamento_subrogante');
        Schema::dropIfExists('sol_departamentos');
        Schema::dropIfExists('sol_cargos');
    }
};
