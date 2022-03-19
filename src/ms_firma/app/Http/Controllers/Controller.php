<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function respondSuccess($message, $status) //200 - 201
    {
        return response()->json([
            'status' => $status, 
            'data' => $message
        ], $status);
    }

    protected function respondFail($message) //400
    {
        $result = array('status' => '400', 'data' => array('comentario' => $message));

        return response()->json($result, '400');  
    }
    
    protected function respondError($message, $status) //406
    {
        return response()->json([
            'status' => $status, 
            'data' => [
                'comentario' => $message
        ]], $status);
    }

    public function getNombreDocumento($idDoc)
    {
        $datosDocumento = Documento::findOrFail($idDoc);
               
        $datosJsonTipoDocumento = json_decode($datosDocumento['json_tipo_documento'],true);

        $nAleatorio = rand(100000,99999999);
        $dFechaCreacion = date('Ymd');
        $txtTipoDoc = $datosJsonTipoDocumento['nombre_corto'];
        
        $nombreFinal = $txtTipoDoc . '-' . $idDoc . '-' . $dFechaCreacion . '-' . $nAleatorio . '.pdf';

        return $nombreFinal;
    }

    
}
