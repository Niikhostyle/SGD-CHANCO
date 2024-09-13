<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTipoDocumentoTableV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tipo_documento', function (Blueprint $table) {
            $table->integer('derivar_primera_firma')->default(0)->nullable();
            $table->integer('derivar_ultima_firma')->default(0)->nullable();
            $table->integer('buzon_primera_firma')->default(0)->nullable();
            $table->integer('buzon_ultima_firma')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
