<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->integer('id_perfil');
            $table->integer('id_estado_usuario');
            $table->string('run', 16)->nullable();
            $table->string('nombres')->nullable();
            $table->string('primer_apellido')->nullable();
            $table->string('segundo_apellido')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('aplica_fea')->nullable();
            $table->boolean('genera_pdf')->nullable();
            $table->longText('hash_recuperacion')->nullable();
            $table->dateTime('hash_recuperacion_fecha')->nullable();
            $table->rememberToken();
            $table->timestamps();


            $table->foreign('id_estado_usuario', 'fk_usuario_estado_usuario')->references('id_estado_usuario')->on('estado_usuario');
            $table->foreign('id_perfil', 'fk_usuario_perfil')->references('id_perfil')->on('perfil');
         });

        DB::statement("COMMENT ON COLUMN  users.id IS 'Identificador de usuario'");
        DB::statement("COMMENT ON COLUMN  users.id_perfil IS 'Identificador de perfil'");
        DB::statement("COMMENT ON COLUMN  users.id_estado_usuario IS 'Identificador de estado de usuario'");
        DB::statement("COMMENT ON COLUMN  users.run IS 'RUN de usuario'");
        DB::statement("COMMENT ON COLUMN  users.nombres IS 'Nombres de usuario'");
        DB::statement("COMMENT ON COLUMN  users.primer_apellido IS 'Primer apellido usuario'");
        DB::statement("COMMENT ON COLUMN  users.segundo_apellido IS 'Segundo apellido usuario'");
        DB::statement("COMMENT ON COLUMN  users.email IS 'Correo electronico usuario'");
        DB::statement("COMMENT ON COLUMN  users.email_verified_at IS 'Fecha verificación correo electronico usuario'");
        DB::statement("COMMENT ON COLUMN  users.password IS 'Clave usuario'");
        DB::statement("COMMENT ON COLUMN  users.aplica_fea IS 'Permiso usuario para aplicacion de FEA'");
        DB::statement("COMMENT ON COLUMN  users.genera_pdf IS 'Permiso usuario para generacion de pdf'");
        DB::statement("COMMENT ON COLUMN  users.hash_recuperacion IS 'Hash para recuperación de clave'");
        DB::statement("COMMENT ON COLUMN  users.hash_recuperacion_fecha IS 'Fecha y hora de creación de hash_recuperacion'");
        DB::statement("COMMENT ON TABLE   users IS 'Registro de usuarios'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
