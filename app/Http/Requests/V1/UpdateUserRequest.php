<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
                'fullName'=>['required'],
                'role'=>['required'],
            ];
        }else{
            return [
                'fullName'=>['sometimes','required'],
                'role'=>['sometimes','required']
            ];
        }
    }
}
