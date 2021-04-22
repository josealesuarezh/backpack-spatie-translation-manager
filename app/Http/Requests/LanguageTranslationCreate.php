<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LanguageTranslationCreate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'key' => 'required',
            'text' => 'required',
            'group' => [
                'required',
                Rule::unique('language_lines')
                    ->where('key', $this->key)
                    ->where('group', $this->group)
            ]
        ];
    }

    public function messages()
    {
        return [
           'unique' => "The combination of group and key must be unique"
        ];
    }
}
