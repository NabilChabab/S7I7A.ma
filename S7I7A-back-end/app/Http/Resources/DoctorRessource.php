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
            'user_id' => $this->user->id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'profile' => $this->getFirstMediaUrl('media/doctors'),
            'price' => $this->price,
            'role' => $this->user->roles->map(function ($role){
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            }),
            'cin' => $this->CIN,
            'experience' => $this->experience,
            'qualification' => $this->qualification,
            'description' => $this->description,
            'address' => $this->address,
            'category' => $this->category->name,
            'category_id' => $this->category_id,
            'created_at' => $this->user->created_at,
            'appointment_status' => $this->appointments->map(function ($appointment) {
                return [
                    'status' => $appointment->status,
                ];
            }),
            'appointments' => $this->appointments->map(function ($appointment) {
                return [
                    'appointment_date' => $appointment->appointment_date,
                    'appointment_hour' => $appointment->appointment_hour,
                ];
            }),
        ];

        return $data;
    }


}
