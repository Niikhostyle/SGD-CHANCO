<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoTramitacion extends Model
{
    protected $table = "estado_tramitacion";
    protected $primaryKey = 'id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
   
}
