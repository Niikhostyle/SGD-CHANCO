<?php
use App\Http\Controllers\BuscadorController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\BuzonController;
use App\Http\Controllers\DocumentoBuzonArchivoController;
use App\Http\Controllers\TipoDocumentoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\DocumentoValidadorController;

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
Route::middleware(['auth:sanctum', 'verified'])->get('buscadorListar/',[BuscadorController::class,'listar'])->name('buscador.listar');
Route::middleware(['auth:sanctum', 'verified'])->get('buscador/{id}',[BuscadorController::class,'show'])->name('buscador.show');

//files
Route::middleware(['auth:sanctum', 'verified'])->resource('files',DocumentoBuzonArchivoController::class);
Route::middleware(['auth:sanctum', 'verified'])->get('files/{id}',[DocumentoBuzonArchivoController::class, 'ver'])->name('files.ver');
//Route::middleware(['auth:sanctum', 'verified'])->post('files',[DocumentoBuzonArchivoController::class,'store'])->name('files.store');
Route::middleware(['auth:sanctum', 'verified'])->get('imagenes/{filename}', [DocumentoBuzonArchivoController::class,'displayImage'])->name('imagenes.displayImage');

//buzones
Route::middleware(['auth:sanctum', 'verified'])->get('buzones',[BuzonController::class,'index'])->name('buzones.index');
Route::middleware(['auth:sanctum', 'verified'])->post('buzones',[BuzonController::class,'store'])->name('buzones.store');
Route::middleware(['auth:sanctum', 'verified'])->get('buzones/{id}',[BuzonController::class,'show'])->name('buzones.show');
Route::middleware(['auth:sanctum', 'verified'])->put('buzones',[BuzonController::class,'update'])->name('buzones.update');
Route::middleware(['auth:sanctum', 'verified'])->delete('buzones/{id}',[BuzonController::class,'delete'])->name('buzones.delete');

//documentos carpetas
Route::middleware(['auth:sanctum', 'verified'])->get('buzonesCarpetas/{id}',[BuzonController::class,'carpetas'])->name('buzones.carpetas');
Route::middleware(['auth:sanctum', 'verified'])->post('buzonesCarpetas',[BuzonController::class,'store_documento'])->name('buzones.store_documento');
Route::middleware(['auth:sanctum', 'verified'])->put('buzonesCarpetas',[BuzonController::class,'update_documento'])->name('buzones.update_documento');
Route::middleware(['auth:sanctum', 'verified'])->put('buzonesCarpetas/{id}',[BuzonController::class,'enviar_documento'])->name('buzones.enviar_documento');
Route::middleware(['auth:sanctum', 'verified'])->get('buzonesListar/',[BuzonController::class,'listar'])->name('buzones.listar');
Route::middleware(['auth:sanctum', 'verified'])->get('documentos/{id}',[BuzonController::class,'ver_documento'])->name('documentos.ver');
Route::middleware(['auth:sanctum', 'verified'])->put('actualizar_estado_documento/{id}',[BuzonController::class,'actualizar_estado_documento'])->name('documentos.actualizar_estado');
Route::middleware(['auth:sanctum', 'verified'])->put('archivar_documento/{id}',[BuzonController::class,'archivar_documento'])->name('documentos.archivar');
Route::middleware(['auth:sanctum', 'verified'])->put('derivarOpcion1',[BuzonController::class,'derivarOpcion1'])->name('documentos.derivarOpcion1');
Route::middleware(['auth:sanctum', 'verified'])->put('accion_editar/{id}',[BuzonController::class,'accion_editar_documento'])->name('documentos.editar');

//tipos de documentos

Route::middleware(['auth:sanctum', 'verified'])->get('tipos_documentos',[TipoDocumentoController::class,'index'])->name('tipos_documentos.index');
Route::middleware(['auth:sanctum', 'verified'])->post('tipos_documentos',[TipoDocumentoController::class,'store'])->name('tipos_documentos.store');
Route::middleware(['auth:sanctum', 'verified'])->put('tipos_documentos',[TipodocumentoController::class,'update'])->name('tipos_documentos.update');
Route::middleware(['auth:sanctum', 'verified'])->get('tipos_documentos/{id}',[TipodocumentoController::class,'show'])->name('tipos_documentos.show');
Route::middleware(['auth:sanctum', 'verified'])->delete('tipos_documentos/{id}',[TipodocumentoController::class,'delete'])->name('tipos_documentos.delete');

//favorito
Route::middleware(['auth:sanctum', 'verified'])->get('favoritos',[FavoritoController::class,'index'])->name('favoritos.index');
Route::middleware(['auth:sanctum', 'verified'])->get('favoritos/{id}',[FavoritoController::class,'show'])->name('favoritos.show');
Route::middleware(['auth:sanctum', 'verified'])->put('favoritos/{id}',[FavoritoController::class,'estado'])->name('favoritos.estado');
//validador
Route::middleware(['auth:sanctum', 'verified'])->get('validador',[DocumentoValidadorController::class,'index'])->name('validador.index');
Route::middleware(['auth:sanctum', 'verified'])->post('validadorCodigo',[DocumentoValidadorController::class,'store'])->name('validador.store');



