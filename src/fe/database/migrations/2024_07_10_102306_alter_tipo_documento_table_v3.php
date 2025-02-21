<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTipoDocumentoTableV3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('tipo_documento', 'nombre_corto_firma')) {
            Schema::table('tipo_documento', function (Blueprint $table) {
                $table->text('nombre_corto_firma')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tipo_documento', function (Blueprint $table) {
            $table->dropColumn('nombre_corto_firma');
        });
    }
}