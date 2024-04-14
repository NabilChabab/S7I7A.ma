<?php

namespace App\Http\Controllers\patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Mail\LocalAppointmentConfirmation;
use App\Mail\OnlineAppointmentConfirmation;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Requests\StoreAppointmentRequest;
use Stripe\PaymentIntent;
use Stripe\Stripe;


class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::id();

        $onlineAppointments = Appointment::where('user_id', $user)
            ->where('type', 'online')
            ->get();

        $localAppointments = Appointment::where('user_id', $user)
            ->where('type', 'local')
            ->get();

        return response()->json([
            'online_appointments' => AppointmentResource::collection($onlineAppointments),
            'local_appointments' => AppointmentResource::collection($localAppointments),
        ]);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $validated = $request->validated();

        $appointmentDateTime = $this->parseAppointmentDateTime($validated);

        $existingAppointment = $this->checkPatientAppointment($appointmentDateTime);

        if ($existingAppointment) {
            return $this->respondWithError('You already have an appointment at this time.', 400);
        }

        $doctorId = $validated['doctor_id'];
        $isAvailable = $this->checkDoctorAvailability($doctorId, $appointmentDateTime);

        if (!$isAvailable) {
            return $this->respondWithError('Doctor is not available at this time. Please choose another time.', 400);
        }

        list($appointment, $clientSecret) = $this->createAppointment($validated);

        return $this->respondWithSuccess($appointment, $clientSecret);
    }


    private function parseAppointmentDateTime($validated)
    {
        return Carbon::parse($validated['appointment_date'] . ' ' . $validated['appointment_hour']);
    }

    private function checkPatientAppointment($appointmentDateTime)
    {
        return Appointment::where('user_id', Auth::id())
            ->where('appointment_date', $appointmentDateTime->format('Y-m-d'))
            ->where('appointment_hour', $appointmentDateTime->format('H:i'))
            ->first();
    }

    private function checkDoctorAvailability($doctorId, $appointmentDateTime)
    {
        return Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $appointmentDateTime->format('Y-m-d'))
            ->where('appointment_hour', $appointmentDateTime->format('H:i'))
            ->doesntExist();
    }

    private function createAppointment($validated)
    {
        $validated['user_id'] = Auth::id();
        $appointment = Appointment::create($validated);
        $validated['price'] = $appointment->doctor->price;
        $appointment->save();
        $clientSecret = $this->createStripePaymentIntent($appointment);
        $this->sendAppointmentConfirmation($validated, $appointment);
        return [$appointment, $clientSecret];
    }


    private function createStripePaymentIntent($appointment)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $stripePaymentIntent = PaymentIntent::create([
            'amount' => $appointment->doctor->price * 100,
            'currency' => 'usd',
        ]);

        return $stripePaymentIntent->client_secret;
    }

    private function sendAppointmentConfirmation($validated, $appointment)
    {
        $patient = User::findOrFail(Auth::id());
        if ($validated['type'] === 'local') {
            $pdf = PDF::loadView('pdf.ticket', compact('appointment'))->output();
            Mail::to($patient->email)->send(new LocalAppointmentConfirmation($pdf));
        } else {
            Mail::to($patient->email)->send(new OnlineAppointmentConfirmation($appointment));
        }
    }

    private function respondWithError($message, $statusCode)
    {
        return response()->json(['error' => $message], $statusCode);
    }

    private function respondWithSuccess($appointment, $clientSecret)
    {
        return response()->json([$appointment, $clientSecret], 201);
    }



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
