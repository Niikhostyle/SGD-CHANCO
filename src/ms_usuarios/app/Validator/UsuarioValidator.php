<?php   
namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsuarioValidator
{
    private $request; 
    private $validator;
    
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function validateInsert()
    {
        return Validator::make($this->request->all(), $this->rules1(), $this->messages());        
    }

    public function validateUpdate()
    {
        return Validator::make($this->request->all(), $this->rules2(), $this->messages());        
    }

    public function validateFieldUser($campo)
    {
        return Validator::make($campo, $this->rules3(), $this->messages());        
    }

    public function validateFieldBuzon($campo)
    {
        return Validator::make($campo, $this->rules4(), $this->messages());        
    }

    private function rules1()
    {
        return [
            'run' => 'required|unique:users',
            'id_perfil' => 'required|integer',
            'id_estado_usuario' => 'required|integer',            
            'nombres' => 'required|max:255|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/',
            'primer_apellido' => 'required|max:255|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/',
            'segundo_apellido' => 'required|max:255|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/',
            'email' => 'required|email|unique:users', 
            'password' => 'required',
            'confirmar_password' => 'required|same:password'

        ];
    }

    private function rules2()
    {
        return [
            'run' => 'required',
            //'run' => 'required|unique:users,run,'.$this->request->id,
            'id_perfil' => 'required|integer',
            'id_estado_usuario' => 'required|integer',            
            'nombres' => 'required|max:255|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/',
            'primer_apellido' => 'required|max:255|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/',
            'segundo_apellido' => 'required|max:255|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/',
            //'email' => 'required|email|unique:users,email,'.$this->request->id, //para actualizacion no considera el valor del mismo registro
            'email' => 'required|email',
            'password' => 'sometimes',
            'confirmar_password' => 'required_with:password|same:password'
        ];
    }

    
    private function rules3()
    {
        return [
            'id_usuario' => 'required|integer',
        ];
    }

    private function rules4()
    {
        return [
            'id_buzon' => 'required|integer',
        ];
    }

    private function messages()
    {
        return [
            'required' => ':attribute es requerido.',
            'max' => ':attribute no cumple con máximo definido',
            'integer' => ':attribute debe ser entero'
        ];
    }

}