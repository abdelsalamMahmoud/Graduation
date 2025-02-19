<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_id' => 'required|exists:users,id',
            'days' => 'required|array|min:1',
            'days.*' => 'in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'time' => 'required|date_format:H:i',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'duration' => 'required|integer',
        ];
    }
}
