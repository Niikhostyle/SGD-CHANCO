<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBuzon;
use App\Models\Buzon;
use App\Models\Users;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\DataTables\UsersDataTable;
use App\Jobs\Firma;
use App\Jobs\FirmarDerivar;
use App\Models\Documento;
use App\Models\DocumentoBuzonArchivo;
use App\Models\DocumentoBuzonBitacora;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\DocumentoBuzon;

//use Barryvdh\DomPDF\PDF;
use PDF;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;
use Yajra\DataTables\DataTables;

use Intervention\Image\ImageManagerStatic as Image;

use App\Mail\MailController;
use App\Models\BuzonUsuario;
use App\Models\DocumentoTmp;
use App\Models\FirmaLog;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
//use Barryvdh\DomPDF\Facade\Pdf;


class BuzonController extends Controller
{
    private $userFirma;

    public function index()
    {

        $sesion_key =  AppServiceProvider::session_key_general();

        $aBuzones = array();
        $listado_buzones = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(30)
            ->withBody(json_encode([
                'texto_busqueda' => '',
            ]), 'json')
            ->get(env('API_SGD_BUZONES','http://sgd_ms_buzones:3333').'/api/sgd-buzones/listar_todos');

        if ($listado_buzones->failed()) {
            $mensaje = $listado_buzones->json()['data']['comentario'];

            $listado_buzones = ['data' => [
                0 => ['id' => '0', 'nombre' => 'Sin Datos', 'nombre_corto' => '', 'total_us_asignados' => '', 'total_us_asignados' => '']
            ]];
            toast($mensaje, 'error');
        } else {

            $aBuzones = $listado_buzones['data'];

            foreach ($aBuzones as $key => $value) {
                if ($value['id_tipo_buzon'] == 1)
                    unset($aBuzones[$key]);
            }
        }

        //listado de usuarios
        $aUsuarios = array();
        $listado_usuarios = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(30)
            ->get(env('API_SGD_USUARIOS','http://sgd_ms_usuarios:3333').'/api/sgd-usuarios/ver_todos');

        if ($listado_usuarios->failed()) {
            $mensaje = $listado_usuarios->json()['data']['comentario'];

            $listado_usuarios = ['data' => [
                0 => ['id' => '0', 'nombres' => 'Sin Datos']
            ]];
            toast($mensaje, 'error');
        } else {
            $aUsuarios = $listado_usuarios['data'];

            foreach ($aUsuarios as $key => $value) {
                if ($value['id_estado_usuario'] == 2)
                    unset($aUsuarios[$key]);
            }
        }


        return View::make('buzon.index', ['listado_buzones' => $aBuzones, 'listado_usuarios' => $aUsuarios]);
    }

    public function store(StoreBuzon $request)
    {
        $sesion_key =  AppServiceProvider::session_key_general();

        $aUsuarios = [];

        if (isset($request->usuarios_asignados)) {
            foreach ($request->usuarios_asignados as $usuario)
                $aUsuarios[] = ['id_usuario' => $usuario];
        }

        $accionBuzon = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(30)
            ->post(env('API_SGD_BUZONES','http://sgd_ms_buzones:3333').'/api/sgd-buzones/crear', [
                'nombre_buzon' => $request->nombre,
                'nombre_corto_buzon' => $request->nombre_corto,
                'tipo_buzon' => '2',
                'usuarios_asignados' => $aUsuarios,
                'titular' => $request->titular_firma,
                'cargo_firma' => $request->cargo_firma,
                'restringir_sr' => $request->restringir,
                'id_usuario_sr' => $request->subrogante
            ]);

        return $accionBuzon->json();
    }

    public function show($id)
    {
        $sesion_key =  AppServiceProvider::session_key_general();

        $datosBuzon = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json']) //
            ->timeout(5)
            ->withBody(json_encode([
                'id_buzon' => $id,
            ]), 'json')
            ->get(env('API_SGD_BUZONES','http://sgd_ms_buzones:3333').'/api/sgd-buzones/ver');

        //listado de usuarios
        $listado_usuarios = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(5)
            ->get(env('API_SGD_USUARIOS','http://sgd_ms_usuarios:3333').'/api/sgd-usuarios/ver_todos');

        if ($listado_usuarios->failed()) {
            $mensaje = $listado_usuarios->json()['data']['comentario'];

            $listado_usuarios = ['data' => [
                0 => ['id' => '0', 'nombres' => 'Sin Datos']
            ]];
            toast($mensaje, 'error');
        } else {
            $aUsuarios = $listado_usuarios['data'];

            foreach ($aUsuarios as $key => $value) {
                if ($value['id_estado_usuario'] == 2)
                    unset($aUsuarios[$key]);
            }
        }

        return $datosBuzon->json();
    }

    public function update(StoreBuzon $request)
    {
        $sesion_key =  AppServiceProvider::session_key_general();

        $aUsuarios = [];
        if (isset($request->usuarios_asignados)) {
            foreach ($request->usuarios_asignados as $usuario)
                $aUsuarios[] = ['id_usuario' => $usuario];
        }

        $accionBuzon = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(30)
            ->put(env('API_SGD_BUZONES','http://sgd_ms_buzones:3333').'/api/sgd-buzones/actualizar', [
                'id_buzon' => $request->hiddBuzon,
                'nombre_buzon' => $request->nombre,
                'nombre_corto_buzon' => $request->nombre_corto,
                'tipo_buzon' => '2',
                'usuarios_asignados' => $aUsuarios,
                'titular' => $request->titular_firma,
                'cargo_firma' => $request->cargo_firma,
                'restringir_sr' => $request->restringir,
                'id_usuario_sr' => $request->subrogante
            ]);

        return $accionBuzon->json();
    }

    public function delete($id)
    {
        $sesion_key = AppServiceProvider::session_key_general();
        $accionBuzon = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json']) //
            ->timeout(30)
            ->withBody(json_encode([
                'id_buzon' => $id,
            ]), 'json')
            ->delete(env('API_SGD_BUZONES','http://sgd_ms_buzones:3333').'/api/sgd-buzones/eliminar');

        return $accionBuzon->json();
    }

