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
use App\Models\DocumentoBuzon;
use App\Models\DocumentoBuzonBitacora;
use App\Models\DocumentoBuzonArchivo;
use FPDF;
use setasign\Fpdi\Fpdi;

//require_once('../../Libraries/fpdf.php');

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
                
                $aInfoUsuarios = Users::where('id', $datos['id_usuario'])->first(['aplica_fea','run','nombres', 'primer_apellido', 'segundo_apellido']);
                
                if (!$aInfoUsuarios['aplica_fea'])
                    return $this->respondFail('Usuario no tiene permiso para realizar firma electrónica.');

                $aDocumentoBuzon = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon_archivo.id_documento_buzon','=','documento_buzon.id_documento_buzon')
                                                        ->where('id_documento', '=', $datos['id_documento'])
                                                        ->where('id_tipo_archivo','=', '1')
                                                        ->where('version','=', '1')
                                                        ->where('nombre_archivo_codificado','!=', null)
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
                $sDescipcion = "Firmado electrónicamente por " . $aInfoUsuarios['nombres'] . ' ' . $aInfoUsuarios['primer_apellido'] . ' ' . $aInfoUsuarios['segundo_apellido'];//sacar de tabla documentos
                $nRut = '22222222';//'18658044';//$aInfoUsuarios['run']
                $sPath = config('app.path_upload') . '/'; //storage_path('app/public/files/')
                $sArchivo = storage_path('app/public/files/'.$sNombreArchivo); //cambiar por linea sgte
                //$sArchivo = storage_path($sPath.$request['archivo']);                
                $id_documento_buzon = $datos['id_documento_buzon'];
                 $imagen_firma = storage_path('app/public/files/'.$datos['img_firma']); 

                //Obtiene pagina para agregar firma
                $pdfPages = file_get_contents($sArchivo);
                $count = 0;
                $regex  = "/\/Count\s+(\d+)/";
/*
                if(preg_match_all($regex, $pdfPages, $matches))
                    $count = max($matches);

                    //$pdf = new FPDI();
                    // get the page count
                    //$pageCount = $pdf->setSourceFile($sArchivo); //cantidad de paginas
                    //$templateId = $pdf->importPage($pageCount); 

                    $fpdi = new FPDI;
                    
                    $count = $fpdi->setSourceFile($sArchivo);
                    for ($i=1; $i<=$count; $i++) 
                    {
                        $template = $fpdi->importPage($i);
                        $size = $fpdi->getTemplateSize($template);
                        $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
                        $fpdi->useTemplate($template);
                        $left = 10;
                        $top =  $size['height'] - 10;
                        $text = "<u>TIDE S.A.</u>";
                        $fpdi->SetFont("helvetica", "", 12);
                        //$fpdi->SetTextColor(153,0,153);
                        $fpdi->Text($left,$top,$text);
                        //$fpdi->writeHTML($text, true, false, true, false, '');
                    }
                    return $fpdi->Output($sArchivo, 'I');


                    
return $fpdi;
*/
                $datosBitacora = DocumentoBuzonBitacora::join('documento_buzon', 'documento_buzon_bitacora.id_documento_buzon','=','documento_buzon.id_documento_buzon')
                ->where('documento_buzon.id_documento', $datos['id_documento'])
                ->where('documento_buzon_bitacora.id_accion', 7)
                ->get();

                if (count($datosBitacora) == 0) //firma 1
                {
                    $n_llx = 300;
                    $n_lly = 50;
                    $n_urx = 550;
                    $n_ury = 120;
                }

                if (count($datosBitacora) > 0) //firma 2 - pendiente firma 3 y 4
                {
                    $n_llx = 40;
                    $n_lly = 50;
                    $n_urx = 280;
                    $n_ury = 120;
                }

                $layout = array(
                    'filename' => $imagen_firma,//storage_path('app/public/files/logo_firma2.png'),
                    'page'     => 'LAST',
                    'llx'      => $n_llx, //50
                    'lly'      => $n_lly, //50
                    'urx'      => $n_urx, //210
                    'ury'      => $n_ury  //100
                );

                $dFechaCreacion = date('Y-m-d H:i:s');

                $nNombreArchivoCargar = $this->getNombreDocumento($datos['id_documento']);
               
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
