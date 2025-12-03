<?php

namespace App\Http\Controllers;

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Documento;

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
        ->get(config('sgd.api_documento').'/api/sgd-documentos/listarFavoritos');

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
        $datosNivelAcceso = \App\Models\NivelAcceso::all('id_nivel_acceso', 'nombre');
        $datosAccion = \App\Models\Accion::all('id_accion', 'id_tipo_accion','nombre');
        $datosEstadoTramitacion =  \App\Models\EstadoTramitacion::All();

        //listado de buzones
        $aBuzones = array();
        $listado_buzones = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)
        ->withBody(json_encode([
            'texto_busqueda' => '',
        ]), 'json')
        ->get(config('sgd.api_buzones').'/api/sgd-buzones/listar_todos');

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
            'nivel_acceso' => $datosNivelAcceso,
            'listadoEstadoTramitacion' => $datosEstadoTramitacion
        ]);
    }

    public function estado($id, Request $request)
    {
        $sesion_key = AppServiceProvider::session_key_general();
        $response = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)
        ->put(config('sgd.api_documento').'/api/sgd-documentos/estadoFavorito', [
            'id_documento' => $id,
            'id_usuario' => Auth::user()->id,
            'accion' => $request['accion']
        ]);

        $response_json=response()->json($response->json());
        return $response_json;
    }
}
