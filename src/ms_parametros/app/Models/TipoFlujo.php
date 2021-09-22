<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoFlujo extends Model
{
    protected $table = "tipo_flujo";
    protected $primaryKey = 'id_tipo_flujo';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
   
}
