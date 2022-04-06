<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\DocumentoBuzonArchivo;
use Illuminate\Http\Request;
use App\Providers\AppServiceProvider;
use Illuminate\Cache\NullStore;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;
use PDF;

use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;

class DocumentoValidadorController extends Controller
{
    public function index()
    {
        $lista_documentos=['data'=>[
            0=>['hash_validacion'=>'','folio'=>'','fecha_documento'=>'','materia'=>'', 'id_documento'=>'', 'id_nivel_acceso'=>'', 'version'=>'']
        ]];
        $status=null;
        return View::make('validador.index',['lista_documentos'=>$lista_documentos, 'status'=>$status]);
    }

    public function store(Request $request)
    {
        
        $codigo = $request['codigo'];
        //return $codigo;
        $sesion_key =  AppServiceProvider::session_key_general();
        $lista_documentos = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)
        ->withBody(json_encode([
            'hash_validacion' => $codigo 
        ]), 'json')
        ->get('http://sgd_ms_documentos:3333/api/sgd-documentos/verificaDocumento');
        
        //return $lista_documentos;
        if($lista_documentos->failed()){
            //return $lista_documentos ;
            $mensaje= $lista_documentos->json()['data']['comentario'];

            $lista_documentos=['data'=>[
                0=>['hash_validacion'=>'sin datos','folio'=>'sin datos','fecha_documento'=>'sin datos','materia'=>'sin datos', 'id_nivel_acceso'=>'', 'version'=>'']
            ]];
            toast($mensaje,'error');
        }else{
            $lista_documentos->json();
        }

        $status = 1;
        foreach($lista_documentos['data'] as $list){
            if ($list['id_nivel_acceso']==2 || $list['id_nivel_acceso']==3 || $list['id_nivel_acceso']==1){
                $status = 0;

            }
        }
        
        
        
        return View::make('validador.index',['lista_documentos'=>$lista_documentos, 'status'=>$status]);
        
    }

    public function download(Request $request)
    {
        /*
        $pdf = PDF::loadView('pdf', [
    		'title' => 'CodeAndDeploy.com Laravel Pdf Tutorial',
    		'description' => 'This is an example Laravel pdf tutorial.',
    		'footer' => 'by <a href="https://codeanddeploy.com">codeanddeploy.com</a>'
    	]);
    
        return $pdf->download('sample.pdf');
        */
        
        //agregar version
        $dFechaCreacion = date('Y-m-d H:i:s');

        $numero = rand(100000,9999999);

        $fileName = 'prueba1';
        $nNombreArchivoCargar = $fileName . '-' . $numero;  
        $nDocumento = $request->idDocumento;

        //datos que llevara el pdf

        $datosDocumentos = Documento::where('id_documento','=', $nDocumento)
        ->select('cuerpo', 'encabezado','materia')
        ->first(); 
        
        //$pdf = PDF::loadView('pdf', $datosDocumentos);
        $data = PDF::loadView('pdf', $datosDocumentos)->save(storage_path('app/public/files/') . 'archivo_'.$nDocumento.'.pdf');

        if (file_exists(storage_path('app/public/files/') . 'archivo_'.$nDocumento.'.pdf'))
            return response()->json([
                'status' => 200, 
                'data' => 'ok'
            ], 200); 
        else
            return response()->json([
                'status' => 400, 
                'data' => 'error'
            ], 400);     

    }
    
}
