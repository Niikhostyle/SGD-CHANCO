<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBuzon;
use App\Models\Buzon;
use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\DataTables\UsersDataTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

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

        $sesion_key =  AppServiceProvider::session_key_general();
        $perfiles_datos="";
        $estados_usuario="";
        $lista_por_recibir = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->get('https://run.mocky.io/v3/6e00ebbb-7878-4b61-93c4-18e8b4d5e720');

        //https://run.mocky.io/v3/6e00ebbb-7878-4b61-93c4-18e8b4d5e720

        if($lista_por_recibir->failed()){
            $mensaje= $lista_por_recibir->json()['data']['comentario'];

            $lista_por_recibir=['data'=>[
                0=>['id'=>'0','nombres'=>'Sin Datos','primer_apellido'=>'','segundo_apellido'=>'','run'=>'','email'=>'','id_estado_usuario'=>'']
            ]];
            toast($mensaje,'error');
        }else{
            $lista_por_recibir->json();
        }



        $perfiles = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(63)
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

        /*$listado_usuarios = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(33)
        ->get('http://sgd_ms_usuarios:3333/api/sgd-usuarios/listado');*/

        //return $listado_usuarios;
        /*if($perfiles_nuevo->failed()){
            $mensaje= $perfiles_nuevo->json()['data']['comentario'];

            $perfiles_nuevo=['data'=>[
                0=>['id_perfil'=>'0','nombre'=>'Sin Datos']
            ]];
            toast($mensaje,'error');
        }*/

        //$User = $perfiles_nuevo->json();

        //$User  = User::paginate(10);

                //return $perfiles_nuevo;
             //$perfiles_nuevo->json();

      /*$User = collect($perfiles_nuevo->json());
      $current_page = LengthAwarePaginator::resolveCurrentPage();
      $current_page_orders = $User->slice(($current_page - 1) * 10, 10)->all(); // slice($offset, $number_of_item)

     $orders_to_show = new LengthAwarePaginator($current_page_orders, count($User), 10);
     $link=[
         ['url'=>"http://sgd_ms_usuarios:3333/api/sgd-usuarios/listado?page=1",'label'=> "1",'active'=> true],
         ['url'=>"http://sgd_ms_usuarios:3333/api/sgd-usuarios/listado?page=2",'label'=> "2",'active'=> false],
         ['url'=>"http://sgd_ms_usuarios:3333/api/sgd-usuarios/listado?page=3",'label'=> "3",'active'=> false]
    ];*/
    //return $orders_to_show;
     // $orders_to_show = $this->paginate($User);


     $n_docs_por_recibir=0;
        $n_docs_recibidos_pendientes=0;
        $menuBuzon = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json']) //
            ->timeout(30)
            ->withBody(json_encode([
                'id_usuario' => Auth::user()->id,
            ]), 'json')
            ->get('http://sgd_ms_buzones:3333/api/sgd-buzones/menu');
        if(isset($menuBuzon['data'])){
            foreach ($menuBuzon['data'] as $key => $value)
            {
                if($value['id_buzon']==$id){
                    $n_docs_por_recibir=$value['n_docs_por_recibir'];
                    $n_docs_recibidos_pendientes=$value['n_docs_recibidos_pendientes'];
                }
            }
        }

        /* NUEVO-DOCUMENTOS */

        //tipos de documentos
        $listado_tiposdoc = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)
        ->get('http://sgd_ms_tipos_documentos:3333/api/sgd-tipodoc/ver_todos');

        if($listado_tiposdoc->failed()){
            $mensaje = $listado_tiposdoc->json()['data']['comentario'];

            toast($mensaje,'error');
        }
        else
        {
            $datosTipoDoc = $listado_tiposdoc['data'];           

        }

        //parametros
        $listado_parametros = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(13)
        ->get('http://sgd_ms_parametros:3333/api/sgd-parametros/traer');
        
        if($listado_parametros->failed()){
            toast("Error al mostrar datos",'error');
        }else{
            $datosNivelAcceso = $listado_parametros['data']['nivel_acceso'];            
        }

        $datosFlujoAccion = $listado_parametros['data']['tipo_flujo_accion'];
        $datosAccion = $listado_parametros['data']['accion'];

        //acciones
        $aAcciones = [];
        foreach ($datosAccion as $dato)
            $aAcciones[$dato['id_accion']] = $dato['nombre'];

        //acciones-flujo
        
        foreach ($datosFlujoAccion as $dato)
        {
            if ($dato['id_tipo_flujo'] == 2) //Controlado
            {
                $aFlujoAccionT2[] = array($dato['id_accion'], $aAcciones[$dato['id_accion']]);    
            }

            if ($dato['id_tipo_flujo'] == 3) //Mixto
            {
                $aFlujoAccionT3[] = array($dato['id_accion'], $aAcciones[$dato['id_accion']]);      
            }  
        }


        $datosBuzon = $this->show($id); //muestra metodo show

        //listado de buzones

        $listado_buzones = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)
        ->withBody(json_encode([
            'texto_busqueda' => '',
        ]), 'json')
        ->get('http://sgd_ms_buzones:3333/api/sgd-buzones/listar_todos');

        if($listado_buzones->failed()){
            $listado_buzones->json()['data']['comentario'];

            $listado_buzones=['data'=>[
                0=>['id'=>'0','nombre'=>'Sin Datos']
            ]];
            toast($mensaje,'error');
        }else{

            foreach ($listado_buzones['data'] as $dato)
            {
                $aBuzones[$dato['id_buzon']] = $dato['nombre'];    
                $aAllBuzones[] = array("value" => $dato['id_buzon'], "text" => $dato['nombre']);// "tipo" => $dato['id_tipo_buzon'] 
            }  
        }

        /* NUEVO-DOCUMENTOS */
        
        return View::make('buzon.carpetas',[
            //'nombre_buzon' => $nombre_buzon,
            'lista_por_recibir' => $lista_por_recibir,
            'perfiles' => $perfiles_datos,
            'estados_usuario' => $estados_usuario,
            'n_docs_por_recibir' => $n_docs_por_recibir,
            'n_docs_recibidos_pendientes' => $n_docs_recibidos_pendientes,
            'nivel_acceso' => $datosNivelAcceso,
            'nombre_buzon' => $datosBuzon['data']['nombre'],
            'listado_tiposdoc'=>$datosTipoDoc,
            'id_buzon' => $id,
            'acciones_tipoflujo2'=>$aFlujoAccionT2,
            'acciones_tipoflujo3'=>$aFlujoAccionT3,
            'listadoBuzones'=>$aBuzones,
            'allBuzones'=>$aAllBuzones 
        ]);
    }

    public function store_documento(Request $request)
    {
        $sesion_key =  AppServiceProvider::session_key_general();

        $accionDocumento = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(20)
        ->post('http://sgd_ms_documentos:3333/api/sgd-documentos/crear', [
            'id_tipo_documento'=>$request->tipo_documento,
            'id_nivel_acceso'=>$request->nivel_acceso,
            'efectos_terceros'=>$request->efectos_terceros,
            'json_respuesta_a'=>"[]",
            'materia'=>$request->materia,
            'anterior'=>$request->anterior,
            'descripcion'=>$request->descripcion,
            'encabezado'=>$request->encabezado,
            'cuerpo'=>$request->cuerpo,
            'id_buzon'=>$request->buzon,
            'contestar_hasta'=>$request->contestar_hasta,          
            'id_usuario'=>Auth::user()->id
        ]);

        return $accionDocumento->json();

    } 

    public function update_documento(Request $request)
    {
        $sesion_key =  AppServiceProvider::session_key_general();

        $accionDocumento = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(20)
        ->put('http://sgd_ms_documentos:3333/api/sgd-documentos/actualizar', [
            'id_tipo_documento'=>$request->tipo_documento,
            'id_nivel_acceso'=>$request->nivel_acceso,
            'id_documento'=>$request->hiddIdDocumento,
            'id_documento_buzon'=>$request->hiddIdDocumentoBuzon,
            'efectos_terceros'=>$request->efectos_terceros,
            'json_respuesta_a'=>"[]",
            'materia'=>$request->materia,
            'anterior'=>$request->anterior,
            'descripcion'=>$request->descripcion,
            'encabezado'=>$request->encabezado,
            'cuerpo'=>$request->cuerpo,
            'id_buzon'=>$request->buzon,
            'contestar_hasta'=>$request->contestar_hasta,          
            'id_usuario'=>Auth::user()->id,
            'destinatarioPrincipal'=>$request->destinatarioPrincipal,
            'destinatarioOtros'=>$request->destinatarioOtros,
            'comentarioPrincipal'=>$request->comentarioPrincipal,
            'comentarioOtros'=>$request->comentarioOtros
        ]);

        return $accionDocumento->json();
        
    } 

}
