<?php

namespace App\Jobs;

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

class Firma extends Job
{
    //use Dispatchable;
   // use InteractsWithQueue;
  //  use Queueable;
  //  use SerializesModels;

    protected $buzon;
    protected $documento;
    protected $sesion_key;
    protected $documento_buzon;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($buzon, $documento, $documento_buzon, $sesion_key, $user)
    {
        //
        $this->buzon = $buzon;
        $this->documento = $documento;
        $this->documento_buzon = $documento_buzon;
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
        $datosFea = Http::withHeaders(['key'=>$this->sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)        
        ->put(env('API_SGD_FIRMA','http://sgd_ms_firma:3333').'/api/sgd-firma/firmar_archivo', [  
            'id_documento_buzon'=>$this->documento_buzon,          
            'id_documento'=>$this->documento,
            'id_usuario'=>$this->user,
            'id_buzon'=>$this->buzon
        ]);     

        $datosFD = Http::withHeaders(['key'=>$this->sesion_key,'Content-Type'=>'application/json'])        
        ->put(env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333').'/api/sgd-documentos/firmar_derivar', [
            "nombre_buzon"=>"buzon publico",
            "nombre_corto_buzon"=>"bp",
            "tipo_buzon"=>"2",
            "usuarios_asignados"=> null,
            "id_documento"=>415,
            "id_documento_buzon"=>1011,
            "id_buzon"=>3,
            "id_usuario"=>1,
            "destinatarioPrincipal"=>1,
            "acciones_solicitadas"=>null,
            "destinatarioOtros"=>null,
            "json_respuesta_a"=>null,
            "id_tipo_destino"=>1,
            "carpeta"=>2,
            "titular"=> null,            
            "cargo_firma"=>"bpublico",
            "restringir_sr" =>0,
            "id_usuario_sr" => 0,
            "contestar_hasta"=>null
        ]); 
        
        if ($datosFea->failed()) {
            dump($datosFea->json()); 
            
            //si no se procesa el documento, se debe dejar en estado pendiente
            DocumentoBuzon::find($this->documento_buzon)->update(['id_estado_documento' => 4]);
        }

        /*
        if ($datosArchivo->json()) {

            //dump($datosArchivo->json());
            $aSalida = $datosArchivo->json();
            dump($aSalida['data']['comentario']);

            if ($aSalida['status'] == 400)
                throw new Exception($aSalida['data']['comentario']);            

        }
        */
    }

    
}
