<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount'=>'required',
            'number_of_days'=>'required',
            'task_per_day'=>'required',
            'progress'=>'nullable',
            'user_id'=>'required',
        ];
    }
}
