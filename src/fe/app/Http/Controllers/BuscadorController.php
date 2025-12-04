<?php

namespace App\Http\Controllers;

use App\Models\Buzon;
use App\Models\Documento;
use App\Models\DocumentoBuzon;
use App\Models\DocumentoBuzonArchivo;
use App\Models\DocumentoBuzonArchivoDescarga;
use App\Models\DocumentoBuzonBitacora;
use App\Models\TipoDocumento;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use League\CommonMark\Node\Block\Document;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

use PDF;
use stdClass;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;


class BuscadorController extends Controller
{
    public function index()
    {

        $sesion_key =  AppServiceProvider::session_key_general();
        $lista_documento = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(10)
            ->withBody(json_encode([
                'id_usuario' => Auth::user()->id,
            ]), 'json')
            ->get(config('sgd.api_documento').'/api/sgd-documentos/listarDocumentos');

        if ($lista_documento->failed()) {
            $mensaje = $lista_documento->json()['data']['comentario'];

            $lista_documento = ['data' => [
                0 => ['id_documento' => '', 'rel_documento_buzon' => '', 'id_tipo_documento' => '', 'folio' => '', 'rel_documento_buzon' => '', 'rel_documento_buzon' => '', 'materia' => '']
            ]];
            toast($mensaje, 'error');
        } else {
            $lista_documento->json();
        }

        /* LISTADO TIPO DE DOCUMENTO */

        $listado_tiposdoc = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(30)
            ->get(config('sgd.api_tipo_documento').'/api/sgd-tipodoc/ver_todos');

        if ($listado_tiposdoc->failed()) {
            $mensaje = $listado_tiposdoc->json()['data']['comentario'];

            toast($mensaje, 'error');
        } else {
            $datosTipoDoc = $listado_tiposdoc['data'];
        }

        /* LISTADO BUZONES */
        $datosBuzones = array();
        $aBuzones = array();

        $listado_buzones = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(30)
            ->withBody(json_encode([
                'texto_busqueda' => '',
            ]), 'json')
            ->get(config('sgd.api_buzones').'/api/sgd-buzones/listar_todos');

        if ($listado_buzones->failed()) {
            $mensaje = $listado_buzones->json()['data']['comentario'];

            $listado_buzones = ['data' => [
                0 => ['id' => '0', 'nombre' => 'Sin Datos']
            ]];
            //toast($mensaje,'error');
        } else {

            $datosBuzones = $listado_buzones['data'];
            foreach ($listado_buzones['data'] as $dato) {
                $aBuzones[$dato['id_buzon']] = $dato['nombre'];
            }
        }

        $datosNivelAcceso = \App\Models\NivelAcceso::all('id_nivel_acceso', 'nombre');
        $datosAccion = \App\Models\Accion::all('id_accion', 'id_tipo_accion','nombre');
        $datosAnios = \App\Models\Anio::all('id_anio', 'descripcion','estado');
        $datosEstadoTramitacion = \App\Models\EstadoTramitacion::all();


        return View::make('buscador.index', [
            'lista_documento' => $lista_documento,
            'listado_tiposdoc' => $datosTipoDoc,
            'listBuzones' => $datosBuzones,
            'listadoBuzones' => $aBuzones,
            'listadoAcciones' => $datosAccion,
            'listadoEstadoTramitacion' => $datosEstadoTramitacion,
            'nivel_acceso' => $datosNivelAcceso,
            'listadoAnios' => $datosAnios
        ]);
    }

