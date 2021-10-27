<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model{

    protected $table = "tipo_documento";
    protected $primaryKey = 'id_tipo_documento';

    protected $hidden = ['created_at', 'updated_at','id_tipo_documento_buzon'];

    protected $fillable = [
        'nombre', 'nombre_corto', 'descripcion', 'id_tipo_origen', 'id_tipo_flujo', 'id_tipo_folio', 'id_tipo_avance', 'id_tipo_asignacion_folio', 'requiere_fe', 'plantilla_encabezado', 'plantilla_cuerpo'
    ];

    public function buzones_flujo()
    {
        return $this->hasMany(TipoDocumentoBuzon::class, 'id_tipo_documento', 'id_tipo_documento')->select(['id_tipo_documento_buzon','id_buzon','orden']);
    }

}
