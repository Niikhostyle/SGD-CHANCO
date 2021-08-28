<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoDocumentoBuzonAccionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_documento_buzon_accion', function (Blueprint $table) {
            $table->bigIncrements('id_tipo_documento_buzon_accion');
            $table->bigInteger('id_tipo_documento_buzon');
            $table->integer('id_accion');
            $table->timestamps();
            $table->foreign('id_accion', 'fk_tipo_documento_buzon_accion_accion')->references('id_accion')->on('accion');
            $table->foreign('id_tipo_documento_buzon', 'fk_tipo_documento_buzon_tipo_accion_tipo_documento_buzon')->references('id_tipo_documento_buzon')->on('tipo_documento_buzon');
        });
        DB::statement("COMMENT ON COLUMN  tipo_documento_buzon_accion.id_tipo_documento_buzon_accion IS 'Identificador unico de registro'");
        DB::statement("COMMENT ON COLUMN  tipo_documento_buzon_accion.id_tipo_documento_buzon IS 'Identificador de tipo documento buzon'");
        DB::statement("COMMENT ON COLUMN  tipo_documento_buzon_accion.id_accion IS 'Identificador de accion'");
        DB::statement("COMMENT ON TABLE   tipo_documento_buzon_accion IS 'Registro de relación entre tipos de documentos, buzones definidos en su flujo y las acciones que estos deben realizar'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_documento_buzon_accion');
    }
}
