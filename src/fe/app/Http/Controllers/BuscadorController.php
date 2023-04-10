<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\DocumentoBuzon;
use App\Models\DocumentoBuzonArchivo;
use App\Models\DocumentoBuzonArchivoDescarga;
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
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;


class BuscadorController extends Controller
{
    public function index(){

        $sesion_key =  AppServiceProvider::session_key_general();
        $lista_documento = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->withBody(json_encode([
            'id_usuario' => Auth::user()->id,
        ]), 'json')
        ->get('http://sgd_ms_documentos:3333/api/sgd-documentos/listarDocumentos');

        if($lista_documento->failed()){
            $mensaje = $lista_documento->json()['data']['comentario'];

            $lista_documento=['data'=>[
                0=>['id_documento'=>'','rel_documento_buzon'=>'','id_tipo_documento'=>'','folio'=>'','rel_documento_buzon'=>'','rel_documento_buzon'=>'','materia'=>'']
            ]];
            toast($mensaje,'error');
        }else{
            $lista_documento->json();
        }

        /* LISTADO TIPO DE DOCUMENTO */

        $listado_tiposdoc = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)
        ->get('http://sgd_ms_tipos_documentos:3333/api/sgd-tipodoc/ver_todos');

        if($listado_tiposdoc->failed()){
            $mensaje = $listado_tiposdoc->json()['data']['comentario'];

            toast($mensaje,'error');
        }
        else
        {
            $datosTipoDoc = $listado_tiposdoc['data'];
        }

        /* LISTADO BUZONES */
        $datosBuzones = array();
        $aBuzones = array();

