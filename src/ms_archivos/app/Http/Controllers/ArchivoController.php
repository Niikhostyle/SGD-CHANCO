<?php
namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use App\Models\Documento;
use App\Models\Users;
use App\Models\DocumentoBuzon;
use App\Models\DocumentoBuzonArchivo;
use App\Models\DocumentoBuzonBitacora;
use Illuminate\Support\Facades\DB;
use App\Validator\DocumentoValidator;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;

use Barryvdh\DomPDF\Options;

class ArchivoController extends Controller{


    private $validator;

    public function __construct(DocumentoValidator $documentoValidator)
    {
        $this->validator = $documentoValidator;
    }

    public function generar_archivo_pdf(Request $request)
    { 
        try 
            {
                DB::beginTransaction();

                $datosRequest = $request->json()->all();     

                $nDocumento = $datosRequest['id_documento'];
                $idDocumentoBuzon = $datosRequest['id_documento_buzon'];

                $nNombreArchivoCargar = $this->getNombreDocumento($nDocumento);
        
                $aInfoUsuarios = Users::where('id', $datosRequest['id_usuario'])->first(['genera_pdf']);

                if (!$aInfoUsuarios['genera_pdf'])
                    return $this->respondError('Usuario no tiene permiso para generar pdf.', 400);
                    
                //verifica que no se genere 2 veces
                //busca en bitacora
                $existePdfGenerado = DocumentoBuzonBitacora::where('id_accion', 8)
                                                           ->where('id_documento_buzon', $idDocumentoBuzon)
                                                           ->first();

                //busca en archivo_existente
                $existeDocPpal = DocumentoBuzonArchivo::where('id_documento_buzon', $idDocumentoBuzon)
                                                        ->where('id_tipo_archivo', 1)
                                                        ->where('version', 1)
                                                        ->first();

                if (isset($existePdfGenerado['id_documento_buzon']) && isset($existeDocPpal['id_documento_buzon']))                                    
                    return $this->respondError('El archivo PDF ya fue generado.', 400);
                
                $datosDocumentos = Documento::findOrFail($nDocumento); 
                
                //OBTENCION DE FOLIO
                $idTipoDocumento = $datosDocumentos->id_tipo_documento;
                $datosJsonTipoDocumento = json_decode($datosDocumentos['json_tipo_documento'],true);
                $idTipoAsigFolio = $datosJsonTipoDocumento['id_tipo_asignacion_folio'];
                $idTipoFolio = $datosJsonTipoDocumento['id_tipo_folio'];
                $idTipoFlujo = $datosJsonTipoDocumento['id_tipo_flujo'];
                $nFolio = $datosDocumentos['folio'];
                $tPlantillaDistribucion = "";

                if (isset($datosJsonTipoDocumento['numero_firmas']))
                    $nNroFirmas = $datosJsonTipoDocumento['numero_firmas'];
                else 
                    $nNroFirmas = 4;  

                if (isset($datosDocumentos['distribucion']))
                    $tPlantillaDistribucion = $datosDocumentos['distribucion'];

                //agregar espacio para firmas al contenido del documento

                $aFirmaPosicion = array(
                    '1' => 85,  //165, 
                    '2' => 85,  //165, 
                    '3' => 185, //265,
                    '4' => 185, //265,
                    '5' => 285, //365, 
                    '6' => 285, //365
                );  
                
                $nAltoFirmas = $aFirmaPosicion[$nNroFirmas];

                /*
                if (isset($datosJsonTipoDocumento['plantilla_distribucion']))
                $tPlantillaDistribucion = $datosJsonTipoDocumento['plantilla_distribucion'];
                */

                //si existe folio, saltar proceso de obtención de folio
                if($nFolio==null){
                    
                    if ($idTipoAsigFolio == 2 && $idTipoFlujo == 1) //se aplica a flujo libre y tipo asig en recepción
                    {                                           
                        $anio = date('Y');
                        $fecha = new \DateTime('now');

                        $nFolio = Http::withHeaders(['key'=>$request->header('key'),'Content-Type'=>'application/json']) 
                        ->timeout(30)
                        ->withBody(json_encode([
                            'id_tipo_documento' => $idTipoDocumento,
                            'anio' => $anio ,
                            'id_buzon' => $datosRequest['id_buzon'],
                            'id_tipo_folio' => $idTipoFolio
                        ]), 'json')
                        ->get('http://sgd_ms_folios:3333/api/sgd-folios/asignaFolio');

                        if (isset($nFolio))
                        {
                            Documento::find($datosRequest["id_documento"])->update(['folio' => $nFolio]); 
                            Documento::find($datosRequest["id_documento"])->update(['fecha' => $fecha->format('Y-m-d H:i:s')]);    
                            
                            //registrar accion de asignacion de folio en bitacora
                            $documentoBuzonBitacoraFolio = DocumentoBuzonBitacora::create([
                                'id_documento_buzon' => $idDocumentoBuzon,
                                'id_accion' => 9,
                                'fecha' => $fecha,
                                'id_usuario' => $datosRequest['id_usuario']
                            ]);
                        }
                        else
                        {
                            return $this->respondError('No fue posible generar el folio.', 400);
                        }  
                
                    }
                    else $fecha = date_create_from_format('Y-m-d H:i:s',$datosDocumentos['fecha']);
                }else{
                    $fecha = date_create_from_format('Y-m-d H:i:s',$datosDocumentos['fecha']);
                }
                
                //reemplazar valores en encabezado
                //Nº {t_folio} {t_anio} {t_fecha}

                $aMeses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");                
 
                //unificacion para set encabezado de fecha cuando viene fecha seteada o cuando es actual    
                $sfecha = $fecha->format('d')." de ".$aMeses[$fecha->format('n')-1]. " del ".$fecha->format('Y');                

                $sEncabezado = $datosDocumentos['encabezado'];
                $sEncabezado = str_replace('{t_folio}', $nFolio, $sEncabezado);
                $sEncabezado = str_replace('{t_anio}', date('Y'), $sEncabezado);
                $sEncabezado = str_replace('{t_fecha}', $sfecha, $sEncabezado);                
               
                $datosDocumentosCuerpo = str_replace(env('APP_URL'), storage_path('app/public'), $datosDocumentos['cuerpo']);
                $datosDocumentosencabezado = str_replace(env('APP_URL'), storage_path('app/public'), $sEncabezado);
                $datosDocumentosDistribucion = str_replace(env('APP_URL'), storage_path('app/public'), $tPlantillaDistribucion);


                $numLineasDistribucion = substr_count($tPlantillaDistribucion, "\n");

                $dataPdf = array('materia'=>$datosDocumentos['materia'], 'encabezado'=>$datosDocumentosencabezado, 'cuerpo'=>$datosDocumentosCuerpo, 'distribucion'=> $datosDocumentosDistribucion, 'altoFirmas'=>$nAltoFirmas);         
                
                //$pdf = app('dompdf.wrapper');
                //$pdf->getDomPDF()->set_option("enable_php", true);

                PDF::loadView('pdf', $dataPdf)->setPaper('legal', 'portrait')->save(storage_path('app/public/files/') . $nNombreArchivoCargar);  
                //return PDF::loadView('pdf', $dataPdf)->setPaper('legal', 'portrait')->stream(storage_path('app/public/files/') . $nNombreArchivoCargar);            
                
                //PDF::loadView('pdf', $dataPdf)->setPaper('legal', 'portrait');
                //$pdf->setOptions(['isHtml5ParserEnabled' => true]);
                
                //$pdf->setOption('footer-html', $tPlantillaDistribucion);
                //$pdf->output();

                //$pageCount = $pdf->getDomPDF()->get_canvas()->get_page_count();
        
                //$dom_pdf = $pdf->getDomPDF();
                //$canvas = $dom_pdf->get_canvas();

                //$canvas->page_text(520, 10, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));

     
                //return PDF::loadView('pdf', $dataPdf)->setPaper('legal', 'portrait')->stream(storage_path('app/public/files/') . $nNombreArchivoCargar);           
 
               
                //ver cuantas paginas tiene para poner firma
                //Obtiene pagina para agregar firma
                $pdfPages = file_get_contents(storage_path('app/public/files/') . $nNombreArchivoCargar);
                $count = 0;
                $count = preg_match_all("/\/Page\W/", $pdfPages, $dummy);
                
                Documento::find($nDocumento)->update(['paginas_archivo' => $count, 'archivo_existente' => true]);

                //se comenta merge por problemas de peso al generar firma
                //23-05-2022
                /*
                $oMerger = PDFMerger::init();
                $oMerger->addPDF(storage_path('app/public/files/') . $nNombreArchivoCargar);

                $anexos = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon.id_documento_buzon', '=', 'documento_buzon_archivo.id_documento_buzon')
                                                ->where('id_documento', $nDocumento)
                                                ->where('id_tipo_archivo', 2)
                                                ->select('nombre_archivo_codificado')
                                                ->get();      

                foreach ($anexos as $file)
                    $oMerger->addPDF(storage_path('app/public/files/') . $file['nombre_archivo_codificado']);
   
                $oMerger->merge();
                $oMerger->save(storage_path('app/public/files/') . $nNombreArchivoCargar);
                */
                $dFechaCreacion = date('Y-m-d H:i:s');                  

                if (file_exists(storage_path('app/public/files/') . $nNombreArchivoCargar))
                {                                
                    $docsPpales = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon_archivo.id_documento_buzon','=','documento_buzon.id_documento_buzon')
                                                        ->where('id_documento', $nDocumento)
                                                        ->where('id_tipo_archivo', 1)
                                                        ->get();                                                        
                
                    foreach ($docsPpales as $archFile)
                    {                        
                        $nSalida = $archFile->version + 1;
                        DocumentoBuzonArchivo::find($archFile->id_documento_buzon_archivo)->update(['version' => $nSalida]);
                    }
                    
                    DocumentoBuzonArchivo::create([
                        'id_documento_buzon' => $idDocumentoBuzon,
                        'id_tipo_archivo' => 1,
                        'nombre_archivo_original' => $nNombreArchivoCargar,
                        'nombre_archivo_codificado' => $nNombreArchivoCargar,
                        'version' => 1,
                        'fecha' => $dFechaCreacion
                    ]);

                    //registrar accion en bitacora

                    $documentoBuzonBitacora = DocumentoBuzonBitacora::create([
                        'id_documento_buzon' => $idDocumentoBuzon,
                        'id_accion' => 8,
                        'fecha' => $dFechaCreacion,
                        'id_usuario' => $datosRequest['id_usuario']
                    ]);            
                    
                    //registrar accion de cambio de archivo ppal en bitacora
                    $documentoBuzonBitacora = DocumentoBuzonBitacora::create([
                        'id_documento_buzon' => $idDocumentoBuzon,
                        'id_accion' => 5,
                        'fecha' => $dFechaCreacion,
                        'id_usuario' => $datosRequest['id_usuario'],
                        'mensaje_respuesta' => "Cambio en archivo principal por generación de pdf."
                    ]);  

                }
                else
                {
                    return $this->respondError('No se encuentra el archivo generado. ', 400);
                }
                
                $datosJsonTipoDocumento = json_decode($datosDocumentos['json_tipo_documento'],true);
                $datosJsonTipoDocumento['id_tipo_origen'] = 2;
                $datosDocumentos->update(['json_tipo_documento' => $datosJsonTipoDocumento]);  
               
                DB::commit();

                return $this->respondSuccess("Archivo pdf generado correctamente.", 200);

            }
            catch (ModelNotFoundException $e) {
                DB::rollBack();

                return response()->json([
                    'status' => 500, 
                    'data' => [
                        'comentario' => 'Error al generar documento PDF.'
                ]], 500);
            }      
    }
 
