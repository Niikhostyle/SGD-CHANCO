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
use App\Models\TipoDocumentoBuzonFolio;
use Illuminate\Support\Facades\DB;
use App\Validator\DocumentoValidator;
use phpDocumentor\Reflection\PseudoTypes\True_;

class FolioController extends Controller{

    /**
     * @BuzonValidator
     */
    private $validator;


    public function asignaFolio(Request $request){
        $nFolio = 1;
        $ultimoFolio = 0;
        $td = $request['id_tipo_documento'];
        $datosRequest = $request->json()->all();
      
        $datosFolio = TipoDocumentoBuzonFolio::all();

        //return $request;
        if ($datosRequest['id_tipo_folio'] == 1)
        {
       
            foreach($datosFolio as $datos)
            {
                if( $datos['anio'] == $datosRequest['anio'])
                {
                    if( $datos['id_tipo_documento'] == $datosRequest['id_tipo_documento'])
                    {

                        $nFolio = $datos['valor'] +1;

                    }
                }

            }
            // por tipo de documento y año
            $documentoBuzon = TipoDocumentoBuzonFolio::updateOrCreate([
                'id_tipo_documento' => $datosRequest['id_tipo_documento'],
                'anio' => $datosRequest['anio'], 
                
            ],[
                'id_tipo_documento' => $datosRequest['id_tipo_documento'],
                'anio' => $datosRequest['anio'], 
                'valor' => $nFolio,
            ]);
        
        }

        if ($datosRequest['id_tipo_folio'] == 2)
        {

            foreach($datosFolio as $datos)
            {
                if( $datos['anio'] == $datosRequest['anio'])
                {
                    if( $datos['id_tipo_documento'] == $datosRequest['id_tipo_documento'])
                    {
                        if( $datos['id_buzon'] == $datosRequest['id_buzon']){

                            $nFolio = $datos['valor'] +1;
                        }
                        
            
                    }
                }

            }
            // por tipo de documento,buzon y año
            $documentoBuzon = TipoDocumentoBuzonFolio::updateOrCreate([
                'id_tipo_documento' => $datosRequest['id_tipo_documento'],
                'id_buzon' => $datosRequest['id_buzon'],
                'anio' => $datosRequest['anio'], 
                
            ],[
                'id_tipo_documento' => $datosRequest['id_tipo_documento'],
                'anio' => $datosRequest['anio'], 
                'id_buzon' => $datosRequest['id_buzon'],
                'valor' => $nFolio,
            ]);
            
        } 

        if ($datosRequest['id_tipo_folio'] == 3)
        {
            return $nFolio;
        }
        

        
        return $nFolio;

    }
}
