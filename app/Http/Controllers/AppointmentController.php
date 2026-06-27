<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->get('view', 'day');
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::today();

        if ($view === 'week') {
            $start = $date->copy()->startOfWeek(Carbon::SATURDAY);
            $end   = $date->copy()->endOfWeek(Carbon::FRIDAY);
        } else {
            $start = $date->copy()->startOfDay();
            $end   = $date->copy()->endOfDay();
        }

        $appointments = Appointment::with('patient')
            ->whereBetween('starts_at', [$start, $end])
            ->orderBy('starts_at')
            ->get();

        return view('appointments.index', compact('appointments', 'date', 'view'));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('name')->get(['id', 'name', 'phone']);
        $selectedDate = $request->get('date', now()->format('Y-m-d'));
        $selectedPatient = $request->get('patient_id');
        return view('appointments.create', compact('patients', 'selectedDate', 'selectedPatient'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'starts_at'  => 'required|date',
            'ends_at'    => 'required|date|after:starts_at',
            'title'      => 'nullable|string|max:255',
            'notes'      => 'nullable|string',
            'status'     => 'nullable|in:scheduled,confirmed,completed,cancelled,no_show',
        ]);

        // Check for double booking
        $conflict = Appointment::where('user_id', auth()->id())
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($validated) {
                $q->whereBetween('starts_at', [$validated['starts_at'], $validated['ends_at']])
                  ->orWhereBetween('ends_at', [$validated['starts_at'], $validated['ends_at']])
                  ->orWhere(function ($q2) use ($validated) {
                      $q2->where('starts_at', '<=', $validated['starts_at'])
                         ->where('ends_at', '>=', $validated['ends_at']);
                  });
            })->exists();

        if ($conflict) {
            return back()->withErrors(['starts_at' => 'يوجد موعد آخر في نفس الوقت'])->withInput();
        }

        Appointment::create([
            ...$validated,
            'user_id' => auth()->id(),
            'status'  => $validated['status'] ?? 'scheduled',
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'تم حجز الموعد بنجاح');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load('patient', 'doctor');
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $patients = Patient::orderBy('name')->get(['id', 'name', 'phone']);
        return view('appointments.edit', compact('appointment', 'patients'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'starts_at'  => 'required|date',
            'ends_at'    => 'required|date|after:starts_at',
            'title'      => 'nullable|string|max:255',
            'notes'      => 'nullable|string',
            'status'     => 'required|in:scheduled,confirmed,completed,cancelled,no_show',
        ]);

        $appointment->update($validated);

        return redirect()->route('appointments.index')
            ->with('success', 'تم تحديث الموعد');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->update(['status' => 'cancelled']);
        return back()->with('success', 'تم إلغاء الموعد');
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate(['status' => 'required|in:scheduled,confirmed,completed,cancelled,no_show']);
        $appointment->update(['status' => $request->status]);
        return back()->with('success', 'تم تحديث حالة الموعد');
    }
}
