<?php

namespace App\Http\Controllers\admin;

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


    public function destroy(string $id){
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();
        return response()->json(['message' => 'Appointment deleted successfully']);
    }


}
