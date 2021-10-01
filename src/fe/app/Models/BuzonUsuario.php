<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuzonUsuario extends Model{

    protected $table = "buzon_usuario";
    protected $primaryKey = 'id_buzon_usuario';

    protected $fillable = [
        'id_buzon', 'id_usuario'
    ];

    public function buzon()
    {
        return $this->belongsTo(Buzon::class, 'id_buzon', 'id_buzon');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

}