<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBuzon extends FormRequest
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
            'nombre'=>'required|max:50',
            'nombre_corto'=>'required|max:50',
            'cargo'=>'required|max:50'
        ];
    }

    public function attributes()
    {
        return [
            'duallistbox'=>'usuarios asignados',
        ];
    }

    public function messages(){
        return [
           
        ];
    }
}
