<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Appointment::with(['patient', 'therapist.therapistProfile', 'service'])
            ->latest('appointment_date')
            ->latest('start_time');

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->input('date'));
        }

        if ($request->filled('location')) {
            $query->where('service_location', $request->input('location'));
        }

        if ($request->filled('therapist_id')) {
            $query->where('therapist_id', $request->input('therapist_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $appointments = $query->paginate(15)->withQueryString();
        $therapists = User::where('role', 'therapist')->where('status', 'active')->get();

        return view('appointments.index', compact('appointments', 'therapists'));
    }

    public function create(Request $request): View
    {
        $patients = Patient::where('status', 'active')->orderBy('first_name')->get();
        $therapists = User::where('role', 'therapist')->where('status', 'active')->with('therapistProfile')->get();
        $services = Service::where('is_active', true)->get();
        $selectedPatientId = $request->query('patient_id');

        return view('appointments.create', compact('patients', 'therapists', 'services', 'selectedPatientId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'therapist_id' => 'required|exists:users,id',
            'service_id' => 'nullable|exists:services,id',
            'service_location' => 'required|in:clinic,home_visit',
            'home_address' => 'nullable|string',
            'home_landmark' => 'nullable|string|max:150',
            'travel_distance_km' => 'nullable|numeric|min:0',
            'travel_charge' => 'nullable|numeric|min:0',
            'appointment_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'travel_buffer_minutes' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $dateFormatted = Carbon::parse($validated['appointment_date'])->format('Ymd');
        $count = Appointment::whereDate('appointment_date', $validated['appointment_date'])->count() + 1;
        $validated['appointment_no'] = sprintf('APT-%s-%03d', $dateFormatted, $count);
        $validated['status'] = 'scheduled';

        // Auto token for in-clinic visits
        if ($validated['service_location'] === 'clinic') {
            $validated['token_number'] = $count;
        }

        $appointment = Appointment::create($validated);

        return redirect()->route('appointments.index')
            ->with('success', "Appointment {$appointment->appointment_no} scheduled successfully!");
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $appointment = Appointment::findOrFail($id);

        $request->validate([
            'status' => 'required|in:scheduled,checked_in,in_progress,completed,cancelled,no_show',
        ]);

        $appointment->update([
            'status' => $request->input('status'),
            'cancellation_reason' => $request->input('cancellation_reason'),
        ]);

        return back()->with('success', "Appointment status updated to {$appointment->status}.");
    }
}
