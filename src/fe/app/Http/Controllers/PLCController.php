<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\DocumentoBuzon;
use App\Models\DocumentoBuzonArchivo;
use App\Models\DocumentoBuzonArchivoDescarga;
use App\Models\EstadoTramitacion;
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
                                        )
                                        ->groupBy('documento_buzon_archivo.id_documento_buzon_archivo')
                                        ->groupBy('documento.folio')
                                        ->groupBy('tipo_documento.nombre_corto')
                                     //   ->groupBy('buzon_usuario.id_buzon_usuario')
                                        ->get();   
                                     
        $dFechaCreacion = date('Y-m-d H:i:s'); 
        foreach ($archivo as $file){              
            if(!isset($file->nombre_archivo_codificado)){
                abort(404);
            }
            $nombre = $file->nombre_archivo_codificado; 
            $ruta = storage_path('app/public/files/'.$nombre);
            return response()->download($ruta, $file->nombre_corto.'_'.$file->folio.'.pdf', [], 'inline');
           
        }
        abort(404);
    }

    public function getDocAnexo(Request $request){
        $nDocumento =  $request->idDocumento;
        $nAnexo =  (int)$request->nAnexo;
        //dd($request);
        $archivo = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon.id_documento_buzon', '=', 'documento_buzon_archivo.id_documento_buzon')
                                        ->join('buzon', 'documento_buzon.id_buzon', '=', 'buzon.id_buzon')
                                        ->join('buzon_usuario', 'buzon.id_buzon', '=', 'buzon_usuario.id_buzon')
                                        ->join('documento', 'documento.id_documento', '=', 'documento_buzon.id_documento')
                                        ->join('tipo_documento', 'tipo_documento.id_tipo_documento', '=', 'documento.id_tipo_documento')
                                        ->where('documento_buzon.id_documento', $nDocumento)
                                        ->where('id_tipo_archivo', 2)
                                        //->where('version', 1)
                                        ->select(
                                            'nombre_archivo_codificado',
                                            'id_documento_buzon_archivo',
                                            'documento.folio',
                                            'tipo_documento.nombre_corto'
                                        )
                                        ->groupBy('documento_buzon_archivo.id_documento_buzon_archivo')
                                        ->groupBy('documento.folio')
                                        ->groupBy('tipo_documento.nombre_corto')
                                        ->skip(($nAnexo-1))->take(1)
                                     //   ->groupBy('buzon_usuario.id_buzon_usuario')
                                        ->get();   
                                     
        $dFechaCreacion = date('Y-m-d H:i:s'); 
        foreach ($archivo as $file){              
            if(!isset($file->nombre_archivo_codificado)){
                abort(404);
            }
            $nombre = $file->nombre_archivo_codificado; 
            $ruta = storage_path('app/public/files/'.$nombre);
            return response()->download($ruta, $file->nombre_corto.'_'.$file->folio.'_anexo'.$nAnexo.'.pdf', [], 'inline');
           
        }
        abort(404);
    }

    public function indexTransparencia(){
        //dd("fffff");
        $sesion_key =  AppServiceProvider::session_key_general();
        $listado_tiposdoc = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(100)
        ->get(env('API_SGD_TIPO_DOCUMENTOS','http://sgd_ms_tipos_documentos:3333').'/api/sgd-tipodoc/ver_todos');

        if($listado_tiposdoc->failed()){
            $mensaje = $listado_tiposdoc->json()['data']['comentario'];
            toast($mensaje,'error');
        }
        else
        {
            $datosTipoDoc = $listado_tiposdoc['data'];

        }
        $listaTramites = EstadoTramitacion::all();

        return View::make('transparencia.index', [
           "listado_tiposdoc"=>$datosTipoDoc,
           "listado_tramites"=>$listaTramites
        ]);
    }
    
    public function getItems(Request $request){

        $tipo = $request->get("tipo_documento",7);
        $ini = $request->get("buscar_fecha_ini",date('Y-m-d'));
        $fin = $request->get("buscar_fecha_fin",date('Y-m-d'));
        $estado = $request->get("estado_tramitacion", null);

        $archivos = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon.id_documento_buzon', '=', 'documento_buzon_archivo.id_documento_buzon')
            ->join('buzon', 'documento_buzon.id_buzon', '=', 'buzon.id_buzon')
            ->join('buzon_usuario', 'buzon.id_buzon', '=', 'buzon_usuario.id_buzon')
            ->join('documento', 'documento.id_documento', '=', 'documento_buzon.id_documento')
            ->join('estado_tramitacion', 'estado_tramitacion.id', '=', 'documento.estado_tramitacion')
            ->join('tipo_documento', 'tipo_documento.id_tipo_documento', '=', 'documento.id_tipo_documento')
           // ->where('documento_buzon.id_documento', $nDocumento)
            ->whereNotNull('folio')
            //->where('tipo_documento.nombre_corto','DGM')
            ->where('id_tipo_archivo', 1)
            ->where('version', 1)
            ->where('tipo_documento.id_tipo_documento',$tipo)
            ->whereBetween('documento.fecha',[$ini,$fin])

            ->select([
                'documento.id_documento',
                'tipo_documento.nombre_corto',
                'documento.folio',
                'documento.fecha',
                'documento.materia',
                'estado_tramitacion.nombre as nombre_estado_tramitacion',
                'nombre_archivo_codificado',
                'id_documento_buzon_archivo',
                DB::raw('(SELECT count(id_documento_buzon_archivo) FROM public.documento_buzon_archivo WHERE id_documento_buzon in(SELECT id_documento_buzon FROM documento_buzon WHERE id_documento=documento.id_documento) AND id_tipo_archivo = 2) as num_anexos')                
            ])
            ->groupBy('documento_buzon_archivo.id_documento_buzon_archivo')
            ->groupBy('documento.folio')
            ->groupBy('documento.id_documento')
            ->groupBy('tipo_documento.nombre_corto')
            ->orderBy('documento.folio')
            ->groupBy('nombre_estado_tramitacion');
            //->get();  
            

            if($estado!=null || $estado!=''){
                $archivos->where('documento.estado_tramitacion', $estado);
                //dd($archivos->get());
            }
           // dd
            $archivos = $archivos->get();
            return response()->json(["data"=>$archivos]);
    }
}