<?php

namespace App\Jobs;

use App\Models\Documento;
use App\Models\DocumentoBuzon;
use App\Models\DocumentoBuzonBitacora;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirmarDerivar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        // $datosFea = Http::withHeaders(['key'=>$this->sesion_key,'Content-Type'=>'application/json'])
        // ->timeout(30)        
        // ->put('http://sgd_ms_firma:3333/api/sgd-firma/firmar_archivo', [  
        //     'id_documento_buzon'=>$this->documento_buzon,          
        //     'id_documento'=>$this->documento,
        //     'id_usuario'=>$this->user,
        //     'id_buzon'=>$this->buzon
        // ]);

        // if ($datosFea->failed()) {
        //     dump($datosFea->json()); 
        //     //si no se procesa el documento, se debe dejar en estado pendiente
        //     DocumentoBuzon::find($this->documento_buzon)->update(['id_estado_documento' => 4]);

        // }
        // else{ //derivar
            try 
            {
                DB::beginTransaction();


                $datosFea = Http::withHeaders(['key'=>$this->sesion_key,'Content-Type'=>'application/json'])
                //->timeout(30)        
                ->put('http://sgd_ms_firma:3333/api/sgd-firma/firmar_archivo', [  
                    'id_documento_buzon'=>$this->documento_buzon,          
                    'id_documento'=>$this->documento,
                    'id_usuario'=>$this->user,
                    'id_buzon'=>$this->buzon
                ]);

                if($datosFea->successful()){
                    $dFechaCreacion = date('Y-m-d H:i:s');                
                    /****** actualizar ***/    
                    //1: actualiza documento
                    //2: crea o actualiza dest principal
                    //3: elimina dest secundario y crea nuevamente
                    
                    $datosDocumento = Documento::findOrFail($this->documento);
                    
    
                    if ($datosDocumento->id_documento != '')
                    {   
                        //si viene destinatario principal se agrega un registro
                        
                        $jsonAcciones = ['4','6','7','11'];                    
                        
    
                        if ($this->buzon_destino != "")
                        {
                            //verificar si se crea o actualiza   
        
                                $documentoBuzon = DocumentoBuzon::updateOrCreate([
                                    'id_documento' => $this->documento,
                                    'id_tipo_destino' => 1,
                                    'id_documento_buzon_padre' => $this->documento_buzon,
                                    'id_carpeta' => 1,
                                    'id_estado_documento' => 4
                                ],[
                                    'id_buzon' => $this->buzon_destino,                                
                                    'id_estado_documento' => 4,
                                    'fecha' => $dFechaCreacion,
                                    'json_acciones'=> json_encode($jsonAcciones),
                                    'notificado' => false,
                                    'recibido' => false,
                                    'favorito' => false    
                                ]);
    
    
                            //pendiente - crear orden 0 en flujo controlado/mixto
                        }
                    }

                    /****** derivar *****/
                    //actualizar en documento el campo flujo_actual al valor siguiente en flujo controlado/mixto
                    // y en buzones_flujo dejar en true en buzon ya procesado (recien enviado)

                    $datosFlujoJson = Documento::findOrFail($this->documento);

                    $datosJsonTipoDocumento = json_decode($datosFlujoJson['json_tipo_documento'],true);
                    $nFlujoActual = $datosJsonTipoDocumento['flujo_actual'];
                    
                    $nNuevoFlujoActual = $nFlujoActual + 1;

                    //ver segun tipo de flujo como será la derivación
                    $nTipoFlujo = $datosJsonTipoDocumento['id_tipo_flujo'];
                    
                    foreach ($datosJsonTipoDocumento['buzones_flujo'] as $key => $valor)
                    {                    
                        //buzon siguiente en el flujo
                        if (($nTipoFlujo == 2) && ($valor['orden'] == ($nFlujoActual + 1)) && ($this->buzon_destino == $valor['id_buzon']))// && ($valor['procesado'] == false)
                        {}

                        //buzon reinicio en el flujo
                        if (($nTipoFlujo == 2) && ($valor['orden'] == 1) && ($this->buzon_destino == $valor['id_buzon']))// && ($valor['procesado'] == true)
                        {
                            $nNuevoFlujoActual = 1;
                        }
                        
                        //buzon anterior en el flujo
                        if (($nTipoFlujo == 2) && ($valor['orden'] == ($nFlujoActual - 1)) && ($this->buzon_destino == $valor['id_buzon']))// && ($valor['procesado'] == false)
                        {
                            $nNuevoFlujoActual = $nFlujoActual - 1;
                        }

                        if ($valor['orden'] == $nFlujoActual)
                        {
                            $valor['procesado'] = true;
                            $datosJsonTipoDocumento['buzones_flujo'][$key] = $valor;
                        }
                    }

                    //actualiza el flujo actual
                    $datosJsonTipoDocumento['flujo_actual'] = $nNuevoFlujoActual;
                    
                    $datosFlujoJson->update(['json_tipo_documento' => json_encode($datosJsonTipoDocumento)]);                     
                    
                    //agregar si estado actual es 11 dejar final como 12 y estado 9 dejar como 10
                    //$estadoDocumentoFinal = 7;        
                    $estadoDocumentoActual = array('4','9','11'); //"4,9,11"; deberia ir con whereIn  
                    
                    $datosUpdate = DocumentoBuzon::find($this->documento_buzon);
                    
                    switch ($datosUpdate->id_estado_documento)
                    {                       
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
                    $datosUpdate->save();


                    if (($this->buzon_destino != "" ||$this->buzon_destino != null) ){

                        $datosDocumentoBuzonD1 = DocumentoBuzon::where('id_documento', $this->documento)
                                        ->where('id_documento_buzon_padre', $this->documento_buzon)
                                        ->where('id_tipo_destino', '1')
                                        ->whereIn('id_estado_documento', $estadoDocumentoActual)
                                        ->where('id_buzon', $this->buzon_destino)
                                        ->select('id_documento_buzon')                                
                                        ->first();

                            $datosDocumentoBuzonD1->update(['id_estado_documento' => 3, 'fecha' => $dFechaCreacion]);
                  
                            $documentoBuzonBitacoraD1 = DocumentoBuzonBitacora::create([
                                'id_documento_buzon' => $datosDocumentoBuzonD1["id_documento_buzon"],
                                'id_accion' => 2,
                                'fecha' => $dFechaCreacion,
                                'id_usuario' => $this->user
                            ]);   
                    }
                    // }
                    
                }
                else{
                    dump($datosFea->json()); 
                    //si no se procesa el documento, se debe dejar en estado pendiente
                    DocumentoBuzon::find($this->documento_buzon)->update(['id_estado_documento' => 4]);
                }
                DB::commit();

            } catch (ModelNotFoundException $e) {
                DB::rollBack();
            }        
        }    
    //}
}
