<?php

namespace App\Http\Requests\Auth;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;


class RegisterRequest extends FormRequest
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
            'email' => ['required', 'email', 'unique:users,email'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed'],
            'avatar' => ['nullable', 'image', 'max:2048', 'mimes:png,jpg'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('Email is required.'),
            'email.email' => __('Email must be a valid email address.'),
            'email.exists' => __('Email does not exist in our records.'),
            'name.*' => __('Name is required and must be a string not exceeding 255 characters.'),
            'password.*' => __('Password is required and must contain at least 8 characters, including uppercase, lowercase, numbers, and symbols.'),
            'avatar.*' => __('Avatar must be an image and not exceed 2048 kilobytes in size.'),
        ];
    }
}
