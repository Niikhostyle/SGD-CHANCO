<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolSolicitudBitacora extends Model
{
    protected $table = 'sol_solicitud_bitacora';

    protected $fillable = [
        'solicitud_id', 'id_buzon', 'id_usuario', 'accion', 'comentario',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolSolicitud::class, 'solicitud_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