    public function index2()
    {

        $sesion_key =  AppServiceProvider::session_key_general();
        $lista_documento = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(10)
            ->withBody(json_encode([
                'id_usuario' => Auth::user()->id,
            ]), 'json')
            ->get(config('sgd.api_documento').'/api/sgd-documentos/listarDocumentos');

        if ($lista_documento->failed()) {
            $mensaje = $lista_documento->json()['data']['comentario'];

            $lista_documento = ['data' => [
                0 => ['id_documento' => '', 'rel_documento_buzon' => '', 'id_tipo_documento' => '', 'folio' => '', 'rel_documento_buzon' => '', 'rel_documento_buzon' => '', 'materia' => '']
            ]];
            toast($mensaje, 'error');
        } else {
            $lista_documento->json();
        }

        /* LISTADO TIPO DE DOCUMENTO */

        $listado_tiposdoc = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(30)
            ->get(config('sgd.api_tipo_documento').'/api/sgd-tipodoc/ver_todos');

        if ($listado_tiposdoc->failed()) {
            $mensaje = $listado_tiposdoc->json()['data']['comentario'];

            toast($mensaje, 'error');
        } else {
            $datosTipoDoc = $listado_tiposdoc['data'];
        }

        /* LISTADO BUZONES */
        $datosBuzones = array();
        $aBuzones = array();

        $listado_buzones = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(30)
            ->withBody(json_encode([
                'texto_busqueda' => '',
            ]), 'json')
            ->get(config('sgd.api_buzones').'/api/sgd-buzones/listar_todos');

        if ($listado_buzones->failed()) {
            $mensaje = $listado_buzones->json()['data']['comentario'];

            $listado_buzones = ['data' => [
                0 => ['id' => '0', 'nombre' => 'Sin Datos']
            ]];
            //toast($mensaje,'error');
        } else {

            $datosBuzones = $listado_buzones['data'];
            foreach ($listado_buzones['data'] as $dato) {
                $aBuzones[$dato['id_buzon']] = $dato['nombre'];
            }
        }

        //parametros

        $datosNivelAcceso = \App\Models\NivelAcceso::all('id_nivel_acceso', 'nombre');
        $datosAccion = \App\Models\Accion::all('id_accion', 'id_tipo_accion','nombre');
        $datosAnios = \App\Models\Anio::all('id_anio', 'descripcion','estado');

        return View::make('buscador.index2', [
            'lista_documento' => $lista_documento,
            'listado_tiposdoc' => $datosTipoDoc,
            'listBuzones' => $datosBuzones,
            'listadoBuzones' => $aBuzones,
            'listadoAcciones' => $datosAccion,
            'nivel_acceso' => $datosNivelAcceso,
            'listadoAnios' => $datosAnios
        ]);
    }

    public function show($id)
    {
        $sesion_key =  AppServiceProvider::session_key_general();
        $lista_bitacora = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(10)
            ->withBody(json_encode([
                'id_documento' => $id,
            ]), 'json')
            ->get(config('sgd.api_documento').'/api/sgd-documentos/ver')->throw();

        if ($lista_bitacora->failed()) {
            $mensaje = $lista_bitacora->json()['data']['comentario'];

            // $lista_bitacora = ['data' => [
            //     0 => ['accion' => '', 'fecha_documento' => '', 'buzon_origen' => '', 'nombre_accion' => '', 'mensaje_respuesta' => '', 'tipo_destino' => '', 'materia' => '',  'identificador' => '']
            // ]];
            toast($mensaje, 'error');
            return response()->json($lista_bitacora);//->json();
        } else {
            return $lista_bitacora->json();
        }
        
        // $item = Documento::findOrFail($id);
        // return response()->json($item);


    }

    public function bitacora($idDocumento)
    {
        //consultar si exixte registro actualizdo, sino actualizar bitácora
        // $derivaciones =  DocumentoBuzon::where('id_documento', $idDocumento)->get()->pluck('id_documento_buzon')->toArray();
        // $consulta = DocumentoBuzonBitacora::whereIn('id_documento_buzon',$derivaciones)
        // ->where('id_accion', 2)
        // ->whereNotNull('informacion_solicitud')->get()->toArray();
        // if (count($consulta) == 0) {
        //     $this->fixbitacora($idDocumento);
        // }

        $info = Documento::with([
            'tipo_documento' => function($query) {
                $query->select(['id_tipo_documento', 'nombre']);
            },
            'estado_tramitacion' => function($query) {
                $query->select(['id', 'nombre']);
            }])
        ->where('id_documento', $idDocumento)
        ->select(["id_documento","folio","materia","id_tipo_documento","estado_tramitacion","anio_tramitacion"])
        ->first();

        $derivacion = DocumentoBuzon::with(['buzon'=> fn($q) => $q->withTrashed()])->where('id_documento', $idDocumento)->get();
        $arbol = $this->arbolDocumentoBuzon($idDocumento,$derivacion);
        $res = [];
        
        $this->recorrerArrayRecursivo($arbol, function($nodo) use(&$res){
            //dump($nodo);
            foreach ($nodo["bitacora"] as $key => $value) {
                $value["buzon"] = $nodo["buzon"];
                $value["mensaje"] =[];
                $value["nodo"] = $nodo;

                //leer si tiene destino (accion 2)
                if(isset($value["informacion_solicitud"]["buzon_destino"])){
                    //tipo derivacion destino
                    $dest = Buzon::withTrashed()->find($value["informacion_solicitud"]["buzon_destino"]);
                    //$der = DocumentoBuzon::where('id_documento', $value["id_documento"])->where('id_documento_buzon_padre', $value["id_documento_buzon"])->first();
                    if($dest){
                        $value["buzon_destino"] = $dest->nombre;
                        $value["id_tipo_destino"] = $value["informacion_solicitud"]["id_tipo_destino"] ?? $nodo["id_tipo_destino"];
                        $value["mensaje"][] = $value["informacion_solicitud"]["mensaje"] ?? '';
                    } else {
                        $value["buzon_destino"] = null;
                        $value["id_tipo_destino"] = $nodo["id_tipo_destino"];
                    }
                } else {
                    $value["buzon_destino"] = null;
                    $value["id_tipo_destino"] = $nodo["id_tipo_destino"];
                }

                //mensajes u otra información de la solicitud
                if(isset($value["comentario"])) {$value["mensaje"][]= $value["comentario"];}
                if(isset($value["mensaje_respuesta"])) {$value["mensaje"][]= $value["mensaje_respuesta"];}
                $value["mensaje"] = implode('<br>', $value["mensaje"]);
               $res[] = $value;
            }
        });

        return response()->json(['info'=>$info,'data'=>$res]);
    }

