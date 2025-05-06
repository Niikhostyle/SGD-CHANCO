<?php

namespace App\Http\Controllers;

//use App\Models\Session;
use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUsuario;
use App\Http\Requests\UpdateUsuario;
use App\Models\BuzonUsuario;
use App\Models\Users;
use Illuminate\Support\Facades\DB;
use Exception;

class UsuarioController extends Controller
{
    Public function index(){

        $sesion_key =  AppServiceProvider::session_key_general();
        $perfiles_datos="";
        $estados_usuario="";
        $lista_usuarios = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->get(env('API_SGD_USUARIOS','http://sgd_ms_usuarios:3333').'/api/sgd-usuarios/ver_todos');


        if($lista_usuarios->failed()){
            $mensaje= $lista_usuarios->json()['data']['comentario'];

            $lista_usuarios=['data'=>[
                0=>['id'=>'0','nombres'=>'Sin Datos','primer_apellido'=>'','segundo_apellido'=>'','run'=>'','email'=>'','id_estado_usuario'=>'']
            ]];
            toast($mensaje,'error');
        }else{
            $lista_usuarios->json();
        }

        $perfiles_datos = \App\Models\Perfil::all('id_perfil', 'nombre');
        $estados_usuario = \App\Models\EstadoUsuario::all('id_estado_usuario','nombre');



        //lista de buzones
        $listado_buzones = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(30)
            ->withBody(json_encode([
                'texto_busqueda' => '',
            ]), 'json')
            ->get(env('API_SGD_BUZONES','http://sgd_ms_buzones:3333').'/api/sgd-buzones/listar_todos');

        if ($listado_buzones->failed()) {
            $mensaje = $listado_buzones->json()['data']['comentario'];

            $listado_buzones = ['data' => [
                0 => ['id' => '0', 'nombre' => 'Sin Datos', 'nombre_corto' => '', 'total_us_asignados' => '', 'total_us_asignados' => '']
            ]];
            toast($mensaje, 'error');
        } else {

            $aBuzones = $listado_buzones['data'];

            foreach ($aBuzones as $key => $value) {
                if ($value['id_tipo_buzon'] == 1)
                    unset($aBuzones[$key]);
            }
        }
        //dd(json_decode($lista_usuarios->getBody()));
        return View::make('usuario.index',['lista_usuarios'=>$lista_usuarios,'perfiles'=>$perfiles_datos,'buzones'=>$aBuzones,'estados_usuario'=>$estados_usuario]);
    }

    public function store(StoreUsuario $request)
    {
        if($request->hasFile('form_imagen_firma'))
            $uploadImg = $this->uploadFile($request);
        else
            $uploadImg = $request->hiddFirma;       
        $sesion_key =  AppServiceProvider::session_key_general();
        $response = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->post(env('API_SGD_USUARIOS','http://sgd_ms_usuarios:3333').'/api/sgd-usuarios/crear', [
            'run'=>$request->run,
            'nombres'=>$request->nombres,
            'primer_apellido'=>$request->primer_apellido,
            'segundo_apellido'=>$request->segundo_apellido,
            'email'=>$request->email,
            'password'=>$request->password,
            'confirmar_password'=>$request->re_password,
            'aplica_fea'=>$request->aplica_fea,
            'numero_contacto'=>$request->form_contacto,
            'cargo'=>$request->form_cargo,
            'genera_pdf'=>$request->aplica_genera_pdf,
            'id_estado_usuario'=>$request->id_estado_usuario,
            'id_perfil'=>$request->id_perfil,
            'img_firma'=>$uploadImg
        ]);
        
        $response_json=$response->json(); 

        if ($response_json['status'] == '201')
        {
            $response_object = $response->object();
            $aUsuarios = [];
            $aUsuarios[] = ['id_usuario' => $response_object->data->id];
            
            $accionBuzon = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
            ->timeout(20)
            ->post(env('API_SGD_BUZONES','http://sgd_ms_buzones:3333').'/api/sgd-buzones/crear', [
                'nombre_buzon'=>'Personal - '.$request->nombres.' '.$request->primer_apellido,
                'nombre_corto_buzon'=>'PRSNAL',
                'tipo_buzon'=>'1',
                'usuarios_asignados'=> $aUsuarios,
                'titular'=> null,      
                'cargo_firma' => null
            ]);
        }

        return $response_json;
    }


    public function show($id){

        $sesion_key =  AppServiceProvider::session_key_general();
        $usuario = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->withBody(json_encode([
            'id_usuario' => $id,
        ]), 'json')
        ->get(env('API_SGD_USUARIOS','http://sgd_ms_usuarios:3333').'/api/sgd-usuarios/ver/')->throw();

        return $usuario->json();

    }

