<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoFlujoAccion extends Model
{
    protected $table = "tipo_flujo_accion";
    protected $primaryKey = 'id_tipo_flujo_accion';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
   
}
