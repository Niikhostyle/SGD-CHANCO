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

//use Illuminate\Support\Facades\Hash;
//use RealRashid\SweetAlert\Facades\Alert;
//use RealRashid\SweetAlert\SweetAlertServiceProvider;

class UsuarioController extends Controller
{
    Public function index(){

        $sesion_key =  AppServiceProvider::session_key_general();

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

        $perfiles = Http::withToken($sesion_key)
        ->timeout(3)
        ->get('https://run.mocky.io/v3/a6883c01-9d3f-4792-a519-468bc0bfb74c');
        if($perfiles->failed()){
            $mensaje= $perfiles->json()['data']['comentario'];

            $perfiles=['data'=>[
                0=>['id_perfil'=>'0','nombre'=>'Sin Datos']
            ]];
            toast($mensaje,'error');
        }else{
            $perfiles->json();
        }

        return View::make('usuario.index',['lista_usuarios'=>$lista_usuarios,'perfiles'=>$perfiles]);
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

        if($response->failed()){
            $mensaje= $response->json()['data']['comentario'];
            toast($mensaje,'error');
        }else{
            toast('Guardado.','susses');
        }
        return $response_json;

    }


    public function show($id){

    }

    public function update($id){
        return $id;
    }
}
