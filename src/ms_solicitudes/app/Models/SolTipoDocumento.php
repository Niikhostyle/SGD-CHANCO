<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolTipoDocumento extends Model
{
    protected $table = 'sol_tipo_documentos';

    protected $fillable = [
        'tipo_solicitud', 'regimen_laboral', 'nombre', 'activo',
        'categoria', 'consume_saldo', 'requiere_fe', 'numero_firmas',
        'primer_buzon_editable', 'id_tipo_documento',
        'descripcion', 'nombre_corto', 'nombre_corto_firma',
        'id_tipo_origen', 'id_tipo_flujo', 'id_tipo_avance', 'id_tipo_folio',
        'id_tipo_asignacion_folio', 'derivar_primera_firma', 'derivar_ultima_firma',
        'buzon_primera_firma', 'buzon_ultima_firma',
        'plantilla_encabezado_html', 'plantilla_cuerpo_html',
        'plantilla_distribucion_html', 'texto_documento',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'consume_saldo' => 'boolean',
        'requiere_fe' => 'boolean',
        'primer_buzon_editable' => 'boolean',
        'numero_firmas' => 'integer',
    ];

    public function buzonesFlujo()
    {
        return $this->hasMany(SolTipoDocumentoBuzon::class, 'sol_tipo_documento_id')->orderBy('orden');
    }
}
