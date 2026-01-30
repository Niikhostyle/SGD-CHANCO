<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Documento;

class TipoDocumento extends Model{

    protected $table = "tipo_documento";
    protected $primaryKey = 'id_tipo_documento';

    protected $hidden = ['created_at', 'updated_at'];

    public function documentos(){
        return $this->hasMany(Documento::class, 'id_tipo_documento', 'id_tipo_documento');
    }
    public function tipo_asignacion_folio(){
        return $this->belongsTo(TipoAsignacionFolio::class, 'id_tipo_asignacion_folio', 'id_tipo_asignacion_folio');
    }
}