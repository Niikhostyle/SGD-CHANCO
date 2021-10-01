<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BuscadorController extends Controller
{
    public function index(){
        //return view(‘buscador/buscador’, compact(‘buscador’));
        $lista_usuarios=['data'=>[
            0=>['id'=>'1234','fecha'=>'01-07-2021','td'=>'MEM','folio'=>'3455','buzon_origen'=>'Calidad y Gestión de Servicios',
                'buzon_actual'=>'Alcadía','materia'=>'Solicita Informe de auditoria 2020'],
            1=>['id'=>'1235','fecha'=>'02-07-2021','td'=>'ORD','folio'=>'587','buzon_origen'=>'Alcadía',
            'buzon_actual'=>'Oficina de Partes','materia'=>'Comunica inicio sesiones de consejo especial']
        ]];
        return view('buscador.index', ['lista_usuarios'=>$lista_usuarios]);
    }

    public function buscador_documentos_get(){
        //data.append([
            //utc_to_timezone(i.fecha).strftime('%d/%m/%Y %H:%M:%S'),
            //i.documento.id,
            //i.documento.doc,
           // i.td,
         //   i.folio,
       //     accion
     //   ]);
    //response = {
        //'draw'; draw;
        //'recordsTotal'; documentos.count();
       // 'recordsFiltered'; documentos.count();
     //   'data'; data
   // }
    //return JsonResponse(response);
    }
}

