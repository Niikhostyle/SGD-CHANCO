<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Http;


class PanelController extends Controller
{
    
    Public function index(Request $request){

        return View::make('panel.index');       
    } 

    Public function captura(Request $request){
       
        $data['year'] = $request->select_anio;        
        session(['year' => $request->select_anio]);
        
        return redirect()->route('panel.index');


    }
}
