<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuzonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buzon', function (Blueprint $table) {
            $table->bigIncrements('id_buzon');
            $table->integer('id_tipo_buzon');
            $table->string('nombre')->nullable();
            $table->string('nombre_corto')->nullable();
            $table->string('cargo_firma')->nullable();
            $table->timestamps();
            $table->foreign('id_tipo_buzon', 'fk_buzon_tipo_buzon')->references('id_tipo_buzon')->on('tipo_buzon');
        });
        DB::statement("COMMENT ON COLUMN  buzon.id_buzon IS 'Identificador de buzon'");
        DB::statement("COMMENT ON COLUMN  buzon.id_tipo_buzon IS 'Identificador de tipo de buzon'");
        DB::statement("COMMENT ON COLUMN  buzon.nombre IS 'Nombre buzon'");
        DB::statement("COMMENT ON COLUMN  buzon.nombre_corto IS 'Nombre corto buzon '");
        DB::statement("COMMENT ON COLUMN  buzon.cargo_firma IS 'Nombre cargo para fea '");
        DB::statement("COMMENT ON TABLE   buzon IS 'Registro de buzones'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('buzon');
    }
}
