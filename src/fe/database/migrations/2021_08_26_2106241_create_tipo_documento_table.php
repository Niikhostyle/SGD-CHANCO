<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoDocumentoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_documento', function (Blueprint $table) {
            $table->increments('id_tipo_documento');
            $table->integer('id_tipo_origen');
            $table->integer('id_tipo_flujo');
            $table->integer('id_tipo_avance')->nullable();
            $table->integer('id_tipo_folio');
            $table->integer('id_tipo_asignacion_folio');
            $table->string('nombre')->nullable();
            $table->string('descripcion', 512)->nullable();
            $table->string('nombre_corto')->nullable();
            $table->boolean('requiere_fe')->nullable();
            $table->longText('plantilla_encabezado')->nullable();
            $table->longText('plantilla_cuerpo')->nullable();
            $table->timestamps();
            $table->foreign('id_tipo_asignacion_folio', 'fk_tipo_documento_tipo_asignacion_folio')->references('id_tipo_asignacion_folio')->on('tipo_asignacion_folio');
            $table->foreign('id_tipo_avance', 'fk_tipo_documento_tipo_avance')->references('id_tipo_avance')->on('tipo_avance');
            $table->foreign('id_tipo_flujo', 'fk_tipo_documento_tipo_flujo')->references('id_tipo_flujo')->on('tipo_flujo');
            $table->foreign('id_tipo_folio', 'fk_tipo_documento_tipo_folio')->references('id_tipo_folio')->on('tipo_folio');
            $table->foreign('id_tipo_origen', 'fk_tipo_documento_tipo_origen')->references('id_tipo_origen')->on('tipo_origen');
        });
        DB::statement("COMMENT ON COLUMN  tipo_documento.id_tipo_documento IS 'Identificador de tipo de documento'");
        DB::statement("COMMENT ON COLUMN  tipo_documento.id_tipo_origen IS 'Identificador de tipo de origen'");
        DB::statement("COMMENT ON COLUMN  tipo_documento.id_tipo_flujo IS 'Identificador de tipo de flujo'");
        DB::statement("COMMENT ON COLUMN  tipo_documento.id_tipo_avance IS 'Identificador de tipo de avance'");
        DB::statement("COMMENT ON COLUMN  tipo_documento.id_tipo_folio IS 'Identificador de tipo de folio'");
        DB::statement("COMMENT ON COLUMN  tipo_documento.id_tipo_asignacion_folio IS 'Identificador de tipo de asignacion de folio y fecha'");
        DB::statement("COMMENT ON COLUMN  tipo_documento.nombre IS 'Nombre de tipo documento'");
        DB::statement("COMMENT ON COLUMN  tipo_documento.descripcion IS 'Descripcion de tipo documento'");
        DB::statement("COMMENT ON COLUMN  tipo_documento.nombre_corto IS 'Nombre corto de tipo documento'");
        DB::statement("COMMENT ON COLUMN  tipo_documento.requiere_fe IS 'Definicion de requerimiento de firma electronica'");
        DB::statement("COMMENT ON COLUMN  tipo_documento.plantilla_encabezado IS 'Plantilla de encabezado de tipo documento'");
        DB::statement("COMMENT ON COLUMN  tipo_documento.plantilla_cuerpo IS 'Plantilla de cuerpo de tipo documento'");
        DB::statement("COMMENT ON TABLE   tipo_documento IS 'Registro de tipos de documentos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_documento');
    }
}
