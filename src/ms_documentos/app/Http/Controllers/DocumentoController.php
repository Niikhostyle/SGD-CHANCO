<?php
namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use App\Models\Documento;
use App\Models\DocumentoBuzon;
use App\Models\DocumentoBuzonArchivo;
use App\Models\DocumentoBuzonBitacora;
use App\Models\DocumentoFavoritoUsuario;
use App\Models\TipoDocumentoBuzonFolio;
use App\Models\TipoDocumento;
use Illuminate\Support\Facades\DB;
use App\Validator\DocumentoValidator;


use App\Http\Controllers\MailController;
use App\Mail\MailController as MailMailController;
use App\Models\Buzon;
use App\Models\User;
use Illuminate\Support\Facades\Mail;


class DocumentoController extends Controller{

    /**
     * @BuzonValidator
     */
    private $validator;

    const HASH_FILE_ALGO = 'sha256';

    public function __construct(DocumentoValidator $documentoValidator)
    {
        $this->validator = $documentoValidator;
    }

    public function crear(Request $request)
    {
        if($request->isJson())
        {
            try
            {
                DB::beginTransaction();
                
                $datosDocumento = $request->json()->all();                

                //$validator = $this->validator->validateInsert();

                //if ($validator->fails())
                //    return $this->respondFail('Falla al crear el documento: revisar datos de entrada');                

                $nTipoDoc = $datosDocumento['id_tipo_documento']; //id_tipo_asignacion_folio = 1 se genera folio al crear doc
                $idBuzon = $datosDocumento['id_buzon'];                 
                //return "paso val";
                /** CODIGO CON EL CUAL SE LLAMA A OTRO MICROSERVICIO **/
                
                $msVerTipoDoc = Http::withHeaders(['key'=>$request->header('key'),'Content-Type'=>'application/json']) //
                ->timeout(30)
                ->withBody(json_encode([
                    'id_tipo_documento' => $nTipoDoc,
                ]), 'json')
                ->get('http://sgd_ms_tipos_documentos:3333/api/sgd-tipodoc/ver');

                $nFolio = null;
                $anio = date('Y');
                
                $idTipoFolio = $msVerTipoDoc['data']['id_tipo_folio'];

                /** CODIGO PARA OBTENER FOLIO CUANDO TIPO ASIGNACIÓN ES EN LA CREACIÓN  **/
                
                if( $msVerTipoDoc['data']['id_tipo_asignacion_folio'] == 1) //creación
                { 
                    $nFolio = Http::withHeaders(['key'=>$request->header('key'),'Content-Type'=>'application/json']) //
                    ->timeout(30)
                    ->withBody(json_encode([
                        'id_tipo_documento' => $nTipoDoc,
                        'anio' => $anio,
                        'id_buzon' => null,
                        'id_tipo_folio' =>  $idTipoFolio,
                    ]), 'json')
                    ->get('http://sgd_ms_folios:3333/api/sgd-folios/asignaFolio');
                    
                }
                
                /* IMPORTANTE::REVISAR QUE PASARÁ CON EL FOLIO SI NO SE LLEGA A CREAR EL DOCUMENTO POR ALGUN ERROR */    

                $dFechaCreacion = date('Y-m-d');
                
                $jsonTipoDocumento = $msVerTipoDoc->json();

                //hash validación

                $sparamHash = $dFechaCreacion.$msVerTipoDoc['data']['nombre_corto'].$datosDocumento['materia'];
                $sHash = hash('sha256', $sparamHash, false);

                //guardar respuesta                   
                $jsonRespuesta = array(); 
                if ($msVerTipoDoc['data']['id_tipo_flujo'] == 1)
                {
                    if ($datosDocumento['json_respuesta_a'] != "" && $datosDocumento['json_respuesta_a'] != null)
                    {                              
                        foreach($datosDocumento['json_respuesta_a'] as $resp)
                        {
                            $datosRespuesta = Documento::where('id_documento','=', $resp)->select('id_documento','identificador', 'materia','created_at')->first();
                            $jsonRespuesta[] = $datosRespuesta;                            
                        }   
                    
                        //$datosRequest['json_respuesta_a'] = json_encode($jsonRespuesta);
                    }
                }

                $documento = Documento::create([
                    'id_tipo_documento' => $datosDocumento['id_tipo_documento'],
                    'id_nivel_acceso' => $datosDocumento['id_nivel_acceso'],
                    'efectos_terceros' => $datosDocumento['efectos_terceros'],
                    'json_tipo_documento' => json_encode($msVerTipoDoc['data']), //obtener de ms_tipos_documentos
                    'json_respuesta_a' => json_encode($jsonRespuesta),
                    'materia' => $datosDocumento['materia'],
                    'anterior' => $datosDocumento['anterior'],
                    'descripcion' => $datosDocumento['descripcion'],
                    'encabezado' => $datosDocumento['encabezado'],
                    'cuerpo' => $datosDocumento['cuerpo'],
                    'fecha' => $dFechaCreacion,
                    'hash_validacion' => $sHash,
                    'folio' => $nFolio                    
                ]);

                $documento = $documento->fresh();
                
                $documentoBuzon = DocumentoBuzon::create([
                    'id_documento' => $documento->id_documento,
                    'id_buzon' => $datosDocumento['id_buzon'],
                    'id_carpeta' => 3,
                    'id_estado_documento' => 1,
                    'id_tipo_destino' => 1,
                    'id_documento_buzon_padre' => null,
                    'fecha' => $dFechaCreacion,
                    'contestar_hasta' => $datosDocumento['contestar_hasta'],
                    'notificado' => false,
                    'recibido' => false,
                    'favorito' => false
                ]);
                
                $documentoBuzonBitacora = DocumentoBuzonBitacora::create([
                    'id_documento_buzon' => $documentoBuzon->id_documento_buzon,
                    'id_accion' => 1,
                    'fecha' => $dFechaCreacion,
                    'id_usuario' => $datosDocumento['id_usuario']
                ]);

                if ($nFolio != null)
                {
                    //registrar accion de asignacion de folio en bitacora
                    $documentoBuzonBitacoraFolio = DocumentoBuzonBitacora::create([
                        'id_documento_buzon' => $documentoBuzon->id_documento_buzon,
                        'id_accion' => 9,
                        'fecha' => $dFechaCreacion,
                        'id_usuario' => $datosDocumento['id_usuario']
                    ]);
                }
                
                $documento->rel_documento_buzon;

                DB::commit();

                return $this->respondSuccess($documento, 201);

            }
            catch (ModelNotFoundException $e)
            {
                
                DB::rollBack();
                //return $e->getMessage();
                return $this->respondError('Falla al crear documento:' . $e->getMessage(), 500);
                
            }
        }
        else
            return $this->respondError('Json inválido', 406);

    }

