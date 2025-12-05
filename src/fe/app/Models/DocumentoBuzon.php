<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Buzon;
use App\Models\Documento;
use App\Models\TipoDestino;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentoBuzon extends Model{

    protected $table = "documento_buzon";
    protected $primaryKey = 'id_documento_buzon';

    protected $fillable = [
        'id_documento',
        'id_buzon',
        'id_carpeta',
        'id_estado_documento',
        'id_tipo_destino',
        'id_documento_buzon_padre',
        'json_acciones',
        'fecha',
        'anterior',
        'comentario_principal',
        'comentario_secundario',
        'notificado',
        'recibido',
        'contestar_hasta',
        'favorito'
    ];
    public function buzon()
    {
        return $this->belongsTo(Buzon::class, 'id_buzon', 'id_buzon');
    }
    public function documento()
    {
        return $this->belongsTo(Documento::class, 'id_documento', 'id_documento');
    }

    public function tipoDestino()
    {
        return $this->belongsTo(TipoDestino::class, 'id_tipo_destino', 'id_tipo_destino');
    }

    public function estadoDocumentoBuzon()
    {
        return $this->belongsTo(EstadoDocumento::class, 'id_estado_documento', 'id_estado_documento');
    }

    public function bitacora(){
        return $this->hasMany(DocumentoBuzonBitacora::class, 'id_documento_buzon', 'id_documento_buzon');
    }
    public function archivos(): HasMany{
        return $this->hasMany(DocumentoBuzonArchivo::class, 'id_documento_buzon', 'id_documento_buzon');
    }

    public function scopePendientes($query)
    {
        return $query
            ->join('documento','documento.id_documento','=','documento_buzon.id_documento')
            ->where('id_estado_documento',4)
            ->where('id_carpeta',2)
            ->where('documento.anio_tramitacion',session('year'))
            ->select(['documento_buzon.*','documento.id_documento']);
    }

    public function scopePorRecibir($query)
    {
        return $query
        ->join('documento','documento.id_documento','=','documento_buzon.id_documento')
            ->where('id_estado_documento',3)
            ->where('id_carpeta',1)
            ->where('documento.anio_tramitacion',session('year'))
            ->select(['documento_buzon.*','documento.id_documento']);
    }

    public function scopeContadores($query){

        return DB::select("select 
            buzon.nombre,
            documento_buzon.id_buzon, 
            documento_buzon.id_carpeta,
            documento_buzon.id_estado_documento,
            count( documento_buzon.id_carpeta) as total
            from documento_buzon 
            join documento on documento.id_documento = documento_buzon.id_documento 
            join buzon on buzon.id_buzon = documento_buzon.id_buzon
            where documento.anio_tramitacion = ".session('year')." 
            and ( (documento_buzon.id_carpeta = 1 and documento_buzon.id_estado_documento=3) or (documento_buzon.id_carpeta = 2 and documento_buzon.id_estado_documento=4) )
            and documento_buzon.id_buzon in (select id_buzon from buzon_usuario where id_usuario = ".Auth::user()->id.") 
            group by documento_buzon.id_buzon, documento_buzon.id_carpeta, documento_buzon.id_estado_documento, buzon.nombre");

    }
}