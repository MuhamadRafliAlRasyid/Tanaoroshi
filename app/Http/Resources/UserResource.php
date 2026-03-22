<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->hashid,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'bagian' => $this->bagian?->nama,
            'photo' => $this->profile_photo_url,
        ];
    }
}
