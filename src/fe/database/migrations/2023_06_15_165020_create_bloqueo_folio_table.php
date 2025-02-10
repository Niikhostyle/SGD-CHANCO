<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBloqueoFolioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('bloqueo_folio')) {
            Schema::create('bloqueo_folio', function (Blueprint $table) {
                $table->bigIncrements('id'); 
                $table->bigInteger('folio'); 	             
                $table->integer('tipo_folio'); 	            
                $table->integer('tipo_documento'); 
                $table->integer('buzon'); 
                $table->integer('reversado'); 
                $table->integer('estado'); 
            });
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bloqueo_folio');
    }
}
