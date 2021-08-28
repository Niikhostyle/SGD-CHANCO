<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoDestinoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_destino', function (Blueprint $table) {
            $table->increments('id_tipo_destino');
            $table->string('nombre')->nullable();
            $table->timestamps();
        });
        DB::statement("COMMENT ON COLUMN  tipo_destino.id_tipo_destino IS 'Identificacion de tipo de destino'");
        DB::statement("COMMENT ON COLUMN  tipo_destino.nombre IS 'Nombre de tipo de destino'");
        DB::statement("COMMENT ON TABLE   tipo_destino IS 'Definición de tipos de destino o tipos de derivación de documentos a buzones'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_destino');
    }
}
