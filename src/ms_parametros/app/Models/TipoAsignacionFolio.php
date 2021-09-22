<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoAsignacionFolio extends Model
{
    protected $table = "tipo_asignacion_folio";
    protected $primaryKey = 'id_tipo_asignacion_folio';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
   
}
