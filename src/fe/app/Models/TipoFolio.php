<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoFolio extends Model
{
    protected $table = "tipo_folio";
    protected $primaryKey = 'id_tipo_folio';

    protected $hidden = ['created_at', 'updated_at'];
}