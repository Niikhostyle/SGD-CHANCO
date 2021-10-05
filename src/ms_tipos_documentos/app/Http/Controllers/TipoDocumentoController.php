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
    
/*    private $validator;

    public function __construct(TipoDocValidator $TipoDocValidator)
    {
        $this->validator = $TipoDocValidator;
    }
 */
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

    public function crear(Request $request)
    {
        if($request->isJson())
        {
            try
            {
                DB::beginTransaction();

                $datosdatosTipoDoc = $request->json()->all();                
                
                //$validator = $this->validator->validateInsert();

                //if ($validator->fails())
                //    return $this->respondFail('No es posible crear el tipo documento: revisar datos de entrada');

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
/*
    public function ver(Request $request)
    {
        if($request->isJson())
        {
            try 
            {
                $datosRequest = $request->json()->all();
                
                $validator = $this->validator->validateFieldUser($datosRequest);
                if ($validator->fails())
                    return $this->respondFail('Falla al obtener usuario: revisar datos de entrada');

                $datosUsuario = Users::findOrFail($datosRequest['id_usuario'],['id', 'run', 'nombres', 'primer_apellido', 'segundo_apellido', 'email', 'aplica_fea', 'genera_pdf', 'id_estado_usuario', 'id_perfil']);
                
                return $this->respondSuccess($datosUsuario, 200);
            }  
            catch (ModelNotFoundException $e) 
            {
                return $this->respondError('Usuario no existe', 500);
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

                $datosUsuario = $request->json()->all();                
                $datosUsuario['password'] = Hash::make($datosUsuario['password']);

                $validator = $this->validator->validateInsert();

                if ($validator->fails())
                    return $this->respondFail('No es posible crear el usuario: revisar datos de entrada');

                $usuarios = Users::create($datosUsuario);                 
                
                DB::commit();

                return $this->respondSuccess($usuarios, 201);
                
            }
            catch (ModelNotFoundException $e) 
            {
                DB::rollBack();

                return $this->respondError('Falla al crear usuario:' . $e->getMessage(), 500);
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
                    return $this->respondFail('Falla al actualizar usuario: revisar datos de entrada');

                $datosUsuario = Users::findOrFail($datosRequest['id'],['id', 'run', 'nombres', 'primer_apellido', 'segundo_apellido', 'email', 'aplica_fea', 'genera_pdf', 'id_estado_usuario', 'id_perfil']);
                
                if (!empty($datosRequest['password']))
                    $datosUsuario->password = Hash::make($datosRequest['password']); 

                $datosUsuario->run = $datosRequest['run'];
                $datosUsuario->nombres = $datosRequest['nombres'];
                $datosUsuario->primer_apellido = $datosRequest['primer_apellido'];
                $datosUsuario->segundo_apellido = $datosRequest['segundo_apellido'];
                $datosUsuario->email = $datosRequest['email'];                               
                $datosUsuario->aplica_fea = $datosRequest['aplica_fea'];
                $datosUsuario->genera_pdf = $datosRequest['genera_pdf'];
                $datosUsuario->id_estado_usuario = $datosRequest['id_estado_usuario'];
                $datosUsuario->id_perfil = $datosRequest['id_perfil'];

                $datosUsuario->save();

                DB::commit();   

                return $this->respondSuccess($datosUsuario, 200);                

            } catch (ModelNotFoundException $e) {
                DB::rollBack();

                return $this->respondError('Falla al actualizar usuario:' . $e->getMessage(), 500);                
            }            
        }
        else
            return $this->respondError('Json inválido', 406);

    }
*/
 


}