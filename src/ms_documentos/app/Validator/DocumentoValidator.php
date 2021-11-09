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

    public function validateFieldUser($campo)
    {
        return Validator::make($campo, $this->rules1(), $this->messages());
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



