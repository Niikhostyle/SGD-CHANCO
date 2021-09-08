<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model{

    protected $table = "usuario";
    protected $primaryKey = 'id_usuario';

    public function usuarios_buzon()
    {
        return $this->hasMany(BuzonUsuario::class, 'id_usuario', 'id_usuario');
    } 

}