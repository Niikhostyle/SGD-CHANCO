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
        //se prepara la data de la columna
        Documento::whereNotNull('json_respuesta_a')->get()->each(function($documento){
            //formato antiguo
           
            if(!isset($documento->json_respuesta_a["respuesta_a"])){
                //eliminar los array vacios
                if(isset($documento->json_respuesta_a[0]["materia"])){
                    $documento->json_respuesta_a = ["respuesta_a"=>$documento->json_respuesta_a];
                }else{
                    $documento->referencias = null;
                }
            }else{
                $documento->json_respuesta_a = ["respuesta_a"=>$documento->json_respuesta_a];
            }
            $documento->save();
       });

       //se cambia nombre de columna
        Schema::table('documento', function(Blueprint $table) {
            $table->renameColumn('json_respuesta_a', 'referencias');
        });
    }


    public function down()
    {
        // rollback data
        Documento::whereNotNull('referencias')->get()->each(function($documento){
            //formato nuevo hacia antiguo
            if(!isset($documento->referencias["respuesta_a"])){
                $documento->referencias = null; 
            }else{
                $documento->referencias = $documento->referencias["respuesta_a"];
            }
            $documento->save();
       });

        Schema::table('documento', function(Blueprint $table) {
            $table->renameColumn('referencias', 'json_respuesta_a');
        });
    }
}
