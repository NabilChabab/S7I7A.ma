<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'profile' => $this->getFirstMediaUrl('media/patients'),
            'created_at' => $this->created_at,
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
    }
}

