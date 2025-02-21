<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
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
                'question_text'=>['required'],
            ];
        }else{
            return [
                'question_text'=>['sometimes','required'],
            ];
        }
    }
}