    /**
     * Construye un árbol de documentos en el buzón.
     *
     * @param int $idDocumento
     * @param int|null $padreId
     * @return array
     */
    private function arbolDocumentoBuzon($idDocumento,$derivaciones, $padreId = null)
    {
        $hijos = $derivaciones->where('id_documento', $idDocumento)
            ->where('id_documento_buzon_padre', $padreId);
        //dump($hijos);
        $resultado = [];
        foreach ($hijos as $hijo) {
            // Obtén los archivos asociados a este documento_buzon
            $bitacora = DocumentoBuzonBitacora::with([
                'usuario'=>function($query) {
                    $query->select(['nombres','primer_apellido','segundo_apellido', 'id']);
                },
                'accion'=>function($query) {
                    $query->select(['nombre', 'id_accion']);
                }
            ])
                ->where('id_documento_buzon', $hijo->id_documento_buzon)->orderBy('id_documento_buzon_bitacora')->get()->toArray();
            //revisar bitacora       
            $resultado[] = [
                'id_documento_buzon' => $hijo->id_documento_buzon,
                'id_buzon' => $hijo->id_buzon,
                'id_documento' => $hijo->id_documento,
                'id_tipo_destino' => $hijo->id_tipo_destino,
                'id_buzon'=>$hijo->id_buzon,
                'buzon'=>$hijo->buzon->nombre,
                //'usuario' => $hijo->buzon->rel_buzon_usuario->pluck('id_usuario')->toArray(),
                'id_carpeta' => $hijo->id_carpeta,
                'id_estado_documento' => $hijo->id_estado_documento,
                'comentario_principal' => $hijo->comentario_principal,
                'comentario_secundario' => $hijo->comentario_secundario,
                'bitacora'=> $bitacora,
                'hijos' => $this->arbolDocumentoBuzon($idDocumento, $derivaciones,$hijo->id_documento_buzon)
            ];
        }
        return $resultado;
    }


    private function recorrerArrayRecursivo(array $arbol, callable $callback)
    {
        foreach ($arbol as $nodo) {
            // Ejecuta la función callback con el nodo actual
            $callback($nodo);

            // Si el nodo tiene hijos, recorre recursivamente
            if (isset($nodo['hijos']) && is_array($nodo['hijos']) && count($nodo['hijos']) > 0) {
                $this->recorrerArrayRecursivo($nodo['hijos'], $callback);
            }
        }
    }


