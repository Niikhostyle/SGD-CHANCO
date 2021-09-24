<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoBuzon extends Model
{
    protected $table = "tipo_buzon";
    protected $primaryKey = 'id_tipo_buzon';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
   
}
