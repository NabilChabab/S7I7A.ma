<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;

use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request) {
        $validatedData = $request->validated();
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $user->roles()->attach(Role::where('name', 'patient')->first()->id);

        Auth::login($user);

        return response()->json([
            'message' => 'User registered successfully',
            'redirect' => '/login',
            'token' => $user->createToken('register')->plainTextToken
        ], 201);
    }

    public function login(LoginRequest $request) {
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();

        if ($user->hasRole('Admin')) {
            return response()->json([
                'message' => 'Admin login successful',
                'redirect' => '/admin/dashboard',
                'role' => 'Admin',
                'name' => $user->name,
                'userId' => $user->id,
                'token' => $user->createToken('login')->plainTextToken
            ], 200);

        }

        elseif ($user->hasRole('Doctor')) {
            return response()->json([
                'message' => 'Doctor login successful',
                'redirect' => '/doctor/dashboard',
                'role' => 'Doctor',
                'name' => $user->name,
                'userId' => $user->id,
                'token' => $user->createToken('login')->plainTextToken
            ], 200);
        }

        elseif ($user->hasRole('Patient')) {
            return response()->json([
                'message' => 'Patient login successful',
                'redirect' => '/patient/dashboard',
                'role' => 'Patient',
                'name' => $user->name,
                'userId' => $user->id,
                'token' => $user->createToken('login')->plainTextToken
            ], 200);
        }
        else{
            return response()->json([
                'message' => 'User does not have a valid role',
            ], 403);
        }

    }



    public function logout(Request $request){
        Auth::user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json([
            'message'=> 'Logout Successfully'
        ]);
    }
}

