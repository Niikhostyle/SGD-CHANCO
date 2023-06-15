<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloqueoFolio extends Model
{
    protected $table = "bloqueo_folio";
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'folio', 'estado'
    ];
}
