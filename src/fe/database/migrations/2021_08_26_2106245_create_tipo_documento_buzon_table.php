<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoDocumentoBuzonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_documento_buzon', function (Blueprint $table) {
            $table->bigIncrements('id_tipo_documento_buzon');
            $table->integer('id_tipo_documento');
            $table->bigInteger('id_buzon');
            $table->integer('orden')->nullable();
            $table->timestamps();
            $table->foreign('id_buzon', 'fk_tipo_documento_buzon_buzon')->references('id_buzon')->on('buzon');
            $table->foreign('id_tipo_documento', 'fk_tipo_documento_buzon_tipo_documento')->references('id_tipo_documento')->on('tipo_documento');
        });
        DB::statement("COMMENT ON COLUMN  tipo_documento_buzon.id_tipo_documento_buzon IS 'Identificador unico de registro'");
        DB::statement("COMMENT ON COLUMN  tipo_documento_buzon.id_tipo_documento IS 'Identificador de tipo de documento'");
        DB::statement("COMMENT ON COLUMN  tipo_documento_buzon.id_buzon IS 'Identificador de buzon'");
        DB::statement("COMMENT ON COLUMN  tipo_documento_buzon.orden IS 'Orden de buzon en el flujo'");
        DB::statement("COMMENT ON TABLE   tipo_documento_buzon IS 'Registro de relacion entre tipos de documentos y buzones por definición de flujo controlado o mixto'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_documento_buzon');
    }
}
