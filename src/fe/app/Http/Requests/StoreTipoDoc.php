<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoDoc extends FormRequest
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
            'nombre' => 'required|max:50|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/',
            'nombre_corto' => 'required|max:50|regex:/^[a-zA-Z0-9]+$/',
            'tipo_origen' => 'required',
            'tipo_flujo' => 'required',
            'tipo_folio' => 'required',
            'tipo_asignacion_folio' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'tipo_origen'=>'origen',
            'tipo_flujo'=>'tipo flujo',
            'tipo_folio'=>'tipo folio',
            'tipo_asignacion_folio'=>'asignación folio y fecha',
        ];
    }

    public function messages(){
        return [
        ];
    }
}
