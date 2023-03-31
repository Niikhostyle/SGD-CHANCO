<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDocumentoBuzonArchivoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documento_buzon_archivo', function (Blueprint $table) {
            $table->integer('firma_anexo')->nullable();
            $table->integer('estado_firma_anexo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documento_buzon_archivo', function (Blueprint $table) {
            $table->dropColumn('firma_anexo', 'estado_firma_anexo');
        });
    }
}
