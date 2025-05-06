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
use App\Http\Controllers\DocDigitalController;
use App\Http\Middleware\PermisosMiddleware;

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

Route::middleware(['auth:sanctum', 'verified'])->get('/', [PanelController::class, 'index'])->name('panel.index');
Route::middleware(['auth:sanctum', 'verified'])->post('captura', [PanelController::class, 'captura'])->name('panel.captura');

//usuarios
Route::middleware(['auth:sanctum', 'verified',PermisosMiddleware::class.':admin'])->group(function () {
    Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    //Route::post('usuarios_new',[UsuarioController::class,'store'])->name('usuarios.store');
    Route::get('usuarios/{id}', [UsuarioController::class, 'show'])->name('usuarios.show');
    Route::post('usuarios_img', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::put('usuarios/{id}', [UsuarioController::class, 'estado'])->name('usuarios.estado');
    Route::post('buscar_usuarios', [UsuarioController::class, 'buscar'])->name('usuarios.buscar');
});

//Documentos
Route::middleware(['auth:sanctum', 'verified'])->get('buscador',[BuscadorController::class,'index'])->name('buscador.index');
Route::middleware(['auth:sanctum', 'verified'])->get('buscadorListar/',[BuscadorController::class,'listar'])->name('buscador.listar');
Route::middleware(['auth:sanctum', 'verified'])->get('buscador/{id}',[BuscadorController::class,'show'])->name('buscador.show'); //bitacora
Route::middleware(['auth:sanctum', 'verified'])->get('bitacora/{id}',[BuscadorController::class,'bitacora'])->name('documento.bitacora'); //bitacora
Route::middleware(['auth:sanctum', 'verified'])->get('descargar_documento',[BuscadorController::class,'documentoBuzonArchivo'])->name('buscador.documentoBuzonArchivo');
Route::middleware(['auth:sanctum', 'verified'])->get('descargar_documento2',[BuscadorController::class,'descargar'])->name('buscador.descargar');
Route::middleware(['auth:sanctum', 'verified'])->get('buscar_categorias',[BuscadorController::class,'buscar'])->name('buscador.categorias');
Route::middleware(['auth:sanctum', 'verified'])->get('eliminar_documento',[BuzonController::class,'eliminar_documento_enviado'])->name('buzones.eliminar_documento_enviado');

//version 2 - buscador
Route::middleware(['auth:sanctum', 'verified'])->get('buscador2', [BuscadorController::class, 'index2'])->name('buscador.index2');
Route::middleware(['auth:sanctum', 'verified'])->get('buscadorListar2/', [BuscadorController::class, 'listar2'])->name('buscador.listar2');
Route::middleware(['auth:sanctum', 'verified'])->get('buscarReferenciaSGD', [BuscadorController::class, 'buscarDocumentoReferencia'])->name('buscador.referenciasgd');

Route::middleware(['auth:sanctum', 'verified'])->get('buscador/{id}', [BuscadorController::class, 'show'])->name('buscador.show');
Route::middleware(['auth:sanctum', 'verified'])->get('buscador/{id}/bitacora', [BuscadorController::class, 'bitacora'])->name('buscador.bitacora');


//contador de pendientes
Route::middleware(['auth:sanctum', 'verified'])->get('getContadores',[BuzonController::class,'getContadores'])->name('index.getContadores');

//files
Route::middleware(['auth:sanctum', 'verified'])->resource('files',DocumentoBuzonArchivoController::class);
Route::middleware(['auth:sanctum', 'verified'])->post('filesSGD',[DocumentoBuzonArchivoController::class,'uploadReferenciaSGD'])->name('archivo.uploadreferenciasgd');


//Route::middleware(['auth:sanctum', 'verified'])->get('files/{id}',[DocumentoBuzonArchivoController::class, 'ver'])->name('files.ver');
Route::get('files/{id}',[DocumentoBuzonArchivoController::class, 'ver'])->name('files.ver');
//Route::middleware(['auth:sanctum', 'verified'])->post('files',[DocumentoBuzonArchivoController::class,'store'])->name('files.store');

//Route::middleware(['auth:sanctum', 'verified'])->get('files/{filename}', [DocumentoBuzonArchivoController::class,'show'])->name('files.show');

Route::get('descargarPdf/{filename}', [DocumentoBuzonArchivoController::class, 'validarUrl']);
Route::get('pruebaPublica', [DocumentoBuzonArchivoController::class, 'pruebaPublica']);

//buzones
Route::middleware(['auth:sanctum', 'verified',PermisosMiddleware::class.':admin'])->group(function () {
    Route::get('buzones', [BuzonController::class, 'index'])->name('buzones.index');
    Route::post('buzones', [BuzonController::class, 'store'])->name('buzones.store');
    Route::get('buzones/{id}', [BuzonController::class, 'show'])->name('buzones.show');
    Route::put('buzones', [BuzonController::class, 'update'])->name('buzones.update');
    Route::delete('buzones/{id}', [BuzonController::class, 'delete'])->name('buzones.delete');
});

//documentos carpetas  (por idbuzon)
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('buzonesCarpetas/{id}',[BuzonController::class,'carpetas'])->name('buzones.carpetas');
    Route::get('buzonesListar/{id}',[BuzonController::class,'listar'])->name('buzones.listar');
    Route::post('documentos/{id}',[BuzonController::class,'store_documento'])->name('buzones.store_documento');
    Route::post('documentos/{id}/buscarGlobal', [BuzonController::class, 'buscar_documento_buzon'])->name('buzones.buscar_global');
    Route::put('documentos/{id}/firma_masiva',[BuzonController::class,'firma_masiva'])->name('documentos.firma_masiva');
});

