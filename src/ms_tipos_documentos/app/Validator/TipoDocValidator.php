<?php   
namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TipoDocValidator
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

    public function validateField($campo)
    {
        return Validator::make($campo, $this->rules3(), $this->messages());        
    }

    private function rules1()
    {
        return [
            'nombre' => 'max:50|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/',
            'nombre_corto' => 'max:50|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/',
            'id_tipo_origen' => 'required|integer',
            'id_tipo_flujo' => 'required|integer',
            'id_tipo_folio' => 'required|integer',
            'id_tipo_asignacion_folio' => 'required|integer'
        ];
    }

    private function rules2()
    {
        return [
            'nombre' => 'max:50|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/',
            'nombre_corto' => 'max:50|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/',
            'id_tipo_origen' => 'required|integer',
            'id_tipo_flujo' => 'required|integer',
            'id_tipo_folio' => 'required|integer',
            'id_tipo_asignacion_folio' => 'required|integer'
        ];
    }
    
    private function rules3()
    {
        return [
            'id_tipo_documento' => 'required|integer',
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



