<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use RuntimeException;

use Illuminate\Support\Facades\Http;

class Firma implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
        ->put('http://sgd_ms_firma:3333/api/sgd-firma/firmar_archivo', [  
            'id_documento_buzon'=>$this->documento_buzon,          
            'id_documento'=>$this->documento,
            'id_usuario'=>$this->user,
            'id_buzon'=>$this->buzon
        ]);

        dump("Enviado a Firma :: ".$this->buzon." :: ".$this->documento);
        
        /*
        $datosArchivo = Http::withHeaders(['key'=>$this->sesion_key,'Content-Type'=>'application/json']) //
        ->timeout(30)        
        ->put('http://sgd_ms_archivos:3333/api/sgd-archivos/generar_archivo_pdf', [            
            'id_documento'=>270,
            'id_documento_buzon'=>532,
            'id_usuario'=>10,
        ]);

        if ($datosArchivo->json()) {

            //dump($datosArchivo->json());
            $aSalida = $datosArchivo->json();
            dump($aSalida['data']['comentario']);

            if ($aSalida['status'] == 400)
                throw new Exception($aSalida['data']['comentario']);            

        }
        */
    }

    public function failed(Exception $exception)
    {
        // Create log file

        //info($exception);
    }
}