    public function listar(Request $request)
    {
        $year_actual = session('year');
        //dd($request);
        $extraquery = "";
        // //construir filtro
        $query = $request->busqueda_simple;
        if ($query) {
            $extraquery = " AND (lower(d.materia) like '%" . strtolower($query) . "%'";
            if ((int)$query > 0) {
                $extraquery = $extraquery . " OR d.id_documento=" . (int)$query . " OR d.folio = " . (int)$query . "";
            }
            if (isset($request->inicio)) {
                $extraquery = $extraquery . " OR lower(d.descripcion) like '%" . strtolower($query) . "%' ";
            }
            $extraquery = $extraquery . ")";
        }
        $filtroAvanzado = " and 1 = 1 ";
        if ($request->buscar_id_documento != "") {
            $filtroAvanzado .= " and d.id_documento = " . $request->buscar_id_documento;
        }
        if ($request->buscar_folio != "") {
            if ((int)$request->buscar_folio > 0) {
                $filtroAvanzado .= " and d.folio = " . $request->buscar_folio;
            }
        }
        if ($request->buscar_tipo_documento != "") {
            $filtroAvanzado .= " and lower(td.nombre) = lower('" . $request->buscar_tipo_documento . "')";
        }
        if ($request->buscar_buzon_origen != "" && $request->buscar_derivado == "") {
            $filtroAvanzado .= " and lower(bo.nombre) = lower('" . $request->buscar_buzon_origen . "')";
        }
        if ($request->buscar_buzon_origen != "" && $request->buscar_derivado != "") {
            $filtroAvanzado .= " and lower((case when db.id_documento_buzon_padre is not null 
            then 
                (select b2.nombre from documento_buzon db2 join buzon b2 on b2.id_buzon = db2.id_buzon  where db2.id_documento_buzon = db.id_documento_buzon_padre) 
            else 
            (select b2.nombre from documento_buzon db2 join buzon b2 on b2.id_buzon = db2.id_buzon  where db2.id_documento_buzon = db.id_documento_buzon)
            end) ) = lower('" . $request->buscar_buzon_origen . "')";
        }

        if ($request->buscar_buzon_actual != "") {
            $filtroAvanzado .= " and lower((select b2.nombre from documento_buzon db3 join buzon b2 on b2.id_buzon = db3.id_buzon where db3.id_documento = db.id_documento order by db3.id_documento_buzon desc limit 1) ) = lower('" . $request->buscar_buzon_actual . "')";
        }
        if ($request->terceros != "") {
            $filtroAvanzado .= "and d.efectos_terceros = " . $request->terceros;
        }

        if ($request->respondidos != "") {
            $filtroAvanzado .= " and d2.id_documento  is not null";
        }

        if ($request->estado_tramitacion != "") {
            $filtroAvanzado .= " and d.estado_tramitacion = " . $request->estado_tramitacion;
        }

        if ($request->buscar_derivado != "") {
            $filtroAvanzado .= " and lower((case when db.id_documento_buzon_padre is not null 
            then 
                (select b2.nombre from documento_buzon db2 join buzon b2 on b2.id_buzon = db2.id_buzon  where db2.id_documento_buzon = db.id_documento_buzon_padre) 
            else 
            (select b2.nombre from documento_buzon db2 join buzon b2 on b2.id_buzon = db2.id_buzon  where db2.id_documento_buzon = db.id_documento_buzon)
            end)) = lower('" . $request->buscar_derivado . "') ";
            if (isset($request->buscar_fecha_ini_d)) {
                $filtroAvanzado .= " and dbb.fecha >=  to_date('" . $request->buscar_fecha_ini_d . "','yyyy-mm-dd') ";
            }
            if (isset($request->buscar_fecha_fin_d)) {
                $filtroAvanzado .= " and dbb.fecha <= to_date('" . $request->buscar_fecha_fin_d . "','yyyy-mm-dd')  + INTERVAL '1 day' ";
            }
        }

        if ($request->buscar_anio != "") {
            $extraquery .= " and ( extract(year from d.created_at) = " . $request->buscar_anio . " or extract(year from d.fecha)  = " . $request->buscar_anio . ")";
        }
        //else{
        if (!isset($request->buscar_fecha_ini) || !isset($request->buscar_fecha_fin)) {
            //$extraquery .= " and extract(year from d.created_at) = " . $year_actual;
            $extraquery .= " and d.anio_tramitacion = " . $year_actual;

        } else {
            $extraquery .= " and d.created_at between to_date('" . $request->buscar_fecha_ini . "','yyyy-mm-dd') and to_date('" . $request->buscar_fecha_fin . "','yyyy-mm-dd') + INTERVAL '1 day'";
        }
        //}

        DB::enableQueryLog();
        if ($request->buscar_derivado != "") {
            $datos =  DB::select("select 
                                        d.id_documento ,
                                        d.identificador,
                                        d.id_nivel_acceso,
                                        d.created_at as fecha_documento ,
                                        dbb.fecha as fecha_documento_firma,
                                        d.folio,
                                        d.materia as materia,
                                        d.json_tipo_documento,
                                        td.id_tipo_documento,
                                        td.nombre tipo_documento,
                                        et.nombre as n_estado_tramitacion,
                                        CASE
                                            WHEN (d.efectos_terceros is true) THEN 'true'
                                            ELSE 'false'
                                        END AS efectos_terceros,
                                        (case when db.id_documento_buzon_padre is not null 
                                        then 
                                            (select b2.nombre from documento_buzon db2 join buzon b2 on b2.id_buzon = db2.id_buzon  where db2.id_documento_buzon = db.id_documento_buzon_padre) 
                                        else 
                                        (select b2.nombre from documento_buzon db2 join buzon b2 on b2.id_buzon = db2.id_buzon  where db2.id_documento_buzon = db.id_documento_buzon)
                                        end) 
                                        as buzon_origen,
                                        (select b2.nombre from documento_buzon db3 join buzon b2 on b2.id_buzon = db3.id_buzon where db3.id_documento = db.id_documento order by db3.id_documento_buzon desc limit 1) 
                                        as buzon_actual,
                                        b.nombre as destinatario
                                        ,d2.id_documento as id_respuesta
                                        ,d2.created_at as fecha_respuesta
                                    from 
                                        documento_buzon_bitacora dbb
                                        join documento_buzon db on dbb.id_documento_buzon = db.id_documento_buzon
                                        join documento d on db.id_documento = d.id_documento
                                        join buzon b on db.id_buzon = b.id_buzon
                                        join estado_tramitacion et on et.id = d.estado_tramitacion 
                                        join tipo_documento td on td.id_tipo_documento = d.id_tipo_documento
                                        left join documento d2 on d2.referencias::text like '%\"materia\": \"'||d.materia ||'\"%'
                                    where 
                                        d.id_nivel_acceso < 3
                                        AND db.id_tipo_destino = 1 
                                        and id_accion = 2 
                                        " . $extraquery . $filtroAvanzado . "
                                    ");
        } else {
            $datos =  DB::select("select 
                                        distinct d.id_documento as id_documento
                                        , max(db.id_documento_buzon)
                                        , d.identificador
                                        , d.id_nivel_acceso
                                        , to_char(d.created_at,'yyyy-mm-dd') as fecha_documento
                                        , to_char(d.fecha,'yyyy-mm-dd') as fecha_documento_firma
                                        , d.folio
                                        , d.materia 
                                        , d.json_tipo_documento 
                                        , d.id_tipo_documento 
                                        , td.nombre as tipo_documento
                                        , et.nombre as n_estado_tramitacion
                                        , CASE
                                            WHEN (d.efectos_terceros is true) THEN 'true'
                                            ELSE 'false'
                                        END AS efectos_terceros
                                        , bo.nombre as buzon_origen
                                        , (select b2.nombre from documento_buzon db3 join buzon b2 on b2.id_buzon = db3.id_buzon where db3.id_documento = db.id_documento order by db3.id_documento_buzon desc limit 1) as buzon_actual
                                        ,'' destinatario
                                        ,d2.id_documento as id_respuesta
                                        ,d2.created_at as fecha_respuesta
                                    from 
                                        documento_buzon db 
                                        join documento d on d.id_documento = db.id_documento and db.id_estado_documento > 1
                                        join buzon b on b.id_buzon = db.id_buzon
                                        join tipo_documento td on td.id_tipo_documento = d.id_tipo_documento
                                        join estado_tramitacion et on et.id = d.estado_tramitacion 
                                        left join documento d2 on d2.referencias::text like '%\"materia\": \"'||d.materia ||'\"%'
                                        LEFT JOIN documento_buzon dbo ON db.id_documento = dbo.id_documento  AND dbo.id_documento_buzon_padre is null
                                        LEFT JOIN buzon bo ON bo.id_buzon = dbo.id_buzon
                                    where
                                        d.id_nivel_acceso < 3
                                        AND db.id_tipo_destino = 1 
                                        " . $extraquery . $filtroAvanzado . "
                                    group by d.id_documento
                                        , d.identificador
                                        , d.id_nivel_acceso
                                        , d.created_at        
                                        , d.fecha        
                                        , d.folio        
                                        , d.materia        
                                        , d.json_tipo_documento    
                                        , d.id_tipo_documento 
                                        , td.nombre
                                        , buzon_origen
                                        , buzon_actual
                                        ,d2.id_documento
                                        ,n_estado_tramitacion
                                        ,d2.created_at");
        }
        //dd(DB::getQueryLog());
        //return $datos;exit;
        return datatables($datos)->toJson();
    }

    public function listar2(Request $request)
    {
        //dd($request);
        //filtro rangos de fecha
        $sWhereBetween = "";

        if ($request['searchFromdate'] != '')
            $sWhereBetween .= "and d.created_at >= '" . $request['searchFromdate'] . "'";
        if ($request['searchTodate'] != '')
            $sWhereBetween .= "and d.created_at <= '" . $request['searchTodate'] . "'";

        $datos =  DB::select("select 
        distinct d.id_documento as id_documento
        , d.identificador
        , d.id_nivel_acceso
        , string_agg(cast(bu.id_usuario as varchar), ',') as list_usuarios
        , d.created_at as fecha_documento
        , d.folio
        , d.materia 
        , d.json_tipo_documento 
        , d.id_tipo_documento 
        , td.nombre as tipo_documento
        , us.id        
        , CASE
			WHEN (d.efectos_terceros is true) THEN 'true'
			ELSE 'false'
		END AS efectos_terceros
        , (select b3.nombre from documento_buzon db2 join buzon b3 on b3.id_buzon = db2.id_buzon where db2.id_documento = db.id_documento and db2.id_documento_buzon_padre is null) as buzon_origen 
        , (select b2.nombre from documento_buzon db3 join buzon b2 on b2.id_buzon = db3.id_buzon where db3.id_documento = db.id_documento order by db3.id_documento_buzon desc limit 1) as buzon_actual 
    from 
        documento_buzon db 
        join documento d on d.id_documento = db.id_documento and db.id_estado_documento > 1
        join buzon b on b.id_buzon = db.id_buzon
        join buzon_usuario bu on bu.id_buzon = b.id_buzon
        join tipo_documento td on td.id_tipo_documento = d.id_tipo_documento 
        join users us on us.id = " . Auth::user()->id . " 
    where 	        
        ((d.id_nivel_acceso in (1,3)) 
        or (bu.id_usuario = " . Auth::user()->id . " and d.id_nivel_acceso = 2))
        AND db.id_tipo_destino = 1 
        " . $sWhereBetween . "
    group by d.id_documento
        , d.identificador
        , d.id_nivel_acceso
        , d.created_at        
        , d.folio        
        , d.materia        
        , d.json_tipo_documento    
        , d.id_tipo_documento 
        , us.id    
        , td.nombre
        , buzon_origen
        , buzon_actual");

        // return datatables( [] )->toJson();
        return datatables($datos)->toJson();
    }

    public function descargar(Request $request)
    {
        $nDocumento =  $request->idDocumento;

        $archivo = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon.id_documento_buzon', '=', 'documento_buzon_archivo.id_documento_buzon')
            ->join('buzon', 'documento_buzon.id_buzon', '=', 'buzon.id_buzon')
            ->join('buzon_usuario', 'buzon.id_buzon', '=', 'buzon_usuario.id_buzon')
            ->where('id_documento', $nDocumento)
            ->where('id_tipo_archivo', 1)
            ->where('version', 1)
            ->select(
                'nombre_archivo_codificado',
                'id_documento_buzon_archivo',
                'buzon_usuario.id_buzon_usuario as id_buzon_usuario'

            )
            ->get();

        $dFechaCreacion = date('Y-m-d H:i:s');
        foreach ($archivo as $file)
            $nombre = $file->nombre_archivo_codificado;
        DocumentoBuzonArchivoDescarga::create([
            'id_documento_buzon_archivo' => $file['id_documento_buzon_archivo'],
            'id_buzon_usuario' =>  $file['id_buzon_usuario'],
            'fecha' => $dFechaCreacion
        ]);

        //$ruta = ('files/') . 'principal_'.$nDocumento.'_.pdf';
        $ruta = ('files/') . $nombre;

        //return response()->download($ruta, $nombre);

        $result = array('status' => '200', 'data' => array('data' => $ruta));
        //return $ruta;
        return response()->json($result, '200');
    }

    public function buscar()
    {
        $anio = request()->input('anio',session('year'));

        $datos = TipoDocumento::leftJoin('documento as d', 'd.id_tipo_documento', 'tipo_documento.id_tipo_documento')
            ->groupBy('tipo_documento.nombre')
            ->orderBy('tipo_documento.nombre')
            ->select(DB::raw('count(d.*) as total'), DB::raw('tipo_documento.nombre as tipo'))
            ->where('d.anio_tramitacion',$anio)
            ->get();

        //return datatables( $datos )->toJson();
        return $datos;
    }



public function fixbitacora($idDocumento = null){
    ini_set('memory_limit', '-1');
    set_time_limit(0);

    $idDocumento = request()->input('idDocumento', $idDocumento);
   
    if($idDocumento == null){
        $anio = request()->input('anio',date('Y'));
        $documentos = Documento::Where("anio_tramitacion",$anio)->orderBy('id_documento', 'ASC')->get('id_documento');
    }else{
        $documentos = Documento::Where("id_documento",'>=',$idDocumento)->orderBy('id_documento', 'ASC')->get('id_documento');
    }
    
    //$documentos = Documento::Where("id_documento",$idDocumento)->orderBy('id_documento', 'ASC')->get('id_documento');
    $derivaciones = DocumentoBuzon::All();
    foreach ($documentos as $documento) {
        Log::info("Iniciando Actualización Bitácora ID: " . $documento->id_documento);
        dump("Iniciando Actualización Bitácora ID: " . $documento->id_documento);
        $derivacion = $derivaciones->where('id_documento', $documento->id_documento);
        $arbol = $this->getArbolDocumentoBuzon($documento->id_documento,$derivacion);
        Log::info("Actualización Bitácora ".$documento->id_documento." aplicada!");
        dump("Actualización Bitácora ".$documento->id_documento." aplicada!");
    }
}

private function getArbolDocumentoBuzon($idDocumento, $derivaciones, $padreId = null)
    {
        $hijos = $derivaciones->where('id_documento', $idDocumento)
            ->where('id_documento_buzon_padre', $padreId);
        $resultado = [];
        foreach ($hijos as $hijo) {
            // Obtén los archivos asociados a este documento_buzon
            $bitacora = DocumentoBuzonBitacora::where('id_documento_buzon', $hijo->id_documento_buzon)->orderBy('id_documento_buzon_bitacora')->get()->toArray();
            //revisar bitacora
            if (count($bitacora) > 0) {
                //corregir registro y agregar destino a registro
                if ($bitacora[0]['id_accion'] == 2) {
                    Log::info($bitacora[0]['id_documento_buzon_bitacora']. " > Actualizando registro" );
                    dump($bitacora[0]['id_documento_buzon_bitacora']. " > Actualizando registro");
                    DocumentoBuzonBitacora::where('id_documento_buzon_bitacora', $bitacora[0]['id_documento_buzon_bitacora'])
                        ->update([
                            'id_documento_buzon' => $padreId,
                            'informacion_solicitud' => ["buzon_destino" => $hijo->id_buzon,"id_tipo_destino"=>$hijo->id_tipo_destino,"mensaje" => ($hijo->id_tipo_destino==1)?$hijo->comentario_principal:$hijo->comentario_secundario],
                        ]);
                    //dump("Registro Actualizado");
                } else {
                    //buscar en los registros del padre si hay envio a buzón
                    $padreBitacora = DocumentoBuzonBitacora::where('id_documento_buzon', $padreId)->where('id_accion', 2)->orderBy('id_documento_buzon_bitacora')->get()->toArray();

                    //dump($padreBitacora);

                    if (count($padreBitacora) > 0) {
                        $reg = array_filter($padreBitacora, function ($item) use ($hijo) {
                            return (isset($item['informacion_solicitud']['buzon_destino']) && $item['informacion_solicitud']['buzon_destino'] == $hijo->id_buzon)
                                && (isset($item['informacion_solicitud']['id_tipo_destino']) && $item['informacion_solicitud']['id_tipo_destino'] == $hijo->id_tipo_destino);
                        });
                        if (count($reg) > 0) {
                            Log::info($bitacora[0]['id_documento_buzon_bitacora']." > Existe registro de envio a buzón, actualizando mensaje");
                            dump($bitacora[0]['id_documento_buzon_bitacora']." > Existe registro de envio a buzón, actualizando mensaje");
                            $reg = reset($reg);
                            //actualizar registro
                            DocumentoBuzonBitacora::where('id_documento_buzon_bitacora', $reg['id_documento_buzon_bitacora'])
                                ->update([
                                    'informacion_solicitud' => ["buzon_destino" => $hijo->id_buzon,"id_tipo_destino"=>$hijo->id_tipo_destino,"mensaje" => ($hijo->id_tipo_destino==1)?$hijo->comentario_principal:$hijo->comentario_secundario],
                                ]);
                            //dump($reg);
                        } else {
                            dump("No existe registro de envio a buzón, actualizando alguno de los disponibles");
                            $reg1 = array_filter($padreBitacora, function ($item) use ($hijo) {
                                return !isset($item['informacion_solicitud']['buzon_destino']);
                            });
                            if (count($reg1) > 0) {
                                //actualizar registro
                                Log::info($bitacora[0]['id_documento_buzon_bitacora']." > Actualizando Registro");
                                dump($bitacora[0]['id_documento_buzon_bitacora']." > Actualizando Registro");
                                $reg1 = reset($reg1);
                                DocumentoBuzonBitacora::where('id_documento_buzon_bitacora', $reg1['id_documento_buzon_bitacora'])
                                    ->update([
                                        'informacion_solicitud' => ["buzon_destino" => $hijo->id_buzon, "id_tipo_destino"=>$hijo->id_tipo_destino,"mensaje" => ($hijo->id_tipo_destino==1)?$hijo->comentario_principal:$hijo->comentario_secundario],
                                    ]);
                            } else {
                                Log::info($bitacora[0]['id_documento_buzon_bitacora']." > No existe registro de envio a buzón, no se puede actualizar");
                                dump($bitacora[0]['id_documento_buzon_bitacora']." > No existe registro de envio a buzón, no se puede actualizar");
                                //agregar registro de envio a buzón
                                // DocumentoBuzonBitacora::create([
                                //     'id_documento_buzon' => $padreId,
                                //     'id_accion' => 2,
                                //     'fecha' => now(),
                                //     'id_usuario' => Auth::user()->id,
                                //     'comentario' => "Envío a buzón",
                                //     'informacion_solicitud' => ["buzon_destino"=>$hijo->id_buzon],
                                //     'mensaje_respuesta' => null
                                // ]);
                            }
                        }
                    } else {
                        dump($padreId." > No hay historial para DocBuzon");
                        //dump($padreBitacora);
                        //agregar registro de envio a buzón
                    }
                }
            }else{
                //dump(">--");
                //buscando en los envíos del padre si hay alguno no editado para agregar este envío
                $padreBitacora = DocumentoBuzonBitacora::where('id_documento_buzon', $padreId)->where('id_accion', 2)->whereNull('informacion_solicitud')->orderBy('id_documento_buzon_bitacora')->get()->toArray();
                //si no esta en padre, revisar si en este docbuzon está el registro, si es asi actualizar
                //dump($padreBitacora);
                if(count($padreBitacora)==0){
                    $padreBitacora = DocumentoBuzonBitacora::where('id_documento_buzon', $hijo->id_documento_buzon)->where('id_accion', 2)->orderBy('id_documento_buzon_bitacora')->get()->toArray();
                    $reg = array_filter($padreBitacora, function ($item) use ($hijo) {
                        return (isset($item['informacion_solicitud']['buzon_destino']) && $item['informacion_solicitud']['buzon_destino'] == $hijo->id_buzon)
                            && (isset($item['informacion_solicitud']['id_tipo_destino']) && $item['informacion_solicitud']['id_tipo_destino'] == $hijo->id_tipo_destino);
                    });
                    $reg = reset($reg);
                    if(isset($reg['id_documento_buzon_bitacora'])){
                        //dump("> registro en hijo a modificar");
                         DocumentoBuzonBitacora::where('id_documento_buzon_bitacora', $reg['id_documento_buzon_bitacora'])
                            ->update([
                                'id_documento_buzon'=>$hijo->id_documento_buzon_padre,
                            ]);
                        //dump($reg);
                        //dump("< registro en hijo a modificar");
                    }

                }else{
                    //actualizar registro de envío sin info destino 
                    $reg=reset($padreBitacora); 
                    dump($reg['id_documento_buzon_bitacora'] ." > actualizando info envío");
                    DocumentoBuzonBitacora::where('id_documento_buzon_bitacora', $reg['id_documento_buzon_bitacora'])
                    ->update([
                        'informacion_solicitud' => ["buzon_destino" => $hijo->id_buzon, "id_tipo_destino"=>$hijo->id_tipo_destino,"mensaje" => ($hijo->id_tipo_destino==1)?$hijo->comentario_principal:$hijo->comentario_secundario],
                    ]);
                }
            }
            $resultado[] = [
                'id_documento_buzon' => $hijo->id_documento_buzon,
                'id_buzon' => $hijo->id_buzon,
                'id_documento' => $hijo->id_documento,
                'id_tipo_destino' => $hijo->id_tipo_destino,
                'id_buzon' => $hijo->id_buzon,
                'id_carpeta' => $hijo->id_carpeta,
                'id_estado_documento' => $hijo->id_estado_documento,
                'bitacora' => $bitacora,
                //'otros_campos' => $hijo->toArray(), // Puedes quitar esto o agregar más campos específicos
                'hijos' => $this->getArbolDocumentoBuzon($idDocumento, $derivaciones, $hijo->id_documento_buzon)
            ];
        }
        return $resultado;
    }



    public function buscarDocumentoReferencia(Request $request)
    {
        $q = $request->input('search',null);
        
        // limitar si no viene parametro de busqueda 
        if($q['value'] == null){
            return [];
        }
        $sql = "SELECT 
            d.id_documento,
            td.nombre_corto,
            d.folio,
            date_part('year',d.fecha) as anio,
            dba.version,
            d.materia,
            dba.id_documento_buzon_archivo ,
            dba.id_tipo_archivo ,
            dba.nombre_archivo_original ,
            dba.nombre_archivo_codificado
        FROM public.documento_buzon_archivo dba
        join documento_buzon db on db.id_documento_buzon = dba.id_documento_buzon 
        join documento d on d.id_documento = db.id_documento 
        join tipo_documento td on td.id_tipo_documento = d.id_tipo_documento ";

        if (is_numeric($q['value'])) {
            $sql .= " where d.id_documento = " . $q['value'];
            $sql .= " or d.folio = " . $q['value'];
        } else {
            $sql .= " where d.materia like '%" . $q['value'] . "%'";
        }
        $res = DB::select($sql); 
        return ['data'=>$res];

    }
}
