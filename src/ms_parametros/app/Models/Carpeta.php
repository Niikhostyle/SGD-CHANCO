<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carpeta extends Model
{
    protected $table = "carpeta";
    protected $primaryKey = 'id_carpeta';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
   
}
