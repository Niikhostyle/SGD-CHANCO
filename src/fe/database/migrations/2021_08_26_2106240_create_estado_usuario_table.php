<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstadoUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estado_usuario', function (Blueprint $table) {
            $table->increments('id_estado_usuario');
            $table->string('nombre')->nullable();
            $table->timestamps();
        });
        DB::statement("COMMENT ON COLUMN  estado_usuario.id_estado_usuario IS 'Identificador de estado de usuario'");
        DB::statement("COMMENT ON COLUMN  estado_usuario.nombre IS 'Nombre de estado de usuario'");
        DB::statement("COMMENT ON TABLE   estado_usuario IS 'Definición de estados de usuarios'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estado_usuario');
    }
}
