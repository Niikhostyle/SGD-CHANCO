<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDestino extends Model
{
    protected $table = "tipo_destino";
    protected $primaryKey = 'id_tipo_destino';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    //relacion a muchos documentobuzon
    public function documentosBuzon()
    {
        return $this->hasMany('App\Models\DocumentoBuzon', 'id_tipo_destino');
    }
   
}
