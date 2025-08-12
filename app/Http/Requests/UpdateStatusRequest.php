<?php

namespace App\Http\Requests;

use App\Enums\Ticket\TicketStatus;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
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
            'status' => ['required', 'string', new Enum(TicketStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'status.*' => __('status is required and must be in the enum options')
        ];
    }
}
