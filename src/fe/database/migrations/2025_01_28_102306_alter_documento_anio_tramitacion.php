<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDocumentoAnioTramitacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('documento', 'anio_tramitacion')) {
            Schema::table('documento', function (Blueprint $table) {
                $table->integer('anio_tramitacion')->nullable();
            });

            //procesar BD para asignar año tramitacion
            ini_set('memory_limit', '-1');
            $documentos = \App\Models\Documento::OrderBy('id_documento', 'ASC')->get();
            foreach ($documentos as $documento) {
                echo "ID ".$documento->id_documento." Año Tramitación:".$documento->fecha->year."\n";
                $documento->anio_tramitacion = $documento->fecha->year;
                $documento->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('documento', 'anio_tramitacion')) {
            Schema::table('documento', function (Blueprint $table) {
                $table->dropColumn('anio_tramitacion');
            });
        }
    }
}
