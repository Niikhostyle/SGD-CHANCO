<?php
namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Buzon;
use App\Models\Users;
use App\Models\BuzonUsuario;
use App\Model\DocumentoBuzon;
use App\Validator\BuzonValidator;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class BuzonController extends Controller{

    
    /**
     * @BuzonValidator
     */
    private $validator;

    public function __construct(BuzonValidator $buzonValidator)
    {
        $this->validator = $buzonValidator;
    }

    public function ver(Request $request)
    {
        if($request->isJson())
        {
            try 
            {
                $datosRequest = $request->json()->all();
                
                $validator = $this->validator->validateFieldBuzon($datosRequest);
                if ($validator->fails())
                    return $this->respondFail('Falla al obtener buzón: revisar datos de entrada');

                $datosBuzon = Buzon::findOrFail($datosRequest['id_buzon'],['id_buzon','nombre','nombre_corto','id_tipo_buzon'], 'No existe buzón');
                $datosBuzon->usuarios_asignados;

                //se agrega un item junto a usuario para que no se permita modificar desde el front, ya que el buzón al que pertenece está asignado en documentos_buzon
                $bExisteDocBuzon = count($datosBuzon->documentos_buzon);
                foreach ($datosBuzon->usuarios_asignados as $datoUsuario)
                {
                    if ($bExisteDocBuzon > 0)
                        $datoUsuario['permite_modificar'] = 0; 
                    else
                        $datoUsuario['permite_modificar'] = 1; 
                }
            

                unset($datosBuzon['documentos_buzon']);
                
                return $this->respondSuccess($datosBuzon, 200);
            }  
            catch (ModelNotFoundException $e) 
            {
                return $this->respondError('No existe buzón', 500);
            } 
        }
        else 
            return $this->respondError('Json inválido', 406);
    }

    public function listar_todos(Request $request)
    {
        if($request->isJson())
        {
            try 
            {
                $datosRequest = $request->json()->all();

                $datosSearchBuzon = Buzon::where('nombre', 'ilike', '%' . $datosRequest['texto_busqueda'] . '%')->orWhere('nombre_corto', 'ilike', '%' . $datosRequest['texto_busqueda'] . '%')->get(["id_buzon","id_tipo_buzon","nombre","nombre_corto"]);
                
                foreach($datosSearchBuzon as $datos)
                {
                    $nCountFea = 0;
                    foreach ($datos->usuarios_asignados as $datosUsuario) 
                    {                       
                        $aInfoUsuarios = Users::where('id', $datosUsuario['id_usuario'])->first(['aplica_fea']); 

                        if ($aInfoUsuarios['aplica_fea'])
                            $nCountFea++;   
                    }

                    $datos['total_us_asignados'] = count($datos->usuarios_asignados);
                    $datos['total_us_fea'] = $nCountFea;  
                }

                if (!$datosSearchBuzon->first())
                   return $this->respondError('No se encontraron buzones', 500);
                
                return $this->respondSuccess($datosSearchBuzon, 200);
            }  
            catch (ModelNotFoundException $e) 
            {
                return $this->respondError('No se encontraron buzones', 500);
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

                $datosBuzon = $request->json()->all();

                $validator = $this->validator->validateInsert();

                if ($validator->fails())
                    return $this->respondFail('Falla al crear buzón: revisar datos de entrada');
                
                $datosUsuario = $datosBuzon['usuarios_asignados'];    
                //opcion de crear buzon sin usuarios

                if(count($datosUsuario) > 0)
                {
                    foreach ($datosUsuario as $datos) 
                    {
                        $aUsuarios[] = $datos['id_usuario'];

                        $validator = $this->validator->validateFieldUser($datos);
                        if ($validator->fails())
                            return $this->respondFail('Falla al crear buzón: revisar usuarios asignados');
                        
                        $datoUsuario = Users::findOrFail($datos['id_usuario']);                  
                    }

                    if (count($datosUsuario) != count(array_unique($aUsuarios)))
                        return $this->respondFail('Falla al crear buzón: usuarios asignados repetidos');

                    if (count($datosUsuario) > 1 && $datosBuzon['tipo_buzon'] == 1)   
                        return $this->respondFail('Falla al crear buzón: asignacion de usuarios'); 
                }

                $buzon = Buzon::create([
                    'nombre' => $datosBuzon['nombre_buzon'],
                    'nombre_corto' => $datosBuzon['nombre_corto_buzon'],
                    'id_tipo_buzon' => $datosBuzon['tipo_buzon'] 
                ]);                 
                
                foreach ($datosUsuario as $datos) 
                {   
                    $buzonUsuario = BuzonUsuario::create([
                        'id_buzon' => $buzon->id_buzon,
                        'id_usuario' => $datos['id_usuario']
                    ]);                    
                }   

                $buzon->usuarios_asignados;
                
                DB::commit();

                return $this->respondSuccess($buzon, 201);
                
            }
            catch (ModelNotFoundException $e) 
            {
                DB::rollBack();

                return $this->respondError('Falla al crear buzón:' . $e->getMessage(), 500);
            }
        }
        else
            return $this->respondError('Json inválido', 406);
        
    }

    public function actualizar(Request $request){

        if ($request->isJson()) 
        {          
            try {

                DB::beginTransaction();

                $datosRequest = $request->json()->all();//all('column1', 'column2', 'column3')

                $validator = $this->validator->validateUpdate();

                if ($validator->fails())
                    return $this->respondFail('Falla al actualizar buzón: revisar datos de entrada');

                $datosUsuario = $datosRequest['usuarios_asignados'];  

                if(count($datosUsuario) > 0)
                {
                    foreach ($datosUsuario as $datos) 
                    {
                        $aUsuarios[] = $datos['id_usuario'];

                        $validator = $this->validator->validateFieldUser($datos);
                        if ($validator->fails())
                            return $this->respondFail('Falla al actualizar buzón: revisar usuarios asignados');
                        
                        $datoUsuario = Users::findOrFail($datos['id_usuario']);                  
                    }

                    if (count($datosUsuario) != count(array_unique($aUsuarios)))
                        return $this->respondFail('Falla al actualizar buzón: usuarios asignados repetidos');

                    if (count($datosUsuario) > 1 && $datosRequest['tipo_buzon'] == 1)   
                        return $this->respondFail('Falla al actualizar buzón: asignacion de usuarios'); 
                }

                $datoBuzon = Buzon::findOrFail($datosRequest['id_buzon'],['id_buzon','nombre','nombre_corto','id_tipo_buzon']);
                
                $datoBuzon->nombre = $datosRequest['nombre_buzon'];
                $datoBuzon->nombre_corto = $datosRequest['nombre_corto_buzon'];
                $datoBuzon->id_tipo_buzon = $datosRequest['tipo_buzon'];

                $datoBuzon->save();
                
                $datoBuzon->usuarios_asignados()->delete();

                foreach ($datosUsuario as $datos) 
                {                                 
                    $buzonUsuario = BuzonUsuario::create([
                        'id_buzon' => $datoBuzon->id_buzon,
                        'id_usuario' => $datos['id_usuario']
                    ]);     
                }  

                $datoBuzon->usuarios_asignados;               

                DB::commit();   

                return $this->respondSuccess($datoBuzon, 200);                

            } catch (ModelNotFoundException $e) {
                DB::rollBack();

                return $this->respondError('Falla al actualizar buzón:' . $e->getMessage(), 500);                
            }            
        }
        else
            return $this->respondError('Json inválido', 406);

    }

    public function eliminar(Request $request)
    {
        if ($request->isJson()) 
        {
            try 
            {
                DB::beginTransaction();

                $datosRequest = $request->json()->all();

                //elimanr si no tiene usuarios asignados
                $validator = $this->validator->validateFieldBuzon($datosRequest);
                if ($validator->fails())
                    return $this->respondFail('Falla al eliminar buzón: revisar datos de entrada');

                $datoBuzon = Buzon::findOrFail($datosRequest['id_buzon']);
                
                if (count($datoBuzon->usuarios_asignados) == 0)
                {
                    $datoBuzon->usuarios_asignados()->delete();
                    $datoBuzon->delete();
                }
                else
                return $this->respondError('Falla al eliminar buzón: existen datos relacionados', 500);         

                DB::commit();

                return $this->respondSuccess('Buzón eliminado con éxito', 200);         
            } 
            catch (ModelNotFoundException $e) 
            {
                DB::rollBack();

                return $this->respondError('Falla al eliminar buzón:' . $e->getMessage(), 500);  
            }
        } 
        else
            return $this->respondError('Json inválido', 406);
   
    }

    

}