<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTeacherRequest extends FormRequest
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
            //
            'fname'=>['required', 'regex:/^[a-zA-Z\s\.\-]+$/'],
            'lname'=>['required', 'regex:/^[a-zA-Z\s\.\-]+$/'],
            'bio'=>['nullable', 'regex:/^[a-zA-Z0-9\s\.\,\-]+$/'],
            'profile_pic'=>['nullable', 'url'],
            'specialty'=>['required', 'regex:/^[a-zA-Z\s\.\-]+$/'],
            'years_of_experience'=>['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'fname.regex' => 'The first name may only contain letters, spaces, dots, and hyphens.',
            'lname.regex' => 'The last name may only contain letters, spaces, dots, and hyphens.',
            'bio.regex' => 'The bio may only contain letters, numbers, spaces, dots, commas, and hyphens.',
            'profile_pic.url' => 'The profile picture must be a valid URL.',
            'specialty.regex' => 'The specialty may only contain letters, spaces, dots, and hyphens.',
            'years_of_experience.integer' => 'The years of experience must be an integer.',
            'years_of_experience.min' => 'The years of experience must be at least 0.',
        ];
    }
}