    public function actualizar(Request $request)
    {
        if ($request->isJson())
        {
            try 
            {
                DB::beginTransaction();

                $datosRequest = $request->json()->all();

                //$validator = $this->validator->validateUpdate();

                //if ($validator->fails())
                //    return $this->respondFail('Falla al actualizar buzón: revisar datos de entrada');

                //1: actualiza documento
                //2: crea o actualiza dest principal
                //3: elimina dest secundario y crea nuevamente
                
                $datosDocumento = Documento::findOrFail($datosRequest['id_documento']);

                if ($datosDocumento->id_documento != '')
                {   
                    $dFechaCreacion = date('Y-m-d H:i:s');

                    if ($datosRequest['opcionGuardar'] != 1 || $datosRequest['opcionGuardar'] == null || $datosRequest['opcionGuardar'] == '')
                    {
                        //actualizar json - agregar orden 0 si corresponde para flujo controlado
                        $datosJsonTipoDocumento = json_decode($datosDocumento['json_tipo_documento'],true);

                        if ($datosRequest['carpeta'] == 3 && $datosJsonTipoDocumento['id_tipo_flujo'] == 2)
                        {                       
                            $nFlujoActual = $datosJsonTipoDocumento['flujo_actual'];

                            foreach ($datosJsonTipoDocumento['buzones_flujo'] as $key => $valor)
                            {                    
                                if ($valor['orden'] == 1)
                                    $nBuzonActual = $valor['id_buzon'];

                                if ($valor['orden'] == 0)
                                   array_splice($datosJsonTipoDocumento['buzones_flujo'], $key);
                            }
                            
                            if ($nFlujoActual == 1) //inicial
                            {
                                if ($datosRequest['destinatarioPrincipal'] != $nBuzonActual) //agregar item con orden 0
                                {
                                    $datosJsonTipoDocumento['flujo_actual'] = 0;

                                    $jsonExtra = array('orden'=>0,
                                                        'acciones'=>array(['id_accion'=>6]),
                                                        'id_buzon'=>$datosRequest['destinatarioPrincipal'],
                                                        'procesado'=>false,
                                                        'id_tipo_documento_buzon'=>null);

                                    $datosJsonTipoDocumento['buzones_flujo'][] = $jsonExtra;

                                    $datosRequest['json_tipo_documento'] = $datosJsonTipoDocumento;
                                }
                                    
                            }

                            if ($nFlujoActual == 0) //se agrega extra
                            {
                                //actualizar orden 0                                
                                $jsonExtra = array('orden'=>0,
                                                'acciones'=>array(['id_accion'=>6]),
                                                'id_buzon'=>$datosRequest['destinatarioPrincipal'],
                                                'procesado'=>false,
                                                'id_tipo_documento_buzon'=>null);

                                $datosJsonTipoDocumento['buzones_flujo'][] = $jsonExtra;

                                $datosRequest['json_tipo_documento'] = $datosJsonTipoDocumento;

                            }
                        } 
                        
                        if ($datosRequest['carpeta'] == 2) //actualiza estado si se edita, se deja en pendiente
                        {
                            DocumentoBuzon::find($datosRequest["id_documento_buzon"])->update(['id_estado_documento' => 4]);

                            DocumentoBuzonBitacora::create([
                                'id_documento_buzon' => $datosRequest["id_documento_buzon"],
                                'id_accion' => 4,
                                'fecha' => $dFechaCreacion,
                                'id_usuario' => $datosRequest['id_usuario']
                            ]);   
                        }

                        if ($datosRequest['carpeta'] == 3 && $datosJsonTipoDocumento['id_tipo_flujo'] == 1)
                        {
                            //guardar respuesta
                            if ($datosJsonTipoDocumento['id_tipo_flujo'] == 1)
                            {
                                $jsonRespuesta = array();                    

                                if ($datosRequest['json_respuesta_a'] != "" && $datosRequest['json_respuesta_a'] != null)
                                {   
                                    foreach($datosRequest['json_respuesta_a'] as $resp)
                                    {
                                        $datosRespuesta = Documento::where('id_documento','=', $resp)->select('id_documento','identificador', 'materia','created_at')->first();
                                        $jsonRespuesta[] = $datosRespuesta;                                        
                                    }   
                                
                                    $datosRequest['json_respuesta_a'] = json_encode($jsonRespuesta);
                                }
                            }
                            
                        }
                        else
                            $datosRequest['json_respuesta_a'] = $datosDocumento['json_respuesta_a'];

                        $datosDocumento->update($datosRequest);
                    }

                                       
                    
                    //si viene destinatario principal se agrega un registro
                    
                    $jsonAcciones = array();                    
                    
                    if ($datosRequest['acciones_solicitadas'] != "" || $datosRequest['acciones_solicitadas'] != null)
                    {    
                        foreach($datosRequest['acciones_solicitadas'] as $accion)
                            $jsonAcciones[] = array("id_accion" => $accion);
                    }

                    if ($datosRequest['destinatarioPrincipal'] != "")
                    {
                        //verificar si se crea o actualiza   

                        if ($datosRequest['carpeta'] == 3) //despachados
                        {
                            $documentoBuzon = DocumentoBuzon::updateOrCreate([
                                'id_documento' => $datosRequest['id_documento'],
                                'id_tipo_destino' => 1,
                                'id_documento_buzon_padre' => $datosRequest['id_documento_buzon'],
                                'id_estado_documento' => 1,
                            ],[
                                'id_buzon' => $datosRequest['destinatarioPrincipal'],
                                'id_carpeta' => 1,
                                'id_estado_documento' => 1,
                                'fecha' => $dFechaCreacion,
                                'json_acciones'=> json_encode($jsonAcciones),
                                'comentario_principal' => $datosRequest['comentarioPrincipal'], 
                                'contestar_hasta' => $datosRequest['contestar_hasta'],
                                'notificado' => false,
                                'recibido' => false,
                                'favorito' => false    
                            ]);                            
                        }

                        if ($datosRequest['carpeta'] == 2) //recibidos
                        {
                            $documentoBuzon = DocumentoBuzon::updateOrCreate([
                                'id_documento' => $datosRequest['id_documento'],
                                'id_tipo_destino' => 1,
                                'id_documento_buzon_padre' => $datosRequest['id_documento_buzon'],
                                'id_carpeta' => 1,
                                'id_estado_documento' => 4
                            ],[
                                'id_buzon' => $datosRequest['destinatarioPrincipal'],                                
                                'id_estado_documento' => 4,
                                'fecha' => $dFechaCreacion,
                                'json_acciones'=> json_encode($jsonAcciones),
                                'comentario_principal' => $datosRequest['comentarioPrincipal'], 
                                'contestar_hasta' => $datosRequest['contestar_hasta'],
                                'notificado' => false,
                                'recibido' => false,
                                'favorito' => false    
                            ]);

                           
                        }

                        //pendiente - crear orden 0 en flujo controlado/mixto
                    }

                    //si viene destinatario secundario se agrega registro

                    $aOtrosDestinatarios = explode (',', $datosRequest['destinatarioOtros']);

                    if ($datosRequest['carpeta'] == 2) //recibidos
                    {                        
                        //eliminar segun criterios y crear nuevamente
                        DocumentoBuzon::where([
                            'id_documento' => $datosRequest['id_documento'],
                            'id_tipo_destino' => 2,
                            'id_documento_buzon_padre' => $datosRequest['id_documento_buzon'],                                
                            'id_estado_documento' => 4,
                            'id_carpeta' => 1
                        ])->delete();

                        if ($datosRequest['destinatarioOtros'] != "")
                        {
                            foreach ($aOtrosDestinatarios as $destinatario)
                            {
                                $documentoBuzon = DocumentoBuzon::create([
                                    'id_documento' => $datosRequest['id_documento'],
                                    'id_buzon' => $destinatario,
                                    'id_carpeta' => 1,
                                    'id_estado_documento' => 4,
                                    'id_tipo_destino' => 2,
                                    'id_documento_buzon_padre' => $datosRequest['id_documento_buzon'],
                                    'json_acciones'=> json_encode($jsonAcciones),
                                    'comentario_secundario' => $datosRequest['comentarioOtros'], 
                                    'fecha' => $dFechaCreacion,
                                    'contestar_hasta' => $datosRequest['contestar_hasta'],
                                    'notificado' => false,
                                    'recibido' => false,
                                    'favorito' => false
                                ]);
                            } 
                        }
                    }
                        
                    if ($datosRequest['carpeta'] == 3) //despachados
                    {
                        //eliminar segun criterios y crear nuevamente
                        DocumentoBuzon::where([
                            'id_documento' => $datosRequest['id_documento'],
                            'id_tipo_destino' => 2,
                            'id_documento_buzon_padre' => $datosRequest['id_documento_buzon'],                                
                            'id_estado_documento' => 1,
                            'id_carpeta' => 1
                        ])->delete();

                        if ($datosRequest['destinatarioOtros'] != "")
                        {
                            foreach ($aOtrosDestinatarios as $destinatario)
                            {
                                $documentoBuzon = DocumentoBuzon::create([
                                    'id_documento' => $datosRequest['id_documento'],
                                    'id_buzon' => $destinatario,
                                    'id_carpeta' => 1,
                                    'id_estado_documento' => 1,
                                    'id_tipo_destino' => 2,
                                    'id_documento_buzon_padre' => $datosRequest['id_documento_buzon'],
                                    'json_acciones'=> json_encode($jsonAcciones),
                                    'comentario_secundario' => $datosRequest['comentarioOtros'], 
                                    'fecha' => $dFechaCreacion,
                                    'contestar_hasta' => $datosRequest['contestar_hasta'],
                                    'notificado' => false,
                                    'recibido' => false,
                                    'favorito' => false
                                ]);
                            } 
                        }
                    }

                    //elimina archivos asociados

                    if (isset($datosRequest['fileDelete']))
                    {

                        $aFilesDelete = explode (',', $datosRequest['fileDelete']);
                        
                        foreach ($aFilesDelete as $idDocBuzArchivo)
                        {
                            $datosArchivo = DocumentoBuzonArchivo::findOrFail($idDocBuzArchivo);  
                            
                            if ($datosArchivo['id_tipo_archivo'] == 1)
                            {
                                $docsPpales = DocumentoBuzonArchivo::where('id_documento_buzon', $datosArchivo['id_documento_buzon'])
                                                                ->where('id_tipo_archivo', 1)
                                                                ->get();

                                foreach ($docsPpales as $archFile)
                                {
                                    $nSalida = $archFile->version - 1;
                                    DocumentoBuzonArchivo::find($archFile->id_documento_buzon_archivo)->update(['version' => $nSalida]);                
                                }
                            }

                            $sDocDelete = $datosArchivo['nombre_archivo_codificado'];
                            $filenameCodificado = storage_path('app/public/files/'.$sDocDelete);
                            if (file_exists($filenameCodificado))
                                unlink($filenameCodificado);

                            DocumentoBuzonArchivo::where('id_documento_buzon_archivo', $idDocBuzArchivo)->delete(); 
                        }
                    }

                    /*if (isset($datosRequest['fileDelete']))
                    {
                        $aFilesDelete = explode (',', $datosRequest['fileDelete']);
                
                        DocumentoBuzonArchivo::whereIn('id_documento_buzon_archivo', $aFilesDelete)->delete();

                    }*/

                    DB::commit();

                    return $this->respondSuccess('Documento actualizado', 200);

                }
                else
                {
                    return $this->respondError('Falla al editar documento:', 500);
                }
            } catch (ModelNotFoundException $e) {
                DB::rollBack();

                return $this->respondError('Falla al editar documento:' . $e->getMessage(), 500);
            }
        }
        else
            return $this->respondError('Json inválido', 406);

    }

