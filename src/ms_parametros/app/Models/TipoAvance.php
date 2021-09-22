<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoAvance extends Model
{
    protected $table = "tipo_avance";
    protected $primaryKey = 'id_tipo_avance';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
   
}
