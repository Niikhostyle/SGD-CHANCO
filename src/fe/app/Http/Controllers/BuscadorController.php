<?php

namespace App\Http\Controllers;

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\DataTables;

class BuscadorController extends Controller
{
    public function index(){

        $sesion_key =  AppServiceProvider::session_key_general();
        $lista_documento = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->withBody(json_encode([
            'id_usuario' => Auth::user()->id,
        ]), 'json')
        ->get('http://sgd_ms_documentos:3333/api/sgd-documentos/listarDocumentos');
        //->get('http://sgd_ms_buscador:3333/api/sgd-buscador/listarDocumentos');

        if($lista_documento->failed()){
            $mensaje= $lista_documento->json()['data']['comentario'];

            $lista_documento=['data'=>[
                0=>['id_documento'=>'','rel_documento_buzon'=>'','id_tipo_documento'=>'','folio'=>'','rel_documento_buzon'=>'','rel_documento_buzon'=>'','materia'=>'']
            ]];
            toast($mensaje,'error');
        }else{
            $lista_documento->json();
        }

        /* LISTAR DOCUMENTO BITACORA */
        

        /* LISTADO TIPO DE DOCUMENTO */

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

        /* LISTADO BUZONES */

        $listado_buzones = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)
        ->withBody(json_encode([
            'texto_busqueda' => '',
        ]), 'json')
        ->get('http://sgd_ms_buzones:3333/api/sgd-buzones/listar_todos');
        //return $listado_buzones;
        if($listado_buzones->failed()){
            $listado_buzones->json()['data']['comentario'];

            $listado_buzones=['data'=>[
                0=>['id'=>'0','nombre'=>'Sin Datos']
            ]];
            toast($mensaje,'error');
        }else{

            $datosBuzones = $listado_buzones['data'];
            foreach ($listado_buzones['data'] as $dato)
            {
                $aBuzones[$dato['id_buzon']] = $dato['nombre'];                  
            }
        }
        //return $aBuzones;
        //parametros
        $listado_parametros = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(13)
        ->get('http://sgd_ms_parametros:3333/api/sgd-parametros/traer');

        if($listado_parametros->failed()){
            toast("Error al mostrar datos",'error');
        }
        
        $datosNivelAcceso = $listado_parametros['data']['nivel_acceso'];
        $datosAccion = $listado_parametros['data']['accion'];

        return View::make('buscador.index',[
            'lista_documento'=>$lista_documento,
            
            'listado_tiposdoc'=>$datosTipoDoc,
            'listBuzones'=>$datosBuzones,
            'listadoBuzones'=>$aBuzones,
            'listadoAcciones' => $datosAccion,
            'nivel_acceso' => $datosNivelAcceso
        ]);
        
    }

    public function show($id)
    {
        
        //return "hola";
        $sesion_key =  AppServiceProvider::session_key_general();
        $lista_bitacora = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->withBody(json_encode([
            'id_documento' => $id,
        ]), 'json')
        ->get('http://sgd_ms_documentos:3333/api/sgd-documentos/listarDocumentosBitacora');
        //->get('http://sgd_ms_bitacora:3333/api/sgd-bitacora/listarDocumentosBitacora');
        return $lista_bitacora;
        

        if($lista_bitacora->failed()){
            $mensaje= $lista_bitacora->json()['data']['comentario'];

            $lista_bitacora=['data'=>[
                0=>['accion'=>'','fecha_documento'=>'','buzon_origen'=>'','nombre_accion'=>'','mensaje_respuesta'=>'', 'tipo_destino'=>'', 'materia'=>'',  'identificador'=>'']
            ]];
            toast($mensaje,'error');
        }else{
            return $lista_bitacora->json();
        }
        
        //return View::make('buscador.index', [
        //    'lista_bitacora'=>$lista_bitacora,

       // ]);
         
    }  

    public function listar(Request $request)
    {
        $datos =  DB::select("select 
        distinct d.id_documento as id_documento
        , d.identificador
        , d.id_nivel_acceso
        , d.fecha as fecha_documento
        , d.folio
        , d.materia 
        , d.json_tipo_documento 
        , d.id_tipo_documento 
        , td.nombre as tipo_documento
        , (select b3.nombre from documento_buzon db2 join buzon b3 on b3.id_buzon = db2.id_buzon where db2.id_documento = db.id_documento and db2.id_documento_buzon_padre is null) as buzon_origen
        , (select b2.nombre from documento_buzon db3 join buzon b2 on b2.id_buzon = db3.id_buzon where db3.id_documento = db.id_documento order by db3.id_documento_buzon desc limit 1) as buzon_actual
    from 
        documento_buzon db 
        join documento d on d.id_documento = db.id_documento 
        join buzon b on b.id_buzon = db.id_buzon
        join buzon_usuario bu on bu.id_buzon = b.id_buzon
        join tipo_documento td on td.id_tipo_documento = d.id_tipo_documento  
    where 	
        
          (d.id_nivel_acceso in (1,3)) 
         or (bu.id_usuario = ".Auth::user()->id." and d.id_nivel_acceso = 2)
    order by d.id_documento desc");
                    
        return datatables( $datos )->toJson();


    }       


}