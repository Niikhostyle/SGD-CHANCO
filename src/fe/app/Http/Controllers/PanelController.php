<?php

namespace App\Http\Controllers;

use App\Models\Buzon;
use App\Models\Documento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;



class PanelController extends Controller
{
    
    Public function index(Request $request){
        $sesion_key =  AppServiceProvider::session_key_general();
        //parametros
        $listado_parametros = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(13)
        ->get('http://sgd_ms_parametros:3333/api/sgd-parametros/traer')->throw();

        if($listado_parametros->failed()){
            toast("Error al mostrar datos",'error');
        }
        
        $datosNivelAcceso = $listado_parametros['data']['nivel_acceso'];
        $datosAccion = $listado_parametros['data']['accion'];
        $datosAnios= $listado_parametros['data']['anio'];
        $datosBuzones = array();
        $aBuzones = array();

        $listado_buzones = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)
        ->withBody(json_encode([
            'texto_busqueda' => '',
        ]), 'json')
        ->get('http://sgd_ms_buzones:3333/api/sgd-buzones/listar_todos');

        if($listado_buzones->failed()){
            $mensaje = $listado_buzones->json()['data']['comentario'];

            $listado_buzones=['data'=>[
                0=>['id'=>'0','nombre'=>'Sin Datos']
            ]];
            //toast($mensaje,'error');
        }else{

            $datosBuzones = $listado_buzones['data'];
            foreach ($listado_buzones['data'] as $dato)
            {
                $aBuzones[$dato['id_buzon']] = $dato['nombre'];                  
            }
        }



        $documentos = Documento::select(DB::raw('count(1) as salida'))->first();
        $usuarios = User::select(DB::raw('count(1) as salida'))->where('id_estado_usuario',1)->first();
        $buzones = Buzon::select(DB::raw('count(1) as salida'))->first();
        $favoritos = DB::select('select count(1) as salida from documento_favorito_usuario where id_usuario ='.Auth::user()->id);

        $total_favoritos = "";
         foreach($favoritos as $d){
            $total_favoritos = $d->salida;
         }

         return View::make('panel.index',[
            'total_documentos' => $documentos->salida,
            'total_usuarios' => $usuarios->salida,
            'total_buzones' => $buzones->salida,
            'total_favoritos' => $total_favoritos,
            'nivel_acceso' => $datosNivelAcceso,
            'listadoAcciones' => $datosAccion,
            'listadoBuzones'=>$aBuzones,
            
        ]);       
    } 

    Public function captura(Request $request){
       
        $data['year'] = $request->select_anio;        
        session(['year' => $request->select_anio]);
        
        return redirect()->route('panel.index');


    }
}
