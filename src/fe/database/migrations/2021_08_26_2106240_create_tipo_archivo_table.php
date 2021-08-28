<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoArchivoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_archivo', function (Blueprint $table) {
            $table->increments('id_tipo_archivo');
            $table->string('nombre')->nullable();
            $table->timestamps();
        });
        DB::statement("COMMENT ON COLUMN  tipo_archivo.id_tipo_archivo IS 'Identificador de tipo de archivo'");
        DB::statement("COMMENT ON COLUMN  tipo_archivo.nombre IS 'Nombre  de tipo de archivo'");
        DB::statement("COMMENT ON TABLE   tipo_archivo IS 'Definición de tipos de archivo'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_archivo');
    }
}
