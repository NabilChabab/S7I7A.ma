<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Mail;


class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
{
    $request->validate(['email' => 'required|email']);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    $token = Password::createToken($user);

    $resetUrl = 'http://localhost:5174/reset-password?token=' . $token . '&email=' . urlencode($request->email);

    Mail::send([], [], function ($message) use ($resetUrl, $request) {
        $message->to($request->email)
            ->subject('Password Reset Link')
            ->html('Click the following link to reset your password: <a href="' . $resetUrl . '">Click here</a>');
    });

    return response()->json([
        'message' => 'Password reset link sent successfully',
        'resetUrl' => $resetUrl,
    ], 200);
}

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'token' => 'required', 
        ]);
    
        $credentials = $request->only('email', 'password', 'password_confirmation', 'token');
    
        $status = Password::reset($credentials, function ($user, $password) {
   
            $user->forceFill([
                'password' => bcrypt($password)
            ])->setRememberToken(Str::random(60));
    
            $user->save();
        });
    
        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['message' => trans($status)], 200);
        } else {
            return response()->json(['error' => trans($status)], 422);
        }
    }
}