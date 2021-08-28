<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNivelAccesoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nivel_acceso', function (Blueprint $table) {
            $table->increments('id_nivel_acceso');
            $table->string('nombre')->nullable();
            $table->timestamps();
        });
        DB::statement("COMMENT ON COLUMN  nivel_acceso.id_nivel_acceso IS 'Identificador de nivel de acceso'");
        DB::statement("COMMENT ON COLUMN  nivel_acceso.nombre IS 'Nombre de nivel de acceso'");
        DB::statement("COMMENT ON TABLE   nivel_acceso IS 'Definición de niveles de acceso o privacidad de documentos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nivel_acceso');
    }
}