//buzon documento (iddoc y idbuzondoc)
Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::get('documentos/{buzon}/{id}',[BuzonController::class,'ver_documento'])->name('documentos.ver');
    Route::put('documentos/{buzon}/{id}',[BuzonController::class,'actualizar_estado_documento'])->name('documentos.actualizar_estado');
    
    Route::put('documentos/{buzon}/{id}/update',[BuzonController::class,'update_documento'])->name('documentos.update');  //
    Route::put('documentos/{buzon}/{id}/editar',[BuzonController::class,'accion_editar_documento'])->name('buzones.editar');
    Route::put('documentos/{buzon}/{id}/enviar',[BuzonController::class,'enviar_documento'])->name('documentos.enviar_documento');
    Route::put('documentos/{buzon}/{id}/clonar',[BuzonController::class,'clonar'])->name('documentos.clonar');
    Route::put('documentos/{buzon}/{id}/generar',[BuzonController::class,'generar_archivo_pdf'])->name('documentos.generar');
    Route::put('documentos/{buzon}/{id}/archivar',[BuzonController::class,'archivar_documento'])->name('documentos.archivar');
    Route::put('documentos/{buzon}/{id}/devolver',[BuzonController::class,'devolver'])->name('documentos.devolver');

    Route::get('documentos/{buzon}/{id}/vista_previa',[BuzonController::class,'generar_vista_previa'])->name('documentos.vista_previa');
    //Route::get('documentos/{buzon}/{id}/vista_previa_sg',[BuzonController::class,'generar_vista_previa_sg'])->name('documentos.vista_previa_sg');

    Route::put('documentos/{buzon}/{id}/firmar',[BuzonController::class,'firmar_documento'])->name('documentos.firmar');
    
       
    Route::delete('documentos/{buzon}/{id}/eliminar',[BuzonController::class,'delete_documento'])->name('documentos.delete');
    Route::get('documentos/{buzon}/{id}/eliminar_envio',[BuzonController::class,'eliminar_documento_enviado'])->name('documentos.eliminar_documento_enviado');
    
});

Route::middleware(['auth:sanctum', 'verified'])->post('vista_previa_sg',[BuzonController::class,'generar_vista_previa_sg'])->name('documentos.vista_previa_sg');
Route::middleware(['auth:sanctum', 'verified'])->get('vista_previa_sg/{id}',[BuzonController::class,'vp_sg'])->name('documentos.vp_sg');
//Route::middleware(['auth:sanctum', 'verified'])->put('derivarOpcion1',[BuzonController::class,'derivarOpcion1'])->name('documentos.derivarOpcion1');

