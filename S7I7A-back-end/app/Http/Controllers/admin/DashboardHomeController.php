<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\DoctorRessource;
use App\Http\Resources\UserRessource;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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




    public function show()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(['user' => new UserRessource($user)]);
    }
}
