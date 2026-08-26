<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use App\Models\Documento;
use App\Models\Users;
use App\Models\DocumentoBuzon;
use App\Models\DocumentoBuzonArchivo;
use App\Models\DocumentoBuzonBitacora;
use Illuminate\Support\Facades\DB;
use App\Validator\DocumentoValidator;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use App\Models\BloqueoFolio;
use Barryvdh\DomPDF\Options;
use App\Models\TipoDocumentoBuzonFolio;
use Illuminate\Support\Facades\Log;


class ArchivoController extends Controller
{


    private $validator;

    public function __construct(DocumentoValidator $documentoValidator)
    {
        $this->validator = $documentoValidator;
    }

    public function generar_archivo_pdf(Request $request)
    {
        //sin limite en caso de documentos grandes
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        try {
            DB::beginTransaction();

            $datosRequest = $request->json()->all();

            $nDocumento = $datosRequest['id_documento'];
            $idDocumentoBuzon = $datosRequest['id_documento_buzon'];
            
            $nNombreArchivoCargar = $this->getNombreDocumento($nDocumento);

            $aInfoUsuarios = Users::where('id', $datosRequest['id_usuario'])->first(['genera_pdf']);

            if (!$aInfoUsuarios['genera_pdf'] && empty($datosRequest['forzar']) && empty($datosRequest['regenerar'])) {
                return $this->respondError('Usuario no tiene permiso para generar pdf.', 400);
            }

            $regenerar = !empty($datosRequest['regenerar']);

            //verifica que no se genere 2 veces
            //busca en bitacora
            $existePdfGenerado = DocumentoBuzonBitacora::where('id_accion', 8)
                ->where('id_documento_buzon', $idDocumentoBuzon)
                ->first();

            //busca en archivo_existente
            $existeDocPpal = DocumentoBuzonArchivo::where('id_documento_buzon', $idDocumentoBuzon)
                ->where('id_tipo_archivo', 1)
                ->where('version', 1)
                ->first();

            if (!$regenerar && isset($existePdfGenerado['id_documento_buzon']) && isset($existeDocPpal['id_documento_buzon'])) {
                if (!empty($datosRequest['forzar'])) {
                    return $this->respondSuccess([
                        'comentario' => 'El archivo PDF ya fue generado.',
                        'folio' => Documento::find($nDocumento)->folio ?? null,
                        'anio' => date('Y'),
                        'tipoDoc' => '',
                    ], 200);
                }
                return $this->respondError('El archivo PDF ya fue generado.', 400);
            }

            if ($regenerar) {
                // Igual que un oficio regenerado: archiva el PDF anterior y permite uno nuevo con visadores.
                $docsPpales = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon_archivo.id_documento_buzon', '=', 'documento_buzon.id_documento_buzon')
                    ->where('documento_buzon.id_documento', $nDocumento)
                    ->where('documento_buzon_archivo.id_tipo_archivo', 1)
                    ->where('documento_buzon_archivo.version', 1)
                    ->select('documento_buzon_archivo.*')
                    ->get();
                foreach ($docsPpales as $archFile) {
                    DocumentoBuzonArchivo::find($archFile->id_documento_buzon_archivo)->update([
                        'version' => (int) $archFile->version + 1,
                    ]);
                }
                Documento::find($nDocumento)->update(['archivo_existente' => false, 'paginas_archivo' => null]);
            }

            $datosDocumentos = Documento::findOrFail($nDocumento);

            //OBTENCION DE FOLIO
            $idTipoDocumento = $datosDocumentos->id_tipo_documento;
            $datosJsonTipoDocumento = json_decode($datosDocumentos['json_tipo_documento'], true);
            $idTipoAsigFolio = $datosJsonTipoDocumento['id_tipo_asignacion_folio'];
            $idTipoFolio = $datosJsonTipoDocumento['id_tipo_folio'];
            $idTipoFlujo = $datosJsonTipoDocumento['id_tipo_flujo'];
            $nFolio = $datosDocumentos['folio'];
            $tPlantillaDistribucion = "";

            $nNroFirmas = (int) ($datosJsonTipoDocumento['numero_firmas'] ?? 4);
            if ($nNroFirmas < 1 || $nNroFirmas > 6) {
                $nNroFirmas = 4;
            }

            if (isset($datosDocumentos['distribucion']))
                $tPlantillaDistribucion = $datosDocumentos['distribucion'];

            //agregar espacio para firmas al contenido del documento

            // Debe alcanzar la fila superior de sellos (ver grilla en FirmaController).
            $aFirmaPosicion = array(
                1 => 175,
                2 => 175,
                3 => 320,
                4 => 320,
                5 => 470,
                6 => 470,
            );

            $nAltoFirmas = $aFirmaPosicion[$nNroFirmas] ?? 320;
            $datosRequest['generaFolio'] = $request->json()->get('generaFolio',0);
            //si existe folio, saltar proceso de obtención de folio
            $fecha = new \DateTime('now');
            if ($nFolio == null && $datosRequest['generaFolio'] == 1) {
                //if ($idTipoAsigFolio == 2 && $idTipoFlujo == 1) //se aplica a flujo libre y tipo asig en recepción
                if (($idTipoAsigFolio == 2 || $idTipoAsigFolio == 4 || $idTipoAsigFolio == 5) && $idTipoFlujo == 1) //se aplica a flujo libre y tipo asig en recepción | primera firma 
                {
                    $anio = date('Y');
                    $fecha = new \DateTime('now');

                    //nueva forma de obtener folio 
                    $datosBloqueo = DB::table('bloqueo_folio')
                        ->where('tipo_folio', $idTipoFolio)
                        ->where('tipo_documento', $idTipoDocumento)
                        ->where('buzon', $datosRequest['id_buzon'])
                        ->where('estado', 1)
                        ->get();

                    $existeBloqueo = count($datosBloqueo);

                    if ($existeBloqueo > 0) {
                        return $this->respondError('No se puede obtener el folio, favor intente en unos minutos.', 400);
                    }

                    $nFolio = $this->obtenerFolio($request->header('key'), $anio, $idTipoDocumento, $idTipoFolio, $datosRequest['id_buzon']);
                    //fin nueva forma de obtener folio 

                    if (isset($nFolio)) {
                        Documento::find($datosRequest["id_documento"])->update(['folio' => $nFolio]);
                        Documento::find($datosRequest["id_documento"])->update(['fecha' => $fecha->format('Y-m-d H:i:s')]);
                    } else {
                        return $this->respondError('No fue posible generar el folio.', 400);
                    }
                } else $fecha = date_create_from_format('Y-m-d H:i:s', $datosDocumentos['fecha']);
            } else {
                $fecha = date_create_from_format('Y-m-d H:i:s', $datosDocumentos['fecha']);
            }
            if (!$fecha) {
                $fecha = new \DateTime();
            }

            //reemplazar valores en encabezado solo si viene folio 
            //Nº {t_folio} {t_anio} {t_fecha}

            $aMeses = array("enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");

            //unificacion para set encabezado de fecha cuando viene fecha seteada o cuando es actual    
            //if ($datosRequest['generaFolio'] == 1)
            $sfecha = $fecha->format('d') . " de " . $aMeses[$fecha->format('n') - 1] . " del " . $fecha->format('Y');

            $sEncabezado = $datosDocumentos['encabezado'];
            $sCuerpo = $datosDocumentos['cuerpo'];

            $sEncabezado = $this->reemplazarTokensFecha($sEncabezado, $sfecha);
            $sCuerpo = $this->reemplazarTokensFecha($sCuerpo, $sfecha);
            $tPlantillaDistribucion = $this->reemplazarTokensFecha($tPlantillaDistribucion, $sfecha);

            if ($datosRequest['generaFolio'] == 1) {
                $sEncabezado = str_replace('{t_folio}', $nFolio, $sEncabezado);
                $sCuerpo = str_replace('{t_folio}', $nFolio, $sCuerpo);
                $tPlantillaDistribucion = str_replace('{t_folio}', $nFolio, $tPlantillaDistribucion);
            } else {
                $sEncabezado = str_replace('{t_folio}', 'SIN FOLIO', $sEncabezado);
                $sCuerpo = str_replace('{t_folio}', 'SIN FOLIO', $sCuerpo);
                $tPlantillaDistribucion = str_replace('{t_folio}', 'SIN FOLIO', $tPlantillaDistribucion);
            }

            $datosDocumentosCuerpo = $this->incrustarImagenesPdf($this->normalizarEstilosPdf($sCuerpo));
            $datosDocumentosencabezado = $this->incrustarImagenesPdf($this->normalizarEstilosPdf($sEncabezado));
            $datosDocumentosDistribucion = $this->incrustarImagenesPdf($this->normalizarEstilosPdf($tPlantillaDistribucion));

            //espacio visadores 

            $altoTotal = 0;
            $nEspacioVisadores = 40;

            //si hay visadores se setea linea y se suma 20 al alto final     
            $sDatosVisadores = $this->obtenerVisadores($nDocumento, $idDocumentoBuzon);

            if ($sDatosVisadores != "") {
                $nEspacioVisadores = 40;
            }

            $numLineasDistribucion = substr_count($tPlantillaDistribucion, "\n");
            $nEspacioDistribucion = $numLineasDistribucion * 20;

            $altoTotal = $nEspacioVisadores + $nEspacioDistribucion + $nAltoFirmas;

            $dataPdf = array(
                'materia' => $datosDocumentos['materia'],
                'encabezado' => $datosDocumentosencabezado,
                'cuerpo' => $datosDocumentosCuerpo,
                'visadores' => $sDatosVisadores !== '' ? '<div style="font-size:11px;letter-spacing:0.5px;">' . htmlspecialchars($sDatosVisadores, ENT_QUOTES, 'UTF-8') . '</div>' : '',
                'distribucion' => $datosDocumentosDistribucion,
                'altoFirmas' => $nAltoFirmas,
                'altoDistribucion' => $nEspacioDistribucion,
                'altoTotal' => $altoTotal
            );
            PDF::setOptions(['isRemoteEnabled' => true, 'chroot' => storage_path('app/public')]);
            PDF::loadView('pdf', $dataPdf)->setPaper('legal', 'portrait')->save(storage_path('app/public/files/') . $nNombreArchivoCargar);
            //return PDF::loadView('pdf', $dataPdf)->setPaper('legal', 'portrait')->stream(storage_path('app/public/files/') . $nNombreArchivoCargar);  

            //ver cuantas paginas tiene para poner firma
            //Obtiene pagina para agregar firma
            $pdfPages = file_get_contents(storage_path('app/public/files/') . $nNombreArchivoCargar);
            $count = 0;
            $count = preg_match_all("/\/Page\W/", $pdfPages, $dummy);

            Documento::find($nDocumento)->update(['paginas_archivo' => $count, 'archivo_existente' => true]);

            $dFechaCreacion = date('Y-m-d H:i:s');

            if (file_exists(storage_path('app/public/files/') . $nNombreArchivoCargar)) {
                $docsPpales = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon_archivo.id_documento_buzon', '=', 'documento_buzon.id_documento_buzon')
                    ->where('id_documento', $nDocumento)
                    ->where('id_tipo_archivo', 1)
                    ->get();

                foreach ($docsPpales as $archFile) {
                    $nSalida = $archFile->version + 1;
                    DocumentoBuzonArchivo::find($archFile->id_documento_buzon_archivo)->update(['version' => $nSalida]);
                }

                DocumentoBuzonArchivo::create([
                    'id_documento_buzon' => $idDocumentoBuzon,
                    'id_tipo_archivo' => 1,
                    'nombre_archivo_original' => $nNombreArchivoCargar,
                    'nombre_archivo_codificado' => $nNombreArchivoCargar,
                    'version' => 1,
                    'fecha' => $dFechaCreacion
                ]);

                if ($datosRequest['generaFolio'] == 1) {
                    //registrar accion en bitacora asignacion folio
                    $documentoBuzonBitacoraFolio = DocumentoBuzonBitacora::create([
                        'id_documento_buzon' => $idDocumentoBuzon,
                        'id_accion' => 9,
                        'fecha' => $fecha,
                        'id_usuario' => $datosRequest['id_usuario']
                    ]);
                }

                //registrar accion en bitacora generar pdf
                $documentoBuzonBitacora = DocumentoBuzonBitacora::create([
                    'id_documento_buzon' => $idDocumentoBuzon,
                    'id_accion' => 8,
                    'fecha' => $dFechaCreacion,
                    'id_usuario' => $datosRequest['id_usuario']
                ]);

                //registrar accion de cambio de archivo ppal en bitacora
                $documentoBuzonBitacora = DocumentoBuzonBitacora::create([
                    'id_documento_buzon' => $idDocumentoBuzon,
                    'id_accion' => 5,
                    'fecha' => $dFechaCreacion,
                    'id_usuario' => $datosRequest['id_usuario'],
                    'mensaje_respuesta' => "Cambio en archivo principal por generación de pdf."
                ]);
            } else {
                //reversa folio
                Documento::find($datosRequest["id_documento"])->update(['folio' => null]);
                Documento::find($datosRequest["id_documento"])->update(['fecha' => null]);

                //$folio,$estado,$tipo_folio,$buzon,$tipo_documento,$reversado
                $this->estado_folio($nFolio, 0, $idTipoFolio, $datosRequest['id_buzon'], $idTipoDocumento, 1);
                //db::statement("update bloqueo_folio set estado = 0, reversado = 1 where folio = ".$nFolio." and tipo_folio = ".$idTipoFolio." and buzon = ".$datosRequest['id_buzon']." and tipo_documento = ".$idTipoDocumento); 


                return $this->respondError("No se encuentra el archivo generado.", 400);
            }
            $datosJsonTipoDocumento = json_decode($datosDocumentos['json_tipo_documento'], true);
            $datosJsonTipoDocumento['id_tipo_origen'] = 2;
            $datosDocumentos->update(['json_tipo_documento' => $datosJsonTipoDocumento]);

            //desbloquear folio 
            //db::statement("update bloqueo_folio set estado = 0 where folio = ".$aDocumentoBuzon['folio']." and tipo_folio = ".$datosJsonTipoDocumento['id_tipo_folio']); 
            if ($datosRequest['generaFolio'] == 1)
                $this->estado_folio($nFolio, 0, $idTipoFolio, $datosRequest['id_buzon'], $idTipoDocumento, 0);
            DB::commit();
             return response()->json([
                'status' => 200,
                'data' => [
                    'comentario' => 'Archivo pdf generado correctamente.',
                    'folio'=>$nFolio,
                    'tipoDoc'=>strtoupper($datosJsonTipoDocumento["nombre_corto"]),
                    'anio'=>$fecha->format("Y"),
                ]
            ], 200);
            //return $this->respondSuccess("Archivo pdf generado correctamente.", 200);
        }catch (ModelNotFoundException $e) {
            db::statement("update bloqueo_folio set estado = 0, reversado = 1 where folio = " . $nFolio . " and tipo_folio = " . $idTipoFolio . " and buzon = " . $datosRequest['id_buzon'] . " and tipo_documento = " . $idTipoDocumento);
            DB::rollBack();
            //$this->estado_folio($nFolio,0,$idTipoFolio,$datosRequest['id_buzon'],$idTipoDocumento,1); 
            return response()->json([
                'status' => 500,
                'data' => [
                    'comentario' => 'Error al generar documento PDF.'
                ]
            ], 500);
        }catch (Exception $e) {
            DB::rollBack();
            //$this->estado_folio($nFolio,0,$idTipoFolio,$datosRequest['id_buzon'],$idTipoDocumento,1); 
            return response()->json([
                'status' => 500,
                'data' => [
                    'comentario' => 'Error al generar documento PDF. '.$e->getMessage()
                ]
            ], 500);
        }

        return response()->json([
            'status' => 500,
            'data' => [
                'comentario' => 'PDF NO Generado. error no encontrado'
            ]
        ], 500);
    }

