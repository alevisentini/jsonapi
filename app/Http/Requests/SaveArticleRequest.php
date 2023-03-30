<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'data.attributes.title' => 'required|min:3',
            'data.attributes.slug' => 'required',
            'data.attributes.content' => 'required',
        ];
    }

    /**
     * override validated() method to return only the attributes
     */
    public function validated($key = null, $default = null)
    {
        return parent::validated()['data']['attributes'];
    }
}
