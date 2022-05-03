<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoFirmaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_firma', function (Blueprint $table) {
            $table->increments('id_tipo_firma');
            $table->string('nombre')->nullable();
            $table->string('sigla')->nullable();
            $table->timestamps();
        });
        DB::statement("COMMENT ON COLUMN  tipo_firma.id_tipo_firma IS 'Identificador de tipo de firma'");
        DB::statement("COMMENT ON COLUMN  tipo_firma.nombre IS 'Nombre de tipo de firma'");
        DB::statement("COMMENT ON COLUMN  tipo_firma.sigla IS 'Sigla que identifica el tipo de firma'");
        DB::statement("COMMENT ON TABLE   tipo_firma IS 'Definición de tipos de firma'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_firma');
    }
}
