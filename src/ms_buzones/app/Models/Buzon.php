<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buzon extends Model{

    protected $table = "buzon";
    protected $primaryKey = 'id_buzon';

    protected $fillable = [
        'nombre', 'nombre_corto', 'id_tipo_buzon'
    ];

    public function usuarios_asignados()
    {
        return $this->hasMany(BuzonUsuario::class, 'id_buzon', 'id_buzon');
    } 

    //validaciones

    public static $reglasValidacionUpdate = [
        'nombre_buzon' => 'required|max:255',
        'nombre_corto_buzon' => 'required|max:255'
    ];



    public static function reglasValidacion()
    {
        return [
            'nombre_buzon' => 'required|max:255',
            'nombre_corto_buzon' => 'required|max:255'
        ];
    }
/*
,[
            'nombre_buzon.required' => 'Campo nombre es requerido',
            'nombre_corto_buzon.required' => 'Campo nombre buzón es requerido'
        ]
*/
}