<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'doctor' => $this->doctor->user?->name,
            'doctor_img' => $this->doctor?->getFirstMediaUrl('media/doctors'),
            'patient_img' => $this->user?->getFirstMediaUrl('media/patients'),
            'patient' => $this->user?->name,
            'patient_phone' => $this->user?->phone,
            'doctor_phone' => $this->doctor->user?->phone,
            'date' => $this->appointment_date,
            'time' => $this->appointment_hour,
            'status' => $this->status,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        return $data;
    }
}
