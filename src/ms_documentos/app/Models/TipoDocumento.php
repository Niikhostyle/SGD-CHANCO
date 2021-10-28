<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model{

    protected $table = "tipo_documento";
    protected $primaryKey = 'id_tipo_documento';

    protected $hidden = ['created_at', 'updated_at'];

    public function buzones_flujo()
    {
        return $this->hasMany(TipoDocumentoBuzon::class, 'id_tipo_documento', 'id_tipo_documento')->select(['id_tipo_documento_buzon','id_buzon','orden']);
    } 

    
}