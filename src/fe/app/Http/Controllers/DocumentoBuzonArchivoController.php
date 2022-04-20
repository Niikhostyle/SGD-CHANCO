<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\DocumentoBuzonArchivo;
use App\Models\DocumentoBuzonArchivoDescarga;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use SebastianBergmann\Environment\Console;
use PDF;
use Illuminate\Support\Facades\View;

//use Image;
use Intervention\Image\ImageManagerStatic as Image;

class DocumentoBuzonArchivoController extends Controller
{
    public function store (Request $request)
    {  
        
        try 
            {
                DB::beginTransaction();
                
                $id_documento = $request->id_documento;
                $id_documento_buzon = $request->id_documento_buzon;
                $id_tipo_archivo = $request->id_tipo_archivo;

                //agregar version

                $dFechaCreacion = date('Y-m-d H:i:s');
                $files = $request->file('file');

                foreach($files as $file)
                {
                    $fileName = $file->getClientOriginalName();
                    $nNombreArchivoCargar = $this->getNombreDocumento($request->id_documento);
                    $nVersion = null;
                    
                    $uploadSuccess = $file->move(storage_path('app/public/files'), $nNombreArchivoCargar);



                    if ($uploadSuccess)
                    {
                        if(strlen($fileName) && $id_tipo_archivo == 1)
                        {
                            /*$docsPpales = DocumentoBuzonArchivo::where('id_documento_buzon', $id_documento_buzon)
                                                    ->where('id_tipo_archivo', 1)
                                                    ->get();
                            */

                            $docsPpales = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon_archivo.id_documento_buzon','=','documento_buzon.id_documento_buzon')
                                                        ->where('id_documento', $id_documento)
                                                        ->where('id_tipo_archivo', 1)
                                                        ->get();

                            foreach ($docsPpales as $archFile)
                            {
                                $nSalida = $archFile->version + 1;
                                DocumentoBuzonArchivo::find($archFile->id_documento_buzon_archivo)->update(['version' => $nSalida]);
                            }

                            if ($id_tipo_archivo == 1)
                                $nVersion = 1;
                        }

                        DocumentoBuzonArchivo::create([
                            'id_documento_buzon' => $id_documento_buzon,
                            'id_tipo_archivo' => $id_tipo_archivo,
                            'nombre_archivo_original' => $fileName,
                            'nombre_archivo_codificado' => $nNombreArchivoCargar,
                            'fecha' => $dFechaCreacion,
                            'version' => $nVersion
                        ]);
                    }
                    else
                    {
                        return response()->json([
                            'status' => 500, 
                            'data' => [
                                'comentario' => 'Error al guardar documento'
                        ]], 500);
                    }
                }

                DB::commit();

                return response()->json([
                    'status' => 200
                ], 200);

            }
            catch (ModelNotFoundException $e) {
                DB::rollBack();

                return response()->json([
                    'status' => 500, 
                    'data' => [
                        'comentario' => 'Error al guardar documento'
                ]], 500);
            }
    
    }

    public function ver(Request $request)
    {
        $datosDocumentos = DocumentoBuzonArchivo::where('id_documento_buzon', $request['id_documento_buzon'])
                                                ->where('id_tipo_archivo', 2)
                                                ->select('nombre_archivo_original')
                                                ->get();                                           

        return response()->json($datosDocumentos);
    }

