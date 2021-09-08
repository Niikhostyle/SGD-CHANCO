<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\View;
use App\Models\User;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    Public function index(){


        return View::make('usuario.index');

    }

    public function create(){

    }

    public function show(){

    }
}
