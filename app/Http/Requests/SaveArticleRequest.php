<?php

namespace App\Http\Requests;

use App\Rules\Slug;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Category;

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
            'data.attributes.title' => ['required', 'min:3'],
            'data.attributes.slug' => [
                'required',
                'alpha_dash',
                new Slug,
                Rule::unique('articles','slug')->ignore($this->route('article')),
            ],
            'data.attributes.content' => ['required'],
            'data.relationships.category.data.id' => [
                Rule::requiredIf(!$this->route('articles')),
                Rule::exists('categories', 'slug'),
            ],
            'data.relationships.author.data.id' => [
                // Rule::requiredIf(!$this->route('articles')),
                // Rule::exists('users', 'id'),
            ],
        ];
    }
    
}
