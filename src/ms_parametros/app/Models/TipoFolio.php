<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoFolio extends Model
{
    protected $table = "tipo_folio";
    protected $primaryKey = 'id_tipo_folio';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
   
}