    public function update(UpdateUsuario $request){

        if($request->hasFile('form_imagen_firma'))
            $uploadImg = $this->uploadFile($request);
        else
            $uploadImg = $request->hiddFirma;

        if($request->hasFile('form_foto'))
            $uploadFoto = $this->uploadFotoPerfil($request);
        else
            $uploadFoto = $request->hiddFoto;
            
        $sesion_key =  AppServiceProvider::session_key_general();
        $response = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->put(env('API_SGD_USUARIOS','http://sgd_ms_usuarios:3333').'/api/sgd-usuarios/actualizar', [
            'id'=>$request->form_id_usuario,
            'run'=>$request->run,
            'nombres'=>$request->nombres,
            'primer_apellido'=>$request->primer_apellido,
            'segundo_apellido'=>$request->segundo_apellido,
            'email'=>$request->email,
            'password'=>$request->password,
            'confirmar_password'=>$request->re_password,
            'aplica_fea'=>$request->aplica_fea,
            'genera_pdf'=>$request->aplica_genera_pdf,
            'id_estado_usuario'=>$request->id_estado_usuario,
            'id_perfil'=>$request->id_perfil,
            'imagen_firma'=>$uploadImg,
            'numero_contacto'=>$request->form_contacto,
            'cargo'=>$request->form_cargo,
            'img_perfil'=>$uploadFoto
        ]);

        //dd($response);
       
        foreach($request->buzonusuario as $buzon){
            $registro = BuzonUsuario::where('id_usuario', '=', $request->form_id_usuario)->where('id_buzon', '=', $buzon)->first();
            if(!$registro){
                BuzonUsuario::create([
                    'id_usuario' => $request->form_id_usuario,
                    'id_buzon' => $buzon,
                    'id_tipo_firma' => 2,
                    'restringir_sr' =>null,
                    'id_usuario_sr' => null
                ]);
            }

        }
        //elimina buzones no asociados al usuario (menos el personal)
        $buzones = $request->buzonusuario;
        $buzonpersonal=BuzonUsuario::where("id_usuario",$request->form_id_usuario)
            ->with(['buzon',function($q){
                $q->where('id_tipo_buzon', 1);
            }])->first();
        if($buzonpersonal)
            $buzones[] = $buzonpersonal->id_buzon;

        BuzonUsuario::where('id_usuario','=', $request->form_id_usuario)->whereNotIn('id_buzon',$buzones)->delete();


        $response_json = response()->json($response->json());

        
        return $response_json;
    }

    public function estado($id)
    {
        $sesion_key = AppServiceProvider::session_key_general();
        $response = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)
        ->put(env('API_SGD_USUARIOS','http://sgd_ms_usuarios:3333').'/api/sgd-usuarios/estado',['id_usuario' => $id]);

        $response_json=response()->json($response->json());
        return $response_json;
    }

    public function uploadFile(Request $request) {

        if(!$request->hasFile('form_imagen_firma')) {
            //throw new Exception("upload_file_not_found.");
            return $this->respondFail("upload_file_not_found.");
        }
        $file = $request->file('form_imagen_firma');
        if(!$file->isValid()) {
            return $this->respondFail("invalid_file_upload.");
        }
        
        $extension = $file->getClientOriginalExtension();
        $nNombreArchivoCargar = $request->run . '.' . $extension;
        
        $uploadSuccess = $file->move(storage_path('app/public/files/imagen_firma/'), $nNombreArchivoCargar);

        if (!$uploadSuccess)
            throw new Exception("No se pudo cargar la imagen.");

            //return $this->respondSuccess($nNombreArchivoCargar, 201);
        
        return $nNombreArchivoCargar;
    }

    public function uploadFotoPerfil(Request $request) {

        if(!$request->hasFile('form_foto')) {
            //throw new Exception("upload_file_not_found.");
            return $this->respondFail("upload_file_not_found.");
        }
        $file = $request->file('form_foto');
        if(!$file->isValid()) {
            return $this->respondFail("invalid_file_upload.");
        }
        
        $extension = $file->getClientOriginalExtension();
        $nNombreArchivoCargar = $request->run . '.' . $extension;
        
        $uploadSuccess = $file->move(storage_path('app/public/files/imagen_perfil/'), $nNombreArchivoCargar);

        if (!$uploadSuccess)
            throw new Exception("No se pudo cargar la imagen.");
        
        return $nNombreArchivoCargar;
    }

    function perfil(){

        $datos = User::join('estado_usuario as eu','eu.id_estado_usuario','users.id_estado_usuario')
                ->join('perfil as p','p.id_perfil','users.id_perfil')
                ->where('id',Auth::user()->id)
                ->select('users.id','run','nombres','primer_apellido','segundo_apellido','email',
                DB::raw("eu.id_estado_usuario as id_estado_usuario"),DB::raw("p.id_perfil as id_perfil"),'cargo','numero_contacto','img_perfil'
                ,DB::raw("eu.nombre as estado"),DB::raw("p.nombre as perfil"),'aplica_fea', 'genera_pdf','img_firma')
                ->get();


        return view('usuario.perfil',['datos'=>$datos]);
    }

    public function buscar(Request $request){
        $sTexto = $request->texto;
        if(!isset($sTexto)){
            $sTexto = "123xyza";
        }
        $datos = Users::whereRaw("lower(users.nombres) like lower('%".$sTexto."%') or lower(users.primer_apellido) like lower('%".$sTexto."%') or lower(users.segundo_apellido) like lower('%".$sTexto."%') or lower(users.cargo) like lower('%".$sTexto."%') or lower(users.email) like lower('%".$sTexto."%') ")
                ->get();
        return $datos;
    }
}
