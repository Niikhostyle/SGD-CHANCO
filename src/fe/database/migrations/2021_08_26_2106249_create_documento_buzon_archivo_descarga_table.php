<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentoBuzonArchivoDescargaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documento_buzon_archivo_descarga', function (Blueprint $table) {
            $table->bigIncrements('id_documento_buzon_archivo_descarga');
            $table->bigInteger('id_documento_buzon_archivo');
            $table->bigInteger('id_buzon_usuario');
            $table->dateTime('fecha')->nullable();
            $table->timestamps();
            $table->foreign('id_buzon_usuario', 'fk_documento_buzon_archivo_descarga_buzon_usuario')->references('id_buzon_usuario')->on('buzon_usuario');
            $table->foreign('id_documento_buzon_archivo', 'fk_documento_buzon_archivo_descarga_documento_buzon_archivo')->references('id_documento_buzon_archivo')->on('documento_buzon_archivo');
        });

        DB::statement("COMMENT ON COLUMN  documento_buzon_archivo_descarga.id_documento_buzon_archivo_descarga IS 'Identificador unico de registro'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_archivo_descarga.id_documento_buzon_archivo IS 'Identificador de documento buzon archivo'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_archivo_descarga.id_buzon_usuario IS 'Identificador de buzon usuario'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_archivo_descarga.fecha IS 'Fecha de descarga'");
        DB::statement("COMMENT ON TABLE   documento_buzon_archivo_descarga IS 'Registro de descarga de archivos por buzones'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documento_buzon_archivo_descarga');
    }
}