//tipos de documentos
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('tipos_documentos', [TipoDocumentoController::class, 'index'])->name('tipos_documentos.index');
    Route::post('tipos_documentos', [TipoDocumentoController::class, 'store'])->name('tipos_documentos.store');
    Route::put('tipos_documentos', [TipodocumentoController::class, 'update'])->name('tipos_documentos.update');    
    Route::delete('tipos_documentos/{id}', [TipodocumentoController::class, 'delete'])->name('tipos_documentos.delete');
});
Route::get('tipos_documentos/{id}', [TipodocumentoController::class, 'show'])->name('tipos_documentos.show');

//favorito
Route::middleware(['auth:sanctum', 'verified'])->get('favoritos', [FavoritoController::class, 'index'])->name('favoritos.index');
Route::middleware(['auth:sanctum', 'verified'])->put('favoritos/{id}', [FavoritoController::class, 'estado'])->name('favoritos.estado');
//validador
Route::get('validador',[DocumentoValidadorController::class,'index'])->name('validador.index');
Route::post('validadorCodigo',[DocumentoValidadorController::class,'store'])->name('validador.store');
Route::get('validador_qr/{id}',[DocumentoValidadorController::class,'validar_qr'])->name('validador.validar_qr'); 
//Route::middleware(['auth:sanctum', 'verified'])->get('pdf',[DocumentoValidadorController::class,'download'])->name('validador.download');



//documentos carpetas
Route::middleware(['auth:sanctum', 'verified'])->get('externo', [BuzonUsuarioExternoController::class, 'index'])->name('externo.index');
Route::middleware(['auth:sanctum', 'verified'])->get('buzonesExterno/{id}', [BuzonUsuarioExternoController::class, 'show'])->name('externo.show');
Route::middleware(['auth:sanctum', 'verified'])->get('buzonesCarpetasExterno/{id}', [BuzonUsuarioExternoController::class, 'carpetas'])->name('externo.carpetas');
Route::middleware(['auth:sanctum', 'verified'])->post('buzonesCarpetasExterno', [BuzonUsuarioExternoController::class, 'store_documento'])->name('externo.store_documento');
Route::middleware(['auth:sanctum', 'verified'])->put('buzonesCarpetasExterno', [BuzonUsuarioExternoController::class, 'update_documento'])->name('externo.update_documento');
Route::middleware(['auth:sanctum', 'verified'])->put('buzonesCarpetasExterno/{id}', [BuzonUsuarioExternoController::class, 'enviar_documento'])->name('externo.enviar_documento');
Route::middleware(['auth:sanctum', 'verified'])->get('buzonesListarExterno/', [BuzonUsuarioExternoController::class, 'listar'])->name('externo.listar');
Route::middleware(['auth:sanctum', 'verified'])->get('documentosExterno/{id}', [BuzonUsuarioExternoController::class, 'ver_documento'])->name('documentosExterno.ver');
Route::middleware(['auth:sanctum', 'verified'])->put('actualizar_estado_documentoExterno/{id}', [BuzonUsuarioExternoController::class, 'actualizar_estado_documento'])->name('documentosExterno.actualizar_estado');
Route::middleware(['auth:sanctum', 'verified'])->put('archivar_documentoExterno/{id}', [BuzonUsuarioExternoController::class, 'archivar_documento'])->name('documentosExterno.archivar');
Route::middleware(['auth:sanctum', 'verified'])->put('derivarOpcion1Externo', [BuzonUsuarioExternoController::class, 'derivarOpcion1'])->name('documentosExterno.derivarOpcion1');
Route::middleware(['auth:sanctum', 'verified'])->put('accion_editarExterno/{id}', [BuzonUsuarioExternoController::class, 'accion_editar_documento'])->name('documentosExterno.editar');
Route::middleware(['auth:sanctum', 'verified'])->put('generar_archivoExterno', [BuzonUsuarioExternoController::class, 'generar_archivo_pdf'])->name('documentosExterno.generar');

//imagenes ckeditor
//Route::middleware(['auth:sanctum', 'verified'])->get('add_imagen',[DocumentoBuzonArchivoController::class,'add'])->name('add_imagen.add');

