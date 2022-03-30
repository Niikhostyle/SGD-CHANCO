<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoFirma extends Model{

    protected $table = "tipo_firma";
    protected $primaryKey = 'id_tipo_firm';

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'nombre', 'sigla', 'id_tipo_firma'
    ];
    
}