    public function generar_folio(Request $request)
    {
        try {
            DB::beginTransaction();

            $datosRequest = $request->json()->all();

            $nDocumento = $datosRequest['id_documento'];
            $idDocumentoBuzon = $datosRequest['id_documento_buzon'];

            $nNombreArchivo = ""; //nombre del archivo - obtener 

            $aInfoUsuarios = Users::where('id', $datosRequest['id_usuario'])->first(['genera_pdf']);

            $datosDocumentos = Documento::findOrFail($nDocumento);

            //OBTENCION DE FOLIO 
            $idTipoDocumento = $datosDocumentos->id_tipo_documento;
            $datosJsonTipoDocumento = json_decode($datosDocumentos['json_tipo_documento'], true);
            $idTipoAsigFolio = $datosJsonTipoDocumento['id_tipo_asignacion_folio'];
            $idTipoFolio = $datosJsonTipoDocumento['id_tipo_folio'];
            $idTipoFlujo = $datosJsonTipoDocumento['id_tipo_flujo'];
            $nFolio = $datosDocumentos['folio'];
            $tPlantillaDistribucion = "";

            $nNroFirmas = (int) ($datosJsonTipoDocumento['numero_firmas'] ?? 4);
            if ($nNroFirmas < 1 || $nNroFirmas > 6) {
                $nNroFirmas = 4;
            }

            if (isset($datosDocumentos['distribucion']))
                $tPlantillaDistribucion = $datosDocumentos['distribucion'];

            //si existe folio, saltar proceso de obtención de folio 
            if (!isset($nFolio) && $datosRequest['generaFolio'] == 1) {

                if (($idTipoAsigFolio == 5) && $idTipoFlujo == 1) //se aplica a flujo libre y ultima firma                   
                {
                    $anio = date('Y');
                    $fecha = new \DateTime('now');

                    //nueva forma de obtener folio 
                    $datosBloqueo = DB::table('bloqueo_folio')
                        ->where('tipo_folio', $idTipoFolio)
                        ->where('tipo_documento', $idTipoDocumento)
                        ->where('buzon', $datosRequest['id_buzon'])
                        ->where('estado', 1)
                        ->get();

                    $existeBloqueo = count($datosBloqueo);

                    if ($existeBloqueo > 0) {
                        return $this->respondError('No se puede obtener el folio, favor intente en unos minutos.', 400);
                    }

                    $nFolio = $this->obtenerFolio($request->header('key'), $anio, $idTipoDocumento, $idTipoFolio, $datosRequest['id_buzon']);
                    //fin nueva forma de obtener folio 

                    if (isset($nFolio)) {
                        Documento::find($datosRequest["id_documento"])->update(['folio' => $nFolio]);
                        Documento::find($datosRequest["id_documento"])->update(['fecha' => $fecha->format('Y-m-d H:i:s')]);
                    } else {
                        return $this->respondError('No fue posible generar el folio.', 400);
                    }
                } else
                    $fecha = date_create_from_format('Y-m-d H:i:s', $datosDocumentos['fecha']);
            } else {
                $fecha = date_create_from_format('Y-m-d H:i:s', $datosDocumentos['fecha']);
            }

            $dFechaCreacion = date('Y-m-d H:i:s');

            //registrar accion en bitacora asignacion folio 
            if ($datosRequest['generaFolio'] == 1) {
                $documentoBuzonBitacoraFolio = DocumentoBuzonBitacora::create([
                    'id_documento_buzon' => $idDocumentoBuzon,
                    'id_accion' => 9,
                    'fecha' => $fecha,
                    'id_usuario' => $datosRequest['id_usuario']
                ]);
            }

            $datosJsonTipoDocumento = json_decode($datosDocumentos['json_tipo_documento'], true);
            $datosJsonTipoDocumento['id_tipo_origen'] = 2;
            $datosDocumentos->update(['json_tipo_documento' => $datosJsonTipoDocumento]);

            //desbloquear folio  
            if ($datosRequest['generaFolio'] == 1)
                $this->estado_folio($nFolio, 0, $idTipoFolio, $datosRequest['id_buzon'], $idTipoDocumento, 0);

            DB::commit();

            //return $this->respondSuccess("Archivo pdf generado correctamente.", 200);
            return response()->json([
                'status' => 200,
                'data' => [
                    'comentario' => 'Folio generado correctamente.',
                    'folio'=>$nFolio,
                    'tipoDoc'=>strtoupper($datosJsonTipoDocumento["nombre_corto"]),
                    'anio'=>$fecha->format("Y"),
                ]
            ], 200);
        } catch (ModelNotFoundException $e) {
            db::statement("update bloqueo_folio set estado = 0, reversado = 1 where folio = " . $nFolio . " and tipo_folio = " . $idTipoFolio . " and buzon = " . $datosRequest['id_buzon'] . " and tipo_documento = " . $idTipoDocumento);

            DB::rollBack();

            return response()->json([
                'status' => 500,
                'data' => [
                    'comentario' => 'Error al generar documento PDF.'
                ]
            ], 500);
        }
    }