//Route::middleware(['auth:sanctum', 'verified'])->post('add_imagen',[DocumentoBuzonArchivoController::class,'add'])->name('add_imagen.add');

//ckfinder
Route::any('/ckfinder/connector', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')->name('ckfinder_connector');
Route::any('/ckfinder/browser', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')->name('ckfinder_browser');

Route::middleware(['auth:sanctum', 'verified'])->get('files/editor/images/{filename}', [DocumentoBuzonArchivoController::class, 'showImage'])->name('images.show');
Route::middleware(['auth:sanctum', 'verified'])->get('files/imagen_firma/{filename}', [DocumentoBuzonArchivoController::class, 'showImageFirma'])->name('images.showFirma');
Route::middleware(['auth:sanctum', 'verified'])->get('files/imagen_perfil/{filename}', [DocumentoBuzonArchivoController::class, 'showImagePerfil'])->name('images.showImagePerfil');


//verificacion archivos
Route::get('pdf/{id}', [DescargaPdfController::class, 'descarga'])->name('pdf.descarga');

Route::get('Firma', [DescargaPdfController::class, 'descarga']);

//Route::put('/firmamasiva', function () {
//    Firma::dispatch("Mensaje de firma");
//});

Route::middleware(['auth:sanctum', 'verified'])->get('descargas', [DescargaController::class, 'index'])->name('descargas.index');

//Route::middleware(['auth:sanctum', 'verified'])->get('descargar_docto',[PLCController::class,'getDoc'])->name('buscador.descargar_plc');
Route::get('descargar_docto', [DocumentoBuzonArchivoController::class, 'getDoc'])->name('buscador.descargar_plc');
Route::get('download_publico', [DocumentoBuzonArchivoController::class, 'download_publico'])->name('buscador.download_publico');
Route::get('download_publico_anexo', [DocumentoBuzonArchivoController::class, 'download_publico_anexo'])->name('buscador.download_publico_anexo');


//Route::middleware(['auth:sanctum', 'verified'])->get('descargar_docto',[PLCController::class,'getDoc'])->name('buscador.descargar_plc');
Route::get('descargar_docto',[DocumentoBuzonArchivoController::class,'getDoc'])->name('buscador.descargar_plc');
Route::get('download_publico',[DocumentoBuzonArchivoController::class,'download_publico'])->name('buscador.download_publico');
Route::get('download_publico_anexo',[DocumentoBuzonArchivoController::class,'download_publico_anexo'])->name('buscador.download_publico_anexo');



//auditoria de folios
Route::middleware(['auth:sanctum', 'verified'])->get('auditoria_folios', [AuditoriaFoliosController::class, 'index'])->name('auditoria_folios.index');
Route::middleware(['auth:sanctum', 'verified'])->get('obtener_tipos_documentos', [AuditoriaFoliosController::class, 'obtener_tipos_documentos'])->name('auditoria_folios.obtener_tipos_documentos');
Route::middleware(['auth:sanctum', 'verified'])->get('obtener_folios', [AuditoriaFoliosController::class, 'obtener_folios'])->name('auditoria_folios.obtener_folios');

//perfil de usuario
Route::middleware(['auth:sanctum', 'verified'])->get('perfil', [UsuarioController::class, 'perfil'])->name('usuario.perfil');

//transparencia
Route::middleware(['auth:sanctum', 'verified'])->get('transparencia', [PLCController::class, 'indexTransparencia'])->name('plc.transparenciaindex');
Route::middleware(['auth:sanctum', 'verified'])->get('transparencia/getitems', [PLCController::class, 'getItems'])->name('plc.transparenciagetitems');
Route::middleware(['auth:sanctum', 'verified'])->get('descargar_documento_plc', [PLCController::class, 'getDoc'])->name('plc.transparenciagetDoc');
Route::middleware(['auth:sanctum', 'verified'])->get('descargar_documento_plc_anexo', [PLCController::class, 'getDocAnexo'])->name('plc.transparenciagetDocAnexo');

//docDigital
Route::middleware(['auth:sanctum', 'verified'])->get('docdigital', [DocDigitalController::class, 'index'])->name('docdigital.index');