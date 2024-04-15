<?php

use App\Events\MessageSent;
use App\Http\Controllers\admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\admin\ArticlesController as AdminArticlesController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\DashboardHomeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\admin\DoctorsController;
use App\Http\Controllers\admin\PatientsController;
use App\Http\Controllers\chat\MessageController;
use App\Http\Controllers\patient\AppointmentController;
use App\Http\Controllers\doctors\AppointmentController as DoctorsAppointmentController;
use App\Http\Controllers\doctors\ArticlesController;
use App\Http\Controllers\doctors\DashboardController;
use App\Http\Controllers\HomeController;
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

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

Route::get('/doctor-details/{id}', [HomeController::class, 'showDoctor']);
Route::get('/doctor-byCategory/{id}', [HomeController::class, 'showDoctorByCategory']);
Route::apiResource('index', HomeController::class);






Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Protect the admin  routes
    Route::middleware('role:Admin')->prefix('admin')->group(function () {
        Route::patch('/profile/{id}', [DashboardHomeController::class, 'updateProfile']);
        Route::apiResource('dashboard', DashboardHomeController::class);
        Route::apiResource('doctors', DoctorsController::class);
        Route::put('patients/{id}/restore', [PatientsController::class, 'restore']);
        Route::apiResource('patients', PatientsController::class);
        Route::put('categories/{id}/restore', [CategoryController::class, 'restore']);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('article', AdminArticlesController::class);
        Route::apiResource('appointment', AdminAppointmentController::class);

    });
    // Protect the doctor  routes
    Route::middleware('role:Doctor')->prefix('doctor')->group(function () {
        Route::apiResource('home', DashboardController::class);
        Route::apiResource('articles', ArticlesController::class);
        Route::apiResource('appointments', DoctorsAppointmentController::class);
    });

    // Protect the patient  routes
    Route::prefix('patient')->group(function () {
        Route::patch('/profile/{id}', [HomeController::class, 'updateProfile']);
        Route::apiResource('appointment', AppointmentController::class);
        Route::post('session', [AppointmentController::class ,'session'])->name('session');
        Route::get('success', [AppointmentController::class , 'success'])->name('success');
    });

    //Chat
    Route::post('/messages', [MessageController::class, 'sendMessage']);
    Route::get('/messages/{userId}', [MessageController::class, 'fetchMessages']);
    Route::get('users', [MessageController::class, 'getPatients']);
    Route::get('doctors', [MessageController::class, 'getDoctors']);
});
