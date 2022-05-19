<?php
namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocumentoValidator
{
    private $request;
    private $validator;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function validateInsert()
    {
        return Validator::make($this->request->all(), $this->rulesInsert(), $this->messages());
    }

    public function validateFieldUser($campo)
    {
        return Validator::make($campo, $this->rules1(), $this->messages());
    }

    private function rulesInsert()
    {
        return [
            'id_tipo_documento' => 'required|integer',
            'id_nivel_acceso' => 'required|integer',
            'efectos_terceros' => 'required|in:true,false',
            'materia' => 'required'
        ];
    }

    private function rules1()
    {
        return [
            'id_usuario' => 'required|integer',
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



