<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorRessource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'profile' => $this->getFirstMediaUrl('media/doctors'),
            'role' => $this->user->roles->map(function ($role){
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            }),
            'cin' => $this->CIN,
            'created_at' => $this->user->created_at,
        ];

        return $data;
    }
}
