<?php   
namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BuzonValidator
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
            'nombre_buzon' => 'required|max:255|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/',
            'nombre_corto_buzon' => 'required|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/',
            'tipo_buzon' => 'required|integer'
        ];
    }

    private function rules2()
    {
        return [
            'id_buzon' => 'required|integer|exists:buzon',
            'nombre_buzon' => 'required|max:255|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/',
            'nombre_corto_buzon' => 'required|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/'
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



