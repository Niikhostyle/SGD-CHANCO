<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuzonUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buzon_usuario', function (Blueprint $table) {
            $table->bigIncrements('id_buzon_usuario');
            $table->bigInteger('id_buzon');
            $table->bigInteger('id_usuario');
            $table->bigInteger('id_tipo_firma');
            $table->timestamps();
            $table->foreign('id_buzon', 'fk_buzon_usuario_buzon')->references('id_buzon')->on('buzon');
            $table->foreign('id_usuario', 'fk_buzon_usuario_usuario')->references('id')->on('users');
            $table->foreign('id_tipo_firma', 'fk_buzon_usuario_firma')->references('id_tipo_firma')->on('tipo_firma');
        });
        DB::statement("COMMENT ON COLUMN  buzon_usuario.id_buzon_usuario IS 'Identificador de buzon_usuario'");
        DB::statement("COMMENT ON COLUMN  buzon_usuario.id_buzon IS 'Identificador de buzon'");
        DB::statement("COMMENT ON COLUMN  buzon_usuario.id_usuario IS 'Identificador de usuario'");
        DB::statement("COMMENT ON TABLE   buzon_usuario IS 'Registro de relación entre buzones y usuarios'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('buzon_usuario');
    }
}
