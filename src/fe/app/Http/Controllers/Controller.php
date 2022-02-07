<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function getNombreDocumento($idDoc)
    {
        $datosDocumento = Documento::findOrFail($idDoc);
               
        $datosJsonTipoDocumento = json_decode($datosDocumento['json_tipo_documento'],true);

        $nAleatorio = rand(100000,99999999);
        $dFechaCreacion = date('Ymd');
        $txtTipoDoc = $datosJsonTipoDocumento['nombre_corto'];
        
        $nombreFinal = $txtTipoDoc . '-' . $idDoc . '-' . $dFechaCreacion . '-' . $nAleatorio;

        return $nombreFinal;
    }

}
