<?php

namespace App\Http\Resources;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorProfileResource extends JsonResource
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
            'experience' => $this->experience,
            'qualification' => $this->qualification,
            'description' => $this->description,
            'address' => $this->address,
            'cin' => $this->CIN,
            'price' => $this->price,
            'created_at' => $this->user->created_at,
        ];

        return $data;

    }


}
