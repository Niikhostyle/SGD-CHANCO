<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumentoBuzonAccion extends Model{

    protected $table = "tipo_documento_buzon_accion";
    protected $primaryKey = 'id_tipo_documento_buzon_accion';

    protected $fillable = [
        'id_tipo_documento_buzon', 'id_accion'
    ];

    public function tipo_doc_buzon()
    {
        return $this->belongsTo(TipoDocumentoBuzon::class, 'id_tipo_documento_buzon', 'id_tipo_documento_buzon');
    }

}