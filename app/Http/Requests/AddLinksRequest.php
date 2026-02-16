<?php

namespace App\Http\Requests;

use App\Enum\Category;
use App\Rules\UrlFilter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddLinksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|min:3|max:100',
            'description' => 'nullable|string|max:500',
            'url' => ['required', 'string', new UrlFilter(), 'unique:links,url'],
            'category' => ['required', Rule::enum(Category::class)],
            'challenge' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'A title is required.',
            'title.min' => 'Title must be at least 3 characters.',
            'description.max' => 'Description must be 500 characters or less.',
            'url.required' => 'An .onion URL is required.',
            'url.unique' => 'This .onion URL has already been submitted.',
            'category.required' => 'Please select a category.',
            'challenge.required' => 'Please answer the security question.',
        ];
    }
}