        $listado_buzones = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)
        ->withBody(json_encode([
            'texto_busqueda' => '',
        ]), 'json')
        ->get('http://sgd_ms_buzones:3333/api/sgd-buzones/listar_todos');

        if($listado_buzones->failed()){
            $mensaje = $listado_buzones->json()['data']['comentario'];

            $listado_buzones=['data'=>[
                0=>['id'=>'0','nombre'=>'Sin Datos']
            ]];
            //toast($mensaje,'error');
        }else{

            $datosBuzones = $listado_buzones['data'];
            foreach ($listado_buzones['data'] as $dato)
            {
                $aBuzones[$dato['id_buzon']] = $dato['nombre'];                  
            }
        }

        //parametros
        $listado_parametros = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(13)
        ->get('http://sgd_ms_parametros:3333/api/sgd-parametros/traer');

        if($listado_parametros->failed()){
            toast("Error al mostrar datos",'error');
        }
        
        $datosNivelAcceso = $listado_parametros['data']['nivel_acceso'];
        $datosAccion = $listado_parametros['data']['accion'];
        $datosAnios= $listado_parametros['data']['anio'];


        return View::make('buscador.index',[
            'lista_documento'=>$lista_documento,            
            'listado_tiposdoc'=>$datosTipoDoc,
            'listBuzones'=>$datosBuzones,
            'listadoBuzones'=>$aBuzones,
            'listadoAcciones' => $datosAccion,
            'nivel_acceso' => $datosNivelAcceso,
            'listadoAnios' => $datosAnios
        ]);
        
    }

    public function index2(){

        $sesion_key =  AppServiceProvider::session_key_general();
        $lista_documento = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->withBody(json_encode([
            'id_usuario' => Auth::user()->id,
        ]), 'json')
        ->get('http://sgd_ms_documentos:3333/api/sgd-documentos/listarDocumentos');

        if($lista_documento->failed()){
            $mensaje = $lista_documento->json()['data']['comentario'];

            $lista_documento=['data'=>[
                0=>['id_documento'=>'','rel_documento_buzon'=>'','id_tipo_documento'=>'','folio'=>'','rel_documento_buzon'=>'','rel_documento_buzon'=>'','materia'=>'']
            ]];
            toast($mensaje,'error');
        }else{
            $lista_documento->json();
        }

        /* LISTADO TIPO DE DOCUMENTO */

        $listado_tiposdoc = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)
        ->get('http://sgd_ms_tipos_documentos:3333/api/sgd-tipodoc/ver_todos');

        if($listado_tiposdoc->failed()){
            $mensaje = $listado_tiposdoc->json()['data']['comentario'];

            toast($mensaje,'error');
        }
        else
        {
            $datosTipoDoc = $listado_tiposdoc['data'];
        }

        /* LISTADO BUZONES */
        $datosBuzones = array();
        $aBuzones = array();

        $listado_buzones = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)
        ->withBody(json_encode([
            'texto_busqueda' => '',
        ]), 'json')
        ->get('http://sgd_ms_buzones:3333/api/sgd-buzones/listar_todos');

        if($listado_buzones->failed()){
            $mensaje = $listado_buzones->json()['data']['comentario'];

            $listado_buzones=['data'=>[
                0=>['id'=>'0','nombre'=>'Sin Datos']
            ]];
            //toast($mensaje,'error');
        }else{

            $datosBuzones = $listado_buzones['data'];
            foreach ($listado_buzones['data'] as $dato)
            {
                $aBuzones[$dato['id_buzon']] = $dato['nombre'];                  
            }
        }

        //parametros
        $listado_parametros = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(13)
        ->get('http://sgd_ms_parametros:3333/api/sgd-parametros/traer');

        if($listado_parametros->failed()){
            toast("Error al mostrar datos",'error');
        }
        $datosNivelAcceso = $listado_parametros['data']['nivel_acceso'];
        $datosAccion = $listado_parametros['data']['accion'];
        $datosAnios= $listado_parametros['data']['anios'];

        return View::make('buscador.index2',[
            'lista_documento'=>$lista_documento,            
            'listado_tiposdoc'=>$datosTipoDoc,
            'listBuzones'=>$datosBuzones,
            'listadoBuzones'=>$aBuzones,
            'listadoAcciones' => $datosAccion,
            'nivel_acceso' => $datosNivelAcceso,
            'listadoAnios' => $datosAnios
        ]);
        
    }

    public function show($id)
    {
        $sesion_key =  AppServiceProvider::session_key_general();
        $lista_bitacora = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->withBody(json_encode([
            'id_documento' => $id,
        ]), 'json')
        ->get('http://sgd_ms_documentos:3333/api/sgd-documentos/listarDocumentosBitacora');
        
        return $lista_bitacora;
        

        if($lista_bitacora->failed()){
            $mensaje= $lista_bitacora->json()['data']['comentario'];

            $lista_bitacora=['data'=>[
                0=>['accion'=>'','fecha_documento'=>'','buzon_origen'=>'','nombre_accion'=>'','mensaje_respuesta'=>'', 'tipo_destino'=>'', 'materia'=>'',  'identificador'=>'']
            ]];
            toast($mensaje,'error');
        }else{
            return $lista_bitacora->json();
        }
         
    }  

    public function listar(Request $request)
    {
        $year_actual = session('year');  
        //dd($request);
        $extraquery = "";
        // //construir filtro
        $query = $request->busqueda_simple;
        if($query){
            $extraquery=" AND (lower(d.materia) like '%".strtolower($query)."%'"; 
            if((int)$query > 0){            
                $extraquery=$extraquery." OR d.id_documento=".(int)$query." OR d.folio = ".(int)$query."";
            }    
            if(isset($request->inicio)){
                $extraquery=$extraquery." OR lower(d.descripcion) like '%".strtolower($query)."%' ";
            }
            $extraquery=$extraquery.")"; 
            
        }
        $filtroAvanzado = " and 1 = 1 ";
        if($request->buscar_id_documento != ""){
            $filtroAvanzado .= " and d.id_documento = ".$request->buscar_id_documento;
        }
        if($request->buscar_folio != ""){
            if((int)$request->buscar_folio > 0){
                $filtroAvanzado .= " and d.folio = ".$request->buscar_folio;
            }
        }
        if($request->buscar_tipo_documento != ""){
            $filtroAvanzado .= " and lower(td.nombre) = lower('".$request->buscar_tipo_documento."')";
        }
        if($request->buscar_buzon_origen != "" && $request->buscar_derivado == ""){ 
            $filtroAvanzado .= " and lower(bo.nombre) = lower('".$request->buscar_buzon_origen."')";
        }

        if($request->buscar_buzon_origen != "" && $request->buscar_derivado != ""){ 
            $filtroAvanzado .= " and lower((case when db.id_documento_buzon_padre is not null  
            then  
                (select b2.nombre from documento_buzon db2 join buzon b2 on b2.id_buzon = db2.id_buzon  where db2.id_documento_buzon = db.id_documento_buzon_padre)  
            else  
            (select b2.nombre from documento_buzon db2 join buzon b2 on b2.id_buzon = db2.id_buzon  where db2.id_documento_buzon = db.id_documento_buzon) 
            end) ) = lower('".$request->buscar_buzon_origen."')"; 
        } 

        if($request->buscar_buzon_actual != ""){
            $filtroAvanzado .= " and lower((select b2.nombre from documento_buzon db3 join buzon b2 on b2.id_buzon = db3.id_buzon where db3.id_documento = db.id_documento order by db3.id_documento_buzon desc limit 1) ) = lower('".$request->buscar_buzon_actual."')";
        }
        if($request->terceros != ""){
            $filtroAvanzado .= "and d.efectos_terceros = ".$request->terceros;
        }

        if($request->buscar_derivado != ""){
            $filtroAvanzado .= " and lower((case when db.id_documento_buzon_padre is not null 
            then 
                (select b2.nombre from documento_buzon db2 join buzon b2 on b2.id_buzon = db2.id_buzon  where db2.id_documento_buzon = db.id_documento_buzon_padre) 
            else 
            (select b2.nombre from documento_buzon db2 join buzon b2 on b2.id_buzon = db2.id_buzon  where db2.id_documento_buzon = db.id_documento_buzon)
            end)) = lower('".$request->buscar_derivado."') ";
            if(isset($request->buscar_fecha_ini_d)){
                $filtroAvanzado .= " and dbb.fecha >=  to_date('".$request->buscar_fecha_ini_d."','yyyy-mm-dd') ";
            }
            if(isset($request->buscar_fecha_fin_d)){
                $filtroAvanzado .= " and dbb.fecha <= to_date('".$request->buscar_fecha_fin_d."','yyyy-mm-dd')  + INTERVAL '1 day' ";
            }
        }




        if($request->buscar_anio != ""){
            $extraquery .= " and ( extract(year from d.created_at) = ".$request->buscar_anio." or extract(year from d.fecha)  = ".$request->buscar_anio.")";
        }
        else{
            if (!isset($request->buscar_fecha_ini) || !isset($request->buscar_fecha_fin)){
                $extraquery .= " and extract(year from d.created_at) = " . $year_actual;
            }
            else{
                $extraquery .= " and d.created_at between to_date('".$request->buscar_fecha_ini."','yyyy-mm-dd') and to_date('".$request->buscar_fecha_fin."','yyyy-mm-dd') + INTERVAL '1 day'";
            }
        }

        DB::enableQueryLog(); 
        if($request->buscar_derivado != ""){
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
                                    from 
                                        documento_buzon_bitacora dbb
                                        join documento_buzon db on dbb.id_documento_buzon = db.id_documento_buzon
                                        join documento d on db.id_documento = d.id_documento
                                        join buzon b on db.id_buzon = b.id_buzon
                                        join tipo_documento td on td.id_tipo_documento = d.id_tipo_documento
                                    where 
                                        d.id_nivel_acceso < 3
                                        AND db.id_tipo_destino = 1 
                                        and id_accion = 2 
                                        ".$extraquery.$filtroAvanzado."
                                    ");
        }
        else{
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
                                    from 
                                        documento_buzon db 
                                        join documento d on d.id_documento = db.id_documento and db.id_estado_documento > 1
                                        join buzon b on b.id_buzon = db.id_buzon
                                        join tipo_documento td on td.id_tipo_documento = d.id_tipo_documento 
                                        LEFT JOIN documento_buzon dbo ON db.id_documento = dbo.id_documento  AND dbo.id_documento_buzon_padre is null
                                        LEFT JOIN buzon bo ON bo.id_buzon = dbo.id_buzon
                                    where
                                        d.id_nivel_acceso < 3
                                        AND db.id_tipo_destino = 1 
                                        ".$extraquery.$filtroAvanzado."
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
                                        , buzon_actual");
        }
        //dd(DB::getQueryLog());  
        //return $datos;exit;
        return datatables( $datos )->toJson();
    }    

    public function listar2(Request $request)
    {
        //dd($request);
        //filtro rangos de fecha
        $sWhereBetween = "";

        if ($request['searchFromdate'] != '')
            $sWhereBetween .= "and d.created_at >= '".$request['searchFromdate']."'";
        if ($request['searchTodate'] != '')
            $sWhereBetween .= "and d.created_at <= '".$request['searchTodate']."'";

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
        join users us on us.id = ".Auth::user()->id." 
    where 	        
        ((d.id_nivel_acceso in (1,3)) 
        or (bu.id_usuario = ".Auth::user()->id." and d.id_nivel_acceso = 2))
        AND db.id_tipo_destino = 1 
        ".$sWhereBetween ."
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
        return datatables( $datos )->toJson();


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
        $ruta = ('files/'). $nombre;
       
        //return response()->download($ruta, $nombre);

        $result = array('status' => '200', 'data' => array('data' => $ruta));
        //return $ruta;
        return response()->json($result, '200');  

    }

    public function buscar(){
        $datos = TipoDocumento::leftJoin('documento as d','d.id_tipo_documento','tipo_documento.id_tipo_documento')
                ->groupBy('tipo_documento.nombre')
                ->orderBy('tipo_documento.nombre')
                ->select(DB::raw('count(d.*) as total'),DB::raw('tipo_documento.nombre as tipo'))
                ->get();
        
        //return datatables( $datos )->toJson();
        return $datos;
    }
   

}