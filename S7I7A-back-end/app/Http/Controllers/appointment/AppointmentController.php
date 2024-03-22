<?php

namespace App\Http\Controllers\appointment;

use App\Http\Controllers\Controller;
use App\Mail\LocalAppointmentConfirmation;
use App\Mail\OnlineAppointmentConfirmation;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctors;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

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

        $appointmentDateTime = Carbon::parse($validated['appointment_date'] . ' ' . $validated['appointment_hour']);

        $existingAppointment = Appointment::where('user_id', Auth::id())
            ->where('appointment_date', $appointmentDateTime->format('Y-m-d'))
            ->where('appointment_hour', $appointmentDateTime->format('H:i'))
            ->first();

        if ($existingAppointment) {
            return response()->json(['error' => 'You already have an appointment at this time.'], 400);
        }

        $doctorId = $validated['doctor_id'];
        $isAvailable = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $appointmentDateTime->format('Y-m-d'))
            ->where('appointment_hour', $appointmentDateTime->format('H:i'))
            ->doesntExist();

        if (!$isAvailable) {
            return response()->json(['error' => 'Doctor is not available at this time choose another time.'], 400);
        }

        $validated['user_id'] = Auth::id();
        $appointment = Appointment::create($validated);

        $doctor = Doctors::findOrFail($doctorId);
        $patient = User::findOrFail(Auth::id());
        if ($validated['type'] === 'local') {
            $pdf = PDF::loadView('pdf.ticket', compact('appointment'))->output();
            Mail::to($patient->email)->send(new LocalAppointmentConfirmation($pdf));
        } else {
            Mail::to($patient->email)->send(new OnlineAppointmentConfirmation($appointment));
        }

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
