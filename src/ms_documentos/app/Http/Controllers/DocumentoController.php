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

                $datosTipoDoc = Http::withHeaders(['key'=>$request->header('key'),'Content-Type'=>'application/json']) //
                ->timeout(30)
                ->withBody(json_encode([
                    'id_tipo_documento' => $nTipoDoc,
                ]), 'json')
                ->get('http://sgd_ms_tipos_documentos:3333/api/sgd-tipodoc/ver');

                //return $datosTipoDoc->json();

                $dFechaCreacion = date('Y-m-d H:i:s');
                //$documento = Documento::create($datosDocumento);
                $documento = Documento::create([
                    'id_tipo_documento' => $datosDocumento['id_tipo_documento'],
                    'id_nivel_acceso' => $datosDocumento['id_nivel_acceso'],
                    'efectos_terceros' => $datosDocumento['efectos_terceros'],
                    'json_tipo_documento' => null, //obtener de ms_tipos_documentos
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

    public function ver(Request $request)
    {
        if($request->isJson())
        {
            try 
            {
                $datosRequest = $request->json()->all();
                
                $validator = $this->validator->validateFieldUser($datosRequest);
                if ($validator->fails())
                    return $this->respondFail('Falla al obtener documento: revisar datos de entrada');

                $datosDocumento = Documento::findOrFail($datosRequest['id_documento'],['id_tipo_documento', 'id_nivel_acceso', 'efectos_terceros', 'json_tipo_documento', 'json_respuesta_a',
                                                                                    'materia', 'anterior', 'descripcion', 'cuerpo', 'fecha', 'hash_validacion', 'folio', 'encabezado']);
                
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

}
