<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolSaldoAnual extends Model
{
    protected $table = 'sol_saldos_anuales';
    protected $fillable = [
        'user_id', 'anio', 'dias_administrativos', 'feriados_legales', 'dias_compensatorios',
    ];

    protected $casts = [
        'dias_administrativos' => 'float',
        'feriados_legales' => 'float',
        'dias_compensatorios' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
