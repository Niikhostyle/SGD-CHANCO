<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelAcceso extends Model
{
    protected $table = "nivel_acceso";
    protected $primaryKey = 'id_nivel_acceso';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
   
}
