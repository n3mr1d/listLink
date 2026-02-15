<?php

namespace App\Http\Requests;

use App\Enum\Category;
use App\Rules\UrlFilter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddLinksRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|min:3|max:50',
            'description' => 'required|min:3|max:25',
            'url' => ['required',new UrlFilter(),'unique:links,url'],
            'category' => ['required',Rule::enum(Category::class)],

        ];
    }
}
