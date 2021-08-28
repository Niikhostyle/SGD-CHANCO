<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentoBuzonArchivoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documento_buzon_archivo', function (Blueprint $table) {
            $table->bigIncrements('id_documento_buzon_archivo');
            $table->bigInteger('id_documento_buzon');
            $table->integer('id_tipo_archivo');
            $table->string('nombre_archivo_original', 1024)->nullable();
            $table->string('nombre_archivo_codificado', 1024)->nullable();
            $table->dateTime('fecha')->nullable();
            $table->integer('version')->nullable();
            $table->timestamps();
            $table->foreign('id_documento_buzon', 'fk_documento_buzon_archivo_documento_buzon')->references('id_documento_buzon')->on('documento_buzon');
            $table->foreign('id_tipo_archivo', 'fk_documento_buzon_archivo_tipo_archivo')->references('id_tipo_archivo')->on('tipo_archivo');
        });
        DB::statement("COMMENT ON COLUMN  documento_buzon_archivo.id_documento_buzon_archivo IS 'Identificador unico de registro'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_archivo.id_documento_buzon IS 'Identificador de documento buzon'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_archivo.id_tipo_archivo IS 'Identificador de tipo de archivo'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_archivo.nombre_archivo_original IS 'Nombre de archivo original'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_archivo.nombre_archivo_codificado IS 'Nombre de archivo codificado'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_archivo.fecha IS 'Fecha de subida o generacion de archivo'");
        DB::statement("COMMENT ON COLUMN  documento_buzon_archivo.version IS 'Numero de version de archivo'");
        DB::statement("COMMENT ON TABLE   documento_buzon_archivo IS 'Registro de archivos generados o subidos y asociados por buzones a documentos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documento_buzon_archivo');
    }
}
