<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoBuzonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_buzon', function (Blueprint $table) {
            $table->increments('id_tipo_buzon');
            $table->string('nombre')->nullable();
            $table->timestamps();
        });
        DB::statement("COMMENT ON COLUMN  tipo_buzon.id_tipo_buzon IS 'Identificador de tipo de buzon'");
        DB::statement("COMMENT ON COLUMN  tipo_buzon.nombre IS 'Nombre de tipo de buzon'");
        DB::statement("COMMENT ON TABLE   tipo_buzon IS 'Definición de tipos de buzones'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_buzon');
    }
}
