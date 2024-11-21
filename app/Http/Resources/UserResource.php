<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        // return parent::toArray($request);
        return[
            'id'=>$this->id,
            'fullName'=>$this->fullName,
            'email'=>$this->email,
            'role'=>$this->role,
            'status'=>$this->status,
            'verification_code'=>$this->verification_code,
            'verification_expires_at'=>$this->verification_expires_at,
            'is_verified'=>$this->is_verified,
        ];
    }
}