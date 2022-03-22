<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoBuzonArchivoDescarga extends Model
{
    use HasFactory;

    protected $table = "documento_buzon_archivo_descarga";
    protected $primaryKey = 'id_documento_buzon_archivo_descarga';

    protected $fillable = [
        'id_documento_buzon_archivo_descarga',
        'id_documento_buzon_archivo',
        'id_buzon_usuario'
    ];
}