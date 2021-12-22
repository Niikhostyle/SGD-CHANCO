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
use App\Models\TipoDocumento;
use Illuminate\Support\Facades\DB;
use App\Validator\DocumentoValidator;


class DocumentoController extends Controller{

    /**
     * @BuzonValidator
     */
    private $validator;

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

                $msVerTipoDoc = Http::withHeaders(['key'=>$request->header('key'),'Content-Type'=>'application/json']) //
                ->timeout(30)
                ->withBody(json_encode([
                    'id_tipo_documento' => $nTipoDoc,
                ]), 'json')
                ->get('http://sgd_ms_tipos_documentos:3333/api/sgd-tipodoc/ver');

                $nFolio = null;

                if( $msVerTipoDoc['data']['id_tipo_asignacion_folio'] == 1) //creación
                    $nFolio = rand(); //servicio folio

                $dFechaCreacion = date('Y-m-d H:i:s');
                
                $jsonTipoDocumento = $msVerTipoDoc->json();
               
                $documento = Documento::create([
                    'id_tipo_documento' => $datosDocumento['id_tipo_documento'],
                    'id_nivel_acceso' => $datosDocumento['id_nivel_acceso'],
                    'efectos_terceros' => $datosDocumento['efectos_terceros'],
                    'json_tipo_documento' => json_encode($msVerTipoDoc['data']), //obtener de ms_tipos_documentos
                    'json_respuesta_a' => $datosDocumento['json_respuesta_a'],
                    'materia' => $datosDocumento['materia'],
                    'anterior' => $datosDocumento['anterior'],
                    'descripcion' => $datosDocumento['descripcion'],
                    'encabezado' => $datosDocumento['encabezado'],
                    'cuerpo' => $datosDocumento['cuerpo'],
                    'fecha' => $dFechaCreacion,
                    'hash_validacion' => 'XyZ987',
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
                
                $documento->rel_documento_buzon;

                DB::commit();

                return $this->respondSuccess($documento, 201);

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
                    if ($datosRequest['opcionGuardar'] != 1 || $datosRequest['opcionGuardar'] == null || $datosRequest['opcionGuardar'] == '')
                        $datosDocumento->update($datosRequest);
                    
                    $dFechaCreacion = date('Y-m-d H:i:s');                   
                    
                    //si viene destinatario principal se agrega un registro
                    
                    $jsonAcciones = array();                    
                    
                    if ($datosRequest['acciones_solicitadas'] != "" || $datosRequest['acciones_solicitadas'] != null)
                    {    foreach($datosRequest['acciones_solicitadas'] as $accion)
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

                    if ($datosRequest['fileDelete'] != null)
                    {
                        $aFilesDelete = explode (',', $datosRequest['fileDelete']);
                
                        DocumentoBuzonArchivo::whereIn('id_documento_buzon_archivo', $aFilesDelete)->delete();

                    }

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
                }

                if ($datosRequest['carpeta'] == 2) //recibidos
                {
                    $estadoDocumentoFinal = 7;        
                    $estadoDocumentoActual = array('4','9','11'); //"4,9,11"; deberia ir con whereIn   
                }
                
                DocumentoBuzon::find($datosRequest["id_documento_buzon"])->update(['id_estado_documento' => $estadoDocumentoFinal]);
                
                $datosDocumentoBuzonD1 = DocumentoBuzon::where('id_documento', $datosRequest['id_documento'])
                                ->where('id_documento_buzon_padre', $datosRequest['id_documento_buzon'])
                                ->where('id_tipo_destino', '1')
                                ->whereIn('id_estado_documento', $estadoDocumentoActual)
                                ->where('id_buzon', $datosRequest['destinatarioPrincipal'])
                                ->select('id_documento_buzon')                                
                                ->first();
                $datosDocumentoBuzonD1->update(['id_estado_documento' => 3]);
 
                $documentoBuzonBitacoraD1 = DocumentoBuzonBitacora::create([
                                    'id_documento_buzon' => $datosDocumentoBuzonD1["id_documento_buzon"],
                                    'id_accion' => 2,
                                    'fecha' => $dFechaCreacion,
                                    'id_usuario' => $datosRequest['id_usuario']
                ]);   

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
                        DocumentoBuzon::find($dato["id_documento_buzon"])->update(['id_estado_documento' => 3]);
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

/*    public function derivar(Request $request)
    {
        
        if ($request->isJson())
        {
            try 
            {
                DB::beginTransaction();

                $datosRequest = $request->json()->all();

                $dFechaCreacion = date('Y-m-d H:i:s');
                
                if ($datosRequest['destinatarioPrincipal'] != "")
                {
                    $documentoBuzon = DocumentoBuzon::create([
                        'id_documento' => $datosRequest->id_documento,
                        'id_buzon' => $datosRequest['destinatarioPrincipal'],
                        'id_carpeta' => 1,
                        'id_estado_documento' => 3,
                        'id_tipo_destino' => 1,
                        'id_documento_buzon_padre' => $datosRequest['id_buzon'],
                        'fecha' => $dFechaCreacion,
                        //'contestar_hasta' => $datosDocumento['contestar_hasta'],
                        'notificado' => false,
                        'recibido' => false,
                        'favorito' => false
                    ]);                    
                   
                }

                //si viene destinatario secundario se agrega registro

                if ($datosRequest['destinatarioOtros'] != "")
                {
                    $aOtrosDestinatarios = explode (',', $datosRequest['destinatarioOtros']);

                    foreach ($aOtrosDestinatarios as $destinatario)
                    {
                        $documentoBuzon = DocumentoBuzon::create([
                            'id_documento' => $datosRequest->id_documento,
                            'id_buzon' => $destinatario,
                            'id_carpeta' => 1,
                            'id_estado_documento' => 3,
                            'id_tipo_destino' => 2,
                            'id_documento_buzon_padre' => $datosRequest['id_buzon'],
                            'fecha' => $dFechaCreacion,
                            //'contestar_hasta' => $datosDocumento['contestar_hasta'],
                            'notificado' => false,
                            'recibido' => false,
                            'favorito' => false
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
*/
    public function actualizar_estado(Request $request)
    {
        if ($request->isJson())
        {
            try 
            {
                DB::beginTransaction();

                $datosRequest = $request->json()->all();

                $dFechaCreacion = date('Y-m-d H:i:s');

                if ($request->estado == 3) // por recibir
                    DocumentoBuzon::find($datosRequest["id_documento_buzon"])->update(['id_estado_documento' => 4, 'id_carpeta' => 2]);
                
                if ($request->estado == 11) // visar
                    DocumentoBuzon::find($datosRequest["id_documento_buzon"])->update(['id_estado_documento' => 11]);

                DB::commit();

                return $this->respondSuccess("Documento recepcionado", 200);

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

                DocumentoBuzon::find($datosRequest["id_documento_buzon"])->update(['id_estado_documento' => 6]);

               /* 
                $datosDocumentoBuzon = DocumentoBuzon::where('id_documento', $datosRequest['id_documento'])
                                                     ->where('id_estado_documento', '4')
                                                     ->where('id_carpeta', '2')
                                                     ->where('id_buzon', $datosRequest['id_buzon'])
                                                     ->select('id_documento_buzon') 
                                                     ->first();
                $datosDocumentoBuzon->update(['id_estado_documento' => 6]);  
                */

                $documentoBuzonBitacora = DocumentoBuzonBitacora::create([
                                            'id_documento_buzon' => $datosRequest["id_documento_buzon"],
                                            'id_accion' => 12,
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
                                    ->where('documento.id_documento','=','155')
                                    ->whereRaw('documento_buzon.id_documento_buzon_padre = (select id_documento_buzon_padre from documento_buzon where id_documento = 155 order by id_documento_buzon desc limit 1)')
                                    ->select('documento_buzon.*')
                                    ->get();

                $datosDocumento['rel_documento_buzon_actual'] =  $datosVerDoc;                  
                
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
                                                    ->get();
                            
                $datosDocumento['rel_archivos'] =  $datosDocumentoBuzon;
                              
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
                                            'documento.fecha as fecha_documento',
                                            'buzon.nombre as buzon_origen'
                                            )
                                        ->where('documento_favorito_usuario.id_usuario','=',$datosRequest['id_usuario'])
                                        ->whereNull('documento_buzon.id_documento_buzon_padre')
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
                    ->select(
                        'documento_buzon_bitacora.id_accion as accion',
                        'documento_buzon_bitacora.fecha as fecha_documento',
                        'buzon.nombre as buzon_destino',
                        'documento_buzon_bitacora.id_accion as accion',
                        'documento_buzon.id_documento_buzon as id_documento_buzon',
                        'documento_buzon.id_tipo_destino as tipo_destino',
                        'documento.identificador as identificador',
                        'documento.materia as materia',
                        'documento_buzon.comentario_principal', 
                        'documento_buzon.comentario_secundario',
                        DB::raw('(select id_buzon from documento_buzon db2 where db2.id_documento_buzon = documento_buzon.id_documento_buzon_padre) as buzon_origen'),
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
                        )
                    ->where('buzon_usuario.id_usuario','=',$datosRequest['id_usuario'])
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

}
