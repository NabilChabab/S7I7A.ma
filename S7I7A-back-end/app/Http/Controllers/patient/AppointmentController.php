<?php

namespace App\Http\Controllers\patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Mail\LocalAppointmentConfirmation;
use App\Mail\OnlineAppointmentConfirmation;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Requests\StoreAppointmentRequest;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;


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

    /**
     * Store a newly created resource in storage.
     */

//      public function session(Request $request)
// {
//     $validated = $request->validate([
//         'doctor_id' => 'required|exists:doctors,id',
//         'appointment_date' => 'required|date',
//         'appointment_hour' => 'required|date_format:H:i',
//         'type' => 'required|in:online,local',
//     ]);
//     Log::info('Request data:', $request->all());

//     $appointmentDateTime = Carbon::parse($validated['appointment_date'] . ' ' . $validated['appointment_hour']);

//     $doctorId = $validated['doctor_id'];
//     $price = null;

//     // Set the appointment price if type is online
//     if ($validated['type'] === 'online') {
//         $doctor = Doctor::findOrFail($doctorId);
//         $price = $doctor->price;
//     }

//     // Set up Stripe
//     Stripe::setApiKey(config('stripe.sk'));

//     // Create a Stripe session for payment
//     $session = Session::create([
//         'payment_method_types' => ['card'],
//         'line_items' => [[
//             'price_data' => [
//                 'currency' => 'usd',
//                 'unit_amount' => $price ? ($price * 100) : 0, // Convert to cents
//                 'product_data' => [
//                     'name' => 'Appointment with ' . $doctor->name,
//                     'description' => 'Appointment on ' . $appointmentDateTime->format('Y-m-d H:i'),
//                 ],
//             ],
//             'quantity' => 1,
//         ]],
//         'mode' => 'payment',
//         'success_url' => route('success', ['doctor_id' => $doctorId]), // Redirect to success page
//     ]);
//     Log::info('Session created successfully');

//     return redirect()->to($session->url);
// }

// public function success(Request $request)
// {
//     $doctorId = $request->doctor_id;
//     $doctor = Doctor::findOrFail($doctorId);
//     $appointmentDateTime = Carbon::now();

//     // Save appointment details
//     $appointment = Appointment::create([
//         'doctor_id' => $doctorId,
//         'user_id' => Auth::id(),
//         'appointment_date' => $appointmentDateTime->format('Y-m-d'),
//         'appointment_hour' => $appointmentDateTime->format('H:i'),
//         'type' => 'online',
//         'price' => $doctor->price,
//         'status' => 'pending', // or any other initial status
//     ]);

//     // Increment reserved_seats or perform any other necessary actions

//     // Send email to user
//     // You can customize this part based on your requirements
//     $user = Auth::user();
//     $data['email'] = $user->email;
//     $data['title'] = 'Appointment Confirmation';
//     $data['doctor'] = $doctor;
//     Mail::send('emails.appointment_confirmation', $data, function ($message) use ($data) {
//         $message->to($data['email'])
//             ->subject($data['title']);
//     });

//     return redirect()->route('dashboard')->with('success', 'Appointment booked successfully.');
// }


    public function store(StoreAppointmentRequest $request)
    {
        $validated = $request->validated();
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

        $doctor = Doctor::findOrFail($doctorId);
        $patient = User::findOrFail(Auth::id());
        if ($validated['type'] === 'local') {
            $pdf = PDF::loadView('pdf.ticket', compact('appointment'))->output();
            Mail::to($patient->email)->send(new LocalAppointmentConfirmation($pdf));
        } else {
            Mail::to($patient->email)->send(new OnlineAppointmentConfirmation($appointment));
        }

        return response()->json($appointment, 201);
    }

}
