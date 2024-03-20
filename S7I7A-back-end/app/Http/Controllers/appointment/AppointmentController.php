<?php

namespace App\Http\Controllers\appointment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctors;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'appointment_hour' => 'required|date_format:H:i',
            'type' => 'required|in:online,local',
        ]);

        // Combine date and hour into a single Carbon instance
        $appointmentDateTime = Carbon::parse($validated['appointment_date'] . ' ' . $validated['appointment_hour']);

        $doctorId = $validated['doctor_id'];
        $patientId = Auth::id();
        $validated['user_id'] = $patientId;
        $isAvailable = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $appointmentDateTime->format('Y-m-d'))
            ->where('appointment_hour', $appointmentDateTime->format('H:i'))
            ->doesntExist();

        if (!$isAvailable) {
            return response()->json(['error' => 'Doctor is not available at the selected time.'], 400);
        }


        $appointment = Appointment::create($validated);

        return response()->json($appointment, 201);
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
        //
    }
}