    public function eliminar(Request $request)
    {
        if ($request->isJson())
        {
            try 
            {
                DB::beginTransaction();

                $datosRequest = $request->json()->all();                             
                
                //verifica que el documento este en estado borrador
                $datoDocBuzon = DocumentoBuzon::where('id_documento_buzon', $datosRequest['id_documento_buzon'])
                                               ->where('id_estado_documento', '1')                          
                                               ->first();

                if ($datoDocBuzon['id_documento_buzon'])
                {
                    //elimina de las tablas relacionadas
                    $datosDocumento = Documento::findOrFail($datosRequest['id_documento']);

                    DocumentoBuzonBitacora::where('id_documento_buzon',$datosRequest['id_documento_buzon'])->delete();
                    $datosDocumento->rel_documento_buzon()->delete();
                    $datosDocumento->delete();     
                    
                    DB::commit();

                    return $this->respondSuccess("Documento eliminado", 200);           
                }
                else
                    return $this->respondError('Falla al eliminar documento:', 500);
               

            } catch (ModelNotFoundException $e) {
                DB::rollBack();

                return $this->respondError('Falla al eliminar documento:' . $e->getMessage(), 500);
            }
        }
        else
            return $this->respondError('Json inválido', 406);


    }

    public function enviar(Request $request)
    {
        //DERIVAR

        //1: actualizar en documento_buzon el registro Borrador(1) a Enviado(2)
        
        //2: actualizar en documento_buzon registro principal y secundarios de estado 1 a 3

        //3: crear registro en documento_buzon_bitacora para destinatario principal y secundarios con accion = 2

        if ($request->isJson())
        {
            try 
            {
                DB::beginTransaction();

                $datosRequest = $request->json()->all();

                $dFechaCreacion = date('Y-m-d H:i:s');                

                if ($datosRequest['carpeta'] == 3) //despachados
                {
                    $estadoDocumentoFinal = 2; 
                    $estadoDocumentoActual = array('1');    
                    
                    DocumentoBuzon::find($datosRequest["id_documento_buzon"])->update(['id_estado_documento' => $estadoDocumentoFinal, 'fecha' => $dFechaCreacion]);
                    
                    //cambia estado de documentos respuesta a
                    
                    if ($datosRequest["json_respuesta_a"] != null && $datosRequest["json_respuesta_a"] != "")
                    {
                        
                        foreach($datosRequest["json_respuesta_a"] as $nDoc)
                        {
                            $datosDocumentoBuzonResp = DocumentoBuzon::where('id_documento', $nDoc)
                                ->where('id_buzon', $datosRequest['id_buzon'])
                                ->where('id_carpeta', '2')
                                ->where('id_estado_documento', '4')                                
                                ->select('id_documento_buzon')                                
                                ->first();
                        
                                $datosDocumentoBuzonResp->update(['id_estado_documento' => 5, 'fecha' => $dFechaCreacion]);
                        }
                    }
                }

                if ($datosRequest['carpeta'] == 2) //recibidos
                {
                    //actualizar en documento el campo flujo_actual al valor siguiente en flujo controlado/mixto
                    // y en buzones_flujo dejar en true en buzon ya procesado (recien enviado)

                    $datosFlujoJson = Documento::findOrFail($datosRequest['id_documento']);

                    $datosJsonTipoDocumento = json_decode($datosFlujoJson['json_tipo_documento'],true);
                    $nFlujoActual = $datosJsonTipoDocumento['flujo_actual'];
                    
                    $nNuevoFlujoActual = $nFlujoActual + 1;

                    //ver segun tipo de flujo como será la derivación
                    $nTipoFlujo = $datosJsonTipoDocumento['id_tipo_flujo'];
                    
                    foreach ($datosJsonTipoDocumento['buzones_flujo'] as $key => $valor)
                    {                    
                        //buzon siguiente en el flujo
                        if (($nTipoFlujo == 2) && ($valor['orden'] == ($nFlujoActual + 1)) && ($datosRequest['destinatarioPrincipal'] == $valor['id_buzon']))// && ($valor['procesado'] == false)
                        {}

                        //buzon reinicio en el flujo
                        if (($nTipoFlujo == 2) && ($valor['orden'] == 1) && ($datosRequest['destinatarioPrincipal'] == $valor['id_buzon']))// && ($valor['procesado'] == true)
                        {
                            $nNuevoFlujoActual = 1;
                        }
                        
                        //buzon anterior en el flujo
                        if (($nTipoFlujo == 2) && ($valor['orden'] == ($nFlujoActual - 1)) && ($datosRequest['destinatarioPrincipal'] == $valor['id_buzon']))// && ($valor['procesado'] == false)
                        {
                            $nNuevoFlujoActual = $nFlujoActual - 1;
                        }

                        if ($valor['orden'] == $nFlujoActual)
                        {
                            $valor['procesado'] = true;
                            $datosJsonTipoDocumento['buzones_flujo'][$key] = $valor;
                        }
                    }

                    //actualiza el flujo actual
                    $datosJsonTipoDocumento['flujo_actual'] = $nNuevoFlujoActual;
                    
                    $datosFlujoJson->update(['json_tipo_documento' => json_encode($datosJsonTipoDocumento)]);                     
                    
                    //agregar si estado actual es 11 dejar final como 12 y estado 9 dejar como 10
                    //$estadoDocumentoFinal = 7;        
                    $estadoDocumentoActual = array('4','9','11'); //"4,9,11"; deberia ir con whereIn  
                    
                    $datosUpdate = DocumentoBuzon::find($datosRequest["id_documento_buzon"]);
                    
                    switch ($datosUpdate->id_estado_documento)
                    {                       
                        case (4):
                            $estadoDocumentoFinal = 7;
                            break;
                        case (9):
                            $estadoDocumentoFinal = 10;
                            break;                        
                        case (11):
                            $estadoDocumentoFinal = 12;
                            break;
                        default:
                            $estadoDocumentoFinal = 7;
                    }

                    $datosUpdate->id_estado_documento = $estadoDocumentoFinal;
                    $datosUpdate->save();
                }
               
                if ($datosRequest['destinatarioPrincipal'] != "" || $datosRequest['destinatarioPrincipal'] != null)
                {
                    //valida acciones
                    if (!isset($datosRequest['acciones_solicitadas']))
                        return $this->respondFail('Falla al enviar documento: Acciones solicitadas no válidas.');

                    $datosDocumentoBuzonD1 = DocumentoBuzon::where('id_documento', $datosRequest['id_documento'])
                                    ->where('id_documento_buzon_padre', $datosRequest['id_documento_buzon'])
                                    ->where('id_tipo_destino', '1')
                                    ->whereIn('id_estado_documento', $estadoDocumentoActual)
                                    ->where('id_buzon', $datosRequest['destinatarioPrincipal'])
                                    ->select('id_documento_buzon')                                
                                    ->first();
                    $datosDocumentoBuzonD1->update(['id_estado_documento' => 3, 'fecha' => $dFechaCreacion]);
    
                    $documentoBuzonBitacoraD1 = DocumentoBuzonBitacora::create([
                                        'id_documento_buzon' => $datosDocumentoBuzonD1["id_documento_buzon"],
                                        'id_accion' => 2,
                                        'fecha' => $dFechaCreacion,
                                        'id_usuario' => $datosRequest['id_usuario']
                    ]);   
                }
                else
                {
                    return $this->respondFail('Falla al enviar documento: Destinatario principal no válido.');
                }

                if ($datosRequest['destinatarioOtros'] != "" || $datosRequest['destinatarioOtros'] != null)
                {
                    $aOtrosDestinatarios = explode (',', $datosRequest['destinatarioOtros']);
                    $datosDocumentoBuzonD2 = DocumentoBuzon::where('id_documento', $datosRequest['id_documento'])
                                    ->where('id_documento_buzon_padre', $datosRequest['id_documento_buzon'])
                                    ->whereIn('id_buzon', $aOtrosDestinatarios)
                                    ->whereIn('id_estado_documento', $estadoDocumentoActual) 
                                    ->where('id_tipo_destino', '2')                                    
                                    ->select('id_documento_buzon')   
                                    ->get();  
                    foreach ($datosDocumentoBuzonD2 as $dato)
                    {
                        DocumentoBuzon::find($dato["id_documento_buzon"])->update(['id_estado_documento' => 3, 'fecha' => $dFechaCreacion]);
                    } 
                
                    foreach ($datosDocumentoBuzonD2 as $dato)
                    {
                        $documentoBuzonBitacoraD2 = DocumentoBuzonBitacora::create([
                                    'id_documento_buzon' => $dato["id_documento_buzon"],
                                    'id_accion' => 2,
                                    'fecha' => $dFechaCreacion,
                                    'id_usuario' => $datosRequest['id_usuario']
                        ]);                        
                    }
                }

                DB::commit();

                return $this->respondSuccess("Documento enviado", 200);

            } catch (ModelNotFoundException $e) {
                DB::rollBack();

                return $this->respondError('Falla al enviar documento:' . $e->getMessage(), 500);
            }
        }
        else
            return $this->respondError('Json inválido', 406);

    }

