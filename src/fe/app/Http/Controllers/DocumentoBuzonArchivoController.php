<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\DocumentoBuzonArchivo;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use SebastianBergmann\Environment\Console;

class DocumentoBuzonArchivoController extends Controller
{
    public function store (Request $request)
    {  
        try 
            {
                DB::beginTransaction();

                $id_documento_buzon = $request->id_documento_buzon;
                $id_tipo_archivo = $request->id_tipo_archivo;
                
                $numero = rand(100000,9999999);
                $path = 'public/imagenes';
                $files = $request->file('file');
                foreach($files as $file){
                    $fileName = $file->getClientOriginalName();
                    $file->move(storage_path('app/public/imagenes'), $fileName);
                
                    DocumentoBuzonArchivo::create([
                        //'url' => storage_path('app/public/imagenes'),
                        'id_documento_buzon' => $id_documento_buzon,
                        'id_tipo_archivo' => $id_tipo_archivo,
                        'nombre_archivo_original' => $fileName,
                        'nombre_archivo_codificado' => $fileName . '-' . $numero 
                    ]);
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
                        'comentario' => 'Error al guardar documentos'
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