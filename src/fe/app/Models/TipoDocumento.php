<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model{

    protected $table = "tipo_documento";
    protected $primaryKey = 'id_tipo_documento';

    protected $hidden = ['created_at', 'updated_at'];
/*
    protected $fillable = [
        
    ];

    public function usuarios_asignados()
    {
        return $this->hasMany(BuzonUsuario::class, 'id_buzon', 'id_buzon')->select(['id_usuario']);
    } 

    public function documentos_buzon()
    {
        return $this->hasMany(DocumentoBuzon::class, 'id_buzon', 'id_buzon');
    } 
    */
}