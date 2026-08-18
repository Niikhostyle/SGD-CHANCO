<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('sol_solicitudes', 'id_documento')) {
            Schema::table('sol_solicitudes', function (Blueprint $table) {
                $table->unsignedBigInteger('id_documento')->nullable();
                $table->unsignedBigInteger('id_documento_buzon')->nullable();
                $table->unsignedInteger('id_tipo_documento')->nullable();
                $table->index('id_documento');
            });
        }

        if (!Schema::hasColumn('sol_tipo_documentos', 'id_tipo_documento')) {
            Schema::table('sol_tipo_documentos', function (Blueprint $table) {
                $table->unsignedInteger('id_tipo_documento')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sol_solicitudes', 'id_documento')) {
            Schema::table('sol_solicitudes', function (Blueprint $table) {
                $table->dropColumn(['id_documento', 'id_documento_buzon', 'id_tipo_documento']);
            });
        }
        if (Schema::hasColumn('sol_tipo_documentos', 'id_tipo_documento')) {
            Schema::table('sol_tipo_documentos', function (Blueprint $table) {
                $table->dropColumn('id_tipo_documento');
            });
        }
    }
};
