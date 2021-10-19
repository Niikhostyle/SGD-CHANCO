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

                DB::commit();

                return $this->respondSuccess($tipo_documento, 201);
                
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

                //actualizar en flujos buzones
/*
                $datosBuzonesFlujo = $datosRequest['buzones_flujo']; 
               
                foreach ($datosBuzonesFlujo as $datos) 
                {   
                    $nOpcion = TipoDocumentoBuzon::updateOrCreate(
                        ['id_tipo_documento_buzon' => $datos['id_tipo_documento_buzon']],
                        ['id_buzon' => $datos['id_buzon'], 'orden' => $datos['orden']]
                    );                   
                    
                    
                }  

                $datosTipoDoc->buzones_flujo;
*/
                //actualizar accciones

                

               /* 
                $datosTipoDoc->nombre = $datosRequest['nombre'];
                $datosTipoDoc->nombre_corto = $datosRequest['nombre_corto'];
                $datosTipoDoc->descripcion = $datosRequest['descripcion'];
                $datosTipoDoc->id_tipo_origen = $datosRequest['id_tipo_origen'];
                $datosTipoDoc->id_tipo_flujo = $datosRequest['id_tipo_flujo'];
                $datosTipoDoc->id_tipo_folio = $datosRequest['id_tipo_folio'];
                $datosTipoDoc->id_tipo_avance = $datosRequest['id_tipo_avance'];
                $datosTipoDoc->id_tipo_asignacion_folio = $datosRequest['id_tipo_asignacion_folio'];
                $datosTipoDoc->requiere_fe = $datosRequest['requiere_fe'];
                $datosTipoDoc->plantilla_encabezado = $datosRequest['plantilla_encabezado'];
                $datosTipoDoc->plantilla_cuerpo = $datosRequest['plantilla_cuerpo'];

                $datosTipoDoc->save();
*/
                DB::commit();   

                return $this->respondSuccess($datosTipoDoc, 200);                

            } catch (ModelNotFoundException $e) {
                DB::rollBack();

                return $this->respondError('Falla al actualizar Tipo Documento:' . $e->getMessage(), 500);                
            }            
        }
        else
            return $this->respondError('Json inválido', 406);

    }

 


}