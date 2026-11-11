<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $query = Patient::with(['patientPackages.package', 'treatmentSessions'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('patient_uid', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $patients = $query->paginate(15)->withQueryString();

        return view('patients.index', compact('patients'));
    }

    public function create(): View
    {
        return view('patients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'gender' => 'required|in:male,female,other',
            'dob' => 'nullable|date',
            'age' => 'nullable|integer|min:0|max:120',
            'blood_group' => 'nullable|string|max:10',
            'phone' => 'required|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'occupation' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'landmark' => 'nullable|string|max:150',
            'city' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'medical_history' => 'nullable|string',
            'surgical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'referral_source' => 'nullable|string|max:150',
        ]);

        // Auto-generate UID
        $year = Carbon::now()->year;
        $count = Patient::whereYear('created_at', $year)->count() + 1;
        $validated['patient_uid'] = sprintf('PHY-%d-%04d', $year, $count);
        $validated['status'] = 'active';

        $patient = Patient::create($validated);

        return redirect()->route('patients.show', $patient->id)
            ->with('success', "Patient {$patient->full_name} ({$patient->patient_uid}) registered successfully!");
    }

    public function show(int $id): View
    {
        $patient = Patient::with([
            'patientPackages.package',
            'appointments.therapist',
            'appointments.service',
            'treatmentPlans',
            'treatmentSessions.therapist',
            'treatmentSessions.patientPackage.package',
            'prescribedExercises.exercise',
            'invoices.payments',
        ])->findOrFail($id);

        return view('patients.show', compact('patient'));
    }

    public function edit(int $id): View
    {
        $patient = Patient::findOrFail($id);

        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $patient = Patient::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'gender' => 'required|in:male,female,other',
            'dob' => 'nullable|date',
            'age' => 'nullable|integer|min:0|max:120',
            'blood_group' => 'nullable|string|max:10',
            'phone' => 'required|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'occupation' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'landmark' => 'nullable|string|max:150',
            'city' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'medical_history' => 'nullable|string',
            'surgical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'referral_source' => 'nullable|string|max:150',
            'status' => 'required|in:active,discharged,inactive',
        ]);

        $patient->update($validated);

        return redirect()->route('patients.show', $patient->id)
            ->with('success', 'Patient profile updated successfully!');
    }
}