    public function actualizar_estado(Request $request)
    {
        
        if ($request->isJson())
        {
            try 
            {
                DB::beginTransaction();

                $datosRequest = $request->json()->all();
                
                $dFecha = date('Y-m-d H:i:s');
                
                //****** SI SE AGREGA EL CAMPO PROCESADO EN EL JSON POR CADA ACCION SE DEBE ACTUALIZAR A TRUE AL HACER EL CAMBIO DE ESTADO.    
                //agregar estados 10 y 12
               
                $datosDocumento = Documento::find($datosRequest["id_documento"]);
                $idTipoDocumento = $datosDocumento->id_tipo_documento;

                if ($request->accion == 3) // por recibir
                {                           
                    $datosDocBuzon = DocumentoBuzon::find($datosRequest["id_documento_buzon"]);
                    $idDocBuzonPadre = $datosDocBuzon->id_documento_buzon_padre;                    
                    
                    $datosDocumento = Documento::find($datosRequest["id_documento"]);
                    $idTipoDocumento = $datosDocumento->id_tipo_documento;

                    //actualizo estado y carpeta
                    $datosDocBuzon->update(['id_estado_documento' => 4, 'id_carpeta' => 2, 'fecha' => $dFecha]);
                    
                    //actualizar recibido en buzon padre si es principal
                    if ($datosDocBuzon->id_tipo_destino == 1)
                        DocumentoBuzon::find($idDocBuzonPadre)->update(['recibido' => true]);

                    // ASIGNACION FOLIO EN LA RECEPCIÓN    
                    $datosJsonTipoDocumento = json_decode($datosDocumento['json_tipo_documento'],true);
                    $idTipoAsigFolio = $datosJsonTipoDocumento['id_tipo_asignacion_folio'];
                    $idTipoFolio = $datosJsonTipoDocumento['id_tipo_folio'];
                    $idTipoFlujo = $datosJsonTipoDocumento['id_tipo_flujo'];
                    
                    $datosJsonAcciones = json_decode($datosDocBuzon['json_acciones'],true);
                    //return $datosJsonAcciones;
                    foreach($datosJsonAcciones as $accion)
                    {
                        $idAccion[] = $accion['id_accion'];
                    }                    
                    
                    if ($idTipoAsigFolio == 2 && $idTipoFlujo != 1) //se aplica a flujo controlado, mixto y tipo asig en recepción
                    {                                           
                        if (in_array(9, $idAccion))//cambiar id_accion a 9
                        {                    
                            $anio = date('Y');
                            $fecha = date('Y-m-d H:i:s');

                            $nFolio = Http::withHeaders(['key'=>$request->header('key'),'Content-Type'=>'application/json']) 
                            ->timeout(30)
                            ->withBody(json_encode([
                                'id_tipo_documento' => $idTipoDocumento,
                                'anio' => $anio ,
                                'id_buzon' => $datosRequest['id_buzon'],
                                'id_tipo_folio' => $idTipoFolio
                            ]), 'json')
                            ->get('http://sgd_ms_folios:3333/api/sgd-folios/asignaFolio');
 
                            Documento::find($datosRequest["id_documento"])->update(['folio' => $nFolio]); 
                            Documento::find($datosRequest["id_documento"])->update(['fecha' => $fecha]);    

                        }
                
                    } 
                }
                
                if ($request->accion == 7) // firmar
                    DocumentoBuzon::find($datosRequest["id_documento_buzon"])->update(['id_estado_documento' => 9]);

                if ($request->accion == 10) // finalizado
                {
                    Documento::find($datosRequest["id_documento"])->update(['finalizado' => true]);
                    DocumentoBuzon::find($datosRequest["id_documento_buzon"])->update(['id_estado_documento' => 13]);
                    //actualizar flujos                    
                }

                if ($request->accion == 6) // visar
                    DocumentoBuzon::find($datosRequest["id_documento_buzon"])->update(['id_estado_documento' => 11]);

                //registrar accion en bitacora

                $documentoBuzonBitacora = DocumentoBuzonBitacora::create([
                            'id_documento_buzon' => $datosRequest["id_documento_buzon"],
                            'id_accion' => $request->accion,
                            'fecha' => $dFecha,
                            'id_usuario' => $datosRequest['id_usuario']
                ]);                 
                
                DB::commit();
                return $this->respondSuccess("Cambio de estado realizado.", 200);

            } catch (ModelNotFoundException $e) {
                DB::rollBack();

                return $this->respondError('Falla al recepcionar documento:' . $e->getMessage(), 500);
            }
        }
        else
            return $this->respondError('Json inválido', 406);

    }



