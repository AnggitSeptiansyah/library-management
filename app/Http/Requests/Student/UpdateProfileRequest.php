<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
        $student = auth()->guard('student')->user();

        return [
            'fullname' => ['required', 'max:255'],
            'email' => ['required', 'email', Rule::unique('students')->ignore($student->id)],
            'phone' => ['nullable', 'max:20'],
            'address' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'fullname.required' => 'Full name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email already exists.',
        ];
    }
}
