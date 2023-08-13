<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\DocumentoBuzon;
use App\Models\DocumentoBuzonArchivo;
use App\Models\DocumentoBuzonArchivoDescarga;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use League\CommonMark\Node\Block\Document;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

use PDF;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;


class PLCController extends Controller
{
    public function getDoc(Request $request){
        $nDocumento =  $request->idDocumento;

        $archivo = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon.id_documento_buzon', '=', 'documento_buzon_archivo.id_documento_buzon')
                                        ->join('buzon', 'documento_buzon.id_buzon', '=', 'buzon.id_buzon')
                                        ->join('buzon_usuario', 'buzon.id_buzon', '=', 'buzon_usuario.id_buzon')
                                        ->join('documento', 'documento.id_documento', '=', 'documento_buzon.id_documento')
                                        ->join('tipo_documento', 'tipo_documento.id_tipo_documento', '=', 'documento.id_tipo_documento')
                                        ->where('documento_buzon.id_documento', $nDocumento)
                                        ->where('id_tipo_archivo', 1)
                                        ->where('version', 1)
                                        ->select(
                                            'nombre_archivo_codificado',
                                            'id_documento_buzon_archivo',
                                            'documento.folio',
                                            'tipo_documento.nombre_corto'
                                            
                                         //   'buzon_usuario.id_buzon_usuario as id_buzon_usuario'

                                        )
                                        ->groupBy('documento_buzon_archivo.id_documento_buzon_archivo')
                                        ->groupBy('documento.folio')
                                        ->groupBy('tipo_documento.nombre_corto')
                                     //   ->groupBy('buzon_usuario.id_buzon_usuario')
                                        ->get();   
                                     
        $dFechaCreacion = date('Y-m-d H:i:s'); 
        foreach ($archivo as $file)
            $nombre = $file->nombre_archivo_codificado; 
            $ruta = storage_path('app/public/files/'.$nombre);
            return response()->download($ruta, $file->nombre_corto.'_'.$file->folio.'.pdf', [], 'inline');
            // DocumentoBuzonArchivoDescarga::create([
            //     'id_documento_buzon_archivo' => $file['id_documento_buzon_archivo'],
            //     'id_buzon_usuario' =>  $file['id_buzon_usuario'],
            //     'fecha' => $dFechaCreacion 
            // ]); 
              
        //$ruta = ('files/') . 'principal_'.$nDocumento.'_.pdf';
      
        dd($archivo->toArray());
        

        // $result = array('status' => '200', 'data' => array('data' => $ruta));
        // //return $ruta;
        // return response()->json($result, '200');  

    }
}