<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumentoBuzon extends Model{

    protected $table = "tipo_documento_buzon";
    protected $primaryKey = 'id_tipo_documento_buzon';

    protected $fillable = [
        'id_tipo_documento_buzon', 'id_tipo_documento', 'id_buzon', 'orden'
    ];

    public function tipo_doc()
    {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento', 'id_tipo_documento');
    }

    public function acciones()
    {
        return $this->hasMany(TipoDocumentoBuzonAccion::class, 'id_tipo_documento_buzon', 'id_tipo_documento_buzon')->select(['id_accion']);
    } 

}