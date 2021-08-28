<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accion', function (Blueprint $table) {
            $table->increments('id_accion');
            $table->integer('id_tipo_accion');
            $table->string('nombre')->nullable();
            $table->timestamps();
            $table->foreign('id_tipo_accion', 'fk_accion_tipo_accion')->references('id_tipo_accion')->on('tipo_accion');
        });
        DB::statement("COMMENT ON COLUMN  accion.id_accion IS 'Identificador de accion'");
        DB::statement("COMMENT ON COLUMN  accion.id_tipo_accion IS 'Identificador de tipo de accion'");
        DB::statement("COMMENT ON COLUMN  accion.nombre IS 'Nombre de accion'");
        DB::statement("COMMENT ON TABLE   accion IS 'Definición de acciones realizables por los buzones sobre un documento'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accion');
    }
}
