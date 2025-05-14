<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreRateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rate' => ['required', 'integer', 'min:1', 'max:5'],
            'feedback' => ['nullable', 'string'],
            'teacher_id' => 'required|exists:users,id',
        ];
    }
}
