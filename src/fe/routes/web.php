<?php

use App\Http\Controllers\ArchivoController;
use App\Http\Controllers\BuscadorController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\BuzonController;
use App\Http\Controllers\DocumentoBuzonArchivoController;
use App\Http\Controllers\TipoDocumentoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\DocumentoValidadorController;
use App\Http\Controllers\BuzonUsuarioExternoController;
//use App\Http\Controllers\DescargaPdfController;
use App\Http\Controllers\DescargaController;
use App\Http\Controllers\PLCController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\AuditoriaFoliosController;

use App\Jobs\Firma;

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

//Route::middleware(['auth:sanctum', 'verified'])->get('/', function () { return view('panel.index'); })->name('panel');

Route::middleware(['auth:sanctum', 'verified'])->get('/',[PanelController::class,'index'])->name('panel.index');
Route::middleware(['auth:sanctum', 'verified'])->post('captura',[PanelController::class,'captura'])->name('panel.captura');

//usuarios
Route::middleware(['auth:sanctum', 'verified'])->get('usuarios',[UsuarioController::class,'index'])->name('usuarios.index');
Route::middleware(['auth:sanctum', 'verified'])->post('usuarios',[UsuarioController::class,'store'])->name('usuarios.store');
//Route::middleware(['auth:sanctum', 'verified'])->post('usuarios_new',[UsuarioController::class,'store'])->name('usuarios.store');
Route::middleware(['auth:sanctum', 'verified'])->get('usuarios/{id}',[UsuarioController::class,'show'])->name('usuarios.show');
Route::middleware(['auth:sanctum', 'verified'])->post('usuarios_img',[UsuarioController::class,'update'])->name('usuarios.update');
Route::middleware(['auth:sanctum', 'verified'])->put('usuarios/{id}',[UsuarioController::class,'estado'])->name('usuarios.estado');
Route::middleware(['auth:sanctum', 'verified'])->post('buscar_usuarios',[UsuarioController::class,'buscar'])->name('usuarios.buscar');

//Documentos
Route::middleware(['auth:sanctum', 'verified'])->get('buscador',[BuscadorController::class,'index'])->name('buscador.index');
Route::middleware(['auth:sanctum', 'verified'])->get('buscadorListar/',[BuscadorController::class,'listar'])->name('buscador.listar');
Route::middleware(['auth:sanctum', 'verified'])->get('buscador/{id}',[BuscadorController::class,'show'])->name('buscador.show');
Route::middleware(['auth:sanctum', 'verified'])->get('descargar_documento',[BuscadorController::class,'documentoBuzonArchivo'])->name('buscador.documentoBuzonArchivo');
Route::middleware(['auth:sanctum', 'verified'])->get('descargar_documento2',[BuscadorController::class,'descargar'])->name('buscador.descargar');
Route::middleware(['auth:sanctum', 'verified'])->get('buscar_categorias',[BuscadorController::class,'buscar'])->name('buscador.categorias');
Route::middleware(['auth:sanctum', 'verified'])->get('eliminar_documento',[BuzonController::class,'eliminar_documento_enviado'])->name('buzones.eliminar_documento_enviado');

//version 2 - buscador
Route::middleware(['auth:sanctum', 'verified'])->get('buscador2',[BuscadorController::class,'index2'])->name('buscador.index2');
Route::middleware(['auth:sanctum', 'verified'])->get('buscadorListar2/',[BuscadorController::class,'listar2'])->name('buscador.listar2');


//files
Route::middleware(['auth:sanctum', 'verified'])->resource('files',DocumentoBuzonArchivoController::class);
//Route::middleware(['auth:sanctum', 'verified'])->get('files/{id}',[DocumentoBuzonArchivoController::class, 'ver'])->name('files.ver');
Route::get('files/{id}',[DocumentoBuzonArchivoController::class, 'ver'])->name('files.ver');
//Route::middleware(['auth:sanctum', 'verified'])->post('files',[DocumentoBuzonArchivoController::class,'store'])->name('files.store');

