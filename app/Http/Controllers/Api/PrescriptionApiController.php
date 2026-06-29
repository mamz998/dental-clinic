<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionApiController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = Prescription::with(['patient:id,name,phone', 'items', 'doctor:id,name']);

        if ($user->isDoctor()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        $prescriptions = $query->latest()->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => $prescriptions->items(),
            'meta'    => [
                'total'        => $prescriptions->total(),
                'current_page' => $prescriptions->currentPage(),
                'last_page'    => $prescriptions->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, Prescription $prescription)
    {
        $user = $request->user();

        if ($user->isDoctor() && $prescription->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'غير مسموح'], 403);
        }

        $prescription->load(['patient:id,name,phone,date_of_birth,gender', 'items', 'doctor:id,name']);

        return response()->json([
            'success' => true,
            'data'    => $prescription,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'        => 'required|exists:patients,id',
            'prescription_date' => 'required|date',
            'diagnosis'         => 'nullable|string',
            'notes'             => 'nullable|string',
            'items'             => 'required|array|min:1',
            'items.*.medication_name' => 'required|string',
            'items.*.dosage'          => 'nullable|string',
            'items.*.frequency'       => 'nullable|string',
            'items.*.duration'        => 'nullable|string',
            'items.*.instructions'    => 'nullable|string',
        ]);

        $prescription = Prescription::create([
            'patient_id'        => $validated['patient_id'],
            'user_id'           => $request->user()->id,
            'prescription_date' => $validated['prescription_date'],
            'diagnosis'         => $validated['diagnosis'] ?? null,
            'notes'             => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $prescription->items()->create($item);
        }

        $prescription->load(['patient:id,name,phone', 'items', 'doctor:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الروشتة',
            'data'    => $prescription,
        ], 201);
    }
}
