<?php

namespace App\Http\Controllers;

use App\Models\TipoDocumento;
use App\Models\TipoFolio;
use App\Providers\AppServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AuditoriaFoliosController extends Controller
{
    public function index(){
        $sesion_key = AppServiceProvider::session_key_general();

        $listado_parametros = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(13)
        ->get('http://sgd_ms_parametros:3333/api/sgd-parametros/traer');

        $datosTipoFolio = $listado_parametros['data']['tipo_folio'];
        return view('auditoria_folios.index',['tipos_folio'=>$datosTipoFolio]);
    }

    public function obtener_tipos_documentos(Request $request){
        $tipos_documentos = TipoDocumento::join('tipo_folio','tipo_documento.id_tipo_folio','tipo_folio.id_tipo_folio')
                            ->where('tipo_folio.nombre',$request->idTipo)
                            ->select('tipo_documento.nombre')
                            ->orderBy('tipo_documento.nombre','ASC')
                            ->get();
        return $tipos_documentos;
    }

    public function obtener_folios(Request $request){
        $tipo_folio = $request->tipo_folio;
        $tipo_documento = $request->tipo_documento;

        // if($tipo_folio == ""){
        //     $tipo_folio = 0;
        // }
        // if($tipo_documento == ""){
        //     $tipo_documento = 0;
        // }

       

        $datos =  DB::table('documento as d')
                    ->join('tipo_documento as td','td.id_tipo_documento','d.id_tipo_documento')
                    ->join('tipo_folio as tf','td.id_tipo_folio','tf.id_tipo_folio')
                    ->join('documento_buzon as db','d.id_documento','db.id_documento')
                    ->join('buzon as b','b.id_buzon','db.id_buzon')
                    ->when($tipo_documento, function ($query, $tipo_documento) {
                        return $query->where('d.id_tipo_documento',$tipo_documento);
                    })
                    ->when($tipo_folio, function ($query, $tipo_folio) {
                        return $query->where('td.id_tipo_folio',$tipo_folio);
                    })
                    // ->where('d.id_tipo_documento',$tipo_documento)
                    // ->where('td.id_tipo_folio',$tipo_folio)
                    ->whereRaw('d.folio is not null')
                    ->whereRaw('db.id_documento_buzon = (select max(db2.id_documento_buzon) from documento_buzon as db2 where db2.id_documento= d.id_documento)')
                    ->where('db.id_tipo_destino',1)
                    ->select('d.id_documento',DB::raw("to_char(d.fecha,'dd-mm-yyyy HH24:MI:SS') fecha_folio"), 'd.folio' ,DB::raw('b.nombre as buzon'),'d.materia',DB::raw('td.nombre as nombre_documento'),DB::raw('tf.nombre as nombre_folio'))
                    ;
        $tWhere = " and 1 = 1 ";
        if($tipo_folio != ""){
            $tWhere .= " and td.id_tipo_folio = ".$tipo_folio;
        }
        if($tipo_documento != ""){
            $tWhere .= " and td.id_tipo_documento = ".$tipo_documento;
        }

        $datos = DB::select("select 
                    d.id_documento,to_char(d.fecha,'dd-mm-yyyy HH24:MI:SS') fecha_folio, d.folio ,b.nombre as buzon,d.materia,td.nombre as nombre_documento,tf.nombre as nombre_folio
                from
                    documento as d
                    join tipo_documento as td on td.id_tipo_documento = d.id_tipo_documento
                    join tipo_folio as tf on td.id_tipo_folio = tf.id_tipo_folio
                    join documento_buzon as db on d.id_documento = db.id_documento
                    join buzon as b on b.id_buzon = db.id_buzon
                where
                    d.folio is not null
                    and db.id_documento_buzon = (select max(db2.id_documento_buzon) from documento_buzon as db2 where db2.id_documento= d.id_documento)
                    and db.id_tipo_destino = 1 ".$tWhere." ");

        return datatables( $datos )->toJson();
    }
}
