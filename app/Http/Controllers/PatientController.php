<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Tooth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $patients = $query->latest()->paginate(20)->withQueryString();

        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'phone'                   => 'required|string|max:20',
            'phone_alt'               => 'nullable|string|max:20',
            'date_of_birth'           => 'nullable|date',
            'gender'                  => 'required|in:male,female',
            'national_id'             => 'nullable|string|max:20|unique:patients',
            'address'                 => 'nullable|string',
            'emergency_contact_name'  => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'notes'                   => 'nullable|string',
            // Medical history
            'allergy_anesthesia'      => 'nullable|boolean',
            'allergy_penicillin'      => 'nullable|boolean',
            'allergies_other'         => 'nullable|string',
            'has_diabetes'            => 'nullable|boolean',
            'has_heart_disease'       => 'nullable|boolean',
            'has_bleeding_disorder'   => 'nullable|boolean',
            'chronic_conditions_other'=> 'nullable|string',
            'current_medications'     => 'nullable|string',
            'is_pregnant'             => 'nullable|boolean',
            'medical_notes'           => 'nullable|string',
        ]);

        $patient = Patient::create([
            'name'                    => $validated['name'],
            'phone'                   => $validated['phone'],
            'phone_alt'               => $validated['phone_alt'] ?? null,
            'date_of_birth'           => $validated['date_of_birth'] ?? null,
            'gender'                  => $validated['gender'],
            'national_id'             => $validated['national_id'] ?? null,
            'address'                 => $validated['address'] ?? null,
            'emergency_contact_name'  => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            'notes'                   => $validated['notes'] ?? null,
        ]);

        // Medical history
        $patient->medicalHistory()->create([
            'allergy_anesthesia'       => $request->boolean('allergy_anesthesia'),
            'allergy_penicillin'       => $request->boolean('allergy_penicillin'),
            'allergies_other'          => $validated['allergies_other'] ?? null,
            'has_diabetes'             => $request->boolean('has_diabetes'),
            'has_heart_disease'        => $request->boolean('has_heart_disease'),
            'has_bleeding_disorder'    => $request->boolean('has_bleeding_disorder'),
            'chronic_conditions_other' => $validated['chronic_conditions_other'] ?? null,
            'current_medications'      => $validated['current_medications'] ?? null,
            'is_pregnant'              => $request->boolean('is_pregnant'),
            'medical_notes'            => $validated['medical_notes'] ?? null,
        ]);

        // Initialize teeth (32 adult teeth)
        $teeth = [];
        for ($i = 1; $i <= 32; $i++) {
            $teeth[] = ['patient_id' => $patient->id, 'tooth_number' => $i, 'tooth_type' => 'adult', 'status' => 'healthy'];
        }
        Tooth::insert($teeth);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'تم إضافة المريض بنجاح');
    }

    public function show(Patient $patient)
    {
        $patient->load([
            'medicalHistory',
            'teeth.treatments',
            'treatmentPlans.items',
            'appointments' => fn($q) => $q->latest('starts_at')->limit(10),
            'invoices',
            'prescriptions' => fn($q) => $q->latest()->limit(5),
            'files',
        ]);

        $teeth = $patient->teeth->keyBy('tooth_number');

        return view('patients.show', compact('patient', 'teeth'));
    }

    public function edit(Patient $patient)
    {
        $patient->load('medicalHistory');
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'phone'                   => 'required|string|max:20',
            'phone_alt'               => 'nullable|string|max:20',
            'date_of_birth'           => 'nullable|date',
            'gender'                  => 'required|in:male,female',
            'national_id'             => 'nullable|string|max:20|unique:patients,national_id,' . $patient->id,
            'address'                 => 'nullable|string',
            'emergency_contact_name'  => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'notes'                   => 'nullable|string',
        ]);

        $patient->update($validated);

        $patient->medicalHistory()->updateOrCreate(
            ['patient_id' => $patient->id],
            [
                'allergy_anesthesia'       => $request->boolean('allergy_anesthesia'),
                'allergy_penicillin'       => $request->boolean('allergy_penicillin'),
                'allergies_other'          => $request->allergies_other,
                'has_diabetes'             => $request->boolean('has_diabetes'),
                'has_heart_disease'        => $request->boolean('has_heart_disease'),
                'has_bleeding_disorder'    => $request->boolean('has_bleeding_disorder'),
                'chronic_conditions_other' => $request->chronic_conditions_other,
                'current_medications'      => $request->current_medications,
                'is_pregnant'              => $request->boolean('is_pregnant'),
                'medical_notes'            => $request->medical_notes,
            ]
        );

        return redirect()->route('patients.show', $patient)
            ->with('success', 'تم تحديث بيانات المريض');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')
            ->with('success', 'تم حذف المريض');
    }

    public function updateTooth(Request $request, Patient $patient, Tooth $tooth)
    {
        $request->validate([
            'status' => 'required|in:healthy,filling,crown,root_canal,missing,needs_extraction,implant,bridge,other',
            'notes'  => 'nullable|string',
        ]);

        $tooth->update($request->only('status', 'notes'));

        return back()->with('success', 'تم تحديث حالة السن');
    }

    public function uploadFile(Request $request, Patient $patient)
    {
        $request->validate([
            'file'        => 'required|file|max:10240|mimes:jpg,jpeg,png,pdf,dcm',
            'file_type'   => 'required|in:xray,photo,document',
            'description' => 'nullable|string',
            'taken_date'  => 'nullable|date',
        ]);

        $path = $request->file('file')->store("patients/{$patient->id}/files", 'public');

        $patient->files()->create([
            'user_id'     => auth()->id(),
            'file_name'   => $request->file('file')->getClientOriginalName(),
            'file_path'   => $path,
            'file_type'   => $request->file_type,
            'mime_type'   => $request->file('file')->getMimeType(),
            'file_size'   => $request->file('file')->getSize(),
            'description' => $request->description,
            'taken_date'  => $request->taken_date,
        ]);

        return back()->with('success', 'تم رفع الملف بنجاح');
    }
}
