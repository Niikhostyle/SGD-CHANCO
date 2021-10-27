<?php
namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Buzon;
use App\Models\Users;
use App\Models\BuzonUsuario;
use App\Models\DocumentoBuzon;
use App\Validator\BuzonValidator;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class DocumentoController extends Controller{


    public function listar(Request $request){
        if($request->isJson())
        {
            try
            {

                $datosRequest = $request->json()->all();
                //$validator = $this->validator->validateFieldBuzon($datosRequest);

                return datatables(
                    DB::table('documento_buzon')
                    ->join('documento', 'documento_buzon.id_documento', '=', 'documento.id_documento')
                    ->join('estado_documento', 'documento_buzon.id_estado_documento', '=', 'estado_documento.id_estado_documento')
                    ->join('tipo_documento', 'documento.id_tipo_documento', '=', 'tipo_documento.id_tipo_documento')
                    //->leftJoin('documento_buzon_bitacora', 'documento.id_tipo_documento', '=', 'documento_buzon_bitacora.id_tipo_documento')
                    ->leftJoin('documento_buzon_bitacora', function ($join) {
                        $join->on('documento_buzon.id_documento_buzon', '=', 'documento_buzon_bitacora.id_documento_buzon')
                             ->where('documento_buzon_bitacora.id_accion', '=', 3);
                    })
                    ->select(
                        'documento_buzon.id_buzon',
                        'documento_buzon.recibido',
                        'estado_documento.nombre_corto',
                        'documento_buzon.fecha',
                        "documento_buzon_bitacora.fecha as fecha_recepcion",
                        'tipo_documento.nombre as tipo_documento',
                        'documento_buzon.json_acciones as destinatario',
                        'documento.materia',
                        'documento.json_respuesta_a as respuesta_a',
                        'documento.fecha as fecha_documento'
                        )
                    ->where('documento_buzon.id_buzon','=',$datosRequest['id_buzon'])
                    ->where('documento_buzon.id_carpeta','=',$datosRequest['id_carpeta'])
                    ->whereIn('documento_buzon.id_estado_documento',array(1,2))
                )->toJson();
                    /*
                    id_buzon            documento_buzon->id_buzon
                    palomitas           documento_buzon->recibido
                    estado              documento_buzon->id_estado_documento / estado_documento->nombre_corto
                    fecha_despacho      documento_buzon->fecha
                 **   fecha_recepcion     ??? si está en (documento_buzon.id_estado_documento==4) "pendiente" La fecha se obtiene de la tabla docuento buzon bitacora campo fecha cuando esta en el id_accion
                    tipo_decumento      documento->id_tipo_documento / tipo_documento->nombre
                    destinatario        documento_buzon->json_acciones
                    materia             documento->materia
                    respuesta_a         documento->json_respuesta_a [{id_documento}]
                    fecha_documento     documento->fecha
                    */
            }
            catch (ModelNotFoundException $e)
            {
                return $this->respondError('No existe buzón', 500);
            }
        }
        else
            return $this->respondError('Json inválido', 406);
    }

}
