<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoAccionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_accion', function (Blueprint $table) {
            $table->increments('id_tipo_accion');
            $table->string('nombre')->nullable();
            $table->timestamps();
        });
        DB::statement("COMMENT ON COLUMN  tipo_accion.id_tipo_accion IS 'Identificador de tipo de accion'");
        DB::statement("COMMENT ON COLUMN  tipo_accion.nombre IS 'Nombre de tipo de accion'");
        DB::statement("COMMENT ON TABLE   tipo_accion IS 'Definición de tipos de acciones'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_accion');
    }
}
