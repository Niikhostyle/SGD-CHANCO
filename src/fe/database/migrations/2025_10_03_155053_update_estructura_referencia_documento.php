<?php

use App\Models\Documento;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEstructuraReferenciaDocumento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        //se cambia nombre de columna
        if (Schema::hasColumn('documento', 'json_respuesta_a')) {
            Schema::table('documento', function (Blueprint $table) {
                $table->renameColumn('json_respuesta_a', 'referencias');
            });
        }
    }


    public function down()
    {
        // rollback data
        if (Schema::hasColumn('documento', 'referencias')) {
            Schema::table('documento', function (Blueprint $table) {
                $table->renameColumn('referencias', 'json_respuesta_a');
            });
        }
    }
}
