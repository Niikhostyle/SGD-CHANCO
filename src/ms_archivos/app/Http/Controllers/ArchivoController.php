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
use App\Models\BloqueoFolio; 
use Barryvdh\DomPDF\Options;
use App\Models\TipoDocumentoBuzonFolio;
use Illuminate\Support\Facades\Log;


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
                    '1' => 115,//85,  //165, 
                    '2' => 115,//85,  //165, 
                    '3' => 245,//185, //265,
                    '4' => 245,//185, //265,
                    '5' => 378,//285, //365, 
                    '6' => 378,//285, //365
                );  
                
                $nAltoFirmas = $aFirmaPosicion[$nNroFirmas];                

                //si existe folio, saltar proceso de obtención de folio
                if($nFolio == null && $datosRequest['generaFolio'] == 1) 
                {                    
                    //if ($idTipoAsigFolio == 2 && $idTipoFlujo == 1) //se aplica a flujo libre y tipo asig en recepción
                    if (($idTipoAsigFolio == 2 || $idTipoAsigFolio == 4 || $idTipoAsigFolio == 5) && $idTipoFlujo == 1) //se aplica a flujo libre y tipo asig en recepción | primera firma 
                    {                                           
                        $anio = date('Y');
                        $fecha = new \DateTime('now');

                        //nueva forma de obtener folio 
                        $datosBloqueo = DB::table('bloqueo_folio') 
                                        ->where('tipo_folio', $idTipoFolio) 
                                        ->where('tipo_documento', $idTipoDocumento) 
                                        ->where('buzon', $datosRequest['id_buzon']) 
                                        ->where('estado', 1) 
                                        ->get(); 
 
                        $existeBloqueo = count($datosBloqueo); 
                         
                        if($existeBloqueo > 0){ 
                            return $this->respondError('No se puede obtener el folio, favor intente en unos minutos.', 400); 
                        } 

                        $nFolio = $this->obtenerFolio($request->header('key'), $anio, $idTipoDocumento, $idTipoFolio, $datosRequest['id_buzon']); 
                        //fin nueva forma de obtener folio 

                        if (isset($nFolio))
                        {
                            Documento::find($datosRequest["id_documento"])->update(['folio' => $nFolio]); 
                            Documento::find($datosRequest["id_documento"])->update(['fecha' => $fecha->format('Y-m-d H:i:s')]);    
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
                
                //reemplazar valores en encabezado solo si viene folio 
                //Nº {t_folio} {t_anio} {t_fecha}

                $aMeses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");                
 
                //unificacion para set encabezado de fecha cuando viene fecha seteada o cuando es actual    
                if ($datosRequest['generaFolio'] == 1) 
                    $sfecha = $fecha->format('d')." de ".$aMeses[$fecha->format('n')-1]. " del ".$fecha->format('Y');                

                $sEncabezado = $datosDocumentos['encabezado'];
                if ($datosRequest['generaFolio'] == 1) 
                {
                    $sEncabezado = str_replace('{t_folio}', $nFolio, $sEncabezado);
                    $sEncabezado = str_replace('{t_anio}', date('Y'), $sEncabezado);
                    $sEncabezado = str_replace('{t_fecha}', $sfecha, $sEncabezado);                        
                }            
               
                $datosDocumentosCuerpo = str_replace(env('APP_URL'), storage_path('app/public'), $datosDocumentos['cuerpo']);
                $datosDocumentosencabezado = str_replace(env('APP_URL'), storage_path('app/public'), $sEncabezado);
                $datosDocumentosDistribucion = str_replace(env('APP_URL'), storage_path('app/public'), $tPlantillaDistribucion);

                //espacio visadores 

                $altoTotal = 0; 
                $nEspacioVisadores = 20; 
    
                //si hay visadores se setea linea y se suma 20 al alto final     
                $sDatosVisadores = $this->obtenerVisadores($nDocumento, $idDocumentoBuzon); 
    
                if ($sDatosVisadores != "") 
                { 
                    $nEspacioVisadores = 40;  
                } 

                $numLineasDistribucion = substr_count($tPlantillaDistribucion, "\n");
                $nEspacioDistribucion = $numLineasDistribucion * 20; 
                
                $altoTotal = $nEspacioVisadores + $nEspacioDistribucion + $nAltoFirmas; 

                $dataPdf = array('materia'=>$datosDocumentos['materia'], 'encabezado'=>$datosDocumentosencabezado, 'cuerpo'=>$datosDocumentosCuerpo, 'visadores' => $sDatosVisadores, 'distribucion'=> $datosDocumentosDistribucion, 'altoFirmas'=>$nAltoFirmas, 'altoDistribucion'=>$nEspacioDistribucion, 'altoTotal'=>$altoTotal);    
                PDF::loadView('pdf', $dataPdf)->setPaper('legal', 'portrait')->save(storage_path('app/public/files/') . $nNombreArchivoCargar);  
                //return PDF::loadView('pdf', $dataPdf)->setPaper('legal', 'portrait')->stream(storage_path('app/public/files/') . $nNombreArchivoCargar);  
               
                //ver cuantas paginas tiene para poner firma
                //Obtiene pagina para agregar firma
                $pdfPages = file_get_contents(storage_path('app/public/files/') . $nNombreArchivoCargar);
                $count = 0;
                $count = preg_match_all("/\/Page\W/", $pdfPages, $dummy);
                
                Documento::find($nDocumento)->update(['paginas_archivo' => $count, 'archivo_existente' => true]);

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

                    if ($datosRequest['generaFolio'] == 1) 
                    {
                        //registrar accion en bitacora asignacion folio
                        $documentoBuzonBitacoraFolio = DocumentoBuzonBitacora::create([
                            'id_documento_buzon' => $idDocumentoBuzon,
                            'id_accion' => 9,
                            'fecha' => $fecha,
                            'id_usuario' => $datosRequest['id_usuario']
                        ]);
                    }

                    //registrar accion en bitacora generar pdf
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
                    //reversa folio
                    Documento::find($datosRequest["id_documento"])->update(['folio' => null]); 
                    Documento::find($datosRequest["id_documento"])->update(['fecha' => null]);    
                            
                    //$folio,$estado,$tipo_folio,$buzon,$tipo_documento,$reversado
                    $this->estado_folio($nFolio,0,$idTipoFolio,$datosRequest['id_buzon'],$idTipoDocumento,1); 
                    //db::statement("update bloqueo_folio set estado = 0, reversado = 1 where folio = ".$nFolio." and tipo_folio = ".$idTipoFolio." and buzon = ".$datosRequest['id_buzon']." and tipo_documento = ".$idTipoDocumento); 


                    return $this->respondError("No se encuentra el archivo generado.", 400);
                }
                
                $datosJsonTipoDocumento = json_decode($datosDocumentos['json_tipo_documento'],true);
                $datosJsonTipoDocumento['id_tipo_origen'] = 2;
                $datosDocumentos->update(['json_tipo_documento' => $datosJsonTipoDocumento]);  
                
                //desbloquear folio 
                //db::statement("update bloqueo_folio set estado = 0 where folio = ".$aDocumentoBuzon['folio']." and tipo_folio = ".$datosJsonTipoDocumento['id_tipo_folio']); 
                if ($datosRequest['generaFolio'] == 1) 
                    $this->estado_folio($nFolio,0,$idTipoFolio,$datosRequest['id_buzon'],$idTipoDocumento,0); 

                DB::commit();

                return $this->respondSuccess("Archivo pdf generado correctamente.", 200);

            }
            catch (ModelNotFoundException $e) {
                
                db::statement("update bloqueo_folio set estado = 0, reversado = 1 where folio = ".$nFolio." and tipo_folio = ".$idTipoFolio." and buzon = ".$datosRequest['id_buzon']." and tipo_documento = ".$idTipoDocumento); 

                DB::rollBack();
                
                //$this->estado_folio($nFolio,0,$idTipoFolio,$datosRequest['id_buzon'],$idTipoDocumento,1); 
                return response()->json([
                    'status' => 500, 
                    'data' => [
                        'comentario' => 'Error al generar documento PDF.'
                ]], 500);
            }      
    }
    
    public function generar_folio(Request $request) 
    {    
        try  
            { 
                DB::beginTransaction(); 
 
                $datosRequest = $request->json()->all();      
 
                $nDocumento = $datosRequest['id_documento']; 
                $idDocumentoBuzon = $datosRequest['id_documento_buzon']; 
 
                $nNombreArchivo = ""; //nombre del archivo - obtener 
         
                $aInfoUsuarios = Users::where('id', $datosRequest['id_usuario'])->first(['genera_pdf']); 
 
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
                 
                //si existe folio, saltar proceso de obtención de folio 
                if(!isset($nFolio) && $datosRequest['generaFolio'] == 1)                 
                { 
                     
                    if (($idTipoAsigFolio == 5) && $idTipoFlujo == 1) //se aplica a flujo libre y ultima firma                   
                    {                                            
                        $anio = date('Y'); 
                        $fecha = new \DateTime('now'); 
 
                        //nueva forma de obtener folio 
                        $datosBloqueo = DB::table('bloqueo_folio') 
                                        ->where('tipo_folio', $idTipoFolio)   
                                        ->where('tipo_documento', $idTipoDocumento)   
                                        ->where('buzon', $datosRequest['id_buzon'])   
                                        ->where('estado', 1) 
                                        ->get(); 
 
                        $existeBloqueo = count($datosBloqueo); 
                         
                        if($existeBloqueo > 0){ 
                            return $this->respondError('No se puede obtener el folio, favor intente en unos minutos.', 400); 
                        } 
 
                        $nFolio = $this->obtenerFolio($request->header('key'), $anio, $idTipoDocumento, $idTipoFolio, $datosRequest['id_buzon']); 
                        //fin nueva forma de obtener folio 
                         
                        if (isset($nFolio)) 
                        { 
                            Documento::find($datosRequest["id_documento"])->update(['folio' => $nFolio]);  
                            Documento::find($datosRequest["id_documento"])->update(['fecha' => $fecha->format('Y-m-d H:i:s')]);     
                        } 
                        else 
                        { 
                            return $this->respondError('No fue posible generar el folio.', 400); 
                        }   
                 
                    } 
                    else  
                        $fecha = date_create_from_format('Y-m-d H:i:s',$datosDocumentos['fecha']); 
                } 
                else 
                { 
                    $fecha = date_create_from_format('Y-m-d H:i:s',$datosDocumentos['fecha']); 
                } 
 
                $dFechaCreacion = date('Y-m-d H:i:s');                   
 
                //registrar accion en bitacora asignacion folio 
                if ($datosRequest['generaFolio'] == 1) 
                { 
                    $documentoBuzonBitacoraFolio = DocumentoBuzonBitacora::create([ 
                        'id_documento_buzon' => $idDocumentoBuzon, 
                        'id_accion' => 9, 
                        'fecha' => $fecha, 
                        'id_usuario' => $datosRequest['id_usuario'] 
                    ]); 
                }  
                 
                $datosJsonTipoDocumento = json_decode($datosDocumentos['json_tipo_documento'],true); 
                $datosJsonTipoDocumento['id_tipo_origen'] = 2; 
                $datosDocumentos->update(['json_tipo_documento' => $datosJsonTipoDocumento]);   
                 
                //desbloquear folio  
                if ($datosRequest['generaFolio'] == 1) 
                    $this->estado_folio($nFolio,0,$idTipoFolio,$datosRequest['id_buzon'],$idTipoDocumento,0);  
 
                DB::commit(); 
 
                return $this->respondSuccess("Archivo pdf generado correctamente.", 200); 
 
            } 
            catch (ModelNotFoundException $e)  
            {                 
                db::statement("update bloqueo_folio set estado = 0, reversado = 1 where folio = ".$nFolio." and tipo_folio = ".$idTipoFolio." and buzon = ".$datosRequest['id_buzon']." and tipo_documento = ".$idTipoDocumento);  
 
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

    public function obtenerFolio($llave, $anio, $tipo_documento, $tipo_folio, $buzon = null)
    { 
        $nFolio = 0;
        //Por tipo de documento y año
        if($tipo_folio == 1)
        {
            $dFolio = DB::table('tipo_documento_buzon_folio')
                        ->where('id_tipo_documento',$tipo_documento)
                        ->where('anio',$anio)
                        ->select('valor')
                        ->get();
            
 
            if(count($dFolio) > 0){
                $nFolio = $dFolio[0]->valor;
            }

            $nFolio++;
            
            if($nFolio <= 1){
                DB::statement('insert into tipo_documento_buzon_folio (id_tipo_documento,anio,valor,created_at,updated_at) values ('.$tipo_documento.','.$anio.','.$nFolio.',now(),now())');
            }
            else{
                DB::statement('update tipo_documento_buzon_folio set valor = '.$nFolio.', updated_at = now() where id_tipo_documento= '.$tipo_documento.' and anio = '.$anio.' ');
            }
        }

        //Por tipo de documento, buzón y año
        if($tipo_folio == 2){
            $dFolio = DB::table('tipo_documento_buzon_folio')
                        ->where('id_tipo_documento',$tipo_documento)
                        ->where('id_buzon',$buzon)
                        ->where('anio',$anio)
                        ->get();

 
            if(count($dFolio)>0){
                $nFolio = $dFolio[0]->valor;
            }
            $nFolio++;

            if($nFolio <= 1){
                DB::statement('insert into tipo_documento_buzon_folio (id_tipo_documento,anio,id_buzon,valor,created_at,updated_at) values ('.$tipo_documento.','.$anio.','.$buzon.','.$nFolio.',now(),now())');
            }
            else{
                DB::statement('update tipo_documento_buzon_folio set valor = '.$nFolio.', updated_at = now() where id_tipo_documento= '.$tipo_documento.' and anio = '.$anio.' and id_buzon = '.$buzon);
            }

        }

        //sin folio
        if($tipo_folio == 3){
            $nFolio = $nFolio;
        } 

        $this->estado_folio($nFolio,1,$tipo_folio,$buzon,$tipo_documento,0); 
        return $nFolio; 
    } 

     
    public function obtenerVisadores($idDocumento, $idDocumentoBuzon) 
    { 
        $datosVisarFirmar = DocumentoBuzonBitacora::join('documento_buzon', 'documento_buzon.id_documento_buzon', '=', 'documento_buzon_bitacora.id_documento_buzon') 
                                                            ->join('documento', 'documento_buzon.id_documento', '=', 'documento.id_documento') 
                                                            ->join('accion', 'accion.id_accion', '=', 'documento_buzon_bitacora.id_accion' ) 
                                                            ->join('users', 'users.id', '=', 'documento_buzon_bitacora.id_usuario') 
                                                            ->join('buzon', 'buzon.id_buzon', '=', 'documento_buzon.id_buzon') 
                                                            ->where('documento.id_documento', $idDocumento) 
                                                            ->whereIn('documento_buzon_bitacora.id_accion', array('1','6')) 
                                                            ->select(       
                                                                'documento_buzon_bitacora.id_accion',                                                           
                                                                'accion.nombre',  
                                                                'documento_buzon_bitacora.id_usuario',                                                                  
                                                                'users.nombres',  
                                                                'users.primer_apellido',  
                                                                'users.segundo_apellido' 
                                                            ) 
                                                            ->orderBy('documento_buzon_bitacora.id_documento_buzon_bitacora') 
                                                            ->get(); 
 
        $txtVisadores = ""; 
        foreach ($datosVisarFirmar as $value)         
        {                     
            if ($value['id_accion'] == 1) 
                $txtVisadoresCrea = strtolower(substr($value['nombres'], 0, 1) . substr($value['primer_apellido'], 0, 1) . substr($value['segundo_apellido'], 0, 1));            
             
            if ($value['id_accion'] == 6) 
                $txtVisadores .= strtoupper(substr($value['nombres'], 0, 1) . substr($value['primer_apellido'], 0, 1) . substr($value['segundo_apellido'], 0, 1)) . "/";            
 
        }; 

        if ($txtVisadores != "")
            $txtVisadores .= $txtVisadoresCrea; 
         
        return $txtVisadores; 
 
    } 
 
    public function estado_folio($folio,$estado,$tipo_folio,$buzon,$tipo_documento,$reversado)
    { 
        if($estado == 1){ 
            db::statement("insert into bloqueo_folio (folio,estado,tipo_folio,buzon,tipo_documento,reversado) values (".$folio.",1,".$tipo_folio.",".$buzon.",".$tipo_documento.",".$reversado.")"); 
        } 
        else{ 
            db::statement("update bloqueo_folio set estado = 0, reversado = ".$reversado." where folio = ".(($folio==null)?'0':$folio)." and tipo_folio = ".$tipo_folio." and buzon = ".$buzon." and tipo_documento = ".$tipo_documento); 
        } 
         
    } 
}
