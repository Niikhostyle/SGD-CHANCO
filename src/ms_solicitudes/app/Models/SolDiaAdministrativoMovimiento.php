<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolDiaAdministrativoMovimiento extends Model
{
    protected $table = 'sol_dia_administrativo_movimientos';
    protected $fillable = [
        'user_id', 'registrado_por', 'anio', 'tipo', 'permiso_tipo', 'dias', 'motivo',
    ];
}
