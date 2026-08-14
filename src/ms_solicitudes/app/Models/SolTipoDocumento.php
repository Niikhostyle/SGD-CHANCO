<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolTipoDocumento extends Model
{
    protected $table = 'sol_tipo_documentos';
    protected $fillable = [
        'tipo_solicitud', 'regimen_laboral', 'nombre', 'activo',
        'plantilla_encabezado_html', 'plantilla_cuerpo_html',
        'plantilla_distribucion_html', 'texto_documento',
    ];

    protected $casts = ['activo' => 'boolean'];
}
