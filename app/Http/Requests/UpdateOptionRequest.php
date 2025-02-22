<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOptionRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $method = $this->method();
        if ($method == 'PUT')
        {
            return [
                'option_text'=>['required'],
                'is_correct'=>['nullable','boolean'],
            ];
        }else{
            return [
                'option_text'=>['sometimes','required'],
                'is_correct'=>['sometimes','nullable','boolean'],
            ];
        }
    }
}
