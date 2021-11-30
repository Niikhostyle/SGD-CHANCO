<?php

namespace App\Http\Controllers;

//use App\Models\Session;
use App\Models\User;
use App\Providers\AppServiceProvider;
//use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUsuario;
use App\Http\Requests\UpdateUsuario;


class UsuarioController extends Controller
{
    Public function index(){

        $sesion_key =  AppServiceProvider::session_key_general();
        $perfiles_datos="";
        $estados_usuario="";
        $lista_usuarios = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->get('http://sgd_ms_usuarios:3333/api/sgd-usuarios/ver_todos');


        if($lista_usuarios->failed()){
            $mensaje= $lista_usuarios->json()['data']['comentario'];

            $lista_usuarios=['data'=>[
                0=>['id'=>'0','nombres'=>'Sin Datos','primer_apellido'=>'','segundo_apellido'=>'','run'=>'','email'=>'','id_estado_usuario'=>'']
            ]];
            toast($mensaje,'error');
        }else{
            $lista_usuarios->json();
        }

        $perfiles = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(13)
        ->get('http://sgd_ms_parametros:3333/api/sgd-parametros/traer');
        if($perfiles->failed()){
            $mensaje= $perfiles->json()['data']['comentario'];

            $perfiles=['data'=>[
                0=>['id_perfil'=>'0','nombre'=>'Sin Datos']
            ]];
            toast($mensaje,'error');
        }else{
            //$perfiles->json();
            $perfiles_datos = $perfiles->json()['data']['perfil'];
            $estados_usuario = $perfiles->json()['data']['estado_usuario'];

        }


        return View::make('usuario.index',['lista_usuarios'=>$lista_usuarios,'perfiles'=>$perfiles_datos,'estados_usuario'=>$estados_usuario]);
    }

    public function store(StoreUsuario $request)
    {
        $sesion_key =  AppServiceProvider::session_key_general();
        $response = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->post('http://sgd_ms_usuarios:3333/api/sgd-usuarios/crear', [
            'run'=>$request->run,
            'nombres'=>$request->nombres,
            'primer_apellido'=>$request->primer_apellido,
            'segundo_apellido'=>$request->segundo_apellido,
            'email'=>$request->email,
            'password'=>$request->password,
            'confirmar_password'=>$request->confirmar_password,
            'aplica_fea'=>$request->aplica_fea,
            'genera_pdf'=>$request->genera_pdf,
            'id_estado_usuario'=>$request->id_estado_usuario,
            'id_perfil'=>$request->id_perfil
        ]);
        $response_json=response()->json($response->json());
        $response_object=$response->object();
        $aUsuarios = [];
        $aUsuarios[] = ['id_usuario' => $response_object->data->id];
        $accionBuzon = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(20)
        ->post('http://sgd_ms_buzones:3333/api/sgd-buzones/crear', [
            'nombre_buzon'=>'Personal - '.$request->nombres.' '.$request->primer_apellido,
            'nombre_corto_buzon'=>'PRSNAL',
            'tipo_buzon'=>'1',
            'usuarios_asignados'=> $aUsuarios
        ]);
        return $response_json;
    }


    public function show($id){

        $sesion_key =  AppServiceProvider::session_key_general();
        $usuario = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->withBody(json_encode([
            'id_usuario' => $id,
        ]), 'json')
        ->get('http://sgd_ms_usuarios:3333/api/sgd-usuarios/ver/');

        return $usuario->json();

    }

    public function update(Request $request){

        $sesion_key =  AppServiceProvider::session_key_general();
        $response = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->put('http://sgd_ms_usuarios:3333/api/sgd-usuarios/actualizar', [
            'id'=>$request->form_id_usuario,
            'run'=>$request->run,
            'nombres'=>$request->nombres,
            'primer_apellido'=>$request->primer_apellido,
            'segundo_apellido'=>$request->segundo_apellido,
            'email'=>$request->email,
            'password'=>$request->password,
            'confirmar_password'=>$request->confirmar_password,
            'aplica_fea'=>$request->aplica_fea,
            'genera_pdf'=>$request->genera_pdf,
            'id_estado_usuario'=>$request->id_estado_usuario,
            'id_perfil'=>$request->id_perfil
        ]);
        $response_json=response()->json($response->json());


        return $response_json;
    }

    public function estado($id)
    {
        $sesion_key = AppServiceProvider::session_key_general();
        $response = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)
        ->put('http://sgd_ms_usuarios:3333/api/sgd-usuarios/estado',['id_usuario' => $id]);

        $response_json=response()->json($response->json());
        return $response_json;
    }
}
