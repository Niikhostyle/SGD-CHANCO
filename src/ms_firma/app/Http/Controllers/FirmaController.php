<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Users;
use App\Models\DocumentoBuzon;
use App\Models\DocumentoBuzonBitacora;
use App\Models\DocumentoBuzonArchivo;


class FirmaController extends Controller
{
    public function firmar_archivo(Request $request)
    {
        if ($request->isJson())
        {
            try 
            {
                DB::beginTransaction();

                $datos = $request->json()->all();

                $aInfoUsuarios = Users::where('id', $datos['id_usuario'])->first(['aplica_fea']);

                if (!$aInfoUsuarios['aplica_fea'])
                    return $this->respondError('Usuario no tiene permiso para realizar firma electrónica.', 500);

                $aDocumentoBuzon = DocumentoBuzonArchivo::where('id_documento_buzon', '=', $datos['id_documento_buzon'])
                                                        ->where('id_tipo_archivo','=', '1')
                                                        ->where('version','=', '1')
                                                        ->where('nombre_archivo_codificado','!=', null)
                                                        ->first();

                if($aDocumentoBuzon == null)
                    return $this->respondError('No existe archivo para realizar firma electrónica.', 500);

                /* AQUI DEBE IR CODIGO DE FEA */ 

                //actualizar estado
                DocumentoBuzon::find($datos["id_documento_buzon"])->update(['id_estado_documento' => 9]);
                
                //registrar accion en bitacora

                $documentoBuzonBitacora = DocumentoBuzonBitacora::create([
                    'id_documento_buzon' => $datos["id_documento_buzon"],
                    'id_accion' => 7,
                    'fecha' => date('Y-m-d H:i:s'),
                    'id_usuario' => $datos['id_usuario']
                ]); 
                
                DB::commit();
                
                return $this->respondSuccess("Enviado a Firma", 200);


            } catch (ModelNotFoundException $e) {
                DB::rollBack();

                return $this->respondError('' . $e->getMessage(), 500);
            }
        }
        else
            return $this->respondError('Json inválido', 406);    
                

    }
}
