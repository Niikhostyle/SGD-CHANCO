<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Libraries\FirmaBase;

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
                    return $this->respondFail('Usuario no tiene permiso para realizar firma electrónica.');

                $aDocumentoBuzon = DocumentoBuzonArchivo::where('id_documento_buzon', '=', $datos['id_documento_buzon'])
                                                        ->where('id_tipo_archivo','=', '1')
                                                        ->where('version','=', '1')
                                                        ->where('nombre_archivo_codificado','!=', null)
                                                        ->first();

                if($aDocumentoBuzon == null)
                    return $this->respondFail('No existe archivo para realizar firma electrónica.');

                /* AQUI DEBE IR CODIGO DE FEA */ 

                $firmaDigitalConfig = array(
                    'api'       => env('PLCSGD_API_URL'),
                    'purpose'   => env('PLCSGD_API_PURPOSE'),
                    'entity'    => env('PLCSGD_API_ENTITY'),
                    'tokenKey'  => env('PLCSGD_API_TOKEN_KEY'),
                    'secretKey' => env('PLCSGD_SECRETO')
                );

                $classFirma = new FirmaBase($firmaDigitalConfig); 

                $sDescipcion = "Descripcion de prueba";//sacar de tabla documentos
                $nrut = '22222222';//$request['rut'];
                $sPath = config('app.path_upload') . '/'; //storage_path('app/public/files/')
                $sArchivo = storage_path($sPath.'TDXY-220-20220202-44527820'); //cambiar por linea sgte
                //$sArchivo = storage_path($sPath.$request['archivo']);
                $id_tipo_archivo = 1;//$request['id_tipo_archivo'];
                $id_documento_buzon = 430;////$request['id_documento_buzon'];

                $dFechaCreacion = date('Y-m-d H:i:s');

               // $nNombreArchivoCargar = $this->getNombreDocumento($id);

                $aRespuestaFirma = $classFirma->setRUN($nrut)                        
                            ->addPDF($sArchivo, $sDescipcion)
                            ->sign();
                return $aRespuestaFirma;
                /* Si existe algun error */
                if (isset($aRespuestaFirma['status'])) 
                {
                    return array('status' => 0, 'comentario' => $aRespuestaFirma['error']);
                    //return $aRespuestaFirma['error'];     
                }
                else
                    return array('status' => 1, 'comentario' => 'Firma OK');



                //$this->firmar($datos['id_documento']);

                /*
                //actualizar estado
                DocumentoBuzon::find($datos["id_documento_buzon"])->update(['id_estado_documento' => 9]);
                
                //registrar accion en bitacora

                $documentoBuzonBitacora = DocumentoBuzonBitacora::create([
                    'id_documento_buzon' => $datos["id_documento_buzon"],
                    'id_accion' => 7,
                    'fecha' => date('Y-m-d H:i:s'),
                    'id_usuario' => $datos['id_usuario']
                ]); 
                */
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

    public function firmar($id)
    {
        return "firma";
        
        //inicialización con datos de conf de fea autorizada
        $firmaDigitalConfig = array(
            'api'       => config('app.sgd_url'),
            'purpose'   => config('app.sgd_proposito'),
            'entity'    => config('app.sgd_entidad'),
            'tokenKey'  => config('app.sgd_token_key'),
            'secretKey' => config('app.sgd_secreto')
        );

        $classFirma = new FirmaBase($firmaDigitalConfig); 

        $sDescipcion = "Descripcion de prueba";//sacar de tabla documentos
        $nrut = '22222222';//$request['rut'];
        $sPath = config('app.path_upload') . '/';
        $sArchivo = storage_path($sPath.'TDXY-220-20220202-44527820'); //cambiar por linea sgte
        //$sArchivo = storage_path($sPath.$request['archivo']);
        $id_tipo_archivo = 1;//$request['id_tipo_archivo'];
        $id_documento_buzon = 430;////$request['id_documento_buzon'];

        $dFechaCreacion = date('Y-m-d H:i:s');

        $nNombreArchivoCargar = $this->getNombreDocumento($id);

        $aRespuestaFirma = $classFirma->setRUN($nrut)                        
                    ->addPDF($sArchivo, $sDescipcion)
                    ->sign();
        
        /* Si existe algun error */
        if (isset($aRespuestaFirma['status'])) 
        {
            return array('status' => 0, 'comentario' => $aRespuestaFirma['error']);
            //return $aRespuestaFirma['error'];     
        }
        
        if ($aRespuestaFirma['metadata']['filesSigned'] == 1 )
        {
            $responseFile = $aRespuestaFirma['files'][0];            
            if($responseFile['status'] == 'OK') 
            {
                $encondedFile = $responseFile['content'];  
                //$storeResp = $this->storeSignedFile($encondedFile, storage_path('app/public/files/principal_220_.pdf'));
                
                $decodedFile = base64_decode($encondedFile, true);
                if (empty($encondedFile) || ! base64_encode($decodedFile) === $encondedFile) {
                    return array('status' => 0, 'comentario' => 'Error de codificación en archivo firmado.');
                }                

                $pdf = fopen (storage_path($sPath.$nNombreArchivoCargar),'w+');
                if (!$pdf)
                    return array('status' => 0, 'comentario' => 'No se pudo crear archivo firmado.');         
                
                fwrite ($pdf, $decodedFile);
                fclose ($pdf);
                
                if (!file_exists(storage_path($sPath.$nNombreArchivoCargar)))
                {
                    return array('status' => 0, 'comentario' => 'No se encuentra el archivo firmado');         
                }
                
                //actualizar archivo firmado

                if($id_tipo_archivo == 1)
                {
                    try 
                    {
                        DB::beginTransaction();
                        $docsPpales = DocumentoBuzonArchivo::where('id_documento_buzon', $id_documento_buzon)
                                                ->where('id_tipo_archivo', 1)
                                                ->get();
                        
                        foreach ($docsPpales as $archFile)
                        {
                            $nSalida = $archFile->version + 1;
                            DocumentoBuzonArchivo::find($archFile->id_documento_buzon_archivo)->update(['version' => $nSalida]);
                        }

                        $nVersion = 1;

                        DocumentoBuzonArchivo::create([
                            'id_documento_buzon' => $id_documento_buzon,
                            'id_tipo_archivo' => $id_tipo_archivo,
                            'nombre_archivo_original' => 'TDXY-220-20220202-44527820.pdf', //cambiar por parametro
                            'nombre_archivo_codificado' => $nNombreArchivoCargar,
                            'fecha' => $dFechaCreacion,
                            'version' => $nVersion
                        ]);    

                        DB::commit();

                        return array(
                            'status'  => 1,
                            'comentario' => 'Archivo firmado almacenado exitosamente.',
                            'file'    => $nNombreArchivoCargar,
                        );

                    }
                    catch (ModelNotFoundException $e) {
                        DB::rollBack();

                        return array(
                            'status' => 500, 
                            'comentario' => 'Error al guardar documento firmado.'
                        );
                    }
                }               
                    
            }
        }
    }
}
