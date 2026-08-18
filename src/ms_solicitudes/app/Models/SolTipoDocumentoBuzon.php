<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolTipoDocumentoBuzon extends Model
{
    protected $table = 'sol_tipo_documento_buzon';

    protected $fillable = [
        'sol_tipo_documento_id', 'id_buzon', 'nombre_buzon', 'orden', 'acciones',
    ];

    protected $casts = [
        'acciones' => 'array',
        'orden' => 'integer',
        'id_buzon' => 'integer',
    ];

    public function tipo()
    {
        return $this->belongsTo(SolTipoDocumento::class, 'sol_tipo_documento_id');
    }
}
