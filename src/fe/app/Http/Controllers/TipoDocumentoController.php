<?php

namespace App\Http\Controllers;

use App\Models\TipoDocumento;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

class TipoDocumentoController extends Controller
{
    public function index(){

        $sesion_key =  AppServiceProvider::session_key_general();

        $listado_tiposdoc = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)
        ->get('http://sgd_ms_tipos_documentos:3333/api/sgd-tipodoc/ver_todos');

        if($listado_tiposdoc->failed()){
            $mensaje = $listado_tiposdoc->json()['data']['comentario'];

            toast($mensaje,'error');
        }

        $listado_parametros = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(13)
        ->get('http://sgd_ms_parametros:3333/api/sgd-parametros/traer');
        
        if($listado_parametros->failed()){
            $mensaje= $listado_parametros->json()['data']['comentario'];

            toast($mensaje,'error');
        }else{
            
            $datosFlujo = $listado_parametros->json()['data']['flujo'];
            $datosOrigen = $listado_parametros->json()['data']['origen'];

        }

        return View::make('tipo_documento.index',['tipos_documentos'=>$listado_tiposdoc,'datos_flujo'=>$datosFlujo,'datos_origen'=>$datosOrigen]);
    }

    

}
