<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentoBitacora;

class Documento extends Model{

    protected $table = "documento";
    protected $primaryKey = 'id_documento';

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'id_tipo_documento',
        'id_nivel_acceso',
        'json_tipo_documento',
        'identificador',
        'folio',
        'fecha',
        'referencias',
        'materia',
        'anterior',
        'descripcion',
        'encabezado',
        'cuerpo',
        'efectos_terceros',
        'hash_validacion',
        'archivo_existente',
        'finalizado',
        'paginas_archivo',
        'img_firma',
        'distribucion',
        'anio_tramitacion',
        'estado_tramitacion'
    ];
    
    protected $casts = [
        'fecha' => 'date',
        'referencias'=>'array',    
        'json_respuesta_a'=>'array',    
    ];

    public function rel_tipo_documento()
    {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento', 'id_tipo_documento');
    }

    public function rel_documento_buzon()
    {
        return $this->hasMany(DocumentoBuzon::class, 'id_documento', 'id_documento');//->select(['id_tipo_documento_buzon','id_buzon','orden']);
    }

    public function buzones_flujo()
    {
        return $this->hasMany(TipoDocumentoBuzon::class, 'id_tipo_documento', 'id_tipo_documento')->select(['id_tipo_documento_buzon','id_buzon','orden']);
    }
    public function nivelAcceso(){
        return $this->belongsTo(NivelAcceso::class, 'id_nivel_acceso', 'id_nivel_acceso');
    }

    public function tipo_documento(){
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento', 'id_tipo_documento');
    }
    public function estado_tramitacion(){
        return $this->belongsTo(EstadoTramitacion::class,'estado_tramitacion', 'id');
    }
}

 