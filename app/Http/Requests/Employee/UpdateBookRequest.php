<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
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
        $book = $this->route('book');

        return [
            'isbn' => ['required', Rule::unique('books', 'isbn')->ignore($book->id)],
            'title' => ['required', 'max:255'],
            'author' => ['required', 'max:255'],
            'publisher' => ['required', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'edition' => ['nullable', 'max:100'],
            'publication_year' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'total_pages' => ['nullable', 'integer', 'min:1'],
            'total_copies' => ['required', 'integer', 'min:' . ($book->total_copies - $book->available_copies)],
            'description' => ['nullable'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'isbn.required' => 'ISBN is required.',
            'isbn.unique' => 'This ISBN already exists.',
            'title.required' => 'Book title is required.',
            'category_id.required' => 'Category is required.',
            'category_id.exists' => 'Selected category does not exist.',
            'total_copies.required' => 'Total copies is required.',
            'total_copies.min' => 'Total copies cannot be less than borrowed copies.',
            'cover_image.image' => 'Cover must be an image file.',
            'cover_image.max' => 'Cover image must not exceed 2MB.',
        ];
    }
}