    public function generar_vista_previa(Request $request)
    { 
        try 
            {
                $datosRequest = $request->json()->all(); 

                $nDocumento = $datosRequest['id_documento'];
                $idDocumentoBuzon = $datosRequest['id_documento_buzon'];

                $nNombreArchivoCargar = $this->getNombreDocumento($nDocumento);
        
                $aInfoUsuarios = Users::where('id', $datosRequest['id_usuario'])->first(['genera_pdf']);
                    
                $datosDocumentos = Documento::findOrFail($nDocumento); 
                $aMeses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

                //OBTENCION DE FOLIO
                // si existe folio (por alguna razon, no volver a foliar)
                if($datosDocumentos->folio==null){
                    $idTipoDocumento = $datosDocumentos->id_tipo_documento;
                    $datosJsonTipoDocumento = json_decode($datosDocumentos['json_tipo_documento'],true);
                    $idTipoAsigFolio = $datosJsonTipoDocumento['id_tipo_asignacion_folio'];
                    $idTipoFolio = $datosJsonTipoDocumento['id_tipo_folio'];
                    $idTipoFlujo = $datosJsonTipoDocumento['id_tipo_flujo'];
                    $nFolio = $datosDocumentos['folio'];
                    $sfecha = date('d')." de ".$aMeses[date('n')-1]. " del ".date('Y');
                }else{
                    //$sfecha = $datosDocumentos->fecha;
                    $date = strtotime($datosDocumentos->fecha);
                    $sfecha = date('d',$date)." de ".$aMeses[date('n',$date)-1]. " del ".date('Y',$date);
                    $nFolio = $datosDocumentos->folio;
                }
                
                if (isset($nFolio) == null || isset($nFolio) == '')
                    $nFolio = '000';

                //reemplazar valores en encabezado
                //Nº {t_folio} {t_anio} {t_fecha}

                               
                               

                $sEncabezado = $datosDocumentos['encabezado'];
                $sEncabezado = str_replace('{t_folio}', $nFolio, $sEncabezado);//$datosDocumentos['folio']
                $sEncabezado = str_replace('{t_anio}', date('Y'), $sEncabezado);
                $sEncabezado = str_replace('{t_fecha}', $sfecha, $sEncabezado);

                //reemplazar path imagenes antes de generar pdf
                //ej: src="http://192.168.1.101:82/files/editor/images/historia.jpg" por src="/src/storage/app/public/files/editor/images/historia.jpg" 
                
                $datosDocumentosCuerpo = str_replace(env('APP_URL'), storage_path('app/public'), $datosDocumentos['cuerpo']);
                $datosDocumentosencabezado = str_replace(env('APP_URL'), storage_path('app/public'), $sEncabezado);

               // dd($datosDocumentosencabezado);
                
                $dataPdf = array('materia'=>$datosDocumentos['materia'], 'encabezado'=>$sEncabezado, 'cuerpo'=>$datosDocumentosCuerpo  );//  $datosDocumentos['cuerpo']            

                $pdf = PDF::loadView('pdf', $dataPdf)->setPaper('legal', 'portrait');
                
                return $pdf->download('vista_previa.pdf');
            
            }
            catch (ModelNotFoundException $e) {

                return response()->json([
                    'status' => 500, 
                    'data' => [
                        'comentario' => 'Error al generar vista previa.'
                ]], 500);
            }      
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
