<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSessionRequest extends FormRequest
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
                'date'=>['required'],
                'time'=>['required'],
                'session_time'=>['required'],
                'status'=>['required'],
                'feedback'=>['nullable'],
            ];
        }else{
            return [
                'date'=>['sometimes','required'],
                'time'=>['sometimes','required'],
                'session_time'=>['sometimes','required'],
                'status'=>['sometimes','required'],
                'feedback'=>['sometimes','nullable'],
            ];
        }
    }
}
