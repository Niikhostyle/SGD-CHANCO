<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDocumentoOrdenCarpetas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('documento', 'anio_tramitacion')) {
            /*procesar BD para reordenar los archivos segun estado en carpetas 
            carpeta | estado
            Por Recibir | 3
            Recibido | 4,5,6,8,9,11,13
            Despachado | 1,2,7,10,12
            */
            ini_set('memory_limit', '-1');
            $documentos = \App\Models\DocumentoBuzon::all();
            foreach ($documentos as $documento) {
                //echo "ID";
                if(in_array($documento->id_estado_documento,[3])){
                    $documento->id_carpeta = 1;
                }
                if(in_array($documento->id_estado_documento,[4,5,6,8,9,11,13])){
                    $documento->id_carpeta = 2;
                }  
                if(in_array($documento->id_estado_documento,[1,2,7,10,12])){
                    $documento->id_carpeta = 3;
                }
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

    }
}