//Route::middleware(['auth:sanctum', 'verified'])->get('files/{filename}', [DocumentoBuzonArchivoController::class,'show'])->name('files.show');

Route::get('descargarPdf/{filename}', [DocumentoBuzonArchivoController::class,'validarUrl']);
Route::get('pruebaPublica', [DocumentoBuzonArchivoController::class,'pruebaPublica']);

//buzones
Route::middleware(['auth:sanctum', 'verified'])->get('buzones',[BuzonController::class,'index'])->name('buzones.index');
Route::middleware(['auth:sanctum', 'verified'])->post('buzones',[BuzonController::class,'store'])->name('buzones.store');
Route::middleware(['auth:sanctum', 'verified'])->get('buzones/{id}',[BuzonController::class,'show'])->name('buzones.show');
Route::middleware(['auth:sanctum', 'verified'])->put('buzones',[BuzonController::class,'update'])->name('buzones.update');
Route::middleware(['auth:sanctum', 'verified'])->delete('buzones/{id}',[BuzonController::class,'delete'])->name('buzones.delete');
Route::middleware(['auth:sanctum', 'verified'])->post('buscarDocumento',[BuzonController::class,'buscar_documento_buzon'])->name('documentos.buscar_documento_buzon');


//documentos carpetas
Route::middleware(['auth:sanctum', 'verified'])->get('buzonesCarpetas/{id}',[BuzonController::class,'carpetas'])->name('buzones.carpetas');
Route::middleware(['auth:sanctum', 'verified'])->post('buzonesCarpetas',[BuzonController::class,'store_documento'])->name('buzones.store_documento');
Route::middleware(['auth:sanctum', 'verified'])->put('buzonesCarpetas',[BuzonController::class,'update_documento'])->name('buzones.update_documento');
Route::middleware(['auth:sanctum', 'verified'])->put('buzonesCarpetas/{id}',[BuzonController::class,'enviar_documento'])->name('buzones.enviar_documento');
Route::middleware(['auth:sanctum', 'verified'])->get('buzonesListar/',[BuzonController::class,'listar'])->name('buzones.listar');
Route::middleware(['auth:sanctum', 'verified'])->get('documentos/{id}',[BuzonController::class,'ver_documento'])->name('documentos.ver');
Route::middleware(['auth:sanctum', 'verified'])->put('actualizar_estado_documento/{id}',[BuzonController::class,'actualizar_estado_documento'])->name('documentos.actualizar_estado');
Route::middleware(['auth:sanctum', 'verified'])->put('firmar_documento/{id}',[BuzonController::class,'firmar_documento'])->name('documentos.firmar');
Route::middleware(['auth:sanctum', 'verified'])->put('firma_masiva',[BuzonController::class,'firma_masiva'])->name('documentos.firma_masiva');
Route::middleware(['auth:sanctum', 'verified'])->delete('documento',[BuzonController::class,'delete_documento'])->name('buzones.delete_documento');
Route::middleware(['auth:sanctum', 'verified'])->get('eliminar_documento',[BuzonController::class,'eliminar_documento_enviado'])->name('buzones.eliminar_documento_enviado');


Route::middleware(['auth:sanctum', 'verified'])->put('archivar_documento/{id}',[BuzonController::class,'archivar_documento'])->name('documentos.archivar');
Route::middleware(['auth:sanctum', 'verified'])->put('derivarOpcion1',[BuzonController::class,'derivarOpcion1'])->name('documentos.derivarOpcion1');
Route::middleware(['auth:sanctum', 'verified'])->put('accion_editar/{id}',[BuzonController::class,'accion_editar_documento'])->name('documentos.editar');
Route::middleware(['auth:sanctum', 'verified'])->put('generar_archivo',[BuzonController::class,'generar_archivo_pdf'])->name('documentos.generar');
Route::middleware(['auth:sanctum', 'verified'])->get('vista_previa',[BuzonController::class,'generar_vista_previa'])->name('documentos.vista_previa');
Route::middleware(['auth:sanctum', 'verified'])->post('vista_previa_sg',[BuzonController::class,'generar_vista_previa_sg'])->name('documentos.vista_previa_sg');
Route::middleware(['auth:sanctum', 'verified'])->get('vista_previa_sg/{id}',[BuzonController::class,'vp_sg'])->name('documentos.vp_sg');
Route::middleware(['auth:sanctum', 'verified'])->get('clonar',[BuzonController::class,'clonar'])->name('documentos.clonar');


