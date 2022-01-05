<?php

namespace App\Http\Controllers;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use App\Models\Documento;
use App\Models\DocumentoBuzon;
use App\Models\DocumentoBuzonArchivo;
use App\Models\DocumentoBuzonBitacora;
use App\Models\TipoDocumento;
use Illuminate\Support\Facades\DB;
use App\Validator\DocumentoValidator;


class ValidadorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $validator;

    public function __construct(DocumentoValidator $documentoValidator)
    {
        $this->validator = $documentoValidator;
    }
    //

    public function verificaDocumento(Request $request){
        if($request->isJson())
        {
            try 
            {
                //return "hola";
                $datosRequest = $request->json()->all();
                
                //$validator = $this->validator->validateFieldUser($datosRequest);
                //if ($validator->fails())
                  //  return $this->respondFail('Falla al obtener documento: revisar datos de entrada');

                $datosDocumento = Documento::findOrFail($datosRequest['id_documento'],['id_documento', 'id_nivel_acceso',
                                                                                    'materia','fecha', 'identificador', 'hash_validacion', 'folio']);
                
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
}
