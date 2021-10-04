<?php

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\BuzonController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth:sanctum', 'verified'])->get('/', function () {
    return view('panel.index');
})->name('panel');

//usuarios
Route::middleware(['auth:sanctum', 'verified'])->get('usuarios',[UsuarioController::class,'index'])->name('usuarios.index');
Route::middleware(['auth:sanctum', 'verified'])->post('usuarios',[UsuarioController::class,'store'])->name('usuarios.store');
Route::middleware(['auth:sanctum', 'verified'])->get('usuarios/{id}',[UsuarioController::class,'show'])->name('usuarios.show');
//Route::middleware(['auth:sanctum', 'verified'])->get('usuarios/{id}/update',[UsuarioController::class,'update'])->name('usuarios.update');
//Route::middleware(['auth:sanctum', 'verified'])->put('usuarios/{id}',[UsuarioController::class,'put'])->name('usuarios.put');

//Documentos
Route::middleware(['auth:sanctum', 'verified'])->get('buscador',[BuscadorController::class,'index'])->name('buscador.index');

//buzones
Route::middleware(['auth:sanctum', 'verified'])->get('buzones',[BuzonController::class,'index'])->name('buzones.index');
Route::middleware(['auth:sanctum', 'verified'])->post('buzones',[BuzonController::class,'store'])->name('buzones.store');
Route::middleware(['auth:sanctum', 'verified'])->get('buzones/{id}',[BuzonController::class,'show'])->name('buzones.show');
Route::middleware(['auth:sanctum', 'verified'])->put('buzones',[BuzonController::class,'update'])->name('buzones.update');
Route::middleware(['auth:sanctum', 'verified'])->delete('buzones/{id}',[BuzonController::class,'delete'])->name('buzones.delete');
