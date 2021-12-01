<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BuscadorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function ver(Request $request)
    {
        if($request->isJson())
        {
            try 
            {
                $datosRequest = $request->json()->all();
                
                //$validator = $this->validator->validateField($datosRequest);
                //if ($validator->fails())
                //    return $this->respondFail('Falla al obtener documento: revisar datos de entrada');

                $datosDocumento = Documento::findOrFail($datosRequest['id_documento']);
                $datosDocumento->rel_documento_buzon; 
                $datosDocumento->rel_tipo_documento;
                                
                return $this->respondSuccess($datosDocumento, 200);
            }  
            catch (ModelNotFoundException $e) 
            {
                return $this->respondError('Documento no existe', 500);
            } 
        }
        else 
            return $this->respondError('Json inválido', 406);
    }

    public function listarDocumentos(Request $request){
        if($request->isJson())
        {
            try
            {

                $datosRequest = $request->json()->all();

                $validator = $this->validator->validateFieldUser($datosRequest);

                if ($validator->fails())
                    return $this->respondFail('Falla al listar los documentos: revisar datos de entrada');


                return datatables(
                    DB::table('documento_buzon')
                    ->join('documento', 'documento_buzon.id_documento', '=', 'documento.id_documento')
                    ->join('estado_documento', 'documento_buzon.id_estado_documento', '=', 'estado_documento.id_estado_documento')
                    ->join('tipo_documento', 'documento.id_tipo_documento', '=', 'tipo_documento.id_tipo_documento')
                    ->join('tipo_origen', 'tipo_documento.id_tipo_origen', '=', 'tipo_origen.id_tipo_origen')
                    ->join('buzon', 'documento_buzon.id_buzon', '=', 'buzon.id_buzon')
                    ->join('buzon_usuario', 'buzon.id_buzon', '=', 'buzon_usuario.id_buzon')
                    ->select(
                        'buzon.nombre as nombre_buzon',
                        'estado_documento.nombre_corto as estado_documento',
                        'documento.id_documento as id_documento',
                        'documento.fecha as fecha_documento',
                        'tipo_documento.nombre as tipo_documento',
                        'tipo_origen.nombre as origen',
                        'documento.materia as materia',
                        'documento_buzon.favorito as estado_favorito',
                        'documento_buzon.id_documento_buzon as id_documento_buzon'
                        )
                    ->where('buzon_usuario.id_usuario','=',$datosRequest['id_usuario'])
                    //->where('documento_buzon.favorito','=',1)
                )
                ->toJson();

            }
            catch (ModelNotFoundException $e)
            {
                return $this->respondError('No existen datos', 500);
            }
        }
        else
            return $this->respondError('Json inválido', 406);
    }
}