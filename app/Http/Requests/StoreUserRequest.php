<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
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
                    'fullName' => ['required', 'regex:/^[a-zA-Z0-9\s\.\-]+$/', 'max:255'],
                    'email' => ['required', 'regex:/^[\w\.-]+@[a-zA-Z\d\.-]+\.[a-zA-Z]{2,6}$/', 'unique:users,email'],
                    'password' => ['required','string','min:8','regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'],
                    'role' => ['required', 'regex:/^[0-2]$/'],
               ];
    }
    
    public function messages(): array
    {
        return [
            'fullName.regex' => 'The full name may only contain letters, numbers, spaces, dots, and hyphens.',
            'email.regex' => 'The email format is invalid.',
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',            
            'role.regex' => 'The role must be one of the following values: 0, 1, or 2.',
        ];
    }
}