    public function archivar(Request $request)
    {
        if ($request->isJson())
        {
            try 
            {
                DB::beginTransaction();

                $datosRequest = $request->json()->all();

                $dFechaCreacion = date('Y-m-d H:i:s');

                //por defecto archivado 
                $nEstadoAccion = 6;
                $nAccion = 12;
                
                //accion desarchivado
                if ($datosRequest['accion'] == 1)
                {
                    $nEstadoAccion = 4;
                    $nAccion = 14;
                }                       

                DocumentoBuzon::find($datosRequest["id_documento_buzon"])->update(['id_estado_documento' => $nEstadoAccion]);

                $documentoBuzonBitacora = DocumentoBuzonBitacora::create([
                                            'id_documento_buzon' => $datosRequest["id_documento_buzon"],
                                            'id_accion' => $nAccion,
                                            'fecha' => $dFechaCreacion,
                                            'id_usuario' => $datosRequest['id_usuario'],
                                            'comentario' => $datosRequest['comentario']
                                            ]);                                        
                                            
                DB::commit();

                return $this->respondSuccess("Documento archivado", 200);

            } catch (ModelNotFoundException $e) {
                DB::rollBack();

                return $this->respondError('Falla al archivar documento:' . $e->getMessage(), 500);
            }
        }
        else
            return $this->respondError('Json inválido', 406);

    }   


