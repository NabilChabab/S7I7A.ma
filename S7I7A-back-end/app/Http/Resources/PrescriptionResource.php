<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionResource extends JsonResource
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
            'medication' => $this->medication,
            'dosage' => $this->dosage,
            'instructions' => $this->instructions,
            'doctor' => $this->appointment->doctor->user?->name,
            'patient' => $this->appointment->user?->name,
            'patient_phone' => $this->appointment->user?->phone,
            'patient_img' => $this->appointment->user?->getFirstMediaUrl('media/patients'),
            'date' => $this->appointment->appointment_date,
            'hour' => $this->appointment->appointment_hour,
            'price' => $this->appointment->price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        return $data;
    }
}
