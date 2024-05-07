<?php

namespace App\Http\Controllers;

use Exception;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

use App\Libraries\FirmaBase;
use App\Models\Buzon;
use App\Models\TipoFirma;
use App\Models\Users;
use App\Models\Documento;
use App\Models\DocumentoBuzon;
use App\Models\DocumentoBuzonBitacora;
use App\Models\DocumentoBuzonArchivo;
use setasign\Fpdi\Fpdi;
use App\Libraries\PDF;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


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
                //GENERACIÓN IMAGEN PARA FIRMA
                $aInfoUsuarios = Users::where('id', $datos['id_usuario'])->first(['run','nombres', 'primer_apellido','segundo_apellido','img_firma','aplica_fea','id']);
                $sNombre = $aInfoUsuarios['nombres'] . ' ' . $aInfoUsuarios['primer_apellido'] . ' ' . $aInfoUsuarios['segundo_apellido'];
                $sNombreImg = $aInfoUsuarios['run'] . date('dmYHis') . '.png';
                $sNombreImgAnexo = $aInfoUsuarios['run'] . date('dmYHis') . '_a.png';
                $dFechaCreacion = date('Y-m-d H:i:s');
        
                $DatosFirma = Buzon::join('buzon_usuario', 'buzon_usuario.id_buzon','=','buzon.id_buzon')
                            ->join('tipo_firma', 'buzon_usuario.id_tipo_firma','=', 'tipo_firma.id_tipo_firma')
                            ->where('buzon.id_buzon','=', $datos['id_buzon'])
                            ->where('buzon_usuario.id_usuario','=', $datos['id_usuario'])
                            ->select('cargo_firma', 'tipo_firma.id_tipo_firma', 'sigla','restringir_sr','id_usuario_sr')
                            ->first();

                if (!isset($DatosFirma['cargo_firma']))  
                { 
                    $comentario = "No existe cargo asociado al buzón.";
                    throw new Exception($comentario);
                    //return $this->respondFail($comentario);
                }

                //verificar restrinccion firma subrogante
                if(isset($DatosFirma['id_usuario_sr']) && $DatosFirma['id_usuario_sr'] !== 0 && $DatosFirma['restringir_sr'] == 1){
                    if($DatosFirma['id_usuario_sr'] == 10000){
                        $comentario = "No existe subrogante definido.";
                        throw new Exception($comentario);
                    }
                    if($DatosFirma['id_usuario_sr'] != $aInfoUsuarios['id']){
                        $comentario = "No está autorizado como subrogante.";
                        throw new Exception($comentario);
                    }
                }

                
                if ($aInfoUsuarios['img_firma'] == '' || $aInfoUsuarios['img_firma'] == null) 
                {
                    $comentario = "No existe imagen para firma asociada al usuario.";
                    throw new Exception($comentario);
                   //return $this->respondFail($comentario);
                }   
                
                $img = Image::make(storage_path('app/public/files/imagen_firma/'.$aInfoUsuarios['img_firma']));
                $dFechaCreacionImg = date('d.m.Y H:i:s');
                $img->text('Firmado electrónicamente por:', 330, 60, function ($font) { $font->file(storage_path('../public/calibri.ttf')); $font->size(34); }); 
                $img->text(Str::upper($sNombre), 330, 110, function ($font) { $font->file(storage_path('../public/calibrib.ttf')); $font->size(34); }); 
                $img->text('Fecha: '. $dFechaCreacionImg, 330, 160, function ($font) { $font->file(storage_path('../public/calibri.ttf')); $font->size(34); }); 

                $string = wordwrap($DatosFirma['cargo_firma'], 35) . ' ' . $DatosFirma['sigla'];
                $img->text($string, 330, 235, function ($font) { $font->file(storage_path('../public/calibri.ttf')); $font->size(34); });         
        
                $img->save(storage_path('app/public/files/imagen_firma/'.$sNombreImg));  

                //INICIO PROCESO FIRMA
                $aInfoDocumento = Documento::where('id_documento', $datos['id_documento'])->first(['hash_validacion', 'identificador', 'json_tipo_documento']);                

                if (!$aInfoUsuarios['aplica_fea'])
                {
                    $comentario = "Usuario no tiene permiso para realizar firma electrónica.";
                    throw new Exception($comentario);
                    //return $this->respondFail($comentario);
                }                    
                
                $aDocumentoBuzon = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon_archivo.id_documento_buzon','=','documento_buzon.id_documento_buzon')
                                                        ->join('documento', 'documento_buzon.id_documento','=','documento.id_documento')
                                                        ->where('documento_buzon.id_documento', '=', $datos['id_documento'])
                                                        ->where('id_tipo_archivo','=', '1')
                                                        ->where('version','=', '1')
                                                        ->where('nombre_archivo_codificado','!=', null)
                                                        ->select('nombre_archivo_codificado','paginas_archivo','id_tipo_archivo','folio')
                                                        ->first();
                
                if(!isset($aDocumentoBuzon['nombre_archivo_codificado']))
                {
                    
                    $comentario = "No existe archivo para realizar firma electrónica.";
                    
                    //generar pdf                    
                    $datosArchivo = Http::withHeaders(['key'=>$request->header('key'),'Content-Type'=>'application/json']) //
                    ->timeout(30)        
                    ->put('http://sgd_ms_archivos:3333/api/sgd-archivos/generar_archivo_pdf', [            
                        'id_documento'=>$datos['id_documento'],
                        'id_documento_buzon'=>$datos['id_documento_buzon'],
                        'id_usuario'=>$datos['id_usuario'],
                        'id_buzon'=>$datos['id_buzon']
                    ]);

                    if (isset($datosArchivo['status']) && $datosArchivo['status'] == '400')
                    {
                        throw new Exception($datosArchivo['data']['comentario']);
                    }

                    $aDocumentoBuzon = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon_archivo.id_documento_buzon','=','documento_buzon.id_documento_buzon')
                                                        ->join('documento', 'documento_buzon.id_documento','=','documento.id_documento')
                                                        ->where('documento_buzon.id_documento', '=', $datos['id_documento'])
                                                        ->where('id_tipo_archivo','=', '1')
                                                        ->where('version','=', '1')
                                                        ->where('nombre_archivo_codificado','!=', null)
                                                        ->select('nombre_archivo_codificado','paginas_archivo','id_tipo_archivo','folio')
                                                        ->first();
                    
                }
                
                $firmaDigitalConfig = array(
                    'api'       => env('PLCSGD_API_URL'),
                    'purpose'   => env('PLCSGD_API_PURPOSE'),
                    'entity'    => env('PLCSGD_API_ENTITY'),
                    'tokenKey'  => env('PLCSGD_API_TOKEN_KEY'),
                    'secretKey' => env('PLCSGD_SECRETO')
                );                
                $classFirma = new FirmaBase($firmaDigitalConfig);
                
                $sNombreArchivo = $aDocumentoBuzon['nombre_archivo_codificado'];
                // $comentario = $sNombreArchivo;
                // throw new Exception($comentario);

                $sDescipcion = "Firmado electrónicamente por " . $aInfoUsuarios['nombres'] . ' ' . $aInfoUsuarios['primer_apellido'] . ' ' . $aInfoUsuarios['segundo_apellido'];
                $nRut = explode("-",$aInfoUsuarios['run']);
                $nRutFirma = $nRut[0];
                $sPath = config('app.path_upload') . '/'; 
                $sArchivo = storage_path('app/public/files/'.$sNombreArchivo); //cambiar por linea sgte               
                $id_documento_buzon = $datos['id_documento_buzon'];
                $imagen_firma = storage_path('app/public/files/imagen_firma/'.$sNombreImg); 
                $imagen_firma_anexo = storage_path('app/public/files/imagen_firma/'.$sNombreImgAnexo);

                

                if ( !file_exists($imagen_firma) )
                {
                    $comentario = "Existe un problema con la imagen relacionada a la firma electrónica.";
                    throw new Exception($comentario);
                }
                
                //acciones de firma en bitacora
                $datosBitacora = DocumentoBuzonBitacora::join('documento_buzon', 'documento_buzon_bitacora.id_documento_buzon','=','documento_buzon.id_documento_buzon')
                ->where('documento_buzon.id_documento', $datos['id_documento'])
                ->where('documento_buzon_bitacora.id_accion', 7)
                ->get();
                
                //obtener máximo de firmas
                $datosJsonTipoDocumento = json_decode($aInfoDocumento['json_tipo_documento'],true);

                //plantilla distribución

                $nEspacioDistribucion = 0;
                if (isset($datosJsonTipoDocumento['plantilla_distribucion']))
                {
                    $tPlantillaDistribucion = $datosJsonTipoDocumento['plantilla_distribucion'];
                    $numLineasDistribucion = substr_count($tPlantillaDistribucion, "\n");

                    $nEspacioDistribucion = $numLineasDistribucion * 20;
                }
                else{
                    $nEspacioDistribucion = 50;
                }

                if (isset($datosJsonTipoDocumento['numero_firmas']))
                    $nNroFirmas = $datosJsonTipoDocumento['numero_firmas'];
                else 
                    $nNroFirmas = 4;  

                if (count($datosBitacora) >= $nNroFirmas)
                {
                    $comentario = "Excede el máximo de firmas electrónicas posibles.";
                    throw new Exception($comentario);
                }

                $aUbicacionesFirma = array(
                    array(300, 240, 555, 325), 
                    array(30, 240, 285, 325),
                    array(300, 140, 555, 225),
                    array(30, 140, 285, 225),
                    array(300, 40, 555, 125),
                    array(30, 40, 285, 125)
                );

