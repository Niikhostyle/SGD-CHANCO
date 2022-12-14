<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuditoriaFoliosController extends Controller
{
    public function index(){
        return view('auditoria_folios.index');
    }
}