    public function carpetas($id)
    {

        $nombre_buzon = "";
        $buzon = Buzon::findOrFail($id);
        $year_actual = session('year');


        $sesion_key =  AppServiceProvider::session_key_general();
        $perfiles_datos = "";
        $estados_usuario = "";
        $n_docs_por_recibir = 0;
        $n_docs_recibidos_pendientes = 0;


        $menuBuzon = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json']) //
            ->timeout(30)
            ->withBody(json_encode([
                'id_usuario' => Auth::user()->id,
                'year_actual' => $year_actual,
            ]), 'json')
            ->get(env('API_SGD_BUZONES','http://sgd_ms_buzones:3333').'/api/sgd-buzones/menu')
            ->throw(function ($response, $e) {});


        // if (isset($menuBuzon['data'])) {
        //     foreach ($menuBuzon['data'] as $key => $value) {
        //         //echo $value['id_buzon'];
        //         if ($value['id_buzon'] == $id) {
        //             $n_docs_por_recibir = $value['n_docs_por_recibir'];
        //             $n_docs_recibidos_pendientes = $value['n_docs_recibidos_pendientes'];
        //         }
        //     }
        // }


        //add check fea masiva
        $aplicaFrm = 0;
        $this->userFirma = Auth::user()->aplica_fea;
        if ($this->userFirma)
            $aplicaFrm = 1;
  
        /* NUEVO-DOCUMENTOS */

        //tipos de documentos
        $listado_tiposdoc = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(30)
            ->get(env('API_SGD_TIPO_DOCUMENTOS','http://sgd_ms_tipos_documentos:3333').'/api/sgd-tipodoc/ver_todos');

        if ($listado_tiposdoc->failed()) {
            $mensaje = $listado_tiposdoc->json()['data']['comentario'];

            toast($mensaje, 'error');
        } else {
            $datosTipoDoc = $listado_tiposdoc['data'];
        }


        //parametros
        $perfiles_datos = \App\Models\Perfil::all('id_perfil', 'nombre');
        $estados_usuario = \App\Models\EstadoUsuario::all('id_estado_usuario','nombre');
        $datosFlujoAccion = \App\Models\TipoFlujoAccion::all('id_tipo_flujo_accion', 'id_tipo_flujo', 'id_accion')->sortBy('id_accion');
        $datosNivelAcceso = \App\Models\NivelAcceso::all('id_nivel_acceso', 'nombre');
        $datosAccion = \App\Models\Accion::all('id_accion', 'id_tipo_accion','nombre')->toArray();
       

        //acciones
        $aAcciones = [];
        foreach ($datosAccion as $dato) {
            $aAcciones[$dato['id_accion']] = $dato['nombre'];

            if ($dato['id_tipo_accion'] == 1 && $dato['id_accion'] != 9) //requerida
            {
                $aFlujoAccionT1[$dato['id_accion']] = $dato['nombre'];
            }

            //elimina generar pdf de acciones
            $mykey = current($dato) - 1;

            if ($dato['id_accion'] == 8 || $dato['id_accion'] == 10)
                unset($datosAccion[$mykey]);
        }

        foreach ($datosFlujoAccion as $dato) {
            //if ($dato['id_accion'] != 9) //revisar caso
            //{
            if ($dato['id_tipo_flujo'] == 2) //Controlado
                $aFlujoAccionT2[] = array($dato['id_accion'], $aAcciones[$dato['id_accion']]);

            if ($dato['id_tipo_flujo'] == 3) //Mixto
                $aFlujoAccionT3[] = array($dato['id_accion'], $aAcciones[$dato['id_accion']]);
            //}
        }
        //$datosBuzon = $this->show($id); //muestra metodo show
        $datosBuzon = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json']) //
            ->withBody(json_encode([
                'id_buzon' => $id,
            ]), 'json')
            ->get(env('API_SGD_BUZONES','http://sgd_ms_buzones:3333').'/api/sgd-buzones/ver');



        //listado de buzones

        $listado_buzones = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(30)
            ->withBody(json_encode([
                'texto_busqueda' => '',
            ]), 'json')
            ->get(env('API_SGD_BUZONES','http://sgd_ms_buzones:3333').'/api/sgd-buzones/listar_todos');

        if ($listado_buzones->failed()) {
            $listado_buzones->json()['data']['comentario'];

            $listado_buzones = ['data' => [
                0 => ['id' => '0', 'nombre' => 'Sin Datos']
            ]];
            toast($mensaje, 'error');
        } else {
            foreach ($listado_buzones['data'] as $dato) {
                $aBuzones[$dato['id_buzon']] = $dato['nombre'];
                $aAllBuzones[] = array("value" => $dato['id_buzon'], "text" => $dato['nombre'], "tipo" => $dato['id_tipo_buzon']); // "tipo" => $dato['id_tipo_buzon']
                $aAllBuzones2[] = array("id" => $dato['id_buzon'], "text" => $dato['nombre'], "tipo" => $dato['id_tipo_buzon']);

                if ($dato['id_tipo_buzon'] == 2)
                    $aAllBuzonesT2[] = array("id" => $dato['id_buzon'], "text" => $dato['nombre']);
            }
        }
        $msjFirma = "";
        $aDocumentos = array();

        //listado documentos pendientes buzon, solo flujo libre
        $listado_pendientes = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(30)
            ->withBody(json_encode([
                'id_buzon' => $id,
            ]), 'json')
            ->get(env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333').'/api/sgd-documentos/verPendientesBuzon');

        if ($listado_pendientes->failed()) {
            $listado_pendientes->json()['data']['comentario'];
        } else {
            $doctoPendiente = array();
            foreach ($listado_pendientes['data'] as $dato) {
                $datosJsonTipoDocumento = json_decode($dato['json_tipo_documento'], true);
                if ($datosJsonTipoDocumento['id_tipo_flujo'] == 1) {
                    $aDocumentos[] = array("value" => $dato['id_documento'], "label" => $dato['identificador'], "title" => $dato['identificador'] . " - " . $dato['materia']);
                    $doctoPendiente[] = $dato['id_documento'];
                }
            }
        }


        // $log_firma = FirmaLog::whereIn('id_documento', $doctoPendiente)->get();
        // if($log_firma->count() > 0){
        //     $msjFirma = "Errores en la última firma:<br />";
        //     $nFilasError = 0;
        //     foreach ($log_firma as $lf) {
        //         $nFilasError++;
        //         $msjFirma  .= $lf->mensaje . "<br>";
        //     }
        // }

        /* NUEVO-DOCUMENTOS */
        return View::make('buzon.carpetas', [
            'perfiles' => $perfiles_datos,
            'estados_usuario' => $estados_usuario,
            'n_docs_por_recibir' => $n_docs_por_recibir,
            'n_docs_recibidos_pendientes' => $n_docs_recibidos_pendientes,
            'nivel_acceso' => $datosNivelAcceso,
            'nombre_buzon' => $buzon->nombre,
            'listado_tiposdoc' => $datosTipoDoc,
            'id_buzon' => $id,
            'listadoAcciones' => $datosAccion,
            'acciones_tipoflujo1' => $aFlujoAccionT1,
            'acciones_tipoflujo2' => $aFlujoAccionT2,
            'acciones_tipoflujo3' => $aFlujoAccionT3,
            'listadoBuzones' => $aBuzones,
            'allBuzones' => $aAllBuzones,
            'allBuzones2' => $aAllBuzones2,
            'allBuzonesT2' => $aAllBuzonesT2,
            'listDocPendientesBuzon' => $aDocumentos,
            'listado_parametros' => [
                "estado_documento"=>\App\Models\EstadoDocumento::All(),
                "estado_tramitacion"=>\App\Models\EstadoTramitacion::All()
            ],
            'aplicaFrm' => $aplicaFrm,
            'log_firma' => ''
        ]);
    }

    public function store_documento(Request $request)
    {
        $sesion_key =  AppServiceProvider::session_key_general();
        $accionDocumento = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(30)
            ->post(env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333').'/api/sgd-documentos/crear', [
                'id_tipo_documento' => $request->tipo_documento,
                'id_nivel_acceso' => $request->nivel_acceso,
                'efectos_terceros' => $request->efectos_terceros,
                'json_respuesta_a' => $request->responder,
                'materia' => $request->materia,
                'anterior' => $request->anterior,
                'descripcion' => $request->descripcion,
                'encabezado' => $request->encabezado,
                'cuerpo' => $request->cuerpo,
                'distribucion' => $request->distribucion,
                'id_buzon' => $request->buzon,
                'contestar_hasta' => $request->contestar_hasta,
                'id_usuario' => Auth::user()->id
            ])->throw();

        return $accionDocumento->json();
    }


