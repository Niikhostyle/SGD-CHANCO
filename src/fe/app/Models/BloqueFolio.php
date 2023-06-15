<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloqueFolio extends Model
{
    protected $fillable = [
        'folio', 'estado','tipo_folio'
    ];
}