    public function ver(Request $request)
    {
        if($request->isJson())
        {
            try 
            {
                $datosRequest = $request->json()->all();

                //$validator = $this->validator->validateField($datosRequest);
                //if ($validator->fails())
                //    return $this->respondFail('Falla al obtener documento: revisar datos de entrada');

                $datosDocumento = Documento::findOrFail($datosRequest['id_documento']);
                $datosDocumento->rel_documento_buzon;

                $datosVerDoc = Documento::join('documento_buzon', 'documento_buzon.id_documento','=','documento.id_documento')
                                    ->where('documento.id_documento','=', $datosRequest['id_documento'])
                                    ->whereRaw('documento_buzon.id_documento_buzon_padre = (select id_documento_buzon_padre from documento_buzon where id_documento = '.$datosRequest['id_documento'].' order by id_documento_buzon desc limit 1)')
                                    ->select('documento_buzon.*')
                                    ->get();

                $datosDocumento['rel_documento_buzon_actual'] =  $datosVerDoc; 
                
                $docEnRespuesta = Documento::where('json_respuesta_a', 'like', '%"id_documento": '.$datosRequest['id_documento'].'%')->select('id_documento','identificador', 'materia','created_at')->get();
                $datosDocumento['rel_responder'] =  $docEnRespuesta; 

                $datosDocumentoBuzon = DocumentoBuzon::join('documento_buzon_archivo', 'documento_buzon_archivo.id_documento_buzon', '=', 'documento_buzon.id_documento_buzon')
                                                    ->where('documento_buzon.id_documento', $request['id_documento'])
                                                    ->select(
                                                        'documento_buzon_archivo.id_documento_buzon_archivo',
                                                        'documento_buzon_archivo.id_documento_buzon',
                                                        'documento_buzon_archivo.id_tipo_archivo',
                                                        'documento_buzon_archivo.nombre_archivo_original',
                                                        'documento_buzon_archivo.nombre_archivo_codificado',
                                                        'documento_buzon_archivo.fecha',
                                                        'documento_buzon_archivo.version')
                                                    ->orderBy('documento_buzon_archivo.version')
                                                    ->get();
                            
                $datosDocumento['rel_archivos'] =  $datosDocumentoBuzon;

                //visaciones y firmas de un doc

                $datosVisarFirmar = DocumentoBuzonBitacora::join('documento_buzon', 'documento_buzon.id_documento_buzon', '=', 'documento_buzon_bitacora.id_documento_buzon')
                                                            ->join('documento', 'documento_buzon.id_documento', '=', 'documento.id_documento')
                                                            ->join('accion', 'accion.id_accion', '=', 'documento_buzon_bitacora.id_accion' )
                                                            ->join('users', 'users.id', '=', 'documento_buzon_bitacora.id_usuario')
                                                            ->join('buzon', 'buzon.id_buzon', '=', 'documento_buzon.id_buzon')
                                                            ->where('documento.id_documento', $datosRequest['id_documento'])
                                                            ->whereIn('documento_buzon_bitacora.id_accion', array('6','7'))
                                                            ->select(
                                                                'documento_buzon_bitacora.id_accion', 
                                                                'accion.nombre', 
                                                                'documento_buzon_bitacora.id_usuario', 
                                                                'users.nombres', 
                                                                'users.primer_apellido', 
                                                                'documento_buzon_bitacora.fecha',
                                                                'buzon.nombre' 
                                                            )
                                                            ->orderBy('documento_buzon_bitacora.id_documento_buzon_bitacora')
                                                            ->get();
                $datosDocumento['rel_bitacora'] =  $datosVisarFirmar;
                              
                return $this->respondSuccess($datosDocumento, 200);
            }  
            catch (ModelNotFoundException $e) 
            {
                return $this->respondError('Documento no existe', 500);
            } 
        }
        else 
            return $this->respondError('Json inválido', 406);
    }

