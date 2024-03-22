<?php

namespace App\Http\Controllers\doctors;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $doctor = Auth::user()->doctor;

    $onlineAppointments = Appointment::where('doctor_id', $doctor->id)
        ->where('type', 'online')
        ->get();

    $localAppointments = Appointment::where('doctor_id', $doctor->id)
        ->where('type', 'local')
        ->get();

    return response()->json([
        'online_appointments' => AppointmentResource::collection($onlineAppointments),
        'local_appointments' => AppointmentResource::collection($localAppointments),
    ]);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();
        return response()->json([
            'message' => "Appointment canceled successfully",
            'appointment' => new AppointmentResource($appointment),
        ]);
    }
}
