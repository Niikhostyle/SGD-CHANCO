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
        
                $DatosFirma = Buzon::join('buzon_usuario', 'buzon_usuario.id_buzon','=','buzon.id_buzon')
                            ->join('tipo_firma', 'buzon_usuario.id_tipo_firma','=', 'tipo_firma.id_tipo_firma')
                            ->where('buzon.id_buzon','=', $datos['id_buzon'])
                            ->where('buzon_usuario.id_usuario','=', $datos['id_usuario'])
                            ->select('cargo_firma', 'tipo_firma.id_tipo_firma', 'sigla')
                            ->first();   
        
                if (!$DatosFirma['cargo_firma'])   
                    return $this->respondFail("No existe cargo asociado al buzón.");
        
                if ($aInfoUsuarios['img_firma'] == '' || $aInfoUsuarios['img_firma'] == null) 
                    return $this->respondFail("No existe imagen para firma asociada al usuario.");
                    
                //$img = Image::make(storage_path('../public/img/firma_base.png')); //debe ser la ing asociada al usuario rut+id.png
                $img = Image::make(storage_path('app/public/files/imagen_firma/'.$aInfoUsuarios['img_firma']));
                $dFechaCreacion = date('d.m.Y H:i:s');
                $img->text('Firmado electrónicamente por:', 330, 75, function ($font) { $font->file(storage_path('../public/calibri.ttf')); $font->size(40); }); 
                $img->text(Str::upper($sNombre), 330, 125, function ($font) { $font->file(storage_path('../public/calibrib.ttf')); $font->size(40); }); 
                $img->text('Fecha: '. $dFechaCreacion, 330, 175, function ($font) { $font->file(storage_path('../public/calibri.ttf')); $font->size(40); }); 
                $img->text($DatosFirma['cargo_firma'] . ' ' . $DatosFirma['sigla'], 330, 250, function ($font) { $font->file(storage_path('../public/calibri.ttf')); $font->size(40); });         
        
                $img->save(storage_path('app/public/files/imagen_firma/'.$sNombreImg));  

                //INICIO PROCESO FIRMA
                //$aInfoUsuarios = Users::where('id', $datos['id_usuario'])->first(['aplica_fea','run','nombres', 'primer_apellido', 'segundo_apellido']);
                $aInfoDocumento = Documento::where('id_documento', $datos['id_documento'])->first(['hash_validacion']);                

                if (!$aInfoUsuarios['aplica_fea'])
                    return $this->respondFail('Usuario no tiene permiso para realizar firma electrónica.');

                $aDocumentoBuzon = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon_archivo.id_documento_buzon','=','documento_buzon.id_documento_buzon')
                                                        ->join('documento', 'documento_buzon.id_documento','=','documento.id_documento')
                                                        ->where('documento_buzon.id_documento', '=', $datos['id_documento'])
                                                        ->where('id_tipo_archivo','=', '1')
                                                        ->where('version','=', '1')
                                                        ->where('nombre_archivo_codificado','!=', null)
                                                        ->select('nombre_archivo_codificado','paginas_archivo','id_tipo_archivo')
                                                        ->first();

                if($aDocumentoBuzon == null)
                    return $this->respondFail('No existe archivo para realizar firma electrónica.');

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
                $nRut = env('PLCSGD_RUT');//'18658044';//$aInfoUsuarios['run']
                $sPath = config('app.path_upload') . '/'; //storage_path('app/public/files/')
                $sArchivo = storage_path('app/public/files/'.$sNombreArchivo); //cambiar por linea sgte
                //$sArchivo = storage_path($sPath.$request['archivo']);                
                $id_documento_buzon = $datos['id_documento_buzon'];
                $imagen_firma = storage_path('app/public/files/imagen_firma/'.$sNombreImg); 

                if ( !file_exists($imagen_firma) )
                    return $this->respondFail('Existe un problema con la imagen relacionada a la firma electrónica.');

                $datosBitacora = DocumentoBuzonBitacora::join('documento_buzon', 'documento_buzon_bitacora.id_documento_buzon','=','documento_buzon.id_documento_buzon')
                ->where('documento_buzon.id_documento', $datos['id_documento'])
                ->where('documento_buzon_bitacora.id_accion', 7)
                ->get();

                if (count($datosBitacora) > 3)
                    return $this->respondFail('Excede el máximo de firmas electróncas posibles.');

                if (count($datosBitacora) == 0) //firma 1
                {
                    $n_llx = 300;
                    $n_lly = 180;
                    $n_urx = 550;
                    $n_ury = 250;
                }

                if (count($datosBitacora) == 1) //firma 2
                {
                    $n_llx = 40;
                    $n_lly = 180;
                    $n_urx = 280;
                    $n_ury = 250;
                }
                if (count($datosBitacora) == 2) //firma 3
                {
                    $n_llx = 300;
                    $n_lly = 90;
                    $n_urx = 550;
                    $n_ury = 160;
                }

                if (count($datosBitacora) == 3) //firma 4
                {
                    $n_llx = 40;
                    $n_lly = 90;
                    $n_urx = 280;
                    $n_ury = 160;
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

                $dFechaCreacion = date('Y-m-d H:i:s');

                $nNombreArchivoCargar = $this->getNombreDocumento($datos['id_documento']); 
                
                //agrega Hash de validación
                if (count($datosBitacora) == 0)
                {                              
                    $pdf->AliasNbPages();
                    $pdf->footer_txt = "Para verificar este documento, use el siguiente identificador: " . $aInfoDocumento['hash_validacion'];
                    $pdf->footer_link = "http://sgd.padrelascasas.cl/verificador";
                    $pdf->PageFirma = $nPaginasPdf;

                    for ($i=1; $i <= $pageCount; $i++) { 
                        //import a page then get the id and will be used in the template
                        $tplId = $pdf->importPage($i);

                        $size = $pdf->getTemplateSize($tplId);
                        $pdf->AddPage($size['orientation'], array($size['width'], $size['height']));

                        //use the template of the imporated page
                        $pdf->useTemplate($tplId);                    
                    }
                    
                    $pdf->Output($sArchivo, 'F');   
                }

                $aRespuestaFirma = $classFirma->setRUN($nRut)                        
                                              ->addPDF($sArchivo, $sDescipcion, $layout)
                                              ->sign();                

                if (isset($aRespuestaFirma['status'])) 
                {
                    return $this->respondFail("Error al generar Firma electónica: " . $aRespuestaFirma['error']);
                }
                
                if ($aRespuestaFirma['metadata']['filesSigned'] == 1 )
                {
                    $responseFile = $aRespuestaFirma['files'][0];            
                    if($responseFile['status'] == 'OK') 
                    {
                        $encondedFile = $responseFile['content'];  
                        
                        $decodedFile = base64_decode($encondedFile, true);
                        if (empty($encondedFile) || ! base64_encode($decodedFile) === $encondedFile) {
                            return $this->respondFail("Error de codificación en archivo firmado.");
                        }                

                        $pdf = fopen (storage_path('app/public/files/'.$nNombreArchivoCargar),'w+');
                        if (!$pdf)
                            return $this->respondFail("Error al generar firma electónica, no se pudo crear archivo firmado. ");
                        
                        fwrite ($pdf, $decodedFile);
                        fclose ($pdf);
                        
                        if (!file_exists(storage_path('app/public/files/'.$nNombreArchivoCargar)))
                        {
                            return $this->respondFail("Error al generar Firma electónica, no se encuentra el archivo firmado. ");
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

                            //registrar accion en bitacora
                            $documentoBuzonBitacora = DocumentoBuzonBitacora::create([
                                'id_documento_buzon' => $id_documento_buzon,
                                'id_accion' => 7,
                                'fecha' => $dFechaCreacion,
                                'id_usuario' => $datos['id_usuario']
                            ]);   
                        }               
                            
                    }

                    //elimina imagen de firma
                    unlink(storage_path('app/public/files/imagen_firma/'.$sNombreImg));
                    
                }

                DB::commit();
                    
                return $this->respondSuccess("Archivo firmado almacenado exitosamente.", 200);

            } catch (Exception $e) {

                DB::rollBack();

                return $this->respondFail("Error al procesar el documento: " . $e->getMessage());
            }
        }
        else
            return $this->respondError('Json inválido', 406);    
                

    }


}