    public function verPendientesBuzon(Request $request)
    {
        if($request->isJson())
        {
            try 
            {
                $datosRequest = $request->json()->all();

                //$validator = $this->validator->validateField($datosRequest);
                //if ($validator->fails())
                //    return $this->respondFail('Falla al obtener documento: revisar datos de entrada');

                $datosDocumentoBuzon = DocumentoBuzon::join('documento', 'documento_buzon.id_documento','=','documento.id_documento')
                                                    ->where('documento_buzon.id_buzon', $request['id_buzon'])
                                                    ->where('documento_buzon.id_carpeta', 2)
                                                    ->where('documento_buzon.id_estado_documento', 4) 
                                                    ->where('documento_buzon.id_tipo_destino', 1) 
                                                    ->select('documento_buzon.id_documento','documento.materia', 'documento.json_tipo_documento', 'documento.identificador')
                                                    ->get();           
                              
                return $this->respondSuccess($datosDocumentoBuzon, 200);
            }  
            catch (ModelNotFoundException $e) 
            {
                return $this->respondError('Documento no existe', 500);
            } 
        }
        else 
            return $this->respondError('Json inválido', 406);
    }



    public function listarFavoritos(Request $request)
    {
        if($request->isJson())
        {
            try
            {
                $datosRequest = $request->json()->all();

                $validator = $this->validator->validateFieldUser($datosRequest);

                if ($validator->fails())
                    return $this->respondFail('Falla al listar los documentos: revisar datos de entrada');

                $datosDocumentoFavorito = DocumentoFavoritoUsuario::join('documento', 'documento_favorito_usuario.id_documento', '=', 'documento.id_documento')
                                        ->join('tipo_documento', 'documento.id_tipo_documento', '=', 'tipo_documento.id_tipo_documento')
                                        ->join('documento_buzon', 'documento_buzon.id_documento', '=', 'documento_favorito_usuario.id_documento')
                                        ->join('buzon', 'buzon.id_buzon', '=', 'documento_buzon.id_buzon')
                                        ->select(
                                            'documento_favorito_usuario.id_documento as id_documento',
                                            'tipo_documento.nombre as tipo_documento',
                                            'documento.materia as materia',
                                            'documento.identificador as identificador',
                                            'documento.created_at as fecha_documento',
                                            'buzon.nombre as buzon_origen'
                                            )
                                        ->where('documento_favorito_usuario.id_usuario','=',$datosRequest['id_usuario'])
                                        ->whereNull('documento_buzon.id_documento_buzon_padre')
                                        //->orderBy('documento.identificador','desc')
                                        ->get();                                    
                
                return $this->respondSuccess($datosDocumentoFavorito, 200);

            }
            catch (ModelNotFoundException $e)
            {
                return $this->respondError('No existen datos', 500);
            }
        }
        else
            return $this->respondError('Json inválido', 406);
    }

    public function verFavorito(Request $request)
    {
        if($request->isJson())
        {
            try 
            {
                $datosRequest = $request->json()->all();
                
                //$validator = $this->validator->validateFieldUser($datosRequest);
                //if ($validator->fails())
                  //  return $this->respondFail('Falla al obtener documento: revisar datos de entrada');

                $datosDocumento = Documento::findOrFail($datosRequest['id_documento'],['id_documento','id_tipo_documento', 'id_nivel_acceso', 'efectos_terceros', 'json_tipo_documento', 'json_respuesta_a',
                                                                                    'materia', 'anterior', 'descripcion', 'cuerpo', 'fecha', 'identificador', 'hash_validacion', 'folio', 'encabezado']);
                
                return $this->respondSuccess($datosDocumento, 200);
            }  
            catch (ModelNotFoundException $e) 
            {
                return $this->respondError('Documento no existe', 500);
            } 
        }
        else 
            return $this->respondError('Json inválido', 406);
    }
    
    public function estadoFavorito(Request $request)
    {
        if($request->isJson())
        {
            try 
            {
                $datosRequest = $request->json()->all();
                
                //$validator = $this->validator->validateFieldUser($datosRequest);
                // if ($validator->fails())
                 //   return $this->respondFail('Falla del servicio, documento inválido');

                //$datosDocumento = DocumentoBuzon::findOrFail($datosRequest['id_documento_buzon']);      
                
                //$datosDocumento->update(['favorito' => $datosRequest['estado']]);    
                
                if ($datosRequest['accion'] == 1) //agregar
                {
                    $addFavorito = DocumentoFavoritoUsuario::create([
                        'id_documento' => $datosRequest['id_documento'],                    
                        'id_usuario' => $datosRequest['id_usuario']
                    ]);

                    return $this->respondSuccess(array('comentario' => "Favorito agregado"), 200);
                }

                if ($datosRequest['accion'] == 2) //quitar
                {
                    DocumentoFavoritoUsuario::where(['id_documento' => $datosRequest['id_documento'], 'id_usuario' => $datosRequest['id_usuario']])->delete();

                    return $this->respondSuccess(array('comentario' => "Favorito eliminado"), 200);
                }
                
            }  
            catch (ModelNotFoundException $e) 
            {
                return $this->respondError('Error al procesar favorito', 500);
            } 
        }
        else 
            return $this->respondError('Json inválido', 406);

    }

