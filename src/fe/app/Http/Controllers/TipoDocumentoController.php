<?php

namespace App\Http\Controllers;

use App\Models\TipoDocumento;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\StoreTipoDoc;

use Illuminate\Http\Request;

class TipoDocumentoController extends Controller
{
    public function index(){

        $sesion_key =  AppServiceProvider::session_key_general();

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
        
        $listado_parametros = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(13)
        ->get('http://sgd_ms_parametros:3333/api/sgd-parametros/traer');
        
        if($listado_parametros->failed()){
            $mensaje= $listado_parametros->json()['data']['comentario'];

            toast($mensaje,'error');
        }else{            
            
            $datosFlujo = $listado_parametros['data']['tipo_flujo'];
            $datosOrigen = $listado_parametros['data']['tipo_origen'];
            $datosAvance = $listado_parametros['data']['tipo_avance'];
            $datosFolio = $listado_parametros['data']['tipo_folio'];
            $datosAsignacionFolio = $listado_parametros['data']['tipo_asignacion_folio'];
            
            $datosFlujoAccion = $listado_parametros['data']['tipo_flujo_accion'];
            $datosAccion = $listado_parametros['data']['accion'];
        }

        //acciones
        $aAcciones = [];
        foreach ($datosAccion as $dato)
            $aAcciones[$dato['id_accion']] = $dato['nombre'];
        
        //$aListadoAcciones[] = array("title" => $dato['nombre']);

        $aFlujoAccionT2[] = array("title" => "Orden", "id" => "");
        $aFlujoAccionT2[] = array("title" => "Buzón", "id" => "");

        $aFlujoAccionT3[] = array("title" => "Orden", "id" => "");
        $aFlujoAccionT3[] = array("title" => "Buzón", "id" => "");

        foreach ($datosFlujoAccion as $dato)
        {
            if ($dato['id_tipo_flujo'] == 2) //Controlado
            {
                $aFlujoAccionT2[] = array("title" => $aAcciones[$dato['id_accion']], "id" => $dato['id_accion']);    
            }

            if ($dato['id_tipo_flujo'] == 3) //Mixto
            {
                $aFlujoAccionT3[] = array("title" => $aAcciones[$dato['id_accion']], "id" => $dato['id_accion']);    
            }  
        }

        $aFlujoAccionT2[] = array("title" => "Acciones", "id" => "");
        $aFlujoAccionT3[] = array("title" => "Acciones", "id" => "");

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
        
        $aOrigen = [];
        foreach ($datosOrigen as $origen){
            $aOrigen[$origen['id_tipo_origen']] = $origen['nombre'];
        }

        $aFlujo = [];
        foreach ($datosFlujo as $flujo){
            $aFlujo[$flujo['id_tipo_flujo']] = $flujo['nombre'];
        }

        return View::make('tipo_documento.index', [
            'listado_tiposdoc'=>$datosTipoDoc,
            'listado_buzones'=>$aBuzones,
            'datosFlujo'=>$datosFlujo,
            'datosOrigen'=>$datosOrigen,
            'aOrigen'=>$aOrigen,
            'aFlujo'=>$aFlujo,
            'datosAvance'=>$datosAvance,
            'datosFolio'=>$datosFolio,
            'datosAsignacionFolio'=>$datosAsignacionFolio,
            'acciones_tipoflujo2'=>$aFlujoAccionT2,
            'acciones_tipoflujo3'=>$aFlujoAccionT3 
        ]);
    }

    public function store(StoreTipoDoc $request)
    {
        $sesion_key =  AppServiceProvider::session_key_general();

        $accionTipoDoc = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(20)
        ->post('http://sgd_ms_tipos_documentos:3333/api/sgd-tipodoc/crear', [
            'nombre'=>$request->nombre,
            'nombre_corto'=>$request->nombre_corto,
            'nombre_corto_firma'=>$request->nombre_corto_firma,
            'descripcion'=>$request->descripcion,
            'id_tipo_origen'=>$request->tipo_origen,
            'id_tipo_flujo'=>$request->tipo_flujo,
            'id_tipo_folio'=>$request->tipo_folio,
            'id_tipo_avance'=>$request->tipo_avance,
            'id_tipo_asignacion_folio'=>$request->tipo_asignacion_folio,
            'requiere_fe'=>$request->requiere_fe,
            'numero_firmas'=>$request->numero_firmas,
            'plantilla_distribucion'=>$request->plantilla_distribucion,
            'plantilla_encabezado'=>$request->plantilla_encabezado,
            'plantilla_cuerpo'=>$request->plantilla_cuerpo,
            'buzones_flujo'=>$request->bzs_flujo       
            
        ]);

        return $accionTipoDoc->json();
    }

    public function show($id)
    {
        $sesion_key =  AppServiceProvider::session_key_general();

        $datosTipoDoc = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json']) //
        ->timeout(30)
        ->withBody(json_encode([
            'id_tipo_documento' => $id,
        ]), 'json')
        ->get('http://sgd_ms_tipos_documentos:3333/api/sgd-tipodoc/ver');

        return $datosTipoDoc->json();
    }

    public function update(StoreTipoDoc $request)
    {
        $sesion_key =  AppServiceProvider::session_key_general();

        $accionTipoDoc = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(20)
        ->put('http://sgd_ms_tipos_documentos:3333/api/sgd-tipodoc/actualizar', [
            'id_tipo_documento'=>$request->hiddTipoDocumento,
            'nombre'=>$request->nombre,
            'nombre_corto'=>$request->nombre_corto,
            'nombre_corto_firma'=>$request->nombre_corto_firma,
            'descripcion'=>$request->descripcion,
            'id_tipo_origen'=>$request->tipo_origen,
            'id_tipo_flujo'=>$request->tipo_flujo,
            'id_tipo_folio'=>$request->tipo_folio,
            'id_tipo_avance'=>$request->tipo_avance,
            'id_tipo_asignacion_folio'=>$request->tipo_asignacion_folio,
            'requiere_fe'=>$request->requiere_fe,
            'numero_firmas'=>$request->numero_firmas,
            'plantilla_distribucion'=>$request->plantilla_distribucion,
            'plantilla_encabezado'=>$request->plantilla_encabezado,
            'plantilla_cuerpo'=>$request->plantilla_cuerpo,
            'buzones_flujo'=>$request->bzs_flujo       
            
        ]);

        return $accionTipoDoc->json();
    }

    public function delete($id)
    {
        $sesion_key = AppServiceProvider::session_key_general();
        $accionTipoDoc = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json']) //
        ->timeout(30)
        ->withBody(json_encode([
            'id_tipo_documento' => $id,
        ]), 'json')
        ->delete('http://sgd_ms_tipos_documentos:3333/api/sgd-tipodoc/eliminar');

        return $accionTipoDoc->json();
    }

}