    public function show($filename)
    {
       /**
        *$path = storage_path(config('app.path_files')) . $filename;//config('app.path_files')
 
       * if (!File::exists($path)) {
       *     abort(404);
       * }

       * $file = File::get($path);
        *$type = File::mimeType($path);
        *
        *$response = Response::make($file, 200);
        *$response->header("Content-Type", $type);
         */
        

        $datos =  DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon.id_documento_buzon', '=', 'documento_buzon_archivo.id_documento_buzon')
                                       
                                        ->join('buzon', 'documento_buzon.id_buzon', '=', 'buzon.id_buzon')
                                        ->join('buzon_usuario', 'buzon.id_buzon', '=', 'buzon_usuario.id_buzon')

                                        //->where('id_documento', $nDocumento)
                                        ->where('id_tipo_archivo', 1)
                                        ->where('version', 1)
                                        ->where('nombre_archivo_codificado', $filename)
                                        ->select(
                                            'nombre_archivo_codificado',
                                            'buzon_usuario.id_usuario as id_usuario',
                                            'id_documento'
                                            
                                           
                                        )
                                        ->get();


          
        //$nombre = $datos->nombre_archivo_codificado;  
        //$buzonUsuario = $datos['id_usuario'];  
        $valido = false;
        $buzonUsuario = [];    
        //return $datos;
        foreach ($datos as $data){
            $nombre = $data->nombre_archivo_codificado;                              
            //$buzonUsuario = $data->id_usuario;
            $nDocumento =  $data->id_documento;
            //tomo los id de usuarios de los buzones y verifico que el usuario logeado este dentro de los id que tiene el buzon/ porque el buzon en el que esta el documento tiene distintos usuarios
            for($i=0; $i<count($datos); $i++){
                $buzonUsuario[$i] = $data->id_usuario;
            }
        }

        $nivelAcceso = Documento::where('id_documento', $nDocumento)->select('id_nivel_acceso')->get();
        $usuario = Auth::user()->id; 
        

        foreach ($nivelAcceso as $data){
            $nAcceso = $data->id_nivel_acceso;                                
        }

        if($nAcceso=='1' ){
            //cuando le entreguen la url el usuario debera logearse y se tendra que verificar que tenga accesos a ese documento  
            $path = storage_path(config('app.path_files')) . $filename;//config('app.path_files')
 
            if (!File::exists($path)) {
                abort(404);
            }

            $file = File::get($path);
            $type = File::mimeType($path);
            
            $response = Response::make($file, 200);
            $response->header("Content-Type", $type);

            return $response;
        }
        if($nAcceso=='2' || $nAcceso=='3'){
            

            for($i=0; $i<count($buzonUsuario); $i++){
                
                if ($buzonUsuario[$i]==$usuario){
                    $valido = true;
                    $path = storage_path(config('app.path_files')) . $filename;//config('app.path_files')
     
                    if (!File::exists($path)) {
                        abort(404);
                    }
    
                    $file = File::get($path);
                    $type = File::mimeType($path);
                    
                    $response = Response::make($file, 200);
                    $response->header("Content-Type", $type);
    
                    return $response;
                }
            }
            if($valido==false){
                 //cuando no tiene acceso al documento
                 return View::make('pruebaPublica');
            }
            
        }  
        //return $response;
    }

    public function showImage($routefilename)
    {       
        
        $path = storage_path(config('app.path_files')) . 'editor/images/' . $routefilename;

        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);
        
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function showImageFirma($routefilename)
    {       
        $path = storage_path(config('app.path_img_firma')) . $routefilename;

        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);
        
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    /* public function destroy (DocumentoBuzonArchivo $file)
    {
        //elimina el registro de la carpeta local
        $url = str_replace('storage', 'public', $file->url);
        Storage::delete($url);
        
        //elimina el registro de la base de datos
        $file->delete();
        return redirect()->route('buscador.index');
    }*/

    public function validarUrl ($id){
        
        
        $nDocumento =  $id;

        $archivo = DocumentoBuzonArchivo::join('documento_buzon', 'documento_buzon.id_documento_buzon', '=', 'documento_buzon_archivo.id_documento_buzon')
                                        ->join('buzon', 'documento_buzon.id_buzon', '=', 'buzon.id_buzon')
                                        ->join('buzon_usuario', 'buzon.id_buzon', '=', 'buzon_usuario.id_buzon')
                                        ->where('id_documento', $nDocumento)
                                        ->where('id_tipo_archivo', 1)
                                        ->where('version', 1)
                                        ->select(
                                            'nombre_archivo_codificado',
                                            'id_documento_buzon_archivo',
                                            'buzon_usuario.id_buzon_usuario as id_buzon_usuario'

                                        )
                                        ->get();   
                                     
        $dFechaCreacion = date('Y-m-d H:i:s'); 
        foreach ($archivo as $file)
            $nombre = $file->nombre_archivo_codificado;                              
            DocumentoBuzonArchivoDescarga::create([
                'id_documento_buzon_archivo' => $file['id_documento_buzon_archivo'],
                'id_buzon_usuario' =>  $file['id_buzon_usuario'],
                'fecha' => $dFechaCreacion 
            ]); 
        
        $path = storage_path(config('app.path_files')) . $nombre ;

        return response()->download($path);
            
    }

    public function pruebaPublica (){

        return View::make('pruebaPublica');
    }
}