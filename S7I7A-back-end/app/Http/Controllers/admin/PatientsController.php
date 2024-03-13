<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PatientRessource;
use App\Models\User;
use Illuminate\Http\Request;

class PatientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patients = User::whereHas('roles', function($query) {
            $query->where('name', 'Patient');
        })->get();

        $deleted_patients = User::onlyTrashed()->whereHas('roles', function($query) {
            $query->where('name', 'Patient');
        })->get();

        return response()->json([
            'patients' => PatientRessource::collection($patients),
            'deleted_patients' => PatientRessource::collection($deleted_patients),
        ]);
    }

    public function restore($id)
    {
        $patient = User::onlyTrashed()->where('id', $id)->firstOrFail();

        $patient->restore();

        return response()->json([
            'message' => 'Patient restored successfully',
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        $patient = User::findOrFail($id);
        $patient->delete();
        return response()->json([
         'message' => 'Patient Deleted Successfully'
        ]);
    }
}
