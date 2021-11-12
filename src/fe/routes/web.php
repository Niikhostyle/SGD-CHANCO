<?php
use App\Http\Controllers\BuscadorController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\BuzonController;
use App\Http\Controllers\DocumentoBuzonArchivoController;
use App\Http\Controllers\TipoDocumentoController;
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
Route::middleware(['auth:sanctum', 'verified'])->put('usuarios',[UsuarioController::class,'update'])->name('usuarios.update');
Route::middleware(['auth:sanctum', 'verified'])->put('usuarios/{id}',[UsuarioController::class,'estado'])->name('usuarios.estado');

//Documentos
Route::middleware(['auth:sanctum', 'verified'])->get('buscador',[BuscadorController::class,'index'])->name('buscador.index');
//files
Route::middleware(['auth:sanctum', 'verified'])->resource('files',DocumentoBuzonArchivoController::class);

//buzones
Route::middleware(['auth:sanctum', 'verified'])->get('buzones',[BuzonController::class,'index'])->name('buzones.index');
Route::middleware(['auth:sanctum', 'verified'])->post('buzones',[BuzonController::class,'store'])->name('buzones.store');
Route::middleware(['auth:sanctum', 'verified'])->get('buzones/{id}',[BuzonController::class,'show'])->name('buzones.show');
Route::middleware(['auth:sanctum', 'verified'])->put('buzones',[BuzonController::class,'update'])->name('buzones.update');
Route::middleware(['auth:sanctum', 'verified'])->delete('buzones/{id}',[BuzonController::class,'delete'])->name('buzones.delete');
Route::middleware(['auth:sanctum', 'verified'])->get('buzonesCarpetas/{id}',[BuzonController::class,'carpetas'])->name('buzones.carpetas');
Route::middleware(['auth:sanctum', 'verified'])->post('buzonesCarpetas',[BuzonController::class,'store_documento'])->name('buzones.store_documento');
Route::middleware(['auth:sanctum', 'verified'])->put('buzonesCarpetas',[BuzonController::class,'update_documento'])->name('buzones.update_documento');
Route::middleware(['auth:sanctum', 'verified'])->put('buzonesCarpetas/{id}',[BuzonController::class,'enviar_documento'])->name('buzones.enviar_documento');
Route::middleware(['auth:sanctum', 'verified'])->get('buzonesListar/',[BuzonController::class,'listar'])->name('buzones.listar');


//tipos de documentos

Route::middleware(['auth:sanctum', 'verified'])->get('tipos_documentos',[TipoDocumentoController::class,'index'])->name('tipos_documentos.index');
Route::middleware(['auth:sanctum', 'verified'])->post('tipos_documentos',[TipoDocumentoController::class,'store'])->name('tipos_documentos.store');
Route::middleware(['auth:sanctum', 'verified'])->put('tipos_documentos',[TipodocumentoController::class,'update'])->name('tipos_documentos.update');
Route::middleware(['auth:sanctum', 'verified'])->get('tipos_documentos/{id}',[TipodocumentoController::class,'show'])->name('tipos_documentos.show');
Route::middleware(['auth:sanctum', 'verified'])->delete('tipos_documentos/{id}',[TipodocumentoController::class,'delete'])->name('tipos_documentos.delete');

