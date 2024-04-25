<?php

namespace App\Http\Controllers\prescription;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrescriptionRequest;
use App\Http\Resources\PrescriptionResource;
use App\Mail\PrescriptionCreated;
use App\Models\Prescription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prescriptions = Prescription::latest()->get();
        return response()->json([
            'prescriptions' => PrescriptionResource::collection($prescriptions)
        ]);
    }

    public function patientPrescriptions()
    {
        $prescriptions = Prescription::join('appointments', 'prescriptions.appointment_id', '=', 'appointments.id')
            ->where('appointments.user_id', auth()->id())
            ->latest('prescriptions.created_at')
            ->get();

        return response()->json([
            'prescriptions' => PrescriptionResource::collection($prescriptions)
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePrescriptionRequest $request)
    {
        $prescription = Prescription::create($request->validated());

        $pdf = PDF::loadView('pdf.prescription', ['prescription' => $prescription]);
        $pdfContents = $pdf->output();
        $appointment = $prescription->appointment;
        $userEmail = $appointment->user->email;
        Mail::to($userEmail)->send(new PrescriptionCreated($pdfContents));

        return response()->json([
            'message' => 'Prescription created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $prescription = Prescription::findOrFail($id);
        return response()->json([
            'prescription' => new PrescriptionResource($prescription)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $prescription = Prescription::findOrFail($id);
        $prescription->delete();
        return response()->json([
            'message' => 'Prescription deleted successfully'
        ]);
    }
}
