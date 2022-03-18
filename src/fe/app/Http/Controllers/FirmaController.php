<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Libraries\FirmaBase;
use App\Models\DocumentoBuzonArchivo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
//use App\Http\Controllers\FirmaController;

use PDF;

class FirmaController extends Controller
{
    //public function firmar($id, Request $request)
    public function firmar($id)
    {
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

    /**
     * Remplaza documento por archivo firmado por SEGPRES
    */

    public function storeSignedFile($encondedFile, $filePath)
	{

    }

}
