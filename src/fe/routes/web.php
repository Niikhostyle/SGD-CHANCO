<?php

use App\Http\Controllers\UsuarioController;
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


Route::middleware(['auth:sanctum', 'verified'])->get('usuarios',[UsuarioController::class,'index'])->name('usuarios.index');
Route::middleware(['auth:sanctum', 'verified'])->post('usuarios',[UsuarioController::class,'store'])->name('usuarios.store');
Route::middleware(['auth:sanctum', 'verified'])->get('usuarios/{id}',[UsuarioController::class,'show'])->name('usuarios.show');
//Route::middleware(['auth:sanctum', 'verified'])->get('usuarios/{id}/update',[UsuarioController::class,'update'])->name('usuarios.update');
//Route::middleware(['auth:sanctum', 'verified'])->put('usuarios/{id}',[UsuarioController::class,'put'])->name('usuarios.put');
