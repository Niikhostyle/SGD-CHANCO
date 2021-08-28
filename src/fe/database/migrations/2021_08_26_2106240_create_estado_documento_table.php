<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstadoDocumentoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estado_documento', function (Blueprint $table) {
            $table->increments('id_estado_documento');
            $table->string('nombre')->nullable();
            $table->string('nombre_corto')->nullable();
            $table->string('codigo_color')->nullable();
            $table->timestamps();
        });
        DB::statement("COMMENT ON COLUMN  estado_documento.id_estado_documento IS 'Identificador de estado de documento'");
        DB::statement("COMMENT ON COLUMN  estado_documento.nombre IS 'Nombre de estado de documento'");
        DB::statement("COMMENT ON COLUMN  estado_documento.nombre_corto IS 'Nombre corto de estado de documento'");
        DB::statement("COMMENT ON COLUMN  estado_documento.codigo_color IS 'Codigo RGB del color que representa el estado de documento'");
        DB::statement("COMMENT ON TABLE   estado_documento IS 'Definición de estados de documentos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estado_documento');
    }
}
