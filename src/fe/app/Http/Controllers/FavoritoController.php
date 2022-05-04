<?php

namespace App\Http\Controllers;

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sesion_key =  AppServiceProvider::session_key_general();
        $lista_favoritos = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->withBody(json_encode([
            'id_usuario' => Auth::user()->id,
        ]), 'json')
        ->get('http://sgd_ms_documentos:3333/api/sgd-documentos/listarFavoritos');

        if($lista_favoritos->failed()){
            $mensaje= $lista_favoritos->json()['data']['comentario'];

            $lista_favoritos=['data'=>[
                0=>['nombre_buzon'=>'','estado_documento'=>'','id_documento'=>'','fecha_documento'=>'','origen'=>'','materia'=>'','estado_favorito'=>'']
            ]];
            toast($mensaje,'error');
        }else{
            $lista_favoritos->json();
        }

        //parametros
        $listado_parametros = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(13)
        ->get('http://sgd_ms_parametros:3333/api/sgd-parametros/traer');

        if($listado_parametros->failed()){
            toast("Error al mostrar datos",'error');
        }
        
        $datosNivelAcceso = $listado_parametros['data']['nivel_acceso'];
        $datosAccion = $listado_parametros['data']['accion'];

        //listado de buzones
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

            foreach ($listado_buzones['data'] as $dato)
            {
                $aBuzones[$dato['id_buzon']] = $dato['nombre'];                  
            }
        }

        return View::make('favorito.index', [
            'lista_favoritos'=>$lista_favoritos,
            'listadoBuzones'=>$aBuzones,
            'listadoAcciones' => $datosAccion,
            'nivel_acceso' => $datosNivelAcceso
        ]);
    }

    public function estado($id, Request $request)
    {
        $sesion_key = AppServiceProvider::session_key_general();
        $response = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)
        ->put('http://sgd_ms_documentos:3333/api/sgd-documentos/estadoFavorito', [
            'id_documento' => $id,
            'id_usuario' => Auth::user()->id,
            'accion' => $request['accion']
        ]);

        $response_json=response()->json($response->json());
        return $response_json;
    }
}
