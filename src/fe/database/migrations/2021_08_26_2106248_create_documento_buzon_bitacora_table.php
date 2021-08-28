<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentoBuzonBitacoraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documento_buzon_bitacora', function (Blueprint $table) {
            $table->bigIncrements('id_documento_buzon_bitacora');
            $table->bigInteger('id_documento_buzon');
            $table->integer('id_accion');
            $table->dateTime('fecha')->nullable();
            $table->bigInteger('id_usuario');
            $table->longText('comentario')->nullable();
            $table->longText('informacion_solicitud')->nullable();
            $table->longText('mensaje_respuesta')->nullable();
            $table->timestamps();
            $table->foreign('id_accion', 'fk_documento_buzon_bitacora_accion')->references('id_accion')->on('accion');
            $table->foreign('id_documento_buzon', 'fk_documento_buzon_bitacora_documento_buzon')->references('id_documento_buzon')->on('documento_buzon');
        });
        DB::statement("COMMENT ON COLUMN  documento_buzon_bitacora.id_documento_buzon_bitacora IS 'Identificador unico de registro'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_bitacora.id_documento_buzon IS 'Identificador de documento buzon'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_bitacora.id_accion IS 'Identificador de accion'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_bitacora.fecha IS 'Fecha de accion'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_bitacora.id_usuario IS 'Identificador de usuario'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_bitacora.comentario IS 'Comentario asociado a la ejecucion de la accion'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_bitacora.informacion_solicitud IS 'Detalle de Informacion de la solicitud'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_bitacora.mensaje_respuesta IS 'Respuesta obtenida luego de la ejecucion de la accion'");
        DB::statement("COMMENT ON TABLE   documento_buzon_bitacora IS 'Registro de acciones de buzones sobre documentos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documento_buzon_bitacora');
    }
}
