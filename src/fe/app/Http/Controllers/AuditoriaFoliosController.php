<?php

namespace App\Http\Controllers;

use App\Models\Buzon;
use App\Models\TipoDocumento;
use App\Models\TipoFolio;
//use App\Providers\AppServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AuditoriaFoliosController extends Controller
{
    public function index(){

        $datosTipoFolio = TipoFolio::orderBy('nombre', 'ASC')->get();

        $datosBuzones = Buzon::orderBy('nombre', 'ASC')->get();

        return view('auditoria_folios.index',['tipos_folio'=>$datosTipoFolio,'buzones'=>$datosBuzones]);
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
        $buzon = $request->buzon;
        DB::enableQueryLog(); // Enable query log


        $year_actual = session('year');   

        $tWhere = " and 1 = 1 ";
        if($tipo_folio != ""){
            $tWhere .= " and td.id_tipo_folio = ".$tipo_folio."";
        }
        if($tipo_documento != ""){
            $tWhere .= " and d.id_tipo_documento = ".$tipo_documento."";
        }
        if($buzon != ""){
            $tWhere .= " and b2.id_buzon = ".$buzon."";
        }

        if($tWhere == " and 1 = 1 "){
            $tWhere = " and 1 = 2 ";
            $datos = DB::select("select 
                        d.id_documento,to_char(d.fecha,'dd-mm-yyyy HH24:MI:SS') fecha_folio, d.folio ,'buzon' as buzon,'materia' as materia
                    from
                        documento as d
                    where
                        d.folio is not null ".$tWhere." ");
        }
        else{            
            $datos = DB::select("select
                        d.folio ,
                        to_char(d.fecha,'dd-mm-yyyy HH24:MI:SS') fecha_folio,  
                        d.id_documento, 
                        b.nombre as buzon , 
                        b2.nombre as buzon_origen,
                        d.materia, 
                        tf.nombre,td.nombre as nombre_documento  
                    from 
                        documento_buzon db
                        join buzon b on b.id_buzon = db.id_buzon 
                        join documento d on d.id_documento = db.id_documento 
                        join tipo_documento td on td.id_tipo_documento = d.id_tipo_documento 
                        join tipo_folio tf on tf.id_tipo_folio = td.id_tipo_folio 
                        and db.id_documento_buzon = (select max(db2.id_documento_buzon) from documento_buzon db2 where db.id_documento = db2.id_documento  )
                        join documento_buzon db2 on db2.id_documento = d.id_documento
                        join buzon b2 on b2.id_buzon = db2.id_buzon
                        join documento_buzon_bitacora dbb on dbb.id_documento_buzon = db2.id_documento_buzon  and dbb.id_accion = 8
                    where 
                        d.folio is not null
                        and db.id_tipo_destino = 1
                        and EXTRACT(YEAR FROM d.created_at)  = ".$year_actual." ".$tWhere." 
            ");
        }
        //dd(DB::getQueryLog()); // Show results of log

        return datatables( $datos )->toJson();
    }
}
