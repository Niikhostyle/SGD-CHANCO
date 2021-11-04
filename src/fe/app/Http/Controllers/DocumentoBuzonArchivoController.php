<?php

namespace App\Http\Controllers;

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
        
        $numero = rand(100000,9999999);
        $imagenes = $request->file('file')->store('public/imagenes');
        $nombre = $request->file('file')->getClientOriginalName();

        $url = Storage::url($imagenes);
        
        $id_documento_buzon = $request->id_documento_buzon;
        $id_tipo_archivo = $request->id_tipo_archivo;
        
        DocumentoBuzonArchivo::create([
            //'url' => $url,
            'id_documento_buzon' => $id_documento_buzon,
            'id_tipo_archivo' => $id_tipo_archivo,
            'nombre_archivo_original' => $nombre,
            'nombre_archivo_codificado' => $nombre . '-' . $numero 
        ]);
        
    }


    public function destroy (DocumentoBuzonArchivo $file)
    {
        
        $url = str_replace('storage', 'public', $file->url);
        Storage::delete($url);
        
        $file->delete();
        return redirect()->route('buscador.index');
    }
}