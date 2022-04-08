<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model{

    protected $table = "users";
    protected $primaryKey = 'id';

    
    public function usuarios_buzon()
    {
        return $this->hasMany(BuzonUsuario::class, 'id_usuario', 'id');
    } 

}