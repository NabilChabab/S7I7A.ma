<?php

namespace App\Http\Controllers\chat;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorRessource;
use App\Http\Resources\PatientResource;
use App\Models\Doctor;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'receiver_id' => 'required',
            'sender_id' => 'required',
        ]);

        $message = Message::create([
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->input('message'),
        ]);


        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['message' => 'Message sent successfully'], 200);
    }



    public function fetchMessages(Request $request, $userId)
    {


        $authUserId = $request->user()->id;
        $messages = Message::where(function ($query) use ($authUserId, $userId) {
            $query->where('sender_id', $authUserId)
                ->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($authUserId, $userId) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', $authUserId);
        })->orderBy('created_at', 'asc')->get();


        return response()->json(['messages' => $messages], 200);
    }


    public function getPatients(Request $request)
    {
        $authDoctorId = $request->user()->doctor->id;
        $patients = User::whereHas('appointments', function ($query) use ($authDoctorId) {
            $query->where('doctor_id', $authDoctorId)->where('type', 'online');
        })->with(['appointments' => function ($query) use ($authDoctorId) {
            $query->where('doctor_id', $authDoctorId)->where('type', 'online');
        }])->get();

        return response()->json(['patients' => PatientResource::collection($patients)], 200);
    }

    public function getDoctors(Request $request)
    {
        $authPatientId = $request->user()->id;
        $doctors = Doctor::whereHas('appointments', function ($query) use ($authPatientId) {
            $query->where('user_id', $authPatientId)->where('type', 'online');
        })->with(['appointments' => function ($query) use ($authPatientId) {
            $query->where('user_id', $authPatientId)->where('type', 'online');
        }])->get();

        return response()->json(['doctors' => DoctorRessource::collection($doctors)], 200);
    }
}
