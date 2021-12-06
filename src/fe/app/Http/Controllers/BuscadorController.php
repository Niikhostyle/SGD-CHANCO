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
        //return view(‘buscador/buscador’, compact(‘buscador’));
        //return "hola";
        $sesion_key =  AppServiceProvider::session_key_general();
        $lista_documento = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->withBody(json_encode([
            'id_usuario' => Auth::user()->id,
        ]), 'json')
        ->get('http://sgd_ms_documentos:3333/api/sgd-documentos/listarDocumentos');
        //->get('http://sgd_ms_buscador:3333/api/sgd-buscador/listarDocumentos');

        //return $lista_documento;

        if($lista_documento->failed()){
            $mensaje= $lista_documento->json()['data']['comentario'];

            $lista_documento=['data'=>[
                0=>['id_documento'=>'','rel_documento_buzon'=>'','id_tipo_documento'=>'','folio'=>'','rel_documento_buzon'=>'','rel_documento_buzon'=>'','materia'=>'']
            ]];
            toast($mensaje,'error');
        }else{
            $lista_documento->json();
        }

        //listar documento bitacora
        $sesion_key =  AppServiceProvider::session_key_general();
        $lista_bitacora = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->withBody(json_encode([
            'id_usuario' => Auth::user()->id,
        ]), 'json')
        ->get('http://sgd_ms_documentos:3333/api/sgd-documentos/listarDocumentosBitacora');
        //->get('http://sgd_ms_bitacora:3333/api/sgd-bitacora/listarDocumentosBitacora');

        //return $lista_documento;

        if($lista_bitacora->failed()){
            $mensaje= $lista_bitacora->json()['data']['comentario'];

            $lista_bitacora=['data'=>[
                0=>['accion'=>'','fecha_documento'=>'','buzon_origen'=>'','nombre_accion'=>'','mensaje_respuesta'=>'', 'tipo_destino'=>'']
            ]];
            toast($mensaje,'error');
        }else{
            $lista_bitacora->json();
        }

        return View::make('buscador.index',['lista_documento'=>$lista_documento, 'lista_bitacora'=>$lista_bitacora]);
        
    }

    public function listarBitacora(){

         //return view(‘buscador/buscador’, compact(‘buscador’));
        //return "hola";
        $sesion_key =  AppServiceProvider::session_key_general();
        $lista_bitacora = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->withBody(json_encode([
            'id_usuario' => Auth::user()->id,
        ]), 'json')
        ->get('http://sgd_ms_documentos:3333/api/sgd-documentos/listarDocumentosBitacora');
        //->get('http://sgd_ms_bitacora:3333/api/sgd-bitacora/listarDocumentosBitacora');

        //return $lista_documento;

        if($lista_bitacora->failed()){
            $mensaje= $lista_bitacora->json()['data']['comentario'];

            $lista_bitacora=['data'=>[
                0=>['accion'=>'','fecha_documento'=>'','buzon_origen'=>'','nombre_accion'=>'','mensaje_respuesta'=>'']
            ]];
            toast($mensaje,'error');
        }else{
            $lista_bitacora->json();
        }

        return View::make('buscador.index',['lista_bitacora'=>$lista_bitacora]);
    }

    public function listar(Request $request)
    {
        //return "hola";
        $datos =  DB::table('documento_buzon')
                    ->join('documento', 'documento_buzon.id_documento', '=', 'documento.id_documento')
                    ->join('estado_documento', 'documento_buzon.id_estado_documento', '=', 'estado_documento.id_estado_documento')
                    ->join('tipo_documento', 'documento.id_tipo_documento', '=', 'tipo_documento.id_tipo_documento')
                    ->join('tipo_origen', 'tipo_documento.id_tipo_origen', '=', 'tipo_origen.id_tipo_origen')
                    ->join('buzon', 'documento_buzon.id_buzon', '=', 'buzon.id_buzon')
                    ->join('buzon_usuario', 'buzon.id_buzon', '=', 'buzon_usuario.id_buzon')
                    ->select(
                        'buzon.nombre as nombre_buzon',
                        'estado_documento.nombre_corto as estado_documento',
                        'documento.id_documento as id_documento',
                        'documento.fecha as fecha_documento',
                        'tipo_documento.nombre as tipo_documento',
                        'tipo_origen.nombre as origen',
                        'documento.materia as materia',
                        'documento_buzon.favorito as estado_favorito',
                        'documento_buzon.id_documento_buzon as id_documento_buzon',
                        'documento.folio as folio',
                        )
                    ->where('buzon_usuario.id_usuario','=', Auth::user()->id);
                    

        return datatables( $datos )->toJson();


    }     

   
}

