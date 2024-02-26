<?php

use App\Http\Controllers\admin\DashboardHomeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\admin\DoctorsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ForgotPasswordController::class, 'reset']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Protect the admin dashboard route
    Route::middleware('role:Admin')->prefix('admin')->group(function () {
        Route::apiResource('dashboard', DashboardHomeController::class);
        Route::apiResource('doctors', DoctorsController::class);
    });
    Route::middleware('role:Doctor')->get('/doctor/dashboard', function () {
        echo 'babydoctor';
    });

    Route::middleware('role:Patient')->get('/patient/dashboard', function () {
        echo 'babydoctor';
    });
});
