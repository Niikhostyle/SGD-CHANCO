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
use JeroenNoten\LaravelAdminLte\Components\Tool\Datatable;
use PhpParser\Node\Stmt\TryCatch;

class DocumentoController extends Controller{


    public function listar(Request $request){
        if($request->isJson())
        {
            try
            {

                $datosRequest = $request->json()->all();
                //$validator = $this->validator->validateFieldBuzon($datosRequest);

                return datatables(
                    DB::table('documento_buzon')
                    ->join('documento', 'documento_buzon.id_documento', '=', 'documento.id_documento')
                    ->join('estado_documento', 'documento_buzon.id_estado_documento', '=', 'estado_documento.id_estado_documento')
                    ->join('tipo_documento', 'documento.id_tipo_documento', '=', 'tipo_documento.id_tipo_documento')
                    //->leftJoin('documento_buzon_bitacora', 'documento.id_tipo_documento', '=', 'documento_buzon_bitacora.id_tipo_documento')
                    ->leftJoin('documento_buzon_bitacora', function ($join) {
                        $join->on('documento_buzon.id_documento_buzon', '=', 'documento_buzon_bitacora.id_documento_buzon')
                             ->where('documento_buzon_bitacora.id_accion', '=', 3);
                    })
                    ->select(
                        'documento_buzon.id_buzon',
                        'documento_buzon.recibido',
                        'estado_documento.nombre_corto',
                        'documento_buzon.fecha',
                        "documento_buzon_bitacora.fecha as fecha_recepcion",
                        'tipo_documento.nombre as tipo_documento',
                        'documento_buzon.json_acciones as destinatario',
                        'documento.materia',
                        'documento.json_respuesta_a as respuesta_a',
                        'documento.fecha as fecha_documento'
                        )
                    ->where('documento_buzon.id_buzon','=',$datosRequest['id_buzon'])
                    ->where('documento_buzon.id_carpeta','=',$datosRequest['id_carpeta'])
                    ->whereIn('documento_buzon.id_estado_documento',array(1,2))
                )->toJson();
                    /*
                    id_buzon            documento_buzon->id_buzon
                    palomitas           documento_buzon->recibido
                    estado              documento_buzon->id_estado_documento / estado_documento->nombre_corto
                    fecha_despacho      documento_buzon->fecha
                 **   fecha_recepcion     ??? si está en (documento_buzon.id_estado_documento==4) "pendiente" La fecha se obtiene de la tabla docuento buzon bitacora campo fecha cuando esta en el id_accion
                    tipo_decumento      documento->id_tipo_documento / tipo_documento->nombre
                    destinatario        documento_buzon->json_acciones
                    materia             documento->materia
                    respuesta_a         documento->json_respuesta_a [{id_documento}]
                    fecha_documento     documento->fecha
                    */
            }
            catch (ModelNotFoundException $e)
            {
                return $this->respondError('No existe buzón', 500);
            }
        }
        else
            return $this->respondError('Json inválido', 406);
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

}
