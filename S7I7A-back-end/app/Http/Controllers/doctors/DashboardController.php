<?php

namespace App\Http\Controllers\doctors;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorProfileResource;
use App\Http\Resources\PatientRessource;
use App\Http\Resources\UserRessource;
use App\Models\Doctors;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function index()
    {
        $patients = User::whereHas('roles', function ($query) {
            $query->where('name', 'Patient');
        })->latest()->take(4)->get();

        return response()->json([
            'patients' => PatientRessource::collection($patients),
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'description' => 'nullable|max:255'
        ]);
        $doctor = Doctors::findOrFail($id);

        $doctor->update($request->all());

        if ($doctor->user) {
            $doctor->user->update($request->all());
        }

        if ($request->hasFile('profile')) {
            $doctor->clearMediaCollection('media/doctors');
            $doctor->addMediaFromRequest('profile')->toMediaCollection('media/doctors', 'doctors_media');
        }

        return response()->json([
            'message' => 'Doctor updated successfully!',
            'doctor' => new DoctorProfileResource($doctor)
        ]);
    }


    public function show(string $id)
    {
        $doctor = Doctors::with('user')->findOrFail($id);

        return response()->json(['doctor' => new DoctorProfileResource($doctor)]);
    }
}
