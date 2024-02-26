<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Resources\DoctorRessource;
use App\Models\doctors\Doctors;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DoctorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = Doctors::all();
        return response()->json([
            'doctors' => DoctorRessource::collection($doctors),
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
    public function store(StoreDoctorRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            Log::debug('Created user:', $user->toArray());
            $doctor = Doctors::create([
                'user_id' => $user->id,
                'address' => $request->address,
                'experience' => $request->experience,
                'qualification' => $request->qualification,
                'description' => $request->description,
                'CIN' => $request->CIN,
            ]);

            $doctor->addMediaFromRequest('profile')->toMediaCollection('doctors', 'users_media');

            $user->roles()->attach(Role::where('name', 'Doctor')->first()->id);

            return response()->json([
                'message' => 'Doctor created successfully!',
                'token' => $user->createToken('register')->plainTextToken,
            ], 201);
        } catch (Exception $e) {
            Log::error('Error creating doctor: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create doctor'], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Doctors $doctors)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $doctor = Doctors::findOrFail($id);
        return response()->json([
            'doctor' => $doctor
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Doctors $doctor)
{
    try {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email,' . $doctor->user->id, // Exclude current user ID
            'phone' => 'required|string',
            'address' => 'required|string',
            'experience' => 'required|string',
            'qualification' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = $doctor->user;

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        $doctor->update([
            'address' => $request->address,
            'experience' => $request->experience,
            'qualification' => $request->qualification,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Doctor updated successfully!',
        ]);
    } catch (Exception $e) {
        Log::error('Error updating doctor: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to update doctor'], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    try {
        $doctors = Doctors::find($id);

        $user = User::where('id', $doctors->user_id)->first();

        if ($user) {
            $user->delete();
        }

        $doctors->delete();

        return response()->json([
            'message' => 'Doctor deleted successfully!',
        ]);
    } catch (Exception $e) {
        Log::error('Error deleting doctor: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to delete doctor'], 500);
    }
}
}
