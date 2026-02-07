<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreBorrowingRequest extends FormRequest
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
            'student_id' => ['required', 'exists:students,id'],
            'borrow_date' => ['required', 'date'],
            'book_ids' => ['required', 'array', 'min:1'],
            'book_ids.*' => ['required', 'exists:books,id', 'distinct'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'Student is required.',
            'student_id.exists' => 'Selected student does not exist.',
            'borrow_date.required' => 'Borrow date is required.',
            'borrow_date.date' => 'Please enter a valid date.',
            'book_ids.required' => 'At least one book must be selected.',
            'book_ids.min' => 'At least one book must be selected.',
            'book_ids.*.exists' => 'One or more selected books do not exist.',
            'book_ids.*.distinct' => 'Cannot select the same book twice.',
        ];
    }
}
