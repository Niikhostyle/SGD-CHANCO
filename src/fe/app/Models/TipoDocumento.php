<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model{

    protected $table = "tipo_documento";
    protected $primaryKey = 'id_tipo_documento';

    protected $hidden = ['created_at', 'updated_at'];

}