/*                $aUbicacionesFirma = array(
                    array(300, 280, 555, 365), 
                    array(30, 280, 285, 365),
                    array(300, 180, 555, 265),
                    array(30, 180, 285, 265),
                    array(300, 80, 555, 165),
                    array(30, 80, 285, 165)
                );*/

                //posiciones desde donde comenzar a utilizar las ubicaciones del array anterior
                $aFirmaPosicion = array(
                    '1' => array('0'=>4),//4, 
                    '2' => array('0'=>4,'1'=>5), 
                    '3' => array('0'=>2,'1'=>3,'2'=>4),
                    '4' => array('0'=>2,'1'=>3,'2'=>4,'3'=>5),
                    '5' => array('0'=>0,'1'=>1,'2'=>2,'3'=>3,'4'=>4), 
                    '6' => array('0'=>0,'1'=>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5) 
                );               

                //$aFirmaPosicion[5]; hay 5 firmas, parten desde la posicion 0 del array $aUbicacionesFirma
                //cantidad de firmas $nNroFirmas
                //count($datosBitacora) firma actual

                $nPosFirma = count($datosBitacora);
                $nPosCant = $aFirmaPosicion[$nNroFirmas][$nPosFirma];
                
                $n_llx = $aUbicacionesFirma[$nPosCant][0];
                $n_lly = $aUbicacionesFirma[$nPosCant][1] + $nEspacioDistribucion;
                $n_urx = $aUbicacionesFirma[$nPosCant][2];
                $n_ury = $aUbicacionesFirma[$nPosCant][3] + $nEspacioDistribucion; 
                
                //por defecto anexo
                $n_llx_anexo = 300;
                $n_lly_anexo = 80;
                $n_urx_anexo = 555;
                $n_ury_anexo = 165;

                //firma 1 anexo
                if (count($datosBitacora) == 0)
                {
                    $n_llx_anexo = 300;
                    $n_lly_anexo = 80;
                    $n_urx_anexo = 555;
                    $n_ury_anexo = 165;
                }
                //firma 2 anexo
                if (count($datosBitacora) == 1) 
                {
                    $n_llx_anexo = 30;
                    $n_lly_anexo = 80;
                    $n_urx_anexo = 285;
                    $n_ury_anexo = 165;
                }
                
                $pdf = new PDF();
                $pageCount = $pdf->setSourceFile($sArchivo);  

                $nPaginasPdf = $aDocumentoBuzon['paginas_archivo'];
                if ($aDocumentoBuzon['paginas_archivo'] == '' || $aDocumentoBuzon['paginas_archivo'] == null)
                    $nPaginasPdf = $pageCount;

                $layout = array(
                    'filename' => $imagen_firma,
                    'page'     => $nPaginasPdf,
                    'llx'      => $n_llx, //50
                    'lly'      => $n_lly, //50
                    'urx'      => $n_urx, //210
                    'ury'      => $n_ury  //100
                );

                $layoutAnexo = array(
                    'filename' => $imagen_firma_anexo,
                    'page'     => 'LAST',
                    'llx'      => $n_llx_anexo, 
                    'lly'      => $n_lly_anexo, 
                    'urx'      => $n_urx_anexo, 
                    'ury'      => $n_ury_anexo  
                );
               
                $nNombreArchivoCargar = $this->getNombreDocumento($datos['id_documento']); 
                //agrega Hash de validación
                if (count($datosBitacora) == 0)
                {                            
                    //QR para validacion
                    $url= env('APP_URL').'/validador_qr/'.$aInfoDocumento['hash_validacion'];
                    //$codigoQR ='http://chart.apis.google.com/chart?chs=90x90&cht=qr&chl='.$url.'&.png';
                    $codigoQR ='https://quickchart.io/qr?text='.$url.'&size=100&.png';
                    $html = '                                      ID: ' . $aInfoDocumento['identificador'] .' | Para validar el documento haga click <a href="'.$url.'">aqui</a>, o escanee el codigo QR.';

                    
                    if(!file_get_contents($codigoQR)){
                        $comentario = "Falla en la generación de código QR";
                        throw new Exception($comentario);
                    }

                    $pdf->AliasNbPages();                    
                    //$pdf->footer_txt = "Para verificar este documento, use el siguiente identificador: " . $aInfoDocumento['hash_validacion'];}
                    $pdf->footer_txt = $html;//"Para verificar este documento haga clic en ".$url." o use el siguiente QR:";
                    $pdf->footer_qr = $codigoQR;
                    $pdf->url_qr = $url;
                    //$pdf->Image($codigoQR,140,325,0,0,'PNG',$url);
                    $pdf->PageFirma = $nPaginasPdf;                    

                    for ($i=1; $i <= $pageCount; $i++) { 
                        //import a page then get the id and will be used in the template
                        $tplId = $pdf->importPage($i);

                        $size = $pdf->getTemplateSize($tplId);
                        $pdf->AddPage($size['orientation'], array($size['width'], $size['height']));

                        //use the template of the imporated page
                        $pdf->useTemplate($tplId);                    
                    }
                    
                    $sArchivo = storage_path('app/public/files/'.$nNombreArchivoCargar);                 
                    $pdf->Output($sArchivo, 'F');                   
                    
                }
                
                //obtiene el peso del archivo para firma

                $fileSize = filesize($sArchivo);
                $fileSize_mb = round($fileSize / 1048576, 0, PHP_ROUND_HALF_UP);
                $nfh = 0;
                //Log::error('FIRMA - peso archivo '.$fileSize_mb); 

                if ($fileSize_mb >= 5) //firma hash  
                {              
                    $aRespuestaFirma = $this->callHash($sArchivo, $layout, '', $nNombreArchivoCargar, $nRutFirma);
                    $nfh = 1;

                //Log::error('FIRMA - hash '); 

                }
                else //firma tradicional
                {
                    $aRespuestaFirma = $classFirma->setRUN($nRutFirma)                        
                                                  ->addPDF($sArchivo, $sDescipcion, $layout)
                                                  ->sign();   
                                                  
                    //Log::error('FIRMA - tradicional '); 
                                                  
                }                                                 
                
                if (isset($aRespuestaFirma['status'])) 
                {
                    $comentario = $aRespuestaFirma['error'];
                    throw new Exception($comentario);
                }

                if (isset($aRespuestaFirma['metadata']))
                {
                    if ($aRespuestaFirma['metadata']['filesSigned'] == 1 )
                    {
                        $responseFile = $aRespuestaFirma['files'][0];            
                        if($responseFile['status'] == 'OK') 
                        {
                            if ($nfh == 0)
                            {
                                $encondedFile = $responseFile['content'];  
                                
                                $decodedFile = base64_decode($encondedFile, true);
                                if (empty($encondedFile) || !base64_encode($decodedFile) === $encondedFile) {
                                    $comentario = "Error de codificación en archivo firmado.";
                                    throw new Exception($comentario);
                                }                

                                $pdf = fopen (storage_path('app/public/files/'.$nNombreArchivoCargar),'w+');
                                if (!$pdf)
                                {
                                    $comentario = "No se pudo crear archivo firmado. ";
                                    throw new Exception($comentario);
                                }

                                fwrite ($pdf, $decodedFile);
                                fclose ($pdf);
                            }
                            
                            if (!file_exists(storage_path('app/public/files/'.$nNombreArchivoCargar)))
                            {
                                $comentario = "No se encuentra el archivo firmado. ";
                                throw new Exception($comentario);
                            }
                            
                            //actualizar archivo firmado
                            if($aDocumentoBuzon['id_tipo_archivo'] == 1)
                            {
                                $docsPpales = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon_archivo.id_documento_buzon','=','documento_buzon.id_documento_buzon')
                                                            ->where('id_documento', $datos['id_documento'])
                                                            ->where('id_tipo_archivo', 1)
                                                            ->get();
                    
                                foreach ($docsPpales as $archFile)
                                {                        
                                    $nSalida = $archFile->version + 1;
                                    DocumentoBuzonArchivo::find($archFile->id_documento_buzon_archivo)->update(['version' => $nSalida]);
                                }

                                DocumentoBuzonArchivo::create([
                                    'id_documento_buzon' => $id_documento_buzon,
                                    'id_tipo_archivo' => 1,
                                    'nombre_archivo_original' => $sNombreArchivo, 
                                    'nombre_archivo_codificado' => $nNombreArchivoCargar,
                                    'fecha' => $dFechaCreacion,
                                    'version' => 1
                                ]); 
                                
                                //actualiza estado
                                DocumentoBuzon::find($id_documento_buzon)->update(['id_estado_documento' => 9]);
                                
                                //registrar accion de firma en bitacora
                                $documentoBuzonBitacora = DocumentoBuzonBitacora::create([
                                    'id_documento_buzon' => $id_documento_buzon,
                                    'id_accion' => 7,
                                    'fecha' => $dFechaCreacion,
                                    'id_usuario' => $datos['id_usuario'] 
                                ]);   

                                //registrar accion de cambio de archivo ppal en bitacora
                                $this->saveBitacora($id_documento_buzon, $dFechaCreacion, $datos['id_usuario'], "Cambio en archivo principal por firma electrónica.",5);                            
                            } 
                            
                            //firma anexos

                            $fAnexos = $this->firmaAnexos($datos['id_documento'], $datosBitacora, $sNombre, $aInfoUsuarios['img_firma'], $DatosFirma, $sNombreImgAnexo, $layoutAnexo, $nRutFirma);

                            if (isset($fAnexos['status']))
                            {
                                if ($fAnexos['status'] == "400")
                                {
                                    $comentario = "Error en firma de anexo. ";
                                    throw new Exception($comentario);
                                }
                            }                             
                            
                            DB::commit();  
                            
                            //elimina imagen de firma 
                            $this->deleteImg($sNombreImg); 

                            
                            return $this->respondSuccess("Archivo firmado almacenado exitosamente.", 200);
                                
                        }

                        //elimina imagen de firma
                        $this->deleteImg($sNombreImg);  
                                                
                    }
                    else
                    {
                        $comentario = "No se pudo procesar la respuesta.";
                        //Log::error("Dump Respuesta: " . $aRespuestaFirma); 
                        throw new Exception($comentario);
                    }
                }

                $comentario = "Error en el proceso de firma.";
                throw new Exception($comentario);                    

            } catch (Exception $e) {

                DB::rollBack();
                
                $msgError = "Error al generar la Firma Electrónica:" . $e->getMessage();
                $this->saveBitacora($datos['id_documento_buzon'], $dFechaCreacion, $datos['id_usuario'],$msgError,13);
                $this->deleteImg($sNombreImg);                
                $this->deleteImg($sNombreImgAnexo);//elimina imagen anexo de firma

                Log::error("Error al generar la Firma Electrónica: " . $e->getMessage()); 

                return $this->respondFail("Error al generar la Firma Electrónica: " . $e->getMessage());
            }
        }
        else
            return $this->respondError('Json inválido', 406);    
                

    }

    public function saveBitacora($docbuzon,$fecha,$usuario,$comentario,$accion)
    {
        //registrar accion en bitacora
        $documentoBuzonBitacora = DocumentoBuzonBitacora::create([
            'id_documento_buzon' => $docbuzon,
            'id_accion' => $accion,
            'fecha' => $fecha,
            'id_usuario' => $usuario,
            'mensaje_respuesta' => $comentario
        ]);  
    }

    public function deleteImg($sImg)
    {
        //elimina imagen de firma
        $filename = storage_path('app/public/files/imagen_firma/'.$sImg);
        if (file_exists($filename))
            unlink($filename);
    }

    public function callHash($sArchivo, $layout, $ultimaPag = 0, $nNombreArchivoFirmado, $nRut)
    {
        $classFilePath = storage_path('../app/Libraries/firmaHashV1.jar');
        $sPathArchivoFirmado = storage_path('app/public/files/'.$nNombreArchivoFirmado);
       
        if ($ultimaPag == 0)
            $nPagina = $layout['page'];
        else
            $nPagina = $ultimaPag;
        
        //$cmd = "java -jar firma.jar -a '".env('PLCSGD_API_URL')."' -e '".env('PLCSGD_API_ENTITY')."' -f '".$sArchivo."' -k ".env('PLCSGD_SECRETO')." -p ".env('PLCSGD_API_PURPOSE')." -r 22222222";
        $cmd = "java -jar ".$classFilePath." -a '".env('PLCSGD_API_URL')."' -e '".env('PLCSGD_API_ENTITY')."' -f '".$sArchivo."' -i ".$layout['filename']." -o ".$sPathArchivoFirmado." -k ".env('PLCSGD_SECRETO')." -p ".env('PLCSGD_API_PURPOSE')." -r ".$nRut." -t ".env('PLCSGD_API_TOKEN_KEY')." -u '".$layout['llx'].",".$layout['lly'].",".$layout['urx'].",".$layout['ury']."' -s " . $nPagina;

        exec($cmd, $output, $estado);        

        if ($estado == 0) //firma ok
        {
            $aSalida = array();
            if (!file_exists($sPathArchivoFirmado))
            {
                $comentario = "No se encuentra el archivo firmado - " . $nNombreArchivoFirmado;
                $aSalida = array("status"=>"400", "error"=>$comentario);
            }
            else
            {
                $aSalida = array("files"=>array(array("content"=>"", "status"=>"OK")), "metadata"=>array("filesSigned"=>1));
            }
        }
        else
        {
            $aSalida = array("status"=>"400", "error"=>$output);
        }

        return $aSalida;
    }

    public function firmaAnexos($id_documento, $datosBitacora, $sNombre, $img_firma, $datosFirma, $sNombreImgAnexo, $layoutAnexo, $nRutFirma)
    {
        
        //solo primera y segunda firma - para firmar anexo
        if(count($datosBitacora) == 0 || count($datosBitacora) == 1)    
        {                               
            //firma de anexo   
            if (count($datosBitacora) == 0)
            {
                $aDocumentoBuzonAnexo = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon_archivo.id_documento_buzon','=','documento_buzon.id_documento_buzon')
                                            ->join('documento', 'documento_buzon.id_documento','=','documento.id_documento')
                                            ->where('documento.id_documento', '=', $id_documento)
                                            ->where('id_tipo_archivo','=', '2')                                                        
                                            ->whereIn('firma_anexo',array(1,2))
                                            ->whereNull('estado_firma_anexo')
                                            ->select('id_documento_buzon_archivo', 'nombre_archivo_codificado','id_tipo_archivo')
                                            ->get();

                $iEstadoFirma = 1;                            
            }

            if (count($datosBitacora) == 1)   
            {
                $aDocumentoBuzonAnexo = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon_archivo.id_documento_buzon','=','documento_buzon.id_documento_buzon')
                ->join('documento', 'documento_buzon.id_documento','=','documento.id_documento')
                ->where('documento.id_documento', '=', $id_documento)
                ->where('id_tipo_archivo','=', '2')                                                        
                ->where('firma_anexo','=', '2')
                ->where('estado_firma_anexo','=', '1')
                ->select('id_documento_buzon_archivo', 'nombre_archivo_codificado','id_tipo_archivo')
                ->get();

                $iEstadoFirma = 2; 
            }
            
            if (count($aDocumentoBuzonAnexo) > 0)
            {                               
                //genera imagen para firma anexo                                   
                
                $imgAnexo = Image::make(storage_path('app/public/files/imagen_firma/'.$img_firma));
                $dFechaCreacionImg = date('d.m.Y H:i:s');
                $imgAnexo->text('Firmado electrónicamente por:', 330, 60, function ($font) { $font->file(storage_path('../public/calibri.ttf')); $font->size(34); }); 
                $imgAnexo->text(Str::upper($sNombre), 330, 110, function ($font) { $font->file(storage_path('../public/calibrib.ttf')); $font->size(34); }); 

                $string = wordwrap($datosFirma['cargo_firma'], 35) . ' ' . $datosFirma['sigla'];
                $imgAnexo->text($string, 330, 235, function ($font) { $font->file(storage_path('../public/calibri.ttf')); $font->size(34); });         
        
                $imgAnexo->save(storage_path('app/public/files/imagen_firma/'.$sNombreImgAnexo)); 
                
                $path_imagen_firma_anexo = storage_path('app/public/files/imagen_firma/'.$sNombreImgAnexo);

                if ( !file_exists($path_imagen_firma_anexo) )
                {
                    $comentario = "Existe un problema con la imagen para anexo relacionada a la firma electrónica.";
                    $aSalida = array("status"=>"400", "error"=>$comentario);

                    return $aSalida;
                }

                foreach ($aDocumentoBuzonAnexo as $archAnexo)
                {                         
                    $nNombreArchivoCargarAnexo = "a_" . $archAnexo->nombre_archivo_codificado;

                    $sArchivoAnexo = storage_path('app/public/files/'.$archAnexo->nombre_archivo_codificado);
                    
                    //lamada por hash a anexos
                    //Obtiene pagina para agregar firma //DF6-80-20230426-45935741.pdf
                    $pdfPagesAnexo = file_get_contents($sArchivoAnexo);
                    $countAnexo = 0;
                    $countAnexo = preg_match_all("/\/Page\W/", $pdfPagesAnexo, $dummy);

                    if ($iEstadoFirma == 2)
                        $countAnexo -= 1;

                    $nSalidaHashAnexo = $this->callHash($sArchivoAnexo, $layoutAnexo, $countAnexo, $nNombreArchivoCargarAnexo, $nRutFirma);

                    if (!file_exists(storage_path('app/public/files/'.$nNombreArchivoCargarAnexo)))
                    {
                        $comentario = "No se encuentra el archivo anexo firmado - " . $nNombreArchivoCargarAnexo;
                        $aSalida = array("status"=>"400", "error"=>$comentario);
                        
                        return $aSalida;
                    }

                    if (isset($nSalidaHashAnexo['status'])) 
                    {
                        $comentario = $nSalidaHashAnexo['error'];
                        
                        $aSalida = array("status"=>"400", "error"=>$comentario);
                        
                        return $aSalida;
                    }
                   

                    //actualiza estado de firma
                    $regFirma = $archAnexo->id_documento_buzon_archivo;
                    DocumentoBuzonArchivo::find($regFirma)->update(['estado_firma_anexo' => $iEstadoFirma, 'nombre_archivo_codificado' => $nNombreArchivoCargarAnexo]); 
 
                }

                //elimina imagen anexo de firma
                $this->deleteImg($sNombreImgAnexo);
            }
        }

        $aSalida = array("status"=>"200", "error"=>"");

        return $aSalida;

    }
}