<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentoBuzonNotificacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documento_buzon_notificacion', function (Blueprint $table) {
            $table->bigIncrements('id_documento_buzon_notificacion');
            $table->bigInteger('id_documento_buzon');
            $table->bigInteger('id_buzon_usuario');
            $table->string('email')->nullable();
            $table->dateTime('fecha')->nullable();
            $table->longText('notificacion')->nullable();
            $table->timestamps();
            $table->foreign('id_buzon_usuario', 'fk_documento_buzon_notificacion_buzon_usuario')->references('id_buzon_usuario')->on('buzon_usuario');
            $table->foreign('id_documento_buzon', 'fk_documento_buzon_notificacion_documento_buzon')->references('id_documento_buzon')->on('documento_buzon');
        });
        DB::statement("COMMENT ON COLUMN  documento_buzon_notificacion.id_documento_buzon_notificacion IS 'Identificador unico de registro'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_notificacion.id_documento_buzon IS 'Identificador de documento buzon'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_notificacion.id_buzon_usuario IS 'Identificador de buzon usuario notificado'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_notificacion.email IS 'Correo electrónico donde se envió la notificación'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_notificacion.fecha IS 'Fecha de envío de notificación'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_notificacion.notificacion IS 'Texto de notificación enviado'");
        DB::statement("COMMENT ON TABLE   documento_buzon_notificacion IS 'Registro de notificaciones por correo electrónico a integrantes de buzones destinatarios principales luego de una derivación'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documento_buzon_notificacion');
    }
}
