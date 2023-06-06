<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SaveCommentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'data.attributes.body' => ['required'],
            'data.relationships.article.data.id' => [
                Rule::requiredIf(!$this->route('comment')),
                Rule::exists('articles', 'slug'),
            ],
            'data.relationships.author.data.id' => [
                Rule::requiredIf(!$this->route('comment')),
                Rule::exists('users', 'id'),
            ],
        ];
    }
}
