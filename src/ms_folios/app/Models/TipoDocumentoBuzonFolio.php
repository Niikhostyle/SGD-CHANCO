<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumentoBuzonFolio extends Model{

    protected $table = "tipo_documento_buzon_folio";
    protected $primaryKey = 'id_tipo_documento_buzon_folio';

    protected $hidden = ['created_at', 'updated_at','id_tipo_documento_buzon'];

    protected $fillable = [
        'id_tipo_documento_buzon',
        'id_tipo_documento',
        'id_buzon',
        'anio',
        'valor'
    ];


    public function rel_buzon()
    {
        return $this->belongsTo(Buzon::class, 'id_buzon', 'id_buzon');
    }

    public function rel_tipo_documento()
    {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento', 'id_tipo_documento');
    }

}

