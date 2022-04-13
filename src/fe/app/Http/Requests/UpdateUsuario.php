<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Malahierba\ChileRut\ChileRut;
use Malahierba\ChileRut\Rules\ValidChileanRut;

class UpdateUsuario extends FormRequest
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
        'run'=>['required','unique:users,run,'.$this->form_id_usuario,'max:10','regex:/^[0-9]+-[0-9kK]{1}/',new ValidChileanRut(new ChileRut)],
        //'run' => 'required|unique:users,run,'.$this->request->id,
        'id_perfil'=>'required',
        'id_estado_usuario'=>'required',
        'nombres'=>'required|max:20',
        'primer_apellido'=>'required|max:20',
        'segundo_apellido'=>'required|max:20',
        'password'=>'min:8|max:12',
        'password' => 'sometimes',
        're_password' => 'required_with:password|same:password',
        'email' => 'required|email|unique:users,email,'.$this->form_id_usuario, 
        //'form_imagen_firma'=>'required|image|mimes:jpeg,png,jpg,gif|max:2048'

//        'email'=>'required|email',
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
            'run.ChileRut'=>'RUN no es valido'
        ];
    }
}