    public function listarDocumentosBitacora(Request $request){
        if($request->isJson())
        {
            try
            {
                $datosRequest = $request->json()->all();
                /* buzon origen documento_buzon.id_buzon si id_documento_buzon_padre is null, sino buzon destino */
                /* y buzon origen es documento_buzon.id_buzon donde id_documento_buzon = id_documento_buzon_padre  */
                return datatables(
                    DB::table('documento_buzon_bitacora')
                    ->join('documento_buzon', 'documento_buzon_bitacora.id_documento_buzon', '=', 'documento_buzon.id_documento_buzon')
                    ->join('documento', 'documento_buzon.id_documento', '=', 'documento.id_documento')
                    ->join('buzon', 'documento_buzon.id_buzon', '=', 'buzon.id_buzon')
                    ->join('users', 'users.id', '=', 'documento_buzon_bitacora.id_usuario')
                    ->select(
                        'documento_buzon_bitacora.id_accion as accion',
                        'documento_buzon_bitacora.fecha as fecha_documento',
                        'buzon.nombre as buzon_destino',
                        'documento_buzon_bitacora.id_accion as accion',
                        'documento_buzon_bitacora.comentario',
                        'documento_buzon_bitacora.mensaje_respuesta',
                        'documento_buzon.id_documento_buzon as id_documento_buzon',
                        'documento_buzon.id_tipo_destino as tipo_destino',
                        'documento.identificador as identificador',
                        'documento.materia as materia',
                        'documento_buzon.comentario_principal', 
                        'documento_buzon.comentario_secundario',
                        DB::raw('users.nombres || \' \' || users.primer_apellido as nombre_usuario'),
                        //DB::raw('(select id_buzon from documento_buzon db2 where db2.id_documento_buzon = documento_buzon.id_documento_buzon_padre) as buzon_origen'),
                        DB::raw('(case when documento_buzon.id_documento_buzon_padre is not null then (select id_buzon from documento_buzon db2 where db2.id_documento_buzon = documento_buzon.id_documento_buzon_padre) else documento_buzon.id_buzon end) as buzon_origen'),
                        )
                          
                     ->where('documento_buzon.id_documento','=',$datosRequest['id_documento']) 
                    ->where('id_accion','<>','1')  
                    ->orderBy('id_documento_buzon_bitacora')                 
                )
                ->toJson();

            }
            catch (ModelNotFoundException $e)
            {
                return $this->respondError('No existen datos', 500);
            }
        }
        else
            return $this->respondError('Json inválido', 406);
    }    

    public function listarDocumentos(Request $request){
        if($request->isJson())
        {
            try
            {

                $datosRequest = $request->json()->all();

                $validator = $this->validator->validateFieldUser($datosRequest);

                if ($validator->fails())
                    return $this->respondFail('Falla al listar los documentos: revisar datos de entrada');


                return datatables(
                    DB::table('documento_buzon')
                    ->join('documento', 'documento_buzon.id_documento', '=', 'documento.id_documento')
                    ->join('estado_documento', 'documento_buzon.id_estado_documento', '=', 'estado_documento.id_estado_documento')
                    ->join('tipo_documento', 'documento.id_tipo_documento', '=', 'tipo_documento.id_tipo_documento')
                    ->join('tipo_origen', 'tipo_documento.id_tipo_origen', '=', 'tipo_origen.id_tipo_origen')
                    ->join('buzon', 'documento_buzon.id_buzon', '=', 'buzon.id_buzon')
                    ->join('buzon_usuario', 'buzon.id_buzon', '=', 'buzon_usuario.id_buzon')
                    ->select(
                        'buzon.nombre as nombre_buzon',
                        'estado_documento.nombre_corto as estado_documento',
                        'documento.id_documento as id_documento',
                        'documento.identificador as identificador',
                        'documento.fecha as fecha_documento',
                        'tipo_documento.nombre as tipo_documento',
                        'tipo_origen.nombre as origen',
                        'documento.materia as materia',
                        'documento_buzon.favorito as estado_favorito',
                        'documento_buzon.id_documento_buzon as id_documento_buzon',
                        'documento.folio as folio',
                        'documento.id_nivel_acceso as nivel_acceso'
                        )
                    ->where('buzon_usuario.id_usuario','=',$datosRequest['id_usuario'])
                    ->where('documento.id_nivel_acceso','=',1)
                )
                ->toJson();

            }
            catch (ModelNotFoundException $e)
            {
                return $this->respondError('No existen datos', 500);
            }
        }
        else
            return $this->respondError('Json inválido', 406);
    }







    public function verificaDocumento(Request $request){
        if($request->isJson())
        {
            try
            {

                $datosRequest = $request->json()->all();
                $codigo = $datosRequest['hash_validacion'];

                $validator = $this->validator->validateFieldUser($datosRequest);

                //if ($validator->fails())
                  //  return $this->respondFail('Falla al listar los documentos: revisar datos de entrada');
                return datatables(
                    DB::table('documento_buzon')
                    ->join('documento', 'documento_buzon.id_documento', '=', 'documento.id_documento')
                    ->join('documento_buzon_archivo', 'documento_buzon_archivo.id_documento_buzon', '=', 'documento_buzon.id_documento_buzon')
                    
                    
                    ->select(
                        'documento.id_documento as id_documento',
                        'documento.id_nivel_acceso as id_nivel_acceso',
                        'documento.identificador as identificador',
                        'documento.fecha as fecha_documento',
                        'documento.materia as materia',
                        'documento.folio as folio',
                        'documento.hash_validacion as hash_validacion',
                        'documento_buzon_archivo.version as version'
                        
                        
                        )
                    ->where('documento.hash_validacion','=',$datosRequest['hash_validacion'])
                    ->where('documento_buzon_archivo.version','=', 1)
                    //->where('documento_buzon.favorito','=',1)
                )
                ->toJson();

            }
            catch (ModelNotFoundException $e)
            {
                return $this->respondError('No existen datos', 500);
            }
        }
        else
            return $this->respondError('Json inválido', 406);
    }


    public function hash(Request $request)
    {
        if($request->isJson())
        {
            try
            {
                DB::beginTransaction();

                $datosDocumento = $request->json()->all();                
                $hashValidacion = $datosDocumento['codigo'];
               
                
                
                
                
                
                

                return $this->respondSuccess($hashValidacion, 201);

            }
            catch (ModelNotFoundException $e)
            {
                DB::rollBack();

                return $this->respondError('Falla al crear documento:' . $e->getMessage(), 500);
            }
        }
        else
            return $this->respondError('Json inválido', 406);

    }


}
