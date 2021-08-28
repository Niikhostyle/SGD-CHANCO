<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoAsignacionFolioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_asignacion_folio', function (Blueprint $table) {
            $table->increments('id_tipo_asignacion_folio');
            $table->string('nombre')->nullable();
            $table->timestamps();
        });
        DB::statement("COMMENT ON COLUMN  tipo_asignacion_folio.id_tipo_asignacion_folio IS 'Identificador de tipo de asignacion de folio y fecha'");
        DB::statement("COMMENT ON COLUMN  tipo_asignacion_folio.nombre IS 'Nombre de tipo de asignacion de folio y fecha'");
        DB::statement("COMMENT ON TABLE   tipo_asignacion_folio IS 'Definición de tipos de eventos donde se asigna folio a los documentos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_asignacion_folio');
    }
}
