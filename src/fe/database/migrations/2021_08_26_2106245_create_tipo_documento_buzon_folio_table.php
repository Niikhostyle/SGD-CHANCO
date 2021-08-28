<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoDocumentoBuzonFolioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_documento_buzon_folio', function (Blueprint $table) {
            $table->bigIncrements('id_tipo_documento_buzon_folio');
            $table->integer('id_tipo_documento');
            $table->bigInteger('id_buzon')->nullable();
            $table->integer('anio')->nullable();
            $table->bigInteger('valor')->nullable();
            $table->timestamps();
            $table->foreign('id_buzon', 'fk_tipo_documento_buzon_folio_buzon')->references('id_buzon')->on('buzon');
            $table->foreign('id_tipo_documento', 'fk_tipo_documento_buzon_folio_tipo_documento')->references('id_tipo_documento')->on('tipo_documento');
        });
        DB::statement("COMMENT ON COLUMN  tipo_documento_buzon_folio.id_tipo_documento_buzon_folio IS 'Identificador unico de registro'");
        DB::statement("COMMENT ON COLUMN  tipo_documento_buzon_folio.id_tipo_documento IS 'Identificador de tipo de documento'");
        DB::statement("COMMENT ON COLUMN  tipo_documento_buzon_folio.id_buzon IS 'Identificador de buzon'");
        DB::statement("COMMENT ON COLUMN  tipo_documento_buzon_folio.anio IS 'Año de secuencia de folio'");
        DB::statement("COMMENT ON COLUMN  tipo_documento_buzon_folio.valor IS 'Valor actual de secuencia de folio'");
        DB::statement("COMMENT ON TABLE   tipo_documento_buzon_folio IS 'Registro de secuencias de folio por tipo documento, buzon y año'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_documento_buzon_folio');
    }
}
