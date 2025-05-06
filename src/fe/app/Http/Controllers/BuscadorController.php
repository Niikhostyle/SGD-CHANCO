<?php

namespace App\Http\Controllers;

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
            ->get(env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333').'/api/sgd-documentos/listarDocumentos');

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
            ->get(env('API_SGD_TIPO_DOCUMENTOS','http://sgd_ms_tipos_documentos:3333').'/api/sgd-tipodoc/ver_todos');

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
            ->get(env('API_SGD_BUZONES','http://sgd_ms_buzones:3333').'/api/sgd-buzones/listar_todos');

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


        return View::make('buscador.index', [
            'lista_documento' => $lista_documento,
            'listado_tiposdoc' => $datosTipoDoc,
            'listBuzones' => $datosBuzones,
            'listadoBuzones' => $aBuzones,
            'listadoAcciones' => $datosAccion,
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
            ->get(env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333').'/api/sgd-documentos/listarDocumentos');

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
            ->get(env('API_SGD_TIPO_DOCUMENTOS','http://sgd_ms_tipos_documentos:3333').'/api/sgd-tipodoc/ver_todos');

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
            ->get(env('API_SGD_BUZONES','http://sgd_ms_buzones:3333').'/api/sgd-buzones/listar_todos');

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
            ->get(env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333').'/api/sgd-documentos/ver')->throw();

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

    public function bitacora($id)
    {
        $sesion_key =  AppServiceProvider::session_key_general();
        // $lista_bitacora = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
        //     ->timeout(10)
        //     ->withBody(json_encode([
        //         'id_documento' => $id,
        //     ]), 'json')
        //     ->get(env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333').'/api/sgd-documentos/listarDocumentosBitacora')->throw();
        // if ($lista_bitacora->failed()) {
        //     $mensaje = $lista_bitacora->json()['data']['comentario'];

        //     $lista_bitacora = ['data' => [
        //         0 => ['accion' => '', 'fecha_documento' => '', 'buzon_origen' => '', 'nombre_accion' => '', 'mensaje_respuesta' => '', 'tipo_destino' => '', 'materia' => '',  'identificador' => '']
        //     ]];
        //     toast($mensaje, 'error');
        //     return response()->json($lista_bitacora);//->json();
        // } else {
        //     return $lista_bitacora->json();
        // }


        // $lista_bitacora = DB::select('
        //     select db.id_documento_buzon,db.id_buzon, 
        //     db.id_carpeta,db.id_estado_documento ,db.id_tipo_destino ,db.id_documento_buzon_padre,
        //     dbb.*
        //     from documento_buzon db
        //     join documento d on d.id_documento = db.id_documento
        //     join documento_buzon_bitacora dbb on dbb.id_documento_buzon = db.id_documento_buzon 
        //     where d.id_documento ='.$id.'
        //     order by dbb.created_at asc
        // ');
        $lista_bitacora = DocumentoBuzon::leftJoin('documento_buzon_bitacora', 'documento_buzon_bitacora.id_documento_buzon', '=', 'documento_buzon.id_documento_buzon')
            ->join('documento', 'documento.id_documento', '=', 'documento_buzon.id_documento')
            ->join('buzon','buzon.id_buzon',"=",'documento_buzon.id_buzon')
            ->leftJoin('accion','accion.id_accion',"=",'documento_buzon_bitacora.id_accion')
            ->join('users','users.id',"=",'documento_buzon_bitacora.id_usuario')
            ->where('documento.id_documento', $id)
            //->where('documento_buzon_bitacora.id_accion','<>',2)
            ->orderBy('documento_buzon_bitacora.created_at', 'asc')
            ->get([
                'documento_buzon_bitacora.id_documento_buzon',
                'documento_buzon.id_documento',
                'documento_buzon_bitacora.id_documento_buzon_bitacora',
                'documento_buzon_bitacora.id_accion',
                'accion.nombre as nombre_accion',
                'documento_buzon_bitacora.fecha',  
                'documento_buzon_bitacora.id_usuario',  
                'documento_buzon_bitacora.comentario',  
                'documento_buzon_bitacora.informacion_solicitud',  
                'documento_buzon_bitacora.mensaje_respuesta',  
                'documento_buzon.id_buzon',
                'buzon.nombre as buzon',
                'documento_buzon.id_carpeta',
                'documento_buzon.id_estado_documento',
                'documento_buzon.id_tipo_destino',
                'documento_buzon.id_documento_buzon_padre',
                'documento_buzon.comentario_principal',
                'documento_buzon.comentario_secundario',
                'documento.id_documento',
                DB::raw("CONCAT(users.nombres,' ',users.primer_apellido,' ',users.segundo_apellido)  as nombre_usuario")
            
            ]);
        if($lista_bitacora->isEmpty()){
            return response("no hay registros",423);
        }
        $bitacora = $this->bitacoraDestino($lista_bitacora[0]->id_documento_buzon,$lista_bitacora,[]);
        
        // dd($bitacora);
        // die;
        //limpiar bitacora y agregar mensajes 
        foreach ($bitacora as $key=>$item){
            //adecuacion accion y mensaje
           // var aTxtSalida = ['', '', 'Derivación a buzón ', 'Recepción en', 'Edición en', 'Cambio en archivo principal', 'Visación en', 'Firma PDF en', 'Generación de PDF en', '', 'Finalizado en', '', 'Archivado en', 'Enviado a Firma', 'Desarchivado en'];

           $bitacora[$key]["mensaje"] = $item["mensaje_respuesta"]." ".$item["comentario"];
            switch($item["id_accion"]){
                case 1:
                    $bitacora[$key]["accion"] = "Creación documento";
                    break;
                case 2:
                    $bitacora[$key]["accion"] = "Derivación ".(($item["id_tipo_destino"]==2)?'<b>copia</b> ':'')."a '".($item["buzon_destino"]??'-')."'";
                    $bitacora[$key]["mensaje"] = ($item["id_tipo_destino"]==1)?$item["comentario_principal"]:$item["comentario_secundario"];
                    break;
                case 3:
                    $bitacora[$key]["accion"] = "Recepción ".(($item["id_tipo_destino"]==2)?'<b>copia</b> ':'')."en '".$item["buzon"]."'";  
                    break;
                default:
                    $bitacora[$key]["accion"] = $item["nombre_accion"];                  
            }



        }

        return response()->json($bitacora);



        
        
    }

    private function bitacoraDestino($derivacion,$bitacora,$acumulador){ 
        
        $arr_derivacion = $bitacora->where('id_documento_buzon', $derivacion);

        $hijos = $bitacora->where('id_documento_buzon_padre',$derivacion)->unique('id_documento_buzon')->pluck('id_documento_buzon');
        if($hijos->isEmpty()){
            // consultar si hay destino en base de datos y agregarlo 
            $destino = DocumentoBuzon::Where('id_documento_buzon_padre', $derivacion)->where('id_tipo_destino', 1)->first();
            if($destino){
                
                $arr_derivacion->last()->id_buzon_destino = $destino->id_buzon;
                $arr_derivacion->last()->buzon_destino = $destino->buzon->nombre;
                $arr_derivacion->last()->comentario_principal = $destino->comentario_principal;
                $arr_derivacion->last()->comentario_secundario = $destino->comentario_secundario;
                $arr_derivacion->last()->dato_corregido = 'se agrega destino mediante consulta';
            }else{
                // dump($derivacion);
                // dump($arr_derivacion->where("id_accion",2)->toArray());
            }

            //$arr_derivacion->where('id_accion', 2)->first()->dato_corregido = 'sin hijos';
            return $arr_derivacion->toArray();
        }
        foreach($hijos as $item){
            $bit = $bitacora->where('id_documento_buzon', $item);
            if($bit->count() == 1){
                // si este registro es solo accion derivar.... asociar al envio 
                $der = $bit->where('id_accion', 2);
                if($der->count()==1){
                    //echo "Fin Nodo  ".$item." ";
                    $der->first()->id_documento_buzon_clon = $derivacion;
                    $der->first()->id_documento_buzon_padre = $arr_derivacion->last()->id_documento_buzon_padre;
                    $der->first()->id_buzon_destino = $der->first()->id_buzon;
                    $der->first()->buzon_destino = $der->first()->buzon;
                    // $der->first()->comentario_principal = $arr_derivacion->last()->comentario_principal;
                    // $der->first()->comentario_secundario = $arr_derivacion->last()->comentario_secundario;
                    $der->first()->dato_corregido = 'SI';
                }else{
                    $bit->first()->dato_corregido = 'NO';
                }
            }
            else{
                // agregar campo destino en padre
                foreach($arr_derivacion->where('id_accion', 2) as $der_padre ){
                    $der_padre->id_buzon_destino = $bit->first()->id_buzon;
                    $der_padre->buzon_destino = $bit->first()->buzon;
                    $der_padre->comentario_principal = $bit->first()->comentario_principal;
                    $der_padre->comentario_secundario = $bit->first()->comentario_secundario;
                    $der_padre->dato_corregido = 'se agrega destino';
                }
            }
            $acumulador = array_merge($acumulador,$this->bitacoraDestino($item, $bitacora,$acumulador));
        }
        //revisar si la actual derivacion tiene destino
        $dest = $arr_derivacion->where('id_accion', 2);
        if($dest->count()==1 && !isset($dest->first()->dato_corregido)){
            
            //

            $buzon_destino = $bitacora->where('id_documento_buzon_padre',$derivacion)->where('id_tipo_destino', 1);
            if(!$buzon_destino->isEmpty()){
                $arr_derivacion->where('id_accion', 2)->first()->id_buzon_destino = $buzon_destino->first()->id_buzon;
                $arr_derivacion->where('id_accion', 2)->first()->buzon_destino = $buzon_destino->first()->buzon;
                $arr_derivacion->where('id_accion', 2)->first()->comentario_principal = $buzon_destino->first()->comentario_principal;
                $arr_derivacion->where('id_accion', 2)->first()->comentario_secundario = $buzon_destino->first()->comentario_secundario;
                $arr_derivacion->where('id_accion', 2)->first()->dato_corregido = 'Dato destino corregido';
            }else{
                $arr_derivacion->where('id_accion', 2)->first()->dato_corregido = 'Dato por corregir';
            }


            
        }

        return array_merge($arr_derivacion->toArray(),$acumulador);

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
                                        join tipo_documento td on td.id_tipo_documento = d.id_tipo_documento
                                        left join documento d2 on d2.json_respuesta_a::text like '%\"materia\": \"'||d.materia ||'\"%'
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
                                        left join documento d2 on d2.json_respuesta_a::text like '%\"materia\": \"'||d.materia ||'\"%'
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
