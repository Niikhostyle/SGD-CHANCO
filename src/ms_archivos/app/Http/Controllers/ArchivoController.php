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
use PDF;

class ArchivoController extends Controller{


    private $validator;

    public function __construct(DocumentoValidator $documentoValidator)
    {
        $this->validator = $documentoValidator;
    }

    public function generar_archivo(Request $request)
    {
        if ($request->isJson())
        {
            try 
            {
                DB::beginTransaction();

                $datosRequest = $request->json()->all();   
                
                $datosDocumento = Documento::findOrFail($datosRequest['id_documento']);
                $datosJsonTipoDocumento = json_decode($datosDocumento['json_tipo_documento'],true);

                $datosJsonTipoDocumento['id_tipo_origen'] = 2;

                $datosRequest['json_tipo_documento'] = $datosJsonTipoDocumento;
                
                $datosDocumento->update($datosRequest);   

                DB::commit();

                return $this->respondSuccess("Archivo generado", 200);

            } catch (ModelNotFoundException $e) {
                DB::rollBack();

                return $this->respondError('Falla al generar archivo:' . $e->getMessage(), 500);
            }
        }
        else
            return $this->respondError('Json inválido', 406);

    }

    public function generar_archivo_pdf(Request $request)
    { 
        try 
            {
                DB::beginTransaction();

                $datosRequest = $request->json()->all();       
        
                $nDocumento = $datosRequest['id_documento'];
                $idDocumentoBuzon = $datosRequest['id_documento_buzon'];
        
                $aInfoUsuarios = Users::where('id', $datosRequest['id_usuario'])->first(['genera_pdf']);

                if (!$aInfoUsuarios['genera_pdf'])
                    return $this->respondError('Usuario no tiene permiso para generar pdf.', 400);
                    
                $datosDocumentos = Documento::where('id_documento','=', $nDocumento)
                ->select('cuerpo', 'encabezado','materia')
                ->first(); 

                $data = PDF::loadView('pdf', $datosDocumentos)->save(storage_path('app/public/files/') . 'principal_'.$nDocumento.'_.pdf');

                $oMerger = PDFMerger::init();

                $oMerger->addPDF(storage_path('app/public/files/') . 'principal_'.$nDocumento.'_.pdf');

                $anexos = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon.id_documento_buzon', '=', 'documento_buzon_archivo.id_documento_buzon')
                                                ->where('id_documento', $nDocumento)
                                                ->where('id_tipo_archivo', 2)
                                                ->select('nombre_archivo_codificado')
                                                ->get(); 
                                                                
                foreach ($anexos as $file)
                    $oMerger->addPDF(storage_path('app/public/files/') . $file['nombre_archivo_codificado']);
                
                $nNombreArchivoCargar = $this->getNombreDocumento($nDocumento);

                $filePpal = 'archivo_generado_'.$nDocumento.'.pdf';    
                $oMerger->merge();
                $oMerger->save(storage_path('app/public/files/') . $nNombreArchivoCargar);

                $dFechaCreacion = date('Y-m-d H:i:s');        

                if (file_exists(storage_path('app/public/files/') . $nNombreArchivoCargar))
                {            
                    $docsPpales = DocumentoBuzonArchivo::where('id_documento_buzon', $idDocumentoBuzon)
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
                        'version' => '1',
                        'fecha' => $dFechaCreacion
                    ]);
                }

                $datosJsonTipoDocumento = json_decode($datosDocumentos['json_tipo_documento'],true);

                $datosJsonTipoDocumento['id_tipo_origen'] = 2;

                $datosRequest['json_tipo_documento'] = $datosJsonTipoDocumento;
                
                $datosDocumentos->update($datosRequest); 
                
                DB::commit();

                return $this->respondSuccess("Archivo generado", 200);

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
