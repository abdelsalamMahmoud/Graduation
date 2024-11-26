<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'user_id'=>$this->user_id,
            'fname' => $this->fname,
            'lname' => $this->lname,
            'bio' => $this->bio,
            'phone' => $this->phone,
            'profile_pic' => $this->profile_pic ? Storage::url($this->profile_pic) : null,
            'specialty' => $this->specialty,
            'years_of_experience' => $this->years_of_experience,
        ];
    }
}