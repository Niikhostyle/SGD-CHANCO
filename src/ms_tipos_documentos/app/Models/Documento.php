<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model{

    protected $table = "documento";
    protected $primaryKey = 'id_documento';

    public function tipo_documento()
    {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento', 'id_tipo_documento');
    }

}