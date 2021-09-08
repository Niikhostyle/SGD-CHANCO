<?php
namespace App\Http\Controllers;

//use App\Http\Controllers\ModelNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Buzon;
use App\Models\Usuario;
use App\Models\BuzonUsuario;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class BuzonController extends Controller{

    public function listar_todos(Request $request)
    {        
        if($request->isJson())
        {
            try 
            {
                $datosBuzon = Buzon::all();

                if($datosBuzon)
                    return $this->respondSuccess($datosBuzon, 200);
                else
                    return $this->respondSuccess('No existen buzones', 204);
            }  
            catch (ModelNotFoundException $e) 
            {
                return $this->respondError('Falla al obtener buzones', 500);
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

                $this->validate($request, [
                    'id_buzon' => 'required|integer'
                ],[
                    'id_buzon.required' => 'Campo buzon es requerido',
                    'id_buzon.integer' => 'Campo buzon debe ser entero'
                ]);
                
                $datosBuzon = Buzon::findOrFail($datosRequest['id_buzon'],['id_buzon','nombre','nombre_corto','id_tipo_buzon']);
                $datosBuzon->usuarios_asignados;

                if($datosBuzon)
                    return $this->respondSuccess($datosBuzon, 200);
                else
                    return $this->respondSuccess('No existe buzón', 204);
            }  
            catch (ModelNotFoundException $e) 
            {
                return $this->respondError('Falla al obtener buzón', 500);
            } 
        }
        else 
            return $this->respondError('Json inválido', 406);
    }

    public function crear(Request $request)
    {
        if($request->isJson())
        {
            $datosBuzon = $request->json()->all();
            
            $this->validate($request, [
                'nombre_buzon' => 'required|max:255',
                'nombre_corto_buzon' => 'required|max:255'
            ],[
                'nombre_buzon.required' => 'Campo nombre es requerido',
                'nombre_corto_buzon.required' => 'Campo nombre buzón es requerido'
            ]);

            try{
                DB::beginTransaction();

                $buzon = Buzon::create([
                    'nombre' => $datosBuzon['nombre_buzon'],
                    'nombre_corto' => $datosBuzon['nombre_corto_buzon'],
                    'id_tipo_buzon' => 2 //$datosBuzon['tipo_buzon'] //debiera ser por defecto ????
                ]); 

                $datosUsuario = $datosBuzon['usuarios_asignados'];  
                foreach ($datosUsuario as $datos ) 
                        $aUsuarios[] = $datos['id_usuario'];

                if(count($datosUsuario) != count(array_unique($aUsuarios)))
                    return $this->respondFail('Existen usuarios asignados repetidos');

                foreach ($datosUsuario as $datos ) 
                {                    
                    //$userExists = Usuario::where("id_usuario", $datos['id_usuario'])->exists();
                    //if ($userExists === FALSE) 
                    //    return response()->json(['error' => 'Invalid parameters'], 406);
                
                    $datoUsuario = Usuario::find($datos['id_usuario']);
                    
                    if(!$datoUsuario)
                        return response()->json([
                            'status' => '500', 
                            'data' => [
                                'comentario' => 'Usuario no encontrado'
                            ]],500);  

                    $buzonUsuario = BuzonUsuario::create([
                        'id_buzon' => $buzon->id_buzon,
                        'id_usuario' => $datos['id_usuario']
                    ]);
                }
                
                DB::commit();

                return response()->json([
                    'status' => '201', 
                    'data' => $buzon
                ]);  

            }
            catch (ModelNotFoundException $e) 
            {
                DB::rollBack();

                return response()->json([
                    'status' => '500', 
                    'data' => [
                        'comentario' => 'Falla al crear buzón'
                    ]]);  
            }
        }
        else
        {
            return response()->json([
                'status' => '401', 
                'data' => [
                    'comentario' => 'Sin autorización'
                ]], 401);  
    
        }      
        
    }

    public function actualizar(Request $request){

        if ($request->isJson()) 
        {
            
            $this->validate($request, [
                'id_buzon' => 'required',
                'nombre_buzon' => 'required|max:255',
                'nombre_corto_buzon' => 'required|max:255'
            ],[
                'id_buzon.required' => 'Campo id buzón es requerido',
                'nombre_buzon.required' => 'Campo nombre es requerido',
                'nombre_corto_buzon.required' => 'Campo nombre buzón es requerido'
            ]);
            
            try {

                DB::beginTransaction();

                $datosRequest = $request->json()->all();
                
                $datoBuzon = Buzon::findOrFail($datosRequest['id_buzon'],['id_buzon','nombre','nombre_corto','id_tipo_buzon']);

                $datoBuzon->nombre = $datosRequest['nombre_buzon'];
                $datoBuzon->nombre_corto = $datosRequest['nombre_corto_buzon'];

                $datoBuzon->save();

                foreach($datoBuzon->usuarios_asignados as $delBuzon)
                    $delBuzon->delete();                  

                $datosUsuario = $datosRequest['usuarios_asignados'];  

                foreach ($datosUsuario as $datos ) 
                    $aUsuarios[] = $datos['id_usuario'];

                if(count($datosUsuario) != count(array_unique($aUsuarios)))
                    return response()->json([
                        'status' => '500', 
                        'data' => [
                            'comentario' => 'Usuarios repetidos'
                        ]],500); 

                foreach ($datosUsuario as $datos) 
                {                                 
                    $datoUsuario = Usuario::find($datos['id_usuario']);
                
                    if(!$datoUsuario)
                        return response()->json([
                            'status' => '500', 
                            'data' => [
                                'comentario' => 'Usuario no encontrado'
                            ]],500);                                              
                    
                    $buzonUsuario = BuzonUsuario::create([
                        'id_buzon' => $datoBuzon->id_buzon,
                        'id_usuario' => $datos['id_usuario']
                    ]);     
                }  
                    
                DB::commit();   

                return response()->json([
                    'status' => '200', 
                    'data' => $datoBuzon
                ]);  

            } catch (ModelNotFoundException $e) {
                DB::rollBack();
                return response()->json([
                    'status' => '406', 
                    'data' => [
                        'comentario' => 'No existe buzón para actualizar'
                    ]], 406);
            }            
        }
        else
        {
            return response()->json([
                'status' => '401', 
                'data' => [
                    'comentario' => 'Sin autorización para actualizar'
                ]], 401);  
        }

    }

    public function eliminar(Request $request)
    {
        if ($request->isJson()) 
        {
            try 
            {
                DB::beginTransaction();

                $datosRequest = $request->json()->all();

                $this->validate($request, [
                    'id_buzon' => 'required|integer'
                ],[
                    'id_buzon.required' => 'Campo buzon es requerido',
                    'id_buzon.integer' => 'Campo buzon debe ser entero'
                ]);

                $datoBuzon = Buzon::findOrFail($datosRequest['id_buzon']);

                foreach($datoBuzon->usuarios_asignados as $delBuzon)
                    $delBuzon->delete();

                $datoBuzon->delete();

                DB::commit();

                return response()->json([
                    'status' => '200', 
                    'data' => [
                        'comentario' => 'Buzón eliminado con éxito'
                    ]], 200);
            } 
            catch (ModelNotFoundException $e) 
            {
                DB::rollBack();

                return response()->json([
                    'status' => '406', 
                    'data' => [
                        'comentario' => 'No existe buzón a eliminar'
                    ]], 406);
            }
        } 
        else
        {
            return $this->respondOff('Sin autorización para eliminar');
            /*
            return response()->json([
                'status' => '401', 
                'data' => [
                    'comentario' => 'Sin autorización para eliminar'
                ]], 401);  
    
            */
            
        }      
    }

}