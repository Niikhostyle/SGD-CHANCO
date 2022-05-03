<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentoFavoritoUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documento_favorito_usuario', function (Blueprint $table) {
            $table->bigIncrements('id_documento_favorito_usuario');
            $table->bigInteger('id_documento');
            $table->bigInteger('id_usuario');
            $table->bigInteger('id_buzon')->nullable();
            $table->boolean('favorito')->nullable();
            $table->foreign('id_buzon', 'fk_favorito_buzon')->references('id_buzon')->on('buzon');
            $table->foreign('id_documento', 'fk_favorito_documento')->references('id_documento')->on('documento');
            $table->foreign('id_usuario', 'fk_favorito_usuario')->references('id')->on('users');     
        });

        DB::statement("COMMENT ON COLUMN public.documento_favorito_usuario.id_documento_favorito_usuario IS 'Identificador unico de registro'");
        DB::statement("COMMENT ON COLUMN public.documento_favorito_usuario.id_documento IS 'Identificador de documento'");
        DB::statement("COMMENT ON COLUMN public.documento_favorito_usuario.id_usuario IS 'Identificador de usuario'");
        DB::statement("COMMENT ON COLUMN public.documento_favorito_usuario.id_buzon IS 'Identificador de buzon'");
        DB::statement("COMMENT ON COLUMN public.documento_favorito_usuario.favorito IS 'Marca de documento como favorito para el usuario'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documento_favorito_usuario');
    }
}
