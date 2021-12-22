<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoFavoritoUsuario extends Model{

    protected $table = "documento_favorito_usuario";
    protected $primaryKey = 'id_documento_favorito_usuario';

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'id_documento',
        'id_buzon',
        'id_usuario',
        'favorito'
    ];

}