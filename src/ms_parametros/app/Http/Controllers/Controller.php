<?php

namespace App\Http\Controllers;

use App\Models\Accion;
use App\Models\Carpeta;
use App\Models\EstadoDocumento;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Perfil;
use App\Models\EstadoUsuario;
use App\Models\NivelAcceso;
use App\Models\TipoAccion;
use App\Models\TipoArchivo;
use App\Models\TipoAsignacionFolio;
use App\Models\TipoAvance;
use App\Models\TipoBuzon;
use App\Models\TipoDestino;
use App\Models\TipoFlujo;
use App\Models\TipoFlujoAccion;
use App\Models\TipoFolio;
use App\Models\TipoOrigen;

class Controller extends BaseController
{
    //

    protected function respondSuccess($message, $status) //200 - 201
    {
        return response()->json([
            'status' => $status, 
            'data' => $message
        ], $status);
    }

    protected function respondError($message, $status) //406
    {
        return response()->json([
            'status' => $status, 
            'data' => [
                'comentario' => $message
        ]], $status);
    }

    public function traer(Request $request)
    {
        if($request->isJson())
        {
            try 
            {
                $aJsonSalida = array();

                $datosPerfiles = Perfil::all('id_perfil', 'nombre');
                $aJsonSalida["perfil"] = $datosPerfiles;

                $datosEstadoUsuario = EstadoUsuario::all('id_estado_usuario','nombre');
                $aJsonSalida["estado_usuario"] = $datosEstadoUsuario;

                $datosTipoBuzon = TipoBuzon::all('id_tipo_buzon','nombre');
                $aJsonSalida["tipo_buzon"] = $datosTipoBuzon;

                $datosTipoArchivo = TipoArchivo::all('id_tipo_archivo','nombre');
                $aJsonSalida["tipo_archivo"] = $datosTipoArchivo;

                $datosTipoAccion = TipoAccion::all('id_tipo_accion','nombre');
                $aJsonSalida["tipo_accion"] = $datosTipoAccion;

                $datosAccion = Accion::all('id_accion', 'id_tipo_accion','nombre');
                $aJsonSalida["accion"] = $datosAccion;
                
                $datosTipoOrigen = TipoOrigen::all('id_tipo_origen', 'nombre');
                $aJsonSalida["tipo_origen"] = $datosTipoOrigen;

                $datosTipoFlujo = TipoFlujo::all('id_tipo_flujo', 'nombre');
                $aJsonSalida["tipo_flujo"] = $datosTipoFlujo;

                $datosTipoAvance = TipoAvance::all('id_tipo_avance', 'nombre');
                $aJsonSalida["tipo_avance"] = $datosTipoAvance;

                $datosTipoFolio = TipoFolio::all('id_tipo_folio', 'nombre');
                $aJsonSalida["tipo_folio"] = $datosTipoFolio;

                $datosTipoFlujoAccion = TipoFlujoAccion::all('id_tipo_flujo_accion', 'id_tipo_flujo', 'id_accion')->sortBy('id_accion');
                $aJsonSalida["tipo_flujo_accion"] = $datosTipoFlujoAccion;
                
                $datosTipoAsignacionFolio = TipoAsignacionFolio::all('id_tipo_asignacion_folio', 'nombre');
                $aJsonSalida["tipo_asignacion_folio"] = $datosTipoAsignacionFolio;
                
                $datosNivelAcceso = NivelAcceso::all('id_nivel_acceso', 'nombre');
                $aJsonSalida["nivel_acceso"] = $datosNivelAcceso;

                $datosCarpeta = Carpeta::all('id_carpeta', 'nombre');
                $aJsonSalida["carpeta"] = $datosCarpeta;

                $datosEstadoDocumento = EstadoDocumento::all('id_estado_documento', 'nombre', 'nombre_corto', 'codigo_color');
                $aJsonSalida["estado_documento"] = $datosEstadoDocumento;

                $datosTipoDestino = TipoDestino::all('id_tipo_destino', 'nombre');
                $aJsonSalida["tipo_destino"] = $datosTipoDestino;

                return $this->respondSuccess($aJsonSalida, 200);
            }  
            catch (ModelNotFoundException $e) 
            {
                return $this->respondError('Falla al obtener parámetros', 500);
            } 
        }
        else 
            return $this->respondError('Json inválido', 406);
    }

}
