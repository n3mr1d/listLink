<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|min:3|max:20|unique:users,username|regex:/^[a-zA-Z0-9_]+$/',
            'password' => 'required|min:6|confirmed',
            'email'    => 'required|email:rfc|max:191|unique:users,email',
            'captcha'  => ['required', 'string', function ($attribute, $value, $fail) {
                if (!\App\Captcha::checkCaptcha($value)) {
                    $fail('The captcha code is invalid or has expired.');
                }
            }],
        ];
    }

    public function messages(): array
    {
        return [
            'username.regex'    => 'Username may only contain letters, numbers, and underscores.',
            'password.confirmed'=> 'Password confirmation does not match.',
            'email.unique'      => 'This email is already associated with another account.',
            'captcha.required'  => 'Please enter the captcha code.',
        ];
    }
}
