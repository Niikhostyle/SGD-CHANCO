<?php
namespace App\Http\Controllers;

use App\Models\TipoDocumento;
use App\Models\TipoDocumentoBuzon;
use App\Models\TipoDocumentoBuzonAccion;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Validator\TipoDocValidator;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\Hash;

class TipoDocumentoController extends Controller{
    
    private $validator;

    public function __construct(TipoDocValidator $TipoDocValidator)
    {
        $this->validator = $TipoDocValidator;
    }
 
    public function ver_todos(Request $request)
    {
        if($request->isJson())
        {
            try 
            {
                $datosTipoDoc = TipoDocumento::all('id_tipo_documento','nombre','id_tipo_origen','id_tipo_flujo');
          
                return $this->respondSuccess($datosTipoDoc, 200);
            }  
            catch (ModelNotFoundException $e) 
            {
                return $this->respondError('No se encontraron usuarios', 500);
            } 
        }
        else 
            return $this->respondError('Json inválido', 406);
    }

    public function ver(Request $request)
    {
        if($request->isJson())
        {
            try 
            {
                $datosRequest = $request->json()->all();
                
                $validator = $this->validator->validateField($datosRequest);
                if ($validator->fails())
                    return $this->respondFail('Falla al obtener tipo de documento: revisar datos de entrada');

                $datosTipoDoc = TipoDocumento::findOrFail($datosRequest['id_tipo_documento']);

                $tipoDocBuzon = $datosTipoDoc->buzones_flujo;

                foreach ($tipoDocBuzon as $tbuzon) 
                {
                    $tbuzon->acciones;
                }
                
                return $this->respondSuccess($datosTipoDoc, 200);
            }  
            catch (ModelNotFoundException $e) 
            {
                return $this->respondError('Tipo de Documento no existe', 500);
            } 
        }
        else 
            return $this->respondError('Json inválido', 406);
    }  

    public function crear(Request $request)
    {
        if($request->isJson())
        {
            try
            {
                DB::beginTransaction();

                $datosdatosTipoDoc = $request->json()->all();  

                $validator = $this->validator->validateInsert();

                if ($validator->fails())
                    return $this->respondFail('No es posible crear el tipo documento: revisar datos de entrada');

                $tipo_documento = TipoDocumento::create($datosdatosTipoDoc);                 
                
                $datosBuzonesFlujo = $datosdatosTipoDoc['buzones_flujo']; 
                
                if ($datosBuzonesFlujo != null) //agregar a ms
                {
                    foreach ($datosBuzonesFlujo as $datos) 
                    {   
                        $tipoDocBuzon = TipoDocumentoBuzon::create([
                            'id_tipo_documento' => $tipo_documento->id_tipo_documento,
                            'id_buzon' => $datos['id_buzon'],
                            'orden' => $datos['orden']
                        ]);  
                        
                        $datosAccion = $datos['acciones']; 

                        foreach ($datosAccion as $accion)
                        {
                            $tipoDocBuzonAccion = TipoDocumentoBuzonAccion::create([
                                'id_tipo_documento_buzon' => $tipoDocBuzon->id_tipo_documento_buzon,
                                'id_accion' => $accion['id_accion']
                            ]);
                        }
                        
                    }  
                }

                DB::commit();

                return $this->respondSuccess("Tipo de Documento creado", 201);
                
            }
            catch (ModelNotFoundException $e) 
            {
                DB::rollBack();

                return $this->respondError('Falla al crear tipo documento:' . $e->getMessage(), 500);
            }
        }
        else
            return $this->respondError('Json inválido', 406);
        
    }

