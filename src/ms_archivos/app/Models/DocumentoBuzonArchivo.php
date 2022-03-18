<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoBuzonArchivo extends Model
{
    use HasFactory;

    protected $table = "documento_buzon_archivo";
    protected $primaryKey = 'id_documento_buzon_archivo';

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'id_documento_buzon_archivo',
        'id_documento_buzon',
        'version',
        'id_tipo_archivo',
        'nombre_archivo_original',
        'nombre_archivo_codificado',
        'fecha'
    ];
}
