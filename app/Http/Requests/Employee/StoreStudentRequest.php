<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
            'nis' => ['required', 'unique:students,nis', 'max:50'],
            'nisn' => ['required', 'unique:students,nisn', 'max:50'],
            'fullname' => ['required', 'max:255'],
            'current_class' => ['nullable', 'max:50'],
            'email' => ['required', 'email', 'unique:students,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'max:20'],
            'address' => ['nullable'],
            'join_date' => ['required', 'date'],
            'status' => ['required', 'in:active,graduated,drop out'],
        ];
    }

    public function messages(): array
    {
        return [
            'nis.required' => 'NIS is required.',
            'nis.unique' => 'This NIS already exists.',
            'nisn.required' => 'NISN is required.',
            'nisn.unique' => 'This NISN already exists.',
            'fullname.required' => 'Full name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email already exists.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'join_date.required' => 'Join date is required.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status selected.',
        ];
    }
}