    public function generar_vista_previa(Request $request)
    {
        try {
            $datosRequest = $request->json()->all();

            $nDocumento = $datosRequest['id_documento'];
            $idDocumentoBuzon = $datosRequest['id_documento_buzon'];

            $nNombreArchivoCargar = $this->getNombreDocumento($nDocumento);

            $aInfoUsuarios = Users::where('id', $datosRequest['id_usuario'])->first(['genera_pdf']);

            $datosDocumentos = Documento::findOrFail($nDocumento);
            $aMeses = array("enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");

            //OBTENCION DE FOLIO
            // si existe folio (por alguna razon, no volver a foliar)
            if ($datosDocumentos->folio == null) {
                $idTipoDocumento = $datosDocumentos->id_tipo_documento;
                $datosJsonTipoDocumento = json_decode($datosDocumentos['json_tipo_documento'], true);
                $idTipoAsigFolio = $datosJsonTipoDocumento['id_tipo_asignacion_folio'];
                $idTipoFolio = $datosJsonTipoDocumento['id_tipo_folio'];
                $idTipoFlujo = $datosJsonTipoDocumento['id_tipo_flujo'];
                $nFolio = $datosDocumentos['folio'];
                $sfecha = date('d') . " de " . $aMeses[date('n') - 1] . " del " . date('Y');
            } else {
                //$sfecha = $datosDocumentos->fecha;
                $date = strtotime($datosDocumentos->fecha);
                $sfecha = date('d', $date) . " de " . $aMeses[date('n', $date) - 1] . " del " . date('Y', $date);
                $nFolio = $datosDocumentos->folio;
            }

            if (isset($nFolio) == null || isset($nFolio) == '')
                $nFolio = '000';

            //reemplazar valores en encabezado
            //Nº {t_folio} {t_anio} {t_fecha}    

            $sEncabezado = $datosDocumentos['encabezado'];
            $sEncabezado = str_replace('{t_folio}', $nFolio, $sEncabezado);
            $sEncabezado = $this->reemplazarTokensFecha($sEncabezado, $sfecha);

            $sCuerpo = $datosDocumentos['cuerpo'];
            $sCuerpo = str_replace('{t_folio}', $nFolio, $sCuerpo);
            $sCuerpo = $this->reemplazarTokensFecha($sCuerpo, $sfecha);

            $datosDocumentosCuerpo = $this->incrustarImagenesPdf($this->normalizarEstilosPdf($sCuerpo));
            $datosDocumentosencabezado = $this->incrustarImagenesPdf($this->normalizarEstilosPdf($sEncabezado));

            $dataPdf = array('materia' => $datosDocumentos['materia'], 'encabezado' => $datosDocumentosencabezado, 'cuerpo' => $datosDocumentosCuerpo);

            PDF::setOptions(['isRemoteEnabled' => true, 'chroot' => storage_path('app/public')]);
            $pdf = PDF::loadView('pdf', $dataPdf)->setPaper('legal', 'portrait');

            return $pdf->download('vista_previa.pdf');
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 500,
                'data' => [
                    'comentario' => 'Error al generar vista previa.'
                ]
            ], 500);
        }
    }


    public function getNombreDocumento($idDoc)
    {
        $datosDocumento = Documento::findOrFail($idDoc);

        $datosJsonTipoDocumento = json_decode($datosDocumento['json_tipo_documento'], true);

        $nAleatorio = rand(100000, 99999999);
        $dFechaCreacion = date('Ymd');
        $txtTipoDoc = $datosJsonTipoDocumento['nombre_corto'];

        $nombreFinal = $txtTipoDoc . '-' . $idDoc . '-' . $dFechaCreacion . '-' . $nAleatorio . '.pdf';
        //$nombreFinal = $txtTipoDoc . '-' . $idDoc . '-' . $dFechaCreacion . '-' . $nAleatorio;

        return $nombreFinal;
    }

    public function obtenerFolio($llave, $anio, $tipo_documento, $tipo_folio, $buzon = null)
    {
        $nFolio = 0;
        //Por tipo de documento y año
        if ($tipo_folio == 1) {
            $dFolio = DB::table('tipo_documento_buzon_folio')
                ->where('id_tipo_documento', $tipo_documento)
                ->where('anio', $anio)
                ->select('valor')
                ->get();


            if (count($dFolio) > 0) {
                $nFolio = $dFolio[0]->valor;
            }

            $nFolio++;

            if ($nFolio <= 1) {
                DB::statement('insert into tipo_documento_buzon_folio (id_tipo_documento,anio,valor,created_at,updated_at) values (' . $tipo_documento . ',' . $anio . ',' . $nFolio . ',now(),now())');
            } else {
                DB::statement('update tipo_documento_buzon_folio set valor = ' . $nFolio . ', updated_at = now() where id_tipo_documento= ' . $tipo_documento . ' and anio = ' . $anio . ' ');
            }
        }
        //Por tipo de documento, buzón y año
        if ($tipo_folio == 2) {
            $dFolio = DB::table('tipo_documento_buzon_folio')
                ->where('id_tipo_documento', $tipo_documento)
                ->where('id_buzon', $buzon)
                ->where('anio', $anio)
                ->get();


            if (count($dFolio) > 0) {
                $nFolio = $dFolio[0]->valor;
            }
            $nFolio++;

            if ($nFolio <= 1) {
                DB::statement('insert into tipo_documento_buzon_folio (id_tipo_documento,anio,id_buzon,valor,created_at,updated_at) values (' . $tipo_documento . ',' . $anio . ',' . $buzon . ',' . $nFolio . ',now(),now())');
            } else {
                DB::statement('update tipo_documento_buzon_folio set valor = ' . $nFolio . ', updated_at = now() where id_tipo_documento= ' . $tipo_documento . ' and anio = ' . $anio . ' and id_buzon = ' . $buzon);
            }
        }

        //sin folio
        if ($tipo_folio == 3) {
            $nFolio = $nFolio;
        }

        $this->estado_folio($nFolio, 1, $tipo_folio, $buzon, $tipo_documento, 0);
        return $nFolio;
    }


    public function obtenerVisadores($idDocumento, $idDocumentoBuzon)
    {
        $datosVisarFirmar = DocumentoBuzonBitacora::join('documento_buzon', 'documento_buzon.id_documento_buzon', '=', 'documento_buzon_bitacora.id_documento_buzon')
            ->join('documento', 'documento_buzon.id_documento', '=', 'documento.id_documento')
            ->join('accion', 'accion.id_accion', '=', 'documento_buzon_bitacora.id_accion')
            ->join('users', 'users.id', '=', 'documento_buzon_bitacora.id_usuario')
            ->join('buzon', 'buzon.id_buzon', '=', 'documento_buzon.id_buzon')
            ->where('documento.id_documento', $idDocumento)
            ->whereIn('documento_buzon_bitacora.id_accion', array('1', '4', '6'))
            ->select(
                'documento_buzon_bitacora.id_accion',
                'documento_buzon.id_buzon',
                'accion.nombre',
                'documento_buzon_bitacora.id_usuario',
                'users.nombres',
                'users.primer_apellido',
                'users.segundo_apellido'
            )
            ->orderBy('documento_buzon_bitacora.id_documento_buzon_bitacora', 'desc')
            ->get();

        $txtVisadores = "";
        $txtVisadoresCrea = "";
        $txtUserBuzonPrev = "";
        $nTerminaCiclo = 0;

        foreach ($datosVisarFirmar as $value) {
            if ((int) $value['id_accion'] === 1) {
                $txtVisadoresCrea = $this->inicialesUsuario($value, false);
            }

            if ((int) $value['id_accion'] === 6 && $nTerminaCiclo != 1) {
                $txtUserBuzon = $value['id_buzon'] . $value['id_usuario'];
                if ($txtUserBuzonPrev != $txtUserBuzon) {
                    $txtUserBuzonPrev = $txtUserBuzon;
                    $txtVisadores .= $this->inicialesUsuario($value, true) . "/";
                }
            }
            if ((int) $value['id_accion'] === 4) {
                $nTerminaCiclo = 1;
            }
        }

        if ($txtVisadores != "") {
            $txtVisadores .= $txtVisadoresCrea;
        }

        return $txtVisadores;
    }

    protected function inicialesUsuario($value, bool $mayusculas): string
    {
        $n = mb_substr(trim((string) ($value['nombres'] ?? '')), 0, 1, 'UTF-8');
        $a1 = mb_substr(trim((string) ($value['primer_apellido'] ?? '')), 0, 1, 'UTF-8');
        $a2 = mb_substr(trim((string) ($value['segundo_apellido'] ?? '')), 0, 1, 'UTF-8');
        $ini = $n . $a1 . $a2;
        return $mayusculas ? mb_strtoupper($ini, 'UTF-8') : mb_strtolower($ini, 'UTF-8');
    }

    protected function reemplazarTokensFecha($html, $sfecha)
    {
        $html = $html ?: '';
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $dia = date('d');
        $mes = $meses[(int) date('n') - 1];
        $anio = date('Y');
        // Si $sfecha trae día completo ("24 de agosto del 2026"), preferir ese día/mes/año.
        if (preg_match('/^(\d{1,2})\s+de\s+(\w+)\s+del\s+(\d{4})/u', (string) $sfecha, $m)) {
            $dia = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $mes = $m[2];
            $anio = $m[3];
        }
        // CKEditor a veces parte {t_dia} con spans.
        $html = preg_replace_callback('/\{([^{}]+)\}/u', function ($mm) {
            $inner = preg_replace('/<[^>]+>/', '', $mm[1]) ?? $mm[1];
            $inner = html_entity_decode($inner, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $inner = preg_replace('/\s+/u', '', $inner) ?? $inner;
            return '{' . $inner . '}';
        }, $html) ?? $html;
        $html = str_replace(['{t_fecha}', '{{fecha}}'], $sfecha, $html);
        $html = str_replace(['{t_anio}', '{{anio}}'], $anio, $html);
        $html = str_replace(['{t_mes}', '{{mes}}', '{mes}'], $mes, $html);
        $html = str_replace(['{t_dia}', '{{dia}}', '{dia}'], $dia, $html);
        return $this->completarDiaFecha($html, $dia);
    }

    protected function completarDiaFecha($html, $dia)
    {
        $meses = 'enero|febrero|marzo|abril|mayo|junio|julio|agosto|septiembre|octubre|noviembre|diciembre';
        $patMes = 'de\s+(' . $meses . ')\s+del?\s+\d{4}';
        $fechaCompleta = '/\d{1,2}\s+(' . $meses . ')\s+del?\s+\d{4}/iu';
        $html = (string) $html;

        $html = $this->deduplicarDiaFecha($html, $patMes);
        $html = preg_replace('/(\d{1,2})\s*<br\s*\/?>\s*(' . $patMes . ')/iu', '$1 $2', $html) ?? $html;

        if (preg_match($fechaCompleta, $html)) {
            return $this->deduplicarDiaFecha($html, $patMes);
        }

        $html = preg_replace_callback(
            '/(^|>|&nbsp;|[\s\x{00A0}_\.·…]+)(' . $patMes . ')/iu',
            function ($m) use ($dia, $html) {
                $pos = $m[0][1];
                $before = substr($html, max(0, $pos - 200), $pos - max(0, $pos - 200));
                $before = html_entity_decode(strip_tags(str_replace('&nbsp;', ' ', $before)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $before = preg_replace('/\s+/u', ' ', $before) ?? $before;
                if (preg_match('/\d{1,2}\s*$/u', rtrim($before))) {
                    return $m[0][0];
                }
                return $m[1][0] . $dia . ' ' . $m[2][0];
            },
            $html,
            -1,
            $count,
            PREG_OFFSET_CAPTURE
        ) ?? $html;

        return $this->deduplicarDiaFecha($html, $patMes);
    }

    protected function deduplicarDiaFecha($html, $patMes)
    {
        return preg_replace('/(\d{1,2})\s+\1\s+(' . $patMes . ')/iu', '$1 $2', $html) ?? $html;
    }

    /** margin-left enorme (p. ej. 600px desde Word) recorta el inicio del texto en DomPDF. */
    protected function normalizarEstilosPdf($html)
    {
        $html = (string) ($html ?: '');
        $html = preg_replace_callback(
            '/\bstyle=(["\'])(.*?)\1/is',
            function ($m) {
                $style = $m[2];
                if (preg_match('/margin-left\s*:\s*(\d+)px/i', $style, $mm) && (int) $mm[1] >= 200) {
                    $style = preg_replace('/margin-left\s*:\s*\d+px\s*;?/i', '', $style);
                    if (!preg_match('/text-align\s*:/i', $style)) {
                        $style = 'text-align:right;' . $style;
                    }
                }
                $style = trim($style, " \t\n\r\0\x0B;") . ';';
                return 'style=' . $m[1] . $style . $m[1];
            },
            $html
        ) ?? $html;
        $html = preg_replace('/<img\b[^>]*\bsrc=["\']file:[^"\']*["\'][^>]*>/i', '', $html) ?? $html;
        return $html;
    }

    protected function incrustarImagenesPdf($html)
    {
        $html = $html ?: '';
        // Cualquier host (prod/test) → path local /files/...
        $html = preg_replace('#https?://[^"\'\s>]+/files/#i', '/files/', $html) ?? $html;
        $appUrl = rtrim((string) env('APP_URL'), '/');
        if ($appUrl !== '') {
            $html = str_replace($appUrl, storage_path('app/public'), $html);
        }
        return preg_replace_callback('/<img\b[^>]*>/i', function ($m) {
            $tag = $m[0];
            if (!preg_match('/\bsrc\s*=\s*["\']([^"\']+)["\']/i', $tag, $sm)) {
                return $tag;
            }
            $src = html_entity_decode($sm[1], ENT_QUOTES, 'UTF-8');
            $local = $this->rutaLocalImagen($src);
            if (!$local) {
                // Evita el texto "Image not found or type unknown" de DomPDF.
                return '';
            }
            $mime = @mime_content_type($local);
            if (!$mime || strpos($mime, 'image/') !== 0) {
                $ext = strtolower(pathinfo($local, PATHINFO_EXTENSION));
                $map = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
                $mime = $map[$ext] ?? 'image/png';
            }
            $data = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($local));
            return preg_replace('/\bsrc\s*=\s*["\'][^"\']+["\']/i', 'src="' . $data . '"', $tag, 1);
        }, $html);
    }

    protected function rutaLocalImagen($src)
    {
        $src = trim(str_replace('\\', '/', (string) $src));
        if ($src === '' || stripos($src, 'data:') === 0) {
            return null;
        }
        if (is_file($src) && is_readable($src)) {
            return $src;
        }
        $path = parse_url($src, PHP_URL_PATH);
        if (!$path) {
            $path = $src;
        }
        $path = urldecode($path);
        $marcadores = ['/files/', '/storage/files/'];
        foreach ($marcadores as $needle) {
            $p = strpos($path, $needle);
            if ($p === false) {
                continue;
            }
            $rel = ltrim(substr($path, $p + strlen($needle)), '/');
            $local = storage_path('app/public/files/' . $rel);
            if (is_file($local) && is_readable($local)) {
                return $local;
            }
        }
        $candidates = [
            storage_path('app/public' . $path),
            storage_path('app/public/files' . $path),
            $path,
        ];
        foreach ($candidates as $c) {
            if (is_file($c) && is_readable($c)) {
                return $c;
            }
        }
        // Fallback escudo municipal (misma imagen que usa el FE)
        $fallback = base_path('../fe/public/img/logo2.png');
        if (!is_file($fallback)) {
            $fallback = storage_path('app/public/files/editor/images/logo_municipal.png');
        }
        if (!is_file($fallback)) {
            $fallback = '/var/www/sgd/public/img/logo2.png';
        }
        if (is_file($fallback) && is_readable($fallback)) {
            return $fallback;
        }
        return null;
    }

    public function estado_folio($folio, $estado, $tipo_folio, $buzon, $tipo_documento, $reversado)
    {
        if ($estado == 1) {
            db::statement("insert into bloqueo_folio (folio,estado,tipo_folio,buzon,tipo_documento,reversado) values (" . $folio . ",1," . $tipo_folio . "," . $buzon . "," . $tipo_documento . "," . $reversado . ")");
        } else {
            db::statement("update bloqueo_folio set estado = 0, reversado = " . $reversado . " where folio = " . (($folio == null) ? '0' : $folio) . " and tipo_folio = " . $tipo_folio . " and buzon = " . $buzon . " and tipo_documento = " . $tipo_documento);
        }
    }
}
