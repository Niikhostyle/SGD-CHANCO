<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->bigIncrements('id_usuario');
            $table->integer('id_perfil');
            $table->integer('id_estado_usuario');
            $table->string('run', 16)->nullable();
            $table->string('nombres')->nullable();
            $table->string('primer_apellido')->nullable();
            $table->string('segundo_apellido')->nullable();
            $table->string('email')->nullable();
            $table->string('clave')->nullable();
            $table->boolean('aplica_fea')->nullable();
            $table->boolean('genera_pdf')->nullable();
            $table->longText('hash_recuperacion')->nullable();
            $table->dateTime('hash_recuperacion_fecha')->nullable();
            $table->timestamps();
            $table->foreign('id_estado_usuario', 'fk_usuario_estado_usuario')->references('id_estado_usuario')->on('estado_usuario');
            $table->foreign('id_perfil', 'fk_usuario_perfil')->references('id_perfil')->on('perfil');
        });
        DB::statement("COMMENT ON COLUMN  usuario.id_usuario IS 'Identificador de usuario'");
        DB::statement("COMMENT ON COLUMN  usuario.id_perfil IS 'Identificador de perfil'");
        DB::statement("COMMENT ON COLUMN  usuario.id_estado_usuario IS 'Identificador de estado de usuario'");
        DB::statement("COMMENT ON COLUMN  usuario.run IS 'RUN de usuario'");
        DB::statement("COMMENT ON COLUMN  usuario.nombres IS 'Nombres de usuario'");
        DB::statement("COMMENT ON COLUMN  usuario.primer_apellido IS 'Primer apellido usuario'");
        DB::statement("COMMENT ON COLUMN  usuario.segundo_apellido IS 'Segundo apellido usuario'");
        DB::statement("COMMENT ON COLUMN  usuario.email IS 'Correo electronico usuario'");
        DB::statement("COMMENT ON COLUMN  usuario.clave IS 'Clave usuario'");
        DB::statement("COMMENT ON COLUMN  usuario.aplica_fea IS 'Permiso usuario para aplicacion de FEA'");
        DB::statement("COMMENT ON COLUMN  usuario.genera_pdf IS 'Permiso usuario para generacion de pdf'"); 
        DB::statement("COMMENT ON COLUMN  usuario.hash_recuperacion IS 'Hash para recuperación de clave'");
        DB::statement("COMMENT ON COLUMN  usuario.hash_recuperacion_fecha IS 'Fecha y hora de creación de hash_recuperacion'");
        DB::statement("COMMENT ON TABLE   usuario IS 'Registro de usuarios'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuario');
    }
}