    public function actualizar(Request $request)
    {
        if ($request->isJson()) 
        {          
            try {

                DB::beginTransaction();

                $datosRequest = $request->json()->all();
                
                $validator = $this->validator->validateUpdate();

                if ($validator->fails())
                    return $this->respondFail('Falla al actualizar tipo de documento: revisar datos de entrada');

                $datosTipoDoc = TipoDocumento::findOrFail($datosRequest['id_tipo_documento']);
                $datosTipoDoc->update($datosRequest);

                //agregar a ms
                //actualizar en flujos buzones

                $aTipoDocBuzon = array();
                $aTipoDocBuzonAccion = array();

                $datosBuzonesFlujo = $datosRequest['buzones_flujo']; 

                if ($datosBuzonesFlujo != null)
                {
                    foreach ($datosBuzonesFlujo as $datos) 
                    {   
                        
                        if ($datos['id_tipo_documento_buzon'] != null)
                        {
                            $tipoDocBuzon = TipoDocumentoBuzon::findOrFail($datos['id_tipo_documento_buzon']);
                            $tipoDocBuzon->id_buzon = $datos['id_buzon'];
                            $tipoDocBuzon->orden = $datos['orden'];

                            $tipoDocBuzon->save();

                        }    
                        else{
                            $tipoDocBuzon = TipoDocumentoBuzon::create([
                                'id_tipo_documento' => $datosRequest['id_tipo_documento'],
                                'id_buzon' => $datos['id_buzon'],
                                'orden' => $datos['orden']
                            ]); 

                            $datos['id_tipo_documento_buzon'] = $tipoDocBuzon->id_tipo_documento_buzon;

                           
                        } 

                        $aTipoDocBuzon[] = $datos['id_tipo_documento_buzon'];

                        //actualizar accciones

                        $datosAccion = $datos['acciones']; 

                        foreach ($datosAccion as $accion)
                        {
                            //busca y crea sino encuentra por tipo de doc y accion
                            $buzon_accion = TipoDocumentoBuzonAccion::firstOrCreate([
                                'id_tipo_documento_buzon' => $datos['id_tipo_documento_buzon'],
                                'id_accion' => $accion['id_accion']
                            ]);
                            
                            $aTipoDocBuzonAccion[] = $accion['id_accion'];
                        }

                        //eliminacion de acciones para el tipo de doc
                        
                        $listAcciones = TipoDocumentoBuzonAccion::where('id_tipo_documento_buzon', $datos['id_tipo_documento_buzon'])->get(); 

                        foreach ($listAcciones as $nAccion)
                        {
                            if (!in_array($nAccion['id_accion'], $aTipoDocBuzonAccion))
                                TipoDocumentoBuzonAccion::destroy($nAccion['id_tipo_documento_buzon_accion']);
                        }

                        unset($aTipoDocBuzonAccion);

                    }  

                    //eliminacion de tipo de doc
                        
                    $listTipoDoc = TipoDocumentoBuzon::where('id_tipo_documento', $datosRequest['id_tipo_documento'])->get(); 

                    foreach ($listTipoDoc as $nTipo)
                    {
                        if (!in_array($nTipo['id_tipo_documento_buzon'], $aTipoDocBuzon))
                        {
                            //eliminar en tipos acciones y tipos buzon
                            TipoDocumentoBuzon::findOrFail($nTipo['id_tipo_documento_buzon'])->acciones()->delete();
                            TipoDocumentoBuzon::destroy($nTipo['id_tipo_documento_buzon']);
                        }
                    }

                    unset($aTipoDocBuzon);
                }

                DB::commit();   

                return $this->respondSuccess('Tipo de Documento actualizado', 200);                

            } catch (ModelNotFoundException $e) {
                DB::rollBack();

                return $this->respondError('Falla al actualizar Tipo Documento:' . $e->getMessage(), 500);                
            }            
        }
        else
            return $this->respondError('Json inválido', 406);

    }

    function eliminar(Request $request)
    {
        if ($request->isJson()) 
        {
            try 
            {
                DB::beginTransaction();

                $datosRequest = $request->json()->all();

                $validator = $this->validator->validateField($datosRequest);
                if ($validator->fails())
                    return $this->respondFail('Falla al eliminar tipo de documento: revisar datos de entrada');

                $datosTipoDoc = TipoDocumento::findOrFail($datosRequest['id_tipo_documento']);
                
                if (count($datosTipoDoc->documento) == 0 && count($datosTipoDoc->documento_buzon_folio) == 0)
                {
                    $listTipoDoc = TipoDocumentoBuzon::where('id_tipo_documento', $datosRequest['id_tipo_documento'])->get(); 
                    
                    foreach ($listTipoDoc as $nTipo)
                    {                        
                        //eliminar en tipos acciones y tipos buzon
                        TipoDocumentoBuzon::findOrFail($nTipo['id_tipo_documento_buzon'])->acciones()->delete();
                        TipoDocumentoBuzon::destroy($nTipo['id_tipo_documento_buzon']);
                    }

                    $datosTipoDoc->delete();
                }
                else
                    return $this->respondError('Falla al eliminar tipo de documento: existen datos relacionados', 500);         

                DB::commit();

                return $this->respondSuccess('Tipo de documento eliminado con éxito', 200);         
            } 
            catch (ModelNotFoundException $e) 
            {
                DB::rollBack();

                return $this->respondError('Falla al eliminar tipo de documento:' . $e->getMessage(), 500);  
            }
        } 
        else
            return $this->respondError('Json inválido', 406);

    }
}