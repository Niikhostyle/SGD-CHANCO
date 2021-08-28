<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentoBuzonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documento_buzon', function (Blueprint $table) {
            $table->bigIncrements('id_documento_buzon');
            $table->BigInteger('id_documento');
            $table->BigInteger('id_buzon');
            $table->integer('id_carpeta');
            $table->integer('id_estado_documento')->nullable();
            $table->integer('id_tipo_destino');
            $table->BigInteger('id_documento_buzon_padre')->nullable();
            $table->jsonb('json_acciones')->nullable();
            $table->dateTime('fecha')->nullable();
            $table->longText('comentario_principal')->nullable();
            $table->longText('comentario_secundario')->nullable();
            $table->boolean('notificado')->nullable();
            $table->boolean('recibido')->nullable();
            $table->dateTime('contestar_hasta')->nullable();
            $table->boolean('favorito')->nullable();
            $table->timestamps();
            $table->foreign('id_buzon', 'fk_documento_buzon_buzon')->references('id_buzon')->on('buzon');
            $table->foreign('id_carpeta', 'fk_documento_buzon_carpeta')->references('id_carpeta')->on('carpeta');
            $table->foreign('id_documento', 'fk_documento_buzon_documento')->references('id_documento')->on('documento');
            $table->foreign('id_estado_documento', 'fk_documento_buzon_estado_documento')->references('id_estado_documento')->on('estado_documento');
            $table->foreign('id_tipo_destino', 'fk_documento_buzon_tipo_destino')->references('id_tipo_destino')->on('tipo_destino');
        });
        DB::statement("COMMENT ON COLUMN  documento_buzon.id_documento_buzon IS 'Identificador unico de registro'");
        DB::statement("COMMENT ON COLUMN  documento_buzon.id_documento IS 'Identificador de documento'");
        DB::statement("COMMENT ON COLUMN  documento_buzon.id_buzon IS 'Identificador de buzon'");
        DB::statement("COMMENT ON COLUMN  documento_buzon.id_carpeta IS 'Identificador de carpeta'");
        DB::statement("COMMENT ON COLUMN  documento_buzon.id_estado_documento IS 'Identificador de estado de documento'");
        DB::statement("COMMENT ON COLUMN  documento_buzon.id_tipo_destino IS 'Identificador de tipo de destino'");
        DB::statement("COMMENT ON COLUMN  documento_buzon.id_documento_buzon_padre IS 'Identificador de documento buzon que deriva'");
        DB::statement("COMMENT ON COLUMN  documento_buzon.json_acciones IS 'Estructura JSON con acciones solicitadas'");
        DB::statement("COMMENT ON COLUMN  documento_buzon.fecha IS 'Fecha de registro'");
        DB::statement("COMMENT ON COLUMN  documento_buzon.comentario_principal IS 'Comentario derivacion principal'");
        DB::statement("COMMENT ON COLUMN  documento_buzon.comentario_secundario IS 'Comentario derivacion secundaria'");
        DB::statement("COMMENT ON COLUMN  documento_buzon.notificado IS 'Marca de envío de correo de notificación'");
        DB::statement("COMMENT ON COLUMN  documento_buzon.recibido IS 'Marca de recepcion de documento por buzon destinatario principal'");
        DB::statement("COMMENT ON COLUMN  documento_buzon.contestar_hasta IS 'Fecha maxima para respuesta del buzon derivado'");
        DB::statement("COMMENT ON COLUMN  documento_buzon.favorito IS 'Marca de documento como favorito para el buzon'");
        DB::statement("COMMENT ON TABLE   documento_buzon IS 'Registro de derivaciones de documentos a buzones'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documento_buzon');
    }
}
