<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class ReturnBorrowingRequest extends FormRequest
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
            'return_date' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'return_date.required' => 'Return date is required.',
            'return_date.date' => 'Please enter a valid date.',
        ];
    }
}
