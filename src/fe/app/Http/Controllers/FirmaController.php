<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Libraries\FirmaBase;
use Illuminate\Support\Facades\App;
use PDF;

class FirmaController 
{
    public function index()
    {
        $firmaDigitalConfig = array(
            'api'       => config('app.sgd_url'),
            'purpose'   => config('app.sgd_proposito'),
            'entity'    => config('app.sgd_entidad'),
            'tokenKey'  => config('app.sgd_token_key'),
            'secretKey' => config('app.sgd_secreto')
        );
        
        $classFirma = new FirmaBase($firmaDigitalConfig);

        $sDescipcion = "Descripcion de prueba";
        $aRespuestaFirma = $classFirma->setRUN('22222222')                        
                    ->addPDF(storage_path('app/public/files/TDXY-220-20220201-89148601'), $sDescipcion)
                    ->sign();
        
        /* Si existe algun error */
        if (isset($aRespuestaFirma['status'])) 
        {
            return $aRespuestaFirma['error'];     
        }
        
        if ($aRespuestaFirma['metadata']['filesSigned'] == 1 )
        {
            $responseFile = $aRespuestaFirma['files'][0];            
            if($responseFile['status'] == 'OK') 
            {
                $encondedFile = $responseFile['content'];  
                //$storeResp = $this->storeSignedFile($encondedFile, storage_path('app/public/files/principal_220_.pdf'));
                
                $decodedFile = base64_decode($encondedFile, true);
                $pdf = fopen (storage_path('app/public/files/principal_firma.pdf'),'w+');
                fwrite ($pdf,$decodedFile);
                fclose ($pdf);
                
                if (file_exists(storage_path('app/public/files/principal_firma.pdf')))
                {
                    return "Archivo firmado";
                }
                else 
                    return "No se encuentra el archivo firmado";
            }
        }


    }

    /**
     * Remplaza documento por archivo firmado por SEGPRES
    */

    public function storeSignedFile($encondedFile, $filePath)
	{
        $decodedFile = base64_decode($encondedFile, true);

        if (empty($encondedFile) || ! base64_encode($decodedFile) === $encondedFile) {
            return array('status' => 0, 'Mensaje' => 'Error de codificación en archivo firmado.');
        }

        $uploadSuccess = $file->move(storage_path('app/public/files'), $nNombreArchivoCargar);

        if ($uploadSuccess)
        {
            if(strlen($fileName) && $id_tipo_archivo == 1)
            {
                $docsPpales = DocumentoBuzonArchivo::where('id_documento_buzon', $id_documento_buzon)
                                        ->where('id_tipo_archivo', 1)
                                        ->get();
                
                foreach ($docsPpales as $archFile)
                {
                    $nSalida = $archFile->version + 1;
                    DocumentoBuzonArchivo::find($archFile->id_documento_buzon_archivo)->update(['version' => $nSalida]);
                }

                if ($id_tipo_archivo == 1)
                    $nVersion = 1;
            }

            
        }
        else
        {
            return response()->json([
                'status' => 500, 
                'data' => [
                    'comentario' => 'Error al guardar documento'
            ]], 500);
        }
        
        //if ( ! $this->eliminaArchivo($filePath)) {
        //    return array('status' => 0, 'Mensaje' => 'No se pudo eliminar el archivo original', 'file'=> $filePath);
        //}
        /*
        $this->load->library('S3', null, 'aws_s3');
        $doc_type = array('Content-Type' => 'application/pdf');
        $s3_path = make_relative_file_path($filePath);
        $s3_bucket = $this->config->item('s3_bucket_name');
        if ($this->aws_s3->putObject($decodedFile, $s3_bucket, $s3_path, NULL, array(), $doc_type) == FALSE) {
            return array('status' => 0, 'Mensaje' => 'No se pudo almacenar el archivo firmado','file'=> $s3_path);
        }
        
        // doble check de archivo en s3
        if ($this->aws_s3->getObjectInfo($s3_bucket, $s3_path) == FALSE) {
            return array('status' => 0, 'Mensaje' => 'No se encuentra el archivo firmado','file'=> $s3_path);
        }
        */
        return array(
            'status'  => 1,
            'Mensaje' => 'Archivo firmado almacenado exitosamente',
            'file'    => $s3_path,
        );
	}

}
