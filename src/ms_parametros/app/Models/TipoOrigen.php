<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoOrigen extends Model
{
    protected $table = "tipo_origen";
    protected $primaryKey = 'id_tipo_origen';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
   
}
