<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOptionRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'options' => 'required|array|min:2|max:4', // Must be an array with at least 2 and at most 4 options
            'options.*.option_text' => 'required|string', // Each option must have text
            'options.*.is_correct' => 'nullable|boolean', // Each option can be true or false
        ];
    }
}
