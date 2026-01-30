<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumentoBuzonFolio extends Model{

    protected $table = "tipo_documento_buzon_folio";
    protected $primaryKey = 'id_tipo_documento_buzon_folio';

    protected $fillable = [
        'id_tipo_documento',
        'id_buzon',
        'anio',
        'valor',
    ];

    public function tipo_documento()
    {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento', 'id_tipo_documento');
    }

}