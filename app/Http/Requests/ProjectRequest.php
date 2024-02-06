<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
            'name' => 'required|min:3|regex:/^[A-Z][\w\s]+$/',
            'task_id' => 'required|array|min:1'
        ];
    }
    /**
     * Returns custom validation messages.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.min' => 'The name field must be at least :min characters.',
            'name.regex' => 'The name field must start with a capital letter and may only contain letters, numbers, spaces, and underscores.',
            'task_id.required' => 'The task field is required.'
        ];
    }
}
