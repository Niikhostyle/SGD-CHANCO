<?php
namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use App\Models\Documento;
use App\Models\DocumentoBuzon;
use App\Models\DocumentoBuzonBitacora;
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

                $datosTipoDoc = TipoDocumento::findOrFail($datosDocumento['id_tipo_documento']);

                $nFolio = null;

                if( $datosTipoDoc->id_tipo_asignacion_folio == 1) //creación
                    $nFolio = rand(); //servicio folio

                $msVerTipoDoc = Http::withHeaders(['key'=>$request->header('key'),'Content-Type'=>'application/json']) //
                ->timeout(30)
                ->withBody(json_encode([
                    'id_tipo_documento' => $nTipoDoc,
                ]), 'json')
                ->get('http://sgd_ms_tipos_documentos:3333/api/sgd-tipodoc/ver');

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
                    'cuerpo' => $datosDocumento['cuerpo'],
                    'fecha' => $dFechaCreacion,
                    'hash_validacion' => 'XyZ987',
                    'folio' => $nFolio,
                    'encabezado' => $datosDocumento['encabezado']
                ]);
                
                $documento = $documento->fresh();
                
                $documentoBuzon = DocumentoBuzon::create([
                    'id_documento' => $documento->id_documento,
                    'id_buzon' => $datosDocumento['id_buzon'],
                    'id_carpeta' => 3,
                    'id_estado_documento' => 1,
                    'id_tipo_destino' => 1,
                    //'id_documento_buzon_padre' => '',
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
            try {

                DB::beginTransaction();

                $datosRequest = $request->json()->all();
                
                //$validator = $this->validator->validateUpdate();

                //if ($validator->fails())
                //    return $this->respondFail('Falla al actualizar buzón: revisar datos de entrada');

                $datosDocumento = Documento::findOrFail($datosRequest['id_documento']);
                $datoDocumentoBuzon = DocumentoBuzon::findOrFail($datosRequest['id_documento_buzon']);
                
                if ($datosDocumento)
                {   
                    $datosDocumento->update($datosRequest);
                   
                    $dFechaCreacion = date('Y-m-d H:i:s');

                    //si viene destinatario principal se agrega un registro
                    
                    if ($datosRequest['destinatarioPrincipal'] != "")
                    {
                        //verificar si se crea o actualiza

                        //busca y crea sino encuentra por id documento, tipo destinatario, buzon padre 
                        $documentoBuzon = DocumentoBuzon::updateOrCreate([
                            'id_documento' => $datosRequest['id_documento'],
                            'id_tipo_destino' => 1,
                            'id_documento_buzon_padre' => $datosRequest['id_buzon'],
                            'id_estado_documento' => 1,
                        ],[
                            'id_buzon' => $datosRequest['destinatarioPrincipal'],
                            'id_carpeta' => 1,
                            'id_estado_documento' => 1,
                            'fecha' => $dFechaCreacion,
                            //'json_acciones'=>
                            'comentario_principal' => $datosRequest['comentarioPrincipal'], 
                            'contestar_hasta' => $datosRequest['contestar_hasta'],
                            'notificado' => false,
                            'recibido' => false,
                            'favorito' => false    
                        ]);

                        //pendiente - crear orden 0 en flujo
                    }

                    //si viene destinatario secundario se agrega registro

                    if ($datosRequest['destinatarioOtros'] != "")
                    {
                        //verificar si se crea o actualiza

                        //busca y crea sino encuentra por id documento, tipo destinatario, buzon padre, id_buzon 

                        $aOtrosDestinatarios = explode (',', $datosRequest['destinatarioOtros']);

                        foreach ($aOtrosDestinatarios as $destinatario)
                        {
                            $documentoBuzon = DocumentoBuzon::updateOrCreate([
                                'id_documento' => $datosRequest['id_documento'],
                                'id_tipo_destino' => 2,
                                'id_documento_buzon_padre' => $datosRequest['id_buzon'],
                                'id_buzon' => $destinatario,
                                'id_estado_documento' => 1,
                            ],[                                                
                                'id_carpeta' => 1,                                
                                'id_buzon' => $destinatario,                                
                                'fecha' => $dFechaCreacion,
                                //'json_acciones'=>
                                'comentario_secundario' => $datosRequest['comentarioOtros'], 
                                'contestar_hasta' => $datosRequest['contestar_hasta'],
                                'notificado' => false,
                                'recibido' => false,
                                'favorito' => false    
                            ]);
                        }

                        // pendiente eliminar
                    }                    
                    
                    DB::commit();

                    return $this->respondSuccess($datosDocumento, 200);

                }
                else
                {
                    return $this->respondError('Falla al actualizar documento:', 500);
                }

                
                
            } catch (ModelNotFoundException $e) {
                DB::rollBack();

                return $this->respondError('Falla al actualizar documento:' . $e->getMessage(), 500);
            }
        }
        else
            return $this->respondError('Json inválido', 406);

    }

    public function enviar(Request $request)
    {
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
                
                $datosDocumentoBuzon = DocumentoBuzon::where('id_documento', $datosRequest['id_documento'])
                                                     ->where('id_buzon', $datosRequest['id_buzon'])
                                                     ->where('id_estado_documento', '1')
                                                     ->where('id_documento_buzon_padre', null)
                                                     ->update(['id_estado_documento' => 2]);
              
                $datosDocumentoBuzonD1 = DocumentoBuzon::where('id_documento', $datosRequest['id_documento'])
                                ->where('id_buzon', $datosRequest['destinatarioPrincipal'])
                                ->where('id_estado_documento', '1')
                                ->where('id_tipo_destino', '1')
                                ->where('id_documento_buzon_padre', $datosRequest['id_buzon'])
                                ->select('id_documento_buzon')                                
                                ->first();
                $datosDocumentoBuzonD1->update(['id_estado_documento' => 3]);

                $aOtrosDestinatarios = explode (',', $datosRequest['destinatarioOtros']);
                $datosDocumentoBuzonD2 = DocumentoBuzon::where('id_documento', $datosRequest['id_documento'])
                                ->whereIn('id_buzon', $aOtrosDestinatarios)
                                ->where('id_estado_documento', '1')
                                ->where('id_tipo_destino', '2')
                                ->where('id_documento_buzon_padre', $datosRequest['id_buzon'])
                                ->select('id_documento_buzon')   
                                ->get();  
                foreach ($datosDocumentoBuzonD2 as $dato)
                {
                    DocumentoBuzon::find($dato["id_documento_buzon"])->update(['id_estado_documento' => 3]);
                }                   
                                 
                $documentoBuzonBitacoraD1 = DocumentoBuzonBitacora::create([
                                    'id_documento_buzon' => $datosDocumentoBuzonD1["id_documento_buzon"],
                                    'id_accion' => 2,
                                    'fecha' => $dFechaCreacion,
                                    'id_usuario' => $datosRequest['id_usuario']
                ]);              
                
                foreach ($datosDocumentoBuzonD2 as $dato)
                {
                    $documentoBuzonBitacoraD2 = DocumentoBuzonBitacora::create([
                                'id_documento_buzon' => $dato["id_documento_buzon"],
                                'id_accion' => 2,
                                'fecha' => $dFechaCreacion,
                                'id_usuario' => $datosRequest['id_usuario']
                    ]);
                    
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

    public function recibir(Request $request)
    {
        if ($request->isJson())
        {
            try 
            {
                DB::beginTransaction();

                $datosRequest = $request->json()->all();

                $dFechaCreacion = date('Y-m-d H:i:s');
                
                $datosDocumentoBuzon = DocumentoBuzon::where('id_documento', $datosRequest['id_documento'])
                                                     ->where('id_documento_buzon_padre', $datosRequest['id_buzon'])
                                                     ->where('id_estado_documento', '3')
                                                     ->update(['id_estado_documento' => 4, 'id_carpeta' => 2]);              

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
                $datosDocumento->rel_tipo_documento;
                                
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



    public function listarFavoritos(Request $request){
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
                        'documento.fecha as fecha_documento',
                        'tipo_documento.nombre as tipo_documento',
                        'tipo_origen.nombre as origen',
                        'documento.materia as materia',
                        'documento_buzon.favorito as estado_favorito'
                        )
                    ->where('buzon_usuario.id_usuario','=',$datosRequest['id_usuario'])
                    ->where('documento_buzon.favorito','=',1)
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
