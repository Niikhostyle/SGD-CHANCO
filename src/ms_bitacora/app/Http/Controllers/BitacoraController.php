<?php
namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use App\Models\Documento;
use App\Models\DocumentoBuzon;
use App\Models\DocumentoBuzonBitacora;
use App\Models\TipoDocumento;
use Illuminate\Support\Facades\DB;
use App\Validator\DocumentoValidator;


class BitacoraController extends Controller{

    /**
     * @BuzonValidator
     */
    private $validator;

    public function __construct(DocumentoValidator $documentoValidator)
    {
        $this->validator = $documentoValidator;
    }

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
                //$datosDocumento->rel_documento_buzon; 
                //$datosDocumento->rel_tipo_documento;
                                
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



    public function listarDocumentosBitacora(Request $request){
        if($request->isJson())
        {
            try
            {

                $datosRequest = $request->json()->all();

                return datatables(
                    DB::table('documento_buzon_bitacora')
                    ->join('documento_buzon', 'documento_buzon_bitacora.id_documento_buzon', '=', 'documento_buzon.id_documento_buzon')
                    ->join('accion', 'documento_buzon_bitacora.id_accion', '=', 'accion.id_accion')
                    ->join('documento', 'documento_buzon.id_documento', '=', 'documento.id_documento')
                    ->join('tipo_documento', 'documento.id_tipo_documento', '=', 'tipo_documento.id_tipo_documento')
                    ->join('buzon', 'documento_buzon.id_buzon', '=', 'buzon.id_buzon')
                    ->join('buzon_usuario', 'buzon.id_buzon', '=', 'buzon_usuario.id_buzon')
                    ->join('tipo_destino', 'documento_buzon.id_tipo_destino', '=', 'tipo_destino.id_tipo_destino')
                    ->select(
                        'documento_buzon_bitacora.id_accion as accion',
                        'documento.fecha as fecha_documento',
                        'buzon.nombre as buzon_origen',
                        'accion.nombre as nombre_accion',
                        'mensaje_respuesta as mensaje_respuesta',
                        'documento_buzon.id_documento_buzon as id_documento_buzon',
                        'tipo_documento.nombre as tipo_documento',
                        'tipo_destino.id_tipo_destino as tipo_destino'
                        )
                    ->where('buzon_usuario.id_usuario','=',$datosRequest['id_usuario'])
                    
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
