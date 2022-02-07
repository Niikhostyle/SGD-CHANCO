<?php
namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use App\Models\Documento;
use App\Models\DocumentoBuzon;
use App\Models\DocumentoBuzonArchivo;
use App\Models\DocumentoBuzonBitacora;
use App\Models\DocumentoFavoritoUsuario;
use App\Models\TipoDocumento;
use Illuminate\Support\Facades\DB;
use App\Validator\DocumentoValidator;


class FolioController extends Controller{

    /**
     * @BuzonValidator
     */
    private $validator;

    




    public function asignaFolio(Request $request){
        $nFolio = 1;

        //tipo_folio, buzon, periodo, id_td, si no viene el buzon,  dependiendo del tipo de folio el buzon no va, hay 2 tipos de folio
        $datosRequest = $request->json()->all();

       /** if ($datosRequest['tipo_folio'] == 0) 
               
        *    }
      *  if ($datosRequest['buzon'] == 0) 
          *  {
        *    
      *  *    }
      *  if ($datosRequest['periodo'] == 0) 
           * {
          *  
      *   *   }
      *  if ($datosRequest['id_td'] == 0) 
          *  {   
            
        *    } */


        $data = array(
                    'valor'=>$nFolio,
                    'tipo_documento'=>1 );

        $nFolio ++;

       
            
        return $data;
        
    }

    public function obtenerFolio(Request $request){
        if($request->isJson())
        {
            try
            {

                $datosRequest = $request->json()->all();

                $validator = $this->validator->validateFieldUser($datosRequest);

                //if ($validator->fails())
                  //  return $this->respondFail('Falla al listar los documentos: revisar datos de entrada');


                return datatables(
                    DB::table('documento_buzon')
                    ->join('documento', 'documento_buzon.id_documento', '=', 'documento.id_documento')
                    ->join('tipo_documento', 'documento.id_tipo_documento', '=', 'tipo_documento.id_tipo_documento')
                    ->join('buzon', 'documento_buzon.id_buzon', '=', 'buzon.id_buzon')
                    ->join('buzon_usuario', 'buzon.id_buzon', '=', 'buzon_usuario.id_buzon')
                    ->select(
                        'documento.folio as folio',
                        'documento.fecha as fecha_documento',
                        'tipo_documento.id_tipo_documento as id_tipo_documento',
                        'buzon.id_buzon as id_buzon'
                        )
                    ->where('documento.id_documento','=',$datosRequest['id_documento'])
                    //->where('buzon.id_buzon','=',1)
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
