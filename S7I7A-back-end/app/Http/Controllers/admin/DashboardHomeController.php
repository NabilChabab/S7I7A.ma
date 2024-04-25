<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\DoctorRessource;
use App\Http\Resources\UserRessource;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardHomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = Doctor::latest()->take(4)->get();

        return response()->json([
            'message' => 'success',
            'users' => DoctorRessource::collection($users),
        ]);
    }
    public function updateProfile(UpdateUserRequest $request, $id)
    {

        $user = User::findOrFail($id);
        $user->update($request->all());

        if ($request->hasFile('profile')) {
            $user->clearMediaCollection('media/admin');
            $user->addMediaFromRequest('profile')->toMediaCollection('media/admin', 'admin_media');
        }

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ], 200);
    }

    public function counts()
    {
        $doctors = Doctor::all()->count();
        $patients = User::all()->count();

        // Most reserved doctor
        $mostReservedDoctor = Doctor::withCount('appointments')
            ->orderBy('appointments_count', 'desc')
            ->first();

        // Most reserved appointment time
        $mostReservedTime = Appointment::select('appointment_hour', DB::raw('count(*) as total'))
            ->groupBy('appointment_hour')
            ->orderBy('total', 'desc')
            ->first();

        // Doctor with the most articles
        $doctorWithMostArticles = Doctor::withCount('articles')
            ->orderBy('articles_count', 'desc')
            ->first();

        // Doctor with the most categories
        $doctorWithMostCategories = Doctor::withCount('category')
            ->orderBy('category_id', 'desc')
            ->first();

        return response()->json([
            'doctors' => $doctors,
            'patients' => $patients,
            'most_reserved_doctor' => $mostReservedDoctor,
            'most_reserved_time' => $mostReservedTime,
            'doctor_with_most_articles' => $doctorWithMostArticles,
            'doctor_with_most_categories' => $doctorWithMostCategories,
        ]);
    }




    public function show()
    {
        $user = Auth::user();
        return response()->json(['user' => new UserRessource($user)]);
    }
}