//tipos de documentos
Route::middleware(['auth:sanctum', 'verified'])->get('tipos_documentos',[TipoDocumentoController::class,'index'])->name('tipos_documentos.index');
Route::middleware(['auth:sanctum', 'verified'])->post('tipos_documentos',[TipoDocumentoController::class,'store'])->name('tipos_documentos.store');
Route::middleware(['auth:sanctum', 'verified'])->put('tipos_documentos',[TipodocumentoController::class,'update'])->name('tipos_documentos.update');
Route::middleware(['auth:sanctum', 'verified'])->get('tipos_documentos/{id}',[TipodocumentoController::class,'show'])->name('tipos_documentos.show');
Route::middleware(['auth:sanctum', 'verified'])->delete('tipos_documentos/{id}',[TipodocumentoController::class,'delete'])->name('tipos_documentos.delete');

//favorito
Route::middleware(['auth:sanctum', 'verified'])->get('favoritos',[FavoritoController::class,'index'])->name('favoritos.index');
Route::middleware(['auth:sanctum', 'verified'])->put('favoritos/{id}',[FavoritoController::class,'estado'])->name('favoritos.estado');
//validador
Route::get('validador',[DocumentoValidadorController::class,'index'])->name('validador.index');
Route::post('validadorCodigo',[DocumentoValidadorController::class,'store'])->name('validador.store');
Route::get('validador_qr/{id}',[DocumentoValidadorController::class,'validar_qr'])->name('validador.validar_qr'); 
//Route::middleware(['auth:sanctum', 'verified'])->get('pdf',[DocumentoValidadorController::class,'download'])->name('validador.download');

//buscador
Route::middleware(['auth:sanctum', 'verified'])->get('buscador/{id}',[BuscadorController::class,'show'])->name('buscador.show');

//documentos carpetas
Route::middleware(['auth:sanctum', 'verified'])->get('externo',[BuzonUsuarioExternoController::class,'index'])->name('externo.index');
Route::middleware(['auth:sanctum', 'verified'])->get('buzonesExterno/{id}',[BuzonUsuarioExternoController::class,'show'])->name('externo.show');

Route::middleware(['auth:sanctum', 'verified'])->get('buzonesCarpetasExterno/{id}',[BuzonUsuarioExternoController::class,'carpetas'])->name('externo.carpetas');
Route::middleware(['auth:sanctum', 'verified'])->post('buzonesCarpetasExterno',[BuzonUsuarioExternoController::class,'store_documento'])->name('externo.store_documento');
Route::middleware(['auth:sanctum', 'verified'])->put('buzonesCarpetasExterno',[BuzonUsuarioExternoController::class,'update_documento'])->name('externo.update_documento');
Route::middleware(['auth:sanctum', 'verified'])->put('buzonesCarpetasExterno/{id}',[BuzonUsuarioExternoController::class,'enviar_documento'])->name('externo.enviar_documento');
Route::middleware(['auth:sanctum', 'verified'])->get('buzonesListarExterno/',[BuzonUsuarioExternoController::class,'listar'])->name('externo.listar');
Route::middleware(['auth:sanctum', 'verified'])->get('documentosExterno/{id}',[BuzonUsuarioExternoController::class,'ver_documento'])->name('documentosExterno.ver');
Route::middleware(['auth:sanctum', 'verified'])->put('actualizar_estado_documentoExterno/{id}',[BuzonUsuarioExternoController::class,'actualizar_estado_documento'])->name('documentosExterno.actualizar_estado');
Route::middleware(['auth:sanctum', 'verified'])->put('archivar_documentoExterno/{id}',[BuzonUsuarioExternoController::class,'archivar_documento'])->name('documentosExterno.archivar');
Route::middleware(['auth:sanctum', 'verified'])->put('derivarOpcion1Externo',[BuzonUsuarioExternoController::class,'derivarOpcion1'])->name('documentosExterno.derivarOpcion1');
Route::middleware(['auth:sanctum', 'verified'])->put('accion_editarExterno/{id}',[BuzonUsuarioExternoController::class,'accion_editar_documento'])->name('documentosExterno.editar');
Route::middleware(['auth:sanctum', 'verified'])->put('generar_archivoExterno',[BuzonUsuarioExternoController::class,'generar_archivo_pdf'])->name('documentosExterno.generar');

