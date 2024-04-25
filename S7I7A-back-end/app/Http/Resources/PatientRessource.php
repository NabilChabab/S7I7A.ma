<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientRessource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'email' => $this->email,
        'phone' => $this->phone,
        'created_at' => $this->created_at,
        'deleted_at' => $this->deleted_at,
        'profile' => $this->getFirstMediaUrl('media/patients'),
        'role' => $this->roles->map(function ($role){
            return [
                'name' => $role->name,
            ];
        }),

        //Role with Ressources also the loading
    ];
}

}
