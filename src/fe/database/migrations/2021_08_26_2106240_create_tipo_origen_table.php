<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoOrigenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_origen', function (Blueprint $table) {
            $table->increments('id_tipo_origen');
            $table->string('nombre');
            $table->timestamps();
        });
        DB::statement("COMMENT ON COLUMN  tipo_origen.id_tipo_origen IS 'Identificador de tipo de origen'");
        DB::statement("COMMENT ON COLUMN  tipo_origen.nombre IS 'Nombre de tipo de origen'");
        DB::statement("COMMENT ON TABLE   tipo_origen IS 'Definición de tipos de origen de documentos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_origen');
    }
}