//imagenes ckeditor
//Route::middleware(['auth:sanctum', 'verified'])->get('add_imagen',[DocumentoBuzonArchivoController::class,'add'])->name('add_imagen.add');

//Route::middleware(['auth:sanctum', 'verified'])->post('add_imagen',[DocumentoBuzonArchivoController::class,'add'])->name('add_imagen.add');

//ckfinder
Route::any('/ckfinder/connector', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')->name('ckfinder_connector');
Route::any('/ckfinder/browser', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')->name('ckfinder_browser');

Route::middleware(['auth:sanctum', 'verified'])->get('files/editor/images/{filename}', [DocumentoBuzonArchivoController::class,'showImage'])->name('images.show');
Route::middleware(['auth:sanctum', 'verified'])->get('files/imagen_firma/{filename}', [DocumentoBuzonArchivoController::class,'showImageFirma'])->name('images.showFirma');
Route::middleware(['auth:sanctum', 'verified'])->get('files/imagen_perfil/{filename}', [DocumentoBuzonArchivoController::class,'showImagePerfil'])->name('images.showImagePerfil');


//verificacion archivos
Route::get('pdf/{id}', [DescargaPdfController::class,'descarga'])->name('pdf.descarga');

Route::get('Firma', [DescargaPdfController::class,'descarga']);

//Route::put('/firmamasiva', function () {
//    Firma::dispatch("Mensaje de firma");
//});

Route::middleware(['auth:sanctum', 'verified'])->get('descargas',[DescargaController::class,'index'])->name('descargas.index');

//Route::middleware(['auth:sanctum', 'verified'])->get('descargar_docto',[PLCController::class,'getDoc'])->name('buscador.descargar_plc');
Route::get('descargar_docto',[DocumentoBuzonArchivoController::class,'getDoc'])->name('buscador.descargar_plc');
Route::get('download_publico',[DocumentoBuzonArchivoController::class,'download_publico'])->name('buscador.download_publico');
Route::get('download_publico_anexo',[DocumentoBuzonArchivoController::class,'download_publico_anexo'])->name('buscador.download_publico_anexo');



//auditoria de folios
Route::middleware(['auth:sanctum', 'verified'])->get('auditoria_folios',[AuditoriaFoliosController::class,'index'])->name('auditoria_folios.index');
Route::middleware(['auth:sanctum', 'verified'])->get('obtener_tipos_documentos',[AuditoriaFoliosController::class,'obtener_tipos_documentos'])->name('auditoria_folios.obtener_tipos_documentos');
Route::middleware(['auth:sanctum', 'verified'])->get('obtener_folios',[AuditoriaFoliosController::class,'obtener_folios'])->name('auditoria_folios.obtener_folios');

//perfil de usuario
Route::middleware(['auth:sanctum', 'verified'])->get('perfil',[UsuarioController::class,'perfil'])->name('usuario.perfil');
