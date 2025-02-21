<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExamRequest extends FormRequest
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
                'title'=>['required'],
            ];
        }else{
            return [
                'title'=>['sometimes','required'],
            ];
        }
    }
}
