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


class FirmarDerivar extends Job
{

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

    public function __construct($buzon, $documento, $documento_buzon,$buzon_destino, $sesion_key, $user)
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
                        
        
        $datosFea = Http::withHeaders(['key'=>$this->sesion_key,'Content-Type'=>'application/json'])
        ->timeout(30)        
        ->put('http://sgd_ms_firma:3333/api/sgd-firma/firmar_archivo', [  
            'id_documento_buzon'=>$this->documento_buzon,          
            'id_documento'=>$this->documento,
            'id_usuario'=>$this->user,
            'id_buzon'=>$this->buzon
        ]);

        if ($datosFea->failed()) {
            dump($datosFea->json()); 
            //si no se procesa el documento, se debe dejar en estado pendiente
            
            DocumentoBuzon::find($this->documento_buzon)->update(['id_estado_documento' => 4]);

        }
        else{
            //obtener datos para guardar
           /* buzon:hiddIdBuzon,
                                                destinatarioPrincipal:destinatarioPrincipal,
                                                destinatarioOtros:otrosDestinatarios,
                                                comentarioPrincipal:comentarioPrincipal,
                                                comentarioOtros:comentarioOtros,
                                                acciones_solicitadas:acciones_solicitadas,
                                                hiddIdDocumento:hiddIdDocumento,
                                                hiddIdDocumentoBuzon:hiddIdDocumentoBuzon,
                                                carpeta:2,
                                                opcionGuardar:1,
                                                id_tipo_destino:tipoDestino 
                                                */
            
        }

    }

    
}
