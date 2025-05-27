<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
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
                'description'=>['nullable'],
            ];
        }else{
            return [
                'title'=>['sometimes','required'],
                'description'=>['sometimes','nullable']
            ];
        }
    }
}