    public function update_documento(Request $request)
    {
        $sesion_key =  AppServiceProvider::session_key_general();
        $accionDocumento = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(30)
            ->put(env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333').'/api/sgd-documentos/actualizar', [
                'id_tipo_documento' => $request->tipo_documento,
                'id_nivel_acceso' => $request->nivel_acceso,
                'id_documento' => $request->hiddIdDocumento,
                'id_documento_buzon' => $request->hiddIdDocumentoBuzon,
                'fileDelete' => $request->hiddIdFileDelete,
                'efectos_terceros' => $request->efectos_terceros,
                'json_respuesta_a' => $request->responder,
                'materia' => $request->materia,
                'anterior' => $request->anterior,
                'descripcion' => $request->descripcion,
                'encabezado' => $request->encabezado,
                'cuerpo' => $request->cuerpo,
                'distribucion' => $request->distribucion,
                'id_buzon' => $request->buzon,
                'contestar_hasta' => $request->contestar_hasta,
                'id_usuario' => Auth::user()->id,
                'destinatarioPrincipal' => $request->destinatarioPrincipal,
                'destinatarioPrincipal2' => $request->destinatarioPrincipal2,
                'destinatarioOtros' => $request->destinatarioOtros,
                'acciones_solicitadas' => $request->acciones_solicitadas,
                'comentarioPrincipal' => $request->comentarioPrincipal,
                'comentarioOtros' => $request->comentarioOtros,
                'carpeta' => $request->carpeta,
                'opcionGuardar' => $request->opcionGuardar,
                'aParaFirma' => $request->aParaFirma
            ]);

