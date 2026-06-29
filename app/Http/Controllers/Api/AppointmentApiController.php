<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentApiController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = Appointment::with(['patient:id,name,phone', 'doctor:id,name']);

        if ($user->isDoctor()) {
            $query->where('doctor_id', $user->id);
        }

        if ($request->filled('date')) {
            $query->whereDate('starts_at', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        $appointments = $query->orderBy('starts_at')->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => $appointments->items(),
            'meta'    => [
                'total'        => $appointments->total(),
                'current_page' => $appointments->currentPage(),
                'last_page'    => $appointments->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, Appointment $appointment)
    {
        $user = $request->user();

        if ($user->isDoctor() && $appointment->doctor_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'غير مسموح'], 403);
        }

        $appointment->load(['patient:id,name,phone,gender', 'doctor:id,name']);

        return response()->json([
            'success' => true,
            'data'    => $appointment,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id'  => 'nullable|exists:users,id',
            'starts_at'  => 'required|date',
            'ends_at'    => 'required|date|after:starts_at',
            'title'      => 'nullable|string|max:255',
            'notes'      => 'nullable|string',
        ]);

        $user = $request->user();
        $validated['user_id']   = $user->id;
        $validated['doctor_id'] = $user->isDoctor() ? $user->id : ($validated['doctor_id'] ?? $user->id);

        $appointment = Appointment::create($validated);
        $appointment->load(['patient:id,name,phone', 'doctor:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الموعد',
            'data'    => $appointment,
        ], 201);
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:scheduled,confirmed,completed,cancelled,no_show',
        ]);

        $user = $request->user();

        if ($user->isDoctor() && $appointment->doctor_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'غير مسموح'], 403);
        }

        $appointment->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الموعد',
            'data'    => $appointment->fresh(['patient:id,name,phone', 'doctor:id,name']),
        ]);
    }

    public function destroy(Request $request, Appointment $appointment)
    {
        $user = $request->user();

        if ($user->isDoctor() && $appointment->doctor_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'غير مسموح'], 403);
        }

        $appointment->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الموعد',
        ]);
    }
}
