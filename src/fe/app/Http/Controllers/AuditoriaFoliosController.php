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
        $tipos_documentos = TipoDocumento::where('id_tipo_folio',$request->idTipo)
                            ->orderBy('nombre','ASC')
                            ->get();
        return $tipos_documentos;
    }

    public function obtener_folios(Request $request){
        $tipo_folio = $request->tipo_folio;
        $tipo_documento = $request->tipo_documento;

        if($tipo_folio == ""){
            $tipo_folio = 0;
        }
        if($tipo_documento == ""){
            $tipo_documento = 0;
        }

        $datos =  DB::table('documento as d')
                    ->join('tipo_documento as td','td.id_tipo_documento','d.id_tipo_documento')
                    ->join('documento_buzon as db','d.id_documento','db.id_documento')
                    ->join('buzon as b','b.id_buzon','db.id_buzon')
                    ->where('d.id_tipo_documento',$tipo_documento)
                    ->where('td.id_tipo_folio',$tipo_folio)
                    ->whereRaw('d.folio is not null')
                    ->whereRaw('db.id_documento_buzon = (select max(db2.id_documento_buzon) from documento_buzon as db2 where db2.id_documento= d.id_documento)')
                    ->where('db.id_tipo_destino',1)
                    ->select('d.id_documento',DB::raw("to_char(d.fecha,'dd-mm-yyyy') fecha_folio"), 'd.folio' ,DB::raw('b.nombre as buzon'),'d.materia')
                    ;

        return datatables( $datos )->toJson();
    }
}
