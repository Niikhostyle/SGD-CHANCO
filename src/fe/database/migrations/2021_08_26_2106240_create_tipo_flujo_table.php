<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoFlujoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_flujo', function (Blueprint $table) {
            $table->increments('id_tipo_flujo');
            $table->string('nombre')->nullable();
            $table->timestamps();
        });
        DB::statement("COMMENT ON COLUMN  tipo_flujo.id_tipo_flujo IS 'Identificador de tipo de flujo'");
        DB::statement("COMMENT ON COLUMN  tipo_flujo.nombre IS 'Nombre de tipo de flujo'");
        DB::statement("COMMENT ON TABLE   tipo_flujo IS 'Definición de tipos de flujo de documentos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_flujo');
    }
}
