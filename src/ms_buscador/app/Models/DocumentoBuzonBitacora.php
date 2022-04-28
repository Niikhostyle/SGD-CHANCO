<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoBuzonBitacora extends Model{

    protected $table = "documento_buzon_bitacora";
    protected $primaryKey = 'id_documento_buzon_bitacora';

    protected $fillable = [
        'id_documento_buzon',
        'id_accion',
        'fecha',
        'id_usuario',
        'comentario',
        'informacion_solicitud',
        'mensaje_respuesta'
    ];

    public function rel_documento_buzon()
    {
        return $this->belongsTo(DocumentoBuzon::class, 'id_documento_buzon', 'id_documento_buzon');
    }


}