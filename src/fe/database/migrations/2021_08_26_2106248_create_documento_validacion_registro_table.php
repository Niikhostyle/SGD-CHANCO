<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentoValidacionRegistroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documento_validacion_registro', function (Blueprint $table) {
            $table->bigIncrements('id_documento_validacion_registro');
            $table->bigInteger('id_documento')->nullable();
            $table->longText('hash_validado')->nullable();
            $table->dateTime('fecha')->nullable();
            $table->longText('informacion_solicitud')->nullable();
            $table->jsonb('json_resultado')->nullable();
            $table->timestamps();
            $table->foreign('id_documento', 'fk_documento_validacion_registro_documento')->references('id_documento')->on('documento');
        });
        DB::statement("COMMENT ON COLUMN  documento_validacion_registro.id_documento_validacion_registro IS 'Identificador unico de registro'");
        DB::statement("COMMENT ON COLUMN  documento_validacion_registro.id_documento IS 'Identificador de documento'");
        DB::statement("COMMENT ON COLUMN  documento_validacion_registro.hash_validado IS 'Resumen de comprobacion a validar'");
        DB::statement("COMMENT ON COLUMN  documento_validacion_registro.fecha IS 'Fecha de validacion de documento'");
        DB::statement("COMMENT ON COLUMN  documento_validacion_registro.informacion_solicitud IS 'Detalle de Informacion de la solicitud'");
        DB::statement("COMMENT ON COLUMN  documento_validacion_registro.json_resultado IS 'Estructura JSON con resultado de la validacion'");
        DB::statement("COMMENT ON TABLE   documento_validacion_registro IS 'Registro de validación de documentos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documento_validacion_registro');
    }
}
