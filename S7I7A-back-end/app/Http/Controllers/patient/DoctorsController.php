<?php

namespace App\Http\Controllers\patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorRessource;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = Doctor::all();
        return response()->json([
            'doctors' => DoctorRessource::collection($doctors)
        ]);
    }

}
