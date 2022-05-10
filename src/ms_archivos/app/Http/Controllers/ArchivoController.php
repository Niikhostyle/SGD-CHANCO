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
                
                //reemplazar valores en encabezado
                //Nº {t_folio} {t_anio} {t_fecha}

                $aMeses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");                
                $sfecha = date('d')." de ".$aMeses[date('n')-1]. " del ".date('Y');                

                $sEncabezado = $datosDocumentos['encabezado'];
                $sEncabezado = str_replace('{t_folio}', $datosDocumentos['folio'], $sEncabezado);
                $sEncabezado = str_replace('{t_anio}', date('Y'), $sEncabezado);
                $sEncabezado = str_replace('{t_fecha}', $sfecha, $sEncabezado);

                //reemplazar path imagenes antes de generar pdf
                //ej: src="http://192.168.1.101:82/files/editor/images/historia.jpg" por src="/src/storage/app/public/files/editor/images/historia.jpg" 
                
                $datosDocumentosCuerpo = str_replace(env('APP_URL'), storage_path('app/public'), $datosDocumentos['cuerpo']);
                $datosDocumentosencabezado = str_replace(env('APP_URL'), storage_path('app/public'), $sEncabezado);
                
                $dataPdf = array('materia'=>$datosDocumentos['materia'], 'encabezado'=>$sEncabezado, 'cuerpo'=>$datosDocumentosCuerpo  );//  $datosDocumentos['cuerpo']            
                
                PDF::loadView('pdf', $dataPdf)->setPaper('legal', 'portrait')->save(storage_path('app/public/files/') . $nNombreArchivoCargar);           
 
                //ver cuantas paginas tiene para poner firma
                //Obtiene pagina para agregar firma
                $pdfPages = file_get_contents(storage_path('app/public/files/') . $nNombreArchivoCargar);
                $count = 0;
                $count = preg_match_all("/\/Page\W/", $pdfPages, $dummy);
                
                Documento::find($nDocumento)->update(['paginas_archivo' => $count, 'archivo_existente' => true]);

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
