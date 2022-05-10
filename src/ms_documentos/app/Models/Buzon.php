<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buzon extends Model{

    protected $table = "buzon";
    protected $primaryKey = 'id_buzon';

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'nombre', 'nombre_corto', 'id_tipo_buzon', 'cargo_firma'
    ];

    public function usuarios_asignados()
    {
        return $this->hasMany(BuzonUsuario::class, 'id_buzon', 'id_buzon')->select(['id_usuario']);
    } 

    public function documentos_buzon()
    {
        return $this->hasMany(DocumentoBuzon::class, 'id_buzon', 'id_buzon');
    } 
}