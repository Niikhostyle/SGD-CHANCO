<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\DocumentoBuzonArchivo;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use SebastianBergmann\Environment\Console;

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
                            $docsPpales = DocumentoBuzonArchivo::where('id_documento_buzon', $id_documento_buzon)
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

   /* public function destroy (DocumentoBuzonArchivo $file)
    {
        //elimina el registro de la carpeta local
        $url = str_replace('storage', 'public', $file->url);
        Storage::delete($url);
        
        //elimina el registro de la base de datos
        $file->delete();
        return redirect()->route('buscador.index');
    }*/
}