        return $accionDocumento->json();
    }

    public function delete_documento(Request $request)
    {
        $sesion_key = AppServiceProvider::session_key_general();
        $accionDocumento = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json']) //
            ->timeout(30)
            ->withBody(json_encode([
                'id_documento' => $request->idDocumento,
                'id_documento_buzon' => $request->idDocumentoBuzon
            ]), 'json')
            ->delete(env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333').'/api/sgd-documentos/eliminar');

        return $accionDocumento->json();
    }

    public function enviar_documento($id, Request $request)
    {
        $sesion_key = AppServiceProvider::session_key_general();
        $accionDocumento = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(30)
            ->put(env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333').'/api/sgd-documentos/enviar', [
                'id_documento' => $request->hiddIdDocumento,
                'id_documento_buzon' => $request->hiddIdDocumentoBuzon,
                'id_buzon' => $request->buzon,
                'id_usuario' => Auth::user()->id,
                'destinatarioPrincipal' => $request->destinatarioPrincipal,
                'acciones_solicitadas' => $request->acciones_solicitadas,
                'destinatarioOtros' => $request->destinatarioOtros,
                'json_respuesta_a' => $request->responder,
                'id_tipo_destino' => $request->id_tipo_destino,
                'carpeta' => $request->carpeta
            ])->throw();


        return $accionDocumento->json();
    }


    public function ver_documento($buzon,$id, Request $request)
    {
        $sesion_key =  AppServiceProvider::session_key_general();

        $datosDocumento = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json']) //
            ->timeout(30)
            ->withBody(json_encode([
                'id_documento' => $id,
                'id_documento_buzon' => $buzon
            ]), 'json')
            ->get(env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333').'/api/sgd-documentos/ver')->throw();

        return $datosDocumento->json();
    }

    public function actualizar_estado_documento($id, Request $request)
    {
        $sesion_key =  AppServiceProvider::session_key_general();

        if ($request->accion != 7) {
            $datosDocumento = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json']) //
                ->timeout(30)
                ->put(env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333').'/api/sgd-documentos/actualizar_estado', [
                    'id_documento_buzon' => $id,
                    'id_documento' => $request->hiddIdDocumento,
                    'id_buzon' => $request->buzon,
                    'archivo' => $request->archivo,
                    'accion' => $request->accion,
                    'id_usuario' => Auth::user()->id
                ]);

            return $datosDocumento->json();
        }
    }

    public function firma_masiva(Request $request)
    {
        $sesion_key =  AppServiceProvider::session_key_general();
        $documentobuzon  = $request->docBuzon;
        foreach ($request->firmas as $key=>$idDoc) {
            //$aValores = explode("-", $idDoc);
            // $derivarPrimera = 0;
            // $derivarUltima  = 0;
            // $datosDocumento = Documento::findOrFail($aValores[0]);
            // $datosDocumento->rel_tipo_documento;

            // $nroFirmas = $datosDocumento->rel_tipo_documento->numero_firmas;
            // $derivarPrimera = intval($datosDocumento->rel_tipo_documento->derivar_primera_firma);
            // $derivarUltima = intval($datosDocumento->rel_tipo_documento->derivar_ultima_firma);
            // $buzonPrimera = intval($datosDocumento->rel_tipo_documento->buzon_primera_firma);
            // $buzonUltima = intval($datosDocumento->rel_tipo_documento->buzon_ultima_firma);

            // $firmasRealizadas = DocumentoBuzonBitacora::where('id_documento_buzon',$aValores[1])->where('id_accion',7)->count();

            // if(($derivarPrimera == 1 && $firmasRealizadas == 0)){
            //     FirmarDerivar::dispatch($request->buzon, $aValores[0], $aValores[1],$buzonPrimera, $sesion_key, Auth::user()->id);     
            // }
            // else {
            //     if($derivarUltima == 1 && $firmasRealizadas == ($nroFirmas - 1)) {
            //         FirmarDerivar::dispatch($request->buzon, $aValores[0], $aValores[1], $buzonUltima, $sesion_key, Auth::user()->id);        
            //     }
            //     else{
            //         Firma::dispatch($request->buzon, $aValores[0], $aValores[1], $sesion_key, Auth::user()->id);        
            //     }
            // }
            DocumentoBuzon::find($documentobuzon[$key])->update(['id_estado_documento' => 8]);
            Firma::dispatch($request->buzon, $idDoc, $documentobuzon[$key], $sesion_key, Auth::user()->id);
            
        }

        return $this->respondSuccess("Documentos enviados a firma.", 200);
    }

    public function visar_documento($buzon,$id)
    {
        $sesion_key =  AppServiceProvider::session_key_general();

        $datosDocumento = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json']) //
        ->timeout(30)
        ->put(env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333').'/api/sgd-documentos/actualizar_estado', [
            'id_documento_buzon' => $buzon,
            'id_documento' => $id,
            'accion' => 6,
            'id_usuario' => Auth::user()->id
        ])->throw();

        return $datosDocumento->json();
    }

    public function firmar_documento($id, Request $request)
    {
        $sesion_key =  AppServiceProvider::session_key_general();


        $datosFea = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(60)
            ->put(env('API_SGD_FIRMA','http://sgd_ms_firma:3333').'/api/sgd-firma/firmar_archivo', [
                'id_documento_buzon' => $id,
                'id_documento' => $request->hiddIdDocumento,
                'id_usuario' => Auth::user()->id,
                'id_buzon' => $request->buzon
            ]);
        if($datosFea->failed()){
            return response()->json([
                'status' => 'error',
                'comentario' => $datosFea->json()['data']['comentario']
            ], 500);
        }
        return $datosFea->json();
    }

    public function archivar_documento($buzon, $id,Request $request)
    {
        $sesion_key =  AppServiceProvider::session_key_general();

        $datosDocumento = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json']) //
            ->timeout(30)
            ->put(env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333').'/api/sgd-documentos/archivar', [
                'id_documento_buzon' => $buzon,
                'id_documento' => $id,
                'id_buzon' => $request->buzon,
                'id_usuario' => Auth::user()->id,
                'comentario' => $request->comentario,
                'accion' => $request->accion
            ])->throw();
        
        //dd(json_decode($datosDocumento->getBody()));
        return json_decode($datosDocumento->getBody());
    }

    public function generar_archivo_pdf(Request $request)
    {
        $sesion_key = AppServiceProvider::session_key_general();
        try {
            $datosArchivo = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json']) //
                ->timeout(30)
                ->put(env('API_SGD_ARCHIVOS','http://sgd_ms_archivos:3333').'/api/sgd-archivos/generar_archivo_pdf', [
                    'id_documento' => $request->idDocumento,
                    'id_documento_buzon' => $request->idDocumentoBuzon,
                    'id_usuario' => Auth::user()->id,
                    'id_buzon' => $request->idBuzon
                ])->throw();

            dd($datosArchivo->status());

            return $datosArchivo->json();
        } catch (\Exception $e) {
            return response()->json(["msg" => $e->getMessage()], 500);
        }
        //dd($datosArchivo);

    }

    public function generar_vista_previa(Request $request)
    {
        $sesion_key = AppServiceProvider::session_key_general();

        $datosArchivo = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json']) //
            ->timeout(30)
            ->withBody(json_encode([
                'id_documento' => $request->idDocumento,
                'id_documento_buzon' => $request->idDocumentoBuzon,
                'id_usuario' => Auth::user()->id,
                'id_buzon' => $request->idBuzon
            ]), 'json')
            ->get(env('API_SGD_ARCHIVOS','http://sgd_ms_archivos:3333').'/api/sgd-archivos/generar_vista_previa');



        return $datosArchivo;
    }

    
    public function vp_sg($nID)
    {

        $datosDocumentos = DocumentoTmp::findOrFail($nID);

        //ORIGINAL
        $datosJsonTipoDocumento = json_decode($datosDocumentos['json_tipo_documento'], true);

        if ($datosJsonTipoDocumento['requiere_fe']) {
            if (isset($datosJsonTipoDocumento['numero_firmas']))
                $nNroFirmas = $datosJsonTipoDocumento['numero_firmas'];
            else
                $nNroFirmas = 4;

            //agregar espacio para firmas al contenido del documento
            $aFirmaPosicion = array(
                '1' => 85,  //165, 
                '2' => 85,  //165, 
                '3' => 185, //265,
                '4' => 185, //265,
                '5' => 285, //365, 
                '6' => 285, //365
            );

            $nAltoFirmas = $aFirmaPosicion[$nNroFirmas] + 10;
        } else {
            $nAltoFirmas = 0;
        }




        $tPlantillaDistribucion = "";
        if (isset($datosDocumentos['distribucion']))
            $tPlantillaDistribucion = $datosDocumentos['distribucion'];

        $numLineasDistribucion = substr_count($tPlantillaDistribucion, "\n");
        $nEspacioDistribucion = $numLineasDistribucion * 20;

        $altoTotal = $nEspacioDistribucion + $nAltoFirmas;

        $aMeses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $sfecha = date('d') . " de " . $aMeses[date('n') - 1] . " del " . date('Y');
        $sEncabezado = $datosDocumentos['encabezado'];
        $sEncabezado = str_replace('{t_anio}', date('Y'), $sEncabezado);
        $sEncabezado = str_replace('{t_fecha}', $sfecha, $sEncabezado);

        $sCuerpo = $datosDocumentos['cuerpo'];
        $sCuerpo = str_replace('{t_anio}', date('Y'), $sCuerpo);
        $sCuerpo = str_replace('{t_fecha}', $sfecha, $sCuerpo);

        $datosDocumentosCuerpo = str_replace(env('APP_URL'), storage_path('app/public'), $sCuerpo);
        $datosDocumentosencabezado = str_replace(env('APP_URL'), storage_path('app/public'), $sEncabezado);
        $datosDocumentosDistribucion = str_replace(env('APP_URL'), storage_path('app/public'), $tPlantillaDistribucion);

        $dataPdf = array('materia' => $datosDocumentos['materia'], 'encabezado' => $datosDocumentosencabezado, 'cuerpo' => $datosDocumentosCuerpo, 'distribucion' => $datosDocumentosDistribucion, 'altoFirmas' => $nAltoFirmas, 'altoTotal' => $altoTotal);

        //Se elimina registro temporal
        $registro = DocumentoTmp::find($nID);
        $registro->delete();
        set_time_limit(300);

        $pdf = PDF::loadView('pdf', $dataPdf)->setPaper('legal', 'portrait');
        return $pdf->stream('vista_previa.pdf');
        //return view('pdf', ['materia'=>$datosDocumentos['materia'], 'encabezado'=>$datosDocumentosencabezado, 'cuerpo'=>$datosDocumentosCuerpo, 'distribucion'=>$datosDocumentosDistribucion, 'altoFirmas'=>$nAltoFirmas]);
    }

    public function generar_vista_previa_sg(Request $request)
    {
        try {
            //se crea documento con los datos básicos para la vista previa
            $sesion_key = AppServiceProvider::session_key_general();
            $datosDocumentosCuerpo = str_replace(env('APP_URL'), storage_path('app/public'), $request->cuerpo);
            $datosDocumentosencabezado = str_replace(env('APP_URL'), storage_path('app/public'), $request->encabezado);
            $datosDocumentosDistribucion = str_replace(env('APP_URL'), storage_path('app/public'), $request->distribucion);
            $msVerTipoDoc = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json']) //
                ->timeout(30)
                ->withBody(json_encode([
                    'id_tipo_documento' => $request->tipo_documento,
                ]), 'json')
                ->get(env('API_SGD_TIPO_DOCUMENTOS','http://sgd_ms_tipos_documentos:3333').'/api/sgd-tipodoc/ver');

            $documento = new DocumentoTmp();
            $documento->materia = $request->materia;
            $documento->encabezado = $datosDocumentosencabezado; //$request->encabezado;
            $documento->cuerpo = $datosDocumentosCuerpo; //$request->cuerpo;
            $documento->distribucion = $datosDocumentosDistribucion; //$request->cuerpo;
            $documento->json_tipo_documento = json_encode($msVerTipoDoc['data']);
            $documento->save();

            return $documento;
        } catch (ModelNotFoundException $e) {

            return response()->json([
                'status' => 500,
                'data' => [
                    'comentario' => 'Error al generar vista previa.'
                ]
            ], 500);
        }
    }
    public function derivarOpcion1(Request $request)
    {
        $sesion_key = AppServiceProvider::session_key_general();

        $accionDocumento = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
            ->timeout(30)
            ->put(env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333').'/api/sgd-documentos/derivar', [
                'carpeta' => 1,
                'id_documento' => $request->hiddIdDocumento,
                'id_buzon' => $request->buzon,
                'id_usuario' => Auth::user()->id,
                'destinatarioPrincipal' => $request->destinatarioPrincipal,
                'destinatarioOtros' => $request->destinatarioOtros,
                'comentarioPrincipal' => $request->comentarioPrincipal,
                'comentarioOtros' => $request->comentarioOtros
            ])->throw();

        return $accionDocumento->json();
    }
    public function devolver(Request $request)
    {
        
        $acciones = [];
        foreach ($request->acciones as $accion) {
            $acciones[] = ['id_accion' => (int)$accion];
        }
        //$sesion_key = AppServiceProvider::session_key_general();
        // $accionDocumento = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json'])
        //     ->timeout(30)
        //     ->put(env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333').'/api/sgd-documentos/devolver', [
        //         'carpeta' => 1,
        //         //'id_documento_buzon' => $request->hiddIdDocumentoBuzon,
        //         //'id_buzon' => $request->buzon,
        //         'id_usuario' => Auth::user()->id,
        //         'acciones' => json_encode($acciones),
        //         'destinatarioPrincipal' => $request->destinatarioPrincipal,
        //         'destinatarioOtros' => $request->destinatarioOtros,
        //         'comentarioPrincipal' => $request->comentarioPrincipal,
        //         'comentarioOtros' => $request->comentarioOtros
        //     ])->throw();

        // $datosRequest = $request->json()->all();
        try {
            DB::beginTransaction();
            //tendria que comprobar que sea de carpeta 1
            if (1 == 1) //por recibir, para devolver
            {
                
                //se copia el envia a enviado
                $datosUpdate = DocumentoBuzon::find($request->hiddIdDocumentoBuzon);
                $datosUpdate->id_estado_documento = 2;
                $datosUpdate->id_carpeta = 3;
                $datosUpdate->save();
                //crear envio a buzon anterior
                $datosDocumentoBuzon = DocumentoBuzon::create([
                    'id_documento' => $datosUpdate->id_documento,
                    'id_buzon' => $request->destinatarioPrincipal,
                    'id_carpeta' => 1,
                    'id_estado_documento' => 3,// estado por recibir
                    'id_tipo_destino' => 1,
                    'id_documento_buzon_padre' => $request->hiddIdDocumentoBuzon,
                    'json_acciones' => json_encode($acciones),
                    'comentario_principal' => $request->comentarioPrincipal,
                    'fecha' => date('Y-m-d H:i:s'),
                    //'contestar_hasta' => $datosRequest['contestar_hasta'],
                    'notificado' => false,
                    'recibido' => false,
                    'favorito' => false
                ]);
                //bitácora (crea envio desde este buzon hacia el buzon de devolución)
                DocumentoBuzonBitacora::create([
                    'id_documento_buzon' => $datosDocumentoBuzon->id_documento_buzon,
                    //'id_buzon' => $request->destinatarioPrincipal,
                    'id_accion' => 2,
                    'fecha' => date('Y-m-d H:i:s'),
                    'id_usuario' => Auth::user()->id,
                    'comentario' => "Devolución: " . $request->comentarioPrincipal,
                    'informacion_solicitud' => ["buzon_destino" => $request->destinatarioPrincipal, "id_tipo_destino"=>1],
                    
                ]);
                DB::commit();
                return $this->respondSuccess("Documento enviado", 200);
            }
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return $this->respondError('Falla al enviar documento:' . $e->getMessage(), 500);
        }        

        //return $accionDocumento->json();
    }

    public function listar(Request $request)
    {
        $year_actual = session('year');
        $nCarpeta = $request->id_carpeta;
       
        $texto = $request->texto;
        $extraquery = "";
        if ($texto) {
            $extraquery = " (lower(documento.materia) like '%" . strtolower($texto) . "%'";
            if ((int)$texto > 0) {
                $extraquery = $extraquery . " OR documento.id_documento=" . (int)$texto . " OR documento.folio = " . (int)$texto . "";
            }
            $extraquery = $extraquery . ")";
        }

        $datos =  DB::table('documento_buzon')
            ->join('documento', 'documento_buzon.id_documento', '=', 'documento.id_documento')
            ->join('estado_documento', 'documento_buzon.id_estado_documento', '=', 'estado_documento.id_estado_documento')
            ->join('tipo_documento', 'documento.id_tipo_documento', '=', 'tipo_documento.id_tipo_documento')
            ->join('tipo_origen', 'tipo_documento.id_tipo_origen', '=', 'tipo_origen.id_tipo_origen')
            ->join('tipo_destino', 'documento_buzon.id_tipo_destino', '=', 'tipo_destino.id_tipo_destino')
            ->join('estado_tramitacion', 'documento.estado_tramitacion', '=', 'estado_tramitacion.id')
             ->leftJoin(DB::raw('documento_buzon as origen'), function ($join)  {
                 $join->on("origen.id_documento_buzon","=", "documento_buzon.id_documento_buzon_padre");
             })
            ->leftJoinSub('select * from documento_buzon where id_tipo_destino = 1', 'destinatario', function ($join) {
                $join->on("destinatario.id_documento_buzon_padre","=", "documento_buzon.id_documento_buzon");
            })
            ->select(
                'documento_buzon.id_documento_buzon as id_documento_buzon',
                'documento_buzon.id_estado_documento as id_estado_documento',
                'documento_buzon.id_buzon as id_buzon',
                'documento.id_documento as id_documento',
                'documento.folio as folio',
                'estado_tramitacion.nombre as etapa_tramitacion',
                'documento_buzon.id_documento_buzon_padre as id_documento_buzon_padre',
                'documento.identificador as identificador',
                'documento_buzon.recibido as recibido',
                'estado_documento.nombre_corto as estado_documento',
                'estado_documento.codigo_color as codigo_estado',
                'documento.created_at as fecha_creacion', //carpeta 3
                'documento_buzon.fecha as fecha_envio_recepcion',
                'documento_buzon.fecha as fecha_envio', //carpeta 3 y 1
                'tipo_documento.nombre as tipo_documento',
                'tipo_documento.id_tipo_documento as id_tipo_documento',
                'documento_buzon.json_acciones as json_acciones',
                'documento.materia as materia',
                'documento.json_respuesta_a as respuesta_a',
                'documento.json_tipo_documento as json_tipo_documento',
                'tipo_destino.nombre as tipo_envio',
                'tipo_destino.id_tipo_destino as id_tipo_destino',
                'origen.id_buzon as buzon_origen',
                'destinatario.id_buzon as destinatario',
                'documento_buzon.fecha as fecha_recepcion',
                'documento_buzon.contestar_hasta as contestas_hasta',
                DB::raw('case when ' . $request->id_carpeta . ' = 3 then case when (select count(1) from  documento_buzon db  	
                        where db.id_documento = documento_buzon.id_documento  	
                        and db.id_documento_buzon_padre  = documento_buzon.id_documento_buzon  	
                        and db.id_estado_documento >=4) > 0 then 0 else 1 end  else 0 end as eliminar')
            )
            ->where('documento_buzon.id_buzon', '=', $request->id_buzon)
            ->where('documento_buzon.id_carpeta', '=', $request->id_carpeta)
            ->where('documento.anio_tramitacion', $year_actual)
            ->when($extraquery, function ($query, $extraquery) {
                return $query->whereRaw($extraquery);
            });
 
        //filtrar por estados
        if ($request->estados != null)
            $datos->whereIn('documento_buzon.id_estado_documento', explode('|', $request->estados));

        //filtrar por tipodoc
         if ($request->tipodocs != null)
         $datos->whereIn('documento.id_tipo_documento', explode('|', $request->tipodocs));

        //filtrar por recibir (forzar a que sea solo los por recibir)(si no se deja este filtro, cada vez que se guarde un destinatario aparece en la bandeja del destinatario sin enviar)
        if($nCarpeta == 1){
            $datos->where('documento_buzon.id_estado_documento','<>',1);
        }


        // echo $datos->toSql();
        // die;

        return datatables($datos)->toJson();
    }

    public function clonar(Request $request)
    {
        $nIDDocumento = $request->idDocumento;
        $nDocumentoBuzon = $request->idDocumentoBuzon;
        $nDocumentoBuzonPadre = $request->idDocumentoBuzonPadre;

        $sesion_key =  AppServiceProvider::session_key_general();

        try {
            DB::beginTransaction();

            $DocumentoOriginal = Documento::where('id_documento', $nIDDocumento)->get();

            $DocumentoBuzonOriginal = DocumentoBuzon::where('id_documento_buzon', $nDocumentoBuzon)->get();
            //dd($DocumentoOriginal[0]);
            $nTipoDoc = $DocumentoOriginal[0]->id_tipo_documento;

            $msVerTipoDoc = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json']) //
                ->timeout(30)
                ->withBody(json_encode([
                    'id_tipo_documento' => $nTipoDoc,
                ]), 'json')
                ->get(env('API_SGD_TIPO_DOCUMENTOS','http://sgd_ms_tipos_documentos:3333').'/api/sgd-tipodoc/ver');

            $nFolio = null;
            $anio = date('Y');


            $idTipoFolio = $msVerTipoDoc['data']['id_tipo_folio'];
            /** CODIGO PARA OBTENER FOLIO CUANDO TIPO ASIGNACIÓN ES EN LA CREACIÓN  **/
            if ($msVerTipoDoc['data']['id_tipo_asignacion_folio'] == 1) //creación
            {
                $nFolio = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json']) //
                    ->timeout(30)
                    ->withBody(json_encode([
                        'id_tipo_documento' => $nTipoDoc,
                        'anio' => $anio,
                        'id_buzon' => null,
                        'id_tipo_folio' =>  $idTipoFolio,
                    ]), 'json')
                    ->get(env('API_SGD_FOLIOS','http://sgd_ms_folios:3333').'/api/sgd-folios/asignaFolio');
            }

            /* IMPORTANTE::REVISAR QUE PASARÁ CON EL FOLIO SI NO SE LLEGA A CREAR EL DOCUMENTO POR ALGUN ERROR */

            $dFechaCreacion = date('Y-m-d H:i:s');
            $dFechaHash = date('Y-m-d H:i:s');

            $jsonTipoDocumento = $msVerTipoDoc->json();

            //hash validación
            $sparamHash = $dFechaHash . $msVerTipoDoc['data']['nombre_corto'] . $DocumentoOriginal[0]->materia;
            $sHash = hash('sha256', $sparamHash, false);


            $jsonRespuesta = array();
            if ($msVerTipoDoc['data']['id_tipo_flujo'] == 1) {
                if ($DocumentoOriginal[0]->son_respuesta_a != "" && $DocumentoOriginal[0]->json_respuesta_a != null) {
                    foreach ($DocumentoOriginal[0]->json_respuesta_a as $resp) {
                        $datosRespuesta = Documento::where('id_documento', '=', $resp)->select('id_documento', 'identificador', 'materia', 'created_at')->first();
                        $jsonRespuesta[] = $datosRespuesta;
                    }
                }
            }

            $documento = Documento::create([
                'id_tipo_documento' => $DocumentoOriginal[0]->id_tipo_documento,
                'id_nivel_acceso' => $DocumentoOriginal[0]->id_nivel_acceso,
                'efectos_terceros' => $DocumentoOriginal[0]->efectos_terceros,
                'json_tipo_documento' => json_encode($msVerTipoDoc['data']), //obtener de ms_tipos_documentos
                'json_respuesta_a' => json_encode($jsonRespuesta),
                'materia' => "(Copia) " . $DocumentoOriginal[0]->materia,
                'anterior' => $DocumentoOriginal[0]->anterior,
                'descripcion' => $DocumentoOriginal[0]->descripcion,
                'encabezado' => $DocumentoOriginal[0]->encabezado,
                'cuerpo' => $DocumentoOriginal[0]->cuerpo,
                'distribucion' => $DocumentoOriginal[0]->distribucion,
                'fecha' => $dFechaCreacion,
                'hash_validacion' => $sHash,
                'folio' => $nFolio,
                'anio_tramitacion' =>date('Y'),
                'estado_tramitacion' => 1,
            ]);

            $fContestarHasta =  $DocumentoBuzonOriginal[0]->contestar_hasta;
            if ($fContestarHasta == "") {
                $fContestarHasta = "null";
                db::statement("insert into documento_buzon (id_documento,id_buzon,id_carpeta,id_estado_documento,id_tipo_destino,id_documento_buzon_padre,fecha,contestar_hasta,notificado,recibido,favorito) values (" . $documento->id_documento . "," . $DocumentoBuzonOriginal[0]->id_buzon . ",3,1,1,null,'" . $dFechaCreacion . "'," . $fContestarHasta . ",false,false,false)");
            } else {
                db::statement("insert into documento_buzon (id_documento,id_buzon,id_carpeta,id_estado_documento,id_tipo_destino,id_documento_buzon_padre,fecha,contestar_hasta,notificado,recibido,favorito) values (" . $documento->id_documento . "," . $DocumentoBuzonOriginal[0]->id_buzon . ",3,1,1,null,'" . $dFechaCreacion . "','" . $fContestarHasta . "',false,false,false)");
            }
            //dd($fContestarHasta);
            $documento = $documento->fresh();
            DB::enableQueryLog();
            //db::statement("insert into documento_buzon (id_documento,id_buzon,id_carpeta,id_estado_documento,id_tipo_destino,id_documento_buzon_padre,fecha,contestar_hasta,notificado,recibido,favorito) values (".$documento->id_documento.",".$DocumentoBuzonOriginal[0]->id_buzon.",3,1,1,null,'". $dFechaCreacion."',".$fContestarHasta.",false,false,false)");
            //dd(DB::getQueryLog());
            $idDocumentoBuzon = DB::getPdo()->lastInsertId();

            $documentoBuzonBitacora = DocumentoBuzonBitacora::create([
                'id_documento_buzon' => $idDocumentoBuzon,
                'id_accion' => 1,
                'fecha' => $dFechaCreacion,
                'id_usuario' => Auth::user()->id
            ]);



            if ($nFolio != null) {
                //registrar accion de asignacion de folio en bitacora
                $documentoBuzonBitacoraFolio = DocumentoBuzonBitacora::create([
                    'id_documento_buzon' => $idDocumentoBuzon,
                    'id_accion' => 9,
                    'fecha' => $dFechaCreacion,
                    'id_usuario' => Auth::user()->id
                ]);
            }

            $documento->rel_documento_buzon;
            DB::commit();

            return $this->respondSuccess($documento, 200);
        } catch (ModelNotFoundException $e) {

            DB::rollBack();
            //return $e->getMessage();
            return $this->respondError('Falla al crear documento:' . $e->getMessage(), 500);
        }
    }

    public function buscar_documento_buzon(Request $request)
    {
        $year_actual = session('year');
        //dd($request);
        $extraquery = "";
        // //construir filtro
        $query = $request->texto;
        if ($query) {
            $extraquery = " (lower(documento.materia) like '%" . strtolower($query) . "%'";
            if ((int)$query > 0) {
                $extraquery = $extraquery . " OR documento.id_documento=" . (int)$query . " OR documento.folio = " . (int)$query . "";
            }
            $extraquery = $extraquery . ")";
            //$extraquery=$extraquery." and documento_buzon.id_buzon = ".$request->buzon; 
        } else {
            $extraquery = " 1 = 2 ";
        }
        DB::enableQueryLog();
        $datos =  DB::table('documento_buzon')
            ->join('documento', 'documento_buzon.id_documento', '=', 'documento.id_documento')
            ->join('estado_documento', 'documento_buzon.id_estado_documento', '=', 'estado_documento.id_estado_documento')
            ->join('tipo_documento', 'documento.id_tipo_documento', '=', 'tipo_documento.id_tipo_documento')
            ->join('tipo_origen', 'tipo_documento.id_tipo_origen', '=', 'tipo_origen.id_tipo_origen')
            ->join('tipo_destino', 'documento_buzon.id_tipo_destino', '=', 'tipo_destino.id_tipo_destino')
            ->select(
                DB::raw("case documento_buzon.id_carpeta when 1 then 'Por Recibir' when 2 then 'Recibidos' else 'Despachados' end as salida")
            )
            ->where('documento_buzon.id_buzon', '=', $request->buzon)
            ->where('documento.anio_tramitacion', $year_actual)

            ->whereRaw('case documento_buzon.id_carpeta 
            when 1 then documento_buzon.id_estado_documento in (3)
            when 2 then documento_buzon.id_estado_documento in (4,5,6,8,9,11,13)
            when 3 then documento_buzon.id_estado_documento in (1,2,7,10,12)
            end')
            ->whereRaw($extraquery)
            ->groupBy('documento_buzon.id_carpeta')
            ->get();

        if ($datos->count() == 0) {
            return $this->respondSuccess(["mensaje"=>"No existen coincidencias para el criterio ingresado."], 201);
        } else {
            return $this->respondSuccess(["mensaje"=>"Coincidencia/s en carpeta/s:","carpetas"=>$datos->pluck('salida')], 200);
        }
    }

    public function eliminar_documento_enviado(Request $request)
    {
        try {
            DB::beginTransaction();


            $datoDocBuzon = DocumentoBuzon::where('id_documento_buzon', $request->idDocumentoBuzon)
                ->where('id_estado_documento', '2')
                ->first();

            if ($datoDocBuzon['id_documento_buzon']) {

                //elimina de las tablas relacionadas 	
                DB::enableQueryLog();
                $datosDocumento = Documento::findOrFail($request->idDocumento);

                if (!$datosDocumento['id_documento']) {

                    return $this->respondError('Falla al eliminar documento: Documento no encontrado', 500);
                }

                DB::delete('delete from documento_buzon_bitacora where id_documento_buzon in (select id_documento_buzon  from documento_buzon db where id_documento =' . $request->idDocumento . ' and db.id_documento_buzon_padre is not null)');
                //DocumentoBuzonBitacora::where('id_documento_buzon',$request->idDocumentoBuzon)->delete(); 	

                // $datosDocumento->rel_documento_buzon()->delete(); 	
                DocumentoBuzon::where('id_documento', $request->idDocumento)
                    ->whereRaw('id_documento_buzon_padre is not null')
                    ->delete();

                //$datosDocumento->delete();  	
                DB::statement('update documento_buzon set id_estado_documento = 1 where id_documento =' . $request->idDocumento);

                //dd(DB::getQueryLog()); 	

                DB::commit();

                return $this->respondSuccess("Documento eliminado", 200);
            } else
                return $this->respondError('Falla al eliminar documento:  Documento-Buzon no encontrado', 500);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();

            return $this->respondError('Falla al eliminar documento:' . $e->getMessage(), 500);
        }
    }

    public function firmar_derivar($sesionKey, $nombreBuzon, $nombreCorto, $usuarios, $IDDocumento, $IDDocBuzon, $IDBuzon, $IDUsuario, $buzonDestino, $acciones, $jsonRespuesta, $cargo, $restringir, $IDUsuarioSub)
    {

        $accionDocumento = Http::withHeaders(['key' => $sesionKey, 'Content-Type' => 'application/json'])
            ->timeout(30)
            ->put(env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333').'/api/sgd-documentos/firmar_derivar', [
                'nombre_buzon' => $nombreBuzon,
                'nombre_corto_buzon' => $nombreCorto,
                'tipo_buzon' => '2',
                'usuarios_asignados' => $usuarios,
                'id_documento' => $IDDocumento,
                'id_documento_buzon' => $$IDDocBuzon,
                'id_buzon' => $IDBuzon,
                'id_usuario' => $IDUsuario,
                'destinatarioPrincipal' => $buzonDestino,
                'acciones_solicitadas' => $acciones,
                'destinatarioOtros' => null,
                'json_respuesta_a' => $jsonRespuesta,
                'id_tipo_destino' => 1,
                'carpeta' => 2,
                'titular' => null,
                'cargo_firma' => $cargo,
                'restringir_sr' => $restringir,
                'id_usuario_sr' => $IDUsuarioSub
            ]);
    }

    public function editor()
    {
        return View::make('buzon.editor', []);
    }


    public function getContadores(){
        $contadores = DocumentoBuzon::Contadores();
        $res = [];
        foreach ($contadores as $value) {
            if(!isset($res[$value->id_buzon])){
                $res[$value->id_buzon] = new \stdClass();
                $res[$value->id_buzon]->nombre = $value->nombre;
                $res[$value->id_buzon]->por_recibir = 0;
                $res[$value->id_buzon]->pendientes = 0;
                $res[$value->id_buzon]->total = 0;
            }
            switch($value->id_carpeta){
                case 1:
                    $res[$value->id_buzon]->por_recibir = $value->total;
                    $res[$value->id_buzon]->total += $value->total;
                    break;
                case 2:
                    $res[$value->id_buzon]->pendientes = $value->total;
                    $res[$value->id_buzon]->total += $value->total;
                    break;
            }
        }
        return response()->json($res);
    }
}
