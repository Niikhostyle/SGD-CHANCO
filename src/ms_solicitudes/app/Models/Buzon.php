<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Buzon extends Model
{
    protected $table = 'buzon';
    protected $primaryKey = 'id_buzon';
    public $timestamps = true;

    use SoftDeletes;

    protected $fillable = ['nombre', 'nombre_corto', 'id_tipo_buzon', 'cargo_firma'];

    public function usuariosAsignados()
    {
        return $this->hasMany(BuzonUsuario::class, 'id_buzon', 'id_buzon');
    }
}
