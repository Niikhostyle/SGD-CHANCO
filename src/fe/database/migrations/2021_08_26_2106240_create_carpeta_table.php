<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarpetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carpeta', function (Blueprint $table) {
            $table->increments('id_carpeta');
            $table->string('nombre')->nullable();
            $table->timestamps();
        });
        DB::statement("COMMENT ON COLUMN  carpeta.id_carpeta IS 'Identificador de carpeta'");
        DB::statement("COMMENT ON COLUMN  carpeta.nombre IS 'Nombre de carpeta'");
        DB::statement("COMMENT ON TABLE   carpeta IS 'Definición de carpetas disponibles por buzón'");  
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carpeta');
    }
}
