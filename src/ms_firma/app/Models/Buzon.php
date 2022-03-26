<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buzon extends Model{

    protected $table = "buzon";
    protected $primaryKey = 'id_buzon';

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'nombre', 'nombre_corto', 'id_tipo_buzon', 'id_tipo_firma'
    ];
    
}