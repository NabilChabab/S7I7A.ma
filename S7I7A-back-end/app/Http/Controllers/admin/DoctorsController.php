<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Http\Resources\DoctorRessource;
use App\Models\Category;
use App\Models\Doctors;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DoctorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = Doctors::all();
        $categories = Category::all();
        return response()->json([
            'doctors' => DoctorRessource::collection($doctors),
            'categories' => $categories
        ]);
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
                'category_id' => $request->category_id,
            ]);

            $doctor->addMediaFromRequest('profile')->toMediaCollection('media/doctors', 'doctors_media');

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
    public function show(string $id)
    {
        $doctor = Doctors::with('user')->findOrFail($id);
            return response()->json([
                'doctor' =>new DoctorRessource($doctor)
            ]);

    }
    public function update(UpdateDoctorRequest $request, string $id)
{
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
    ]);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    try {
        $doctors = Doctors::findOrFail($id);

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
