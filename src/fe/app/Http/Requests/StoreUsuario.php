<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Malahierba\ChileRut\ChileRut;
use Malahierba\ChileRut\Rules\ValidChileanRut;

class StoreUsuario extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
        'run'=>['required','unique:users','max:10','regex:/^[0-9]+-[0-9kK]{1}/',new ValidChileanRut(new ChileRut)],
        'id_perfil'=>'required',
        'id_estado_usuario'=>'required',
        'nombres'=>'required|max:20',
        'primer_apellido'=>'required|max:20',
        'segundo_apellido'=>'required|max:20',
        'password'=>'required|min:8|max:12',
        'email'=>'required|unique:users|email',
        ];
    }

    public function attributes()
    {
        return [
            'id_perfil'=>'Perfil',
            'id_estado_usuario'=>'Estado',
            'run'=>'RUN',
            'nombres'=>'Nombres',
        ];
    }

    public function messages(){
        return [
            'run.ValidChileanRut'=>'RUN no es valido',
            'run.ChileRut'=>'RUN no es valido',
            'run.format'=>'El formato de RUN es inválido'
        ];
    }
}
