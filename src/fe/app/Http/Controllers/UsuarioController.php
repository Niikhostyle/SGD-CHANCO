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

        $lista_usuarios = Http::withToken($sesion_key)
        ->timeout(3)
        ->get('https://run.mocky.io/v3/1ddc16d2-39bf-4303-a945-5f6c6a60ed03');
        //->get('https://run.mocky.io/v3/56edfff2-9303-4917-a27b-4e7cf519f4bb');
        //->json();
        if($lista_usuarios->failed()){
            $mensaje= $lista_usuarios->json()['data']['comentario'];

            $lista_usuarios=['data'=>[
                0=>['id_usuario'=>'0','nombres'=>'Sin Datos','rut'=>'','e-mail'=>'','estado_usuario'=>'']
            ]];
            toast($mensaje,'error');
        }else{
            $lista_usuarios->json();
        }

        $perfiles = Http::withToken($sesion_key)
        ->timeout(3)
        ->get('https://run.mocky.io/v3/a6883c01-9d3f-4792-a519-468bc0bfb74c');
        //->get('https://run.mocky.io/v3/56edfff2-9303-4917-a27b-4e7cf519f4bb');
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
        //$validated = $request->validated();
        //toast('dd','error');

        $sesion_key =  AppServiceProvider::session_key_general();
        $response = Http::withToken($sesion_key)
        ->timeout(3)
        ->post(//'https://run.mocky.io/v3/56edfff2-9303-4917-a27b-4e7cf519f4bb', [
            'https://run.mocky.io/v3/ed2f095f-a4d4-423c-8efe-440ec80893f8', [
            'id_perfil'=>$request->id_perfil,
            'id_estado_usuario'=>$request->id_estado_usuario,
            'run'=>$request->run,
            'nombres'=>$request->nombres,
            'primer_apellido'=>$request->primer_apellido,
            'segundo_apellido'=>$request->segundo_apellido,
            'email'=>$request->email,
            'password'=>$request->password,
            'aplica_fea'=>$request->aplica_fea,
            'genera_pdf'=>$request->genera_pdf
        ]);
        return response()->json($response->json());

    }


    public function show($id){

    }

    public function update($id){
        return $id;
    }
}
