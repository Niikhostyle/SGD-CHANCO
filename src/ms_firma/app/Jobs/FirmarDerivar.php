<?php

namespace App\Jobs;

use App\Models\BuzonUsuario;
use Exception;
//use Illuminate\Bus\Queueable;
//use Illuminate\Contracts\Queue\ShouldBeUnique;
//use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
//use Illuminate\Queue\InteractsWithQueue;
//use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use RuntimeException;

use App\Models\DocumentoBuzon;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Auth;

class FirmarDerivar extends Job
{
    //use Dispatchable;
   // use InteractsWithQueue;
  //  use Queueable;
  //  use SerializesModels;

    protected $buzon;
    protected $documento;
    protected $sesion_key;
    protected $documento_buzon;
    protected $buzon_destino;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($buzon, $documento, $documento_buzon, $buzon_destino, $sesion_key, $user)
    {
        //
        $this->buzon = $buzon;
        $this->documento = $documento;
        $this->documento_buzon = $documento_buzon;
        $this->buzon_destino = $buzon_destino;
        $this->sesion_key = $sesion_key;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   

        $datosDocumento = DocumentoBuzon::findOrFail($this->documento)->first();
        $datosBuzon = DocumentoBuzon::findOrFail($this->buzon)->first();
        $datosBU = BuzonUsuario::findOrFail($this->buzon)->first();

        $jsonAcciones = array();                        
        $jsonAcciones[] = array("id_accion" => 7);

        $datosFea = Http::withHeaders(['key'=>$this->sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)        
        ->put(env('API_SGD_FIRMA','http://sgd_ms_firma:3333').'/api/sgd-firma/firmar_archivo', [  
            'id_documento_buzon'=>$this->documento_buzon,          
            'id_documento'=>$this->documento,
            'id_usuario'=>$this->user,
            'id_buzon'=>$this->buzon
        ]);     
                
        $datosFD = Http::withHeaders(['key'=>$this->sesion_key,'Content-Type'=>'application/json'])
        ->timeout(1000)               
        ->put(env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333').'/api/sgd-documentos/firmar_derivar', [
            'nombre_buzon'=>$datosBuzon->nombre_buzon,
            'nombre_corto_buzon'=>$datosBuzon->nombre_corto,
            'tipo_buzon'=>'2',
            'usuarios_asignados'=> null,
            'id_documento'=>$this->documento,
            'id_documento_buzon'=>$this->documento_buzon,
            'id_buzon'=>$this->buzon,
            'id_usuario'=>$this->user,
            'destinatarioPrincipal'=>$this->buzon_destino,
            'acciones_solicitadas'=>json_encode($jsonAcciones),
            'destinatarioOtros'=>null,
            'json_respuesta_a'=>$datosDocumento->json_respuesta_a,
            'id_tipo_destino'=>1,
            'carpeta'=>2,
            'titular'=> null,            
            'cargo_firma'=>$datosBuzon->cargo_firma,
            'restringir_sr' =>$datosBU->restringir_sr,
            'id_usuario_sr' => $datosBU->id_usuario_sr,
            'contestar_hasta' =>$datosBuzon->contestar_hasta
        ]); 

        
        if ($datosFea->failed()) {
            dump($datosFea->json()); 
            
            //si no se procesa el documento, se debe dejar en estado pendiente
            DocumentoBuzon::find($this->documento_buzon)->update(['id_estado_documento' => 4]);

            

        }
        else{ //derivar
        //     $datosDB = DocumentoBuzon::findOrFail($this->documento_buzon)->first();
        //     $datosDocumento = DocumentoBuzon::findOrFail($this->documento)->first();
        //     $datosBuzon = DocumentoBuzon::findOrFail($this->buzon)->first();
        //     $datosBU = BuzonUsuario::findOrFail($this->buzon)->first();

        //     $jsonAcciones = array();                        
        //     $jsonAcciones[] = array("id_accion" => 7);
            
        //     $datosFea = Http::withHeaders(['key'=>$this->sesion_key,'Content-Type'=>'application/json'])
        //     ->timeout(100)               
        //     ->put(env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333').'/api/sgd-documentos/firmar_derivar', [
        //         'nombre_buzon'=>$datosBuzon->nombre_buzon,
        //         'nombre_corto_buzon'=>$datosBuzon->nombre_corto,
        //         'tipo_buzon'=>'2',
        //         'usuarios_asignados'=> null,
        //         'id_documento'=>$this->documento,
        //         'id_documento_buzon'=>$this->documento_buzon,
        //         'id_buzon'=>$this->buzon,
        //         'id_usuario'=>$this->user,
        //         'destinatarioPrincipal'=>$this->buzon_destino,
        //         'acciones_solicitadas'=>json_encode($jsonAcciones),
        //         'destinatarioOtros'=>null,
        //         'json_respuesta_a'=>$datosDocumento->json_respuesta_a,
        //         'id_tipo_destino'=>1,
        //         'carpeta'=>2,
        //         'titular'=> null,            
        //         'cargo_firma'=>$datosBuzon->cargo_firma,
        //         'restringir_sr' =>$datosBU->restringir_sr,
        //         'id_usuario_sr' => $datosBU->id_usuario_sr
        //   ]); 
        }  
    }

    
}
