<?php
use App\Models\Documento;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixDocumentoColReferencia extends Migration
{
    public function up()
    {
        //si existe columna en el modelo
        if (Schema::hasColumn('documento', 'referencias')) {
            //se prepara la data de la columna
            Documento::whereNotNull('referencias')->get()->each(function($documento){
                //formato antiguo
                if(!isset($documento->referencias["respuesta_a"])){
                    //eliminar los array vacios
                    if(isset($documento->referencias[0]["materia"])){
                        $documento->referencias = ["respuesta_a"=>[$documento->referencias[0]]];
                    }else{
                        $documento->referencias = null;
                    }
                    $documento->save();
                }
                
            });
        }
    }

    public function down()
    {
        //
    if (Schema::hasColumn('documento', 'referencias')) {
            Documento::whereNotNull('referencias')->get()->each(function($documento){
                //formato nuevo hacia antiguo
                if(!isset($documento->referencias["respuesta_a"])){
                    $documento->referencias = null; 
                }else{
                    $documento->referencias = $documento->referencias["respuesta_a"];
                }
                $documento->save();
        });
        }
    }
}
