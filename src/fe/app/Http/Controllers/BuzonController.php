<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBuzon;
use App\Models\Buzon;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

class BuzonController extends Controller
{
    public function index(){

        $sesion_key =  AppServiceProvider::session_key_general();

        $listado_buzones = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)
        ->withBody(json_encode([
            'texto_busqueda' => '',
        ]), 'json')
        ->get('http://sgd_ms_buzones:3333/api/sgd-buzones/listar_todos');

        if($listado_buzones->failed()){
            $mensaje = $listado_buzones->json()['data']['comentario'];

            $listado_buzones=['data'=>[
                0=>['id'=>'0','nombre'=>'Sin Datos','nombre_corto'=>'','total_us_asignados'=>'','total_us_asignados'=>'']
            ]];
            toast($mensaje,'error');
        }else{

            $aBuzones = $listado_buzones['data'];

            foreach ($aBuzones as $key => $value)
            {
                if ($value['id_tipo_buzon'] == 1)
                    unset($aBuzones[$key]);
            }
        }

        //listado de usuarios

        $listado_usuarios = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)
        ->get('http://sgd_ms_usuarios:3333/api/sgd-usuarios/ver_todos');

        if($listado_usuarios->failed()){
            $mensaje = $listado_usuarios->json()['data']['comentario'];

            $listado_usuarios=['data'=>[
                0=>['id'=>'0','nombres'=>'Sin Datos']
            ]];
            toast($mensaje,'error');
        }else{
            $aUsuarios = $listado_usuarios['data'];

            foreach ($aUsuarios as $key => $value)
            {
                if ($value['id_estado_usuario'] == 2)
                    unset($aUsuarios[$key]);
            }
        }

        return View::make('buzon.index',['listado_buzones'=>$aBuzones, 'listado_usuarios'=>$aUsuarios]);
    }

    public function store(StoreBuzon $request)
    {
        $sesion_key =  AppServiceProvider::session_key_general();

        $aUsuarios = [];

        if (isset($request->usuarios_asignados))
        {
            foreach ($request->usuarios_asignados as $usuario)
                $aUsuarios[] = ['id_usuario' => $usuario];

        }

        $accionBuzon = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(20)
        ->post('http://sgd_ms_buzones:3333/api/sgd-buzones/crear', [
            'nombre_buzon'=>$request->nombre,
            'nombre_corto_buzon'=>$request->nombre_corto,
            'tipo_buzon'=>'2',
            'usuarios_asignados'=> $aUsuarios
        ]);

        return $accionBuzon->json();
    }

    public function show($id)
    {
        $sesion_key =  AppServiceProvider::session_key_general();

        $datosBuzon = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json']) //
        ->timeout(30)
        ->withBody(json_encode([
            'id_buzon' => $id,
        ]), 'json')
        ->get('http://sgd_ms_buzones:3333/api/sgd-buzones/ver');

        //listado de usuarios

        $listado_usuarios = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)
        ->get('http://sgd_ms_usuarios:3333/api/sgd-usuarios/ver_todos');

        if($listado_usuarios->failed()){
            $mensaje = $listado_usuarios->json()['data']['comentario'];

            $listado_usuarios=['data'=>[
                0=>['id'=>'0','nombres'=>'Sin Datos']
            ]];
            toast($mensaje,'error');
        }else{
            $aUsuarios = $listado_usuarios['data'];

            foreach ($aUsuarios as $key => $value)
            {
                if ($value['id_estado_usuario'] == 2)
                    unset($aUsuarios[$key]);
            }
        }

        return $datosBuzon->json();
    }

    public function update(StoreBuzon $request)
    {
        $sesion_key =  AppServiceProvider::session_key_general();

        $aUsuarios = [];

        if (isset($request->usuarios_asignados))
        {
            foreach ($request->usuarios_asignados as $usuario)
                $aUsuarios[] = ['id_usuario' => $usuario];
        }

        $accionBuzon = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(20)
        ->put('http://sgd_ms_buzones:3333/api/sgd-buzones/actualizar', [
            'id_buzon'=>$request->hiddBuzon,
            'nombre_buzon'=>$request->nombre,
            'nombre_corto_buzon'=>$request->nombre_corto,
            'tipo_buzon'=>'2',
            'usuarios_asignados'=> $aUsuarios
        ]);

        return $accionBuzon->json();
    }

    public function delete($id)
    {
        $sesion_key = AppServiceProvider::session_key_general();
        $accionBuzon = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json']) //
        ->timeout(30)
        ->withBody(json_encode([
            'id_buzon' => $id,
        ]), 'json')
        ->delete('http://sgd_ms_buzones:3333/api/sgd-buzones/eliminar');

        return $accionBuzon->json();
    }

    public function carpetas($id){

        $nombre_buzon="Personal";
        return View::make('buzon.carpetas',['nombre_buzon'=>$nombre_buzon]);
    }


}
