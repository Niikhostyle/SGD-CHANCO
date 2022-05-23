<?php

namespace App\Http\Controllers;

use Exception;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                $aInfoUsuarios = Users::where('id', $datos['id_usuario'])->first(['run','nombres', 'primer_apellido','segundo_apellido','img_firma','aplica_fea']);
                $sNombre = $aInfoUsuarios['nombres'] . ' ' . $aInfoUsuarios['primer_apellido'] . ' ' . $aInfoUsuarios['segundo_apellido'];
                $sNombreImg = $aInfoUsuarios['run'] . date('dmYHis') . '.png';
                $dFechaCreacion = date('Y-m-d H:i:s');
        
                $DatosFirma = Buzon::join('buzon_usuario', 'buzon_usuario.id_buzon','=','buzon.id_buzon')
                            ->join('tipo_firma', 'buzon_usuario.id_tipo_firma','=', 'tipo_firma.id_tipo_firma')
                            ->where('buzon.id_buzon','=', $datos['id_buzon'])
                            ->where('buzon_usuario.id_usuario','=', $datos['id_usuario'])
                            ->select('cargo_firma', 'tipo_firma.id_tipo_firma', 'sigla')
                            ->first();

                if (!isset($DatosFirma['cargo_firma']))  
                { 
                    $comentario = "No existe cargo asociado al buzón.";
                    throw new Exception($comentario);
                    //return $this->respondFail($comentario);
                }

                if ($aInfoUsuarios['img_firma'] == '' || $aInfoUsuarios['img_firma'] == null) 
                {
                    $comentario = "No existe imagen para firma asociada al usuario.";
                    throw new Exception($comentario);
                   //return $this->respondFail($comentario);
                }   

                //$img = Image::make(storage_path('../public/img/firma_base.png')); //debe ser la ing asociada al usuario rut+id.png
                $img = Image::make(storage_path('app/public/files/imagen_firma/'.$aInfoUsuarios['img_firma']));
                $dFechaCreacionImg = date('d.m.Y H:i:s');
                $img->text('Firmado electrónicamente por:', 330, 60, function ($font) { $font->file(storage_path('../public/calibri.ttf')); $font->size(34); }); 
                $img->text(Str::upper($sNombre), 330, 110, function ($font) { $font->file(storage_path('../public/calibrib.ttf')); $font->size(34); }); 
                $img->text('Fecha: '. $dFechaCreacionImg, 330, 160, function ($font) { $font->file(storage_path('../public/calibri.ttf')); $font->size(34); }); 

                $string = wordwrap($DatosFirma['cargo_firma'], 35) . ' ' . $DatosFirma['sigla'];
                $img->text($string, 330, 235, function ($font) { $font->file(storage_path('../public/calibri.ttf')); $font->size(34); });         
        
                $img->save(storage_path('app/public/files/imagen_firma/'.$sNombreImg));  

                //INICIO PROCESO FIRMA
                $aInfoDocumento = Documento::where('id_documento', $datos['id_documento'])->first(['hash_validacion', 'identificador']);                

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
                                                        ->select('nombre_archivo_codificado','paginas_archivo','id_tipo_archivo')
                                                        ->first();
                                      
                if(!isset($aDocumentoBuzon['nombre_archivo_codificado']))
                {
                    $comentario = "No existe archivo para realizar firma electrónica.";
                    throw new Exception($comentario);
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
                $sDescipcion = "Firmado electrónicamente por " . $aInfoUsuarios['nombres'] . ' ' . $aInfoUsuarios['primer_apellido'] . ' ' . $aInfoUsuarios['segundo_apellido'];
                $nRut = explode("-",$aInfoUsuarios['run']);//'18658044';//env('PLCSGD_RUT')
                $nRutFirma = $nRut[0];
                $sPath = config('app.path_upload') . '/'; //storage_path('app/public/files/')
                $sArchivo = storage_path('app/public/files/'.$sNombreArchivo); //cambiar por linea sgte
                //$sArchivo = storage_path($sPath.$request['archivo']);                
                $id_documento_buzon = $datos['id_documento_buzon'];
                $imagen_firma = storage_path('app/public/files/imagen_firma/'.$sNombreImg); 

                if ( !file_exists($imagen_firma) )
                {
                    $comentario = "Existe un problema con la imagen relacionada a la firma electrónica.";
                    throw new Exception($comentario);
                    //return $this->respondFail($comentario);
                }

                $datosBitacora = DocumentoBuzonBitacora::join('documento_buzon', 'documento_buzon_bitacora.id_documento_buzon','=','documento_buzon.id_documento_buzon')
                ->where('documento_buzon.id_documento', $datos['id_documento'])
                ->where('documento_buzon_bitacora.id_accion', 7)
                //->whereIn('documento_buzon.id_estado_documento',array(9,10))
                ->get();
                
                if (count($datosBitacora) > 3)
                {
                    $comentario = "Excede el máximo de firmas electróncas posibles.";
                    throw new Exception($comentario);
                    //return $this->respondFail($comentario);
                }

                if (count($datosBitacora) == 0) //firma 1
                {//270 - 90 : 90% | 255 - 85 : 85%
                    $n_llx = 300;
                    $n_lly = 180;
                    $n_urx = 555;
                    $n_ury = 265;
                }

                if (count($datosBitacora) == 1) //firma 2
                {
                    $n_llx = 30;
                    $n_lly = 180;
                    $n_urx = 285;
                    $n_ury = 265;
                }
                if (count($datosBitacora) == 2) //firma 3
                {
                    $n_llx = 300;
                    $n_lly = 80;
                    $n_urx = 555;
                    $n_ury = 165;
                }

                if (count($datosBitacora) == 3) //firma 4
                {
                    $n_llx = 30;
                    $n_lly = 80;
                    $n_urx = 285;
                    $n_ury = 165;
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

                $nNombreArchivoCargar = $this->getNombreDocumento($datos['id_documento']); 
                
                //agrega Hash de validación
                if (count($datosBitacora) == 0)
                {                              
                    $pdf->AliasNbPages();
                    $pdf->footer_txt = "Para verificar este documento, use el siguiente identificador: " . $aInfoDocumento['hash_validacion'];
                    $pdf->footer_id_txt = "ID: " . $aInfoDocumento['identificador'] . " | ";
                    $pdf->footer_link = env('PLCSGD_LINKVALIDADOR');
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
                
                $aRespuestaFirma = $classFirma->setRUN($nRutFirma)                        
                                              ->addPDF($sArchivo, $sDescipcion, $layout)
                                              ->sign();   
                
                if (isset($aRespuestaFirma['status'])) 
                {
                    $comentario = $aRespuestaFirma['error'];
                    throw new Exception($comentario);
                    //return $this->respondFail($comentario);
                }

                if (isset($aRespuestaFirma['metadata']))
                {
                    if ($aRespuestaFirma['metadata']['filesSigned'] == 1 )
                    {
                        $responseFile = $aRespuestaFirma['files'][0];            
                        if($responseFile['status'] == 'OK') 
                        {
                            $encondedFile = $responseFile['content'];  
                            
                            $decodedFile = base64_decode($encondedFile, true);
                            if (empty($encondedFile) || !base64_encode($decodedFile) === $encondedFile) {
                                $comentario = "Error de codificación en archivo firmado.";
                                throw new Exception($comentario);
                                //return $this->respondFail("Error de codificación en archivo firmado.");
                            }                

                            $pdf = fopen (storage_path('app/public/files/'.$nNombreArchivoCargar),'w+');
                            if (!$pdf)
                            {
                                $comentario = "No se pudo crear archivo firmado. ";
                                throw new Exception($comentario);
                                //return $this->respondFail("Error al generar firma electónica, no se pudo crear archivo firmado. ");
                            }

                            fwrite ($pdf, $decodedFile);
                            fclose ($pdf);
                            
                            if (!file_exists(storage_path('app/public/files/'.$nNombreArchivoCargar)))
                            {
                                $comentario = "No se encuentra el archivo firmado. ";
                                throw new Exception($comentario);
                                //return $this->respondFail("Error al generar Firma electónica, no se encuentra el archivo firmado. ");
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
                            
                            DB::commit();                    
                            return $this->respondSuccess("Archivo firmado almacenado exitosamente.", 200);
                                
                        }

                        //elimina imagen de firma
                        $this->deleteImg($sNombreImg);                          
                    }
                    else
                    {
                        $comentario = "No se pudo procesar la respuesta.";
                        throw new Exception($comentario);
                    }
                }

                $comentario = "Tamaño del archivo supera los 4MB.";
                throw new Exception($comentario);                
                

            } catch (Exception $e) {

                DB::rollBack();
                
                $msgError = "Error al generar la Firma Electónica:" . $e->getMessage();
                $this->saveBitacora($datos['id_documento_buzon'], $dFechaCreacion, $datos['id_usuario'],$msgError,13);
                $this->deleteImg($sNombreImg);

                Log::error("Error al generar la Firma Electónica: " . $e->getMessage()); 

                return $this->respondFail("Error al generar la Firma Electónica: " . $e->getMessage());
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
}
