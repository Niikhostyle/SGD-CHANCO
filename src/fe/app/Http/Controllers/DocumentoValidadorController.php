<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;
use \PDF;

use Illuminate\Support\Facades\Auth;

class DocumentoValidadorController extends Controller
{
    public function index()
    {
        $lista_documentos=['data'=>[
            0=>['hash_validacion'=>'','folio'=>'','fecha_documento'=>'','materia'=>'',]
        ]];
        return View::make('validador.index',['lista_documentos'=>$lista_documentos]);
    }

    public function store(Request $request)
    {
        
        $codigo = $request['codigo'];
        //return $codigo;
        $sesion_key =  AppServiceProvider::session_key_general();
        $lista_documentos = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->withBody(json_encode([
            'hash_validacion' => $codigo 
        ]), 'json')
        ->get('http://sgd_ms_documentos:3333/api/sgd-documentos/verificaDocumento');
        //return $lista_documentos ;

        if($lista_documentos->failed()){
            $mensaje= $lista_documentos->json()['data']['comentario'];

            $lista_documentos=['data'=>[
                0=>['hash_validacion'=>'sin datos','folio'=>'sin datos','fecha_documento'=>'sin datos','materia'=>'sin datos',]
            ]];
            toast($mensaje,'error');
        }else{
            $lista_documentos->json();
        }
        return View::make('validador.index',['lista_documentos'=>$lista_documentos]);
        
    }

    public function validar(Request $request)
    {
        
       
        
    }

    public function download()
    {
        $data = [
            'titulo' => 'Styde.net'
        ];
    
        $pdf = \PDF::loadView('pdf', $data);
    
        return $pdf->download('archivo.pdf');
    }
    
}
