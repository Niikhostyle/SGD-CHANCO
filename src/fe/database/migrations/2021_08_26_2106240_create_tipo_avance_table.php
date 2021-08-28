<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoAvanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_avance', function (Blueprint $table) {
            $table->Increments('id_tipo_avance');
            $table->string('nombre')->nullable();
            $table->timestamps();
        });
        DB::statement("COMMENT ON COLUMN  tipo_avance.id_tipo_avance IS 'Identificador de tipo de avance'");
        DB::statement("COMMENT ON COLUMN  tipo_avance.nombre IS 'Nombre de tipo de avance'");
        DB::statement("COMMENT ON TABLE   tipo_avance IS 'Definición de tipos de avance de documentos por el flujo'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_avance');
    }
}
