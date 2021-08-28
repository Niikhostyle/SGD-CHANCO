<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoFlujoAccionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_flujo_accion', function (Blueprint $table) {
            $table->increments('id_tipo_flujo_accion');
            $table->integer('id_tipo_flujo');
            $table->integer('id_accion');
            $table->timestamps();
            $table->foreign('id_accion', 'fk_tipo_flujo_accion_accion')->references('id_accion')->on('accion');
            $table->foreign('id_tipo_flujo', 'fk_tipo_flujo_accion_tipo_flujo')->references('id_tipo_flujo')->on('tipo_flujo');
        });
        DB::statement("COMMENT ON COLUMN  tipo_flujo_accion.id_tipo_flujo_accion IS 'Identificador unico de registro'");
        DB::statement("COMMENT ON COLUMN  tipo_flujo_accion.id_tipo_flujo IS 'Identificador de tipo de flujo'");
        DB::statement("COMMENT ON COLUMN  tipo_flujo_accion.id_accion IS 'Identificador de accion'");
        DB::statement("COMMENT ON TABLE   tipo_flujo_accion IS 'Registro de relación entre tipos de flujo y acciones'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_flujo_accion');
    }
}
