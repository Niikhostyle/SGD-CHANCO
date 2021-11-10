<?php

namespace App\Http\Controllers;

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sesion_key =  AppServiceProvider::session_key_general();
        $lista_favoritos = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->withBody(json_encode([
            'id_usuario' => Auth::user()->id,
        ]), 'json')
        ->get('http://sgd_ms_documentos:3333/api/sgd-documentos/listarFavoritos');


        //return $lista_favoritos;

        if($lista_favoritos->failed()){
            $mensaje= $lista_favoritos->json()['data']['comentario'];

            $lista_favoritos=['data'=>[
                0=>['nombre_buzon'=>'','estado_documento'=>'','id_documento'=>'','fecha_documento'=>'','origen'=>'','materia'=>'','estado_favorito'=>'']
            ]];
            toast($mensaje,'error');
        }else{
            $lista_favoritos->json();
        }

        return View::make('favorito.index',['lista_favoritos'=>$lista_favoritos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sesion_key =  AppServiceProvider::session_key_general();
        $documento = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json'])
        ->timeout(10)
        ->withBody(json_encode([
            'id_usuario' => $id,
        ]), 'json')
        ->get('http://sgd_ms_documentos:3333/api/sgd-documentos/ver/');

        return $documento->json();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
