<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolSolicitudBuzon extends Model
{
    protected $table = 'sol_solicitud_buzon';

    protected $fillable = [
        'solicitud_id', 'id_buzon', 'nombre_buzon', 'orden', 'estado',
        'acciones', 'id_usuario_accion', 'observaciones', 'decidido_at',
    ];

    protected $casts = [
        'acciones' => 'array',
        'orden' => 'integer',
        'id_buzon' => 'integer',
        'decidido_at' => 'datetime',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolSolicitud::class, 'solicitud_id');
    }

    public function usuarioAccion()
    {
        return $this->belongsTo(User::class, 'id_usuario_accion');
    }

    public function buzon()
    {
        return $this->belongsTo(Buzon::class, 'id_buzon', 'id_buzon');
    }
}
