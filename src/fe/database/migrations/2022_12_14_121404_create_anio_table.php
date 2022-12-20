<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anio', function (Blueprint $table) {
            $table->integer('id_anio')->primary();
            $table->string('descripcion')->nullable();
            $table->string('estado')->nullable();
            $table->timestamps();
        });
        DB::statement("COMMENT ON COLUMN  anio.id_anio IS 'Identificador de año'");
        DB::statement("COMMENT ON COLUMN  anio.descripcion IS 'Valor del año'");
        DB::statement("COMMENT ON TABLE   anio IS 'Definición de años'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anio');
    }
}
