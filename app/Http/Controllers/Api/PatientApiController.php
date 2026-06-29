<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientApiController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = Patient::with(['doctor:id,name', 'medicalHistory']);

        if ($user->isDoctor()) {
            $query->where('doctor_id', $user->id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('name', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%")
                  ->orWhere('national_id', 'like', "%$s%")
            );
        }

        $patients = $query->latest()->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => $patients->items(),
            'meta'    => [
                'total'        => $patients->total(),
                'per_page'     => $patients->perPage(),
                'current_page' => $patients->currentPage(),
                'last_page'    => $patients->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, Patient $patient)
    {
        $user = $request->user();

        if ($user->isDoctor() && $patient->doctor_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'غير مسموح'], 403);
        }

        $patient->load([
            'doctor:id,name',
            'medicalHistory',
            'appointments' => fn($q) => $q->latest('starts_at')->limit(5),
            'invoices'     => fn($q) => $q->latest()->limit(5),
            'prescriptions'=> fn($q) => $q->latest()->limit(5),
        ]);

        return response()->json([
            'success' => true,
            'data'    => $patient,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'required|string|max:20',
            'gender'      => 'required|in:male,female',
            'date_of_birth'=> 'nullable|date',
            'national_id' => 'nullable|string|max:20|unique:patients',
            'address'     => 'nullable|string',
            'doctor_id'   => 'nullable|exists:users,id',
            'notes'       => 'nullable|string',
        ]);

        $user = $request->user();
        $validated['doctor_id'] = $user->isDoctor() ? $user->id : ($validated['doctor_id'] ?? null);

        $patient = Patient::create($validated);

        $patient->medicalHistory()->create([]);

        for ($i = 1; $i <= 32; $i++) {
            $teeth[] = [
                'patient_id'   => $patient->id,
                'tooth_number' => $i,
                'tooth_type'   => 'adult',
                'status'       => 'healthy',
            ];
        }
        \App\Models\Tooth::insert($teeth);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المريض بنجاح',
            'data'    => $patient,
        ], 201);
    }

    public function update(Request $request, Patient $patient)
    {
        $user = $request->user();

        if ($user->isDoctor() && $patient->doctor_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'غير مسموح'], 403);
        }

        $validated = $request->validate([
            'name'         => 'sometimes|string|max:255',
            'phone'        => 'sometimes|string|max:20',
            'gender'       => 'sometimes|in:male,female',
            'date_of_birth'=> 'nullable|date',
            'national_id'  => 'nullable|string|max:20|unique:patients,national_id,' . $patient->id,
            'address'      => 'nullable|string',
            'doctor_id'    => 'nullable|exists:users,id',
            'notes'        => 'nullable|string',
        ]);

        $patient->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات المريض',
            'data'    => $patient->fresh(),
        ]);
    }

    public function destroy(Request $request, Patient $patient)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'غير مسموح'], 403);
        }

        $patient->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المريض',
        ]);
    }
}
