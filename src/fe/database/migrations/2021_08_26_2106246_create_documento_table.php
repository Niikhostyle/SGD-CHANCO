<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documento', function (Blueprint $table) {
            $table->bigIncrements('id_documento');
            $table->integer('id_tipo_documento');
            $table->integer('id_nivel_acceso');
            $table->jsonb('json_tipo_documento')->nullable();
            $table->bigIncrements('identificador');
            $table->BigInteger('folio')->nullable();
            $table->dateTime('fecha')->nullable();
            $table->jsonb('json_respuesta_a')->nullable();
            $table->string('materia', 512)->nullable();
            $table->string('anterior', 512)->nullable();
            $table->longText('descripcion')->nullable();
            $table->longText('encabezado')->nullable();
            $table->longText('cuerpo')->nullable();
            $table->boolean('efectos_terceros')->nullable();
            $table->longText('hash_validacion')->nullable();
            $table->boolean('archivo_existente')->nullable();
            $table->integer('paginas_archivo')->nullable();
            $table->string('img_firma')->nullable();
            $table->boolean('finalizado')->nullable();
            $table->timestamps();
            $table->foreign('id_nivel_acceso', 'fk_documento_nivel_acceso')->references('id_nivel_acceso')->on('nivel_acceso');
            $table->foreign('id_tipo_documento', 'fk_documento_tipo_documento')->references('id_tipo_documento')->on('tipo_documento');
        });
        DB::statement("COMMENT ON COLUMN  documento.id_documento IS 'Identificador de documento'");
        DB::statement("COMMENT ON COLUMN  documento.id_tipo_documento IS 'Identificador de tipo de documento'");
        DB::statement("COMMENT ON COLUMN  documento.id_nivel_acceso IS 'Identificador de nivel de acceso'");
        DB::statement("COMMENT ON COLUMN  documento.json_tipo_documento IS 'Estructura JSON con valores de parametros que definen el tipo de documento'");
        DB::statement("COMMENT ON COLUMN  documento.identificador IS 'ID asignado al documento luego de su creacion'");
        DB::statement("COMMENT ON COLUMN  documento.folio IS 'Folio del documento'");
        DB::statement("COMMENT ON COLUMN  documento.fecha IS 'Fecha del documento'");
        DB::statement("COMMENT ON COLUMN  documento.json_respuesta_a IS 'Estructura JSON con referencia a otros documentos a los que se responde'");
        DB::statement("COMMENT ON COLUMN  documento.materia IS 'Materia del documento'");
        DB::statement("COMMENT ON COLUMN  documento.anterior IS 'Indicacion de relacion del documento con otro documento anterior'");
        DB::statement("COMMENT ON COLUMN  documento.descripcion IS 'Descripcion o extracto del documento'");
        DB::statement("COMMENT ON COLUMN  documento.encabezado IS 'Encabezado del documento'");
        DB::statement("COMMENT ON COLUMN  documento.cuerpo IS 'Cuerpo del documento'");
        DB::statement("COMMENT ON COLUMN  documento.efectos_terceros IS 'Marca indicativa de efectos sobre terceros'");
        DB::statement("COMMENT ON COLUMN  documento.hash_validacion IS 'Resumen para validacion de autenticidad de documento y FE'");
        DB::statement("COMMENT ON COLUMN  documento.archivo_existente IS 'Marca indicativa de existencia fisica de archivo principal'");
        DB::statement("COMMENT ON COLUMN  documento.finalizado IS 'Marca indicativa de finalizacion de documento'");
        DB::statement("COMMENT ON COLUMN  documento.paginas_archivo IS 'Numero de paginas del documento pdf generado'");
        DB::statement("COMMENT ON COLUMN  documento.img_firma IS 'Nombre imagen asociada a la fea'");
        DB::statement("COMMENT ON TABLE   documento IS 'Registro de documentos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documento');
    }
}
