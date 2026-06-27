<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\Patient;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Prescription::with('patient', 'doctor')->latest('prescription_date');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $prescriptions = $query->paginate(20)->withQueryString();

        return view('prescriptions.index', compact('prescriptions'));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('name')->get(['id', 'name', 'phone']);
        $selectedPatient = $request->patient_id ? Patient::find($request->patient_id) : null;
        return view('prescriptions.create', compact('patients', 'selectedPatient'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id'               => 'required|exists:patients,id',
            'prescription_date'        => 'required|date',
            'diagnosis'                => 'nullable|string',
            'notes'                    => 'nullable|string',
            'items'                    => 'required|array|min:1',
            'items.*.medication_name'  => 'required|string',
            'items.*.dosage'           => 'nullable|string',
            'items.*.frequency'        => 'nullable|string',
            'items.*.duration'         => 'nullable|string',
            'items.*.instructions'     => 'nullable|string',
        ]);

        $prescription = Prescription::create([
            'patient_id'        => $request->patient_id,
            'user_id'           => auth()->id(),
            'prescription_date' => $request->prescription_date,
            'diagnosis'         => $request->diagnosis,
            'notes'             => $request->notes,
        ]);

        foreach ($request->items as $item) {
            $prescription->items()->create($item);
        }

        return redirect()->route('prescriptions.show', $prescription)
            ->with('success', 'تم إنشاء الروشتة بنجاح');
    }

    public function show(Prescription $prescription)
    {
        $prescription->load('patient', 'doctor', 'items');
        return view('prescriptions.show', compact('prescription'));
    }

    public function edit(Prescription $prescription)
    {
        $patients = Patient::orderBy('name')->get(['id', 'name']);
        $prescription->load('items');
        return view('prescriptions.edit', compact('prescription', 'patients'));
    }

    public function update(Request $request, Prescription $prescription)
    {
        $request->validate([
            'prescription_date'        => 'required|date',
            'diagnosis'                => 'nullable|string',
            'notes'                    => 'nullable|string',
            'items'                    => 'required|array|min:1',
            'items.*.medication_name'  => 'required|string',
            'items.*.dosage'           => 'nullable|string',
            'items.*.frequency'        => 'nullable|string',
            'items.*.duration'         => 'nullable|string',
            'items.*.instructions'     => 'nullable|string',
        ]);

        $prescription->update([
            'prescription_date' => $request->prescription_date,
            'diagnosis'         => $request->diagnosis,
            'notes'             => $request->notes,
        ]);

        $prescription->items()->delete();
        foreach ($request->items as $item) {
            $prescription->items()->create($item);
        }

        return redirect()->route('prescriptions.show', $prescription)
            ->with('success', 'تم تحديث الروشتة');
    }

    public function destroy(Prescription $prescription)
    {
        $prescription->delete();
        return redirect()->route('prescriptions.index')->with('success', 'تم حذف الروشتة');
    }
}
