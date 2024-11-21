<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'firstname'=>$this->fname,
            'lastname'=>$this->fname,
            'bio'=>$this->bio,
            'phone'=>$this->phone,
            'profile_pic'=>$this->profile_pic,
            'specialty'=>$this->specialty,
            'years_of_experience'=>$this->years_of_experience,
        ];
    }
}