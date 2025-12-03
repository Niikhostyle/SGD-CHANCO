<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Buzon;
use App\Models\Documento;
use App\Models\TipoDestino;

class DocumentoBuzon extends Model{

    protected $table = "documento_buzon";
    protected $primaryKey = 'id_documento_buzon';

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'id_documento',
        'id_buzon',
        'id_carpeta',
        'id_estado_documento',
        'id_tipo_destino',
        'id_documento_buzon_padre',
        'json_acciones',
        'fecha',
        'anterior',
        'comentario_principal',
        'comentario_secundario',
        'notificado',
        'recibido',
        'contestar_hasta',
        'favorito'
    ];

    public function rel_buzon()
    {
        return $this->belongsTo(Buzon::class, 'id_buzon', 'id_buzon');
    }

    public function rel_documento()
    {
        return $this->belongsTo(Documento::class, 'id_documento', 'id_documento');
    }

    public function tipoDestino()
    {
        return $this->belongsTo(TipoDestino::class, 'id_tipo_destino', 'id_tipo_destino');
    }

    public function rel_documento_buzon_bitacora()
    {
        return $this->hasMany(DocumentoBuzonBitacora::class, 'id_documento_buzon', 'id_documento_buzon');//->select(['id_tipo_documento_buzon','id_buzon','orden']);
    }

    public function rel_documento_buzon_archivo()
    {
        return $this->hasMany(DocumentoBuzonArchivo::class, 'id_documento_buzon', 'id_documento_buzon');//->select(['id_tipo_documento_buzon','id_buzon','orden']);
    }
}
