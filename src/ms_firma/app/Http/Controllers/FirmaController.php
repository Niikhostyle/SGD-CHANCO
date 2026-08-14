<?php

namespace App\Http\Controllers;

use Exception;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

use App\Libraries\FirmaBase;
use App\Models\Buzon;
use App\Models\TipoFirma;
use App\Models\Users;
use App\Models\Documento;
use App\Models\DocumentoBuzon;
use App\Models\DocumentoBuzonBitacora;
use App\Models\DocumentoBuzonArchivo;
use setasign\Fpdi\Fpdi;
use App\Libraries\PDF;
use App\Models\FirmaLog;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\Return_;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class FirmaController extends Controller
{
    public function firmar_archivo(Request $request)
    {
        if ($request->isJson()) {


            //eliminar log de firmas con errores
            $datos = $request->json()->all();
            $logs = FirmaLog::where('id_documento', $datos['id_documento'])->delete();

            try {
                DB::beginTransaction();

                $datos = $request->json()->all();

                //GENERACIÓN IMAGEN PARA FIRMA
                $aInfoUsuarios = Users::where('id', $datos['id_usuario'])->first(['run', 'nombres', 'primer_apellido', 'segundo_apellido', 'img_firma', 'aplica_fea', 'id']);
                $sNombre = $aInfoUsuarios['nombres'] . ' ' . $aInfoUsuarios['primer_apellido'] . ' ' . $aInfoUsuarios['segundo_apellido'];
                $sNombreImg = $aInfoUsuarios['run'] . date('dmYHis') . '.png';
                $sNombreImgAnexo = $aInfoUsuarios['run'] . date('dmYHis') . '_a.png';
                $sNombreImgFolio = $aInfoUsuarios['run'] . date('dmYHis') . '_f.png';
                $dFechaCreacion = date('Y-m-d H:i:s');

                $DatosFirma = Buzon::join('buzon_usuario', 'buzon_usuario.id_buzon', '=', 'buzon.id_buzon')
                    ->join('tipo_firma', 'buzon_usuario.id_tipo_firma', '=', 'tipo_firma.id_tipo_firma')
                    ->where('buzon.id_buzon', '=', $datos['id_buzon'])
                    ->where('buzon_usuario.id_usuario', '=', $datos['id_usuario'])
                    ->select('cargo_firma', 'tipo_firma.id_tipo_firma', 'sigla', 'restringir_sr', 'id_usuario_sr')
                    ->first();

                if (!isset($DatosFirma['cargo_firma'])) {
                    $comentario = "No existe cargo asociado al buzón.";
                    throw new Exception($comentario);
                }

                //verificar restriccion firma subrogante
                if (isset($DatosFirma['id_usuario_sr']) && $DatosFirma['id_usuario_sr'] !== 0 && $DatosFirma['restringir_sr'] == 1 &&  $DatosFirma['id_tipo_firma'] == 2) {
                    if ($DatosFirma['id_usuario_sr'] == 10000) {
                        $comentario = "No existe subrogante definido.";
                        throw new Exception($comentario);
                    }
                    if ($DatosFirma['id_usuario_sr'] != $aInfoUsuarios['id']) {
                        $comentario = "No está autorizado como subrogante.";
                        throw new Exception($comentario);
                    }
                }

                if ($aInfoUsuarios['img_firma'] == '' || $aInfoUsuarios['img_firma'] == null) {
                    $comentario = "No existe imagen para firma asociada al usuario.";
                    throw new Exception($comentario);
                }


                //comprobar si la imagen está en el repositorio
                if (!file_exists(storage_path('app/public/files/imagen_firma/' . $aInfoUsuarios['img_firma']))) {
                    $comentario = "No existe imagen para firma asociada al usuario.";
                    throw new Exception($comentario);
                }
                $img = Image::make(storage_path('app/public/files/imagen_firma/' . $aInfoUsuarios['img_firma']));
                
                $dFechaCreacionImg = date('d.m.Y H:i:s');
                $img->text('Firmado electrónicamente por:', 330, 60, function ($font) {
                    $font->file(storage_path('../public/calibri.ttf'));
                    $font->size(34);
                });
                $img->text(Str::upper($sNombre), 330, 110, function ($font) {
                    $font->file(storage_path('../public/calibrib.ttf'));
                    $font->size(34);
                });
                $img->text('Fecha: ' . $dFechaCreacionImg, 330, 160, function ($font) {
                    $font->file(storage_path('../public/calibri.ttf'));
                    $font->size(34);
                });

                $string = wordwrap($DatosFirma['cargo_firma'], 35) . ' ' . $DatosFirma['sigla'];
                $img->text($string, 330, 235, function ($font) {
                    $font->file(storage_path('../public/calibri.ttf'));
                    $font->size(34);
                });

                $img->save(storage_path('app/public/files/imagen_firma/' . $sNombreImg));

                //INICIO PROCESO FIRMA
                $aInfoDocumento = Documento::where('id_documento', $datos['id_documento'])->first(['hash_validacion', 'identificador', 'json_tipo_documento', 'distribucion']);

                //obtener máximo de firmas 
                $datosJsonTipoDocumento = json_decode($aInfoDocumento['json_tipo_documento'], true);
                $idTipoAsigFolio = $datosJsonTipoDocumento['id_tipo_asignacion_folio'];

                if (isset($datosJsonTipoDocumento['numero_firmas']))
                    $nNroFirmas = $datosJsonTipoDocumento['numero_firmas'];
                else
                    $nNroFirmas = 4;

                if (!$aInfoUsuarios['aplica_fea']) {
                    $comentario = "Usuario no tiene permiso para realizar firma electrónica.";
                    throw new Exception($comentario);
                }

                
                // calcular la cantidad de firmas en el documento
                $cantidadFirmas = $this->getNfirmas($datos['id_documento']);// count($datosBitacora);


                $aDocumentoBuzon = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon_archivo.id_documento_buzon', '=', 'documento_buzon.id_documento_buzon')
                    ->join('documento', 'documento_buzon.id_documento', '=', 'documento.id_documento')
                    ->where('documento_buzon.id_documento', '=', $datos['id_documento'])
                    ->where('id_tipo_archivo', '=', '1')
                    ->where('version', '=', '1')
                    ->where('nombre_archivo_codificado', '!=', null)
                    ->select('nombre_archivo_codificado', 'paginas_archivo', 'id_tipo_archivo', 'folio')
                    ->first();

                $txt_footer_folio = "";
                    
                if (!isset($aDocumentoBuzon['nombre_archivo_codificado']) || !isset($aDocumentoBuzon['folio'])) //si no hay archivo o folio en ultima firma 
                {

                    $comentario = "No existe archivo para realizar firma electrónica.";

                    $nGeneraFolio = 0;
                    $nGeneraArchivo = 0;

                    if (($idTipoAsigFolio == 2 || $idTipoAsigFolio == 4) && $cantidadFirmas == 0) //primera firma 
                    {
                        $nGeneraFolio = 1;
                        $nGeneraArchivo = 1;
                    }

                    if ($idTipoAsigFolio == 5 && ($cantidadFirmas == ($nNroFirmas - 1))) //ultima firma 
                    {
                        $nGeneraFolio = 1;
                    }

                    if ((($idTipoAsigFolio == 5 || $idTipoAsigFolio == 1  || $idTipoAsigFolio == 3) && $cantidadFirmas == 0)) {
                        $nGeneraFolio = 0;
                        if (!isset($aDocumentoBuzon['nombre_archivo_codificado']))
                            $nGeneraArchivo = 1;
                    }

                    if ($nGeneraArchivo == 1) {
                        //generar pdf                    
                        $datosArchivo = Http::withHeaders(['key' => $request->header('key'), 'Content-Type' => 'application/json']) //
                            ->timeout(60)
                            ->put(env('API_SGD_ARCHIVOS','http://sgd_ms_archivos:3333').'/api/sgd-archivos/generar_archivo_pdf', [
                                'id_documento' => $datos['id_documento'],
                                'id_documento_buzon' => $datos['id_documento_buzon'],
                                'id_usuario' => $datos['id_usuario'],
                                'id_buzon' => $datos['id_buzon'],
                                'generaFolio' => $nGeneraFolio
                            ])->throw();
                        if($datosArchivo->failed()){
                            $comentario = "No se pudo generar el archivo PDF.";
                            throw new Exception($comentario);
                        }
                        // ver si hay folio
                        if($datosArchivo["data"]["folio"]!=null)
                            $txt_footer_folio = $datosArchivo['data']['tipoDoc']." ".$datosArchivo["data"]["folio"]. "/".$datosArchivo["data"]["anio"];

                    }

                    if ($nGeneraArchivo == 0 && $nGeneraFolio == 1) {

                        //generar folio 
                        $datosArchivo = Http::withHeaders(['key' => $request->header('key'), 'Content-Type' => 'application/json'])
                            ->timeout(60)
                            ->put(env('API_SGD_ARCHIVOS','http://sgd_ms_archivos:3333').'/api/sgd-archivos/generar_folio', [
                                'id_documento' => $datos['id_documento'],
                                'id_documento_buzon' => $datos['id_documento_buzon'],
                                'id_usuario' => $datos['id_usuario'],
                                'id_buzon' => $datos['id_buzon'],
                                'generaFolio' => $nGeneraFolio
                            ])->throw();
                            if($datosArchivo->failed()){
                                $comentario = "No se pudo generar el folio.";
                                throw new Exception($comentario);
                            } 
                        $txt_footer_folio = $datosArchivo['data']['tipoDoc']." ".$datosArchivo["data"]["folio"]. "/".$datosArchivo["data"]["anio"];
                    }

                    if (isset($datosArchivo['status']) && ($datosArchivo['status'] == '400' || $datosArchivo['status'] == '500')) {
                        throw new Exception($datosArchivo['data']['comentario']);
                    }

                    $aDocumentoBuzon = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon_archivo.id_documento_buzon', '=', 'documento_buzon.id_documento_buzon')
                        ->join('documento', 'documento_buzon.id_documento', '=', 'documento.id_documento')
                        ->where('documento_buzon.id_documento', '=', $datos['id_documento'])
                        ->where('id_tipo_archivo', '=', '1')
                        ->where('version', '=', '1')
                        ->where('nombre_archivo_codificado', '!=', null)
                        ->select('nombre_archivo_codificado', 'paginas_archivo', 'id_tipo_archivo', 'folio')
                        ->first();
                }

                if (!isset($aDocumentoBuzon['nombre_archivo_codificado'])) {
                    $comentario = "No existe archivo PDF para realizar firma electrónica.";
                    throw new Exception($comentario);
                }

                $firmaDigitalConfig = array(
                    'api'       => env('PLCSGD_API_URL'),
                    'purpose'   => env('PLCSGD_API_PURPOSE'),
                    'entity'    => env('PLCSGD_API_ENTITY'),
                    'tokenKey'  => env('PLCSGD_API_TOKEN_KEY'),
                    'secretKey' => env('PLCSGD_SECRETO')
                );
                $classFirma = new FirmaBase($firmaDigitalConfig);

                $sNombreArchivo = $aDocumentoBuzon['nombre_archivo_codificado'];

                $sDescipcion = "Firmado electrónicamente por " . $aInfoUsuarios['nombres'] . ' ' . $aInfoUsuarios['primer_apellido'] . ' ' . $aInfoUsuarios['segundo_apellido'];
                $nRut = explode("-", $aInfoUsuarios['run']);
                $nRutFirma = $nRut[0];
                $sPath = config('app.path_upload') . '/';
                //$sArchivo = storage_path('app/public/files/' . $sNombreArchivo); //cambiar por linea sgte               
                $id_documento_buzon = $datos['id_documento_buzon'];
                $imagen_firma = storage_path('app/public/files/imagen_firma/' . $sNombreImg);
                $imagen_firma_anexo = storage_path('app/public/files/imagen_firma/' . $sNombreImgAnexo);
                $imagen_firma_folio = storage_path('app/public/files/imagen_firma/' . $sNombreImgFolio);



                if (!file_exists($imagen_firma)) {
                    $comentario = "Existe un problema con la imagen relacionada a la firma electrónica.";
                    throw new Exception($comentario);
                }

                //plantilla distribución

                $nEspacioDistribucion = 0;

                if (isset($aInfoDocumento['distribucion'])) {
                    $tPlantillaDistribucion = $aInfoDocumento['distribucion'];
                    $numLineasDistribucion = substr_count($tPlantillaDistribucion, "\n");

                    $nEspacioDistribucion = $numLineasDistribucion * 20;
                } else {
                    $nEspacioDistribucion = 50;
                }

                if ($cantidadFirmas >= $nNroFirmas) {
                    $comentario = "Excede el máximo de firmas electrónicas posibles.";
                    throw new Exception($comentario);
                }

                //alto visadores
                $datosVisar = DocumentoBuzonBitacora::join('documento_buzon', 'documento_buzon_bitacora.id_documento_buzon', '=', 'documento_buzon.id_documento_buzon')
                    ->where('documento_buzon.id_documento', $datos['id_documento'])
                    ->where('documento_buzon_bitacora.id_accion', 6)
                    ->get();

                $nEspacioVisadores = 20;
                if (count($datosVisar) > 0)
                    $nEspacioVisadores = 40;

                $aUbicacionesFirma = array(
                    array(300, 240, 555, 325),
                    array(30, 240, 285, 325),
                    array(300, 140, 555, 225),
                    array(30, 140, 285, 225),
                    array(300, 40, 555, 125),
                    array(30, 40, 285, 125)
                );

                //posiciones desde donde comenzar a utilizar las ubicaciones del array anterior
                $aFirmaPosicion = array(
                    '1' => array('0' => 4),
                    '2' => array('0' => 4, '1' => 5),
                    '3' => array('0' => 2, '1' => 3, '2' => 4),
                    '4' => array('0' => 2, '1' => 3, '2' => 4, '3' => 5),
                    '5' => array('0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4),
                    '6' => array('0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5)
                );

                //$aFirmaPosicion[5]; hay 5 firmas, parten desde la posicion 0 del array $aUbicacionesFirma
                //cantidad de firmas $nNroFirmas
                
                $nPosFirma = $cantidadFirmas;
                $nPosCant = $aFirmaPosicion[$nNroFirmas][$nPosFirma];

                $n_llx = $aUbicacionesFirma[$nPosCant][0];
                $n_lly = $aUbicacionesFirma[$nPosCant][1] + $nEspacioDistribucion + $nEspacioVisadores;
                $n_urx = $aUbicacionesFirma[$nPosCant][2];
                $n_ury = $aUbicacionesFirma[$nPosCant][3] + $nEspacioDistribucion + $nEspacioVisadores;

                //por defecto anexo
                $n_llx_anexo = 300;
                $n_lly_anexo = 80;
                $n_urx_anexo = 555;
                $n_ury_anexo = 165;

                //firma 1 anexo
                if ($cantidadFirmas == 0) {
                    $n_llx_anexo = 300;
                    $n_lly_anexo = 80;
                    $n_urx_anexo = 555;
                    $n_ury_anexo = 165;
                }
                //firma 2 anexo
                if ($cantidadFirmas == 1) {
                    $n_llx_anexo = 30;
                    $n_lly_anexo = 80;
                    $n_urx_anexo = 285;
                    $n_ury_anexo = 165;
                }

                $pdf = new PDF();

                //leer si la version de PDF es 1.4, solo si no es generado en el sistema, en caso contrario, transformarlo
                //$pdf->setPdfVersion('1.4');
                if($datosJsonTipoDocumento["id_tipo_origen"] != 1)
                        $sArchivo = $this->convertPDF($sNombreArchivo);
                else
                        $sArchivo =  storage_path('app/public/files/'.$sNombreArchivo);


                $pageCount = $pdf->setSourceFile($sArchivo);
                //$pageCount = 1;

                $nPaginasPdf = $aDocumentoBuzon['paginas_archivo'];
                if ($aDocumentoBuzon['paginas_archivo'] == '' || $aDocumentoBuzon['paginas_archivo'] == null)
                    $nPaginasPdf = $pageCount;

                $layout = array(
                    'filename' => $imagen_firma,
                    'page'     => $nPaginasPdf,
                    'llx'      => $n_llx, //50
                    'lly'      => $n_lly, //50
                    'urx'      => $n_urx, //210
                    'ury'      => $n_ury  //100
                );

                $layoutAnexo = array(
                    'filename' => $imagen_firma_anexo,
                    'page'     => 'LAST',
                    'llx'      => $n_llx_anexo,
                    'lly'      => $n_lly_anexo,
                    'urx'      => $n_urx_anexo,
                    'ury'      => $n_ury_anexo
                );

                $layoutFolio = array(
                    'filename' => $imagen_firma_folio,
                    'page'     => 1,
                    'llx'      => 180,
                    'lly'      => 900,
                    'urx'      => 480,
                    'ury'      => 980
                );

                $nNombreArchivoCargar = $this->getNombreDocumento($datos['id_documento']);

                //agrega Hash de validación
                if ($cantidadFirmas == 0) {
                    //QR para validacion
                    $url = env('APP_URL') . '/validador_qr/' . $aInfoDocumento['hash_validacion'];

                    $imgQR = QrCode::format('png')->size(60)->generate($url);
                    $tempQrImage = tempnam(sys_get_temp_dir(), $aInfoDocumento['hash_validacion']);
                    file_put_contents($tempQrImage, $imgQR);

                    //excepcion si no existe imagen
                    if (!file_exists($tempQrImage)) {
                        $comentario = "No se pudo generar el código QR.";
                        throw new Exception($comentario);
                    }

                    //Leer tipo de documento y folio (si esá seteado en priumera firma o creacion de documento)
                    

                    $html = 'ID: ' . $aInfoDocumento['identificador'] . ' | Para validar el documento haga click <a href="' . $url . '">aquí</a>, o escanee el código QR.';
                    if($txt_footer_folio!="")
                        $html = $txt_footer_folio."<br>".$html;
                 
                    $pdf->AliasNbPages();
                    //$pdf->footer_txt = "Para verificar este documento, use el siguiente identificador: " . $aInfoDocumento['hash_validacion'];}
                    $pdf->footer_txt = $html; //"Para verificar este documento haga clic en ".$url." o use el siguiente QR:";
                    $pdf->footer_qr = $tempQrImage;
                    $pdf->url_qr = $url;
                    $pdf->tipo_origen = $datosJsonTipoDocumento['id_tipo_origen'];
                    //$pdf->Image($codigoQR,140,325,0,0,'PNG',$url);
                    $pdf->PageFirma = $nPaginasPdf;
                    $pageSize = $pdf->GetPageWidth();

                    for ($i = 1; $i <= $pageCount; $i++) {
                        //import a page then get the id and will be used in the template
                        $tplId = $pdf->importPage($i);

                        $size = $pdf->getTemplateSize($tplId);

                        $pdf->AddPage($size['orientation'], array($size['width'], $size['height']));
                        $pdf->alto = $pdf->GetPageHeight();
                        //use the template of the imporated page
                        $pdf->useTemplate($tplId);
                    }

                    $sArchivo = storage_path('app/public/files/' . $nNombreArchivoCargar);
                    $pdf->Output($sArchivo, 'F');
                    //borrar imagen QR
                    //unlink($aInfoDocumento['hash_validacion'].".png");

                }

                //obtiene el peso del archivo para firma

                $fileSize = filesize($sArchivo);
                $fileSize_mb = round($fileSize / 1048576, 0, PHP_ROUND_HALF_UP);
                $nfh = 0;

                if ($fileSize_mb >= 5) //firma hash  
                {
                    $aRespuestaFirma = $this->callHash($sArchivo, $layout, 0, $nNombreArchivoCargar, $nRutFirma);
                    Log::info("Firma DOC ".$sArchivo." por RUT ".$nRutFirma." Forma JAVA");
                    $nfh = 1;
                } else //firma tradicional
                {
                    //dd("firma tradicional");
                    Log::info("Firma DOC ".$sArchivo." por RUT ".$nRutFirma." Forma Tradicional");
                    $aRespuestaFirma = $classFirma->setRUN($nRutFirma)
                        ->addPDF($sArchivo, $sDescipcion, $layout)
                        ->sign();
                }

                if (isset($aRespuestaFirma['status'])) {
                    $comentario = $aRespuestaFirma['error'];
                    throw new Exception($comentario);
                }

                if (isset($aRespuestaFirma['metadata'])) {
                    if ($aRespuestaFirma['metadata']['filesSigned'] == 1) {
                        $responseFile = $aRespuestaFirma['files'][0];
                        if ($responseFile['status'] == 'OK') {
                            if ($nfh == 0) //firma tradicional
                            {
                                $encondedFile = $responseFile['content'];

                                $decodedFile = base64_decode($encondedFile, true);
                                if (empty($encondedFile) || !base64_encode($decodedFile) === $encondedFile) {
                                    $comentario = "Error de codificación en archivo firmado.";
                                    throw new Exception($comentario);
                                }

                                $pdf = fopen(storage_path('app/public/files/' . $nNombreArchivoCargar), 'w+');
                                if (!$pdf) {
                                    $comentario = "No se pudo crear archivo firmado. ";
                                    throw new Exception($comentario);
                                }

                                fwrite($pdf, $decodedFile);
                                fclose($pdf);
                            }

                            if (!file_exists(storage_path('app/public/files/' . $nNombreArchivoCargar))) {
                                $comentario = "No se encuentra el archivo firmado. ";
                                throw new Exception($comentario);
                            }

                            //actualizar archivo firmado
                            if ($aDocumentoBuzon['id_tipo_archivo'] == 1) {
                                $docsPpales = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon_archivo.id_documento_buzon', '=', 'documento_buzon.id_documento_buzon')
                                    ->where('id_documento', $datos['id_documento'])
                                    ->where('id_tipo_archivo', 1)
                                    ->get();

                                foreach ($docsPpales as $archFile) {
                                    $nSalida = $archFile->version + 1;
                                    DocumentoBuzonArchivo::find($archFile->id_documento_buzon_archivo)->update(['version' => $nSalida]);
                                }

                                $documentoBuzonArchivo = DocumentoBuzonArchivo::create([
                                    //DocumentoBuzonArchivo::create([
                                    'id_documento_buzon' => $id_documento_buzon,
                                    'id_tipo_archivo' => 1,
                                    'nombre_archivo_original' => $sNombreArchivo,
                                    'nombre_archivo_codificado' => $nNombreArchivoCargar,
                                    'fecha' => $dFechaCreacion,
                                    'version' => 1
                                ]);

                                //actualizar año tramitacion del archivo
                                $datosDocumento = Documento::findOrFail($datos['id_documento']);
                                $datosDocumento->anio_tramitacion = date('Y');


                                //actualiza estado de tramitación
                                if ($cantidadFirmas == 0) {
                                    //cambiar estado a en proceso de firma (si es unica firma, marca como finalizado directamente)
                                    if ($nNroFirmas == 1)
                                        $datosDocumento->estado_tramitacion = 4;
                                    else
                                        $datosDocumento->estado_tramitacion = 3;
                                } elseif ($cantidadFirmas == ($nNroFirmas - 1)) {
                                    //ultima firma, pasar a finalizado
                                    $datosDocumento->estado_tramitacion = 4;
                                }

                                $datosDocumento->save();

                                //actualiza estado
                                DocumentoBuzon::find($id_documento_buzon)->update(['id_estado_documento' => 9]);



                                //registrar accion de firma en bitacora
                                $documentoBuzonBitacora = DocumentoBuzonBitacora::create([
                                    'id_documento_buzon' => $id_documento_buzon,
                                    'id_accion' => 7,
                                    'fecha' => $dFechaCreacion,
                                    'id_usuario' => $datos['id_usuario']
                                ]);

                                //registrar accion de cambio de archivo ppal en bitacora
                                $this->saveBitacora($id_documento_buzon, $dFechaCreacion, $datos['id_usuario'], "Cambio en archivo principal por firma electrónica.", 5);

                                //firma archivo nuevamente si folio en ultima firma                                 
                                if (($idTipoAsigFolio == 5) && $cantidadFirmas == ($nNroFirmas - 1)) {
                                    $datosDocumentos = Documento::findOrFail($datos['id_documento']);

                                    if (isset($datosDocumentos['folio']) && $datosDocumentos['folio'] != "") {
                                        //Generación imagen de firma 

                                        $datosJsonTipoDocumento = json_decode($datosDocumentos['json_tipo_documento'], true);
                                        $sPrefijoFolio = $datosJsonTipoDocumento['nombre_corto_firma'] . ' N° ' . $datosDocumentos['folio'] . ' / ' . date('Y');

                                        $aMeses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
                                        $fecha = date_create_from_format('Y-m-d H:i:s', $datosDocumentos['fecha']);
                                        $sfechaFolio = env('PLCSGD_FECHA_FOLIO_TXT') . ', ' . $fecha->format('d') . " de " . $aMeses[$fecha->format('n') - 1] . " del " . $fecha->format('Y');

                                        $img = Image::canvas(500, 100, '#FFFFFF');
                                        $img->text($sPrefijoFolio, 10, 45, function ($font) {
                                            $font->file(storage_path('../public/timesb.ttf'));
                                            $font->size(30);
                                            $font->align('left');
                                        });
                                        $img->text($sfechaFolio, 10, 85, function ($font) {
                                            $font->file(storage_path('../public/timesb.ttf'));
                                            $font->size(18);
                                            $font->align('left');
                                        });

                                        $img->save(storage_path('app/public/files/imagen_firma/' . $sNombreImgFolio));

                                        $nNombreArchivoCargarNew = $this->getNombreDocumento($datos['id_documento']);

                                        $aRespuestaFirma = $this->callHash(storage_path('app/public/files/' . $nNombreArchivoCargar), $layoutFolio, 1, $nNombreArchivoCargarNew, $nRutFirma);

                                        if (isset($aRespuestaFirma['status'])) {
                                            $comentario = $aRespuestaFirma['error'];
                                            throw new Exception($comentario);
                                        }
                                        //actualizar año tramitacion del archivo
                                        $datosDocumentos->anio_tramitacion = $fecha->format('Y');
                                        $datosDocumentos->save();

                                        //actualizar registro de archivo 
                                        DocumentoBuzonArchivo::find($documentoBuzonArchivo->id_documento_buzon_archivo)->update(['nombre_archivo_codificado' => $nNombreArchivoCargarNew]);
                                    } else {
                                        $comentario = "No fue posible generar el folio.";
                                        throw new Exception($comentario);
                                    }
                                }
                            }

                            //firma anexos asociados
                            $fAnexos = $this->firmaAnexos($datos['id_documento'], $cantidadFirmas, $sNombre, $aInfoUsuarios['img_firma'], $DatosFirma, $sNombreImgAnexo, $layoutAnexo, $nRutFirma);
                            
                            
                            //Log::error($fAnexos);

                            if (isset($fAnexos['status'])) {
                                if ($fAnexos['status'] == "400") {
                                    $comentario = "Firma Anexo: " . $fAnexos['error'];
                                    Log::error("Error Firma Anexo: " . $fAnexos['error']);
                                    throw new Exception($comentario);
                                }
                            }

                            /****** derivar luego de la primera y/o ultima firma */
                            $derivarPrimera = 0;
                            $derivarUltima  = 0;
                            $buzonPrimera = 0;
                            $buzonUltima = 0;

                            if (isset($datosJsonTipoDocumento['derivar_primera_firma'])) {
                                $derivarPrimera = intval($datosJsonTipoDocumento['derivar_primera_firma']);
                            }
                            if (isset($datosJsonTipoDocumento['derivar_ultima_firma'])) {
                                $derivarUltima = intval($datosJsonTipoDocumento['derivar_ultima_firma']);
                            }
                            if (isset($datosJsonTipoDocumento['buzon_primera_firma'])) {
                                $buzonPrimera = intval($datosJsonTipoDocumento['buzon_primera_firma']);
                            }
                            if (isset($datosJsonTipoDocumento['buzon_ultima_firma'])) {
                                $buzonUltima = intval($datosJsonTipoDocumento['buzon_ultima_firma']);
                            }

                            $firmasRealizadas = $cantidadFirmas;
                            $salida = "200";

                            if (($derivarPrimera == 1 && $firmasRealizadas == 0)) {
                                if ($nNroFirmas > 1) {
                                    $salida = $this->derivar_auto($buzonPrimera, $request->header('key'), $datos['id_documento'], $datos['id_documento_buzon'], $datos['id_usuario'], $datos['id_buzon'], 0);
                                } else {
                                    $salida = $this->derivar_auto($buzonPrimera, $request->header('key'), $datos['id_documento'], $datos['id_documento_buzon'], $datos['id_usuario'], $datos['id_buzon'], 1);
                                }
                            } else {
                                if ($derivarUltima == 1 && $firmasRealizadas == ($nNroFirmas - 1)) {
                                    $salida = $this->derivar_auto($buzonUltima, $request->header('key'), $datos['id_documento'], $datos['id_documento_buzon'], $datos['id_usuario'], $datos['id_buzon'], 1);
                                }
                            }

                            if ($salida != "200") {
                                $comentario = "No se pudo derivar automáticamente el documento despues de firmar.";
                                //Log::error("Dump Respuesta: " . $aRespuestaFirma); 
                                throw new Exception($comentario);
                            }
                            DB::commit();

                            //elimina imagen de firma 

                            $this->deleteImg($sNombreImg); //elimina imagen de firma   
                            $this->deleteImg($sNombreImgFolio); //imagen folio   

                            return $this->respondSuccess("Archivo firmado almacenado exitosamente.", 200);
                        }

                        //elimina imagen de firma 
                        $this->deleteImg($sNombreImg);
                    } else {
                        $comentario = "No se pudo procesar la respuesta.";
                        //Log::error("Dump Respuesta: " . $aRespuestaFirma); 
                        throw new Exception($comentario);
                    }
                }

                $comentario = "Error en el proceso de firma.";
                throw new Exception($comentario . " - " . $aRespuestaFirma['metadata']);
            } catch (Exception $e) {
                DB::rollBack();
                $msgError = "Error al Firmar:" . $e->getMessage();
                $this->saveBitacora($datos['id_documento_buzon'], $dFechaCreacion, $datos['id_usuario'], $msgError, 13);
                $this->deleteImg($sNombreImg);
                $this->deleteImg($sNombreImgAnexo); //elimina imagen anexo de firma
                $this->saveLog($datos['id_documento'], $e->getMessage());

                Log::error("IDDOC " . $datos['id_documento'] . " Error al generar la Firma Electrónica: " . $e->getMessage() . $e->getFile() . " " . $e->getLine() .$e->getTraceAsString());
                return $this->respondFail("Error " . $e->getMessage());
            }
        } else
            return $this->respondError('Json inválido', 406);
    }

    public function saveBitacora($docbuzon, $fecha, $usuario, $comentario, $accion)
    {
        //registrar accion en bitacora
        $documentoBuzonBitacora = DocumentoBuzonBitacora::create([
            'id_documento_buzon' => $docbuzon,
            'id_accion' => $accion,
            'fecha' => $fecha,
            'id_usuario' => $usuario,
            'mensaje_respuesta' => $comentario
        ]);
    }

    public function saveLog($documento, $comentario)
    {
        $documentoLog = FirmaLog::create([
            'id_documento' => $documento,
            'mensaje' => $comentario
        ]);
    }

    public function deleteImg($sImg)
    {
        //elimina imagen de firma
        // $filename = storage_path('app/public/files/imagen_firma/' . $sImg);
        // if (file_exists($filename))
        //     unlink($filename);
    }

    public function getNfirmas($idDoc){
        //acciones de firma en bitacora 
        $datosBitacora = DocumentoBuzonBitacora::join('documento_buzon', 'documento_buzon_bitacora.id_documento_buzon', '=', 'documento_buzon.id_documento_buzon')
            ->where('documento_buzon.id_documento', $idDoc)
            ->where('documento_buzon_bitacora.id_accion', 7)
            ->get();

        return count($datosBitacora);
    }
    
    public function anularFirma($id){
        $documentoBuzon = DocumentoBuzon::findOrFail($id);

    }

    public function callHash($sArchivo, $layout, $ultimaPag = 0, $nNombreArchivoFirmado, $nRut,$retry=0)
    {
        $classFilePath = storage_path('../app/Libraries/firmaHashV1.jar');
        $sPathArchivoFirmado = storage_path('app/public/files/' . $nNombreArchivoFirmado);

        if (!file_exists($classFilePath)) {
            Log::error("No se encuentra el archivo de firmaHashV1.jar");
            return array("status" => "400", "error" => "No se encuentra el archivo de firmaHashV1.jar");
        }

        if ($ultimaPag == 0)
            $nPagina = $layout['page'];
        else
            $nPagina = $ultimaPag;
        
        $cmd = "java -jar " . $classFilePath . " -a '" . env('PLCSGD_API_URL') . "' -e '" . env('PLCSGD_API_ENTITY') . "' -f '" . $sArchivo . "' -i " . $layout['filename'] . " -o " . $sPathArchivoFirmado . " -k " . env('PLCSGD_SECRETO') . " -p " . env('PLCSGD_API_PURPOSE') . " -r " . $nRut . " -t " . env('PLCSGD_API_TOKEN_KEY') . " -u '" . $layout['llx'] . "," . $layout['lly'] . "," . $layout['urx'] . "," . $layout['ury'] . "' -s " . $nPagina;
        $estado = shell_exec($cmd);
        Log::info("Firma DOC ".$sArchivo." por RUT ".$nRut." Estado :>" . var_dump($estado)."<");
        Log::info($cmd);
        if ($estado == null || $estado == '') //firma ok
        {
            Log::info("Firma Hash OK");
            $aSalida = array();
            if (!file_exists($sPathArchivoFirmado)) {
                
                //si no existe el archivo firmado, convertir el archivo a 1.4 y reintentar
                if ($retry ==0 ) {
                    $retry++;
                    Log::info("Reintentando firma anexo, intento: " . $retry);
                    //convertir a PDF 1.4
                    $sArchivo = $this->convertPDF(basename($sArchivo));
                    //$nArchivoParchado = storage_path('app/public/files/' . $sArchivo);
                    return $this->callHash( $sArchivo, $layout, $ultimaPag, $nNombreArchivoFirmado, $nRut, $retry);
                }else{
                    $comentario = "No se encuentra el archivo firmado";
                    $aSalida = array("status" => "400", "error" => $comentario);
                }

            } else {
                $aSalida = array("files" => array(array("content" => "", "status" => "OK")), "metadata" => array("filesSigned" => 1));
            }
        } else {
            $aSalida = array("status" => "400", "error" => "Excepción en comando de firma");
            Log::Info($cmd. " CON ERROR > salida " . var_dump($estado));
        }
        
        return $aSalida;
    }

    public function firmaAnexos($id_documento, $cantidadFirmas, $sNombre, $img_firma, $datosFirma, $sNombreImgAnexo, $layoutAnexo, $nRutFirma)
    {

        //solo primera y segunda firma - para firmar anexo
        if ($cantidadFirmas == 0 || $cantidadFirmas == 1) {
            //firma de anexo   
            if ($cantidadFirmas == 0) {
                $aDocumentoBuzonAnexo = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon_archivo.id_documento_buzon', '=', 'documento_buzon.id_documento_buzon')
                    ->join('documento', 'documento_buzon.id_documento', '=', 'documento.id_documento')
                    ->where('documento.id_documento', '=', $id_documento)
                    ->where('id_tipo_archivo', '=', '2')
                    ->whereIn('firma_anexo', array(1, 2))
                    ->whereNull('estado_firma_anexo')
                    ->select('id_documento_buzon_archivo', 'nombre_archivo_codificado', 'id_tipo_archivo','nombre_archivo_original')
                    ->get();

                $iEstadoFirma = 1;
            }

            if ($cantidadFirmas == 1) {
                $aDocumentoBuzonAnexo = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon_archivo.id_documento_buzon', '=', 'documento_buzon.id_documento_buzon')
                    ->join('documento', 'documento_buzon.id_documento', '=', 'documento.id_documento')
                    ->where('documento.id_documento', '=', $id_documento)
                    ->where('id_tipo_archivo', '=', '2')
                    ->where('firma_anexo', '=', '2')
                    ->where('estado_firma_anexo', '=', '1')
                    ->select('id_documento_buzon_archivo', 'nombre_archivo_codificado', 'id_tipo_archivo','nombre_archivo_original')
                    ->get();

                $iEstadoFirma = 2;
            }

            if (count($aDocumentoBuzonAnexo) > 0) {
                //genera imagen para firma anexo                                   

                $imgAnexo = Image::make(storage_path('app/public/files/imagen_firma/' . $img_firma));
                $dFechaCreacionImg = date('d.m.Y H:i:s');
                $imgAnexo->text('Firmado electrónicamente por:', 330, 60, function ($font) {
                    $font->file(storage_path('../public/calibri.ttf'));
                    $font->size(34);
                });
                $imgAnexo->text(Str::upper($sNombre), 330, 110, function ($font) {
                    $font->file(storage_path('../public/calibrib.ttf'));
                    $font->size(34);
                });

                $string = wordwrap($datosFirma['cargo_firma'], 35) . ' ' . $datosFirma['sigla'];
                $imgAnexo->text($string, 330, 235, function ($font) {
                    $font->file(storage_path('../public/calibri.ttf'));
                    $font->size(34);
                });

                $imgAnexo->save(storage_path('app/public/files/imagen_firma/' . $sNombreImgAnexo));

                $path_imagen_firma_anexo = storage_path('app/public/files/imagen_firma/' . $sNombreImgAnexo);

                if (!file_exists($path_imagen_firma_anexo)) {
                    $comentario = "Existe un problema con la imagen para anexo relacionada a la firma electrónica.";
                    $aSalida = array("status" => "400", "error" => $comentario);

                    return $aSalida;
                }

                foreach ($aDocumentoBuzonAnexo as $archAnexo) {
                    $nNombreArchivoCargarAnexo = "a_" . $archAnexo->nombre_archivo_codificado;

                    $sArchivoAnexo = storage_path('app/public/files/' . $archAnexo->nombre_archivo_codificado);
                    
                    //convertir a PDF 1.4
                    $sArchivoAnexo = $this->convertPDF($archAnexo->nombre_archivo_codificado);

                    //lamada por hash a anexos
                    //Obtiene pagina para agregar firma
                    $pdfPagesAnexo = file_get_contents($sArchivoAnexo);
                    $countAnexo = 0;
                    $countAnexo = preg_match_all("/\/Page\W/", $pdfPagesAnexo, $dummy);

                    if ($iEstadoFirma == 2)
                        $countAnexo -= 1;


                    $nSalidaHashAnexo = $this->callHash($sArchivoAnexo, $layoutAnexo, $countAnexo, $nNombreArchivoCargarAnexo, $nRutFirma);

                  

                    // if (!file_exists(storage_path('app/public/files/' . $nNombreArchivoCargarAnexo))) {
                    //     $comentario = "No se encuentra el archivo anexo firmado - " . $nNombreArchivoCargarAnexo;
                    //     $aSalida = array("status" => "400", "error" => $comentario);

                    //     return $aSalida;
                    // }

                    if (isset($nSalidaHashAnexo['status'])) {
                        $comentario = $nSalidaHashAnexo['error'];
                        $aSalida = array("status" => "400", "error" => $archAnexo->nombre_archivo_original." ".$comentario);
                        //throw new Exception($aSalida['error']);
                        return $aSalida;
                    }


                    //actualiza estado de firma
                    $regFirma = $archAnexo->id_documento_buzon_archivo;
                    DocumentoBuzonArchivo::find($regFirma)->update(['estado_firma_anexo' => $iEstadoFirma, 'nombre_archivo_codificado' => $nNombreArchivoCargarAnexo]);
                }

                //elimina imagen anexo de firma
                $this->deleteImg($sNombreImgAnexo);
            }
        }

        $aSalida = array("status" => "200", "error" => "");

        return $aSalida;
    }

    public function derivar_auto($buzonDestino, $sesionKey, $IDDocumento, $IDDocBuzon, $IDUsuario, $IDBuzon, $nUltima = 0)
    {
        try {

            DB::beginTransaction();
            $datosDocumento = Documento::findOrFail($IDDocumento);
            $datosDocumentoBuzon = DocumentoBuzon::findOrFail($IDDocBuzon);
            $opGuardar = 1;
            $nCarpeta = 2;
            $dFechaCreacion = date('Y-m-d H:i:s');

            //actualizar documento
            if ($datosDocumento->id_documento != '') {

                //si viene destinatario principal se agrega un registro
                $jsonAcciones = array();
                if ($nUltima == 1) {
                    $jsonAcciones[] = array("id_accion" => 11);
                } else {
                    $jsonAcciones[] = array("id_accion" => 7);
                }

                if ($buzonDestino != "") {
                    //verificar si se crea o actualiza   
                    if ($nCarpeta == 2) //recibidos 
                    {

                        $documentoBuzon = DocumentoBuzon::updateOrCreate([
                            'id_tipo_destino' => 1,
                            'id_documento' => $IDDocumento,
                            'id_documento_buzon_padre' => $IDDocBuzon,
                            'id_carpeta' => 1,
                            'id_estado_documento' => 4
                        ], [
                            'id_buzon' => $buzonDestino,
                            'id_documento' => $IDDocumento,
                            'id_estado_documento' => 4,
                            'fecha' => $dFechaCreacion,
                            'json_acciones' => json_encode($jsonAcciones),
                            'comentario_principal' => null,
                            'contestar_hasta' => null,
                            'notificado' => false,
                            'recibido' => false,
                            'favorito' => false
                        ]);
                        //return $IDDocumento."-".$IDDocBuzon."-".$buzonDestino;


                    }

                    //pendiente - crear orden 0 en flujo controlado/mixto
                }

                //si viene destinatario secundario se agrega registro

                //elimina archivos asociados
            }

            //derivar
            if ($nCarpeta == 2) //recibidos
            {
                //actualizar en documento el campo flujo_actual al valor siguiente en flujo controlado/mixto
                // y en buzones_flujo dejar en true en buzon ya procesado (recien enviado)

                $datosFlujoJson = Documento::findOrFail($IDDocumento);

                $datosJsonTipoDocumento = json_decode($datosFlujoJson['json_tipo_documento'], true);
                $nFlujoActual = $datosJsonTipoDocumento['flujo_actual'];

                $nNuevoFlujoActual = $nFlujoActual + 1;

                //ver segun tipo de flujo como será la derivación
                $nTipoFlujo = $datosJsonTipoDocumento['id_tipo_flujo'];

                foreach ($datosJsonTipoDocumento['buzones_flujo'] as $key => $valor) {
                    //buzon siguiente en el flujo
                    if (($nTipoFlujo == 2) && ($valor['orden'] == ($nFlujoActual + 1)) && ($buzonDestino == $valor['id_buzon'])) // && ($valor['procesado'] == false)
                    {
                    }

                    //buzon reinicio en el flujo
                    if (($nTipoFlujo == 2) && ($valor['orden'] == 1) && ($buzonDestino == $valor['id_buzon'])) // && ($valor['procesado'] == true)
                    {
                        $nNuevoFlujoActual = 1;
                    }

                    //buzon anterior en el flujo
                    if (($nTipoFlujo == 2) && ($valor['orden'] == ($nFlujoActual - 1)) && ($buzonDestino == $valor['id_buzon'])) // && ($valor['procesado'] == false)
                    {
                        $nNuevoFlujoActual = $nFlujoActual - 1;
                    }

                    if ($valor['orden'] == $nFlujoActual) {
                        $valor['procesado'] = true;
                        $datosJsonTipoDocumento['buzones_flujo'][$key] = $valor;
                    }
                }

                //actualiza el flujo actual
                $datosJsonTipoDocumento['flujo_actual'] = $nNuevoFlujoActual;

                $datosFlujoJson->update(['json_tipo_documento' => json_encode($datosJsonTipoDocumento)]);

                //agregar si estado actual es 11 dejar final como 12 y estado 9 dejar como 10
                //$estadoDocumentoFinal = 7;        
                $estadoDocumentoActual = array('4', '9', '11'); //"4,9,11"; deberia ir con whereIn  

                $datosUpdate = DocumentoBuzon::find($IDDocBuzon);

                switch ($datosUpdate->id_estado_documento) {
                    case (4):
                        $estadoDocumentoFinal = 7;
                        break;
                    case (9):
                        $estadoDocumentoFinal = 10;
                        break;
                    case (11):
                        $estadoDocumentoFinal = 12;
                        break;
                    default:
                        $estadoDocumentoFinal = 7;
                }

                $datosUpdate->id_estado_documento = $estadoDocumentoFinal;
                //cambia de carpeta a despachados
                $datosUpdate->id_carpeta = 3;

                $datosUpdate->save();
            }
            if (($buzonDestino != "" || $buzonDestino != null)) {
                //valida acciones
                $jsonAcciones = array();

                if ($nUltima == 1) {
                    $jsonAcciones[] = array("id_accion" => 11);
                } else {
                    $jsonAcciones[] = array("id_accion" => 7);
                }

                $datosDocumentoBuzonD1 = DocumentoBuzon::where('id_documento', $IDDocumento)
                    ->where('id_documento_buzon_padre', $IDDocBuzon)
                    ->where('id_tipo_destino', '1')
                    ->whereIn('id_estado_documento', $estadoDocumentoActual)
                    ->where('id_buzon', $buzonDestino)
                    ->select('id_documento_buzon')
                    ->first();
                $datosDocumentoBuzonD1->update(['id_estado_documento' => 3, 'fecha' => $dFechaCreacion]);


                //actualizar, se deja accion se hizo desde fuzon de firma con datos de envio 
                $documentoBuzonBitacoraD1 = DocumentoBuzonBitacora::create([
                    'id_documento_buzon' => $IDDocBuzon,
                    'id_accion' => 2,
                    'fecha' => $dFechaCreacion,
                    'id_usuario' => $IDUsuario,
                    'informacion_solicitud' => ["buzon_destino" => $buzonDestino,"id_tipo_destino"=>1,"mensaje" => "Der. Automática por Firma Electrónica"],
                ]);
            }
            DB::commit();
            return "200";
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return "500"; //$this->respondError('Falla al editar documento:' . $e->getMessage(), 400);
        }
    }


    //funcion para convertir documento a PDF 1.4
    function convertPDF($srcfile)
    {

        $sArchivo = storage_path('app/public/files/' . $srcfile);
        $filepdf = fopen($sArchivo, "r");
        if ($filepdf) {
            $line_first = fgets($filepdf);
            fclose($filepdf);
        } else {
            echo "error opening the file.";
        }
        // extract number such as 1.4,1.5 from first read line of pdf file
        preg_match_all('!\d+!', $line_first, $matches);

        // save that number in a variable
        $pdfversion = implode('.', $matches[0]);

        if ($pdfversion > "1.4") {
            $srcfile_new = substr($srcfile, 0, -4) . "_bkp.pdf";
            //$sArchivo = storage_path('app/public/files/' . $sNombreArchivo); //cambiar por linea sgte     
            if (!rename($sArchivo, storage_path('app/public/files/' . $srcfile_new))) {
                throw new Exception("Error al renombrar el archivo PDF Ver.".$pdfversion);
            }
            // USE GHOSTSCRIPT IF PDF VERSION ABOVE 1.4 AND SAVE ANY PDF TO VERSION 1.4 , SAVE NEW PDF OF 1.4 VERSION TO NEW PATH
            shell_exec('gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -sOutputFile="' . $sArchivo . '" "' . storage_path('app/public/files/' . $srcfile_new) . '"');
            Log::error('Comando : gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite  -dCompatibilityLevel=1.4 -sOutputFile="' . $sArchivo . '" "' . storage_path('app/public/files/' . $srcfile_new) . '"');
            //comprobar si existe archivo
            if (!file_exists($sArchivo)) {
                //rollback PDF
                if (!rename(storage_path('app/public/files/' . $srcfile_new), $sArchivo)) {
                    throw new Exception("Error al renombrar el archivo PDF Ver.".$pdfversion." a su nombre original");
                }

                throw new Exception("Error al convertir el archivo PDF Ver.".$pdfversion);
            }

        }
        return $sArchivo;
    }

    public function testPDF(){


    }

    /**
     * Firma un PDF arbitrario (módulo Solicitudes u otros) con FirmaGob.
     * Body JSON: id_usuario, pdf_base64, nombre_salida?, carpeta?
     */
    public function firmar_pdf(Request $request)
    {
        if (!$request->isJson()) {
            return response()->json(['error' => 'Se requiere JSON'], 400);
        }

        try {
            $datos = $request->json()->all();
            $idUsuario = $datos['id_usuario'] ?? null;
            $pdfBase64 = $datos['pdf_base64'] ?? null;
            $carpeta = $datos['carpeta'] ?? 'solicitudes';
            $nombreSalida = $datos['nombre_salida'] ?? ('firmado-' . time() . '.pdf');

            if (!$idUsuario || !$pdfBase64) {
                throw new Exception('id_usuario y pdf_base64 son obligatorios.');
            }

            $usuario = Users::where('id', $idUsuario)->first(['id', 'run', 'nombres', 'primer_apellido', 'segundo_apellido']);
            if (!$usuario) {
                throw new Exception('Usuario no encontrado.');
            }

            $runParts = explode('-', (string) $usuario->run);
            $runSinDv = preg_replace('/\D/', '', $runParts[0] ?? '');
            if ($runSinDv === '') {
                $runSinDv = (string) env('PLCSGD_RUT', '22222222');
            }

            $dir = storage_path('app/public/files/' . trim($carpeta, '/'));
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            $tmpName = 'tmp-' . $idUsuario . '-' . time() . '.pdf';
            $tmpPath = $dir . DIRECTORY_SEPARATOR . $tmpName;
            $pdfBinary = base64_decode($pdfBase64, true);
            if ($pdfBinary === false || $pdfBinary === '') {
                throw new Exception('pdf_base64 inválido.');
            }
            file_put_contents($tmpPath, $pdfBinary);

            $firmaDigitalConfig = array(
                'api'       => env('PLCSGD_API_URL'),
                'purpose'   => env('PLCSGD_API_PURPOSE'),
                'entity'    => env('PLCSGD_API_ENTITY'),
                'tokenKey'  => env('PLCSGD_API_TOKEN_KEY'),
                'secretKey' => env('PLCSGD_SECRETO')
            );
            $classFirma = new FirmaBase($firmaDigitalConfig);
            $classFirma->setRUN($runSinDv);
            $classFirma->addPDF($tmpPath, 'Solicitud firmada por ' . $usuario->nombres . ' ' . $usuario->primer_apellido, null);

            $salida = $classFirma->sign();
            @unlink($tmpPath);

            if ($salida->failed()) {
                Log::error('firmar_pdf FirmaGob error', ['status' => $salida->status(), 'body' => $salida->body()]);
                throw new Exception('FirmaGob rechazó la firma (HTTP ' . $salida->status() . ').');
            }

            $json = $salida->json();
            $content = $json['files'][0]['content'] ?? null;
            if (!$content) {
                // Sandbox a veces no devuelve content: devolver original
                if (env('PLCSGD_API_TOKEN_KEY') === 'sandbox') {
                    $outRel = trim($carpeta, '/') . '/' . basename($nombreSalida);
                    file_put_contents(storage_path('app/public/files/' . $outRel), $pdfBinary);
                    return response()->json([
                        'ok' => true,
                        'sandbox' => true,
                        'path' => $outRel,
                        'nombre' => basename($nombreSalida),
                        'pdf_base64' => base64_encode($pdfBinary),
                    ]);
                }
                throw new Exception('Respuesta FirmaGob sin content.');
            }

            $signed = base64_decode($content);
            $outRel = trim($carpeta, '/') . '/' . basename($nombreSalida);
            file_put_contents(storage_path('app/public/files/' . $outRel), $signed);

            return response()->json([
                'ok' => true,
                'path' => $outRel,
                'nombre' => basename($nombreSalida),
                'pdf_base64' => base64_encode($signed),
            ]);
        } catch (Exception $e) {
            Log::error('firmar_pdf: ' . $e->getMessage());
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 400);
        }
    }
}
