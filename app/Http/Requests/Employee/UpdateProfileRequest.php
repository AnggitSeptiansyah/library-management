<?php

namespace App\Http\Requests\Employee;

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
        $employee = auth()->user();
        
        return [
            'name' => ['required', 'max:255'],
            'fullname' => ['required', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($employee->id)],
            'password' => ['nullable', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'max:20'],
            'address' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'fullname.required' => 'Full name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email already exists.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
