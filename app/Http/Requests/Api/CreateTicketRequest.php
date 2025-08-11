<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateTicketRequest extends FormRequest
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
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048']
        ];
    }


    public function message(): array
    {
        return [
            'subject.*' => __('The subject must be a string and may not be greater than 255 characters.'),
            'message.*' => __('The message must be a string and may not be greater than 1000 characters.'),
            'attachment.*' => __('The attachment must be a file of type: jpg, jpeg, png, pdf and may not be greater than 2048 kilobytes.')
        ];
    }
}
