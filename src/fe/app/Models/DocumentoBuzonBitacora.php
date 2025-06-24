<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoBuzonBitacora extends Model{

    protected $table = "documento_buzon_bitacora";
    protected $primaryKey = 'id_documento_buzon_bitacora';

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'id_documento_buzon',
        'id_accion',
        'fecha',
        'id_usuario',
        'comentario',
        'informacion_solicitud',
        'mensaje_respuesta'
    ];

    protected $casts = [
        'informacion_solicitud' => 'array',
    ];

    public function rel_documento_buzon()
    {
        return $this->belongsTo(DocumentoBuzon::class, 'id_documento_buzon', 'id_documento_buzon');
    }
    public function accion()
    {
        return $this->belongsTo(Accion::class, 'id_accion', 'id_accion');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }


}